<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotteryWinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'lottery_id',
        'draw_id',
        'ticket_id',
        'user_id',
        'prize_category',
        'prize_position',
        'prize_amount',
        'payout_status',
        'payout_transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'prize_position' => 'integer',

        'prize_amount' => 'decimal:2',

        'payout_transaction_id' => 'integer',

        'paid_at' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class)->withTrashed();
    }

    public function draw(): BelongsTo
    {
        return $this->belongsTo(LotteryDraw::class, 'draw_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(LotteryTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    public function isPaid(): bool
    {
        return $this->payout_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->payout_status === 'pending';
    }

    public function hasFailed(): bool
    {
        return $this->payout_status === 'failed';
    }
}