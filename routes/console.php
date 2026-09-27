<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('lottery:draw-due')
    ->everyMinute()
    ->withoutOverlapping(5);

Schedule::command('investment:generate-daily-roi')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('games:tick-color-rounds')
    ->everyMinute()
    ->withoutOverlapping(2);

Schedule::command('games:draw-limited')
    ->everyMinute()
    ->withoutOverlapping(2);
