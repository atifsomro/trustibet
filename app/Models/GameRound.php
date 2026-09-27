<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GameRoundStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class GameRound extends Model
{
    protected $fillable = [
        'game_id',
        'round_number',
        'status',
        'result_color',
        'starts_at',
        'locks_at',
        'ends_at',
        'settled_at',
    ];

    protected $casts = [
        'status' => GameRoundStatus::class,
        'round_number' => 'integer',
        'starts_at' => 'datetime',
        'locks_at' => 'datetime',
        'ends_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function plays(): HasMany
    {
        return $this->hasMany(GamePlay::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            GameRoundStatus::BETTING,
            GameRoundStatus::LOCKED,
        ]);
    }

    public function isBetting(): bool
    {
        return $this->status === GameRoundStatus::BETTING
            && now()->lt($this->locks_at);
    }

    public function secondsRemaining(?Carbon $now = null): int
    {
        $now ??= now();
        $endsAt = $this->ends_at;

        if (! $endsAt) {
            return 0;
        }

        return max(0, $endsAt->getTimestamp() - $now->getTimestamp());
    }
}
