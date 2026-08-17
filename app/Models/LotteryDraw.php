<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LotteryDraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'lottery_id',
        'sales_start_at',
        'sales_end_at',
        'prize_snapshot',
        'status',
        'total_tickets',
        'total_winners',
        'drawn_by',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'total_tickets' => 'integer',
        'total_winners' => 'integer',

        'drawn_by' => 'integer',

        'sales_start_at' => 'datetime',
        'sales_end_at' => 'datetime',
        'prize_snapshot' => 'array',

        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }

    public function winners(): HasMany
    {
        return $this->hasMany(LotteryWinner::class, 'draw_id');
    }


    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }
}