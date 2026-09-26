<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Game\ColorRoundTicker;
use Illuminate\Console\Command;

class TickColorRoundsCommand extends Command
{
    protected $signature = 'games:tick-color-rounds';

    protected $description = 'Create, lock, and settle color trading rounds';

    public function handle(ColorRoundTicker $ticker): int
    {
        $processed = $ticker->tick();
        $this->info("Processed {$processed} color round action(s).");

        return self::SUCCESS;
    }
}
