<?php

declare(strict_types=1);

namespace App\Services\Game;

use App\Models\GamePrize;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class WeightedPrizePicker
{
    /**
     * Pick a prize using relative integer weights.
     *
     * @param  Collection<int, GamePrize>|iterable<GamePrize>  $prizes
     */
    public function pick(iterable $prizes): GamePrize
    {
        $items = Collection::make($prizes)->values();

        if ($items->isEmpty()) {
            throw new InvalidArgumentException('No prizes available to pick from.');
        }

        $totalWeight = (int) $items->sum(fn (GamePrize $prize) => max(0, (int) $prize->weight));

        if ($totalWeight <= 0) {
            throw new InvalidArgumentException('Prize weights must sum to more than zero.');
        }

        $roll = random_int(1, $totalWeight);
        $cursor = 0;

        foreach ($items as $prize) {
            $cursor += max(0, (int) $prize->weight);

            if ($roll <= $cursor) {
                return $prize;
            }
        }

        return $items->last();
    }
}
