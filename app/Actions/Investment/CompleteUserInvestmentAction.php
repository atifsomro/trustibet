<?php

declare(strict_types=1);

namespace App\Actions\Investment;

use App\Enums\BalanceType;
use App\Enums\InvestmentStatus;
use App\Enums\WalletTransactionType;
use App\Models\UserInvestment;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;

class CompleteUserInvestmentAction
{
    public function __construct(
        protected WalletService $walletService,
    ) {
    }

    public function execute(UserInvestment $investment): UserInvestment
    {
        return DB::transaction(function () use ($investment) {
            $investment = UserInvestment::query()
                ->lockForUpdate()
                ->findOrFail($investment->id);

            if ($investment->status !== InvestmentStatus::ACTIVE) {
                return $investment;
            }

            if ($investment->principal_return_transaction_id) {
                $investment->status = InvestmentStatus::COMPLETED;
                $investment->completed_at = $investment->completed_at ?? now();
                $investment->save();

                return $investment;
            }

            $user = $investment->user;
            $amount = (float) $investment->price;
            $idempotencyKey = 'investment-principal-return:' . $investment->id;

            $existing = WalletTransaction::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                $investment->principal_return_transaction_id = $existing->id;
                $investment->status = InvestmentStatus::COMPLETED;
                $investment->completed_at = now();
                $investment->save();

                return $investment;
            }

            $this->walletService->credit(
                user: $user,
                balanceType: BalanceType::WITHDRAWABLE,
                transactionType: WalletTransactionType::INVESTMENT_PRINCIPAL_RETURN,
                amount: $amount,
                reference: $investment,
                idempotencyKey: $idempotencyKey,
                meta: [
                    'user_investment_id' => $investment->id,
                    'package_name' => $investment->package_name,
                    'principal' => $amount,
                ],
            );

            $transaction = WalletTransaction::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            $investment->principal_return_transaction_id = $transaction?->id;
            $investment->status = InvestmentStatus::COMPLETED;
            $investment->completed_at = now();
            $investment->save();

            return $investment;
        });
    }
}
