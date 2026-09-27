<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\GameType;
use App\Models\Game;
use App\Models\GamePackage;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedScratch();
        $this->seedDice();
        $this->seedWheel();
        $this->seedColorTrading();
        $this->seedLimitedDraw();
    }

    public function seedLimitedDraw(): void
    {
        $game = Game::query()->updateOrCreate(
            ['slug' => 'limited-draw'],
            [
                'type' => GameType::LIMITED_DRAW,
                'title' => 'One Rupee Lucky Draw',
                'description' => 'Enter our exclusive Lucky Draw for only Rs.1 and get a chance to win exciting prizes. Every ticket gives you a fair opportunity to become the next lucky winner.',
                'image_path' => 'images/draw/draw.png',
                'badge' => 'Limited Lucky Draw',
                'rating' => 4.9,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5,
                'config' => [
                    'headline' => 'Win Amazing Rewards',
                    'prize_name' => 'Honda CG125',
                    'prize_value' => 280000,
                    'prize_image' => 'images/draw/prize.png',
                    'currency' => 'Rs.',
                    'draw_at' => '2026-10-30 18:00:00',
                    'max_entries' => 50000,
                    'winner_count' => 1,
                    'max_per_user' => 1,
                ],
            ]
        );

        $this->replacePackages($game, [
            [
                'name' => 'Rs.1 Entry',
                'fee' => 1,
                'sort_order' => 1,
                'prizes' => [
                    ['label' => 'Honda CG125', 'prize_amount' => 0, 'weight' => 1],
                ],
            ],
        ]);

        app(\App\Services\Game\LimitedDrawService::class)->sync($game->fresh());
    }

    protected function seedScratch(): void
    {
        $game = Game::query()->updateOrCreate(
            ['slug' => 'scratch-card'],
            [
                'type' => GameType::SCRATCH_CARD,
                'title' => 'Scratch Card',
                'description' => 'Scratch cards instantly and reveal amazing rewards.',
                'image_path' => 'images/games/scratch.png',
                'badge' => 'Popular',
                'rating' => 4.9,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'config' => [],
            ]
        );

        $this->replacePackages($game, [
            [
                'name' => '$1 Card',
                'fee' => 1,
                'sort_order' => 1,
                'prizes' => [
                    ['label' => '$0', 'prize_amount' => 0, 'weight' => 50],
                    ['label' => '$2', 'prize_amount' => 2, 'weight' => 20],
                    ['label' => '$5', 'prize_amount' => 5, 'weight' => 10],
                    ['label' => '$10', 'prize_amount' => 10, 'weight' => 5],
                    ['label' => 'FREE SCRATCH', 'prize_amount' => 1, 'weight' => 8],
                    ['label' => '$0', 'prize_amount' => 0, 'weight' => 30],
                ],
            ],
            [
                'name' => '$5 Card',
                'fee' => 5,
                'sort_order' => 2,
                'prizes' => [
                    ['label' => '$0', 'prize_amount' => 0, 'weight' => 40],
                    ['label' => '$5', 'prize_amount' => 5, 'weight' => 20],
                    ['label' => '$10', 'prize_amount' => 10, 'weight' => 15],
                    ['label' => '$25', 'prize_amount' => 25, 'weight' => 8],
                    ['label' => '$50', 'prize_amount' => 50, 'weight' => 3],
                ],
            ],
            [
                'name' => '$10 Card',
                'fee' => 10,
                'sort_order' => 3,
                'prizes' => [
                    ['label' => '$0', 'prize_amount' => 0, 'weight' => 35],
                    ['label' => '$10', 'prize_amount' => 10, 'weight' => 25],
                    ['label' => '$25', 'prize_amount' => 25, 'weight' => 15],
                    ['label' => '$50', 'prize_amount' => 50, 'weight' => 8],
                    ['label' => '$100', 'prize_amount' => 100, 'weight' => 2],
                ],
            ],
        ]);
    }

    protected function seedDice(): void
    {
        $game = Game::query()->updateOrCreate(
            ['slug' => 'dice'],
            [
                'type' => GameType::DICE,
                'title' => 'Dice Game',
                'description' => 'Roll the dice and predict the winning number.',
                'image_path' => 'images/games/dice.png',
                'badge' => 'Trending',
                'rating' => 4.8,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'config' => ['faces' => 6],
            ]
        );

        $this->replacePackages($game, [
            [
                'name' => '$1 / 1 Chance',
                'fee' => 1,
                'meta' => ['chances' => 1, 'multiplier' => 5.5],
                'sort_order' => 1,
                'prizes' => [
                    ['label' => 'Win $5.50', 'prize_amount' => 5.50, 'weight' => 1],
                ],
            ],
            [
                'name' => '$5 / 6 Chances',
                'fee' => 5,
                'meta' => ['chances' => 6, 'multiplier' => 5.5],
                'sort_order' => 2,
                'prizes' => [
                    ['label' => 'Win $27.50', 'prize_amount' => 27.50, 'weight' => 1],
                ],
            ],
            [
                'name' => '$10 / 15 Chances',
                'fee' => 10,
                'meta' => ['chances' => 15, 'multiplier' => 5.5],
                'sort_order' => 3,
                'prizes' => [
                    ['label' => 'Win $55', 'prize_amount' => 55, 'weight' => 1],
                ],
            ],
            [
                'name' => '$25 / 45 Chances',
                'fee' => 25,
                'meta' => ['chances' => 45, 'multiplier' => 5.5],
                'sort_order' => 4,
                'prizes' => [
                    ['label' => 'Win $137.50', 'prize_amount' => 137.50, 'weight' => 1],
                ],
            ],
        ]);
    }

    protected function seedWheel(): void
    {
        $game = Game::query()->updateOrCreate(
            ['slug' => 'wheel'],
            [
                'type' => GameType::WHEEL,
                'title' => 'Lucky Wheel',
                'description' => 'Spin the wheel and unlock exciting lucky prizes.',
                'image_path' => 'images/games/wheel.png',
                'badge' => 'Hot',
                'rating' => 4.9,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 3,
                'config' => ['free_spins_daily' => 2],
            ]
        );

        $segments = [
            [
                'label' => '2 Spins',
                'prize_amount' => 0,
                'weight' => 20,
                'meta' => ['color' => '#22c55e', 'segment' => 1, 'free_spins' => 2],
            ],
            ['label' => '$10', 'prize_amount' => 10, 'weight' => 12, 'meta' => ['color' => '#3b82f6', 'segment' => 2]],
            ['label' => '$1', 'prize_amount' => 1, 'weight' => 18, 'meta' => ['color' => '#f59e0b', 'segment' => 3]],
            ['label' => '$0', 'prize_amount' => 0, 'weight' => 40, 'meta' => ['color' => '#ef4444', 'segment' => 4]],
            ['label' => '$5', 'prize_amount' => 5, 'weight' => 8, 'meta' => ['color' => '#06b6d4', 'segment' => 5]],
            ['label' => '$100', 'prize_amount' => 100, 'weight' => 2, 'meta' => ['color' => '#9333ea', 'segment' => 6]],
            ['label' => '$0', 'prize_amount' => 0, 'weight' => 40, 'meta' => ['color' => '#ef4444', 'segment' => 7]],
            ['label' => '$3', 'prize_amount' => 3, 'weight' => 12, 'meta' => ['color' => '#14b8a6', 'segment' => 8]],
            ['label' => '$0', 'prize_amount' => 0, 'weight' => 40, 'meta' => ['color' => '#ef4444', 'segment' => 9]],
            [
                'label' => '2 Spins',
                'prize_amount' => 0,
                'weight' => 20,
                'meta' => ['color' => '#22c55e', 'segment' => 10, 'free_spins' => 2],
            ],
            ['label' => '$10', 'prize_amount' => 10, 'weight' => 12, 'meta' => ['color' => '#3b82f6', 'segment' => 11]],
        ];

        $this->replacePackages($game, [
            [
                'name' => '$1 / 1 Spin',
                'fee' => 1,
                'meta' => ['spins' => 1],
                'sort_order' => 1,
                'prizes' => $segments,
            ],
            [
                'name' => '$5 / 6 Spins',
                'fee' => 5,
                'meta' => ['spins' => 6],
                'sort_order' => 2,
                'prizes' => $segments,
            ],
            [
                'name' => '$10 / 15 Spins',
                'fee' => 10,
                'meta' => ['spins' => 15],
                'sort_order' => 3,
                'prizes' => $segments,
            ],
            [
                'name' => '$25 / 45 Spins',
                'fee' => 25,
                'meta' => ['spins' => 45],
                'sort_order' => 4,
                'prizes' => $segments,
            ],
        ]);
    }

    protected function seedColorTrading(): void
    {
        $colors = [
            'green', 'red', 'blue', 'yellow', 'orange',
            'purple', 'pink', 'cyan', 'white', 'black',
        ];

        $game = Game::query()->updateOrCreate(
            ['slug' => 'color-trading'],
            [
                'type' => GameType::COLOR_TRADING,
                'title' => 'Color Trading',
                'description' => 'Predict the winning color and earn big rewards.',
                'image_path' => 'images/games/trading.png',
                'badge' => 'New',
                'rating' => 4.8,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 4,
                'config' => [
                    'round_seconds' => 10,
                    'lock_seconds' => 5,
                    'colors' => $colors,
                ],
            ]
        );

        $colorWeights = collect($colors)->map(fn ($color, $i) => [
            'label' => ucfirst($color),
            'prize_amount' => 0,
            'weight' => 10,
            'meta' => ['color' => $color],
            'sort_order' => $i,
        ])->all();

        $this->replacePackages($game, [
            [
                'name' => 'Chip $10',
                'fee' => 10,
                'meta' => ['chip_value' => 10, 'multiplier' => 2],
                'sort_order' => 1,
                'prizes' => array_merge($colorWeights, [
                    ['label' => 'Win $20', 'prize_amount' => 20, 'weight' => 0],
                ]),
            ],
            [
                'name' => 'Chip $50',
                'fee' => 50,
                'meta' => ['chip_value' => 50, 'multiplier' => 2],
                'sort_order' => 2,
                'prizes' => array_merge($colorWeights, [
                    ['label' => 'Win $100', 'prize_amount' => 100, 'weight' => 0],
                ]),
            ],
            [
                'name' => 'Chip $100',
                'fee' => 100,
                'meta' => ['chip_value' => 100, 'multiplier' => 2],
                'sort_order' => 3,
                'prizes' => array_merge($colorWeights, [
                    ['label' => 'Win $200', 'prize_amount' => 200, 'weight' => 0],
                ]),
            ],
            [
                'name' => 'Chip $500',
                'fee' => 500,
                'meta' => ['chip_value' => 500, 'multiplier' => 2],
                'sort_order' => 4,
                'prizes' => array_merge($colorWeights, [
                    ['label' => 'Win $1000', 'prize_amount' => 1000, 'weight' => 0],
                ]),
            ],
            [
                'name' => 'Chip $1000',
                'fee' => 1000,
                'meta' => ['chip_value' => 1000, 'multiplier' => 2],
                'sort_order' => 5,
                'prizes' => array_merge($colorWeights, [
                    ['label' => 'Win $2000', 'prize_amount' => 2000, 'weight' => 0],
                ]),
            ],
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $packages
     */
    protected function replacePackages(Game $game, array $packages): void
    {
        $game->packages()->each(function (GamePackage $package) {
            $package->prizes()->delete();
            $package->delete();
        });

        foreach ($packages as $packageData) {
            $prizes = $packageData['prizes'] ?? [];
            unset($packageData['prizes']);

            $package = $game->packages()->create([
                'name' => $packageData['name'],
                'fee' => $packageData['fee'],
                'meta' => $packageData['meta'] ?? [],
                'is_active' => true,
                'sort_order' => $packageData['sort_order'] ?? 0,
            ]);

            foreach ($prizes as $index => $prize) {
                $package->prizes()->create([
                    'label' => $prize['label'],
                    'prize_amount' => $prize['prize_amount'],
                    'weight' => $prize['weight'] ?? 1,
                    'meta' => $prize['meta'] ?? [],
                    'is_active' => true,
                    'sort_order' => $prize['sort_order'] ?? $index,
                ]);
            }
        }
    }
}
