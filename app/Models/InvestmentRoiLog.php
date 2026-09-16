<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvestmentRoiStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentRoiLog extends Model
{
    protected $fillable = [
        'user_investment_id',
        'user_id',
        'roi_date',
        'amount',
        'status',
        'claimed_at',
        'expired_at',
        'claim_transaction_id',
    ];

    protected $casts = [
        'roi_date' => 'date',
        'amount' => 'decimal:4',
        'status' => InvestmentRoiStatus::class,
        'claimed_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function userInvestment(): BelongsTo
    {
        return $this->belongsTo(UserInvestment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function claimTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'claim_transaction_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', InvestmentRoiStatus::PENDING);
    }

    public function scopeClaimableToday(Builder $query, ?\DateTimeInterface $today = null): Builder
    {
        $date = ($today ?? now())->format('Y-m-d');

        return $query->pending()->whereDate('roi_date', $date);
    }
}
