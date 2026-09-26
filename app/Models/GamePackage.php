<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GamePackage extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'fee',
        'meta',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'meta' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function prizes(): HasMany
    {
        return $this->hasMany(GamePrize::class);
    }

    public function activePrizes(): HasMany
    {
        return $this->prizes()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function plays(): HasMany
    {
        return $this->hasMany(GamePlay::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function metaValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->meta ?? [], $key, $default);
    }

    public function winPrize(): ?GamePrize
    {
        return $this->activePrizes()
            ->where('prize_amount', '>', 0)
            ->orderByDesc('prize_amount')
            ->first();
    }
}
