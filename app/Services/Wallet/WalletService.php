<?php

declare(strict_types=1);

namespace App\Services\Wallet;

use App\Actions\Wallet\ApproveWithdrawalAction;
use App\Actions\Wallet\CreditWalletAction;
use App\Actions\Wallet\DebitWalletAction;
use App\Actions\Wallet\GrantBonusAction;
use App\Actions\Wallet\RequestWithdrawalAction;
use App\Actions\Wallet\RejectWithdrawalAction;
use App\Enums\BalanceType;
use App\Enums\BonusType;
use App\Enums\WalletTransactionType;
use App\Models\Admin;
use App\Models\Bonus;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Database\Eloquent\Model;

class WalletService
{
    public function __construct(
        protected WalletManager $walletManager,
        protected CreditWalletAction $creditWalletAction,
        protected DebitWalletAction $debitWalletAction,
        protected GrantBonusAction $grantBonusAction,
        protected RequestWithdrawalAction $requestWithdrawalAction,
        protected ApproveWithdrawalAction $approveWithdrawalAction,
        protected RejectWithdrawalAction $rejectWithdrawalAction
    ) {
    }

    /**
     * Get or create user's wallet.
     */
    public function wallet(User $user): Wallet
    {
        return $this->walletManager->getOrCreate($user);
    }

    /**
     * Credit a wallet balance.
     */
    public function credit(
        User $user,
        BalanceType $balanceType,
        WalletTransactionType $transactionType,
        float $amount,
        ?Model $reference = null,
        ?Bonus $bonus = null,
        ?string $idempotencyKey = null,
        array $meta = []
    ): Wallet {
        return $this->creditWalletAction->execute(
            wallet: $this->wallet($user),
            balanceType: $balanceType,
            transactionType: $transactionType,
            amount: $amount,
            reference: $reference,
            bonus: $bonus,
            idempotencyKey: $idempotencyKey,
            meta: $meta
        );
    }

    /**
     * Debit a wallet balance.
     *
     * Existing wallet functionality should continue
     * using this method.
     */
    public function debit(
        User $user,
        BalanceType $balanceType,
        WalletTransactionType $transactionType,
        float $amount,
        ?Model $reference = null,
        ?Bonus $bonus = null,
        ?string $idempotencyKey = null,
        array $meta = []
    ): Wallet {
        return $this->debitWalletAction->execute(
            wallet: $this->wallet($user),
            balanceType: $balanceType,
            transactionType: $transactionType,
            amount: $amount,
            reference: $reference,
            bonus: $bonus,
            idempotencyKey: $idempotencyKey,
            meta: $meta
        );
    }

    /**
     * Debit a wallet balance and return the
     * exact wallet transaction.
     *
     * Use this when the caller needs the transaction ID.
     */
    public function debitWithTransaction(
        User $user,
        BalanceType $balanceType,
        WalletTransactionType $transactionType,
        float $amount,
        ?Model $reference = null,
        ?Bonus $bonus = null,
        ?string $idempotencyKey = null,
        array $meta = []
    ): WalletTransaction {
        return $this->debitWalletAction->executeWithTransaction(
            wallet: $this->wallet($user),
            balanceType: $balanceType,
            transactionType: $transactionType,
            amount: $amount,
            reference: $reference,
            bonus: $bonus,
            idempotencyKey: $idempotencyKey,
            meta: $meta
        );
    }

    /**
     * Grant a bonus.
     */
    public function grantBonus(
        User $user,
        float $amount,
        BonusType $bonusType = BonusType::WELCOME,
        ?Model $reference = null,
        array $meta = [],
        ?\DateTimeInterface $expiresAt = null
    ): Bonus {
        return $this->grantBonusAction->execute(
            user: $user,
            amount: $amount,
            bonusType: $bonusType,
            reference: $reference,
            meta: $meta,
            expiresAt: $expiresAt
        );
    }

    /**
     * Request a withdrawal.
     */
    public function requestWithdrawal(
        User $user,
        float $amount,
        string $paymentMethod,
        string $accountDetails,
        ?string $remarks = null
    ): WithdrawalRequest {
        return $this->requestWithdrawalAction->execute(
            user: $user,
            amount: $amount,
            paymentMethod: $paymentMethod,
            accountDetails: $accountDetails,
            remarks: $remarks
        );
    }

    /**
     * Approve a withdrawal.
     */
    public function approveWithdrawal(
        WithdrawalRequest $withdrawal,
        Admin $approvedBy,
        ?string $remarks = null,
        array $meta = []
    ): WithdrawalRequest {
        return $this->approveWithdrawalAction->execute(
            withdrawal: $withdrawal,
            approvedBy: $approvedBy,
            remarks: $remarks,
            meta: $meta
        );
    }

    /**
     * Reject a withdrawal.
     */
    public function rejectWithdrawal(
        WithdrawalRequest $withdrawal,
        Admin $rejectedBy,
        ?string $remarks = null,
        array $meta = []
    ): WithdrawalRequest {
        return $this->rejectWithdrawalAction->execute(
            withdrawal: $withdrawal,
            rejectedBy: $rejectedBy,
            remarks: $remarks,
            meta: $meta
        );
    }

    /**
     * Get user's wallet balances.
     */
    public function balances(User $user): array
    {
        $wallet = $this->wallet($user);

        return [
            'withdrawable' => $wallet->withdrawable_balance,
            'bonus' => $wallet->bonus_balance,
            'locked' => $wallet->locked_balance,
            'total' => $wallet->withdrawable_balance
                + $wallet->bonus_balance
                + $wallet->locked_balance,
        ];
    }
}