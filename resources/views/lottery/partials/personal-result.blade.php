@php
    $userId = auth('web')->id();
    $personal = $draw->personalResult($userId);
    $prizes = $draw->prizeCatalog($lottery);
    $compact = $compact ?? false;
@endphp

@if ($personal['outcome'] === 'won')
    <div class="{{ $compact ? 'overflow-hidden rounded-2xl border border-green-500/30' : 'space-y-3' }}">
        @if ($compact)
            <div class="divide-y divide-brand-border">
                @foreach ($personal['wins'] as $win)
                    @php
                        $prize = $prizes->get($win->prize_category, [
                            'short_label' => ucfirst((string) $win->prize_category),
                            'amount' => (float) $win->prize_amount,
                        ]);
                    @endphp
                    <div class="flex items-center gap-3 bg-green-500/10 px-4 py-3">
                        <span class="w-10 shrink-0 text-xs font-semibold uppercase tracking-wider text-orange-400">
                            {{ $prize['short_label'] ?? $prize['label'] ?? 'Win' }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-green-400">You won</p>
                            <p class="truncate font-mono text-xs text-green-400/80">
                                {{ $win->ticket?->ticket_number ?? '—' }}
                            </p>
                        </div>
                        <strong class="shrink-0 text-sm text-orange-400">
                            {{ $lottery->currency }}
                            {{ number_format((float) ($prize['amount'] ?? $win->prize_amount), 2) }}
                        </strong>
                    </div>
                @endforeach
            </div>
        @else
            @foreach ($personal['wins'] as $win)
                @php
                    $prize = $prizes->get($win->prize_category, [
                        'label' => ucfirst((string) $win->prize_category),
                        'amount' => (float) $win->prize_amount,
                    ]);
                @endphp
                <div class="rounded-2xl border border-green-500/40 bg-green-500/10 p-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <span class="text-sm opacity-60">{{ $prize['label'] ?? 'Prize' }}</span>
                            <h3 class="mt-1 text-lg font-bold text-green-400">You won this prize</h3>
                            <span class="mt-1 block font-mono text-sm text-green-400">
                                Ticket: {{ $win->ticket?->ticket_number ?? '—' }}
                            </span>
                        </div>
                        <div class="text-left md:text-right">
                            <strong class="block text-xl text-orange-400">
                                {{ $lottery->currency }}
                                {{ number_format((float) ($prize['amount'] ?? $win->prize_amount), 2) }}
                            </strong>
                            <span class="mt-1 inline-block rounded-full px-3 py-1 text-xs {{ $win->payout_status === 'paid' ? 'bg-green-500/10 text-green-400' : 'bg-orange-500/10 text-orange-400' }}">
                                {{ ucfirst((string) $win->payout_status) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 rounded-xl bg-green-500/10 px-4 py-3 text-sm text-green-400">
                        Congratulations! This result is private and only visible to you.
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@elseif ($personal['outcome'] === 'lost')
    <div class="rounded-2xl border border-brand-border bg-brand-dark px-4 py-5 text-center">
        <p class="text-sm font-semibold">You did not win this round</p>
        <p class="mt-1 text-xs opacity-50">
            Your result is private. Other players cannot see your tickets or outcome.
        </p>
    </div>
@elseif ($personal['outcome'] === 'not_entered')
    <div class="rounded-2xl border border-brand-border bg-brand-dark px-4 py-5 text-center">
        <p class="text-sm font-semibold opacity-70">You did not enter this round</p>
        <p class="mt-1 text-xs opacity-50">
            Individual results stay private. No player names or tickets are shown publicly.
        </p>
    </div>
@else
    <div class="rounded-2xl border border-brand-border bg-brand-dark px-4 py-5 text-center">
        <p class="text-sm font-semibold opacity-70">Sign in to view your private result</p>
    </div>
@endif
