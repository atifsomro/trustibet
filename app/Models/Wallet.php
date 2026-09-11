<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'withdrawable_balance',
        'bonus_balance',
        'locked_balance',
        'roi_balance',
        'currency',
        'version',
    ];

    protected $casts = [
        'withdrawable_balance' => 'float',
        'bonus_balance' => 'float',
        'locked_balance' => 'float',
        'roi_balance' => 'float',
        'version' => 'integer',
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

    public function bonuses(): HasMany
    {
        return $this->hasMany(Bonus::class, 'user_id', 'user_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getBalance(string $balanceType): float
    {
        return match ($balanceType) {
            'withdrawable' => (float) $this->withdrawable_balance,
            'bonus' => (float) $this->bonus_balance,
            'locked' => (float) $this->locked_balance,
            'roi' => (float) $this->roi_balance,
            default => 0.0,
        };
    }
}