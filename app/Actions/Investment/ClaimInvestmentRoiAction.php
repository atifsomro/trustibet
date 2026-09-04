<?php

declare(strict_types=1);

namespace App\Actions\Investment;

use App\Enums\BalanceType;
use App\Enums\InvestmentRoiStatus;
use App\Enums\WalletTransactionType;
use App\Models\InvestmentRoiLog;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ClaimInvestmentRoiAction
{
    public function __construct(
        protected WalletService $walletService,
    ) {
    }

    /**
     * Claim all of today's pending ROI logs for the user into ROI balance.
     *
     * @return array{claimed_count:int, amount:float, transaction:?WalletTransaction}
     */
    public function execute(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $today = now()->toDateString();

            $logs = InvestmentRoiLog::query()
                ->where('user_id', $user->id)
                ->where('status', InvestmentRoiStatus::PENDING)
                ->whereDate('roi_date', $today)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($logs->isEmpty()) {
                throw ValidationException::withMessages([
                    'claim' => 'No claimable ROI available right now.',
                ]);
            }

            $amount = round((float) $logs->sum('amount'), 2);
            $logIds = $logs->pluck('id')->sort()->values()->all();
            $batchId = (string) Str::uuid();
            // Key includes the exact pending log set so a later same-day claim
            // for newly generated rows is not blocked by an earlier claim.
            $idempotencyKey = 'investment-roi-claim:' . $user->id . ':' . $today . ':' . md5(implode(',', $logIds));

            $this->walletService->credit(
                user: $user,
                balanceType: BalanceType::ROI,
                transactionType: WalletTransactionType::INVESTMENT_ROI_CLAIM,
                amount: $amount,
                reference: null,
                idempotencyKey: $idempotencyKey,
                meta: [
                    'batch_id' => $batchId,
                    'roi_date' => $today,
                    'log_ids' => $logIds,
                    'claimed_count' => $logs->count(),
                ],
            );

            $transaction = WalletTransaction::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            InvestmentRoiLog::query()
                ->whereIn('id', $logIds)
                ->where('status', InvestmentRoiStatus::PENDING)
                ->update([
                    'status' => InvestmentRoiStatus::CLAIMED,
                    'claimed_at' => now(),
                    'claim_transaction_id' => $transaction?->id,
                    'updated_at' => now(),
                ]);

            return [
                'claimed_count' => $logs->count(),
                'amount' => $amount,
                'transaction' => $transaction,
            ];
        });
    }
}
