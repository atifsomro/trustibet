@php
    $remainingTickets = $lottery->remainingTickets();
    $endsAt = $lottery->ends_at ?? $lottery->sales_end_at;
    $isOpen = $lottery->isSalesOpen();
    $maxPerUser = $lottery->max_tickets_per_user;
    $userTickets = auth()->check() ? $lottery->ticketsForCurrentRoundUser(auth()->id()) : 0;
@endphp

<div data-lottery-shell="{{ $lottery->id }}"
    class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-2 hover:border-green-500 hover:shadow-[0_0_35px_rgba(34,197,94,.18)]">

    <div
        class="absolute inset-0 opacity-0 duration-300 group-hover:opacity-100 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
    </div>

    <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

    <div class="relative z-10 text-center p-2 md:p-4">

        <div class="flex justify-between">
            <div
                class="flex h-8 md:h-12 w-8 md:w-12 items-center justify-center rounded border border-green-500/20 bg-gradient-to-br from-green-500/20 to-orange-500/20 transition duration-300 group-hover:scale-110 group-hover:rotate-6">
                <i class="fa-solid fa-ticket text-sm md:text-base text-green-500"></i>
            </div>

            <h3 class="mt-2 text-sm md:text-base font-bold">
                {{ $lottery->title }}
            </h3>
        </div>

        <div class="mt-2 flex items-end justify-center gap-2">
            <span class="text-xl font-extrabold text-green-500 drop-shadow-[0_0_10px_rgba(34,197,94,.4)]">
                {{ $lottery->currency }}
                {{ number_format((float) $lottery->ticket_price, 2) }}
            </span>
            <span class="text-sm opacity-60">
                / Ticket
            </span>
        </div>

        @if ($endsAt && !$lottery->isCompleted() && !$lottery->isCancelled())
            <div
                class="mx-auto w-full mt-2 inline-flex items-center justify-center gap-3 rounded-2xl border border-orange-500/20 bg-orange-500/5 px-2 py-2">
                <i class="fa-regular fa-clock text-orange-400"></i>
                <span class="lottery-countdown font-mono text-base font-bold tracking-[3px] text-orange-400"
                    data-end="{{ $endsAt->toIso8601String() }}" data-server-now="{{ now()->toIso8601String() }}"
                    data-draw-url="{{ route('lotteries.draw-due', $lottery) }}">
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

    <div class="relative z-10 p-2 md:p-4">

        <div class="rounded-2xl border border-green-500/10 bg-gradient-to-b from-brand-dark to-[#07131b] p-5">

            <div class="mb-4 flex items-center justify-between">
                <span class="text-sm font-semibold opacity-70">
                    PRIZES
                </span>
                <i class="fa-solid fa-trophy text-orange-400"></i>
            </div>

            <ul class="space-y-2">
                @foreach ([
        '1st Prize' => $lottery->first_prize,
        '2nd Prize' => $lottery->second_prize,
        '3rd Prize' => $lottery->third_prize,
        '4th Prize' => $lottery->fourth_prize,
        '5th Prize' => $lottery->fifth_prize,
    ] as $label => $amount)
                    <li class="flex items-center justify-between">
                        <span class="text-xs opacity-70">{{ $label }}</span>
                        <span
                            class="text-xs p-1 rounded-full border border-orange-500/20 text-sm font-semibold text-orange-400">
                            {{ $lottery->currency }}
                            {{ number_format((float) $amount, 2) }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>

        @php
            $roundNumber = $lottery->currentRoundNumber();
        @endphp

        @if ($lottery->hasPreviousRounds())
            <div class="mt-2 rounded-2xl border border-green-500/20 bg-green-500/5 px-2 py-2 text-center">
                <span class="block text-xs font-semibold uppercase tracking-[3px] text-green-400">
                    New Round
                </span>
                <span class="mt-1 block text-xs">
                    Round {{ $roundNumber }} is live
                </span>
            </div>
        @endif

        <div class="mt-2 flex items-center justify-between opacity-60">
            <span class="text-xs">
                <i class="fa-solid fa-ticket mr-1"></i>
                @if ($remainingTickets === null)
                    Unlimited Tickets
                @else
                    {{ number_format($remainingTickets) }}
                    Tickets Remaining
                @endif
            </span>

            @auth
                <span class="text-xs">
                    @if ($maxPerUser === null)
                        {{ $userTickets }} purchased
                    @else
                        {{ $userTickets }}/{{ $maxPerUser }} Purchased
                    @endif
                </span>
            @endauth
        </div>

        @include('lottery.partials.buy-ticket-form', ['lottery' => $lottery])

        <div class="mt-2 flex items-center justify-center gap-2 opacity-50">
            <a href="{{ route('lotteries.show', $lottery) }}"
                class="text-sm transition hover:text-green-500 hover:opacity-100">
                View details
            </a>

            @if ($lottery->hasPreviousRounds())
                <span>·</span>
                <a href="{{ route('lotteries.results', $lottery) }}"
                    class="text-sm transition hover:text-green-500 hover:opacity-100">
                    View results
                </a>
            @endif
        </div>

    </div>
</div>
