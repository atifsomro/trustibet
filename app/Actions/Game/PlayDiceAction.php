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
use App\Services\Game\PackageCreditLedger;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PlayDiceAction
{
    public function __construct(
        protected WalletService $walletService,
        protected PackageCreditLedger $packageCredits,
    ) {
    }

    public function execute(
        User $user,
        Game $game,
        GamePackage $package,
        int $selectedNumber,
        ?string $idempotencyKey = null,
    ): GamePlay {
        if ($game->type !== GameType::DICE) {
            throw ValidationException::withMessages([
                'game' => 'This action is only valid for dice games.',
            ]);
        }

        if (! $game->is_active || ! $package->is_active || $package->game_id !== $game->id) {
            throw ValidationException::withMessages([
                'package' => 'This dice package is not available.',
            ]);
        }

        $faces = (int) $game->configValue('faces', 6);
        $faces = max(2, $faces);

        if ($selectedNumber < 1 || $selectedNumber > $faces) {
            throw ValidationException::withMessages([
                'number' => "Select a number between 1 and {$faces}.",
            ]);
        }

        $idempotencyKey ??= (string) Str::uuid();

        return DB::transaction(function () use (
            $user,
            $game,
            $package,
            $selectedNumber,
            $faces,
            $idempotencyKey
        ) {
            $existing = GamePlay::query()
                ->where('user_id', $user->id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing;
            }

            $fee = (float) $package->fee;
            $usingCredit = false;
            $purchasingBundle = false;
            $allowance = $this->packageCredits->allowance($package);

            if ($this->packageCredits->isBundled($package)
                && $this->packageCredits->remaining($user, $package, lock: true) > 0) {
                $fee = 0;
                $usingCredit = true;
            } elseif ($fee <= 0) {
                throw ValidationException::withMessages([
                    'package' => 'Dice package fee must be greater than zero.',
                ]);
            } else {
                $purchasingBundle = $this->packageCredits->isBundled($package);
            }

            $multiplier = (float) $package->metaValue('multiplier', 0);
            $winPrize = $package->winPrize();

            if ($multiplier <= 0 && ! $winPrize) {
                throw ValidationException::withMessages([
                    'package' => 'Dice package needs a win prize or multiplier.',
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
                'selection' => [
                    'number' => $selectedNumber,
                    'package_credit' => $usingCredit,
                ],
                'idempotency_key' => $idempotencyKey,
            ]);

            $betTxn = null;

            if ($fee > 0) {
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
            }

            $roll = random_int(1, $faces);
            $won = $roll === $selectedNumber;
            $prizeAmount = 0.0;
            $prizeId = null;
            $winTxnId = null;
            $label = 'Lose';

            if ($won) {
                if ($winPrize) {
                    $prizeAmount = (float) $winPrize->prize_amount;
                    $prizeId = $winPrize->id;
                    $label = $winPrize->label;
                } else {
                    $prizeAmount = round((float) $package->fee * $multiplier, 2);
                    $label = "{$multiplier}x Win";
                }

                if ($prizeAmount > 0) {
                    $winTxn = $this->walletService->creditWithTransaction(
                        user: $user,
                        balanceType: BalanceType::WITHDRAWABLE,
                        transactionType: WalletTransactionType::GAME_WIN,
                        amount: $prizeAmount,
                        reference: $play,
                        idempotencyKey: "game-win-{$idempotencyKey}",
                        meta: [
                            'game' => $game->slug,
                            'roll' => $roll,
                            'selected' => $selectedNumber,
                        ]
                    );
                    $winTxnId = $winTxn->id;
                }
            }

            $play->update([
                'game_prize_id' => $prizeId,
                'prize_amount' => $prizeAmount,
                'status' => $won && $prizeAmount > 0
                    ? GamePlayStatus::WON
                    : GamePlayStatus::LOST,
                'outcome' => [
                    'roll' => $roll,
                    'selected' => $selectedNumber,
                    'label' => $label,
                    'prize_amount' => $prizeAmount,
                    'package_bundle' => $purchasingBundle || $usingCredit,
                    'package_credits' => $purchasingBundle ? $allowance : 0,
                ],
                'bet_transaction_id' => $betTxn?->id,
                'win_transaction_id' => $winTxnId,
            ]);

            return $play->fresh(['prize', 'package']);
        });
    }
}
