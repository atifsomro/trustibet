<div class="wallet">
    <div class="rounded-3xl border border-brand-border bg-brand-surface">
        <div class="flex flex-col p-3 md:p-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-brand-primary uppercase tracking-[3px]">Wallet Balance</p>
                <h2 class="mt-3">Rs. 12,500</h2>
                <p class="mt-2 opacity-70">Available balance ready to use.</p>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 grid-cols-2 lg:grid-cols-4">
        <div
            class="flex flex-col sm:items-start items-center sm:text-start text-center rounded-3xl border border-brand-border bg-brand-surface p-3 md:p-6">
            <i class="fa-solid fa-gift text-base md:text-3xl text-brand-primary"></i>
            <p class="mt-4 opacity-70">Bonus Balance</p>
            <span class="mt-2">Rs.500</span>
        </div>

        <div
            class="flex flex-col sm:items-start items-center sm:text-start text-center rounded-3xl border border-brand-border bg-brand-surface p-3 md:p-6">
            <i class="fa-solid fa-lock text-base md:text-3xl text-yellow-500"></i>
            <p class="mt-4 opacity-70">Locked Balance</p>
            <span class="mt-2">Rs.300</span>
        </div>

        <div
            class="flex flex-col sm:items-start items-center sm:text-start text-center rounded-3xl border border-brand-border bg-brand-surface p-3 md:p-6">
            <i class="fa-solid fa-arrow-down text-base md:text-3xl text-green-500"></i>
            <p class="mt-4 opacity-70">Total Deposited</p>
            <span class="mt-2">Rs.25,000</span>
        </div>

        <div
            class="flex flex-col sm:items-start items-center sm:text-start text-center rounded-3xl border border-brand-border bg-brand-surface p-3 md:p-6">
            <i class="fa-solid fa-arrow-up text-base md:text-3xl text-red-500"></i>
            <p class="mt-4 opacity-70">Withdrawals</p>
            <span class="mt-2">Rs.12,500</span>
        </div>
    </div>
    <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
        <div class="mb-6 flex items-center gap-3 sm:flex-row flex-col sm:text-start text-center">
            <i class="fa-solid fa-clock-rotate-left text-brand-primary text-2xl"></i>
            <div>
                <h3>Recent Wallet Activity</h3>
                <p class="opacity-70">Latest deposits, withdrawals and bonuses.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[500px] text-[12px] md:text-base lg:text-xl">
                <thead>
                    <tr class="border-b border-brand-border">
                        <th class="py-2 md:py-4 text-left">Type</th>
                        <th class="py-2 md:py-4 text-left">Amount</th>
                        <th class="py-2 md:py-4 text-left">Date</th>
                        <th class="py-2 md:py-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-brand-border">
                        <td class="py-2 md:py-4">Deposit</td>
                        <td class="py-2 md:py-4 text-green-500">+ Rs.5000</td>
                        <td class="py-2 md:py-4">02 Jul 2026</td>
                        <td class="py-2 md:py-4">
                            <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">
                                Completed
                            </span>
                        </td>
                    </tr>
                    <tr class="border-b border-brand-border">
                        <td class="py-2 md:py-4">Withdrawal</td>
                        <td class="py-2 md:py-4 text-red-500">- Rs.2000</td>
                        <td class="py-2 md:py-4">01 Jul 2026</td>
                        <td class="py-2 md:py-4">
                            <span class="rounded-full bg-yellow-500/20 px-3 py-1 text-yellow-500">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 md:py-4">Joining Bonus</td>
                        <td class="py-2 md:py-4 text-brand-primary">+ Rs.100</td>
                        <td class="py-2 md:py-4">30 Jun 2026</td>
                        <td class="py-2 md:py-4">
                            <span class="rounded-full bg-brand-primary/20 px-3 py-1 text-brand-primary">
                                Credited
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
