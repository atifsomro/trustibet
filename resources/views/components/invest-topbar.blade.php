<section class="invest-topbar py-6">
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Active Plan --}}
        <div
            class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-green-500 hover:shadow-[0_0_30px_rgba(34,197,94,.18)]">
            <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="opacity-70">Active Plan</p>
                        <span class="mt-2 font-semibold text-green-500"
                            data-invest-stat="active-plan">{{ $activeInvestment?->package_name ?? 'None' }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-layer-group text-green-500 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Days Left --}}
        <div
            class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-orange-500 hover:shadow-[0_0_30px_rgba(249,115,22,.18)]">
            <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="opacity-70">Days Left</p>
                        <span class="mt-2 font-semibold text-orange-400" data-invest-stat="days-left">
                            @if ($activeInvestment)
                                {{ max(0, $activeInvestment->total_days - $activeInvestment->daysElapsed()) }} days
                            @else
                                —
                            @endif
                        </span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-days text-orange-400 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Invested --}}
        <div
            class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-green-500 hover:shadow-[0_0_30px_rgba(34,197,94,.18)]">
            <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="opacity-70">Total Invested</p>
                        <span class="mt-2 font-semibold text-green-500"
                            data-invest-stat="total-invested">${{ number_format($totalInvested ?? 0, 2) }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-wallet text-green-500 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Withdrawable --}}
        <div
            class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-orange-500 hover:shadow-[0_0_30px_rgba(249,115,22,.18)]">
            <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="opacity-70">Withdrawable</p>
                        <span class="mt-2 font-semibold text-orange-400" data-invest-stat="withdrawable">
                            ${{ number_format((float) ($wallet->withdrawable_balance ?? 0), 2) }}
                        </span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-coins text-orange-400 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
