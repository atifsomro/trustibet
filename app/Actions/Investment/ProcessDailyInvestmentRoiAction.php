<?php

declare(strict_types=1);

namespace App\Actions\Investment;

use App\Enums\InvestmentRoiStatus;
use App\Enums\InvestmentStatus;
use App\Models\InvestmentRoiLog;
use App\Models\UserInvestment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessDailyInvestmentRoiAction
{
    public function __construct(
        protected CompleteUserInvestmentAction $completeUserInvestmentAction,
    ) {
    }

    /**
     * Expire stale pending ROI, generate today's pending rows,
     * and complete matured investments.
     *
     * @return array{expired:int, generated:int, missed:int, completed:int}
     */
    public function execute(?Carbon $today = null): array
    {
        $today = ($today ?? now())->copy()->startOfDay();

        $expired = $this->expireStalePending($today);
        [$generated, $missed] = $this->generateRoiLogs($today);
        $completed = $this->completeMaturedInvestments($today);

        return [
            'expired' => $expired,
            'generated' => $generated,
            'missed' => $missed,
            'completed' => $completed,
        ];
    }

    protected function expireStalePending(Carbon $today): int
    {
        return InvestmentRoiLog::query()
            ->pending()
            ->whereDate('roi_date', '<', $today->toDateString())
            ->update([
                'status' => InvestmentRoiStatus::EXPIRED,
                'expired_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /**
     * @return array{0:int,1:int}
     */
    protected function generateRoiLogs(Carbon $today): array
    {
        $generated = 0;
        $missed = 0;

        $investments = UserInvestment::query()
            ->active()
            ->get();

        foreach ($investments as $investment) {
            $first = $investment->firstRoiDate()->startOfDay();
            $last = $investment->lastRoiDate()->startOfDay();

            if ($today->lt($first)) {
                continue;
            }

            $cursor = $first->copy();
            $end = $today->lt($last) ? $today->copy() : $last->copy();

            while ($cursor->lte($end)) {
                $exists = InvestmentRoiLog::query()
                    ->where('user_investment_id', $investment->id)
                    ->whereDate('roi_date', $cursor->toDateString())
                    ->exists();

                if (! $exists) {
                    $isToday = $cursor->isSameDay($today);

                    InvestmentRoiLog::query()->create([
                        'user_investment_id' => $investment->id,
                        'user_id' => $investment->user_id,
                        'roi_date' => $cursor->toDateString(),
                        'amount' => $investment->daily_roi,
                        'status' => $isToday
                            ? InvestmentRoiStatus::PENDING
                            : InvestmentRoiStatus::EXPIRED,
                        'expired_at' => $isToday ? null : now(),
                    ]);

                    if ($isToday) {
                        $generated++;
                    } else {
                        $missed++;
                    }
                }

                $cursor->addDay();
            }
        }

        return [$generated, $missed];
    }

    protected function completeMaturedInvestments(Carbon $today): int
    {
        $completed = 0;

        $investments = UserInvestment::query()
            ->active()
            ->whereDate('ends_at', '<', $today->toDateString())
            ->get();

        foreach ($investments as $investment) {
            $hasPending = $investment->roiLogs()
                ->pending()
                ->exists();

            if ($hasPending) {
                continue;
            }

            DB::transaction(function () use ($investment, &$completed) {
                $locked = UserInvestment::query()
                    ->lockForUpdate()
                    ->find($investment->id);

                if (
                    ! $locked
                    || $locked->status !== InvestmentStatus::ACTIVE
                ) {
                    return;
                }

                $this->completeUserInvestmentAction->execute($locked);
                $completed++;
            });
        }

        return $completed;
    }
}
