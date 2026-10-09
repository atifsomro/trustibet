@php
    $user = auth()->user();
    $search = trim((string) request()->input('q', ''));
    $perPage = 10;
    $page = max(1, (int) request()->input('entries_page', 1));

    $lotteryTickets = \App\Models\LotteryTicket::query()
        ->with(['lottery' => fn ($q) => $q->withTrashed(), 'winner'])
        ->where('user_id', $user->id)
        ->whereNotIn('status', ['cancelled'])
        ->get();

    $gamePlays = \App\Models\GamePlay::query()
        ->with(['game', 'round', 'prize', 'package'])
        ->where('user_id', $user->id)
        ->get();

    $investments = \App\Models\UserInvestment::query()
        ->with('package')
        ->where('user_id', $user->id)
        ->get();

    $entryStats = [
        'total' => $lotteryTickets->count() + $gamePlays->count() + $investments->count(),
        'active' => $lotteryTickets->where('status', 'active')->count()
            + $gamePlays->where('status', \App\Enums\GamePlayStatus::PENDING)->count()
            + $investments->where('status', \App\Enums\InvestmentStatus::ACTIVE)->count(),
        'won' => $lotteryTickets->where('status', 'winner')->count()
            + $gamePlays->where('status', \App\Enums\GamePlayStatus::WON)->count()
            + $investments->where('status', \App\Enums\InvestmentStatus::COMPLETED)->count(),
        'lost' => $lotteryTickets->where('status', 'lost')->count()
            + $gamePlays->where('status', \App\Enums\GamePlayStatus::LOST)->count()
            + $investments->where('status', \App\Enums\InvestmentStatus::CANCELLED)->count(),
    ];

    $lotteryEntries = $lotteryTickets->map(function ($ticket) {
        $lottery = $ticket->lottery;
        $currency = $lottery?->currency ?? '';
        $drawAt = $lottery?->draw_at ?? $lottery?->periodEnd();
        $prizeLabel = $ticket->winner
            ? trim($currency . ' ' . number_format((float) $ticket->winner->prize_amount, 2))
            : trim($currency . ' ' . number_format((float) ($lottery?->first_prize ?? 0), 2));

        $statusMap = [
            'active' => 'active',
            'winner' => 'won',
            'lost' => 'lost',
            'refunded' => 'refunded',
        ];

        return (object) [
            'source' => 'lottery',
            'game' => $lottery?->title ?? 'Lottery',
            'prize' => $prizeLabel !== '' ? $prizeLabel : '—',
            'ticket_id' => $ticket->ticket_number,
            'fee' => trim($currency . ' ' . number_format((float) $ticket->price, 2)),
            'draw_at' => $drawAt,
            'status' => $statusMap[$ticket->status] ?? $ticket->status,
            'view_url' => $lottery ? route('lotteries.show', $lottery) : null,
            'sort_at' => $ticket->purchased_at ?? $ticket->created_at,
        ];
    });

    $gameEntries = $gamePlays->map(function ($play) {
        $game = $play->game;
        $currency = (string) ($game?->configValue('currency') ?: '$');
        $drawAt = $play->round?->ends_at
            ?? $play->round?->settled_at
            ?? $play->created_at;

        if ($play->status === \App\Enums\GamePlayStatus::WON) {
            $prize = data_get($play->outcome, 'label')
                ?: trim($currency . ' ' . number_format((float) $play->prize_amount, 2));
        } else {
            $prize = $play->prize?->label
                ?: (string) ($game?->configValue('prize_name') ?: '')
                ?: (data_get($play->outcome, 'label') ?: '—');
        }

        $status = match ($play->status) {
            \App\Enums\GamePlayStatus::PENDING => 'active',
            \App\Enums\GamePlayStatus::WON => 'won',
            \App\Enums\GamePlayStatus::LOST => 'lost',
            default => $play->status?->value ?? 'lost',
        };

        $viewUrl = null;
        if ($game) {
            $viewUrl = $game->type === \App\Enums\GameType::LIMITED_DRAW
                ? route('participate')
                : route('game.show', $game->slug);
        }

        return (object) [
            'source' => 'game',
            'game' => $game?->title ?? 'Game',
            'prize' => $prize,
            'ticket_id' => $play->ticket_number,
            'fee' => trim($currency . ' ' . number_format((float) $play->fee_amount, 2)),
            'draw_at' => $drawAt,
            'status' => $status,
            'view_url' => $viewUrl,
            'sort_at' => $play->created_at,
        ];
    });

    $investmentEntries = $investments->map(function ($investment) {
        $totalRoi = round((float) $investment->daily_roi * (int) $investment->total_days, 2);
        $status = match ($investment->status) {
            \App\Enums\InvestmentStatus::ACTIVE => 'active',
            \App\Enums\InvestmentStatus::COMPLETED => 'completed',
            \App\Enums\InvestmentStatus::CANCELLED => 'cancelled',
            default => $investment->status?->value ?? 'active',
        };

        return (object) [
            'source' => 'investment',
            'game' => $investment->package_name ?: 'Investment',
            'prize' => '$' . number_format($totalRoi, 2) . ' ROI',
            'ticket_id' => app(\App\Services\Wallet\TransactionReferenceGenerator::class)
                ->forEntity('INV', (int) $investment->id, $investment->created_at ?? $investment->starts_at),
            'fee' => '$' . number_format((float) $investment->price, 2),
            'draw_at' => $investment->ends_at,
            'status' => $status,
            'view_url' => route('investments.show', $investment),
            'sort_at' => $investment->created_at ?? $investment->starts_at,
        ];
    });

    $entries = $lotteryEntries
        ->concat($gameEntries)
        ->concat($investmentEntries)
        ->sortByDesc(fn ($entry) => optional($entry->sort_at)->getTimestamp() ?? 0)
        ->values();

    if ($search !== '') {
        $needle = mb_strtolower($search);
        $entries = $entries
            ->filter(fn ($entry) => str_contains(mb_strtolower((string) $entry->ticket_id), $needle)
                || str_contains(mb_strtolower((string) $entry->game), $needle))
            ->values();
    }

    $tickets = new \Illuminate\Pagination\LengthAwarePaginator(
        $entries->forPage($page, $perPage)->values(),
        $entries->count(),
        $perPage,
        $page,
        [
            'path' => request()->url(),
            'pageName' => 'entries_page',
            'query' => request()->query(),
        ]
    );
    $tickets->fragment('entries');

    $activeLottery = $lotteryTickets
        ->filter(function ($ticket) {
            if ($ticket->status !== 'active' || ! $ticket->lottery) {
                return false;
            }

            $endsAt = $ticket->lottery->periodEnd() ?? $ticket->lottery->draw_at;

            return $endsAt instanceof \Illuminate\Support\Carbon && $endsAt->isFuture();
        })
        ->sortBy(function ($ticket) {
            $endsAt = $ticket->lottery->periodEnd() ?? $ticket->lottery->draw_at;

            return $endsAt?->getTimestamp() ?? PHP_INT_MAX;
        })
        ->first();

    $activePlay = $gamePlays
        ->filter(function ($play) {
            return $play->status === \App\Enums\GamePlayStatus::PENDING
                && $play->round?->ends_at instanceof \Illuminate\Support\Carbon
                && $play->round->ends_at->isFuture();
        })
        ->sortBy(fn ($play) => $play->round->ends_at->getTimestamp())
        ->first();

    $activeInvestment = $investments
        ->filter(function ($investment) {
            return $investment->status === \App\Enums\InvestmentStatus::ACTIVE
                && $investment->ends_at
                && $investment->ends_at->copy()->endOfDay()->isFuture();
        })
        ->sortBy(fn ($investment) => $investment->ends_at->getTimestamp())
        ->first();

    if ($activeLottery) {
        $activeEntryTitle = $activeLottery->lottery->title;
        $activeEntryTicket = $activeLottery->ticket_number;
        $activeEndsAt = $activeLottery->lottery->periodEnd() ?? $activeLottery->lottery->draw_at;
    } elseif ($activePlay) {
        $activeEntryTitle = $activePlay->game?->configValue('prize_name')
            ?: ($activePlay->game?->title ?? 'Active Draw');
        $activeEntryTicket = $activePlay->ticket_number;
        $activeEndsAt = $activePlay->round->ends_at;
    } elseif ($activeInvestment) {
        $activeEntryTitle = $activeInvestment->package_name ?: 'Investment';
        $activeEntryTicket = app(\App\Services\Wallet\TransactionReferenceGenerator::class)
            ->forEntity('INV', (int) $activeInvestment->id, $activeInvestment->created_at ?? $activeInvestment->starts_at);
        $activeEndsAt = $activeInvestment->ends_at->copy()->endOfDay();
    } else {
        $activeEntryTitle = null;
        $activeEntryTicket = null;
        $activeEndsAt = null;
    }

    $statusStyles = [
        'active' => 'bg-yellow-500/20 text-yellow-500',
        'won' => 'bg-green-500/20 text-green-500',
        'completed' => 'bg-green-500/20 text-green-500',
        'lost' => 'bg-red-500/20 text-red-500',
        'cancelled' => 'bg-red-500/20 text-red-400',
        'refunded' => 'bg-orange-500/20 text-orange-400',
    ];

    $statusLabels = [
        'active' => 'Active',
        'won' => 'Won',
        'completed' => 'Completed',
        'lost' => 'Lost',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
    ];
@endphp

<div class="entries" data-entries-root>
    <a href="{{ route('lotteries.history') }}"
        class="mb-6 inline-flex items-center gap-2 text-sm text-green-400 hover:underline">
        <i class="fa-solid fa-clock-rotate-left"></i>
        View lottery purchase history
    </a>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
            <div class="flex flex-col items-center justify-between text-center">
                <i class="fa-solid fa-ticket text-4xl text-brand-primary"></i>
                <h3 class="mt-2">{{ $entryStats['total'] }}</h3>
                <p class="opacity-70">Total Entries</p>
            </div>
        </div>
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
            <div class="flex flex-col items-center justify-between text-center">
                <i class="fa-solid fa-spinner text-4xl text-yellow-500"></i>
                <h3 class="mt-2">{{ $entryStats['active'] }}</h3>
                <p class="opacity-70">Active Entries</p>
            </div>
        </div>
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
            <div class="flex flex-col items-center justify-between text-center">
                <i class="fa-solid fa-trophy text-4xl text-green-500"></i>
                <h3 class="mt-2">{{ $entryStats['won'] }}</h3>
                <p class="opacity-70">Won Games</p>
            </div>
        </div>
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
            <div class="flex flex-col items-center justify-between text-center">
                <i class="fa-solid fa-circle-xmark text-4xl text-red-500"></i>
                <h3 class="mt-2">{{ $entryStats['lost'] }}</h3>
                <p class="opacity-70">Lost Games</p>
            </div>
        </div>
    </div>

    {{-- Entries Table --}}
    <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-8">
            <div>
                <h3>My Entries</h3>
                <p class="mt-2 opacity-70">View all your participated games.</p>
            </div>
            <form method="GET" action="{{ route('user-account') }}#entries" class="flex gap-3">
                <input type="text" name="q" value="{{ $search }}" placeholder="Search Ticket ID..."
                    class="rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                <button type="submit" class="btn-primary text-sm px-4 py-3">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-[12px] md:text-base lg:text-xl">
                <thead>
                    <tr class="border-b border-brand-border">
                        <th class="py-2 sm:py-4 text-left">Game</th>
                        <th class="py-2 sm:py-4 text-left">Prize</th>
                        <th class="py-2 sm:py-4 text-left">Ticket ID</th>
                        <th class="py-2 sm:py-4 text-left">Entry Fee</th>
                        <th class="py-2 sm:py-4 text-left">Draw Date</th>
                        <th class="py-2 sm:py-4 text-left">Status</th>
                        <th class="py-2 sm:py-4 text-left">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tickets as $entry)
                        @php
                            $statusClass = $statusStyles[$entry->status] ?? 'bg-brand-dark text-white/70';
                            $statusLabel = $statusLabels[$entry->status] ?? ucfirst((string) $entry->status);
                        @endphp
                        <tr class="border-b border-brand-border last:border-0">
                            <td class="py-3 sm:py-5">{{ $entry->game }}</td>
                            <td class="py-3 sm:py-5">{{ $entry->prize }}</td>
                            <td class="py-3 sm:py-5">
                                <code class="font-mono text-sm tracking-wide">{{ $entry->ticket_id }}</code>
                            </td>
                            <td class="py-3 sm:py-5">{{ $entry->fee }}</td>
                            <td class="py-3 sm:py-5">
                                {{ $entry->draw_at ? $entry->draw_at->timezone(config('app.timezone'))->format('d M Y') : '—' }}
                            </td>
                            <td class="py-3 sm:py-5">
                                <span class="rounded-full {{ $statusClass }} px-3 py-1 text-sm">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="py-3 sm:py-5">
                                @if ($entry->view_url)
                                    <a href="{{ $entry->view_url }}"
                                        class="btn-primary inline-block text-[12px] sm:text-sm px-3 sm:px-5 py-2">
                                        View
                                    </a>
                                @else
                                    <span
                                        class="btn-primary inline-block text-[12px] sm:text-sm px-3 sm:px-5 py-2 opacity-50 pointer-events-none">
                                        View
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center opacity-70">
                                @if ($search !== '')
                                    No entries match your search.
                                @else
                                    You have not entered any games yet.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tickets->total() > 0)
            <div class="mt-8 border-t border-brand-border pt-6">
                {{ $tickets->links('vendor.pagination.brand') }}
            </div>
        @endif
    </div>

    {{-- Active Draw --}}
    @if ($activeEntryTitle && $activeEndsAt)
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-8">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <small class="uppercase tracking-[3px] text-brand-primary">
                        Current Active Entry
                    </small>
                    <h3 class="mt-3">
                        {{ $activeEntryTitle }}
                    </h3>
                    <p class="mt-2 opacity-70">
                        Your ticket <strong>#{{ $activeEntryTicket }}</strong> is successfully entered in this
                        draw.
                    </p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center" data-entry-countdown
                    data-end="{{ $activeEndsAt->toIso8601String() }}">
                    <div class="rounded-2xl bg-brand-dark p-4">
                        <h3 data-cd-days>00</h3>
                        <small>Days</small>
                    </div>
                    <div class="rounded-2xl bg-brand-dark p-4">
                        <h3 data-cd-hours>00</h3>
                        <small>Hours</small>
                    </div>
                    <div class="rounded-2xl bg-brand-dark p-4">
                        <h3 data-cd-minutes>00</h3>
                        <small>Minutes</small>
                    </div>
                    <div class="rounded-2xl bg-brand-dark p-4">
                        <h3 data-cd-seconds>00</h3>
                        <small>Seconds</small>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    (function () {
        const root = document.querySelector('[data-entries-root]');
        if (!root || root.dataset.bound === '1') {
            return;
        }
        root.dataset.bound = '1';

        const countdown = root.querySelector('[data-entry-countdown]');
        if (!countdown || !countdown.dataset.end) {
            return;
        }

        const endMs = new Date(countdown.dataset.end).getTime();
        const daysEl = countdown.querySelector('[data-cd-days]');
        const hoursEl = countdown.querySelector('[data-cd-hours]');
        const minutesEl = countdown.querySelector('[data-cd-minutes]');
        const secondsEl = countdown.querySelector('[data-cd-seconds]');

        function pad(value) {
            return String(Math.max(0, value)).padStart(2, '0');
        }

        function tick() {
            const diff = Math.max(0, endMs - Date.now());
            const totalSeconds = Math.floor(diff / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            if (daysEl) daysEl.textContent = pad(days);
            if (hoursEl) hoursEl.textContent = pad(hours);
            if (minutesEl) minutesEl.textContent = pad(minutes);
            if (secondsEl) secondsEl.textContent = pad(seconds);
        }

        tick();
        setInterval(tick, 1000);
    })();
</script>
