<?php

declare(strict_types=1);

namespace App\Actions\Game;

use App\Enums\BalanceType;
use App\Enums\GamePlayStatus;
use App\Enums\GameType;
use App\Enums\WalletTransactionType;
use App\Models\Game;
use App\Models\GamePackage;
use App\Models\GamePlay;
use App\Models\GameRound;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PlaceColorBetAction
{
    public function __construct(
        protected WalletService $walletService,
    ) {
    }

    public function execute(
        User $user,
        Game $game,
        GamePackage $package,
        GameRound $round,
        string $color,
        ?string $idempotencyKey = null,
    ): GamePlay {
        if ($game->type !== GameType::COLOR_TRADING) {
            throw ValidationException::withMessages([
                'game' => 'This action is only valid for color trading games.',
            ]);
        }

        if (! $game->is_active || ! $package->is_active || $package->game_id !== $game->id) {
            throw ValidationException::withMessages([
                'package' => 'This chip package is not available.',
            ]);
        }

        if ($round->game_id !== $game->id || ! $round->isBetting()) {
            throw ValidationException::withMessages([
                'round' => 'Betting is closed for this round.',
            ]);
        }

        $colors = collect($game->configValue('colors', []))
            ->map(fn ($c) => strtolower((string) $c))
            ->filter()
            ->values();

        $color = strtolower(trim($color));

        if ($colors->isNotEmpty() && ! $colors->contains($color)) {
            throw ValidationException::withMessages([
                'color' => 'Invalid color selection.',
            ]);
        }

        $fee = (float) $package->fee;

        if ($fee <= 0) {
            throw ValidationException::withMessages([
                'package' => 'Chip package fee must be greater than zero.',
            ]);
        }

        $idempotencyKey ??= (string) Str::uuid();

        return DB::transaction(function () use (
            $user,
            $game,
            $package,
            $round,
            $color,
            $fee,
            $idempotencyKey
        ) {
            $existing = GamePlay::query()
                ->where('user_id', $user->id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing;
            }

            $round = GameRound::query()
                ->lockForUpdate()
                ->findOrFail($round->id);

            if (! $round->isBetting()) {
                throw ValidationException::withMessages([
                    'round' => 'Betting is closed for this round.',
                ]);
            }

            $this->assertWithinBetCaps($user, $game, $round, $fee);

            $play = GamePlay::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'game_id' => $game->id,
                'game_package_id' => $package->id,
                'game_round_id' => $round->id,
                'fee_amount' => $fee,
                'prize_amount' => 0,
                'status' => GamePlayStatus::PENDING,
                'selection' => ['color' => $color],
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
                    'round_id' => $round->id,
                    'color' => $color,
                ]
            );

            $play->update([
                'bet_transaction_id' => $betTxn->id,
            ]);

            return $play->fresh(['package', 'round']);
        });
    }

    protected function assertWithinBetCaps(User $user, Game $game, GameRound $round, float $fee): void
    {
        $maxPerRound = (float) $game->configValue('max_bet_per_round', 2000);
        $maxPerDay = (float) $game->configValue('max_bet_per_day', 10000);

        if ($maxPerRound > 0) {
            $roundStake = (float) GamePlay::query()
                ->where('user_id', $user->id)
                ->where('game_round_id', $round->id)
                ->sum('fee_amount');

            if (($roundStake + $fee) > $maxPerRound + 0.00001) {
                throw ValidationException::withMessages([
                    'package' => 'This bet would exceed the maximum stake allowed for this round.',
                ]);
            }
        }

        if ($maxPerDay > 0) {
            $dayStake = (float) GamePlay::query()
                ->where('user_id', $user->id)
                ->where('game_id', $game->id)
                ->whereDate('created_at', now()->toDateString())
                ->sum('fee_amount');

            if (($dayStake + $fee) > $maxPerDay + 0.00001) {
                throw ValidationException::withMessages([
                    'package' => 'This bet would exceed the maximum stake allowed for today.',
                ]);
            }
        }
    }
}
