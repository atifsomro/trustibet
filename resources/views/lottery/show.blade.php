@extends('layouts.master')

@section('content')

    @php
        $remainingTickets = $lottery->remainingTickets();
        $maxPerUser = $lottery->max_tickets_per_user;
        $userTicketCount = auth()->check() ? $lottery->ticketsForCurrentRoundUser(auth()->id()) : 0;
        $maxQuantity = $lottery->maxPurchaseQuantityForUser(auth()->id());
        $endsAt = $lottery->ends_at ?? $lottery->sales_end_at;
        $latestDraw = $draws->first();
        $userTickets = $userTickets ?? collect();
    @endphp

    <section class="lottery_detail py-10">

        <div class="container">

            {{-- Back --}}
            <div class="mb-4">

                <a href="{{ route('lotteries.index') }}"
                    class="inline-flex items-center gap-2 text-sm opacity-60 transition hover:text-green-500 hover:opacity-100">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Lotteries

                </a>

            </div>


            @include('lottery.partials.new-round-banner')

            @include('lottery.partials.user-balance')

            {{-- Messages --}}
            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-green-500/20 bg-green-500/10 px-5 py-4 text-sm text-green-400">

                    {{ session('success') }}

                </div>
            @endif


            @if ($errors->any())
                <div class="mb-4 rounded-2xl border border-red-500/20 bg-red-500/10 px-5 py-4 text-sm text-red-400">

                    <ul class="space-y-1">

                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach

                    </ul>

                </div>
            @endif


            {{-- ========================================================= --}}
            {{-- MAIN LAYOUT --}}
            {{-- ========================================================= --}}

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                {{-- ===================================================== --}}
                {{-- LEFT COLUMN --}}
                {{-- ===================================================== --}}

                <div class="lg:col-span-2">

                    {{-- ================================================= --}}
                    {{-- LOTTERY INFORMATION --}}
                    {{-- ================================================= --}}

                    <div class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                        {{-- Top Gradient --}}
                        <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
                        <div class="p-3 md:p-6">
                            {{-- Header --}}
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <span class="sec_subtitle text-xs">
                                        Lucky Draw
                                    </span>
                                    <h1 class="mt-1 text-xl font-bold md:text-3xl">
                                        {{ $lottery->title }}
                                    </h1>
                                    @if ($lottery->description)
                                        <div class="mt-2 text-xs leading-5 opacity-70">
                                            {!! nl2br(e($lottery->description)) !!}
                                        </div>
                                    @endif
                                </div>
                                <div
                                    class="shrink-0 rounded-xl border border-green-500/20 bg-green-500/5 px-4 py-3 text-center">
                                    <span class="block text-[10px] uppercase tracking-wider opacity-50">
                                        Ticket Price
                                    </span>
                                    <strong class="mt-1 block text-xl text-green-500">
                                        {{ $lottery->currency }}
                                        {{ number_format((float) $lottery->ticket_price, 2) }}
                                    </strong>
                                </div>
                            </div>
                            {{-- ================================================= --}}
                            {{-- LOTTERY INFORMATION --}}
                            {{-- ================================================= --}}

                            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                {{-- Ends --}}
                                <div
                                    class="rounded-2xl border border-green-500/10 bg-gradient-to-b from-brand-dark to-[#07131b] p-5">
                                    <span class="text-xs uppercase tracking-wider opacity-50">
                                        Ends
                                    </span>
                                    <div class="mt-2 text-lg font-bold text-orange-400">
                                        {{ $endsAt?->format('d M Y h:i A') ?? '—' }}
                                    </div>
                                    @if ($endsAt && !$lottery->isCompleted() && !$lottery->isCancelled())
                                        <span class="lottery-countdown mt-2 block font-mono text-sm text-green-400"
                                            data-end="{{ $endsAt->toIso8601String() }}"
                                            data-server-now="{{ now()->toIso8601String() }}"
                                            data-draw-url="{{ route('lotteries.draw-due', $lottery) }}"
                                            data-lottery-id="{{ $lottery->id }}">
                                            --:--:--
                                        </span>
                                    @endif
                                </div>
                                {{-- Draw --}}
                                <div
                                    class="rounded-2xl border border-green-500/10 bg-gradient-to-b from-brand-dark to-[#07131b] p-5">
                                    <span class="text-xs uppercase tracking-wider opacity-50">
                                        Draw
                                    </span>
                                    <div class="mt-2 text-lg font-bold text-orange-400">
                                        Automatic via scheduler
                                    </div>
                                    @if ($lottery->hasPreviousRounds())
                                        <span
                                            class="mt-2 block text-xs font-semibold uppercase tracking-[2px] text-green-400">
                                            New Round {{ $lottery->currentRoundNumber() }}
                                        </span>
                                    @endif
                                </div>
                                {{-- Tickets Sold --}}
                                <div
                                    class="rounded-2xl border border-green-500/10 bg-gradient-to-b from-brand-dark to-[#07131b] p-5">
                                    <span class="text-xs uppercase tracking-wider opacity-50">
                                        Tickets Sold
                                    </span>
                                    <div class="mt-2 text-2xl font-bold text-green-500">
                                        {{ number_format($lottery->totalCurrentRoundTickets()) }}
                                    </div>
                                </div>
                                {{-- Tickets Remaining --}}
                                <div
                                    class="rounded-2xl border border-green-500/10 bg-gradient-to-b from-brand-dark to-[#07131b] p-5">
                                    <span class="text-xs uppercase tracking-wider opacity-50">
                                        Tickets Remaining
                                    </span>
                                    <div class="mt-2 text-2xl font-bold text-orange-400">
                                        @if ($remainingTickets === null)
                                            Unlimited
                                        @else
                                            {{ number_format($remainingTickets) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- ================================================= --}}
                            {{-- PRIZES --}}
                            {{-- ================================================= --}}
                            <div class="mt-6">
                                <h3 class="text-lg font-bold">
                                    Prize Structure
                                </h3>
                                <p class="mt-1 text-xs opacity-60">
                                    Five winners will be selected. Each user can win only once.
                                </p>
                                <div class="mt-3 space-y-2">
                                    <div
                                        class="flex items-center justify-between rounded-xl border border-brand-border bg-brand-dark p-3">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500/10 text-xs font-bold text-orange-400">
                                                1
                                            </span>
                                            <span class="text-xs opacity-70">
                                                1st Prize
                                            </span>
                                        </div>
                                        <strong class="text-xs text-orange-400">
                                            {{ $lottery->currency }}
                                            {{ number_format((float) $lottery->first_prize, 2) }}
                                        </strong>
                                    </div>
                                    <div
                                        class="flex items-center justify-between rounded-xl border border-brand-border bg-brand-dark p-3">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500/10 text-xs font-bold text-orange-400">
                                                2
                                            </span>
                                            <span class="text-xs opacity-70">
                                                2nd Prize
                                            </span>
                                        </div>
                                        <strong class="text-xs text-orange-400">
                                            {{ $lottery->currency }}
                                            {{ number_format((float) $lottery->second_prize, 2) }}
                                        </strong>
                                    </div>
                                    <div
                                        class="flex items-center justify-between rounded-xl border border-brand-border bg-brand-dark p-3">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500/10 text-xs font-bold text-orange-400">
                                                3
                                            </span>
                                            <span class="text-xs opacity-70">
                                                3rd Prize
                                            </span>
                                        </div>
                                        <strong class="text-xs text-orange-400">
                                            {{ $lottery->currency }}
                                            {{ number_format((float) $lottery->third_prize, 2) }}
                                        </strong>
                                    </div>
                                    <div
                                        class="flex items-center justify-between rounded-xl border border-brand-border bg-brand-dark p-3">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500/10 text-xs font-bold text-orange-400">
                                                4
                                            </span>
                                            <span class="text-xs opacity-70">
                                                4th Prize
                                            </span>
                                        </div>
                                        <strong class="text-xs text-orange-400">
                                            {{ $lottery->currency }}
                                            {{ number_format((float) $lottery->fourth_prize, 2) }}
                                        </strong>
                                    </div>
                                    <div
                                        class="flex items-center justify-between rounded-xl border border-brand-border bg-brand-dark p-3">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500/10 text-xs font-bold text-orange-400">
                                                5
                                            </span>
                                            <span class="text-xs opacity-70">
                                                5th Prize
                                            </span>
                                        </div>
                                        <strong class="text-xs text-orange-400">
                                            {{ $lottery->currency }}
                                            {{ number_format((float) $lottery->fifth_prize, 2) }}
                                        </strong>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>


                    @if ($userTickets->count())
                        <div class="mt-4 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                            <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
                            <div class="p-5 md:p-8">
                                <h2 class="text-2xl font-bold">Your tickets</h2>
                                <p class="mt-2 text-sm opacity-60">
                                    Your purchases and results for this lottery.
                                </p>
                                <div class="mt-5 space-y-3">
                                    @foreach ($userTickets as $ticket)
                                        <div
                                            class="flex items-center justify-between rounded-2xl border border-brand-border bg-brand-dark p-4">
                                            <div>
                                                <span
                                                    class="font-mono text-sm text-green-400">{{ $ticket->ticket_number }}</span>
                                                <span class="mt-1 block text-xs opacity-50">
                                                    {{ $ticket->purchased_at?->format('d M Y h:i A') }}
                                                    · {{ $lottery->currency }}
                                                    {{ number_format((float) $ticket->price, 2) }}
                                                </span>
                                            </div>
                                            <span
                                                class="rounded-full border px-3 py-1 text-xs
                                            @if ($ticket->status === 'winner') border-orange-500/20 bg-orange-500/10 text-orange-400
                                            @elseif ($ticket->status === 'lost') border-gray-500/20 bg-gray-500/10 opacity-70
                                            @else border-green-500/20 bg-green-500/10 text-green-400 @endif">
                                                {{ ucfirst($ticket->status === 'active' ? 'pending' : $ticket->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ================================================= --}}
                    {{-- DRAW HISTORY --}}
                    {{-- ================================================= --}}

                    @if ($draws->count())
                        <div class="mt-4 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                            <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
                            <div class="p-4 md:p-6">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <span class="sec_subtitle text-xs">
                                            Draw History
                                        </span>
                                        <h2 class="mt-1.5 text-xl font-bold md:text-2xl">
                                            Lottery Results
                                        </h2>
                                        <p class="mt-1.5 text-xs opacity-60">
                                            This lottery has
                                            {{ $draws->total() }}
                                            completed draw(s).
                                            Open results to see your private win/lose history.
                                        </p>
                                    </div>
                                    @if ($latestDraw)
                                        <a href="{{ route('lotteries.results', $lottery) }}"
                                            class="inline-flex w-fit items-center rounded-full border border-green-500/20 bg-green-500/10 p-3 text-xs font-semibold text-green-400 transition hover:border-green-500/40">
                                            View my results
                                        </a>
                                    @endif
                                </div>
                                <div class="mt-5 overflow-hidden rounded-2xl border border-brand-border">
                                    <div class="overflow-x-auto">
                                        <table class="w-full min-w-[650px] text-left">
                                            <thead class="bg-brand-dark/70">
                                                <tr class="border-b border-brand-border">
                                                    <th
                                                        class="px-3 py-2 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500 text-base font-bold uppercase">
                                                        Draw
                                                    </th>
                                                    <th
                                                        class="px-3 py-2 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500 text-base font-bold uppercase">
                                                        Date
                                                    </th>
                                                    <th
                                                        class="px-3 py-2 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500 text-base font-bold uppercase">
                                                        Tickets
                                                    </th>
                                                    <th
                                                        class="px-3 py-2 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500 text-base font-bold uppercase">
                                                        Status
                                                    </th>
                                                    <th
                                                        class="px-3 py-2 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500 text-right text-base font-bold uppercase">
                                                        Result
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($draws as $resultDraw)
                                                    @if (!$resultDraw->winners_announced)
                                                        <tr
                                                            class="border-b border-brand-border transition last:border-b-0 hover:bg-white/[0.02]">
                                                            <td class="p-2">
                                                                <strong class="block text-xs font-semibold">
                                                                    Draw #{{ $resultDraw->id }}
                                                                </strong>
                                                            </td>
                                                            <td class="p-2 text-xs">
                                                                {{ $resultDraw->completed_at?->format('d M Y h:i A') ?? '—' }}
                                                            </td>
                                                            <td class="p-2 text-xs">
                                                                —
                                                            </td>
                                                            <td class="p-2">
                                                                <span
                                                                    class="inline-flex rounded-full border border-orange-500/20 bg-orange-500/10 text-[10px] font-semibold text-orange-400">
                                                                    Pending
                                                                </span>
                                                            </td>
                                                            <td class="p-2 text-right text-xs">
                                                                Not available
                                                            </td>
                                                        </tr>
                                                        @continue
                                                    @endif
                                                    @php
                                                        $personal = $resultDraw->personalResult(auth('web')->id());
                                                    @endphp
                                                    <tr
                                                        class="border-b border-brand-border transition last:border-b-0 hover:bg-green-500/[0.025]">
                                                        <td class="p-2">
                                                            <a href="{{ route('lotteries.results', ['lottery' => $lottery, 'draw' => $resultDraw->id]) }}"
                                                                class="text-xs font-semibold transition hover:text-green-400">
                                                                Draw #{{ $resultDraw->id }}
                                                            </a>
                                                        </td>
                                                        <td class="p-2 text-xs">
                                                            {{ $resultDraw->completed_at?->format('d M Y h:i A') ?? '—' }}
                                                        </td>
                                                        <td class="p-2 text-xs">
                                                            {{ $resultDraw->total_tickets }}
                                                        </td>
                                                        <td class="p-2">
                                                            <span
                                                                class="inline-flex rounded-full border border-green-500/20 bg-green-500/10 p-2 text-xs font-semibold text-green-400">
                                                                Completed
                                                            </span>
                                                        </td>
                                                        <td class="p-2 text-right">
                                                            @if ($personal['outcome'] === 'won')
                                                                <a href="{{ route('lotteries.results', ['lottery' => $lottery, 'draw' => $resultDraw->id]) }}"
                                                                    class="inline-flex flex-col items-end">
                                                                    <span class="text-xs font-semibold text-green-400">
                                                                        You won
                                                                    </span>
                                                                    <span class="mt-1 text-xs opacity-50">
                                                                        View result →
                                                                    </span>
                                                                </a>
                                                            @elseif ($personal['outcome'] === 'lost')
                                                                <a href="{{ route('lotteries.results', ['lottery' => $lottery, 'draw' => $resultDraw->id]) }}"
                                                                    class="inline-flex flex-col items-end">
                                                                    <span class="text-xs font-semibold text-orange-400">
                                                                        No win
                                                                    </span>
                                                                    <span class="mt-1 text-xs opacity-50">
                                                                        View result →
                                                                    </span>
                                                                </a>
                                                            @else
                                                                <a href="{{ route('lotteries.results', ['lottery' => $lottery, 'draw' => $resultDraw->id]) }}"
                                                                    class="inline-flex flex-col items-end">
                                                                    <span class="text-xs font-semibold">
                                                                        Not entered
                                                                    </span>
                                                                    <span class="mt-1 text-xs opacity-50">
                                                                        View result →
                                                                    </span>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if ($draws->hasPages())
                                    <div class="mt-4 flex items-center justify-center gap-2">
                                        @if ($draws->onFirstPage())
                                            <span
                                                class="inline-flex h-8 items-center rounded-lg border border-brand-border px-3 text-[11px] font-medium opacity-30">
                                                <i class="fa-solid fa-angle-right"></i> Prev
                                            </span>
                                        @else
                                            <a href="{{ $draws->previousPageUrl() }}"
                                                class="inline-flex h-8 items-center rounded-lg border border-brand-border px-3 text-[11px] font-medium transition hover:border-green-500/30 hover:text-green-400">
                                                <i class="fa-solid fa-angle-right"></i> Prev
                                            </a>
                                        @endif
                                        <span
                                            class="inline-flex h-8 min-w-[58px] items-center justify-center rounded-lg bg-brand-dark px-3 text-[11px] font-semibold">
                                            {{ $draws->currentPage() }}
                                            <span class="mx-1 opacity-30">/</span>
                                            {{ $draws->lastPage() }}
                                        </span>
                                        @if ($draws->hasMorePages())
                                            <a href="{{ $draws->nextPageUrl() }}"
                                                class="inline-flex h-8 items-center rounded-lg border border-brand-border px-3 text-[11px] font-medium transition hover:border-green-500/30 hover:text-green-400">
                                                Next <i class="fa-solid fa-angle-right"></i>
                                            </a>
                                        @else
                                            <span
                                                class="inline-flex h-8 items-center rounded-lg border border-brand-border px-3 text-[11px] font-medium opacity-30">
                                                Next <i class="fa-solid fa-angle-right"></i>
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="mt-4 rounded-3xl border border-brand-border bg-brand-surface p-6 md:p-8">
                            <div class="text-center">
                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-500/10">
                                    <i class="fa-solid fa-trophy text-xl text-orange-400"></i>
                                </div>
                                <h3 class="mt-4 text-xl font-bold">
                                    No Draw Results Yet
                                </h3>
                                <p class="mt-2 text-sm opacity-60">
                                    The lottery has not had a completed draw yet.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
                {{-- ===================================================== --}}
                {{-- RIGHT COLUMN - PURCHASE BOX --}}
                {{-- ===================================================== --}}

                <div>

                    <div class="sticky top-6 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

                        <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                        <div class="p-5 md:p-6" data-lottery-show-actions="{{ $lottery->id }}">

                            <h3 class="text-xl font-bold">
                                Buy Tickets
                            </h3>

                            @include('lottery.partials.buy-ticket-form', ['lottery' => $lottery])

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

@endsection


@push('scripts')
    @include('lottery.partials.countdown-scripts')
@endpush
