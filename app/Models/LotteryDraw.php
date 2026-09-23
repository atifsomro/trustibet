<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

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
        'winners_announced',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'total_tickets' => 'integer',
        'total_winners' => 'integer',
        'winners_announced' => 'boolean',

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
        return $this->belongsTo(Lottery::class)->withTrashed();
    }

    public function winners(): HasMany
    {
        return $this->hasMany(LotteryWinner::class, 'draw_id');
    }

    /**
     * Private outcome for one user. Never expose other participants.
     *
     * @return array{outcome: string, wins: Collection<int, LotteryWinner>}
     */
    public function personalResult(?int $userId): array
    {
        if (!$userId) {
            return [
                'outcome' => 'guest',
                'wins' => collect(),
            ];
        }

        $wins = $this->relationLoaded('winners')
            ? $this->winners->where('user_id', $userId)->values()
            : $this->winners()
                ->where('user_id', $userId)
                ->with(['ticket', 'user'])
                ->get();

        if ($wins->isNotEmpty()) {
            return [
                'outcome' => 'won',
                'wins' => $wins,
            ];
        }

        $enteredQuery = LotteryTicket::query()
            ->where('lottery_id', $this->lottery_id)
            ->where('user_id', $userId);

        if ($this->sales_start_at) {
            $enteredQuery->where('purchased_at', '>=', $this->sales_start_at);
        }

        if ($this->sales_end_at) {
            $enteredQuery->where('purchased_at', '<=', $this->sales_end_at);
        }

        return [
            'outcome' => $enteredQuery->exists() ? 'lost' : 'not_entered',
            'wins' => collect(),
        ];
    }

    /**
     * Prize labels/amounts for display, from snapshot when available.
     *
     * @return Collection<string, array{label: string, short_label: string, amount: float}>
     */
    public function prizeCatalog(?Lottery $lottery = null): Collection
    {
        $lottery ??= $this->lottery;

        $prizes = collect($this->prize_snapshot ?? []);

        if ($prizes->isEmpty() && $lottery) {
            $prizes = collect([
                ['category' => 'first', 'amount' => $lottery->first_prize],
                ['category' => 'second', 'amount' => $lottery->second_prize],
                ['category' => 'third', 'amount' => $lottery->third_prize],
                ['category' => 'fourth', 'amount' => $lottery->fourth_prize],
                ['category' => 'fifth', 'amount' => $lottery->fifth_prize],
            ]);
        }

        return $prizes->mapWithKeys(function ($prize) {
            $category = (string) ($prize['category'] ?? 'prize');

            return [
                $category => [
                    'label' => match ($category) {
                        'first' => '1st Prize',
                        'second' => '2nd Prize',
                        'third' => '3rd Prize',
                        'fourth' => '4th Prize',
                        'fifth' => '5th Prize',
                        default => ucfirst($category),
                    },
                    'short_label' => match ($category) {
                        'first' => '1st',
                        'second' => '2nd',
                        'third' => '3rd',
                        'fourth' => '4th',
                        'fifth' => '5th',
                        default => ucfirst($category),
                    },
                    'amount' => (float) ($prize['amount'] ?? 0),
                ],
            ];
        });
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

    /**
     * When this round began. The calendar day of this instant owns the round number.
     */
    public function roundStartedAt(): ?Carbon
    {
        return $this->sales_start_at ?? $this->completed_at ?? $this->created_at;
    }

    /**
     * Calendar day this round belongs to, in the application timezone.
     */
    public function roundDate(): Carbon
    {
        return ($this->roundStartedAt() ?? now())
            ->copy()
            ->timezone(config('app.timezone'))
            ->startOfDay();
    }

    /**
     * Completed draws whose round started on a calendar day.
     */
    public function scopeStartedOnDate(Builder $query, string $date): Builder
    {
        return $query->where(function (Builder $query) use ($date) {
            $query->whereDate('sales_start_at', $date)
                ->orWhere(function (Builder $inner) use ($date) {
                    $inner->whereNull('sales_start_at')
                        ->whereDate('completed_at', $date);
                })
                ->orWhere(function (Builder $inner) use ($date) {
                    $inner->whereNull('sales_start_at')
                        ->whereNull('completed_at')
                        ->whereDate('created_at', $date);
                });
        });
    }

    /**
     * 1-based position of this draw among completed rounds that started the same day.
     */
    public function dailyRoundNumber(): int
    {
        $query = static::query()
            ->where('lottery_id', $this->lottery_id)
            ->where('status', 'completed')
            ->startedOnDate($this->roundDate()->toDateString());

        if ($this->isCompleted()) {
            return (int) $query->where('id', '<=', $this->id)->count();
        }

        return (int) $query->where('id', '<', $this->id)->count() + 1;
    }

    public function dailyRoundLabel(): string
    {
        return sprintf(
            'Round %d · %s',
            $this->dailyRoundNumber(),
            $this->roundDate()->format('d M Y')
        );
    }

    /**
     * Daily round number and date for each draw. Numbers restart at 1 each day.
     *
     * @param  Collection<int, LotteryDraw>  $draws
     * @return Collection<int, array{number: int, date: Carbon}>
     */
    public static function dailyRoundsFor(Collection $draws): Collection
    {
        $counts = [];

        $ordered = $draws->sortBy(function (LotteryDraw $draw) {
            $started = $draw->roundStartedAt();

            return sprintf('%010d-%010d', $started?->timestamp ?? 0, $draw->id);
        })->values();

        return $ordered->mapWithKeys(function (LotteryDraw $draw) use (&$counts) {
            $key = $draw->roundDate()->toDateString();
            $counts[$key] = ($counts[$key] ?? 0) + 1;

            return [
                (int) $draw->id => [
                    'number' => $counts[$key],
                    'date' => $draw->roundDate(),
                ],
            ];
        });
    }
}
