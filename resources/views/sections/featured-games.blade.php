<section class="py-10 md:py-16">
    <div class="container">
        <div class="mb-8">
            <h2>Featured Games</h2>
            <p class="text-gray-400 mt-2">
                Play our most popular casino games.
            </p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {{-- Scratch Card --}}
            <div
                class="bg-brand-surface border border-brand-border rounded-2xl overflow-hidden transition hover:border-brand-primary">
                <div class="aspect-square bg-brand-dark">
                    <img src="{{ asset('images/games/scratch.png') }}" alt="scrach game image"
                        class="max-w-full max-h-full object-contain">
                </div>
                <div class="p-4">
                    <h4>Scratch Card</h4>
                    <p class="text-gray-400 mt-2">
                        Scratch cards instantly and reveal amazing rewards.
                    </p>
                    <a href="{{ route('game.show', 'scratch-card') }}" class="btn-primary w-full mt-4">
                        Play Now
                    </a>
                </div>
            </div>
            {{-- Dice Game --}}
            <div
                class="bg-brand-surface border border-brand-border rounded-2xl overflow-hidden transition hover:border-brand-primary">
                <div class="aspect-square bg-brand-dark">
                    <img src="{{ asset('images/games/dice.png') }}" alt="dice game image"
                        class="max-w-full max-h-full object-contain">
                </div>
                <div class="p-4">
                    <h4>Dice Game</h4>
                    <p class="text-gray-400 mt-2">
                        Roll the dice and predict the winning number.
                    </p>
                    <a href="{{ route('game.show', 'dice') }}" class="btn-primary w-full mt-4">
                        Play Now
                    </a>
                </div>
            </div>
            {{-- Wheel Game --}}
            <div
                class="bg-brand-surface border border-brand-border rounded-2xl overflow-hidden transition hover:border-brand-primary">
                <div class="aspect-square bg-brand-dark">
                    <img src="{{ asset('images/games/wheel.png') }}" alt="wheel game image"
                        class="max-w-full max-h-full object-contain">
                </div>
                <div class="p-4">
                    <h4>Lucky Wheel</h4>
                    <p class="text-gray-400 mt-2">
                        Spin the wheel and unlock exciting lucky prizes.
                    </p>
                    <a href="{{ route('game.show', 'wheel') }}" class="btn-primary w-full mt-4">
                        Play Now
                    </a>
                </div>
            </div>
            {{-- Color Trading Game --}}
            <div
                class="bg-brand-surface border border-brand-border rounded-2xl overflow-hidden transition hover:border-brand-primary">
                <div class="aspect-square bg-brand-dark">
                    <img src="{{ asset('images/games/trading.png') }}" alt="color trading game image"
                        class="max-w-full max-h-full object-contain">
                </div>
                <div class="p-4">
                    <h4>Color Trading</h4>
                    <p class="text-gray-400 mt-2">
                        Predict the winning color and earn big rewards.
                    </p>
                    <a href="{{ route('game.show', 'color-trading') }}" class="btn-primary w-full mt-4">
                        Play Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
