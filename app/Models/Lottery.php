<?php

namespace App\Models;

use App\Enums\LotteryStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lottery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'ticket_price',
        'currency',
        'sales_start_at',
        'sales_end_at',
        'draw_at',

        'first_prize',
        'second_prize',
        'third_prize',
        'fourth_prize',
        'fifth_prize',

        'max_tickets',
        'sort_order',
        'description',
        'status',
        'is_active',
    ];

    protected $casts = [
        'ticket_price' => 'decimal:2',

        'sales_start_at' => 'datetime',
        'sales_end_at' => 'datetime',
        'draw_at' => 'datetime',

        'first_prize' => 'decimal:2',
        'second_prize' => 'decimal:2',
        'third_prize' => 'decimal:2',
        'fourth_prize' => 'decimal:2',
        'fifth_prize' => 'decimal:2',

        'max_tickets' => 'integer',
        'sort_order' => 'integer',

        'is_active' => 'boolean',

        'status' => LotteryStatus::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function tickets(): HasMany
    {
        return $this->hasMany(LotteryTicket::class);
    }

    public function draw(): HasOne
    {
        return $this->hasOne(LotteryDraw::class);
    }

    public function draws(): HasMany
    {
        return $this->hasMany(LotteryDraw::class, 'lottery_id');
    }

    public function latestCompletedDraw(): HasOne
    {
        return $this->hasOne(LotteryDraw::class)
            ->where('status', 'completed')
            ->latestOfMany('id');
    }

    public function winners(): HasMany
    {
        return $this->hasMany(LotteryWinner::class);
    }

    public function ticketsForUser(int $userId): int
    {
        return $this->tickets()
            ->where('user_id', $userId)
            ->whereIn('status', ['active', 'winner'])
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSelling(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('status', LotteryStatus::SELLING->value)
            ->where(function ($query) {
                $query
                    ->whereNull('sales_start_at')
                    ->orWhere('sales_start_at', '<=', now());
            })
            ->where('sales_end_at', '>', now());
    }
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc'); // or whatever column makes sense
    }
    /*
    |--------------------------------------------------------------------------
    | Lottery State
    |--------------------------------------------------------------------------
    */

    public function isSalesOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->isSelling()) {
            return false;
        }

        if ($this->sales_start_at && now()->lt($this->sales_start_at)) {
            return false;
        }

        if (now()->gte($this->sales_end_at)) {
            return false;
        }

        return true;
    }

    public function hasEnded(): bool
    {
        return now()->gte($this->sales_end_at);
    }

    public function canDraw(): bool
    {
        if (!$this->hasEnded() || !$this->is_active || $this->isCancelled() || $this->isDrawing()) {
            return false;
        }

        /*
         * A completed draw from a previous round is allowed.
         * Only the currently configured sales period can block another draw.
         */
        $query = $this->draws()
            ->whereIn('status', ['pending', 'running', 'completed']);

        if ($this->sales_start_at) {
            $query->where('sales_start_at', $this->sales_start_at);
        } else {
            $query->whereNull('sales_start_at');
        }

        $query->where('sales_end_at', $this->sales_end_at);

        return !$query->exists();
    }

    public function isDraft(): bool
    {
        return $this->status === LotteryStatus::DRAFT;
    }

    public function isScheduled(): bool
    {
        return $this->status === LotteryStatus::SCHEDULED;
    }

    public function isSelling(): bool
    {
        return $this->status === LotteryStatus::SELLING;
    }

    public function isEnded(): bool
    {
        return $this->status === LotteryStatus::ENDED;
    }

    public function isDrawing(): bool
    {
        return $this->status === LotteryStatus::DRAWING;
    }

    public function isCompleted(): bool
    {
        return $this->status === LotteryStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === LotteryStatus::CANCELLED;
    }

    public function canBuyTickets(): bool
    {
        return $this->isSalesOpen()
            && !$this->hasReachedTicketLimit();
    }

    public function hasReachedTicketLimit(): bool
    {
        if ($this->max_tickets === null) {
            return false;
        }

        return $this->totalCurrentRoundTickets() >= $this->max_tickets;
    }

    public function canCancel(): bool
    {
        return in_array($this->status, [
            LotteryStatus::SCHEDULED,
            LotteryStatus::SELLING,
            LotteryStatus::ENDED,
        ], true);
    }

    public function canExtend(): bool
    {
        return in_array($this->status, [
            LotteryStatus::SCHEDULED,
            LotteryStatus::SELLING,
            LotteryStatus::ENDED,
        ], true)
            && !$this->hasReachedRequiredTickets();
    }

    public function markScheduled(): void
    {
        $this->update([
            'status' => LotteryStatus::SCHEDULED,
        ]);
    }

    public function startSelling(): void
    {
        $this->update([
            'status' => LotteryStatus::SELLING,
        ]);
    }

    public function markEnded(): void
    {
        $this->update([
            'status' => LotteryStatus::ENDED,
        ]);
    }

    public function startDrawing(): void
    {
        $this->update([
            'status' => LotteryStatus::DRAWING,
        ]);
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => LotteryStatus::COMPLETED,
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => LotteryStatus::CANCELLED,
            'is_active' => false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Ticket Information
    |--------------------------------------------------------------------------
    */

    public function totalTicketsSold(): int
    {
        return $this->tickets()
            ->whereIn('status', ['active', 'winner'])
            ->count();
    }

    /**
     * Tickets purchased during the currently configured draw period.
     */
    public function currentRoundTickets()
    {
        $query = $this->tickets()
            ->whereIn('status', ['active', 'winner']);

        if ($this->sales_start_at) {
            $query->where('purchased_at', '>=', $this->sales_start_at);
        }

        if ($this->sales_end_at) {
            $query->where('purchased_at', '<=', $this->sales_end_at);
        }

        return $query;
    }

    public function totalCurrentRoundTickets(): int
    {
        return $this->currentRoundTickets()->count();
    }

    public function ticketsForCurrentRoundUser(int $userId): int
    {
        return $this->currentRoundTickets()
            ->where('user_id', $userId)
            ->count();
    }

    public function remainingTickets(): ?int
    {
        if ($this->max_tickets === null) {
            return null;
        }

        return max(
            0,
            $this->max_tickets - $this->totalCurrentRoundTickets()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Status Synchronization
    |--------------------------------------------------------------------------
    */

    public function syncStatus(): void
    {
        if (
            $this->isCancelled() ||
            $this->isCompleted() ||
            $this->isDrawing()
        ) {
            return;
        }

        $now = now();

        if (
            $this->sales_start_at &&
            $now->lt($this->sales_start_at)
        ) {
            if (!$this->isScheduled()) {
                $this->update([
                    'status' => LotteryStatus::SCHEDULED,
                ]);
            }

            return;
        }

        if ($now->lt($this->sales_end_at)) {
            if (!$this->isSelling()) {
                $this->update([
                    'status' => LotteryStatus::SELLING,
                ]);
            }

            return;
        }

        if (!$this->isEnded()) {
            $this->update([
                'status' => LotteryStatus::ENDED,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Prize Information
    |--------------------------------------------------------------------------
    */

    /**
     * Every lottery has exactly 5 prize slots.
     */
    public function requiredWinnerCount(): int
    {
        return 5;
    }

    /**
     * Total value of all five prizes.
     */
    public function totalPrizeAmount(): float
    {
        return
            (float) $this->first_prize
            + (float) $this->second_prize
            + (float) $this->third_prize
            + (float) $this->fourth_prize
            + (float) $this->fifth_prize;
    }

    /**
     * Get the five prize slots.
     */
    public function prizes(): array
    {
        return [
            [
                'category' => 'first',
                'position' => 1,
                'amount' => (float) $this->first_prize,
                'winners' => 1,
            ],
            [
                'category' => 'second',
                'position' => 1,
                'amount' => (float) $this->second_prize,
                'winners' => 1,
            ],
            [
                'category' => 'third',
                'position' => 1,
                'amount' => (float) $this->third_prize,
                'winners' => 1,
            ],
            [
                'category' => 'fourth',
                'position' => 1,
                'amount' => (float) $this->fourth_prize,
                'winners' => 1,
            ],
            [
                'category' => 'fifth',
                'position' => 1,
                'amount' => (float) $this->fifth_prize,
                'winners' => 1,
            ],
        ];
    }

    public function hasReachedRequiredTickets(): bool
    {
        return $this->totalTicketsSold() >= $this->requiredWinnerCount();
    }

    public function getTotalPrizePoolAttribute(): float
    {
        return $this->totalPrizeAmount();
    }
}