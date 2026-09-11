@php
    $winnerByCategory = $draw->winners->keyBy('prize_category');

    $prizes = collect($draw->prize_snapshot ?? []);

    if ($prizes->isEmpty()) {
        $prizes = collect([
            ['category' => 'first', 'amount' => $lottery->first_prize],
            ['category' => 'second', 'amount' => $lottery->second_prize],
            ['category' => 'third', 'amount' => $lottery->third_prize],
            ['category' => 'fourth', 'amount' => $lottery->fourth_prize],
            ['category' => 'fifth', 'amount' => $lottery->fifth_prize],
        ]);
    }

    $prizes = $prizes->mapWithKeys(function ($prize) {
        return [
            $prize['category'] => [
                'label' => match ($prize['category']) {
                    'first' => '1st',
                    'second' => '2nd',
                    'third' => '3rd',
                    'fourth' => '4th',
                    'fifth' => '5th',
                    default => ucfirst($prize['category']),
                },
                'amount' => (float) ($prize['amount'] ?? 0),
            ],
        ];
    });
@endphp

<div class="overflow-hidden rounded-2xl border border-brand-border">
    <div class="divide-y divide-brand-border">
        @foreach ($prizes as $category => $prize)
            @php
                $winner = $winnerByCategory->get($category);
                $isCurrentUser = $winner && auth()->check()
                    && $winner->user_id === auth()->id();
            @endphp

            <div class="flex items-center gap-3 px-4 py-3 {{ $isCurrentUser ? 'bg-green-500/10' : 'bg-brand-dark/60' }}">
                <span class="w-10 shrink-0 text-xs font-semibold uppercase tracking-wider text-orange-400">
                    {{ $prize['label'] }}
                </span>

                <div class="min-w-0 flex-1">
                    @if ($winner)
                        <p class="truncate text-sm font-semibold">
                            {{ $winner->user?->name ?? 'Winner' }}
                            @if ($isCurrentUser)
                                <span class="ml-1 text-xs font-medium text-green-400">You</span>
                            @endif
                        </p>
                        <p class="truncate font-mono text-xs text-green-400/80">
                            {{ $winner->ticket?->ticket_number ?? '—' }}
                        </p>
                    @else
                        <p class="text-sm opacity-40">No winner</p>
                    @endif
                </div>

                <strong class="shrink-0 text-sm text-orange-400">
                    {{ $lottery->currency }}
                    {{ number_format($prize['amount'], 2) }}
                </strong>
            </div>
        @endforeach
    </div>
</div>
