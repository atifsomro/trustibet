<?php

declare(strict_types=1);

namespace App\Actions\Deposit;

use App\Enums\BalanceType;
use App\Enums\WalletTransactionType;
use App\Models\Deposit;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Throwable;

class ApproveDepositAction
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    /**
     * Approve a deposit and credit the user's wallet.
     *
     * Flow:
     * - Ensure deposit is pending
     * - Credit user's withdrawable wallet
     * - Mark deposit as approved
     *
     * @throws Throwable
     */
    public function execute(
        Deposit $deposit,
        int $adminId,
        ?string $remarks = null
    ): Deposit {

        return DB::transaction(function () use (
            $deposit,
            $adminId,
            $remarks
        ) {

            // Prevent double approval
            if (! $deposit->isPending()) {

                throw new \RuntimeException(
                    'This deposit has already been processed.'
                );
            }
            // Credit user's wallet
            try {
                $this->walletService->credit(
                    user: $deposit->user,
                    balanceType: BalanceType::WITHDRAWABLE,
                    transactionType: WalletTransactionType::DEPOSIT,
                    amount: (float) $deposit->amount,
                    reference: $deposit,
                    meta: [
                        'deposit_id' => $deposit->id,
                        'bank_account_id' => $deposit->bank_account_id,
                        'reference_number' => $deposit->reference_number,
                    ]
                );
            } catch (\Throwable $e) {
                return;
            }
            // Mark deposit approved
            $result = $deposit->approve(
                adminId: $adminId,
                remarks: $remarks
            );
            return $deposit->fresh([
                'user.wallet',
            ]);
        });
    }
}