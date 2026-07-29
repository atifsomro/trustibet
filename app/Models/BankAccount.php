<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}