<?php

declare(strict_types=1);

namespace App\Actions\Game;

use App\Enums\BalanceType;
use App\Enums\GamePlayStatus;
use App\Enums\GameRoundStatus;
use App\Enums\GameType;
use App\Enums\WalletTransactionType;
use App\Models\Game;
use App\Models\GamePackage;
use App\Models\GamePlay;
use App\Models\User;
use App\Services\Game\LimitedDrawService;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EnterLimitedDrawAction
{
    public function __construct(
        protected WalletService $walletService,
        protected LimitedDrawService $limitedDrawService,
    ) {
    }

    public function execute(
        User $user,
        Game $game,
        GamePackage $package,
        ?string $idempotencyKey = null,
    ): GamePlay {
        if ($game->type !== GameType::LIMITED_DRAW) {
            throw ValidationException::withMessages([
                'game' => 'This action is only valid for limited draw games.',
            ]);
        }

        if (! $game->is_active || ! $package->is_active || $package->game_id !== $game->id) {
            throw ValidationException::withMessages([
                'package' => 'This draw entry is not available.',
            ]);
        }

        $fee = (float) $package->fee;

        if ($fee <= 0) {
            throw ValidationException::withMessages([
                'package' => 'Entry fee must be greater than zero.',
            ]);
        }

        $idempotencyKey ??= (string) Str::uuid();

        return DB::transaction(function () use ($user, $game, $package, $fee, $idempotencyKey) {
            $existing = GamePlay::query()
                ->where('user_id', $user->id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing;
            }

            $round = $this->limitedDrawService->sync($game);

            if (! $round || $round->status !== GameRoundStatus::BETTING || now()->gte($round->ends_at)) {
                throw ValidationException::withMessages([
                    'draw' => 'This draw is closed.',
                ]);
            }

            $round = $round->newQuery()->lockForUpdate()->findOrFail($round->id);
            $entries = $round->plays()->lockForUpdate()->count();
            $maxEntries = max(0, (int) $game->configValue('max_entries', 0));

            if ($maxEntries > 0 && $entries >= $maxEntries) {
                throw ValidationException::withMessages([
                    'draw' => 'This draw is full.',
                ]);
            }

            $maxPerUser = max(1, (int) $game->configValue('max_per_user', 1));
            $userEntries = $round->plays()->where('user_id', $user->id)->count();

            if ($userEntries >= $maxPerUser) {
                throw ValidationException::withMessages([
                    'draw' => 'You have already joined this draw.',
                ]);
            }

            $play = GamePlay::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'game_id' => $game->id,
                'game_package_id' => $package->id,
                'game_round_id' => $round->id,
                'fee_amount' => $fee,
                'prize_amount' => 0,
                'status' => GamePlayStatus::PENDING,
                'selection' => ['entry' => $entries + 1],
                'idempotency_key' => $idempotencyKey,
            ]);

            $betTxn = $this->walletService->debitWithTransaction(
                user: $user,
                balanceType: BalanceType::WITHDRAWABLE,
                transactionType: WalletTransactionType::GAME_BET,
                amount: $fee,
                reference: $play,
                idempotencyKey: "game-bet-{$idempotencyKey}",
                meta: [
                    'game' => $game->slug,
                    'package_id' => $package->id,
                    'round_id' => $round->id,
                ]
            );

            $play->update([
                'bet_transaction_id' => $betTxn->id,
                'outcome' => [
                    'label' => 'Entered',
                ],
            ]);

            return $play->fresh(['package']);
        });
    }
}
