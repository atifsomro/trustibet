<?php

declare(strict_types=1);

namespace App\Enums;

enum BalanceType: string
{
    case WITHDRAWABLE = 'withdrawable';

    case BONUS = 'bonus';

    case LOCKED = 'locked';

    case ROI = 'roi';

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
            self::ROI => 'ROI Balance',
        };
    }

    public function column(): string
    {
        return match ($this) {
            self::WITHDRAWABLE => 'withdrawable_balance',
            self::BONUS => 'bonus_balance',
            self::LOCKED => 'locked_balance',
            self::ROI => 'roi_balance',
        };
    }
}
