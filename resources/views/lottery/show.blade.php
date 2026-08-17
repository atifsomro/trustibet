@extends('layouts.master')

@section('content')

@php
    $remainingTickets = $lottery->remainingTickets();

    $maxPerUser = (int) config(
        'lottery.max_tickets_per_user',
        10
    );

    /*
     * Ticket limits are applied per draw round.
     */
    $userTickets = auth()->check()
        ? $lottery->ticketsForCurrentRoundUser(auth()->id())
        : 0;

    $remainingUserTickets = max(
        0,
        $maxPerUser - $userTickets
    );

    $maxQuantity = $remainingUserTickets;

    if ($remainingTickets !== null) {
        $maxQuantity = min(
            $maxQuantity,
            $remainingTickets
        );
    }

    /*
     * $draws is provided by LotteryController@show().
     *
     * There is NO $draw variable on this page.
     * Individual draw results are available through
     * lotteries.draws.show.
     */
    $latestDraw = $draws->first();
@endphp

<section class="lottery_detail py-10">

    <div class="container">

        {{-- Back --}}
        <div class="mb-6">

            <a
                href="{{ route('lotteries.index') }}"
                class="inline-flex items-center gap-2 text-sm opacity-60 transition hover:text-green-500 hover:opacity-100">

                <i class="fa-solid fa-arrow-left"></i>

                Back to Lotteries

            </a>

        </div>


        {{-- Messages --}}
        @if (session('success'))

            <div
                class="mb-6 rounded-2xl border border-green-500/20 bg-green-500/10 px-5 py-4 text-sm text-green-400">

                {{ session('success') }}

            </div>

        @endif


        @if ($errors->any())

            <div
                class="mb-6 rounded-2xl border border-red-500/20 bg-red-500/10 px-5 py-4 text-sm text-red-400">

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

                <div
                    class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

                    {{-- Top Gradient --}}
                    <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                    <div class="p-5 md:p-8">

                        {{-- Header --}}
                        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                            <div>

                                <span class="sec_subtitle">
                                    Lucky Draw
                                </span>

                                <h1 class="mt-3 text-3xl font-bold md:text-4xl">
                                    {{ $lottery->title }}
                                </h1>

                                @if ($lottery->description)

                                    <div class="mt-4 text-sm leading-7 opacity-70">
                                        {!! nl2br(e($lottery->description)) !!}
                                    </div>

                                @endif

                            </div>


                            {{-- Ticket Price --}}
                            <div
                                class="shrink-0 rounded-2xl border border-green-500/20 bg-green-500/5 px-6 py-4 text-center">

                                <span class="block text-xs uppercase tracking-wider opacity-50">
                                    Ticket Price
                                </span>

                                <strong class="mt-1 block text-2xl text-green-500">

                                    {{ $lottery->currency }}
                                    {{ number_format((float) $lottery->ticket_price, 2) }}

                                </strong>

                            </div>

                        </div>


                        {{-- ================================================= --}}
                        {{-- LOTTERY INFORMATION --}}
                        {{-- ================================================= --}}

                        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">

                            {{-- Sales End --}}
                            <div
                                class="rounded-2xl border border-green-500/10 bg-gradient-to-b from-brand-dark to-[#07131b] p-5">

                                <span class="text-xs uppercase tracking-wider opacity-50">
                                    Sales Close
                                </span>

                                <div class="mt-2 text-lg font-bold text-orange-400">

                                    {{ $lottery->sales_end_at?->format('d M Y h:i A') }}

                                </div>

                            </div>


                            {{-- Draw --}}
                            <div
                                class="rounded-2xl border border-green-500/10 bg-gradient-to-b from-brand-dark to-[#07131b] p-5">

                                <span class="text-xs uppercase tracking-wider opacity-50">
                                    Draw
                                </span>

                                <div class="mt-2 text-lg font-bold text-orange-400">

                                    {{ $lottery->draw_at?->format('d M Y h:i A') ?? 'After sales close' }}

                                </div>

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

                        <div class="mt-8">

                            <h3 class="text-xl font-bold">
                                Prize Structure
                            </h3>

                            <p class="mt-2 text-sm opacity-60">
                                Five winners will be selected. Each user can win only once.
                            </p>


                            <div class="mt-4 space-y-3">

                                {{-- First Prize --}}
                                <div
                                    class="flex items-center justify-between rounded-2xl border border-brand-border bg-brand-dark p-4">

                                    <div class="flex items-center gap-3">

                                        <span
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-500/10 text-sm font-bold text-orange-400">
                                            1
                                        </span>

                                        <span class="opacity-70">
                                            1st Prize
                                        </span>

                                    </div>

                                    <strong class="text-orange-400">

                                        {{ $lottery->currency }}
                                        {{ number_format((float) $lottery->first_prize, 2) }}

                                    </strong>

                                </div>


                                {{-- Second Prize --}}
                                <div
                                    class="flex items-center justify-between rounded-2xl border border-brand-border bg-brand-dark p-4">

                                    <div class="flex items-center gap-3">

                                        <span
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-500/10 text-sm font-bold text-orange-400">
                                            2
                                        </span>

                                        <span class="opacity-70">
                                            2nd Prize
                                        </span>

                                    </div>

                                    <strong class="text-orange-400">

                                        {{ $lottery->currency }}
                                        {{ number_format((float) $lottery->second_prize, 2) }}

                                    </strong>

                                </div>


                                {{-- Third Prize --}}
                                <div
                                    class="flex items-center justify-between rounded-2xl border border-brand-border bg-brand-dark p-4">

                                    <div class="flex items-center gap-3">

                                        <span
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-500/10 text-sm font-bold text-orange-400">
                                            3
                                        </span>

                                        <span class="opacity-70">
                                            3rd Prize
                                        </span>

                                    </div>

                                    <strong class="text-orange-400">

                                        {{ $lottery->currency }}
                                        {{ number_format((float) $lottery->third_prize, 2) }}

                                    </strong>

                                </div>


                                {{-- Fourth Prize --}}
                                <div
                                    class="flex items-center justify-between rounded-2xl border border-brand-border bg-brand-dark p-4">

                                    <div class="flex items-center gap-3">

                                        <span
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-500/10 text-sm font-bold text-orange-400">
                                            4
                                        </span>

                                        <span class="opacity-70">
                                            4th Prize
                                        </span>

                                    </div>

                                    <strong class="text-orange-400">

                                        {{ $lottery->currency }}
                                        {{ number_format((float) $lottery->fourth_prize, 2) }}

                                    </strong>

                                </div>


                                {{-- Fifth Prize --}}
                                <div
                                    class="flex items-center justify-between rounded-2xl border border-brand-border bg-brand-dark p-4">

                                    <div class="flex items-center gap-3">

                                        <span
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-500/10 text-sm font-bold text-orange-400">
                                            5
                                        </span>

                                        <span class="opacity-70">
                                            5th Prize
                                        </span>

                                    </div>

                                    <strong class="text-orange-400">

                                        {{ $lottery->currency }}
                                        {{ number_format((float) $lottery->fifth_prize, 2) }}

                                    </strong>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- DRAW HISTORY --}}
                {{-- ================================================= --}}

                @if ($draws->count())

                    <div class="mt-8 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

                        <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                        <div class="p-5 md:p-8">

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                <div>

                                    <span class="sec_subtitle">
                                        Draw History
                                    </span>

                                    <h2 class="mt-2 text-2xl font-bold">
                                        Lottery Results
                                    </h2>

                                    <p class="mt-2 text-sm opacity-60">

                                        This lottery has
                                        {{ $draws->count() }}
                                        completed draw(s).

                                    </p>

                                </div>


                                @if ($latestDraw)

                                    <span
                                        class="rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2 text-xs font-semibold text-green-400">

                                        Latest: Draw #{{ $latestDraw->id }}

                                    </span>

                                @endif

                            </div>


                            <div class="mt-6 space-y-3">

                                @foreach ($draws as $resultDraw)

                                    <a
                                        href="{{ route('lotteries.draws.show', [
                                            'lottery' => $lottery,
                                            'draw' => $resultDraw,
                                        ]) }}"
                                        class="flex items-center justify-between gap-4 rounded-2xl border border-brand-border bg-brand-dark p-4 transition hover:border-green-500/30">

                                        <div>

                                            <strong class="block">
                                                Draw #{{ $resultDraw->id }}
                                            </strong>

                                            <span class="mt-1 block text-xs opacity-50">

                                                {{ $resultDraw->completed_at?->format('d M Y h:i A') ?? '—' }}

                                                ·

                                                {{ $resultDraw->total_tickets }}
                                                tickets

                                            </span>

                                        </div>


                                        <div class="text-right">

                                            <span
                                                class="block text-sm font-semibold text-green-400">

                                                {{ $resultDraw->total_winners }}
                                                winner(s)

                                            </span>

                                            <span class="mt-1 block text-xs opacity-50">
                                                View Results →
                                            </span>

                                        </div>

                                    </a>

                                @endforeach

                            </div>

                        </div>

                    </div>

                @else

                    {{-- No Draws Yet --}}
                    <div
                        class="mt-8 rounded-3xl border border-brand-border bg-brand-surface p-6 md:p-8">

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

                <div
                    class="sticky top-6 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

                    <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                    <div class="p-5 md:p-6">

                        <h3 class="text-xl font-bold">
                            Buy Tickets
                        </h3>


                        @auth

                            @if ($lottery->isSelling() && $maxQuantity > 0)

                                <form
                                    method="POST"
                                    action="{{ route('lotteries.tickets.buy', $lottery) }}"
                                    class="mt-6">

                                    @csrf


                                    <div>

                                        <label
                                            for="quantity"
                                            class="mb-2 block text-sm font-medium">

                                            Number of Tickets

                                        </label>


                                        <input
                                            type="number"
                                            id="quantity"
                                            name="quantity"
                                            value="1"
                                            min="1"
                                            max="{{ $maxQuantity }}"
                                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500"
                                            required>


                                        <p class="mt-2 text-xs opacity-50">

                                            Maximum:
                                            {{ $maxQuantity }}
                                            ticket(s)

                                        </p>

                                    </div>


                                    {{-- Total --}}
                                    <div
                                        class="mt-6 rounded-2xl border border-green-500/10 bg-green-500/5 p-5">

                                        <div class="flex items-center justify-between">

                                            <span class="opacity-60">
                                                Total
                                            </span>

                                            <strong
                                                id="lottery-total"
                                                class="text-2xl text-green-500">

                                                {{ $lottery->currency }}
                                                {{ number_format((float) $lottery->ticket_price, 2) }}

                                            </strong>

                                        </div>

                                    </div>


                                    <button
                                        type="submit"
                                        class="mt-6 block w-full rounded-2xl bg-gradient-to-r from-green-500 to-orange-500 py-4 text-center font-semibold text-white shadow-lg shadow-green-500/20 transition duration-300 hover:scale-[1.02] hover:shadow-orange-500/30">

                                        <i class="fa-solid fa-ticket mr-2"></i>

                                        Buy Tickets

                                    </button>

                                </form>


                            @elseif ($lottery->isSelling())

                                <div
                                    class="mt-6 rounded-2xl border border-orange-500/20 bg-orange-500/10 p-5 text-center text-sm text-orange-400">

                                    You have reached your ticket purchase limit.

                                </div>


                            @else

                                <div
                                    class="mt-6 rounded-2xl border border-gray-500/20 bg-gray-500/10 p-5 text-center text-sm opacity-60">

                                    Ticket sales are currently closed.

                                </div>

                            @endif


                        @else

                            <div
                                class="mt-6 rounded-2xl border border-orange-500/20 bg-orange-500/10 p-5 text-center">

                                <p class="text-sm opacity-70">
                                    Login to purchase lottery tickets.
                                </p>


                                <a
                                    href="{{ route('auth.login') }}"
                                    class="mt-4 block rounded-xl bg-gradient-to-r from-green-500 to-orange-500 py-3 font-semibold text-white">

                                    Login

                                </a>

                            </div>

                        @endauth

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const quantityInput = document.getElementById('quantity');
    const totalElement = document.getElementById('lottery-total');

    if (!quantityInput || !totalElement) {
        return;
    }

    const ticketPrice = {{ (float) $lottery->ticket_price }};
    const currency = @json($lottery->currency);

    function updateTotal() {

        let quantity = parseInt(quantityInput.value) || 1;

        const max = parseInt(quantityInput.max);

        if (quantity < 1) {
            quantity = 1;
        }

        if (max && quantity > max) {
            quantity = max;
            quantityInput.value = max;
        }

        const total = (
            ticketPrice * quantity
        ).toFixed(2);

        totalElement.textContent =
            currency + ' ' + total;
    }

    quantityInput.addEventListener(
        'input',
        updateTotal
    );

    updateTotal();

});
</script>

@endpush