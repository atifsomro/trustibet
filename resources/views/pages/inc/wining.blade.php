@php
    $user = auth()->user();
    $perPage = 10;
    $page = max(1, (int) request()->input('winnings_page', 1));

    $lotteryWins = \App\Models\LotteryWinner::query()
        ->with([
            'lottery' => fn ($q) => $q->withTrashed(),
            'ticket',
            'draw',
        ])
        ->where('user_id', $user->id)
        ->where('payout_status', '!=', 'suppressed')
        ->latest('id')
        ->get();

    $gameWins = \App\Models\GamePlay::query()
        ->with(['game', 'prize', 'round', 'package'])
        ->where('user_id', $user->id)
        ->where('status', \App\Enums\GamePlayStatus::WON)
        ->latest('id')
        ->get();

    $statusStyles = [
        'credited' => 'bg-brand-primary/20 text-brand-primary',
        'delivered' => 'bg-green-500/20 text-green-500',
        'processing' => 'bg-yellow-500/20 text-yellow-500',
        'failed' => 'bg-red-500/20 text-red-500',
    ];

    $statusLabels = [
        'credited' => 'Credited',
        'delivered' => 'Delivered',
        'processing' => 'Processing',
        'failed' => 'Failed',
    ];

    $lotteryRows = $lotteryWins->map(function ($win) use ($statusLabels) {
        $lottery = $win->lottery;
        $currency = $lottery?->currency ?? '$';
        $catalog = $win->draw?->prizeCatalog($lottery) ?? collect();
        $prizeMeta = $catalog->get($win->prize_category, []);
        $label = $prizeMeta['label']
            ?? (ucfirst((string) $win->prize_category) . ' Prize');
        $amount = (float) ($prizeMeta['amount'] ?? $win->prize_amount);
        $wonAt = $win->paid_at
            ?? $win->draw?->completed_at
            ?? $win->created_at;

        $uiStatus = match ($win->payout_status) {
            'paid' => 'credited',
            'pending' => 'processing',
            'failed' => 'failed',
            default => 'processing',
        };

        $fee = (float) ($win->ticket?->price ?? $lottery?->ticket_price ?? 0);

        return (object) [
            'id' => 'lottery-' . $win->id,
            'source' => 'lottery',
            'prize' => $label,
            'game' => $lottery?->title ?? 'Lottery',
            'won_at' => $wonAt,
            'won_at_label' => $wonAt
                ? $wonAt->timezone(config('app.timezone'))->format('d M Y, h:i A')
                : '—',
            'amount' => $amount,
            'amount_label' => trim($currency . ' ' . number_format($amount, 2)),
            'fee_label' => trim($currency . ' ' . number_format($fee, 2)),
            'status' => $uiStatus,
            'status_label' => $statusLabels[$uiStatus] ?? ucfirst($uiStatus),
            'ticket_id' => $win->ticket?->ticket_number ?? '—',
            'image' => asset('images/draw/prize.png'),
            'note' => match ($uiStatus) {
                'credited' => 'Your lottery prize has been credited to your wallet.',
                'processing' => 'Your win is confirmed. Prize verification is in progress.',
                'failed' => 'There was an issue paying this prize. Please contact support.',
                default => 'Your lottery win details are shown below.',
            },
            'sort_at' => $wonAt ?? $win->created_at,
        ];
    });

    $gameRows = $gameWins->map(function ($play) use ($statusLabels) {
        $game = $play->game;
        $currency = (string) ($game?->configValue('currency') ?: '$');
        $amount = (float) $play->prize_amount;
        $label = data_get($play->outcome, 'label')
            ?: $play->prize?->label
            ?: (string) ($game?->configValue('prize_name') ?: 'Prize');
        $wonAt = $play->round?->settled_at ?? $play->updated_at ?? $play->created_at;
        $isLimitedDraw = $game?->type === \App\Enums\GameType::LIMITED_DRAW;
        $uiStatus = ($play->win_transaction_id || $amount > 0)
            ? 'credited'
            : ($isLimitedDraw ? 'processing' : 'credited');

        $image = $game?->imageUrl() ?: asset('images/draw/prize.png');

        return (object) [
            'id' => 'game-' . $play->id,
            'source' => 'game',
            'prize' => $label,
            'game' => $game?->title ?? 'Game',
            'won_at' => $wonAt,
            'won_at_label' => $wonAt
                ? $wonAt->timezone(config('app.timezone'))->format('d M Y, h:i A')
                : '—',
            'amount' => $amount,
            'amount_label' => $amount > 0
                ? trim($currency . ' ' . number_format($amount, 2))
                : $label,
            'fee_label' => trim($currency . ' ' . number_format((float) $play->fee_amount, 2)),
            'status' => $uiStatus,
            'status_label' => $statusLabels[$uiStatus] ?? ucfirst($uiStatus),
            'ticket_id' => $play->ticket_number,
            'image' => $image,
            'note' => match ($uiStatus) {
                'credited' => 'Your prize has been credited to your wallet.',
                'processing' => 'Your win is confirmed. Prize verification is in progress.',
                default => 'Your game win details are shown below.',
            },
            'sort_at' => $wonAt ?? $play->created_at,
        ];
    });

    $allWins = $lotteryRows
        ->concat($gameRows)
        ->sortByDesc(fn ($row) => optional($row->sort_at)->getTimestamp() ?? 0)
        ->values();

    $totalWinnings = (float) $allWins->sum('amount');
    $pendingClaim = (float) $allWins->where('status', 'processing')->sum('amount');
    $rewardsWon = $allWins->count();

    $biggest = $allWins->sortByDesc('amount')->first();
    $biggestLabel = $biggest
        ? ($biggest->amount > 0 ? $biggest->amount_label : $biggest->prize)
        : '—';

    $latestWin = $allWins->first();

    $history = new \Illuminate\Pagination\LengthAwarePaginator(
        $allWins->forPage($page, $perPage)->values(),
        $allWins->count(),
        $perPage,
        $page,
        [
            'path' => request()->url(),
            'pageName' => 'winnings_page',
            'query' => request()->query(),
        ]
    );
    $history->fragment('winnings');

    $progressStep = 1;
    if ($latestWin) {
        $progressStep = match ($latestWin->status) {
            'credited', 'delivered' => 4,
            'processing' => 3,
            'failed' => 1,
            default => 2,
        };
    }

    $winsPayload = $allWins->mapWithKeys(fn ($win) => [
        $win->id => [
            'prize' => $win->prize,
            'game' => $win->game,
            'ticket_id' => $win->ticket_id,
            'amount_label' => $win->amount_label,
            'fee_label' => $win->fee_label,
            'won_at_label' => $win->won_at_label,
            'status' => $win->status,
            'status_label' => $win->status_label,
            'image' => $win->image,
            'note' => $win->note,
            'source' => $win->source,
        ],
    ]);
@endphp

<section class="winnings" data-winnings-root>
    <div class="container">
        {{-- Stats --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-sack-dollar text-4xl text-green-500"></i>
                    <h3 class="mt-2">${{ number_format($totalWinnings, 2) }}</h3>
                    <p class="opacity-70">Total Winnings</p>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-hourglass-half text-4xl text-yellow-500"></i>
                    <h3 class="mt-2">${{ number_format($pendingClaim, 2) }}</h3>
                    <p class="opacity-70">Pending Claim</p>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-gift text-4xl text-brand-primary"></i>
                    <h3 class="mt-2">{{ $rewardsWon }}</h3>
                    <p class="opacity-70">Rewards Won</p>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-trophy text-4xl text-orange-500"></i>
                    <h3 class="mt-2">{{ $biggestLabel }}</h3>
                    <p class="opacity-70">Biggest Prize</p>
                </div>
            </div>
        </div>

        {{-- Latest Winning --}}
        @if ($latestWin)
            <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
                <div class="grid items-center gap-8 lg:grid-cols-2">
                    <div>
                        <small class="uppercase tracking-[3px] text-brand-primary">
                            Latest Winning
                        </small>
                        <h3 class="mt-4">
                            🎉 Congratulations!
                        </h3>
                        <h4 class="mt-4">
                            You Won {{ $latestWin->prize }}
                        </h4>
                        <p class="mt-4 opacity-70">
                            Your ticket <strong>{{ $latestWin->ticket_id ?? '—' }}</strong> has been selected as the
                            winning ticket.
                            @if ($latestWin->status === 'processing')
                                Our team will contact you shortly for prize verification.
                            @elseif ($latestWin->status === 'credited')
                                Your prize has been credited to your wallet.
                            @endif
                        </p>
                        <button type="button"
                            class="btn-primary mt-8"
                            data-win-detail="{{ $latestWin->id }}">
                            View Prize Details
                        </button>
                    </div>
                    <div class="text-center">
                        <img src="{{ $latestWin->image }}" alt="{{ $latestWin->game }}"
                            class="mx-auto w-full sm:max-w-sm rounded-2xl object-cover aspect-square">
                    </div>
                </div>
            </div>

            {{-- Prize Progress --}}
            <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <i class="fa-solid fa-truck-fast text-2xl text-brand-primary"></i>
                    <div>
                        <h3>Prize Delivery Progress</h3>
                        <p class="opacity-70">Track your latest winning.</p>
                    </div>
                </div>

                @php
                    $progressPercent = match ($progressStep) {
                        1 => 'w-1/4',
                        2 => 'w-2/4',
                        3 => 'w-3/4',
                        default => 'w-full',
                    };
                @endphp

                <div class="w-full h-3 rounded-full bg-brand-dark overflow-hidden">
                    <div class="h-full {{ $progressPercent }} rounded-full bg-brand-primary"></div>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-4">
                    <div class="text-center">
                        <i
                            class="fa-solid fa-circle-check text-3xl {{ $progressStep >= 1 ? 'text-green-500' : 'opacity-40' }}"></i>
                        <p class="mt-3 {{ $progressStep >= 1 ? '' : 'opacity-50' }}">Winner Confirmed</p>
                    </div>

                    <div class="text-center">
                        <i
                            class="fa-solid fa-circle-check text-3xl {{ $progressStep >= 2 ? 'text-green-500' : 'opacity-40' }}"></i>
                        <p class="mt-3 {{ $progressStep >= 2 ? '' : 'opacity-50' }}">Documents Verified</p>
                    </div>

                    <div class="text-center">
                        @if ($progressStep >= 4)
                            <i class="fa-solid fa-circle-check text-3xl text-green-500"></i>
                            <p class="mt-3">Shipping</p>
                        @elseif ($progressStep === 3)
                            <i class="fa-solid fa-box text-3xl text-yellow-500"></i>
                            <p class="mt-3">Shipping</p>
                        @else
                            <i class="fa-solid fa-box text-3xl opacity-40"></i>
                            <p class="mt-3 opacity-50">Shipping</p>
                        @endif
                    </div>

                    <div class="text-center">
                        <i
                            class="fa-solid fa-house text-3xl {{ $progressStep >= 4 ? 'text-green-500' : 'opacity-40' }}"></i>
                        <p class="mt-3 {{ $progressStep >= 4 ? '' : 'opacity-50' }}">Delivered</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Winning History --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
            <div class="mb-8">
                <h3>Winning History</h3>
                <p class="mt-2 opacity-70">
                    All your previous winnings.
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[950px] text-[12px] md:text-base lg:text-xl">
                    <thead>
                        <tr class="border-b border-brand-border">
                            <th class="py-2 sm:py-4 text-left">Prize</th>
                            <th class="py-2 sm:py-4 text-left">Game</th>
                            <th class="py-2 sm:py-4 text-left">Winning Date</th>
                            <th class="py-2 sm:py-4 text-left">Prize Value</th>
                            <th class="py-2 sm:py-4 text-left">Status</th>
                            <th class="py-2 sm:py-4 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($history as $win)
                            @php
                                $statusClass = $statusStyles[$win->status] ?? 'bg-brand-dark text-white/70';
                                $statusLabel = $statusLabels[$win->status] ?? ucfirst((string) $win->status);
                            @endphp
                            <tr class="border-b border-brand-border last:border-0">
                                <td class="py-3 sm:py-5">{{ $win->prize }}</td>
                                <td class="py-3 sm:py-5">{{ $win->game }}</td>
                                <td class="py-3 sm:py-5">
                                    {{ $win->won_at ? $win->won_at->timezone(config('app.timezone'))->format('d M Y') : '—' }}
                                </td>
                                <td class="py-3 sm:py-5 text-green-500">{{ $win->amount_label }}</td>
                                <td class="py-3 sm:py-5">
                                    <span class="rounded-full {{ $statusClass }} px-3 py-1">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="py-3 sm:py-5">
                                    <button type="button"
                                        class="btn-primary px-3 sm:px-5 py-2 text-[12px] sm:text-sm"
                                        data-win-detail="{{ $win->id }}">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center opacity-70">
                                    You have no winnings yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($history->total() > 0)
                <div class="mt-8 border-t border-brand-border pt-6">
                    {{ $history->links('vendor.pagination.brand') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Win detail modal --}}
    <div id="winDetailModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-5">
        <div
            class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-3xl border border-brand-border bg-brand-surface shadow-[0_0_45px_rgba(34,197,94,.18)]">
            <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

            <div class="p-6 sm:p-8 relative">
                <button type="button" data-win-detail-close
                    class="absolute right-5 top-5 text-2xl opacity-70 transition hover:opacity-100 hover:text-orange-400">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="text-center">
                    <img id="winDetailImage" src="" alt=""
                        class="mx-auto h-40 w-40 rounded-2xl object-cover border-2 border-brand-border">
                    <small class="mt-5 inline-block uppercase tracking-[3px] text-brand-primary">
                        Prize Details
                    </small>
                    <h3 id="winDetailPrize" class="mt-3"></h3>
                    <p id="winDetailGame" class="mt-2 opacity-70"></p>
                    <span id="winDetailStatus"
                        class="mt-4 inline-block rounded-full px-3 py-1 text-sm"></span>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                        <p class="text-sm opacity-60">Ticket ID</p>
                        <p id="winDetailTicket" class="mt-2 break-all font-semibold"></p>
                    </div>
                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                        <p class="text-sm opacity-60">Prize Value</p>
                        <p id="winDetailAmount" class="mt-2 font-semibold text-green-500"></p>
                    </div>
                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                        <p class="text-sm opacity-60">Entry Fee</p>
                        <p id="winDetailFee" class="mt-2 font-semibold"></p>
                    </div>
                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                        <p class="text-sm opacity-60">Winning Date</p>
                        <p id="winDetailDate" class="mt-2 font-semibold"></p>
                    </div>
                </div>

                <p id="winDetailNote" class="mt-6 text-center text-sm opacity-70"></p>

                <div class="mt-8 text-center">
                    <button type="button" class="btn-primary" data-win-detail-close>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    (function () {
        const root = document.querySelector('[data-winnings-root]');
        if (!root || root.dataset.bound === '1') {
            return;
        }
        root.dataset.bound = '1';

        const wins = @json($winsPayload);
        const statusStyles = @json($statusStyles);
        const modal = document.getElementById('winDetailModal');
        if (!modal) {
            return;
        }

        const imageEl = document.getElementById('winDetailImage');
        const prizeEl = document.getElementById('winDetailPrize');
        const gameEl = document.getElementById('winDetailGame');
        const statusEl = document.getElementById('winDetailStatus');
        const ticketEl = document.getElementById('winDetailTicket');
        const amountEl = document.getElementById('winDetailAmount');
        const feeEl = document.getElementById('winDetailFee');
        const dateEl = document.getElementById('winDetailDate');
        const noteEl = document.getElementById('winDetailNote');

        function openModal(winId) {
            const win = wins[winId];
            if (!win) {
                return;
            }

            imageEl.src = win.image || '';
            imageEl.alt = win.game || 'Prize';
            prizeEl.textContent = win.prize || '—';
            gameEl.textContent = win.game || '—';
            ticketEl.textContent = win.ticket_id || '—';
            amountEl.textContent = win.amount_label || '—';
            feeEl.textContent = win.fee_label || '—';
            dateEl.textContent = win.won_at_label || '—';
            noteEl.textContent = win.note || '';

            statusEl.textContent = win.status_label || '';
            statusEl.className = 'mt-4 inline-block rounded-full px-3 py-1 text-sm ' +
                (statusStyles[win.status] || 'bg-brand-dark text-white/70');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        root.querySelectorAll('[data-win-detail]').forEach(function (button) {
            button.addEventListener('click', function () {
                openModal(button.getAttribute('data-win-detail'));
            });
        });

        modal.querySelectorAll('[data-win-detail-close]').forEach(function (button) {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    })();
</script>
