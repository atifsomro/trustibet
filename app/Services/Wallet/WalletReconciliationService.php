<?php

declare(strict_types=1);

namespace App\Services\Wallet;

use App\Enums\BalanceType;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Collection;

class WalletReconciliationService
{
    /**
     * Reconcile a user's wallet.
     */
    public function reconcile(User $user): array
    {
        $wallet = Wallet::query()
            ->where('user_id', $user->id)
            ->firstOrFail();

        $withdrawable = $this->calculateBalance(
            $wallet,
            BalanceType::WITHDRAWABLE
        );

        $bonus = $this->calculateBalance(
            $wallet,
            BalanceType::BONUS
        );

        $locked = $this->calculateBalance(
            $wallet,
            BalanceType::LOCKED
        );

        $roi = $this->calculateBalance(
            $wallet,
            BalanceType::ROI
        );

        return [
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,

            'withdrawable' => [
                'wallet' => $wallet->withdrawable_balance,
                'ledger' => $withdrawable,
                'match' => $wallet->withdrawable_balance === $withdrawable,
            ],

            'bonus' => [
                'wallet' => $wallet->bonus_balance,
                'ledger' => $bonus,
                'match' => $wallet->bonus_balance === $bonus,
            ],

            'locked' => [
                'wallet' => $wallet->locked_balance,
                'ledger' => $locked,
                'match' => $wallet->locked_balance === $locked,
            ],

            'roi' => [
                'wallet' => $wallet->roi_balance,
                'ledger' => $roi,
                'match' => $wallet->roi_balance === $roi,
            ],

            'is_valid' => (
                $wallet->withdrawable_balance === $withdrawable
                && $wallet->bonus_balance === $bonus
                && $wallet->locked_balance === $locked
                && $wallet->roi_balance === $roi
            ),
        ];
    }

    /**
     * Reconcile every wallet.
     */
    public function reconcileAll(): Collection
    {
        return Wallet::query()
            ->with('user')
            ->get()
            ->map(fn (Wallet $wallet) => $this->reconcile($wallet->user));
    }

    /**
     * Calculate balance from ledger.
     */
    protected function calculateBalance(
        Wallet $wallet,
        BalanceType $balanceType
    ): int {

        return (int) WalletTransaction::query()
            ->where('wallet_id', $wallet->id)
            ->where('balance_type', $balanceType)
            ->sum('amount');
    }

    /**
     * Get only wallets with mismatches.
     */
    public function mismatches(): Collection
    {
        return $this->reconcileAll()
            ->filter(fn (array $result) => ! $result['is_valid'])
            ->values();
    }

    /**
     * Check whether a wallet is valid.
     */
    public function isValid(User $user): bool
    {
        return $this->reconcile($user)['is_valid'];
    }
}