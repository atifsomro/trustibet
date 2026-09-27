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
use App\Services\Game\FreeSpinCounter;
use App\Services\Game\PackageCreditLedger;
use App\Services\Game\WeightedPrizePicker;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SpinWheelAction
{
    public function __construct(
        protected WalletService $walletService,
        protected WeightedPrizePicker $prizePicker,
        protected FreeSpinCounter $freeSpinCounter,
        protected PackageCreditLedger $packageCredits,
    ) {
    }

    public function execute(
        User $user,
        Game $game,
        GamePackage $package,
        bool $useFreeSpin = false,
        ?string $idempotencyKey = null,
    ): GamePlay {
        if ($game->type !== GameType::WHEEL) {
            throw ValidationException::withMessages([
                'game' => 'This action is only valid for wheel games.',
            ]);
        }

        if (! $game->is_active || ! $package->is_active || $package->game_id !== $game->id) {
            throw ValidationException::withMessages([
                'package' => 'This wheel package is not available.',
            ]);
        }

        $idempotencyKey ??= (string) Str::uuid();

        return DB::transaction(function () use (
            $user,
            $game,
            $package,
            $useFreeSpin,
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
            $isFree = false;
            $usingCredit = false;
            $purchasingBundle = false;
            $allowance = $this->packageCredits->allowance($package);

            if ($useFreeSpin && $this->freeSpinCounter->remaining($user, $game) > 0) {
                $fee = 0;
                $isFree = true;
            } elseif ($this->packageCredits->isBundled($package)
                && $this->packageCredits->remaining($user, $package, lock: true) > 0) {
                $fee = 0;
                $usingCredit = true;
            } elseif ($fee <= 0) {
                throw ValidationException::withMessages([
                    'package' => 'Wheel package fee must be greater than zero.',
                ]);
            } else {
                $purchasingBundle = $this->packageCredits->isBundled($package);
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
                'selection' => [
                    'free_spin' => $isFree,
                    'package_credit' => $usingCredit,
                ],
                'idempotency_key' => $idempotencyKey,
            ]);

            $betTxnId = null;

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
                $betTxnId = $betTxn->id;
            }

            $prize = $this->prizePicker->pick($prizes);
            $prizeAmount = (float) $prize->prize_amount;
            $extraFreeSpins = (int) $prize->metaValue('free_spins', 0);

            if ($extraFreeSpins <= 0 && str_contains(strtolower($prize->label), 'spin')) {
                if (preg_match('/(\d+)/', $prize->label, $matches)) {
                    $extraFreeSpins = (int) $matches[1];
                }
            }

            $won = $prizeAmount > 0 || $extraFreeSpins > 0;
            $winTxnId = null;

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
                        'prize_id' => $prize->id,
                        'label' => $prize->label,
                    ]
                );
                $winTxnId = $winTxn->id;
            }

            $segmentIndex = (int) $prize->metaValue(
                'segment',
                $prizes->search(fn ($p) => $p->id === $prize->id)
            );

            $play->update([
                'game_prize_id' => $prize->id,
                'prize_amount' => $prizeAmount,
                'status' => $won ? GamePlayStatus::WON : GamePlayStatus::LOST,
                'outcome' => [
                    'label' => $prize->label,
                    'prize_amount' => $prizeAmount,
                    'segment' => $segmentIndex,
                    'color' => $prize->metaValue('color'),
                    'free_spin' => $isFree,
                    'granted_free_spins' => $extraFreeSpins,
                    'package_bundle' => $purchasingBundle || $usingCredit,
                    'package_credits' => $purchasingBundle ? $allowance : 0,
                ],
                'bet_transaction_id' => $betTxnId,
                'win_transaction_id' => $winTxnId,
            ]);

            return $play->fresh(['prize', 'package']);
        });
    }
}
