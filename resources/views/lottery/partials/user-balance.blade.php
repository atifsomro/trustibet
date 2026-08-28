@auth
    @php
        $balanceUser = auth('web')->user();
        $balanceWallet = $balanceUser?->wallet;
        $usedBalance = (float) \App\Models\LotteryTicket::query()
            ->where('user_id', $balanceUser->id)
            ->whereNotIn('status', ['refunded', 'cancelled'])
            ->sum('price');
        $remainingBalance = (float) ($balanceWallet?->withdrawable_balance ?? 0);
        $walletCurrency = $balanceWallet?->currency ?? 'USD';
    @endphp

    <style>
        .lottery-balance-bar {
            position: fixed;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 60;
            width: 176px;
        }

        @media (max-width: 767px) {
            .lottery-balance-bar {
                left: 12px;
                right: 72px;
                top: auto;
                bottom: 80px;
                transform: none;
                width: auto;
            }
        }
    </style>

    <aside class="lottery-balance-bar" aria-label="Used and remaining balance">
        <div class="overflow-hidden rounded-2xl border border-green-500/40 bg-[#0b1220] shadow-[0_0_24px_rgba(34,197,94,.25)]">
            <div class="h-1 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

            <div class="grid grid-cols-2 md:grid-cols-1">
                <div class="border-r border-brand-border px-4 py-3 md:border-r-0 md:border-b">
                    <span class="block text-[10px] uppercase tracking-wider opacity-50">Used</span>
                    <strong class="mt-1 block text-base text-orange-400 md:text-lg">
                        {{ $walletCurrency }} {{ number_format($usedBalance, 2) }}
                    </strong>
                </div>
                <div class="px-4 py-3">
                    <span class="block text-[10px] uppercase tracking-wider opacity-50">Remaining</span>
                    <strong class="mt-1 block text-base text-green-400 md:text-lg">
                        {{ $walletCurrency }} {{ number_format($remainingBalance, 2) }}
                    </strong>
                </div>
            </div>
        </div>
    </aside>
@endauth
