<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BalanceType;
use App\Enums\WalletTransactionType;
use LogicException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'wallet_id',
        'bonus_id',
        'balance_type',
        'type',
        'amount',
        'balance_after',
        'reference_type',
        'reference_id',
        'idempotency_key',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'balance_type' => BalanceType::class,
        'type' => WalletTransactionType::class,
        'amount' => 'integer',
        'balance_after' => 'integer',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function () {
            throw new LogicException('Wallet transactions are immutable.');
        });

        static::deleting(function () {
            throw new LogicException('Wallet transactions cannot be deleted.');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function bonus(): BelongsTo
    {
        return $this->belongsTo(Bonus::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCredits($query)
    {
        return $query->where('amount', '>', 0);
    }

    public function scopeDebits($query)
    {
        return $query->where('amount', '< 0');
    }

    public function scopeWithdrawable($query)
    {
        return $query->where('balance_type', BalanceType::WITHDRAWABLE);
    }

    public function scopeBonus($query)
    {
        return $query->where('balance_type', BalanceType::BONUS);
    }
}