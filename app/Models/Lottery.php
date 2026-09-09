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
        'duration_seconds',
        'starts_at',
        'ends_at',

        'first_prize',
        'second_prize',
        'third_prize',
        'fourth_prize',
        'fifth_prize',

        'max_tickets',
        'max_tickets_per_user',
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
        'duration_seconds' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',

        'first_prize' => 'decimal:2',
        'second_prize' => 'decimal:2',
        'third_prize' => 'decimal:2',
        'fourth_prize' => 'decimal:2',
        'fifth_prize' => 'decimal:2',

        'max_tickets' => 'integer',
        'max_tickets_per_user' => 'integer',
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

    public function latestAnnouncedDraw(): HasOne
    {
        return $this->hasOne(LotteryDraw::class)
            ->where('status', 'completed')
            ->where('winners_announced', true)
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
            ->whereNotIn('status', [
                LotteryStatus::DRAFT->value,
                LotteryStatus::CANCELLED->value,
                LotteryStatus::COMPLETED->value,
                LotteryStatus::DRAWING->value,
                LotteryStatus::ENDED->value,
            ])
            ->where(function ($query) {
                $query
                    ->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query
                    ->where(function ($inner) {
                        $inner->whereNotNull('ends_at')
                            ->where('ends_at', '>', now());
                    })
                    ->orWhere(function ($inner) {
                        $inner->whereNull('ends_at')
                            ->where('sales_end_at', '>', now());
                    });
            });
    }

    public function scopeDueForDraw(Builder $query): Builder
    {
        return $query
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->whereNotIn('status', [
                LotteryStatus::DRAFT->value,
                LotteryStatus::CANCELLED->value,
                LotteryStatus::DRAWING->value,
                LotteryStatus::COMPLETED->value,
            ]);
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
        if ($this->isCancelled() || $this->isDraft() || $this->isCompleted() || $this->isDrawing()) {
            return false;
        }

        if ($this->periodStart() && now()->lt($this->periodStart())) {
            return false;
        }

        if ($this->hasEnded()) {
            return false;
        }

        return true;
    }

    public function hasEnded(): bool
    {
        $endsAt = $this->periodEnd();

        if ($endsAt === null) {
            return false;
        }

        return now()->gte($endsAt);
    }

    public function canDraw(): bool
    {
        if (!$this->hasEnded() || $this->isCancelled() || $this->isDrawing() || $this->isDraft()) {
            return false;
        }

        return !$this->currentRoundHasDraw();
    }

    public function currentRoundHasDraw(): bool
    {
        $query = $this->draws()
            ->whereIn('status', ['pending', 'running', 'completed']);

        $periodStart = $this->periodStart();
        $periodEnd = $this->periodEnd();

        if ($periodStart) {
            $query->where('sales_start_at', $periodStart);
        } else {
            $query->whereNull('sales_start_at');
        }

        if ($periodEnd) {
            $query->where('sales_end_at', $periodEnd);
        }

        return $query->exists();
    }

    public function periodStart(): mixed
    {
        return $this->starts_at ?? $this->sales_start_at;
    }

    public function periodEnd(): mixed
    {
        return $this->ends_at ?? $this->sales_end_at;
    }

    /**
     * Apply the countdown window from now (or a given start).
     * Also keeps the legacy sales_* columns in sync so existing
     * round-scoped ticket/draw queries keep working.
     */
    public function applyCountdown(?\DateTimeInterface $from = null): void
    {
        $from = $from
            ? \Illuminate\Support\Carbon::parse($from)
            : now();

        $duration = max(1, (int) $this->duration_seconds);

        // DATETIME columns store whole seconds. Snap to the second boundary
        // so a 7s timer is stored as exactly 7s, not ~6.x after truncation.
        $from = $from->copy()->startOfSecond();

        $this->starts_at = $from;
        $this->ends_at = $from->copy()->addSeconds($duration);
        $this->sales_start_at = $this->starts_at;
        $this->sales_end_at = $this->ends_at;
        $this->draw_at = $this->ends_at;
    }

    public function recalculateEndsAt(): void
    {
        if (!$this->starts_at || !$this->duration_seconds) {
            return;
        }

        $this->ends_at = $this->starts_at->copy()->addSeconds((int) $this->duration_seconds);
        $this->sales_start_at = $this->starts_at;
        $this->sales_end_at = $this->ends_at;
        $this->draw_at = $this->ends_at;
    }

    /**
     * @return array{hours: int, minutes: int, seconds: int}
     */
    public function durationParts(): array
    {
        $total = max(0, (int) ($this->duration_seconds ?? 0));

        return [
            'hours' => intdiv($total, 3600),
            'minutes' => intdiv($total % 3600, 60),
            'seconds' => $total % 60,
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status instanceof LotteryStatus
            ? $this->status->label()
            : ucfirst((string) $this->status);
    }

    public function getStatusBadgeAttribute(): string
    {
        $status = $this->status instanceof LotteryStatus
            ? $this->status
            : LotteryStatus::tryFrom((string) $this->status);

        return match ($status) {
            LotteryStatus::SELLING => 'success',
            LotteryStatus::COMPLETED => 'primary',
            LotteryStatus::ENDED => 'warning',
            LotteryStatus::CANCELLED => 'danger',
            LotteryStatus::DRAWING => 'info',
            LotteryStatus::SCHEDULED => 'secondary',
            default => 'secondary',
        };
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

    /**
     * Begin the next sales window using the configured duration.
     */
    public function startNextRound(?\DateTimeInterface $from = null): void
    {
        $this->applyCountdown($from);
        $this->status = LotteryStatus::SELLING;
        $this->save();
    }

    public function currentRoundNumber(): int
    {
        $completed = array_key_exists('completed_draws_count', $this->attributes)
            ? (int) $this->completed_draws_count
            : $this->draws()->where('status', 'completed')->count();

        return $completed + 1;
    }

    public function hasPreviousRounds(): bool
    {
        return $this->currentRoundNumber() > 1;
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
            ->where('status', 'active');

        $periodStart = $this->periodStart();
        $periodEnd = $this->periodEnd();

        if ($periodStart) {
            $query->where('purchased_at', '>=', $periodStart);
        }

        if ($periodEnd) {
            $query->where('purchased_at', '<=', $periodEnd);
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

    /**
     * Remaining tickets this user may buy in the current round.
     * null means unlimited (aside from the lottery-wide cap).
     */
    public function remainingTicketsForUser(?int $userId): ?int
    {
        $lotteryRemaining = $this->remainingTickets();

        $userRemaining = null;

        if ($this->max_tickets_per_user !== null) {
            $owned = $userId
                ? $this->ticketsForCurrentRoundUser($userId)
                : 0;

            $userRemaining = max(0, (int) $this->max_tickets_per_user - $owned);
        }

        if ($lotteryRemaining === null && $userRemaining === null) {
            return null;
        }

        if ($lotteryRemaining === null) {
            return $userRemaining;
        }

        if ($userRemaining === null) {
            return $lotteryRemaining;
        }

        return min($lotteryRemaining, $userRemaining);
    }

    public function maxPurchaseQuantityForUser(?int $userId): int
    {
        $remaining = $this->remainingTicketsForUser($userId);

        if ($remaining === null) {
            return 99;
        }

        return max(0, $remaining);
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
        $startsAt = $this->periodStart();
        $endsAt = $this->periodEnd();

        if ($startsAt && $now->lt($startsAt)) {
            if (!$this->isScheduled()) {
                $this->update([
                    'status' => LotteryStatus::SCHEDULED,
                ]);
            }

            return;
        }

        if ($endsAt && $now->lt($endsAt)) {
            if (!$this->isSelling()) {
                $this->update([
                    'status' => LotteryStatus::SELLING,
                ]);
            }

            return;
        }

        if (!$endsAt) {
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