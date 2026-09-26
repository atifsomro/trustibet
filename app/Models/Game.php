<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GameType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Game extends Model
{
    protected $fillable = [
        'type',
        'slug',
        'title',
        'description',
        'image_path',
        'badge',
        'rating',
        'is_active',
        'is_featured',
        'sort_order',
        'config',
    ];

    protected $casts = [
        'type' => GameType::class,
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'config' => 'array',
    ];

    public function packages(): HasMany
    {
        return $this->hasMany(GamePackage::class);
    }

    public function activePackages(): HasMany
    {
        return $this->packages()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(GameRound::class);
    }

    public function plays(): HasMany
    {
        return $this->hasMany(GamePlay::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function imageUrl(): string
    {
        if ($this->image_path) {
            if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
                return $this->image_path;
            }

            if (str_starts_with($this->image_path, 'images/')) {
                return asset($this->image_path);
            }

            return Storage::disk('public')->url($this->image_path);
        }

        return asset('images/games/' . $this->type->viewSlug() . '.png');
    }

    public function configValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->config ?? [], $key, $default);
    }
}
