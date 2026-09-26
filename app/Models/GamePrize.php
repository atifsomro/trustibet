<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamePrize extends Model
{
    protected $fillable = [
        'game_package_id',
        'label',
        'prize_amount',
        'weight',
        'meta',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'prize_amount' => 'decimal:2',
        'weight' => 'integer',
        'meta' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(GamePackage::class, 'game_package_id');
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
}
