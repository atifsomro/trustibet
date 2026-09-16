<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvestmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class UserInvestment extends Model
{
    protected $fillable = [
        'user_id',
        'investment_package_id',
        'package_name',
        'price',
        'daily_roi',
        'daily_roi_updated_at',
        'total_days',
        'feature_points',
        'starts_at',
        'ends_at',
        'status',
        'purchase_transaction_id',
        'principal_return_transaction_id',
        'completed_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'daily_roi' => 'decimal:4',
        'daily_roi_updated_at' => 'datetime',
        'total_days' => 'integer',
        'feature_points' => 'array',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'status' => InvestmentStatus::class,
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(InvestmentPackage::class, 'investment_package_id');
    }

    public function roiLogs(): HasMany
    {
        return $this->hasMany(InvestmentRoiLog::class);
    }

    public function purchaseTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'purchase_transaction_id');
    }

    public function principalReturnTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'principal_return_transaction_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', InvestmentStatus::ACTIVE);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', InvestmentStatus::COMPLETED);
    }

    /**
     * First claimable ROI date is the calendar day after purchase.
     */
    public function firstRoiDate(): Carbon
    {
        return $this->starts_at->copy()->addDay();
    }

    /**
     * Last claimable ROI date equals ends_at (total_days claimable days).
     */
    public function lastRoiDate(): Carbon
    {
        return $this->ends_at->copy();
    }

    public function daysElapsed(?Carbon $today = null): int
    {
        $today = ($today ?? now())->startOfDay();
        $start = $this->starts_at->copy()->startOfDay();

        if ($today->lt($start)) {
            return 0;
        }

        return (int) min(
            $this->total_days,
            $start->diffInDays($today)
        );
    }

    public function isMatured(?Carbon $today = null): bool
    {
        $today = ($today ?? now())->startOfDay();

        return $today->gt($this->ends_at->copy()->startOfDay());
    }
}
