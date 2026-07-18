<div class="investment">
    {{-- Stats --}}
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col items-center justify-center gap-2">
                <i class="fa-solid fa-layer-group text-xl sm:text-4xl text-brand-primary"></i>
                <p class="opacity-70">Active Plans</p>
                <h3 class="mt-2">2</h3>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col items-center justify-center gap-2">
                <i class="fa-solid fa-wallet text-xl sm:text-4xl text-brand-primary"></i>
                <p class="opacity-70">Total Investment</p>
                <h3 class="mt-2 text-brand-primary">$5,000</h3>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col items-center justify-center gap-2">
                <i class="fa-solid fa-circle-check text-xl sm:text-4xl text-green-500"></i>
                <p class="opacity-70">Completed Plans</p>
                <h3 class="mt-2 text-green-500">3</h3>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col items-center justify-center gap-2">
                <i class="fa-solid fa-clock text-xl sm:text-4xl text-yellow-500"></i>
                <p class="opacity-70">Pending Plans</p>
                <h3 class="mt-2 text-yellow-500">1</h3>
            </div>
        </div>

    </div>

    {{-- Filters --}}
    <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
        <div class="grid gap-4 lg:grid-cols-4">
            <input type="text" placeholder="Search Plan"
                class="rounded-xl border border-brand-border bg-brand-dark px-2 sm:px-4 py-3 outline-none focus:border-brand-primary">
            <select
                class="rounded-xl border border-brand-border bg-brand-dark px-2 sm:px-4 py-3 outline-none focus:border-brand-primary">
                <option>All Plans</option>
                <option>Basic</option>
                <option>Premium</option>
                <option>Business</option>
            </select>

            <select
                class="rounded-xl border border-brand-border bg-brand-dark px-2 sm:px-4 py-3 outline-none focus:border-brand-primary">
                <option>All Status</option>
                <option>Active</option>
                <option>Completed</option>
                <option>Expired</option>
            </select>
            <input type="date"
                class="rounded-xl border border-brand-border bg-brand-dark px-2 sm:px-4 py-3 outline-none focus:border-brand-primary">
        </div>
    </div>
    {{-- Table --}}
    <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
        <div class="mb-8">
            <h3>
                Investment Plans
            </h3>
            <p class="mt-2 opacity-70">
                View and manage all your purchased plans.
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[950px] text-[12px] md:text-base lg:text-xl">
                <thead>
                    <tr class="border-b border-brand-border">
                        <th class="py-2 sm:py-4 text-left">Plan</th>
                        <th class="py-2 sm:py-4 text-left">Amount</th>
                        <th class="py-2 sm:py-4 text-left">Duration</th>
                        <th class="py-2 sm:py-4 text-left">Start Date</th>
                        <th class="py-2 sm:py-4 text-left">End Date</th>
                        <th class="py-2 sm:py-4 text-left">Status</th>
                        <th class="py-2 sm:py-4 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-brand-border">
                        <td class="py-3 sm:py-5">M30S</td>
                        <td class="py-3 sm:py-5">$30</td>
                        <td class="py-3 sm:py-5">5 Years</td>
                        <td class="py-3 sm:py-5">12 Jul 2026</td>
                        <td class="py-3 sm:py-5">12 Jul 2031</td>
                        <td class="py-3 sm:py-5">
                            <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">
                                Active
                            </span>
                        </td>
                        <td class="py-3 sm:py-5">
                            <button class="btn-primary text-[12px] sm:text-sm px-3 sm:px-5 py-2">
                                View
                            </button>
                        </td>
                    </tr>
                    <tr class="border-b border-brand-border">
                        <td class="py-3 sm:py-5">Business Pro</td>
                        <td class="py-3 sm:py-5">$500</td>
                        <td class="py-3 sm:py-5">5 Years</td>
                        <td class="py-3 sm:py-5">01 Jun 2026</td>
                        <td class="py-3 sm:py-5">01 Jun 2031</td>
                        <td class="py-3 sm:py-5">
                            <span class="rounded-full bg-brand-primary/20 px-3 py-1 text-brand-primary">
                                Running
                            </span>
                        </td>
                        <td class="py-3 sm:py-5">
                            <button class="btn-primary text-[12px] sm:text-sm px-3 sm:px-5 py-2">
                                View
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 sm:py-5">Starter</td>
                        <td class="py-3 sm:py-5">$100</td>
                        <td class="py-3 sm:py-5">3 Years</td>
                        <td class="py-3 sm:py-5">15 Jan 2023</td>
                        <td class="py-3 sm:py-5">15 Jan 2026</td>
                        <td class="py-3 sm:py-5">
                            <span class="rounded-full bg-gray-500/20 px-3 py-1 text-gray-400">
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
