@extends('layouts.master')

@section('content')

@php
    $winnerByCategory = $draw->winners->keyBy('prize_category');

    $prizes = collect($draw->prize_snapshot ?? []);

    /*
     * Fallback for draws created before the round snapshot migration.
     */
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

<section class="py-10">
    <div class="container">

        <div class="mb-6">
            <a
                href="{{ route('lotteries.results', $lottery) }}#draw-{{ $draw->id }}"
                class="inline-flex items-center gap-2 text-sm opacity-60 transition hover:text-green-500 hover:opacity-100"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Back to Results
            </a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
            <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

            <div class="p-5 md:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="sec_subtitle">Lottery Results</span>
                        <h1 class="mt-3 text-3xl font-bold">
                            {{ $lottery->title }}
                        </h1>
                        <p class="mt-2 text-sm opacity-60">
                            Draw #{{ $draw->id }}
                            · {{ $draw->completed_at?->format('d M Y h:i A') }}
                        </p>
                    </div>

                    <span class="rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2 text-sm font-semibold text-green-400">
                        {{ $draw->total_winners }} / 5 Winners
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-5">
                        <span class="block text-xs uppercase tracking-wider opacity-50">
                            Draw Tickets
                        </span>
                        <strong class="mt-2 block text-2xl">
                            {{ number_format($draw->total_tickets) }}
                        </strong>
                    </div>

                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-5">
                        <span class="block text-xs uppercase tracking-wider opacity-50">
                            Drawn At
                        </span>
                        <strong class="mt-2 block text-sm">
                            {{ $draw->completed_at?->format('d M Y h:i A') ?? '—' }}
                        </strong>
                    </div>

                    {{-- <div class="rounded-2xl border border-brand-border bg-brand-dark p-5">
                        <span class="block text-xs uppercase tracking-wider opacity-50">
                            Sales Period
                        </span>
                        <strong class="mt-2 block text-sm">
                            {{ $draw->sales_start_at?->format('d M Y h:i A') ?? 'Any start' }}
                            →
                            {{ $draw->sales_end_at?->format('d M Y h:i A') ?? '—' }}
                        </strong>
                    </div> --}}
                </div>

                <div class="mt-8">
                    <h2 class="text-2xl font-bold">Winning Tickets</h2>

                    <div class="mt-5 space-y-3">
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
                                        🎉 Congratulations! You won this prize.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>
@endsection
