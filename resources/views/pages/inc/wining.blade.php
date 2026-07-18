<section class="winnings">
    <div class="container">
        {{-- Stats --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-sack-dollar text-4xl text-green-500"></i>
                    <h3 class="mt-2">Rs. 58,500</h3>
                    <p class="opacity-70">Total Winnings</p>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-hourglass-half text-4xl text-yellow-500"></i>
                    <h3 class="mt-2">Rs. 5,000</h3>
                    <p class="opacity-70">Pending Claim</p>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-gift text-4xl text-brand-primary"></i>
                    <h3 class="mt-2">18</h3>
                    <p class="opacity-70">Rewards Won</p>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                <div class="flex flex-col text-center items-center justify-between">
                    <i class="fa-solid fa-trophy text-4xl text-orange-500"></i>
                    <h3 class="mt-2">Honda Bike</h3>
                    <p class="opacity-70">Biggest Prize</p>
                </div>
            </div>
        </div>
        {{-- Latest Winning --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
            <div class="grid items-center gap-8 lg:grid-cols-2">
                <div>
                    <small class="uppercase tracking-[3px] text-brand-primary">
                        Latest Winning
                    </small>
                    <h3 class="mt-4">
                        🎉 Congratulations!
                    </h3>
                    <h4 class="mt-4">
                        You Won Honda CG125
                    </h4>
                    <p class="mt-4 opacity-70">
                        Your ticket <strong>TB102210</strong> has been selected as the winning ticket.
                        Our team will contact you shortly for prize verification.
                    </p>
                    <button class="btn-primary mt-8">
                        View Prize Details
                    </button>
                </div>
                <div class="text-center">
                    <img src="{{ asset('images/draw/prize.png') }}" alt="Prize" class="mx-auto w-full sm:max-w-sm">
                </div>
            </div>
        </div>
        {{-- Prize Progress --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">

            <div class="flex items-center gap-3 mb-6">
                <i class="fa-solid fa-truck-fast text-2xl text-brand-primary"></i>
                <div>
                    <h3>Prize Delivery Progress</h3>
                    <p class="opacity-70">Track your latest winning.</p>
                </div>
            </div>

            <div class="w-full h-3 rounded-full bg-brand-dark overflow-hidden">
                <div class="h-full w-4/5 rounded-full bg-brand-primary"></div>
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-4">

                <div class="text-center">
                    <i class="fa-solid fa-circle-check text-3xl text-green-500"></i>
                    <p class="mt-3">Winner Confirmed</p>
                </div>

                <div class="text-center">
                    <i class="fa-solid fa-circle-check text-3xl text-green-500"></i>
                    <p class="mt-3">Documents Verified</p>
                </div>

                <div class="text-center">
                    <i class="fa-solid fa-box text-3xl text-yellow-500"></i>
                    <p class="mt-3">Shipping</p>
                </div>

                <div class="text-center">
                    <i class="fa-solid fa-house text-3xl opacity-40"></i>
                    <p class="mt-3 opacity-50">Delivered</p>
                </div>

            </div>

        </div>

        {{-- Winning History --}}
        <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
            <div class="mb-8">
                <h3>Winning History</h3>
                <p class="mt-2 opacity-70">
                    All your previous winnings.
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[950px] text-[12px] md:text-base lg:text-xl">
                    <thead>
                        <tr class="border-b border-brand-border">
                            <th class="py-2 sm:py-4 text-left">Prize</th>
                            <th class="py-2 sm:py-4 text-left">Game</th>
                            <th class="py-2 sm:py-4 text-left">Winning Date</th>
                            <th class="py-2 sm:py-4 text-left">Prize Value</th>
                            <th class="py-2 sm:py-4 text-left">Status</th>
                            <th class="py-2 sm:py-4 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr class="border-b border-brand-border">
                            <td class="py-3 sm:py-5">Honda CG125</td>
                            <td class="py-3 sm:py-5">One Rupee Game</td>
                            <td class="py-3 sm:py-5">12 Jul 2026</td>
                            <td class="py-3 sm:py-5 text-green-500">Rs.250,000</td>
                            <td class="py-3 sm:py-5">
                                <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">
                                    Delivered
                                </span>
                            </td>
                            <td class="py-3 sm:py-5">
                                <button class="btn-primary px-3 sm:px-5 py-2 text-[12px] sm:text-sm">
                                    View
                                </button>
                            </td>
                        </tr>

                        <tr class="border-b border-brand-border">
                            <td class="py-3 sm:py-5">AirPods Pro</td>
                            <td class="py-3 sm:py-5">One Rupee Game</td>
                            <td class="py-3 sm:py-5">05 Jul 2026</td>
                            <td class="py-3 sm:py-5 text-green-500">Rs.65,000</td>
                            <td class="py-3 sm:py-5">
                                <span class="rounded-full bg-yellow-500/20 px-3 py-1 text-yellow-500">
                                    Processing
                                </span>
                            </td>
                            <td class="py-3 sm:py-5">
                                <button class="btn-primary px-3 sm:px-5 py-2 text-[12px] sm:text-sm">
                                    View
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td class="py-3 sm:py-5">Rs.500 Cash</td>
                            <td class="py-3 sm:py-5">Daily Spin</td>
                            <td class="py-3 sm:py-5">28 Jun 2026</td>
                            <td class="py-3 sm:py-5 text-green-500">Rs.500</td>
                            <td class="py-3 sm:py-5">
                                <span class="rounded-full bg-brand-primary/20 px-3 py-1 text-brand-primary">
                                    Credited
                                </span>
                            </td>
                            <td class="py-3 sm:py-5">
                                <button class="btn-primary px-3 sm:px-5 py-2 text-[12px] sm:text-sm">
                                    View
                                </button>
                            </td>
                        </tr>

                    </tbody>

                </table>
            </div>
        </div>
    </div>
</section>
