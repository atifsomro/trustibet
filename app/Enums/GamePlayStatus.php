<?php

declare(strict_types=1);

namespace App\Enums;

enum GamePlayStatus: string
{
    case PENDING = 'pending';
    case WON = 'won';
    case LOST = 'lost';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::WON => 'Won',
            self::LOST => 'Lost',
        };
    }
}
