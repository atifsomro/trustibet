<section class="wallet">
    <div class="container">

        <div class="rounded-3xl border border-brand-border bg-brand-surface">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                <div>
                    <p class="text-brand-primary uppercase tracking-[3px]">Wallet Balance</p>
                    <h2 class="mt-3">Rs. 12,500</h2>
                    <p class="mt-2 opacity-70">Available balance ready to use.</p>
                </div>

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('deposit') }}" class="btn-primary">
                        <i class="fa-solid fa-wallet mr-2"></i>
                        Deposit
                    </a>

                    <a href="{{ route('withdraw') }}" class="btn-secondary">
                        <i class="fa-solid fa-money-bill-wave mr-2"></i>
                        Withdraw
                    </a>
                </div>

            </div>
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                <i class="fa-solid fa-gift text-3xl text-brand-primary"></i>
                <p class="mt-4 opacity-70">Bonus Balance</p>
                <h3 class="mt-2">Rs.500</h3>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                <i class="fa-solid fa-lock text-3xl text-yellow-500"></i>
                <p class="mt-4 opacity-70">Locked Balance</p>
                <h3 class="mt-2">Rs.300</h3>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                <i class="fa-solid fa-arrow-down text-3xl text-green-500"></i>
                <p class="mt-4 opacity-70">Total Deposited</p>
                <h3 class="mt-2">Rs.25,000</h3>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                <i class="fa-solid fa-arrow-up text-3xl text-red-500"></i>
                <p class="mt-4 opacity-70">Total Withdrawn</p>
                <h3 class="mt-2">Rs.12,500</h3>
            </div>

        </div>

        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-8">

            <div class="mb-6 flex items-center gap-3">
                <i class="fa-solid fa-clock-rotate-left text-brand-primary text-2xl"></i>
                <div>
                    <h3>Recent Wallet Activity</h3>
                    <p class="opacity-70">Latest deposits, withdrawals and bonuses.</p>
                </div>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>
                        <tr class="border-b border-brand-border">
                            <th class="py-4 text-left">Type</th>
                            <th class="py-4 text-left">Amount</th>
                            <th class="py-4 text-left">Date</th>
                            <th class="py-4 text-left">Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr class="border-b border-brand-border">
                            <td class="py-4">Deposit</td>
                            <td class="py-4 text-green-500">+ Rs.5000</td>
                            <td class="py-4">02 Jul 2026</td>
                            <td class="py-4">
                                <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">
                                    Completed
                                </span>
                            </td>
                        </tr>

                        <tr class="border-b border-brand-border">
                            <td class="py-4">Withdrawal</td>
                            <td class="py-4 text-red-500">- Rs.2000</td>
                            <td class="py-4">01 Jul 2026</td>
                            <td class="py-4">
                                <span class="rounded-full bg-yellow-500/20 px-3 py-1 text-yellow-500">
                                    Pending
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td class="py-4">Joining Bonus</td>
                            <td class="py-4 text-brand-primary">+ Rs.100</td>
                            <td class="py-4">30 Jun 2026</td>
                            <td class="py-4">
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
</section>
