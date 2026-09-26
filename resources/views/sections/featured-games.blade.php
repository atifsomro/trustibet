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
            @forelse ($featuredGames ?? [] as $featured)
                <div
                    class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-2 hover:border-green-500 hover:shadow-[0_0_35px_rgba(34,197,94,.18)]">

                    <div
                        class="absolute inset-0 opacity-0 transition duration-300 group-hover:opacity-100 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
                    </div>

                    <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                    <div class="relative flex h-60 items-center justify-center bg-brand-dark p-2 sm:p-6">
                        <img src="{{ $featured->imageUrl() }}" alt="{{ $featured->title }}"
                            class="max-h-full w-full object-contain transition duration-500 group-hover:scale-110">

                        @if ($featured->badge)
                            <span
                                class="absolute left-0 top-0 rounded-full bg-orange-500/15 px-3 py-1 text-[10px] sm:text-xs font-semibold text-orange-400">
                                {{ $featured->badge }}
                            </span>
                        @endif
                    </div>

                    <div class="relative p-2 sm:p-6">
                        <h4 class="transition group-hover:text-green-500">
                            {{ $featured->title }}
                        </h4>

                        <p class="mt-3 text-gray-400">
                            {{ $featured->description }}
                        </p>

                        <div class="mt-5 flex items-center justify-between">
                            <span class="flex items-center gap-2 text-[10px] sm:text-sm text-green-500">
                                <i class="fa-solid fa-circle-check"></i>
                                Available
                            </span>

                            <span class="flex items-center gap-1 text-[10px] sm:text-sm text-orange-400">
                                <i class="fa-solid fa-star"></i>
                                {{ number_format((float) $featured->rating, 1) }}
                            </span>
                        </div>

                        <a href="{{ route('game.show', $featured->slug) }}" class="mt-6 btn-orange">
                            <i class="fa-solid fa-play mr-2"></i>
                            Play Now
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-400 py-10">
                    No featured games available yet.
                </div>
            @endforelse
        </div>
    </div>
</section>
