@php
    $remainingBalance = (float) ($remainingBalance ?? (auth()->user()?->wallet?->withdrawable_balance ?? 0));
@endphp

<h3 class="text-xl font-bold">
    Buy Tickets
</h3>

<div data-lottery-buy-slot="{{ $lottery->id }}">
    @include('lottery.partials.buy-ticket-form', [
        'lottery' => $lottery,
        'remainingBalance' => $remainingBalance,
    ])
</div>
