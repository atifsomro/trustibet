<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Game\LimitedDrawService;
use Illuminate\Console\Command;

class DrawLimitedDrawsCommand extends Command
{
    protected $signature = 'games:draw-limited';

    protected $description = 'Settle limited draws whose countdown has ended';

    public function handle(LimitedDrawService $limitedDrawService): int
    {
        $processed = $limitedDrawService->tick();
        $this->info("Processed {$processed} limited draw(s).");

        return self::SUCCESS;
    }
}
