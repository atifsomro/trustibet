<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\WithdrawalStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;

class RequestWithdrawalAction
{
    public function __construct(
        protected RecordTransactionAction $recordTransactionAction
    ) {
    }

    /**
     * Create a withdrawal request and lock the funds.
     *
     * @throws InsufficientBalanceException
     */
    public function execute(
        User $user,
        float $amount,
        string $paymentMethod,
        array $accountDetails,
        ?string $remarks = null
    ): WithdrawalRequest {

        return DB::transaction(function () use (
            $user,
            $amount,
            $paymentMethod,
            $accountDetails,
            $remarks
        ) {

            /** @var Wallet $wallet */
            $wallet = Wallet::query()
                ->lockForUpdate()
                ->where('user_id', $user->id)
                ->firstOrFail();

            if ($wallet->withdrawable_balance < $amount) {
                throw new InsufficientBalanceException(
                    balanceType: \App\Enums\BalanceType::WITHDRAWABLE,
                    requestedAmount: $amount,
                    availableAmount: $wallet->withdrawable_balance
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Move funds
            |--------------------------------------------------------------------------
            */

            $wallet->withdrawable_balance -= $amount;
            $wallet->locked_balance += $amount;
            $wallet->version++;

            $wallet->save();

            /*
            |--------------------------------------------------------------------------
            | Withdrawal Request
            |--------------------------------------------------------------------------
            */

            $withdrawal = new WithdrawalRequest();

            $withdrawal->user_id = $user->id;
            $withdrawal->wallet_id = $wallet->id;
            $withdrawal->amount = $amount;
            $withdrawal->status = WithdrawalStatus::PENDING;
            $withdrawal->payment_method = $paymentMethod;
            $withdrawal->account_details = $accountDetails;
            $withdrawal->remarks = $remarks;
            $withdrawal->requested_at = now();

            $withdrawal->save();

            /*
            |--------------------------------------------------------------------------
            | Ledger
            |--------------------------------------------------------------------------
            */

            $this->recordTransactionAction->execute(
                wallet: $wallet,
                balanceType: \App\Enums\BalanceType::WITHDRAWABLE,
                transactionType: \App\Enums\WalletTransactionType::WITHDRAW_REQUEST,
                amount: -$amount,
                balanceAfter: $wallet->withdrawable_balance,
                reference: $withdrawal
            );

            return $withdrawal;
        });
    }
}