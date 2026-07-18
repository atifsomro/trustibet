<div class="transactions py-6">
        {{-- Stats --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col gap-2 items-center justify-center">
                    <i class="fa-solid fa-receipt text-xl md:text-4xl text-brand-primary"></i>
                    <p class="opacity-70">Total Transactions</p>
                    <h3 class="mt-2">248</h3>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col gap-2 items-center justify-center">
                    <i class="fa-solid fa-arrow-down text-xl md:text-4xl text-green-500"></i>
                    <p class="opacity-70">Total Deposits</p>
                    <h3 class="mt-2 text-green-500">Rs.52,000</h3>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col gap-2 items-center justify-center">
                    <i class="fa-solid fa-arrow-up text-xl md:text-4xl text-red-500"></i>
                    <p class="opacity-70">Total Withdrawals</p>
                    <h3 class="mt-2 text-red-500">Rs.24,500</h3>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col gap-2 items-center justify-center">
                    <i class="fa-solid fa-clock text-xl md:text-4xl text-yellow-500"></i>
                    <p class="opacity-70">Pending</p>
                    <h3 class="mt-2 text-yellow-500">3</h3>
                </div>
            </div>

        </div>
        {{-- Filters --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="grid gap-4 lg:grid-cols-4">
                <input type="text" placeholder="Search Transaction ID"
                    class="rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                <select
                    class="rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                    <option>All Types</option>
                    <option>Deposit</option>
                    <option>Withdraw</option>
                    <option>Bonus</option>
                    <option>Entry Fee</option>
                </select>
                <select
                    class="rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                    <option>All Status</option>
                    <option>Completed</option>
                    <option>Pending</option>
                    <option>Failed</option>
                </select>
                <input type="date"
                    class="rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
            </div>
        </div>
        {{-- Table --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
            <div class="mb-8">
                <h3>Transaction History</h3>
                <p class="mt-2 opacity-70">
                    View all your wallet activities.
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[950px] text-[12px] md:text-base lg:text-xl">
                    <thead>
                        <tr class="border-b border-brand-border">
                            <th class="py-2 sm:py-4 text-left">Transaction ID</th>
                            <th class="py-2 sm:py-4 text-left">Type</th>
                            <th class="py-2 sm:py-4 text-left">Amount</th>
                            <th class="py-2 sm:py-4 text-left">Method</th>
                            <th class="py-2 sm:py-4 text-left">Date</th>
                            <th class="py-2 sm:py-4 text-left">Status</th>
                            <th class="py-2 sm:py-4 text-left">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr class="border-b border-brand-border">
                            <td class="py-3 sm:py-5">TX982145</td>
                            <td class="py-3 sm:py-5">Deposit</td>
                            <td class="py-3 sm:py-5 text-green-500">+Rs.5,000</td>
                            <td class="py-3 sm:py-5">EasyPaisa</td>
                            <td class="py-3 sm:py-5">12 Jul 2026</td>
                            <td class="py-3 sm:py-5">
                                <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">
                                    Completed
                                </span>
                            </td>
                            <td class="py-3 sm:py-5">
                                <button class="btn-primary text-[12px] sm:text-sm px-3 sm:px-5 py-2">
                                    Details
                                </button>
                            </td>
                        </tr>

                        <tr class="border-b border-brand-border">
                            <td class="py-3 sm:py-5">TX982102</td>
                            <td class="py-3 sm:py-5">Withdraw</td>
                            <td class="py-3 sm:py-5 text-red-500">-Rs.2,500</td>
                            <td class="py-3 sm:py-5">JazzCash</td>
                            <td class="py-3 sm:py-5">10 Jul 2026</td>
                            <td class="py-3 sm:py-5">
                                <span class="rounded-full bg-yellow-500/20 px-3 py-1 text-yellow-500">
                                    Pending
                                </span>
                            </td>
                            <td class="py-3 sm:py-5">
                                <button class="btn-primary text-[12px] sm:text-sm px-3 sm:px-5 py-2">
                                    Details
                                </button>
                            </td>
                        </tr>

                        <tr class="border-b border-brand-border">
                            <td class="py-3 sm:py-5">TX981888</td>
                            <td class="py-3 sm:py-5">Bonus</td>
                            <td class="py-3 sm:py-5 text-brand-primary">+Rs.500</td>
                            <td class="py-3 sm:py-5">Joining Bonus</td>
                            <td class="py-3 sm:py-5">08 Jul 2026</td>
                            <td class="py-3 sm:py-5">
                                <span class="rounded-full bg-brand-primary/20 px-3 py-1 text-brand-primary">
                                    Credited
                                </span>
                            </td>
                            <td class="py-3 sm:py-5">
                                <button class="btn-primary text-[12px] sm:text-sm px-3 sm:px-5 py-2">
                                    Details
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td class="py-3 sm:py-5">TX981500</td>
                            <td class="py-3 sm:py-5">Entry Fee</td>
                            <td class="py-3 sm:py-5 text-red-500">-Rs.1</td>
                            <td class="py-3 sm:py-5">One Rupee Game</td>
                            <td class="py-3 sm:py-5">05 Jul 2026</td>
                            <td class="py-3 sm:py-5">
                                <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">
                                    Completed
                                </span>
                            </td>
                            <td class="py-3 sm:py-5">
                                <button class="btn-primary text-[12px] sm:text-sm px-3 sm:px-5 py-2">
                                    Details
                                </button>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>
</div>
