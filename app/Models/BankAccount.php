<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class BankAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'bank_name',
        'account_title',
        'account_number',
        'iban',
        'swift_code',
        'branch_name',
        'branch_code',
        'currency',
        'conversion_rate',
        'type',
        'picture',
        'qr_code',
        'instructions',
        'sort_order',
        'is_active',
    ];

    /**
     * Attribute casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get all deposits made to this bank account.
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Active bank accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Ordered by sort order then latest.
     */
    public function scopeOrdered($query)
    {
        return $query
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Public URL for the payment method logo.
     */
    public function getPictureUrlAttribute(): string
    {
        $fallback = $this->fallbackAssetUrl('picture');

        // Prefer bundled brand logos — they are reliable across local hosts.
        if (! str_ends_with($fallback, 'placeholder.webp')) {
            return $fallback;
        }

        if ($this->picture && Storage::disk('public')->exists($this->picture)) {
            return asset('storage/' . ltrim($this->picture, '/'));
        }

        return $fallback;
    }

    /**
     * Public URL for the payment QR code image.
     */
    public function getQrCodeUrlAttribute(): string
    {
        $fallback = $this->fallbackAssetUrl('qr');

        if (! str_ends_with($fallback, 'placeholder.webp')) {
            return $fallback;
        }

        if ($this->qr_code && Storage::disk('public')->exists($this->qr_code)) {
            return asset('storage/' . ltrim($this->qr_code, '/'));
        }

        return $fallback;
    }

    /**
     * Get complete account details.
     */
    public function getFullAccountAttribute(): string
    {
        return "{$this->bank_name} - {$this->account_title}";
    }

    /**
     * Get masked account number.
     */
    public function getMaskedAccountNumberAttribute(): string
    {
        $length = strlen($this->account_number);

        if ($length <= 4) {
            return $this->account_number;
        }

        return str_repeat('*', $length - 4) . substr($this->account_number, -4);
    }

    /**
     * Status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active
            ? 'success'
            : 'secondary';
    }

    /**
     * Status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active
            ? 'Active'
            : 'Inactive';
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if account can be deleted.
     */
    public function canDelete(): bool
    {
        return !$this->deposits()->exists();
    }

    /**
     * Fallback brand assets when uploaded storage files are missing.
     */
    protected function fallbackAssetUrl(string $type): string
    {
        $key = strtolower(trim((string) $this->bank_name));

        $logos = [
            'jazzcash' => 'images/account/jazzcash.png',
            'easypaisa' => 'images/account/easypaisa.png',
            'binance' => 'images/account/binance.png',
            'binance pay' => 'images/account/binance.png',
            'binance pay id' => 'images/account/binance.png',
            'trc20' => 'images/account/trc20.svg',
            'till' => 'images/account/till.svg',
        ];

        $qrs = [
            'jazzcash' => 'images/account/jazzcashqr.png',
            'easypaisa' => 'images/account/easypaisaqr.png',
            'binance' => 'images/account/binanceqr.png',
            'binance pay' => 'images/account/binanceqr.png',
            'binance pay id' => 'images/account/binanceqr.png',
        ];

        $map = $type === 'qr' ? $qrs : $logos;

        foreach ($map as $needle => $path) {
            if ($key === $needle || str_contains($key, $needle)) {
                return asset($path);
            }
        }

        return asset('images/placeholder/placeholder.webp');
    }
}
