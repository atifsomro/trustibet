@php
    $remainingBalance = (float) ($remainingBalance ?? (auth()->user()?->wallet?->withdrawable_balance ?? 0));
@endphp

<h3 class="text-xl font-bold">
    Buy Tickets
</h3>

@include('lottery.partials.buy-ticket-form', [
    'lottery' => $lottery,
    'remainingBalance' => $remainingBalance,
])
