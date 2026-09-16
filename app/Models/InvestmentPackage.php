<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestmentPackage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'daily_roi',
        'total_days',
        'feature_points',
        'is_active',
        'is_recommended',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'daily_roi' => 'decimal:4',
        'total_days' => 'integer',
        'feature_points' => 'array',
        'is_active' => 'boolean',
        'is_recommended' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function userInvestments(): HasMany
    {
        return $this->hasMany(UserInvestment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('price')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Shared feature points prefilled on the admin create/edit form.
     *
     * @return list<string>
     */
    public static function defaultFeaturePoints(): array
    {
        return [
            'Daily ROI: $0.00',
            'Maintenance Fee: 2.5%',
            'One Time Deposit Fee',
            '24/7 Customer Support',
            'Instant Withdrawal',
        ];
    }

    /**
     * Human-friendly duration label (e.g. "5 Year", "30 Days").
     */
    public function durationLabel(): string
    {
        $days = (int) $this->total_days;

        if ($days >= 365 && $days % 365 === 0) {
            $years = (int) ($days / 365);

            return $years === 1 ? '1 Year' : "{$years} Year";
        }

        return $days === 1 ? '1 Day' : "{$days} Days";
    }
}
