@php
    $remainingTickets = $lottery->remainingTickets();

    $salesEnd = $lottery->sales_end_at;

    $isSelling = $lottery->isSelling();

    $maxPerUser = (int) config(
        'lottery.max_tickets_per_user',
        10
    );

    $userTickets = auth()->check()
        ? $lottery->ticketsForCurrentRoundUser(auth()->id())
        : 0;

    $remainingUserTickets = max(
        0,
        $maxPerUser - $userTickets
    );

    $maxPurchase = $remainingUserTickets;

    if ($remainingTickets !== null) {
        $maxPurchase = min(
            $maxPurchase,
            $remainingTickets
        );
    }
@endphp

<div
    class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-2 hover:border-green-500 hover:shadow-[0_0_35px_rgba(34,197,94,.18)]">

    {{-- Hover Background --}}
    <div
        class="absolute inset-0 opacity-0 duration-300 group-hover:opacity-100 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
    </div>

    {{-- Top Gradient --}}
    <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

    <div class="relative z-10 p-4 text-center md:p-8">

        {{-- Ticket Icon --}}
        <div
            class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl border border-green-500/20 bg-gradient-to-br from-green-500/20 to-orange-500/20 transition duration-300 group-hover:scale-110 group-hover:rotate-6">

            <i class="fa-solid fa-ticket text-3xl text-green-500"></i>

        </div>

        {{-- Lottery Title --}}
        <h3 class="mt-6 text-2xl font-bold">
            {{ $lottery->title }}
        </h3>

        {{-- Ticket Price --}}
        <div class="mt-3 flex items-end justify-center gap-2">

            <h2 class="text-green-500 drop-shadow-[0_0_10px_rgba(34,197,94,.4)]">
                {{ $lottery->currency }}
                {{ number_format((float) $lottery->ticket_price, 2) }}
            </h2>

            <span class="mb-1 opacity-60">
                / Ticket
            </span>

        </div>

        {{-- Countdown --}}
        @if ($isSelling && $salesEnd)

            <div
                class="mx-auto mt-6 inline-flex items-center gap-3 rounded-2xl border border-orange-500/20 bg-orange-500/5 px-6 py-3">

                <i class="fa-regular fa-clock text-orange-400"></i>

                <span
                    class="lottery-countdown font-mono text-xl font-bold tracking-[3px] text-orange-400"
                    data-end="{{ $salesEnd->toIso8601String() }}">

                    --:--:--

                </span>

            </div>

        @else

            <div
                class="mx-auto mt-6 inline-flex items-center gap-3 rounded-2xl border border-gray-500/20 bg-gray-500/5 px-6 py-3">

                <i class="fa-regular fa-clock opacity-60"></i>

                <span class="text-sm opacity-60">
                    Sales Closed
                </span>

            </div>

        @endif

    </div>

    {{-- Prizes --}}
    <div class="relative z-10 px-4 pb-4 md:px-8 md:pb-8">

        <div
            class="rounded-2xl border border-green-500/10 bg-gradient-to-b from-brand-dark to-[#07131b] p-5">

            <div class="mb-4 flex items-center justify-between">

                <span class="text-sm font-semibold opacity-70">
                    PRIZES
                </span>

                <i class="fa-solid fa-trophy text-orange-400"></i>

            </div>

            <ul class="space-y-4">

                {{-- First Prize --}}
                <li class="flex items-center justify-between">

                    <span class="text-sm opacity-70">
                        1st Prize
                    </span>

                    <span
                        class="rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-sm font-semibold text-orange-400">

                        {{ $lottery->currency }}
                        {{ number_format((float) $lottery->first_prize, 2) }}

                    </span>

                </li>

                {{-- Second Prize --}}
                <li class="flex items-center justify-between">

                    <span class="text-sm opacity-70">
                        2nd Prize
                    </span>

                    <span
                        class="rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-sm font-semibold text-orange-400">

                        {{ $lottery->currency }}
                        {{ number_format((float) $lottery->second_prize, 2) }}

                    </span>

                </li>

                {{-- Third Prize --}}
                <li class="flex items-center justify-between">

                    <span class="text-sm opacity-70">
                        3rd Prize
                    </span>

                    <span
                        class="rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-sm font-semibold text-orange-400">

                        {{ $lottery->currency }}
                        {{ number_format((float) $lottery->third_prize, 2) }}

                    </span>

                </li>

                {{-- Fourth Prize --}}
                <li class="flex items-center justify-between">

                    <span class="text-sm opacity-70">
                        4th Prize
                    </span>

                    <span
                        class="rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-sm font-semibold text-orange-400">

                        {{ $lottery->currency }}
                        {{ number_format((float) $lottery->fourth_prize, 2) }}

                    </span>

                </li>

                {{-- Fifth Prize --}}
                <li class="flex items-center justify-between">

                    <span class="text-sm opacity-70">
                        5th Prize
                    </span>

                    <span
                        class="rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-sm font-semibold text-orange-400">

                        {{ $lottery->currency }}
                        {{ number_format((float) $lottery->fifth_prize, 2) }}

                    </span>

                </li>

            </ul>

        </div>

        {{-- Ticket Limit --}}
        <div class="mt-4 flex items-center justify-between text-xs opacity-60">

            <span>

                <i class="fa-solid fa-ticket mr-1"></i>

                @if ($remainingTickets === null)

                    Unlimited Tickets

                @else

                    {{ number_format($remainingTickets) }}
                    Tickets Remaining

                @endif

            </span>

            @auth

                <span>
                    {{ $userTickets }}/{{ $maxPerUser }} Purchased
                </span>

            @endauth

        </div>

        {{-- Action --}}
        @if ($isSelling && $maxPurchase > 0)

            <a
                href="{{ route('lotteries.show', $lottery) }}"
                class="mt-6 block w-full rounded-2xl bg-gradient-to-r from-green-500 to-orange-500 py-4 text-center font-semibold text-white shadow-lg shadow-green-500/20 transition duration-300 hover:scale-[1.02] hover:shadow-orange-500/30">

                <i class="fa-solid fa-ticket mr-2"></i>

                Buy Ticket

            </a>

        @elseif ($isSelling && auth()->check() && $maxPurchase <= 0)

            <div
                class="mt-6 block w-full rounded-2xl border border-orange-500/20 bg-orange-500/10 py-4 text-center text-sm font-semibold text-orange-400">

                Ticket Limit Reached

            </div>

        @else

            <div
                class="mt-6 block w-full rounded-2xl border border-gray-500/20 bg-gray-500/10 py-4 text-center text-sm font-semibold opacity-60">

                Sales Closed

            </div>

        @endif

        {{-- Sales Closing --}}
        @if ($isSelling && $salesEnd)

            <p class="mt-4 text-center text-xs font-semibold uppercase tracking-[3px] text-orange-400">

                Sales close on
                {{ $salesEnd->format('d M Y h:i A') }}

            </p>

        @endif

    </div>
</div>