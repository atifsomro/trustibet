<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
}
