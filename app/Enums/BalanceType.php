<?php

declare(strict_types=1);

namespace App\Enums;

enum BalanceType: string
{
    case WITHDRAWABLE = 'withdrawable';

    case BONUS = 'bonus';

    case LOCKED = 'locked';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::WITHDRAWABLE => 'Withdrawable Balance',
            self::BONUS => 'Bonus Balance',
            self::LOCKED => 'Locked Balance',
        };
    }
}