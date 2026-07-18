<section class="py-10 md:py-16">
    <div class="container">
        <div class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
            <!-- Top Gradient -->
            <div class="relative z-10 grid items-center  gap-6 sm:gap-10 md:gap-14 lg:grid-cols-2 p-4 sm:p-8 lg:p-14">
                <!-- Left -->
                <div>
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-scrach-lock/20 bg-scrach-lock/10 px-4 py-2 text-sm font-medium text-scrach-lock">
                        🎟️ Limited Lucky Draw
                    </span>
                    <h2 class="mt-5">
                        Win Amazing Rewards
                        <span class="text-green-500">
                            With Just Rs.1
                        </span>
                    </h2>
                    <p class="mt-6 text-gray-400 leading-8">
                        Enter our exclusive Lucky Draw for only <strong class="text-white">Rs.1</strong>
                        and get a chance to win exciting prizes. Every ticket gives
                        you a fair opportunity to become the next lucky winner.
                    </p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-3">

                        <div
                            class="rounded-2xl border border-brand-border bg-brand-dark p-5 text-center transition hover:border-green-500">

                            <div
                                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-green-500/10">
                                <i class="fa-solid fa-ticket text-green-500"></i>
                            </div>

                            <h4 class="text-xl font-bold text-green-500">
                                Rs.1
                            </h4>

                            <p class="mt-1 text-sm text-gray-400">
                                Entry Fee
                            </p>

                        </div>

                        <div
                            class="rounded-2xl border border-brand-border bg-brand-dark p-5 text-center transition hover:border-brand-primary">

                            <div
                                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-primary/10">
                                <i class="fa-solid fa-users text-brand-primary"></i>
                            </div>

                            <h4 class="text-xl font-bold text-brand-primary">
                                28K+
                            </h4>

                            <p class="mt-1 text-sm text-gray-400">
                                Active Players
                            </p>

                        </div>

                        <div
                            class="rounded-2xl border border-brand-border bg-brand-dark p-5 text-center transition hover:border-scrach-lock">

                            <div
                                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-scrach-lock/10">
                                <i class="fa-solid fa-trophy text-scrach-lock"></i>
                            </div>

                            <h4 class="text-xl font-bold text-scrach-lock">
                                1 Winner
                            </h4>

                            <p class="mt-1 text-sm text-gray-400">
                                Every Draw
                            </p>

                        </div>

                    </div>
                    <a href="{{ route('participate') }}"
                        class="mt-4 md:mt-10 btn-green w-full sm:w-max">
                        <i class="fa-solid fa-ticket"></i>
                        Participate Now
                    </a>
                </div>
                <!-- Right -->
                <div class="relative flex justify-center">
                    <img src="{{ asset('images/draw/draw.png') }}"
                        class="relative z-10 mx-auto w-full max-w-md drop-shadow-[0_20px_50px_rgba(247,165,1,.25)] transition duration-500 hover:scale-105"
                        alt="Lucky Draw Prize">
                </div>
            </div>
        </div>
    </div>
</section>
