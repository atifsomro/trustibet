<section class="entries">
    <div class="container">
        {{-- Stats --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                <div  class="flex  flex-col items-center justify-between text-center">
                        <i class="fa-solid fa-ticket text-4xl text-brand-primary"></i>
                        <h3 class="mt-2">25</h3>
                        <p class="opacity-70">Total Entries</p>
                </div>
            </div>
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                <div  class="flex  flex-col items-center justify-between text-center">
                        <i class="fa-solid fa-spinner text-4xl text-yellow-500"></i>
                        <h3 class="mt-2">5</h3>
                        <p class="opacity-70">Active Entries</p>
                </div>
            </div>
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                <div  class="flex  flex-col items-center justify-between text-center">
                        <i class="fa-solid fa-trophy text-4xl text-green-500"></i>
                        <h3 class="mt-2">3</h3>
                        <p class="opacity-70">Won Games</p>
                </div>
            </div>
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                <div class="flex  flex-col items-center justify-between text-center">
                        <i class="fa-solid fa-circle-xmark text-4xl text-red-500"></i>
                        <h3 class="mt-2">17</h3>
                        <p class="opacity-70">Lost Games</p>
                </div>
            </div>
        </div>
        {{-- Entries Table --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-8">
                <div>
                    <h3>My Entries</h3>
                    <p class="mt-2 opacity-70">View all your participated games.</p>
                </div>
                <input type="text" placeholder="Search Ticket ID..."
                    class="rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px]">
                    <thead>
                        <tr class="border-b border-brand-border">
                            <th class="py-4 text-left">Game</th>
                            <th class="py-4 text-left">Prize</th>
                            <th class="py-4 text-left">Ticket ID</th>
                            <th class="py-4 text-left">Entry Fee</th>
                            <th class="py-4 text-left">Draw Date</th>
                            <th class="py-4 text-left">Status</th>
                            <th class="py-4 text-left">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr class="border-b border-brand-border">
                            <td class="py-5">One Rupee Game</td>
                            <td class="py-5">iPhone 16 Pro</td>
                            <td class="py-5">TB102541</td>
                            <td class="py-5">Rs.1</td>
                            <td class="py-5">15 Jul 2026</td>
                            <td class="py-5">
                                <span class="rounded-full bg-yellow-500/20 px-3 py-1 text-sm text-yellow-500">
                                    Active
                                </span>
                            </td>
                            <td class="py-5">
                                <button class="btn-primary text-sm px-5 py-2">
                                    View
                                </button>
                            </td>
                        </tr>

                        <tr class="border-b border-brand-border">
                            <td class="py-5">One Rupee Game</td>
                            <td class="py-5">Honda Bike</td>
                            <td class="py-5">TB102210</td>
                            <td class="py-5">Rs.1</td>
                            <td class="py-5">05 Jul 2026</td>
                            <td class="py-5">
                                <span class="rounded-full bg-green-500/20 px-3 py-1 text-sm text-green-500">
                                    Won
                                </span>
                            </td>
                            <td class="py-5">
                                <button class="btn-primary text-sm px-5 py-2">
                                    View
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td class="py-5">One Rupee Game</td>
                            <td class="py-5">AirPods Pro</td>
                            <td class="py-5">TB101985</td>
                            <td class="py-5">Rs.1</td>
                            <td class="py-5">28 Jun 2026</td>
                            <td class="py-5">
                                <span class="rounded-full bg-red-500/20 px-3 py-1 text-sm text-red-500">
                                    Lost
                                </span>
                            </td>
                            <td class="py-5">
                                <button class="btn-primary text-sm px-5 py-2">
                                    View
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Active Draw --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-8">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <small class="uppercase tracking-[3px] text-brand-primary">
                        Current Active Entry
                    </small>
                    <h3 class="mt-3">
                        iPhone 16 Pro Giveaway
                    </h3>
                    <p class="mt-2 opacity-70">
                        Your ticket <strong>#TB102541</strong> is successfully entered in this draw.
                    </p>
                </div>
                <div class="grid grid-cols-4 gap-4 text-center">
                    <div class="rounded-2xl bg-brand-dark p-4">
                        <h3>02</h3>
                        <small>Days</small>
                    </div>
                    <div class="rounded-2xl bg-brand-dark p-4">
                        <h3>12</h3>
                        <small>Hours</small>
                    </div>
                    <div class="rounded-2xl bg-brand-dark p-4">
                        <h3>45</h3>
                        <small>Minutes</small>
                    </div>
                    <div class="rounded-2xl bg-brand-dark p-4">
                        <h3>10</h3>
                        <small>Seconds</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
