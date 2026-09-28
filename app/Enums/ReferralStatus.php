<?php

declare(strict_types=1);

namespace App\Enums;

enum ReferralStatus: string
{
    case PENDING = 'pending';
    case UNLOCKED = 'unlocked';
    case VOIDED = 'voided';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::UNLOCKED => 'Unlocked',
            self::VOIDED => 'Voided',
        };
    }
}
