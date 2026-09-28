<?php

declare(strict_types=1);

namespace App\Enums;

enum BonusType: string
{
    case WELCOME = 'welcome';
    case REFERRAL = 'referral';

    /**
     * Get all enum values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::WELCOME => 'Welcome Bonus',
            self::REFERRAL => 'Referral Bonus',
        };
    }
}