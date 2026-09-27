@if (!empty($limitedDrawCard))
    @php
        $card = $limitedDrawCard;
        $game = $card['game'];
        $winnersLabel = $card['winner_count'] === 1
            ? '1 Winner'
            : $card['winner_count'].' Winners';
    @endphp
    <section class="py-10 md:py-16">
        <div class="container">
            <div class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                <div class="relative z-10 grid items-center gap-6 sm:gap-10 md:gap-14 lg:grid-cols-2 p-4 sm:p-8 lg:p-14">
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-scrach-lock/20 bg-scrach-lock/10 px-4 py-2 text-sm font-medium text-scrach-lock">
                            🎟️ {{ $game->badge ?: 'Limited Lucky Draw' }}
                        </span>
                        <h2 class="mt-5">
                            {{ $card['headline'] }}
                            <span class="text-green-500">
                                With Just {{ $card['fee_label'] }}
                            </span>
                        </h2>
                        <p class="mt-6 text-gray-400 leading-8">
                            {{ $game->description }}
                        </p>

                        <div class="mt-8 grid gap-4 sm:grid-cols-3">
                            <div
                                class="rounded-2xl border border-brand-border bg-brand-dark p-5 text-center transition hover:border-green-500">
                                <div
                                    class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-green-500/10">
                                    <i class="fa-solid fa-ticket text-green-500"></i>
                                </div>
                                <h4 class="text-xl font-bold text-green-500">
                                    {{ $card['fee_label'] }}
                                </h4>
                                <p class="mt-1 text-sm text-gray-400">Entry Fee</p>
                            </div>

                            <div
                                class="rounded-2xl border border-brand-border bg-brand-dark p-5 text-center transition hover:border-brand-primary">
                                <div
                                    class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-primary/10">
                                    <i class="fa-solid fa-users text-brand-primary"></i>
                                </div>
                                <h4 class="text-xl font-bold text-brand-primary">
                                    {{ number_format($card['entries']) }}
                                </h4>
                                <p class="mt-1 text-sm text-gray-400">Players Joined</p>
                            </div>

                            <div
                                class="rounded-2xl border border-brand-border bg-brand-dark p-5 text-center transition hover:border-scrach-lock">
                                <div
                                    class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-scrach-lock/10">
                                    <i class="fa-solid fa-trophy text-scrach-lock"></i>
                                </div>
                                <h4 class="text-xl font-bold text-scrach-lock">
                                    {{ $winnersLabel }}
                                </h4>
                                <p class="mt-1 text-sm text-gray-400">Every Draw</p>
                            </div>
                        </div>
                        <a href="{{ route('participate') }}" class="mt-4 md:mt-10 btn-green w-full sm:w-max">
                            <i class="fa-solid fa-ticket"></i>
                            Participate Now
                        </a>
                    </div>
                    <div class="relative flex justify-center">
                        <img src="{{ $game->imageUrl() }}"
                            class="relative z-10 mx-auto w-full max-w-md drop-shadow-[0_20px_50px_rgba(247,165,1,.25)] transition duration-500 hover:scale-105"
                            alt="{{ $card['prize_name'] }}">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
