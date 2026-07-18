<section class="featured-games">
    <div class="container">
        <div class="mb-10 text-center">
            <span
                class="inline-flex items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2 text-sm font-medium text-green-500">
                <i class="fa-solid fa-gamepad"></i>
                Featured Games
            </span>

            <h2 class="mt-5">
                Choose Your Favorite Game
            </h2>

            <p class="mx-auto mt-3 max-w-2xl text-gray-400">
                Play exciting games, test your luck, and unlock rewarding opportunities with our most popular
                collection.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-6 md:grid-cols-3 xl:grid-cols-4">

            {{-- Scratch Card --}}
            <div
                class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-2 hover:border-green-500 hover:shadow-[0_0_35px_rgba(34,197,94,.18)]">

                <div
                    class="absolute inset-0 opacity-0 transition duration-300 group-hover:opacity-100 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
                </div>

                <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="relative flex h-60 items-center justify-center bg-brand-dark p-2 sm:p-6">
                    <img src="{{ asset('images/games/scratch.png') }}" alt="Scratch Card"
                        class="max-h-full w-full object-contain transition duration-500 group-hover:scale-110">

                    <span
                        class="absolute left-0 top-0 rounded-full bg-orange-500/15 px-3 py-1 text-[10px] sm:text-xs font-semibold text-orange-400">
                        Popular
                    </span>
                </div>

                <div class="relative p-2 sm:p-6">
                    <h4 class="transition group-hover:text-green-500">
                        Scratch Card
                    </h4>

                    <p class="mt-3 text-gray-400">
                        Scratch cards instantly and reveal amazing rewards.
                    </p>

                    <div class="mt-5 flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[10px] sm:text-sm text-green-500">
                            <i class="fa-solid fa-circle-check"></i>
                            Available
                        </span>

                        <span class="flex items-center gap-1 text-[10px] sm:text-sm text-orange-400">
                            <i class="fa-solid fa-star"></i>
                            4.9
                        </span>
                    </div>

                    <a href="{{ route('game.show', 'scratch-card') }}"
                        class="mt-6 btn-orange">
                        <i class="fa-solid fa-play mr-2"></i>
                        Play Now
                    </a>
                </div>
            </div>

            {{-- Dice --}}
            <div
                class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-2 hover:border-green-500 hover:shadow-[0_0_35px_rgba(34,197,94,.18)]">

                <div
                    class="absolute inset-0 opacity-0 transition duration-300 group-hover:opacity-100 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
                </div>

                <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="relative flex h-60 items-center justify-center bg-brand-dark p-2 sm:p-6">
                    <img src="{{ asset('images/games/dice.png') }}" alt="Dice"
                        class="max-h-full w-full object-contain transition duration-500 group-hover:scale-110">

                    <span
                        class="absolute left-0 top-0 rounded-full bg-green-500/15 px-3 py-1 text-[10px] sm:text-xs font-semibold text-green-500">
                        Trending
                    </span>
                </div>

                <div class="relative p-2 sm:p-6">
                    <h4 class="transition group-hover:text-green-500">
                        Dice Game
                    </h4>

                    <p class="mt-3 text-gray-400">
                        Roll the dice and predict the winning number.
                    </p>

                    <div class="mt-5 flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[10px] sm:text-sm text-green-500">
                            <i class="fa-solid fa-circle-check"></i>
                            Available
                        </span>

                        <span class="flex items-center gap-1 text-[10px] sm:text-sm text-orange-400">
                            <i class="fa-solid fa-star"></i>
                            4.8
                        </span>
                    </div>

                    <a href="{{ route('game.show', 'dice') }}"
                        class="mt-6 btn-orange">
                        <i class="fa-solid fa-play mr-2"></i>
                        Play Now
                    </a>
                </div>
            </div>

            {{-- Lucky Wheel --}}
            <div
                class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-2 hover:border-green-500 hover:shadow-[0_0_35px_rgba(34,197,94,.18)]">

                <div
                    class="absolute inset-0 opacity-0 transition duration-300 group-hover:opacity-100 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
                </div>

                <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="relative flex h-60 items-center justify-center bg-brand-dark p-2 sm:p-6">
                    <img src="{{ asset('images/games/wheel.png') }}" alt="Wheel"
                        class="max-h-full w-full object-contain transition duration-500 group-hover:scale-110">

                    <span
                        class="absolute left-0 top-0 rounded-full bg-orange-500/15 px-3 py-1 text-[10px] sm:text-xs font-semibold text-orange-400">
                        Hot
                    </span>
                </div>

                <div class="relative p-2 sm:p-6">
                    <h4 class="transition group-hover:text-green-500">
                        Lucky Wheel
                    </h4>

                    <p class="mt-3 text-gray-400">
                        Spin the wheel and unlock exciting lucky prizes.
                    </p>

                    <div class="mt-5 flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[10px] sm:text-sm text-green-500">
                            <i class="fa-solid fa-circle-check"></i>
                            Available
                        </span>

                        <span class="flex items-center gap-1 text-[10px] sm:text-sm text-orange-400">
                            <i class="fa-solid fa-star"></i>
                            4.9
                        </span>
                    </div>

                    <a href="{{ route('game.show', 'wheel') }}"
                        class="mt-6 btn-orange">
                        <i class="fa-solid fa-play mr-2"></i>
                        Play Now
                    </a>
                </div>
            </div>

            {{-- Color Trading --}}
            <div
                class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-2 hover:border-green-500 hover:shadow-[0_0_35px_rgba(34,197,94,.18)]">

                <div
                    class="absolute inset-0 opacity-0 transition duration-300 group-hover:opacity-100 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
                </div>

                <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="relative flex h-60 items-center justify-center bg-brand-dark p-2 sm:p-6">
                    <img src="{{ asset('images/games/trading.png') }}" alt="Color Trading"
                        class="max-h-full w-full object-contain transition duration-500 group-hover:scale-110">

                    <span
                        class="absolute left-0 top-0 rounded-full bg-green-500/15 px-3 py-1 text-[10px] sm:text-xs font-semibold text-green-500">
                        New
                    </span>
                </div>

                <div class="relative p-2 sm:p-6">
                    <h4 class="transition group-hover:text-green-500">
                        Color Trading
                    </h4>

                    <p class="mt-3 text-gray-400">
                        Predict the winning color and earn big rewards.
                    </p>

                    <div class="mt-5 flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[10px] sm:text-sm text-green-500">
                            <i class="fa-solid fa-circle-check"></i>
                            Available
                        </span>

                        <span class="flex items-center gap-1 text-[10px] sm:text-sm text-orange-400">
                            <i class="fa-solid fa-star"></i>
                            4.8
                        </span>
                    </div>

                    <a href="{{ route('game.show', 'color-trading') }}"
                        class="mt-6 btn-orange">
                        <i class="fa-solid fa-play mr-2"></i>
                        Play Now
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
