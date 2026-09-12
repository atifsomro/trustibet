<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LotteryTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'lottery_id',
        'user_id',
        'ticket_number',
        'price',
        'status',
        'purchase_transaction_id',
        'refund_transaction_id',
        'purchased_at',
        'refunded_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'purchased_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Lottery this ticket belongs to.
     */
    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class)->withTrashed();
    }

    /**
     * User who purchased this ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Winning record for this ticket, if any.
     */
    public function winner(): HasOne
    {
        return $this->hasOne(LotteryWinner::class, 'ticket_id');
    }

    /**
     * Purchase wallet transaction.
     */
    public function purchaseTransaction(): BelongsTo
    {
        return $this->belongsTo(
            WalletTransaction::class,
            'purchase_transaction_id'
        );
    }

    /**
     * Refund wallet transaction.
     */
    public function refundTransaction(): BelongsTo
    {
        return $this->belongsTo(
            WalletTransaction::class,
            'refund_transaction_id'
        );
    }

    /**
     * Is this ticket active?
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Is this ticket a winner?
     */
    public function isWinner(): bool
    {
        return $this->status === 'winner';
    }

    /**
     * Is this ticket a losing ticket after a draw?
     */
    public function isLost(): bool
    {
        return $this->status === 'lost';
    }

    /**
     * Is this ticket refunded?
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }
}