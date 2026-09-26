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
use App\Models\User;
use App\Services\Game\WeightedPrizePicker;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PlayScratchCardAction
{
    public function __construct(
        protected WalletService $walletService,
        protected WeightedPrizePicker $prizePicker,
    ) {
    }

    public function execute(
        User $user,
        Game $game,
        GamePackage $package,
        ?int $cardIndex = null,
        ?string $idempotencyKey = null,
    ): GamePlay {
        if ($game->type !== GameType::SCRATCH_CARD) {
            throw ValidationException::withMessages([
                'game' => 'This action is only valid for scratch card games.',
            ]);
        }

        if (! $game->is_active || ! $package->is_active || $package->game_id !== $game->id) {
            throw ValidationException::withMessages([
                'package' => 'This scratch package is not available.',
            ]);
        }

        $fee = (float) $package->fee;

        if ($fee <= 0) {
            throw ValidationException::withMessages([
                'package' => 'Scratch package fee must be greater than zero.',
            ]);
        }

        $idempotencyKey ??= (string) Str::uuid();

        return DB::transaction(function () use ($user, $game, $package, $cardIndex, $fee, $idempotencyKey) {
            $existing = GamePlay::query()
                ->where('user_id', $user->id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing;
            }

            $prizes = $package->activePrizes()->get();

            if ($prizes->isEmpty()) {
                throw ValidationException::withMessages([
                    'package' => 'This package has no active prizes configured.',
                ]);
            }

            $play = GamePlay::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'game_id' => $game->id,
                'game_package_id' => $package->id,
                'fee_amount' => $fee,
                'prize_amount' => 0,
                'status' => GamePlayStatus::PENDING,
                'selection' => ['card' => $cardIndex],
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
                ]
            );

            $prize = $this->prizePicker->pick($prizes);
            $prizeAmount = (float) $prize->prize_amount;
            $won = $prizeAmount > 0;
            $winTxnId = null;

            if ($won) {
                $winTxn = $this->walletService->creditWithTransaction(
                    user: $user,
                    balanceType: BalanceType::WITHDRAWABLE,
                    transactionType: WalletTransactionType::GAME_WIN,
                    amount: $prizeAmount,
                    reference: $play,
                    idempotencyKey: "game-win-{$idempotencyKey}",
                    meta: [
                        'game' => $game->slug,
                        'prize_id' => $prize->id,
                        'label' => $prize->label,
                    ]
                );
                $winTxnId = $winTxn->id;
            }

            $play->update([
                'game_prize_id' => $prize->id,
                'prize_amount' => $prizeAmount,
                'status' => $won ? GamePlayStatus::WON : GamePlayStatus::LOST,
                'outcome' => [
                    'label' => $prize->label,
                    'prize_amount' => $prizeAmount,
                ],
                'bet_transaction_id' => $betTxn->id,
                'win_transaction_id' => $winTxnId,
            ]);

            return $play->fresh(['prize', 'package']);
        });
    }
}
