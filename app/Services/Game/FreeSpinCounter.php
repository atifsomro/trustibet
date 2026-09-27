<?php

declare(strict_types=1);

namespace App\Services\Game;

use App\Models\Game;
use App\Models\GamePlay;
use App\Models\User;

class FreeSpinCounter
{
    public function remaining(User $user, Game $game): int
    {
        $daily = (int) $game->configValue('free_spins_daily', 0);

        $todayPlays = GamePlay::query()
            ->where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->whereDate('created_at', today())
            ->get(['fee_amount', 'selection', 'outcome']);

        $used = $todayPlays
            ->filter(fn (GamePlay $play) => (float) $play->fee_amount === 0.0
                && (bool) data_get($play->selection, 'free_spin') === true)
            ->count();

        $granted = (int) $todayPlays
            ->sum(fn (GamePlay $play) => (int) data_get($play->outcome, 'granted_free_spins', 0));

        return max(0, $daily + $granted - $used);
    }
}
