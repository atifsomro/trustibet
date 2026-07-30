<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'bank_account_id',
        'amount',
        'currency',
        'reference_number',
        'payment_proof',
        'remarks',
        'status',
        'approved_by',
        'approved_at',
        'admin_remarks',
    ];


    /**
     * Attribute casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Deposit owner.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Selected bank account.
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }


    /**
     * Admin who approved/rejected the deposit.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }


    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */


    /**
     * Pending deposits.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }


    /**
     * Approved deposits.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }


    /**
     * Rejected deposits.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }


    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */


    /**
     * Check pending status.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }


    /**
     * Check approved status.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }


    /**
     * Check rejected status.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }


    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'warning',
        };
    }


    /**
     * Get readable status.
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }


    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */


    /**
     * Approve deposit.
     *
     * Wallet credit logic will be added here later.
     */
    public function approve(int $adminId, ?string $remarks = null): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        return $this->update([
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_remarks' => $remarks,
        ]);
    }


    /**
     * Reject deposit.
     */
    public function reject(int $adminId, ?string $remarks = null): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        return $this->update([
            'status' => 'rejected',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_remarks' => $remarks,
        ]);
    }


    /**
     * Check if user can cancel deposit.
     */
    public function canCancel(): bool
    {
        return $this->isPending();
    }
}