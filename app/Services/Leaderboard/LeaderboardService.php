<?php

declare(strict_types=1);

namespace App\Services\Leaderboard;

use App\Enums\WalletTransactionType;
use App\Models\Country;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Daily leaderboard built from the wallet ledger.
 *
 * - Only GAME_WIN, BONUS_GRANTED and LOTTERY_PRIZE transactions count.
 * - Only transactions created today (app timezone) are summed.
 * - The cache key contains the current date, so the board resets
 *   automatically at midnight without any scheduled job.
 */
class LeaderboardService
{
    public const LIMIT = 20;

    /** Short TTL keeps the board near real-time while the page stays instant. */
    private const TTL_SECONDS = 30;

    /**
     * @return list<WalletTransactionType>
     */
    public static function countedTypes(): array
    {
        return [
            WalletTransactionType::GAME_WIN,
            WalletTransactionType::BONUS_GRANTED,
            WalletTransactionType::LOTTERY_PRIZE,
        ];
    }

    /**
     * Full board payload for today: rows + summary stats.
     *
     * @return array{date:string, resets_at:string, rows:list<array>, stats:array}
     */
    public function today(): array
    {
        $now = Carbon::now();
        $key = 'leaderboard:' . $now->toDateString();

        return Cache::remember($key, self::TTL_SECONDS, fn () => $this->build($now));
    }

    /**
     * Single row by rank (1-based) from today's board, or null.
     */
    public function rowByRank(int $rank): ?array
    {
        return $this->today()['rows'][$rank - 1] ?? null;
    }

    /**
     * Detailed popup payload for a user already on today's board.
     */
    public function detailsByRank(int $rank): ?array
    {
        $row = $this->rowByRank($rank);

        if ($row === null) {
            return null;
        }

        $cacheKey = 'leaderboard:detail:' . Carbon::now()->toDateString() . ':' . $row['user_id'];

        return Cache::remember($cacheKey, self::TTL_SECONDS, function () use ($row) {
            $user = User::query()->find($row['user_id']);

            if (! $user) {
                return null;
            }

            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();

            $p = DB::getTablePrefix();

            $breakdown = WalletTransaction::query()
                ->join('wallets', 'wallets.id', '=', 'wallet_transactions.wallet_id')
                ->where('wallets.user_id', $user->id)
                ->whereIn('wallet_transactions.type', array_column(self::countedTypes(), 'value'))
                ->whereBetween('wallet_transactions.created_at', [$start, $end])
                ->selectRaw("{$p}wallet_transactions.type as type, SUM({$p}wallet_transactions.amount) as total, COUNT(*) as hits, MAX({$p}wallet_transactions.created_at) as last_at")
                ->groupBy('wallet_transactions.type')
                ->get()
                ->keyBy(fn ($r) => (string) $r->type);

            $parts = [];
            foreach (self::countedTypes() as $type) {
                $line = $breakdown->get($type->value);
                $parts[] = [
                    'label' => $type->label(),
                    'total' => $this->money((float) ($line->total ?? 0)),
                    'hits' => (int) ($line->hits ?? 0),
                ];
            }

            $lastAt = $breakdown->max('last_at');
            $country = $user->country_id ? Country::query()->find($user->country_id) : null;

            return [
                'rank' => $row['rank'],
                'name' => $row['name'],
                'avatar' => $row['avatar'],
                'country' => $country?->name ?: 'Pakistan',
                'total' => $row['total_formatted'],
                'transactions' => $row['hits'],
                'breakdown' => $parts,
                'last_win' => $lastAt ? Carbon::parse($lastAt)->format('h:i A') : null,
                'member_since' => $user->created_at?->format('M Y'),
            ];
        });
    }

    private function build(Carbon $now): array
    {
        $start = $now->copy()->startOfDay();
        $end = $now->copy()->endOfDay();
        $types = array_column(self::countedTypes(), 'value');

        // selectRaw/havingRaw bypass the query grammar, so table prefixes must be applied manually.
        $p = DB::getTablePrefix();

        $base = fn () => WalletTransaction::query()
            ->join('wallets', 'wallets.id', '=', 'wallet_transactions.wallet_id')
            ->whereIn('wallet_transactions.type', $types)
            ->whereBetween('wallet_transactions.created_at', [$start, $end]);

        // Top N users by summed amount - one grouped query, uses type/created_at indexes.
        $top = $base()
            ->selectRaw("{$p}wallets.user_id as user_id, SUM({$p}wallet_transactions.amount) as total, COUNT(*) as hits, MAX({$p}wallet_transactions.created_at) as last_at")
            ->groupBy('wallets.user_id')
            ->havingRaw("SUM({$p}wallet_transactions.amount) > 0")
            ->orderByDesc('total')
            ->orderBy('user_id')
            ->limit(self::LIMIT)
            ->get();

        $users = User::query()
            ->whereIn('id', $top->pluck('user_id'))
            ->get(['id', 'name', 'username', 'avatar'])
            ->keyBy('id');

        $rows = [];
        foreach ($top->values() as $i => $line) {
            $user = $users->get($line->user_id);
            if (! $user) {
                continue;
            }

            $rows[] = [
                'rank' => count($rows) + 1,
                'user_id' => (int) $user->id,
                'name' => $this->mask($user->username ?: $user->name),
                'avatar' => $user->avatar ?: asset('images/profile/avatar.png'),
                'total' => (float) $line->total,
                'total_formatted' => $this->money((float) $line->total),
                'hits' => (int) $line->hits,
                'last_at' => Carbon::parse($line->last_at)->format('h:i A'),
            ];
        }

        $summary = $base()
            ->selectRaw("COUNT(DISTINCT {$p}wallets.user_id) as players, COALESCE(SUM({$p}wallet_transactions.amount), 0) as total")
            ->first();

        return [
            'date' => $now->format('d M Y'),
            'resets_at' => $now->copy()->addDay()->startOfDay()->toIso8601String(),
            'rows' => $rows,
            'stats' => [
                'players' => (int) ($summary->players ?? 0),
                'total' => $this->money((float) ($summary->total ?? 0)),
                'last_win' => $rows ? collect($rows)->pluck('last_at')->first() : null,
            ],
        ];
    }

    private function mask(string $value): string
    {
        $value = trim($value);
        $length = Str::length($value);

        if ($length <= 3) {
            return $value . '***';
        }

        return Str::substr($value, 0, 3) . '***' . ($length > 6 ? Str::substr($value, -3) : '');
    }

    private function money(float $amount): string
    {
        return '$' . number_format($amount, 2);
    }
}
