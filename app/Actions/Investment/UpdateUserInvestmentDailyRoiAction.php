<?php

declare(strict_types=1);

namespace App\Actions\Investment;

use App\Enums\InvestmentRoiStatus;
use App\Enums\InvestmentStatus;
use App\Models\InvestmentRoiLog;
use App\Models\UserInvestment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateUserInvestmentDailyRoiAction
{
    /**
     * Update purchased package daily ROI and sync today's pending log amount.
     *
     * @return array{investment:UserInvestment, previous_daily_roi:float, daily_roi:float, pending_log_updated:bool}
     */
    public function execute(UserInvestment $investment, float $dailyRoi): array
    {
        if ($investment->status !== InvestmentStatus::ACTIVE) {
            throw ValidationException::withMessages([
                'daily_roi' => 'Daily ROI can only be changed on active investments.',
            ]);
        }

        $dailyRoi = round($dailyRoi, 2);

        if ($dailyRoi < 0.01) {
            throw ValidationException::withMessages([
                'daily_roi' => 'Daily ROI must be at least $0.01.',
            ]);
        }

        return DB::transaction(function () use ($investment, $dailyRoi) {
            $investment = $investment->newQuery()->lockForUpdate()->findOrFail($investment->id);

            if ($investment->status !== InvestmentStatus::ACTIVE) {
                throw ValidationException::withMessages([
                    'daily_roi' => 'Daily ROI can only be changed on active investments.',
                ]);
            }

            $previous = round((float) $investment->daily_roi, 2);

            $investment->forceFill([
                'daily_roi' => $dailyRoi,
                'daily_roi_updated_at' => now(),
            ])->save();

            $pendingUpdated = false;
            $today = now()->toDateString();

            $pendingLog = InvestmentRoiLog::query()
                ->where('user_investment_id', $investment->id)
                ->whereDate('roi_date', $today)
                ->where('status', InvestmentRoiStatus::PENDING)
                ->lockForUpdate()
                ->first();

            if ($pendingLog) {
                $pendingLog->forceFill([
                    'amount' => $dailyRoi,
                ])->save();
                $pendingUpdated = true;
            }

            return [
                'investment' => $investment->fresh(),
                'previous_daily_roi' => $previous,
                'daily_roi' => $dailyRoi,
                'pending_log_updated' => $pendingUpdated,
            ];
        });
    }
}
