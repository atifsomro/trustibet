@php
    $userId = auth()->id();
    $maxQuantity = $lottery->maxPurchaseQuantityForUser($userId);
    $canBuy = auth()->check() && $lottery->isSalesOpen() && $maxQuantity > 0;
    $remainingBalance = (float) ($remainingBalance ?? (auth()->user()?->wallet?->withdrawable_balance ?? 0));
    $ticketPrice = (float) $lottery->ticket_price;
    $canAfford = $remainingBalance >= $ticketPrice;
@endphp

@if ($canBuy && $canAfford)
    <form
        method="POST"
        action="{{ route('lotteries.tickets.buy', $lottery) }}"
        class="mt-2"
    >
        @csrf

        <button
            type="submit"
            class="block w-full rounded-2xl bg-gradient-to-r from-green-500 to-orange-500 py-2 text-center font-semibold text-white shadow-lg shadow-green-500/20 transition duration-300 hover:scale-[1.02] hover:shadow-orange-500/30"
        >
            <i class="fa-solid fa-ticket mr-2"></i>
            Buy Ticket
        </button>
    </form>
@elseif ($canBuy && !$canAfford)
    <div class="mt-2 rounded-2xl border border-orange-500/20 bg-orange-500/10 px-2 py-2 text-center text-sm font-semibold text-orange-400">
        Insufficient remaining balance
    </div>
    <a
        href="{{ route('deposits.index') }}"
        class="mt-2 block text-center text-xs font-semibold text-green-400 hover:underline"
    >
        Deposit funds to buy a ticket
    </a>
@elseif (auth()->check() && $lottery->isSalesOpen() && $maxQuantity <= 0)
    <div class="mt-2 rounded-2xl border border-orange-500/20 bg-orange-500/10 py-4 text-center text-sm font-semibold text-orange-400">
        Ticket limit reached
    </div>
@elseif (!auth()->check() && $lottery->isSalesOpen())
    <a
        href="{{ route('auth.login') }}"
        class="mt-2 block w-full rounded-2xl bg-gradient-to-r from-green-500 to-orange-500 py-4 text-center font-semibold text-white"
    >
        Login to Buy
    </a>
@else
    <div class="mt-2 block w-full rounded-2xl border border-gray-500/20 bg-gray-500/10 py-2 text-center text-sm font-semibold opacity-60">
        Sales Closed
    </div>
@endif
