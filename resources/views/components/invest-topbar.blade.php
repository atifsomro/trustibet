<section class="invest-topbar py-6">
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="opacity-70">Active Plan</p>
                    <span class="mt-2">{{ $activeInvestment?->package_name ?? 'None' }}</span>
                </div>
                <div class="w-10 h-10 rounded bg-brand-primary/10 flex items-center justify-center">
                    <i class="fa-solid fa-layer-group text-brand-primary text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="opacity-70">Days Left</p>
                    <span class="mt-2">
                        @if ($activeInvestment)
                            {{ max(0, $activeInvestment->total_days - $activeInvestment->daysElapsed()) }} days
                        @else
                            —
                        @endif
                    </span>
                </div>
                <div class="w-10 h-10 rounded bg-blue-500/10 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-days text-blue-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="opacity-70">Total Invested</p>
                    <span class="mt-2">${{ number_format($totalInvested ?? 0, 2) }}</span>
                </div>
                <div class="w-10 h-10 rounded bg-green-500/10 flex items-center justify-center">
                    <i class="fa-solid fa-wallet text-green-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="opacity-70">Withdrawable</p>
                    <span class="mt-2 text-brand-primary">
                        ${{ number_format((float) ($wallet->withdrawable_balance ?? 0), 2) }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded bg-yellow-500/10 flex items-center justify-center">
                    <i class="fa-solid fa-coins text-yellow-500 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</section>
