{{-- 🔄 Backend developer baad me connect karega
Real wallet balance
Deposit gateway
Real chances from database
Reward calculation
Transaction history
Anti-cheat logic
Admin controls --}}
<section class="py-8 md:py-10">
    <div class="container">
        <div class="dice_game_wrapper">
            <div class="grid lg:grid-cols-12 gap-6">
                {{-- Left --}}
                <div class="lg:col-span-8">
                    <div class="bg-brand-surface border border-brand-border rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3>Dice Game</h3>
                                <p class="text-gray-400 mt-2">
                                    Predict the dice roll and multiply your winnings.
                                </p>
                            </div>
                            <span class="px-4 py-2 rounded-xl bg-brand-primary text-brand-light">
                                Live Game
                            </span>
                        </div>
                        <div class="flex justify-center py-10">
                            <div id="dice"
                                class="w-40 h-40 rounded-3xl bg-brand-dark text-color-primary flex items-center justify-center text-7xl">
                                🎲
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @for ($i = 1; $i <= 6; $i++)
                                <button
                                    class="dice-number rounded-xl border border-brand-border bg-brand-dark py-4 hover:border-brand-primary transition"
                                    data-number="{{ $i }}">
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>
                    </div>
                </div>
                {{-- Right --}}
                <div class="lg:col-span-4">
                    <div class="bg-brand-surface border border-brand-border rounded-2xl p-6">
                        <h3>Game Panel</h3>
                        <div class="mt-6">
                            <label>Bet Amount</label>
                            <div class="relative mt-2">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400">
                                    $
                                </span>
                                <input type="number" value="100" min="10"
                                    class="w-full bg-brand-dark border border-brand-border rounded-xl pl-10 pr-4 py-3">
                            </div>
                        </div>
                        <button id="rollDice" class="btn-primary w-full mt-6">
                            Roll Dice
                        </button>
                        <div id="diceResult"
                            class="mt-6 border border-brand-border rounded-xl p-5 bg-brand-dark text-center">
                            Choose a number & Roll
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-2">
                            <button
                                class="chance-package border border-brand-border rounded-xl p-3 hover:border-brand-primary"
                                data-chances="1">
                                <h4>$1</h4>
                                <p class="text-gray-400 text-sm">
                                    1 Chance
                                </p>
                            </button>
                            <button
                                class="chance-package border border-brand-border rounded-xl p-3 hover:border-brand-primary"
                                data-chances="6">
                                <h4>$5</h4>
                                <p class="text-gray-400 text-sm">
                                    6 Chances
                                </p>
                            </button>
                            <button
                                class="chance-package border border-brand-border rounded-xl p-3 hover:border-brand-primary"
                                data-chances="15">
                                <h4>$10</h4>
                                <p class="text-gray-400 text-sm">
                                    15 Chances
                                </p>
                            </button>
                            <button
                                class="chance-package border border-brand-border rounded-xl p-3 hover:border-brand-primary"
                                data-chances="45">
                                <h4>$25</h4>
                                <p class="text-gray-400 text-sm">
                                    45 Chances
                                </p>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const emojis = ["", "⚀", "⚁", "⚂", "⚃", "⚄", "⚅"];
            let selected = null;
            const dice = document.getElementById("dice");
            const resultBox = document.getElementById("diceResult");
            const popup = document.getElementById("winPopup");
            const closePopup = document.getElementById("closePopup");
            // -------------------
            // Number Select
            // -------------------
            document.querySelectorAll(".dice-number").forEach(btn => {
                btn.addEventListener("click", () => {
                    document.querySelectorAll(".dice-number").forEach(b => {
                        b.classList.remove("border-brand-primary", "bg-brand-primary");
                    });
                    btn.classList.add("border-brand-primary", "bg-brand-primary");
                    selected = btn.dataset.number;
                });
            });
            // -------------------
            // Confetti
            // -------------------
            function launchConfetti() {
                confetti({
                    particleCount: 180,
                    spread: 90,
                    origin: {
                        y: 0.6
                    }
                });
            }
            // -------------------
            // Roll Dice
            // -------------------
            document.getElementById("rollDice").addEventListener("click", () => {
                if (!selected) {
                    alert("Select a number first");
                    return;
                }
                // 🎲 animation start
                dice.classList.add("dice-rolling");
                resultBox.innerHTML = "Rolling... 🎲";
                setTimeout(() => {
                    const result = Math.floor(Math.random() * 6) + 1;
                    dice.innerHTML = emojis[result];
                    dice.classList.remove("dice-rolling");
                    if (result == selected) {
                        resultBox.innerHTML = "🎉 You Win";
                        popup.classList.remove("hidden");
                        popup.classList.add("flex");
                        launchConfetti();
                    } else {
                        resultBox.innerHTML = "😔 Better Luck Next Time";
                    }
                }, 800);
            });
            // -------------------
            // Close Popup
            // -------------------
            if (closePopup) {
                closePopup.addEventListener("click", () => {
                    popup.classList.add("hidden");
                    popup.classList.remove("flex");
                });
            }

        });
    </script>
@endpush
