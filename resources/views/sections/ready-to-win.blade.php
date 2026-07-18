<section class="ready-win">
    <div class="container">
        <div
            class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface p-4 md:px-8 md:py-12 lg:px-16 lg:py-16">
            <!-- Background Glow -->
            <div class="hidden md:block absolute -top-24 -left-24 h-72 w-72 rounded-full bg-green-500/10 blur-3xl"></div>
            <div class="hidden md:block absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-scrach-lock/10 blur-3xl"></div>
            <!-- Top Gradient -->
            <div class="hidden md:block absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-green-500 via-brand-primary to-scrach-lock">
            </div>
            <div
                class="relative z-10 flex flex-col items-center justify-between gap-4 md:gap-10 text-center lg:flex-row lg:text-left">
                <div class="max-w-2xl">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2 text-sm font-medium text-green-400">
                        🚀 Join Thousands of Active Members
                    </span>
                    <h2 class="mt-5">
                        Start Your
                        <span class="text-green-500">
                            Earning Journey
                        </span>
                        Today
                    </h2>

                    <p class="mt-5 text-gray-400 leading-8">
                        Become part of a trusted platform where thousands of members
                        participate daily through secure transactions, transparent rewards,
                        and exciting earning opportunities.
                    </p>

                    <div class="mt-6 flex flex-wrap justify-center gap-3 lg:justify-start">

                        <span
                            class="rounded-full border border-brand-border bg-brand-dark px-4 py-2 text-sm text-gray-300">
                            🔒 Secure Platform
                        </span>

                        <span
                            class="rounded-full border border-brand-border bg-brand-dark px-4 py-2 text-sm text-gray-300">
                            💰 Daily Rewards
                        </span>

                        <span
                            class="rounded-full border border-brand-border bg-brand-dark px-4 py-2 text-sm text-gray-300">
                            ⚡ Fast Withdrawals
                        </span>

                    </div>

                </div>

                <div class="flex flex-col gap-4">

                    <a href="{{ route('register') }}" class="btn-green">
                        Create Free Account
                    </a>

                    <a href="{{ route('about') }}" class="btn-orange">
                        Learn More About Us
                    </a>

                </div>

            </div>

        </div>

    </div>
</section>
