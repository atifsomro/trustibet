<?php

declare(strict_types=1);

namespace App\Enums;

enum GameRoundStatus: string
{
    case BETTING = 'betting';
    case LOCKED = 'locked';
    case SETTLED = 'settled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::BETTING => 'Betting',
            self::LOCKED => 'Locked',
            self::SETTLED => 'Settled',
        };
    }
}
