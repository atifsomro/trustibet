<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Lottery\DrawLotteryAction;
use App\Models\Lottery;
use Illuminate\Console\Command;
use Throwable;

class DrawDueLotteriesCommand extends Command
{
    protected $signature = 'lottery:draw-due';

    protected $description = 'Draw every lottery whose countdown has ended and has not yet been drawn.';

    public function handle(DrawLotteryAction $action): int
    {
        $lotteries = Lottery::query()->dueForDraw()->get();

        if ($lotteries->isEmpty()) {
            $this->info('No lotteries are due for draw.');

            return self::SUCCESS;
        }

        foreach ($lotteries as $lottery) {
            try {
                $draw = $action->execute($lottery);

                $this->info(sprintf(
                    'Lottery #%d (%s) drawn. Winners announced: %s.',
                    $lottery->id,
                    $lottery->title,
                    $draw->winners_announced ? 'yes' : 'no'
                ));
            } catch (Throwable $exception) {
                report($exception);

                $this->error(sprintf(
                    'Lottery #%d failed: %s',
                    $lottery->id,
                    $exception->getMessage()
                ));
            }
        }

        return self::SUCCESS;
    }
};
