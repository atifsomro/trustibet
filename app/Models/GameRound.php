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
        'server_seed_hash',
        'server_seed',
        'fairness_roll',
        'starts_at',
        'locks_at',
        'ends_at',
        'settled_at',
    ];

    protected $hidden = [
        'server_seed',
    ];

    protected $casts = [
        'status' => GameRoundStatus::class,
        'round_number' => 'integer',
        'fairness_roll' => 'integer',
        'starts_at' => 'datetime',
        'locks_at' => 'datetime',
        'ends_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function revealServerSeed(): ?string
    {
        if ($this->status !== GameRoundStatus::SETTLED) {
            return null;
        }

        return $this->server_seed;
    }

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

    /**
     * A stake can still join this round until the winning color is stored.
     * The early lock only warns the screen; it must not reject a bet that
     * arrived while the countdown was still running.
     */
    public function acceptsBets(): bool
    {
        if ($this->status === GameRoundStatus::SETTLED) {
            return false;
        }

        return $this->result_color === null || $this->result_color === '';
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
