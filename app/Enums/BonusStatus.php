<?php

declare(strict_types=1);

namespace App\Enums;

enum BonusStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';
    case VOIDED = 'voided';

    /**
     * Get all values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Is active?
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Human readable label.
     */
    public function label(): string
    {
        return ucwords($this->value);
    }
}