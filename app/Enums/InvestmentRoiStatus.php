<?php

declare(strict_types=1);

namespace App\Enums;

enum InvestmentRoiStatus: string
{
    case PENDING = 'pending';
    case CLAIMED = 'claimed';
    case EXPIRED = 'expired';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CLAIMED => 'Claimed',
            self::EXPIRED => 'Expired',
        };
    }
}
