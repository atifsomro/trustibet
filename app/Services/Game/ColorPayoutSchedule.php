<?php

declare(strict_types=1);

namespace App\Services\Game;

use App\Models\Game;
use Carbon\CarbonInterface;

class ColorPayoutSchedule
{
    /**
     * Profit percentages that rotate every window (e.g. 70 => $1 pays $1.70).
     *
     * @return list<float>
     */
    public function rates(Game $game): array
    {
        $configured = $game->configValue('payout_rates', [70, 75, 80, 85, 90, 93]);

        $rates = collect(is_array($configured) ? $configured : [])
            ->map(fn ($rate) => (float) $rate)
            ->filter(fn ($rate) => $rate > 0)
            ->values()
            ->all();

        return $rates !== [] ? $rates : [70, 75, 80, 85, 90, 93];
    }

    public function windowMinutes(Game $game): int
    {
        return max(1, (int) $game->configValue('payout_window_minutes', 5));
    }

    /**
     * @return array{
     *     rate: float,
     *     multiplier: float,
     *     window_minutes: int,
     *     seconds_remaining: int,
     *     index: int
     * }
     */
    public function current(Game $game, ?CarbonInterface $at = null): array
    {
        $at ??= now();
        $rates = $this->rates($game);
        $windowMinutes = $this->windowMinutes($game);
        $windowSeconds = $windowMinutes * 60;

        $slot = intdiv($at->getTimestamp(), $windowSeconds);
        $index = $slot % count($rates);
        $rate = (float) $rates[$index];
        $slotEnd = ($slot + 1) * $windowSeconds;
        $secondsRemaining = max(0, $slotEnd - $at->getTimestamp());

        return [
            'rate' => $rate,
            'multiplier' => round(1 + ($rate / 100), 4),
            'window_minutes' => $windowMinutes,
            'seconds_remaining' => $secondsRemaining,
            'index' => $index,
        ];
    }

    public function payoutFor(float $stake, float $multiplier): float
    {
        return round(max(0, $stake) * max(0, $multiplier), 2);
    }
}
