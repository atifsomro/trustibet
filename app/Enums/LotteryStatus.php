<?php

namespace App\Enums;

enum LotteryStatus: string
{
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case SELLING = 'selling';
    case ENDED = 'ended';
    case DRAWING = 'drawing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SCHEDULED => 'Scheduled',
            self::SELLING => 'Selling',
            self::ENDED => 'Ended',
            self::DRAWING => 'Drawing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }
}