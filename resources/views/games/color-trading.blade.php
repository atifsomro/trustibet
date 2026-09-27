@php
    $colorPackages = $packages ?? collect();
    $colors = $gameModel->configValue('colors', [
        'green', 'red', 'blue', 'yellow', 'orange', 'purple', 'pink', 'cyan', 'white', 'black',
    ]);
    $colorClass = [
        'green' => 'bg-green-500',
        'red' => 'bg-red-500',
        'blue' => 'bg-blue-500',
        'yellow' => 'bg-yellow-500 text-black',
        'orange' => 'bg-orange-500',
        'purple' => 'bg-purple-600',
        'pink' => 'bg-pink-500',
        'cyan' => 'bg-cyan-500',
        'white' => 'bg-white text-black',
        'black' => 'bg-black border border-gray-500',
    ];
@endphp

<div class="color-trading">
    <div class="grid lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8">
            <div class="bg-brand-surface border border-brand-border rounded-3xl p-3 sm:p-6 shadow-2xl">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="text-center sm:text-start">
                        <h2>Earn more from color trading</h2>
                        <p class="text-gray-400 mt-2">Predict the winning color before countdown ends.</p>
                    </div>
                    <span class="text-green-500 font-semibold animate-pulse">LIVE</span>
                </div>

                <div class="flex justify-center mt-5 sm:mt-10">
                    <div
                        class="relative w-56 h-56 rounded-full border-[12px] border-brand-border flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-gray-400">Time Left</p>
                            <h1 id="timer" class="text-6xl font-black mt-2">
                                {{ $round?->secondsRemaining() ?? 10 }}
                            </h1>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mt-6 sm:mt-12">
                    @foreach ($colors as $color)
                        <button type="button"
                            class="color-btn {{ $colorClass[$color] ?? 'bg-gray-500' }} rounded-2xl h-10 sm:h-20 font-bold capitalize"
                            data-color="{{ $color }}">
                            {{ $color }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-5 sm:mt-10">
                    <label class="font-semibold">Quick Bet Packages</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mt-4">
                        @foreach ($colorPackages as $pkg)
                            <button type="button" class="chip-btn border border-brand-border rounded-xl py-2"
                                data-package-id="{{ $pkg->id }}"
                                data-fee="{{ $pkg->fee }}">
                                ${{ number_format((float) $pkg->fee, 0) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 sm:mt-8">
                    <input id="betAmount" type="text" readonly placeholder="Select a chip package"
                        class="w-full h-8 sm:h-14 rounded-2xl bg-brand-dark border border-brand-border px-3 sm:px-5 text-[12px] sm:text-sm">
                </div>
                <button id="placeBet" type="button"
                    class="btn-primary w-full mt-3 sm:mt-6 h-8 sm:h-14 rounded-2xl text-[12px] sm:text-base md:text-lg">
                    Place Bet
                </button>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="bg-brand-surface border border-brand-border rounded-3xl p-3 sm:p-6">
                <div class="rounded-2xl bg-brand-dark p-3 sm:p-5 border border-brand-border">
                    <p class="text-gray-400">Balance</p>
                    <h3 id="balance" class="mt-2" data-live-balance>${{ number_format($balance, 2) }}</h3>
                </div>
                <div class="rounded-2xl bg-brand-dark p-3 sm:p-5 border border-brand-border mt-3 sm:mt-5">
                    <p class="text-gray-400">Current Round</p>
                    <h2 id="round" class="mt-2">#{{ $round?->round_number ?? '—' }}</h2>
                </div>
                <div class="rounded-2xl bg-brand-dark p-3 sm:p-5 border border-brand-border mt-3 sm:mt-5">
                    <p class="text-gray-400">Selected Color</p>
                    <h3 id="selectedColor" class="mt-2 capitalize">None</h3>
                </div>
                <div class="rounded-2xl bg-brand-dark p-3 sm:p-5 border border-brand-border mt-3 sm:mt-5">
                    <p class="text-gray-400">Balance Deduction</p>
                    <h3 id="deduction" class="mt-2">$0</h3>
                </div>
                <div class="rounded-2xl bg-brand-dark p-3 sm:p-5 border border-brand-border mt-3 sm:mt-5">
                    <p class="text-gray-400">Last Result</p>
                    <h3 id="result" class="capitalize">Waiting...</h3>
                </div>
                <div class="mt-4 sm:mt-8">
                    <h4>History</h4>
                    <div id="history" class="grid grid-cols-5 gap-3 mt-5"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const roundUrl = @json(route('games.round', $slug));
            const betUrl = @json(route('games.bets', $slug));
            const csrf = "{{ csrf_token() }}";

            const UI = {
                timer: document.getElementById("timer"),
                balance: document.getElementById("balance"),
                round: document.getElementById("round"),
                result: document.getElementById("result"),
                selectedColor: document.getElementById("selectedColor"),
                deduction: document.getElementById("deduction"),
                history: document.getElementById("history"),
                betInput: document.getElementById("betAmount"),
                placeBet: document.getElementById("placeBet"),
                colorButtons: document.querySelectorAll(".color-btn"),
                chipButtons: document.querySelectorAll(".chip-btn"),
                walletBalance: document.getElementById("wallet-balance"),
                walletBet: document.getElementById("wallet-bet"),
                walletPrize: document.getElementById("wallet-prize"),
            };

            const State = {
                selectedColor: null,
                selectedPackageId: null,
                selectedAmount: 0,
                bettingLocked: false,
                loading: false,
                currentRoundId: null,
                lastResultShown: null,
            };

            function updateTimer(seconds) {
                UI.timer.textContent = seconds;
                if (seconds <= 5) {
                    UI.timer.classList.add("text-red-500", "animate-pulse");
                } else {
                    UI.timer.classList.remove("text-red-500", "animate-pulse");
                }
            }

            function updateBalance(balance) {
                if (typeof syncWalletBalance === "function") {
                    syncWalletBalance(balance);
                }
            }

            function updateRound(round) {
                UI.round.textContent = "#" + round;
            }

            function updateResult(text) {
                UI.result.textContent = text;
            }

            function updateSelectedColor(color) {
                UI.selectedColor.textContent = color;
            }

            function updateDeduction(amount) {
                UI.deduction.textContent = "$" + Number(amount).toFixed(2);
            }

            function lockBetting() {
                State.bettingLocked = true;
                UI.placeBet.disabled = true;
                UI.placeBet.classList.add("opacity-50", "cursor-not-allowed");
            }

            function unlockBetting() {
                State.bettingLocked = false;
                State.loading = false;
                UI.placeBet.disabled = false;
                UI.placeBet.innerHTML = "Place Bet";
                UI.placeBet.classList.remove("opacity-50", "cursor-not-allowed");
            }

            function resetSelections() {
                State.selectedColor = null;
                UI.colorButtons.forEach(button => {
                    button.classList.remove("ring-4", "ring-brand-primary", "scale-105", "shadow-2xl", "shadow-brand-primary/40");
                });
                updateSelectedColor("None");
                updateDeduction(0);
            }

            function addHistory(color) {
                if (!color) return;
                const item = document.createElement("div");
                item.className = "aspect-square rounded-xl border border-brand-border flex items-center justify-center capitalize font-semibold";
                const colorMap = {
                    green: "bg-green-500 text-white",
                    red: "bg-red-500 text-white",
                    blue: "bg-blue-500 text-white",
                    yellow: "bg-yellow-400 text-black",
                    orange: "bg-orange-500 text-white",
                    purple: "bg-purple-600 text-white",
                    pink: "bg-pink-500 text-white",
                    cyan: "bg-cyan-500 text-black",
                    white: "bg-white text-black",
                    black: "bg-black text-white border-gray-500"
                };
                item.classList.add(...(colorMap[color] || "bg-gray-500 text-white").split(" "));
                item.textContent = color.charAt(0).toUpperCase();
                UI.history.prepend(item);
                while (UI.history.children.length > 20) {
                    UI.history.removeChild(UI.history.lastChild);
                }
            }

            function setLoading(status) {
                State.loading = status;
                UI.placeBet.disabled = status || State.bettingLocked;
                UI.placeBet.innerHTML = status ? "Processing..." : "Place Bet";
            }

            UI.colorButtons.forEach(button => {
                button.addEventListener("click", () => {
                    if (State.bettingLocked || State.loading) return;
                    UI.colorButtons.forEach(btn => {
                        btn.classList.remove("ring-4", "ring-brand-primary", "scale-105", "shadow-2xl", "shadow-brand-primary/40");
                    });
                    button.classList.add("ring-4", "ring-brand-primary", "scale-105", "shadow-2xl", "shadow-brand-primary/40");
                    State.selectedColor = button.dataset.color;
                    updateSelectedColor(State.selectedColor);
                });
            });

            UI.chipButtons.forEach(button => {
                button.addEventListener("click", () => {
                    if (State.bettingLocked || State.loading) return;
                    UI.chipButtons.forEach(btn => btn.classList.remove("bg-brand-primary", "text-white", "scale-105"));
                    button.classList.add("bg-brand-primary", "text-white", "scale-105");
                    State.selectedPackageId = Number(button.dataset.packageId);
                    State.selectedAmount = Number(button.dataset.fee);
                    UI.betInput.value = "$" + State.selectedAmount.toFixed(2);
                    if (UI.walletBet) UI.walletBet.textContent = "$" + State.selectedAmount.toFixed(2);
                });
            });

            UI.placeBet.addEventListener("click", async () => {
                if (State.bettingLocked || State.loading) return;
                if (!State.selectedColor) {
                    showGameNotice({
                        type: 'warning',
                        title: 'Pick a color',
                        message: 'Select a color before placing your bet.',
                        emoji: '🎨',
                    });
                    return;
                }
                if (!State.selectedPackageId) {
                    showGameNotice({
                        type: 'warning',
                        title: 'Pick a chip',
                        message: 'Choose a chip package to place your bet.',
                        emoji: '🪙',
                    });
                    return;
                }

                setLoading(true);
                try {
                    const res = await fetch(betUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": csrf
                        },
                        body: JSON.stringify({
                            package_id: State.selectedPackageId,
                            color: State.selectedColor,
                            idempotency_key: crypto.randomUUID()
                        })
                    });
                    const data = await res.json();
                    if (!res.ok || !data.success) {
                        throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || "Bet failed");
                    }
                    updateBalance(data.balance);
                    updateDeduction(data.play?.fee_amount || State.selectedAmount);
                    applyRound(data.round);
                } catch (err) {
                    showGameNotice({
                        type: 'error',
                        title: 'Bet failed',
                        message: err.message || 'Unable to place bet right now.',
                        emoji: '😔',
                    });
                } finally {
                    setLoading(false);
                }
            });

            function applyRound(round) {
                if (!round) return;
                const isNewRound = State.currentRoundId && State.currentRoundId !== round.id;
                State.currentRoundId = round.id;
                updateRound(round.round_number);
                updateTimer(round.seconds_left);

                if (round.betting_open) {
                    unlockBetting();
                } else {
                    lockBetting();
                }

                if (isNewRound) {
                    resetSelections();
                }
            }

            async function pollRound() {
                try {
                    const res = await fetch(roundUrl, {
                        headers: { "Accept": "application/json" }
                    });
                    const data = await res.json();
                    if (!data.success) return;

                    if (typeof data.balance !== "undefined") {
                        updateBalance(data.balance);
                    }

                    if (data.last_result && data.last_result !== State.lastResultShown) {
                        State.lastResultShown = data.last_result;
                        updateResult(data.last_result);
                        addHistory(data.last_result);
                    }

                    applyRound(data.round);

                    if (data.my_bets && data.my_bets.length) {
                        const latest = data.my_bets[data.my_bets.length - 1];
                        if (latest.status === "won" || latest.status === "lost") {
                            if (UI.walletPrize) {
                                UI.walletPrize.textContent = "$" + Number(latest.prize_amount || 0).toFixed(2);
                            }
                        }
                    }
                } catch (e) {}
            }

            window.ColorTrading = {
                updateTimer, updateBalance, updateRound, updateResult,
                updateSelectedColor, updateDeduction, lockBetting, unlockBetting,
                resetRound: resetSelections, addHistory, setLoading
            };

            pollRound();
            setInterval(pollRound, 1000);
        });
    </script>
@endpush
