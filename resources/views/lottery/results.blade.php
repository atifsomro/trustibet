@extends('layouts.master')

@section('content')

    @php
        $userId = auth('web')->id();
    @endphp

    <section class="py-10">
        <div class="container">

            <div class="mb-6">
                <a href="{{ route('lotteries.show', $lottery) }}"
                    class="inline-flex items-center gap-2 text-sm opacity-60 transition hover:text-green-500 hover:opacity-100">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to {{ $lottery->title }}
                </a>
            </div>

            <div class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="p-5 md:p-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <span class="sec_subtitle">Private Results</span>
                            <h1 class="mt-3 text-3xl font-bold md:text-4xl">
                                {{ $lottery->title }}
                            </h1>
                            <p class="mt-3 max-w-xl text-sm opacity-70">
                                Only your own win or lose result is shown here. Other players’ names and tickets stay
                                completely private.
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('lotteries.show', $lottery) }}"
                                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-green-500 to-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-green-500/20 transition hover:scale-[1.02]">
                                <i class="fa-solid fa-ticket"></i>
                                Buy tickets
                            </a>
                            <a href="{{ route('lotteries.index') }}"
                                class="inline-flex items-center gap-2 rounded-2xl border border-brand-border bg-brand-dark px-5 py-3 text-sm font-semibold opacity-80 transition hover:border-green-500/40 hover:opacity-100">
                                All lotteries
                            </a>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                            <span class="block text-xs uppercase tracking-wider opacity-50">Rounds</span>
                            <strong class="mt-2 block text-2xl text-green-400">
                                {{ number_format($totalRounds) }}
                            </strong>
                        </div>
                        <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                            <span class="block text-xs uppercase tracking-wider opacity-50">Matched</span>
                            <strong class="mt-2 block text-2xl">
                                {{ number_format($matchedRounds) }}
                            </strong>
                        </div>
                        <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                            <span class="block text-xs uppercase tracking-wider opacity-50">1st Prize</span>
                            <strong class="mt-2 block text-2xl text-orange-400">
                                {{ $lottery->currency }}
                                {{ number_format((float) $lottery->first_prize, 2) }}
                            </strong>
                        </div>
                        <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                            <span class="block text-xs uppercase tracking-wider opacity-50">Showing</span>
                            <strong class="mt-2 block text-2xl">
                                @if ($draws->total() > 0)
                                    {{ $draws->firstItem() }}–{{ $draws->lastItem() }}
                                @else
                                    0
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                <form method="GET" action="{{ route('lotteries.results', $lottery) }}" class="p-5 md:p-8">
                    @if ($errors->any())
                        <div class="mb-4 rounded-2xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                            <ul class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
                        <div class="md:col-span-2 xl:col-span-6">
                            <label for="lottery-results-search"
                                class="mb-2 block text-xs uppercase tracking-wider opacity-50">
                                Search my tickets
                            </label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-white/40">
                                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                                </span>
                                <input id="lottery-results-search" type="text" name="q"
                                    value="{{ $filters['q'] }}" placeholder="Enter your ticket number" autocomplete="off"
                                    class="w-full rounded-2xl border border-brand-border bg-brand-dark py-3.5 pl-14 pr-4 text-sm text-white placeholder:text-white/35 outline-none transition focus:border-green-500/40">
                            </div>
                        </div>

                        <div>
                            <label for="lottery-results-round"
                                class="mb-2 block text-xs uppercase tracking-wider opacity-50">Round</label>
                            <input id="lottery-results-round" type="number" name="round" min="1"
                                max="{{ $maxDailyRound }}" value="{{ $filters['round'] }}"
                                placeholder="e.g. {{ $maxDailyRound }}"
                                class="w-full rounded-2xl border border-brand-border bg-brand-dark px-4 py-3.5 text-sm text-white placeholder:text-white/35 outline-none transition focus:border-green-500/40">
                        </div>

                        <div>
                            <label for="lottery-results-prize"
                                class="mb-2 block text-xs uppercase tracking-wider opacity-50">My prize</label>
                            <select id="lottery-results-prize" name="prize"
                                class="w-full rounded-2xl border border-brand-border bg-brand-dark px-4 py-3.5 text-sm text-white outline-none transition focus:border-green-500/40">
                                <option value="">Any / all</option>
                                <option value="first" @selected($filters['prize'] === 'first')>I won 1st</option>
                                <option value="second" @selected($filters['prize'] === 'second')>I won 2nd</option>
                                <option value="third" @selected($filters['prize'] === 'third')>I won 3rd</option>
                                <option value="fourth" @selected($filters['prize'] === 'fourth')>I won 4th</option>
                                <option value="fifth" @selected($filters['prize'] === 'fifth')>I won 5th</option>
                            </select>
                        </div>

                        <div>
                            <label for="lottery-results-sort"
                                class="mb-2 block text-xs uppercase tracking-wider opacity-50">Sort</label>
                            <select id="lottery-results-sort" name="sort"
                                class="w-full rounded-2xl border border-brand-border bg-brand-dark px-4 py-3.5 text-sm text-white outline-none transition focus:border-green-500/40">
                                <option value="newest" @selected($filters['sort'] === 'newest')>Newest first</option>
                                <option value="oldest" @selected($filters['sort'] === 'oldest')>Oldest first</option>
                            </select>
                        </div>

                        <div>
                            <label for="lottery-results-from"
                                class="mb-2 block text-xs uppercase tracking-wider opacity-50">From</label>
                            <input id="lottery-results-from" type="date" name="from" value="{{ $filters['from'] }}"
                                class="w-full rounded-2xl border border-brand-border bg-brand-dark px-4 py-3.5 text-sm text-white outline-none transition [color-scheme:dark] focus:border-green-500/40">
                        </div>

                        <div>
                            <label for="lottery-results-to"
                                class="mb-2 block text-xs uppercase tracking-wider opacity-50">To</label>
                            <input id="lottery-results-to" type="date" name="to" value="{{ $filters['to'] }}"
                                class="w-full rounded-2xl border border-brand-border bg-brand-dark px-4 py-3.5 text-sm text-white outline-none transition [color-scheme:dark] focus:border-green-500/40">
                        </div>

                        <div class="flex items-end">
                            <label
                                class="flex h-[50px] w-full cursor-pointer items-center gap-3 rounded-2xl border border-brand-border bg-brand-dark px-4 text-sm text-white">
                                <input type="checkbox" name="my_wins" value="1" @checked($filters['my_wins'])
                                    class="h-4 w-4 rounded border-brand-border bg-brand-surface text-green-500 focus:ring-green-500/40">
                                <span>My wins only</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-green-500 to-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-green-500/20 transition hover:scale-[1.02]">
                            <i class="fa-solid fa-filter"></i>
                            Apply filters
                        </button>

                        @if ($hasActiveFilters)
                            <a href="{{ route('lotteries.results', $lottery) }}"
                                class="inline-flex items-center gap-2 rounded-2xl border border-brand-border bg-brand-dark px-5 py-3 text-sm font-semibold opacity-80 transition hover:border-orange-500/40 hover:opacity-100">
                                <i class="fa-solid fa-xmark"></i>
                                Clear
                            </a>
                            <span class="text-xs opacity-50">
                                {{ number_format($matchedRounds) }}
                                {{ \Illuminate\Support\Str::plural('round', $matchedRounds) }} matched
                            </span>
                        @endif
                    </div>
                </form>
            </div>

            <div class="mt-8">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-lg font-bold">Your draw history</h2>
                    @if ($draws->lastPage() > 1)
                        <span class="text-sm opacity-50">
                            Page {{ $draws->currentPage() }} of {{ $draws->lastPage() }}
                        </span>
                    @endif
                </div>

                @if ($draws->count())
                    <div class="space-y-3">
                        @foreach ($draws as $draw)
                            @php
                                $roundNumber = $roundNumbers[(int) $draw->id] ?? null;
                                $roundDate = $roundDates[(int) $draw->id] ?? $draw->roundDate();
                                $isFocused = $focusDrawId
                                    ? (int) $focusDrawId === (int) $draw->id
                                    : $loop->first && !$hasActiveFilters;
                                $personal = $draw->personalResult($userId);
                                $isLatest = (int) $draw->id === (int) $latestDrawId;
                            @endphp

                            <details id="draw-{{ $draw->id }}"
                                class="group overflow-hidden rounded-2xl border border-brand-border bg-brand-surface open:border-green-500/30"
                                @if ($isFocused) open @endif>
                                <summary
                                    class="flex cursor-pointer list-none items-center gap-3 p-3 transition hover:bg-white/[0.02] md:p-4 [&::-webkit-details-marker]:hidden">
                                    <div
                                        class="flex h-10 w-10 shrink-0 flex-col items-center justify-center rounded-xl border border-orange-500/20 bg-orange-500/10">
                                        <span class="text-[8px] uppercase tracking-wider text-orange-400/80">Rnd</span>
                                        <span class="text-base font-bold leading-none text-orange-400">
                                            {{ $roundNumber ?? '—' }}
                                        </span>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <h3 class="text-sm font-bold">
                                                Round {{ $roundNumber ?? $draw->id }} · {{ $roundDate->format('d M Y') }}
                                            </h3>

                                            @if ($isLatest)
                                                <span
                                                    class="rounded-full border border-green-500/20 bg-green-500/10 px-2 py-0.5 text-[9px] font-semibold text-green-400">
                                                    Latest
                                                </span>
                                            @endif

                                            @if ($personal['outcome'] === 'won')
                                                <span
                                                    class="rounded-full border border-green-500/20 bg-green-500/10 px-2 py-0.5 text-[9px] font-semibold text-green-400">
                                                    You won
                                                </span>
                                            @elseif ($personal['outcome'] === 'lost')
                                                <span
                                                    class="rounded-full border border-orange-500/20 bg-orange-500/10 px-2 py-0.5 text-[9px] font-semibold text-orange-400">
                                                    No win
                                                </span>
                                            @else
                                                <span
                                                    class="rounded-full border border-brand-border bg-brand-dark px-2 py-0.5 text-[9px] font-semibold opacity-50">
                                                    Not entered
                                                </span>
                                            @endif
                                        </div>

                                        <p class="mt-0.5 truncate text-[11px] opacity-60">
                                            {{ $draw->completed_at?->format('d M Y · h:i A') ?? '—' }}
                                            · {{ number_format($draw->total_tickets) }} tickets in round
                                        </p>
                                    </div>

                                    <i
                                        class="fa-solid fa-chevron-down shrink-0 text-xs opacity-40 transition group-open:rotate-180"></i>
                                </summary>

                                <div class="border-t border-brand-border px-3 pb-3 pt-3 md:px-4 md:pb-4">
                                    @include('lottery.partials.personal-result', [
                                        'lottery' => $lottery,
                                        'draw' => $draw,
                                        'compact' => true,
                                    ])

                                    <div class="mt-3 flex justify-end">
                                        <a href="{{ route('lotteries.draws.show', [$lottery, $draw]) }}"
                                            class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-green-400 transition hover:underline">
                                            Full private details
                                            <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </details>
                        @endforeach
                    </div>

                    @if ($draws->hasPages())
                        <div class="mt-8 rounded-3xl border border-brand-border bg-brand-surface px-4 py-4 md:px-6">
                            {{ $draws->links('vendor.pagination.brand') }}
                        </div>
                    @endif
                @else
                    <div
                        class="rounded-3xl border border-dashed border-brand-border bg-brand-surface/50 px-6 py-16 text-center">
                        <div
                            class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-orange-500/20 bg-orange-500/10">
                            <i class="fa-solid fa-magnifying-glass text-xl text-orange-400"></i>
                        </div>
                        <h3 class="mt-5 text-lg font-bold">No rounds matched</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm opacity-60">
                            Try a different filter or clear them to see your private draw history.
                        </p>
                        @if ($hasActiveFilters)
                            <a href="{{ route('lotteries.results', $lottery) }}"
                                class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-green-400 hover:underline">
                                Clear filters
                            </a>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const focused = document.querySelector('details[open]');
                if (focused && window.location.search.includes('draw=')) {
                    focused.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        </script>
    @endpush

@endsection
