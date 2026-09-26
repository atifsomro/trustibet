<?php

declare(strict_types=1);

namespace App\Enums;

enum GameType: string
{
    case SCRATCH_CARD = 'scratch_card';
    case DICE = 'dice';
    case WHEEL = 'wheel';
    case COLOR_TRADING = 'color_trading';
    case LIMITED_DRAW = 'limited_draw';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::SCRATCH_CARD => 'Scratch Card',
            self::DICE => 'Dice',
            self::WHEEL => 'Lucky Wheel',
            self::COLOR_TRADING => 'Color Trading',
            self::LIMITED_DRAW => 'Limited Draw',
        };
    }

    public function viewSlug(): string
    {
        return match ($this) {
            self::SCRATCH_CARD => 'scratch-card',
            self::DICE => 'dice',
            self::WHEEL => 'wheel',
            self::COLOR_TRADING => 'color-trading',
            self::LIMITED_DRAW => 'limited-draw',
        };
    }

    public static function fromSlug(string $slug): ?self
    {
        return match ($slug) {
            'scratch-card' => self::SCRATCH_CARD,
            'dice' => self::DICE,
            'wheel' => self::WHEEL,
            'color-trading' => self::COLOR_TRADING,
            'limited-draw' => self::LIMITED_DRAW,
            default => null,
        };
    }
}
