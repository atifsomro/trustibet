<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_id',
        'amount',
        'status',
        'payment_method',
        'account_details',
        'approved_by',
        'remarks',
        'requested_at',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'status' => WithdrawalStatus::class,
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
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

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Account details are stored as plain text. Older rows may still be
     * JSON-encoded from when this attribute was incorrectly cast as array.
     */
    public function getAccountDetailsAttribute(?string $value): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"'))
            || str_starts_with($value, '{')
            || str_starts_with($value, '[')
        ) {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', WithdrawalStatus::PENDING);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === WithdrawalStatus::PENDING;
    }
}
