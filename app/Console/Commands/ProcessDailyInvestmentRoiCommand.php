<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Investment\ProcessDailyInvestmentRoiAction;
use Illuminate\Console\Command;
use Throwable;

class ProcessDailyInvestmentRoiCommand extends Command
{
    protected $signature = 'investment:generate-daily-roi';

    protected $description = 'Generate daily investment ROI rows, expire unclaimed ROI, and complete matured packages.';

    public function handle(ProcessDailyInvestmentRoiAction $action): int
    {
        try {
            $result = $action->execute();

            $this->info(sprintf(
                'Investment ROI processed. Generated: %d, Missed(expired): %d, Expired pending: %d, Completed: %d.',
                $result['generated'],
                $result['missed'],
                $result['expired'],
                $result['completed']
            ));

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Investment ROI processing failed: ' . $exception->getMessage());

            return self::FAILURE;
        }
    }
}
