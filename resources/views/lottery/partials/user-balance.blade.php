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

    <div
        class="mx-auto mb-8 max-w-3xl overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-[0_0_28px_rgba(34,197,94,.12)]"
        aria-label="Used and remaining balance"
    >
        <div class="h-1 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

        <div class="relative grid grid-cols-2">
            {{-- <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(249,115,22,.08),transparent_42%),radial-gradient(circle_at_bottom_right,rgba(34,197,94,.08),transparent_42%)]">
            </div> --}}

            <div class="flex items-center gap-4 p-2 sm:p-4">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-orange-500/25 bg-orange-500/10">
                    <i class="fa-solid fa-ticket text-sm text-orange-400"></i>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-orange-400/80">
                        Used on tickets
                    </span>
                    <strong class="mt-1 block text-base font-bold text-orange-400" data-lottery-balance="used">
                        {{ $walletCurrency }} {{ number_format($usedBalance, 2) }}
                    </strong>
                </div>
            </div>

            <div class="flex items-center gap-4 p-2 sm:p-4">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-green-500/25 bg-green-500/10">
                    <i class="fa-solid fa-wallet text-sm text-green-400"></i>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-green-400/80">
                        Remaining balance
                    </span>
                    <strong class="mt-1 block text-sm font-bold text-green-400" data-lottery-balance="remaining">
                        {{ $walletCurrency }} {{ number_format($remainingBalance, 2) }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
@endauth
