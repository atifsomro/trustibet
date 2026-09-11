<?php

declare(strict_types=1);

namespace App\Actions\Investment;

use App\Enums\BalanceType;
use App\Enums\WalletTransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransferRoiToWalletAction
{
    public function __construct(
        protected WalletService $walletService,
    ) {
    }

    /**
     * Transfer ROI balance to withdrawable within daily limit.
     *
     * @return array{amount:float, remaining_daily_limit:float}
     */
    public function execute(User $user, float $amount): array
    {
        $min = (float) config('investment.roi_transfer.min_amount', 1);
        $dailyLimit = (float) config('investment.roi_transfer.daily_limit', 50);

        if ($amount < $min) {
            throw ValidationException::withMessages([
                'amount' => sprintf('Minimum transfer amount is $%s.', number_format($min, 2)),
            ]);
        }

        return DB::transaction(function () use ($user, $amount, $dailyLimit) {
            $wallet = $this->walletService->wallet($user);
            $wallet = $wallet->newQuery()->lockForUpdate()->findOrFail($wallet->id);

            $roiBalance = (float) $wallet->roi_balance;

            if ($amount > $roiBalance) {
                throw new InsufficientBalanceException(
                    balanceType: BalanceType::ROI,
                    requestedAmount: $amount,
                    availableAmount: $roiBalance,
                );
            }

            $transferredToday = $this->transferredToday($wallet->id);
            $remaining = round(max(0, $dailyLimit - $transferredToday), 2);

            if ($amount > $remaining) {
                throw ValidationException::withMessages([
                    'amount' => sprintf(
                        'Daily ROI transfer limit exceeded. Remaining today: $%s.',
                        number_format($remaining, 2)
                    ),
                ]);
            }

            $transferId = (string) Str::uuid();
            $debitKey = 'investment-roi-transfer-debit:' . $transferId;
            $creditKey = 'investment-roi-transfer-credit:' . $transferId;

            $this->walletService->debit(
                user: $user,
                balanceType: BalanceType::ROI,
                transactionType: WalletTransactionType::INVESTMENT_ROI_TRANSFER,
                amount: $amount,
                reference: null,
                idempotencyKey: $debitKey,
                meta: [
                    'transfer_id' => $transferId,
                    'direction' => 'roi_to_withdrawable',
                    'side' => 'debit',
                ],
            );

            $this->walletService->credit(
                user: $user,
                balanceType: BalanceType::WITHDRAWABLE,
                transactionType: WalletTransactionType::INVESTMENT_ROI_TRANSFER,
                amount: $amount,
                reference: null,
                idempotencyKey: $creditKey,
                meta: [
                    'transfer_id' => $transferId,
                    'direction' => 'roi_to_withdrawable',
                    'side' => 'credit',
                ],
            );

            $newRemaining = round(max(0, $remaining - $amount), 2);

            return [
                'amount' => $amount,
                'remaining_daily_limit' => $newRemaining,
            ];
        });
    }

    public function transferredToday(int $walletId): float
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // Debit side stores negative amounts; sum absolute transferred out of ROI.
        $sum = (float) WalletTransaction::query()
            ->where('wallet_id', $walletId)
            ->where('balance_type', BalanceType::ROI)
            ->where('type', WalletTransactionType::INVESTMENT_ROI_TRANSFER)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('amount');

        return round(abs($sum), 2);
    }

    public function remainingDailyLimit(User $user): float
    {
        $dailyLimit = (float) config('investment.roi_transfer.daily_limit', 50);
        $wallet = $this->walletService->wallet($user);
        $used = $this->transferredToday($wallet->id);

        return round(max(0, $dailyLimit - $used), 2);
    }
}
