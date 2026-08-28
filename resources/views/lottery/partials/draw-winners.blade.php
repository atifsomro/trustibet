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
                    'first' => '1st Prize',
                    'second' => '2nd Prize',
                    'third' => '3rd Prize',
                    'fourth' => '4th Prize',
                    'fifth' => '5th Prize',
                    default => ucfirst($prize['category']),
                },
                'amount' => (float) ($prize['amount'] ?? 0),
            ],
        ];
    });
@endphp

<div class="space-y-3">
    @foreach ($prizes as $category => $prize)
        @php
            $winner = $winnerByCategory->get($category);
            $isCurrentUser = $winner && auth()->check()
                && $winner->user_id === auth()->id();
        @endphp

        <div class="rounded-2xl border {{ $isCurrentUser ? 'border-green-500/40 bg-green-500/10' : 'border-brand-border bg-brand-dark' }} p-5">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <span class="text-sm opacity-60">
                        {{ $prize['label'] }}
                    </span>

                    @if ($winner)
                        <h3 class="mt-1 text-lg font-bold">
                            {{ $winner->user?->name ?? 'Winner' }}
                        </h3>

                        <span class="mt-1 block font-mono text-sm text-green-400">
                            Ticket: {{ $winner->ticket?->ticket_number ?? '—' }}
                        </span>
                    @else
                        <h3 class="mt-1 text-lg font-semibold opacity-40">
                            No winner
                        </h3>
                        <span class="mt-1 block text-xs opacity-40">
                            This prize slot remained empty.
                        </span>
                    @endif
                </div>

                <div class="text-left md:text-right">
                    <strong class="block text-xl text-orange-400">
                        {{ $lottery->currency }}
                        {{ number_format($prize['amount'], 2) }}
                    </strong>

                    @if ($winner)
                        <span class="mt-1 inline-block rounded-full px-3 py-1 text-xs {{ $winner->payout_status === 'paid' ? 'bg-green-500/10 text-green-400' : 'bg-orange-500/10 text-orange-400' }}">
                            {{ ucfirst($winner->payout_status) }}
                        </span>
                    @endif
                </div>
            </div>

            @if ($isCurrentUser)
                <div class="mt-4 rounded-xl bg-green-500/10 px-4 py-3 text-sm text-green-400">
                    Congratulations! You won this prize.
                </div>
            @endif
        </div>
    @endforeach
</div>
