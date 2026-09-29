<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReferralStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_user_id',
        'deposit_id',
        'deposit_amount',
        'bonus_percent',
        'bonus_amount',
        'status',
        'bonus_id',
        'unlocked_at',
    ];

    protected $casts = [
        'deposit_amount' => 'float',
        'bonus_percent' => 'float',
        'bonus_amount' => 'float',
        'status' => ReferralStatus::class,
        'unlocked_at' => 'datetime',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }

    public function bonus(): BelongsTo
    {
        return $this->belongsTo(Bonus::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ReferralStatus::PENDING);
    }

    public function scopeUnlocked(Builder $query): Builder
    {
        return $query->where('status', ReferralStatus::UNLOCKED);
    }

    public function isPending(): bool
    {
        return $this->status === ReferralStatus::PENDING;
    }

    public function isUnlocked(): bool
    {
        return $this->status === ReferralStatus::UNLOCKED;
    }
}
