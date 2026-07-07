<section class="ready-to-win py-6">
    <div class="container">
        <div
            class="ready-to-win__wrapper relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface px-8 py-12 lg:px-16 lg:py-16">
            {{-- Background Glow --}}
            <div
                class="ready-to-win__glow absolute -top-20 -left-20 h-60 w-60 rounded-full bg-brand-primary/10 blur-3xl">
            </div>
            <div
                class="ready-to-win__glow absolute -bottom-24 -right-20 h-72 w-72 rounded-full bg-brand-primary/10 blur-3xl">
            </div>
            <div
                class="ready-to-win__content relative z-10 flex flex-col gap-10 lg:flex-row lg:items-center lg:justify-between">
                {{-- Left --}}
                <div class="ready-to-win__left max-w-2xl">
                    <small class="uppercase tracking-[3px] text-brand-primary">
                        Start Playing Today
                    </small>
                    <h2 class="mt-4">
                        Ready To
                        <span class="text-brand-primary text-inherit">
                            Win Big?
                        </span>
                    </h2>
                    <p class="mt-5">
                        Explore exciting casino games, place your bets with confidence,
                        and enjoy a fast, secure, and entertaining gaming experience.
                    </p>
                </div>
                {{-- Right --}}
                <div class="ready-to-win__actions flex flex-wrap gap-4">
                    <a href="{{ route('about') }}" class="btn-secondary">
                        Explore Games
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>