<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\BalanceType;
use App\Enums\WalletTransactionType;
use App\Enums\WithdrawalStatus;
use App\Exceptions\WalletException;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;

class RejectWithdrawalAction
{
    public function __construct(
        protected RecordTransactionAction $recordTransactionAction
    ) {
    }

    /**
     * Reject a pending withdrawal request.
     *
     * Locked balance is released back to the user's
     * withdrawable balance.
     *
     * @throws WalletException
     */
    public function execute(
        WithdrawalRequest $withdrawal,
        User $rejectedBy,
        ?string $remarks = null,
        array $meta = []
    ): WithdrawalRequest {

        return DB::transaction(function () use (
            $withdrawal,
            $rejectedBy,
            $remarks,
            $meta
        ) {

            /** @var WithdrawalRequest $withdrawal */
            $withdrawal = WithdrawalRequest::query()
                ->lockForUpdate()
                ->findOrFail($withdrawal->id);

            if ($withdrawal->status !== WithdrawalStatus::PENDING) {
                throw new WalletException(
                    'Only pending withdrawals can be rejected.'
                );
            }

            /** @var Wallet $wallet */
            $wallet = Wallet::query()
                ->lockForUpdate()
                ->findOrFail($withdrawal->wallet_id);

            if ($wallet->locked_balance < $withdrawal->amount) {
                throw new WalletException(
                    'Locked balance is lower than withdrawal amount.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Return locked funds
            |--------------------------------------------------------------------------
            */

            $wallet->locked_balance -= $withdrawal->amount;
            $wallet->withdrawable_balance += $withdrawal->amount;
            $wallet->version++;

            $wallet->save();

            /*
            |--------------------------------------------------------------------------
            | Update withdrawal
            |--------------------------------------------------------------------------
            */

            $withdrawal->status = WithdrawalStatus::REJECTED;
            $withdrawal->approved_by = $rejectedBy->id;
            $withdrawal->remarks = $remarks;
            $withdrawal->processed_at = now();

            $withdrawal->save();

            /*
            |--------------------------------------------------------------------------
            | Ledger Entry 1
            | Remove amount from locked balance
            |--------------------------------------------------------------------------
            */

            $this->recordTransactionAction->execute(
                wallet: $wallet,
                balanceType: BalanceType::LOCKED,
                transactionType: WalletTransactionType::WITHDRAW_REJECTED,
                amount: -$withdrawal->amount,
                balanceAfter: $wallet->locked_balance,
                reference: $withdrawal,
                meta: array_merge($meta, [
                    'step' => 'locked_balance_release',
                ])
            );

            /*
            |--------------------------------------------------------------------------
            | Ledger Entry 2
            | Credit amount back to withdrawable balance
            |--------------------------------------------------------------------------
            */

            $this->recordTransactionAction->execute(
                wallet: $wallet,
                balanceType: BalanceType::WITHDRAWABLE,
                transactionType: WalletTransactionType::WITHDRAW_REJECTED,
                amount: $withdrawal->amount,
                balanceAfter: $wallet->withdrawable_balance,
                reference: $withdrawal,
                meta: array_merge($meta, [
                    'step' => 'withdrawable_balance_restore',
                ])
            );

            return $withdrawal;
        });
    }
}