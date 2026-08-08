<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BonusStatus;
use App\Enums\BonusType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bonus extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'initial_amount',
        'remaining_amount',
        'status',
        'activated_at',
        'expires_at',
        'completed_at',
    ];

    protected $casts = [
        'type' => BonusType::class,
        'initial_amount' => 'float',
        'remaining_amount' => 'float',
        'status' => BonusStatus::class,
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', BonusStatus::ACTIVE);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === BonusStatus::ACTIVE;
    }
}