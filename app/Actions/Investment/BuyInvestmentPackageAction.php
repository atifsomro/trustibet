<?php

declare(strict_types=1);

namespace App\Actions\Investment;

use App\Enums\BalanceType;
use App\Enums\InvestmentStatus;
use App\Enums\WalletTransactionType;
use App\Models\InvestmentPackage;
use App\Models\User;
use App\Models\UserInvestment;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BuyInvestmentPackageAction
{
    public function __construct(
        protected WalletService $walletService,
    ) {
    }

    public function execute(User $user, InvestmentPackage $package): UserInvestment
    {
        return DB::transaction(function () use ($user, $package) {
            $package = InvestmentPackage::query()
                ->lockForUpdate()
                ->findOrFail($package->id);

            if (! $package->is_active) {
                throw ValidationException::withMessages([
                    'package' => 'This investment package is not available.',
                ]);
            }

            $price = (float) $package->price;

            if ($price <= 0) {
                throw ValidationException::withMessages([
                    'package' => 'Invalid package price.',
                ]);
            }

            $purchaseId = (string) Str::uuid();
            $idempotencyKey = 'investment-purchase:' . $purchaseId;

            $transaction = $this->walletService->debitWithTransaction(
                user: $user,
                balanceType: BalanceType::WITHDRAWABLE,
                transactionType: WalletTransactionType::INVESTMENT_PURCHASE,
                amount: $price,
                reference: $package,
                idempotencyKey: $idempotencyKey,
                meta: [
                    'purchase_id' => $purchaseId,
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                    'price' => $price,
                    'daily_roi' => (float) $package->daily_roi,
                    'total_days' => $package->total_days,
                ],
            );

            $startsAt = now()->startOfDay();
            $endsAt = $startsAt->copy()->addDays($package->total_days);

            return UserInvestment::query()->create([
                'user_id' => $user->id,
                'investment_package_id' => $package->id,
                'package_name' => $package->name,
                'price' => $price,
                'daily_roi' => $package->daily_roi,
                'total_days' => $package->total_days,
                'feature_points' => $package->feature_points ?? [],
                'starts_at' => $startsAt->toDateString(),
                'ends_at' => $endsAt->toDateString(),
                'status' => InvestmentStatus::ACTIVE,
                'purchase_transaction_id' => $transaction->id,
            ]);
        });
    }
}
