<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\BalanceType;
use App\Enums\WalletTransactionType;
use App\Enums\WithdrawalStatus;
use App\Exceptions\WalletException;
use App\Models\Admin;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;

class ApproveWithdrawalAction
{
    public function __construct(
        protected RecordTransactionAction $recordTransactionAction
    ) {
    }

    /**
     * Approve a pending withdrawal.
     *
     * @throws WalletException
     */
    public function execute(
        WithdrawalRequest $withdrawal,
        Admin $approvedBy,
        ?string $remarks = null,
        array $meta = []
    ): WithdrawalRequest {

        return DB::transaction(function () use (
            $withdrawal,
            $approvedBy,
            $remarks,
            $meta
        ) {

            /** @var WithdrawalRequest $withdrawal */
            $withdrawal = WithdrawalRequest::query()
                ->lockForUpdate()
                ->findOrFail($withdrawal->id);

            if ($withdrawal->status !== WithdrawalStatus::PENDING) {
                throw new WalletException(
                    'Only pending withdrawals can be approved.'
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
            | Release locked balance permanently
            |--------------------------------------------------------------------------
            */

            $wallet->locked_balance -= $withdrawal->amount;
            $wallet->version++;

            $wallet->save();

            /*
            |--------------------------------------------------------------------------
            | Update withdrawal
            |--------------------------------------------------------------------------
            */

            $withdrawal->status = WithdrawalStatus::APPROVED;
            $withdrawal->approved_by = $approvedBy->id;
            $withdrawal->remarks = $remarks;
            $withdrawal->processed_at = now();

            $withdrawal->save();

            /*
            |--------------------------------------------------------------------------
            | Ledger
            |--------------------------------------------------------------------------
            */

            $this->recordTransactionAction->execute(
                wallet: $wallet,
                balanceType: BalanceType::LOCKED,
                transactionType: WalletTransactionType::WITHDRAW_APPROVED,
                amount: -$withdrawal->amount,
                balanceAfter: $wallet->locked_balance,
                reference: $withdrawal,
                meta: $meta
            );

            return $withdrawal;
        });
    }
}