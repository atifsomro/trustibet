<?php

declare(strict_types=1);

namespace App\Services\Game;

use App\Models\GamePackage;
use App\Models\GamePlay;
use App\Models\User;

class PackageCreditLedger
{
    public function isBundled(GamePackage $package): bool
    {
        return $this->allowance($package) > 0
            && ($package->metaValue('spins') !== null || $package->metaValue('chances') !== null);
    }

    public function allowance(GamePackage $package): int
    {
        $spins = (int) $package->metaValue('spins', 0);
        $chances = (int) $package->metaValue('chances', 0);

        return max(0, $spins, $chances);
    }

    public function remaining(User $user, GamePackage $package, bool $lock = false): int
    {
        if (! $this->isBundled($package)) {
            return 0;
        }

        $query = GamePlay::query()
            ->where('user_id', $user->id)
            ->where('game_package_id', $package->id)
            ->orderBy('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        $balance = 0;

        foreach ($query->get(['fee_amount', 'selection', 'outcome']) as $play) {
            if ((bool) data_get($play->selection, 'free_spin') === true) {
                continue;
            }

            $isBundlePlay = (bool) data_get($play->outcome, 'package_bundle') === true
                || (bool) data_get($play->selection, 'package_credit') === true;

            if (! $isBundlePlay) {
                continue;
            }

            if ((float) $play->fee_amount > 0) {
                $granted = (int) data_get($play->outcome, 'package_credits', 0);
                $balance += $granted > 0 ? $granted : $this->allowance($package);
            }

            $balance -= 1;
        }

        return max(0, $balance);
    }
}
