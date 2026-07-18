{{-- Backend Developer Future Me Sirf Ye Call Karega
updateTimer(9);
updateTimer(8);
updateTimer(7);
updateBalance(1850);
updateRound(2050);
updateResult("Green");
lockBetting();
unlockBetting();
resetRound();
Usko tumhari UI ka ek bhi element manually update nahi karna padega. --}}

<div class="color-trading">
    <div class="grid lg:grid-cols-12 gap-6">
        {{-- LEFT --}}
        <div class="lg:col-span-8">
            <div class="bg-brand-surface border border-brand-border rounded-3xl p-3 sm:p-6 shadow-2xl">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="text-center sm:text-start">
                        <h2>
                            Earn more from color trading
                        </h2>
                        <p class="text-gray-400 mt-2">
                            Predict the winning color before countdown ends.
                        </p>
                    </div>
                    <span class="text-green-500 font-semibold animate-pulse">
                        LIVE
                    </span>

                </div>

                {{-- TIMER --}}
                <div class="flex justify-center mt-5 sm:mt-10">
                    <div
                        class="relative w-56 h-56 rounded-full border-[12px] border-brand-border flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-gray-400">
                                Time Left
                            </p>
                            <h1 id="timer" class="text-6xl font-black mt-2">
                                10
                            </h1>
                        </div>
                    </div>
                </div>

                {{-- COLORS --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mt-6 sm:mt-12">
                    <button class="color-btn bg-green-500 rounded-2xl h-10 sm:h-20 font-bold" data-color="green">
                        Green
                    </button>
                    <button class="color-btn bg-red-500 rounded-2xl h-10 sm:h-20 font-bold" data-color="red">
                        Red
                    </button>
                    <button class="color-btn bg-blue-500 rounded-2xl h-10 sm:h-20 font-bold" data-color="blue">
                        Blue
                    </button>
                    <button class="color-btn bg-yellow-500 rounded-2xl h-10 sm:h-20 font-bold text-black"
                        data-color="yellow">
                        Yellow
                    </button>
                    <button class="color-btn bg-orange-500 rounded-2xl h-10 sm:h-20 font-bold" data-color="orange">
                        Orange
                    </button>
                    <button class="color-btn bg-purple-600 rounded-2xl h-10 sm:h-20 font-bold" data-color="purple">
                        Purple
                    </button>
                    <button class="color-btn bg-pink-500 rounded-2xl h-10 sm:h-20 font-bold" data-color="pink">
                        Pink
                    </button>
                    <button class="color-btn bg-cyan-500 rounded-2xl h-10 sm:h-20 font-bold" data-color="cyan">
                        Cyan
                    </button>
                    <button class="color-btn bg-white rounded-2xl h-10 sm:h-20 font-bold text-black" data-color="white">
                        White
                    </button>
                    <button class="color-btn bg-black border border-gray-500 rounded-2xl h-10 sm:h-20 font-bold"
                        data-color="black">
                        Black
                    </button>
                </div>
                {{-- BET CHIPS --}}
                <div class="mt-5 sm:mt-10">
                    <label class="font-semibold">
                        Quick Bet
                    </label>
                    <div class="grid grid-cols-5 gap-3 mt-4">
                        <button class="chip-btn">10</button>
                        <button class="chip-btn">50</button>
                        <button class="chip-btn">100</button>
                        <button class="chip-btn">500</button>
                        <button class="chip-btn">1000</button>
                    </div>
                </div>
                {{-- INPUT --}}
                <div class="mt-4 sm:mt-8">
                    <input id="betAmount" type="number" placeholder="Enter Bet Amount"
                        class="w-full h-8 sm:h-14 rounded-2xl bg-brand-dark border border-brand-border px-3 sm:px-5 text-[12px] sm:text-sm">
                </div>
                <button id="placeBet"
                    class="btn-primary w-full mt-3 sm:mt-6 h-8 sm:h-14 rounded-2xl text-[12px] sm:text-base md:text-lg">
                    Place Bet
                </button>
            </div>
        </div>
        {{-- RIGHT --}}
        <div class="lg:col-span-4">

            <div class="bg-brand-surface border border-brand-border rounded-3xl p-3 sm:p-6">
                <div class="rounded-2xl bg-brand-dark p-3 sm:p-5 border border-brand-border">
                    <p class="text-gray-400">
                        Balance
                    </p>
                    <h3 id="balance" class="mt-2">
                        $1000
                    </h3>
                </div>
                <div class="rounded-2xl bg-brand-dark p-3 sm:p-5 border border-brand-border mt-3 sm:mt-5">
                    <p class="text-gray-400">
                        Current Round
                    </p>
                    <h2 id="round" class="mt-2">
                        #1001
                    </h2>
                </div>
                <div class="rounded-2xl bg-brand-dark p-3 sm:p-5 border border-brand-border mt-3 sm:mt-5">
                    <p class="text-gray-400">
                        Selected Color
                    </p>
                    <h3 id="selectedColor" class="mt-2 capitalize">
                        None
                    </h3>
                </div>
                <div class="rounded-2xl bg-brand-dark p-3 sm:p-5 border border-brand-border mt-3 sm:mt-5">
                    <p class="text-gray-400">
                        Balance Deduction
                    </p>
                    <h3 id="deduction" class="mt-2">
                        $0
                    </h3>
                </div>
                <div class="rounded-2xl bg-brand-dark p-3 sm:p-5 border border-brand-border mt-3 sm:mt-5">
                    <p class="text-gray-400">
                        Last Result
                    </p>
                    <h3 id="result">
                        Waiting...
                    </h3>
                </div>
                <div class="mt-4 sm:mt-8">
                    <h4>
                        History
                    </h4>
                    <div id="history" class="grid grid-cols-5 gap-3 mt-5">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // ==========================================
            // DOM CACHE
            // ==========================================
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
                chipButtons: document.querySelectorAll(".chip-btn")
            };
            // ==========================================
            // FRONTEND STATE
            // ==========================================
            const State = {

                selectedColor: null,

                selectedAmount: 0,

                bettingLocked: false,

                loading: false,

                balance: 1000,

                timer: 10,

                round: 1001,

                currentBet: null

            };

            // ==========================================
            // EVENTS
            // ==========================================
            function bindEvents() {
                // Color Buttons
                UI.colorButtons.forEach(button => {
                    button.addEventListener("click", () => {
                        selectColor(button);
                    });
                });
                // Chip Buttons
                UI.chipButtons.forEach(button => {
                    button.addEventListener("click", () => {
                        selectChip(button);
                    });
                });
                // Bet Input
                UI.betInput.addEventListener("input", handleBetInput);
                // Place Bet
                UI.placeBet.addEventListener("click", placeBet);
            }
            // ==========================================
            // COLOR SELECTION
            // ==========================================

            function selectColor(button) {

                if (State.bettingLocked || State.loading) return;

                // Remove active state from all buttons
                UI.colorButtons.forEach(btn => {

                    btn.classList.remove(
                        "ring-4",
                        "ring-brand-primary",
                        "scale-105",
                        "shadow-2xl",
                        "shadow-brand-primary/40"
                    );

                });

                // Active current button
                button.classList.add(
                    "ring-4",
                    "ring-brand-primary",
                    "scale-105",
                    "shadow-2xl",
                    "shadow-brand-primary/40"
                );

                // Save selected color
                State.selectedColor = button.dataset.color;

                // Update the selected color display in right panel
                updateSelectedColor(State.selectedColor);

            }
            // ==========================================
            // BET CHIPS
            // ==========================================

            function selectChip(button) {

                if (State.bettingLocked || State.loading) return;

                UI.chipButtons.forEach(btn => {

                    btn.classList.remove(
                        "bg-brand-primary",
                        "text-white",
                        "scale-105"
                    );

                });

                button.classList.add(
                    "bg-brand-primary",
                    "text-white",
                    "scale-105"
                );

                const amount = Number(button.textContent.trim());

                UI.betInput.value = amount;

                State.selectedAmount = amount;

            }
            // ==========================================
            // BET INPUT
            // ==========================================
            function handleBetInput() {
                State.selectedAmount = Number(this.value);
            }
            // ==========================================
            // PLACE BET
            // ==========================================
            function placeBet() {
                if (State.bettingLocked || State.loading)
                    return;
                if (!State.selectedColor) {
                    alert("Please select a color.");
                    return;
                }
                if (State.selectedAmount <= 0) {
                    alert("Enter bet amount.");
                    return;
                }
                if (State.selectedAmount > State.balance) {
                    alert("Insufficient Balance.");
                    return;
                }

                State.currentBet = {
                    color: State.selectedColor,
                    amount: State.selectedAmount
                };
            }
            // ====================================
            // Finish Round
            // ====================================
            function finishRound(resultColor) {

                const bet = State.currentBet;

                if (!bet)
                    return;

                let won = bet.color === resultColor;

                if (won) {
                    // They win: give them back 2x their bet (original + winnings)
                    State.balance += bet.amount * 2;
                    updateDeduction(0);
                } else {
                    // They lose: amount already deducted upfront
                    updateDeduction(bet.amount);
                }

                updateBalance(State.balance);
                updateResult(resultColor);
                addHistory(resultColor);
                State.currentBet = null;

            }
            // ====================================
            // Backend Developer will replace this
            // ====================================
            // axios.post('/place-bet', payload)
            //     .then(response => {
            //         setLoading(false);
            //     })
            //     .catch(error => {
            //         setLoading(false);
            //     });
            // ==========================================
            // UI CONTROLLER
            // ==========================================

            function updateTimer(seconds) {

                UI.timer.textContent = seconds;

                // Turn red at 5 seconds
                if (seconds <= 5) {

                    UI.timer.classList.add(
                        "text-red-500",
                        "animate-pulse"
                    );

                } else {

                    UI.timer.classList.remove(
                        "text-red-500",
                        "animate-pulse"
                    );

                }
            }

            function updateBalance(balance) {
                UI.balance.textContent = "$" + balance;
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

                UI.deduction.textContent = "$" + amount;

            }
            // ==========================================
            // BETTING CONTROL
            // ==========================================

            function lockBetting() {

                State.bettingLocked = true;

                UI.placeBet.disabled = true;

                UI.placeBet.classList.add(
                    "opacity-50",
                    "cursor-not-allowed"
                );

            }

            function unlockBetting() {

                State.bettingLocked = false;

                State.loading = false;

                UI.placeBet.disabled = false;

                UI.placeBet.innerHTML = "Place Bet";

                UI.placeBet.classList.remove(
                    "opacity-50",
                    "cursor-not-allowed"
                );

            }
            // ==========================================
            // RESET UI
            // ==========================================

            function resetRound() {

                State.selectedColor = null;

                State.selectedAmount = 0;

                UI.betInput.value = "";

                UI.colorButtons.forEach(button => {

                    button.classList.remove(
                        "ring-4",
                        "ring-brand-primary",
                        "scale-105",
                        "shadow-2xl",
                        "shadow-brand-primary/40"
                    );

                });

                UI.chipButtons.forEach(button => {

                    button.classList.remove(
                        "bg-brand-primary",
                        "text-white",
                        "scale-105"
                    );

                });
                updateResult("Waiting...");
                updateSelectedColor("None");
                updateDeduction(0);
            }
            // ==========================================
            // HISTORY CONTROLLER
            // ==========================================

            function addHistory(color) {

                const item = document.createElement("div");

                item.className =
                    "aspect-square rounded-xl border border-brand-border flex items-center justify-center capitalize font-semibold";

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

                item.classList.add(...colorMap[color].split(" "));

                item.textContent = color.charAt(0).toUpperCase();

                UI.history.prepend(item);

                // Keep only last 20 history items
                while (UI.history.children.length > 20) {

                    UI.history.removeChild(UI.history.lastChild);

                }

            }
            // ==========================================
            // LOADING State
            // ==========================================

            function setLoading(status) {

                State.loading = status;

                if (status) {

                    UI.placeBet.disabled = true;

                    UI.placeBet.innerHTML = "Processing...";

                    UI.placeBet.classList.add(
                        "opacity-60",
                        "cursor-not-allowed"
                    );

                } else {

                    UI.placeBet.disabled = false;

                    UI.placeBet.innerHTML = "Place Bet";

                    UI.placeBet.classList.remove(
                        "opacity-60",
                        "cursor-not-allowed"
                    );

                }

            }
            // ==========================================
            // GAME LOOP & TIMER
            // ==========================================
            const colors = ["green", "red", "blue", "yellow", "orange", "purple", "pink", "cyan", "white", "black"];
            let timerInterval = null;

            function startTimerCountdown() {
                let seconds = 10;
                updateTimer(seconds);

                timerInterval = setInterval(() => {
                    seconds--;
                    updateTimer(seconds);

                    // Lock betting when 5 seconds left
                    if (seconds === 5 && !State.bettingLocked) {
                        lockBetting();
                    }

                    if (seconds <= 0) {
                        clearInterval(timerInterval);
                        finishGameRound();
                    }
                }, 1000);
            }

            function finishGameRound() {
                // Generate random color result
                const randomResult = colors[Math.floor(Math.random() * colors.length)];

                // Only process bet if one was placed
                if (State.currentBet) {
                    let won = State.currentBet.color === randomResult;

                    if (won) {
                        // They win: add double their bet
                        State.balance += State.currentBet.amount * 2;
                        updateDeduction(0);
                    } else {
                        // They lose: deduct their bet
                        State.balance -= State.currentBet.amount;
                        updateDeduction(State.currentBet.amount);
                    }

                    updateBalance(State.balance);
                    State.currentBet = null;
                }

                // Show result regardless of bet
                updateResult(randomResult);
                addHistory(randomResult);

                // After 3 seconds, start new round
                setTimeout(() => {
                    startNewRound();
                }, 3000);
            }

            function startNewRound() {
                State.round++;
                updateRound(State.round);
                resetRound();
                unlockBetting();
                startTimerCountdown();
            }
            // ==========================================
            // PUBLIC API
            // ==========================================

            window.ColorTrading = {
                updateTimer,
                updateBalance,
                updateRound,
                updateResult,
                updateSelectedColor,
                updateDeduction,
                lockBetting,
                unlockBetting,
                resetRound,
                addHistory,
                setLoading,
                finishRound
            };
            // ==========================================
            // INIT
            // ==========================================
            function init() {
                bindEvents();
                startTimerCountdown();
            }

            init();
        });
    </script>
@endpush
