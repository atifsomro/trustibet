@php
    $colors = $gameModel->configValue('colors', [
        'green',
        'red',
        'blue',
        'yellow',
        'orange',
        'purple',
        'pink',
        'cyan',
        'white',
        'black',
    ]);
    $historyLimit = max(1, min(5, (int) $gameModel->configValue('history_limit', 3)));
    $minBet = (float) ($minBet ?? $gameModel->configValue('min_bet', 1));
    $payoutRate = (float) data_get($payout ?? [], 'rate', 93);
    $payoutMultiplier = (float) data_get($payout ?? [], 'multiplier', 1.93);
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

        {{-- Left: Main game --}}
        <div class="lg:col-span-8">
            <div class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-2xl">

                <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="p-3 sm:p-6">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="text-center sm:text-start">
                            <span
                                class="inline-flex items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-3 py-1 text-xs sm:text-sm font-medium text-green-500">
                                <i class="fa-solid fa-palette"></i>
                                Color Trading
                            </span>
                            <h3 class="mt-2">Earn more from color trading</h3>
                            <p class="text-gray-400 text-[12px] sm:text-sm mt-2">Predict the winning color before
                                countdown ends.</p>
                            <p class="text-gray-500 text-[12px] sm:text-sm mt-1">Each round is independent — past
                                colors do not affect the next result.</p>
                        </div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-orange-500/30 bg-orange-500/10 px-3 py-1 text-[10px] sm:text-sm font-semibold text-orange-400 animate-pulse">
                            <span class="h-2 w-2 rounded-full bg-orange-400"></span>
                            LIVE
                        </span>
                    </div>

                    {{-- Timer --}}
                    <div class="flex justify-center mt-5 sm:mt-10">
                        <div
                            class="relative w-30 sm:w-56 h-30 sm:h-56 rounded-full border-[12px] border-green-500 flex items-center justify-center shadow-[0_0_35px_rgba(34,197,94,.18),0_0_60px_rgba(249,115,22,.10)]">
                            <div class="text-center">
                                <p class="text-gray-400 text-[10px] sm:text-sm">Time Left</p>
                                <h1 id="timer" class="text-xl sm:text-6xl font-black sm:mt-2 text-orange-400">
                                    {{ $round?->secondsRemaining() ?? 10 }}
                                </h1>
                            </div>
                        </div>
                    </div>

                    {{-- Colors --}}
                    <div class="grid grid-cols-5 sm:grid-cols-3 md:grid-cols-5 gap-1 sm:gap-4 mt-6 sm:mt-12">
                        @foreach ($colors as $color)
                            <button type="button"
                                class="color-btn {{ $colorClass[$color] ?? 'bg-gray-500' }} text-[10px] sm:text-sm rounded-2xl h-8 sm:h-20 font-bold capitalize transition-all duration-300 hover:-translate-y-1"
                                data-color="{{ $color }}">
                                {{ $color }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Bet amount (simple trading-style field) --}}
                    <div class="mt-4 sm:mt-10 space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <label for="betInput" class="font-semibold text-[12px] sm:text-base text-green-500">Investment</label>
                            <div class="flex items-center gap-3">
                                <button id="autoBetToggle" type="button" aria-pressed="false"
                                    class="inline-flex items-center gap-2 rounded-full border border-brand-border bg-brand-dark px-2.5 py-1 text-[10px] sm:text-xs font-semibold text-gray-400 transition hover:border-green-500/50">
                                    <span id="autoBetKnob"
                                        class="relative h-4 w-7 rounded-full bg-brand-surface border border-brand-border transition">
                                        <span
                                            class="absolute top-0.5 left-0.5 h-3 w-3 rounded-full bg-gray-500 transition-all duration-200"></span>
                                    </span>
                                    Auto Bet
                                </button>
                                <span class="text-[11px] sm:text-sm text-orange-400 font-semibold">
                                    Win <span id="payoutRateText">+{{ number_format($payoutRate, 0) }}%</span>
                                </span>
                            </div>
                        </div>

                        <div class="flex items-start gap-2 sm:gap-3">
                            <div class="relative flex-1 pb-3">
                                <div id="betControl"
                                    class="flex items-center h-12 sm:h-14 rounded-xl border border-brand-border bg-brand-dark overflow-hidden focus-within:border-green-500">
                                    <button id="betMinus" type="button" aria-label="Decrease"
                                        class="h-full w-11 sm:w-14 shrink-0 text-green-500 hover:bg-green-500/10 transition">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>

                                    <div class="flex flex-1 items-center justify-center gap-0.5 border-x border-brand-border px-2">
                                        <input id="betInput" type="text" inputmode="numeric" autocomplete="off"
                                            value="{{ number_format($minBet, 0) }}"
                                            class="w-auto min-w-[2ch] max-w-[8rem] bg-transparent text-right text-lg sm:text-2xl font-semibold text-white outline-none"
                                            aria-label="Investment amount"
                                            size="3">
                                        <span id="betSuffix" class="text-base sm:text-xl font-semibold text-green-500">$</span>
                                    </div>

                                    <button id="betPlus" type="button" aria-label="Increase"
                                        class="h-full w-11 sm:w-14 shrink-0 text-orange-400 hover:bg-orange-500/10 transition">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>

                                <button id="betModeToggle" type="button" aria-label="Convert dollar and percent"
                                    class="absolute left-1/2 -translate-x-1/2 bottom-0 z-10 inline-flex items-center gap-1.5 rounded-full border border-brand-border bg-brand-surface px-2.5 py-0.5 text-[10px] sm:text-xs font-semibold text-gray-300 hover:border-green-500 hover:text-green-400 transition">
                                    <i class="fa-solid fa-right-left text-[10px] sm:text-xs"></i>
                                    <span>convert</span>
                                </button>
                            </div>

                            <button id="placeBet" type="button"
                                class="shrink-0 h-12 sm:h-14 px-4 sm:px-8 rounded-xl bg-gradient-to-r from-green-500 to-orange-500 text-white text-[12px] sm:text-base font-semibold hover:opacity-90 transition">
                                Trade
                            </button>
                        </div>

                        <p class="text-[11px] sm:text-sm text-gray-400 text-right">
                            Stake <span id="actualStake" class="text-green-400 font-semibold">${{ number_format($minBet, 2) }}</span>
                            <span class="mx-1 text-gray-600">·</span>
                            Payout <span id="payoutAmount" class="text-orange-400 font-semibold">${{ number_format($minBet * $payoutMultiplier, 2) }}</span>
                        </p>
                        <p id="betHint" class="hidden"></p>
                        <p id="modeCaption" class="hidden"></p>
                        <span id="modeDollarLabel" class="hidden"></span>
                        <span id="modePercentLabel" class="hidden"></span>
                    </div>

                    {{-- History --}}
                    <div class="mt-4 sm:mt-8">
                        <h4 class="text-green-500">History</h4>
                        <div id="historyMain" class="history-list grid grid-cols-5 gap-3 mt-1 sm:mt-5"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Stats --}}
        <div class="lg:col-span-4">
            <div class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

                <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="p-2 sm:p-3">
                    <div class="grid grid-cols-2 md:grid-cols-1 gap-2">
                        <div
                            class="rounded-2xl text-center sm:text-start bg-brand-dark p-2 sm:p-5 border border-green-500/20">
                            <p class="text-gray-400 text-[10px] sm:text-base">Current Balance</p>
                            <span id="balance" class="mt-2 text-[10px] sm:text-base font-semibold text-green-500"
                                data-live-balance>${{ number_format($balance, 2) }}</span>
                        </div>
                        <div
                            class="rounded-2xl text-center sm:text-start bg-brand-dark p-2 sm:p-5 border border-orange-500/20">
                            <p class="text-gray-400 text-[10px] sm:text-base">Balance Deduction</p>
                            <span id="deduction"
                                class="mt-2 text-[10px] sm:text-base font-semibold text-orange-400">$0</span>
                        </div>
                        <div
                            class="rounded-2xl text-center sm:text-start bg-brand-dark p-2 sm:p-5 border border-brand-border">
                            <p class="text-gray-400 text-[10px] sm:text-base">Current Round</p>
                            <span id="round"
                                class="mt-2 text-[10px] sm:text-base text-green-500">#{{ $round?->round_number ?? '—' }}</span>
                        </div>
                        <div
                            class="rounded-2xl text-center sm:text-start bg-brand-dark p-2 sm:p-5 border border-brand-border">
                            <p class="text-gray-400 text-[10px] sm:text-base">Selected Color</p>
                            <span id="selectedColor"
                                class="mt-2 text-[10px] sm:text-base capitalize text-orange-400">None</span>
                        </div>
                        <div
                            class="rounded-2xl text-center sm:text-start bg-brand-dark p-2 sm:p-5 border border-brand-border">
                            <p class="text-gray-400 mt-2 text-[10px] sm:text-base">Last Result</p>
                            <span id="result" class="capitalize text-green-500">Waiting...</span>
                        </div>
                        <div
                            class="rounded-2xl text-center sm:text-start bg-brand-dark p-2 sm:p-5 border border-brand-border">
                            <span class="text-[10px] sm:text-base text-green-500">Recent Result</span>
                            <div id="historySide" class="history-list grid grid-cols-3 gap-1 sm:gap-3 mt-5"></div>
                        </div>
                    </div>
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
            const historyLimit = {{ $historyLimit }};
            const minBet = {{ $minBet }};

            function csrfToken() {
                return syncCsrfFromCookie();
            }

            function syncCsrfFromCookie() {
                const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
                const fromCookie = match ? decodeURIComponent(match[1]) : '';
                const meta = document.querySelector('meta[name="csrf-token"]');
                const fromMeta = meta?.getAttribute('content') || '';
                const token = fromCookie || fromMeta;

                if (token) {
                    document.querySelectorAll('meta[name="csrf-token"]').forEach(function (el) {
                        el.setAttribute('content', token);
                    });
                }

                return token;
            }

            async function readJsonResponse(res) {
                const text = await res.text();
                let data = {};

                if (text) {
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        if (res.status === 419) {
                            throw new Error('Your session expired. Please refresh the page and try again.');
                        }

                        throw new Error('Unexpected server response. Please refresh and try again.');
                    }
                }

                return data;
            }

            async function postBet(payload) {
                const token = csrfToken();

                return fetch(betUrl, {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": token,
                        "X-XSRF-TOKEN": token,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify(payload)
                });
            }

            const UI = {
                timer: document.getElementById("timer"),
                balance: document.getElementById("balance"),
                round: document.getElementById("round"),
                result: document.getElementById("result"),
                selectedColor: document.getElementById("selectedColor"),
                deduction: document.getElementById("deduction"),
                historyLists: document.querySelectorAll(".history-list"),
                placeBet: document.getElementById("placeBet"),
                colorButtons: document.querySelectorAll(".color-btn"),
                betMinus: document.getElementById("betMinus"),
                betPlus: document.getElementById("betPlus"),
                betInput: document.getElementById("betInput"),
                betSuffix: document.getElementById("betSuffix"),
                betHint: document.getElementById("betHint"),
                betModeToggle: document.getElementById("betModeToggle"),
                autoBetToggle: document.getElementById("autoBetToggle"),
                autoBetKnob: document.getElementById("autoBetKnob"),
                modeDollarLabel: document.getElementById("modeDollarLabel"),
                modePercentLabel: document.getElementById("modePercentLabel"),
                modeCaption: document.getElementById("modeCaption"),
                payoutAmount: document.getElementById("payoutAmount"),
                actualStake: document.getElementById("actualStake"),
                payoutRateText: document.getElementById("payoutRateText"),
                walletBet: document.getElementById("wallet-bet"),
                walletPrize: document.getElementById("wallet-prize"),
            };

            const State = {
                selectedColor: null,
                mode: "dollar",
                units: minBet,
                walletBalance: {{ (float) $balance }},
                payoutRate: {{ $payoutRate }},
                payoutMultiplier: {{ $payoutMultiplier }},
                autoBet: false,
                autoBetTimer: null,
                lastAutoBetRoundId: null,
                bettingLocked: false,
                loading: false,
                currentRoundId: {{ $round?->id ?? 'null' }},
                lastResultShown: null,
                lastSettledNotifiedId: null,
                secondsLeft: Number(UI.timer?.textContent || 0),
                endsAtMs: {{ $round?->ends_at ? (int) ($round->ends_at->getTimestamp() * 1000) : 'null' }},
                locksAtMs: {{ $round?->locks_at ? (int) ($round->locks_at->getTimestamp() * 1000) : 'null' }},
            };

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

            function updateTimer(seconds) {
                const value = Math.max(0, Number(seconds) || 0);
                const prev = State.secondsLeft;
                State.secondsLeft = value;
                UI.timer.textContent = String(value);
                if (value <= 2) {
                    UI.timer.classList.add("text-red-500", "animate-pulse");
                } else {
                    UI.timer.classList.remove("text-red-500", "animate-pulse");
                }
                // Soft clock tick on the final seconds of each round
                if (value !== prev && value > 0 && value <= 3 && typeof playCountdownTickSound === "function") {
                    playCountdownTickSound();
                }
            }

            function bettingWindowOpen() {
                if (!State.endsAtMs || Number.isNaN(State.endsAtMs)) return true;
                return Date.now() < State.endsAtMs;
            }

            function newIdempotencyKey() {
                if (window.crypto && typeof crypto.randomUUID === "function") {
                    return crypto.randomUUID();
                }
                return "bet-" + Date.now().toString(36) + "-" + Math.random().toString(16).slice(2);
            }

            function syncBettingWindow() {
                if (!bettingWindowOpen()) {
                    lockBetting();
                }
            }

            function syncTimerFromRound(round) {
                if (!round) return;
                State.locksAtMs = round.locks_at ? Date.parse(round.locks_at) : null;
                if (round.ends_at) {
                    State.endsAtMs = Date.parse(round.ends_at);
                    const left = Math.max(0, Math.ceil((State.endsAtMs - Date.now()) / 1000));
                    updateTimer(left);
                    return;
                }
                updateTimer(round.seconds_left ?? 0);
            }

            function tickLocalTimer() {
                if (State.endsAtMs) {
                    const left = Math.max(0, Math.ceil((State.endsAtMs - Date.now()) / 1000));
                    if (left !== State.secondsLeft) {
                        updateTimer(left);
                    }
                }
                syncBettingWindow();
            }

            function updateBalance(balance) {
                State.walletBalance = Number(balance) || 0;
                if (typeof syncWalletBalance === "function") {
                    syncWalletBalance(balance);
                }
                refreshBetUI();
            }

            function money(value) {
                return "$" + Number(value || 0).toFixed(2);
            }

            function currentStake() {
                if (State.mode === "percent") {
                    return Math.max(0, (State.walletBalance * State.units) / 100);
                }
                return Math.max(0, State.units);
            }

            function maxUnits() {
                if (State.mode === "percent") {
                    return 100;
                }
                const walletMax = Math.floor(State.walletBalance);
                return Math.max(minBet, walletMax || minBet);
            }

            function syncModeToggle() {
                const isPercent = State.mode === "percent";
                if (UI.betSuffix) {
                    UI.betSuffix.textContent = isPercent ? "%" : "$";
                    UI.betSuffix.className = isPercent
                        ? "text-base sm:text-xl font-semibold text-orange-400"
                        : "text-base sm:text-xl font-semibold text-green-500";
                }
                if (UI.betModeToggle) {
                    UI.betModeToggle.classList.toggle("border-orange-500", isPercent);
                    UI.betModeToggle.classList.toggle("text-orange-400", isPercent);
                    UI.betModeToggle.classList.toggle("border-brand-border", !isPercent);
                }
            }

            function formatUnits(value) {
                const num = Number(value) || 0;
                return Number.isInteger(num) ? String(num) : String(Math.round(num * 100) / 100);
            }

            function syncInputWidth() {
                if (!UI.betInput) return;
                const length = Math.max(1, String(UI.betInput.value || "0").length);
                UI.betInput.size = Math.min(8, length);
                UI.betInput.style.width = `${Math.max(2, length)}ch`;
            }

            function refreshBetUI(options = {}) {
                const keepInputFocus = options.keepInputFocus === true;
                const max = maxUnits();
                if (State.units > max) State.units = max;
                if (State.units < minBet && !keepInputFocus) State.units = minBet;

                if (!keepInputFocus || document.activeElement !== UI.betInput) {
                    UI.betInput.value = formatUnits(State.units);
                }
                syncInputWidth();

                if (State.mode === "percent") {
                    UI.betHint.textContent = "Of wallet (" + money(State.walletBalance) + ") · type %";
                } else {
                    UI.betHint.textContent = "Tap to type · or use + / −";
                }

                const stake = currentStake();
                const payout = stake * State.payoutMultiplier;
                UI.actualStake.textContent = money(stake);
                UI.payoutAmount.textContent = money(payout);
                UI.payoutRateText.textContent = "+" + Number(State.payoutRate).toFixed(0) + "%";

                if (UI.walletBet) {
                    UI.walletBet.textContent = money(stake);
                }

                syncModeToggle();
            }

            function commitBetInput() {
                let raw = String(UI.betInput.value || "").replace(/[^0-9.]/g, "");
                if (raw === "" || raw === ".") {
                    State.units = minBet;
                } else {
                    let value = Number(raw);
                    if (!Number.isFinite(value)) value = minBet;
                    value = Math.floor(value);
                    State.units = Math.min(maxUnits(), Math.max(minBet, value));
                }
                refreshBetUI();
            }

            function adjustBet(delta) {
                if (State.bettingLocked || State.loading) return;
                commitBetInput();
                State.units = Math.min(maxUnits(), Math.max(minBet, State.units + delta));
                refreshBetUI();
            }

            function applyPayout(payout) {
                if (!payout) return;
                State.payoutRate = Number(payout.rate) || State.payoutRate;
                State.payoutMultiplier = Number(payout.multiplier) || State.payoutMultiplier;
                refreshBetUI();
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
                if (!State.loading) {
                    UI.placeBet.textContent = "Locked";
                }
                if (UI.betInput) UI.betInput.disabled = true;
                if (UI.betMinus) UI.betMinus.disabled = true;
                if (UI.betPlus) UI.betPlus.disabled = true;
                if (UI.betModeToggle) UI.betModeToggle.disabled = true;
            }

            function unlockBetting() {
                State.bettingLocked = false;
                const busy = State.loading;
                UI.placeBet.disabled = busy;
                if (!busy) {
                    UI.placeBet.textContent = "Trade";
                    UI.placeBet.classList.remove("opacity-50", "cursor-not-allowed");
                }
                if (UI.betInput) UI.betInput.disabled = false;
                if (UI.betMinus) UI.betMinus.disabled = false;
                if (UI.betPlus) UI.betPlus.disabled = false;
                if (UI.betModeToggle) UI.betModeToggle.disabled = false;
            }

            function resetSelections() {
                State.selectedColor = null;
                UI.colorButtons.forEach(button => {
                    button.classList.remove("ring-4", "ring-brand-primary", "scale-105", "shadow-2xl",
                        "shadow-brand-primary/40");
                });
                updateSelectedColor("None");

                updateDeduction(0);
                refreshBetUI();
            }

            function syncAutoBetToggle() {
                if (!UI.autoBetToggle || !UI.autoBetKnob) return;
                const knob = UI.autoBetKnob.querySelector("span");
                UI.autoBetToggle.setAttribute("aria-pressed", State.autoBet ? "true" : "false");
                UI.autoBetToggle.classList.toggle("border-green-500", State.autoBet);
                UI.autoBetToggle.classList.toggle("text-green-400", State.autoBet);
                UI.autoBetToggle.classList.toggle("bg-green-500/10", State.autoBet);
                UI.autoBetToggle.classList.toggle("text-gray-400", !State.autoBet);
                UI.autoBetKnob.classList.toggle("bg-green-500", State.autoBet);
                UI.autoBetKnob.classList.toggle("border-green-500", State.autoBet);
                if (knob) {
                    knob.style.left = State.autoBet ? "0.85rem" : "0.125rem";
                    knob.classList.toggle("bg-white", State.autoBet);
                    knob.classList.toggle("bg-gray-500", !State.autoBet);
                }
            }

            function scheduleAutoBet(delayMs = 350) {
                if (!State.autoBet) return;
                clearTimeout(State.autoBetTimer);
                State.autoBetTimer = setTimeout(() => {
                    maybeAutoBet();
                }, delayMs);
            }

            function maybeAutoBet() {
                if (!State.autoBet || State.bettingLocked || State.loading) return;
                if (!State.selectedColor) return;
                if (State.lastAutoBetRoundId && State.lastAutoBetRoundId === State.currentRoundId) return;

                commitBetInput();
                const stake = Number(currentStake().toFixed(2));
                if (stake < minBet || stake > State.walletBalance + 0.00001) return;

                placeBet({ auto: true });
            }

            async function placeBet(options = {}) {
                const auto = options.auto === true;
                if (State.loading) return;
                commitBetInput();

                if (!State.selectedColor) {
                    if (!auto) {
                        showGameNotice({
                            type: 'warning',
                            title: 'Pick a color',
                            message: 'Select a color before placing your bet.',
                            emoji: '🎨',
                        });
                    }
                    return;
                }

                const stake = Number(currentStake().toFixed(2));
                if (stake < minBet) {
                    if (!auto) {
                        showGameNotice({
                            type: 'warning',
                            title: 'Bet too small',
                            message: 'Minimum bet is $' + minBet.toFixed(2) + '.',
                            emoji: '🪙',
                        });
                    }
                    return;
                }

                if (stake > State.walletBalance + 0.00001) {
                    if (!auto) {
                        showGameNotice({
                            type: 'warning',
                            title: 'Insufficient balance',
                            message: 'Your wallet does not cover this stake.',
                            emoji: '💼',
                        });
                    }
                    return;
                }

                if (auto && State.currentRoundId) {
                    State.lastAutoBetRoundId = State.currentRoundId;
                }

                setLoading(true);
                const payload = {
                    amount: stake,
                    mode: State.mode,
                    color: State.selectedColor,
                    round_id: State.currentRoundId,
                    idempotency_key: newIdempotencyKey()
                };
                let lastError = null;

                try {
                    for (let attempt = 0; attempt < 2; attempt++) {
                        try {
                            let res = await postBet(payload);

                            // Session/CSRF rotated while the page stayed open — refresh token and retry once.
                            if (res.status === 419) {
                                syncCsrfFromCookie();
                                res = await postBet(payload);
                            }

                            const data = await readJsonResponse(res);
                            if (res.status === 419) {
                                const expired = new Error('Your session expired. Please refresh the page and try again.');
                                expired.permanent = true;
                                throw expired;
                            }
                            if (!res.ok || !data.success) {
                                const failed = new Error(data.message || Object.values(data.errors || {})[0]?.[0] ||
                                    "Bet failed");
                                failed.permanent = res.status < 500;
                                throw failed;
                            }
                            if (auto && State.currentRoundId) {
                                State.lastAutoBetRoundId = State.currentRoundId;
                            }
                            applyPayout(data.payout);
                            applyRound(data.round);
                            updateBalance(data.balance);
                            updateDeduction(data.play?.fee_amount || stake);
                            if (typeof playColorBetSound === "function") {
                                playColorBetSound();
                            }
                            lastError = null;
                            break;
                        } catch (err) {
                            lastError = err;
                            if (err && err.permanent) break;
                        }
                    }

                    if (lastError) {
                        throw lastError;
                    }
                } catch (err) {
                    if (auto && State.currentRoundId && State.lastAutoBetRoundId === State.currentRoundId) {
                        State.lastAutoBetRoundId = null;
                    }
                    showGameNotice({
                        type: 'error',
                        title: 'Bet failed',
                        message: err.message || 'Unable to place bet right now.',
                        emoji: '😔',
                    });
                } finally {
                    setLoading(false);
                }
            }

            function addHistory(color) {
                if (!color || !UI.historyLists.length) return;

                UI.historyLists.forEach((list) => {
                    const item = document.createElement("div");
                    item.className =
                        "aspect-square rounded-xl w-8 sm:w-14 h-8 sm:h-14 text-[8px] sm:text-base border border-brand-border flex items-center justify-center capitalize font-semibold";
                    item.classList.add(...(colorMap[color] || "bg-gray-500 text-white").split(" "));
                    item.textContent = color.charAt(0).toUpperCase();
                    list.prepend(item);
                    while (list.children.length > historyLimit) {
                        list.removeChild(list.lastChild);
                    }
                });
            }

            function setLoading(status) {
                State.loading = status;
                UI.placeBet.disabled = status || State.bettingLocked;
                UI.placeBet.textContent = status ? "Processing..." : (State.bettingLocked ? "Locked" : "Trade");
            }

            function notifySettledBets(bets, resultColor, settledRoundId) {
                if (!Array.isArray(bets) || !bets.length) return;
                if (State.lastSettledNotifiedId === settledRoundId) return;
                State.lastSettledNotifiedId = settledRoundId;

                let totalPrize = 0;
                let wonAny = false;

                bets.forEach((bet) => {
                    if (bet.status === "won") {
                        wonAny = true;
                        totalPrize += Number(bet.prize_amount || 0);
                    }
                });

                if (UI.walletPrize) {
                    UI.walletPrize.textContent = "$" + Number(totalPrize).toFixed(2);
                }

                if (typeof playGameOutcomeSound === "function") {
                    playGameOutcomeSound(wonAny);
                }

                if (typeof showGameNotice !== "function") return;

                if (wonAny) {
                    showGameNotice({
                        type: "success",
                        title: "You won!",
                        message: "Result was " + resultColor + ". Prize: $" + totalPrize.toFixed(2),
                        emoji: "🎉",
                    });
                } else {
                    showGameNotice({
                        type: "info",
                        title: "Round settled",
                        message: "Result was " + resultColor + ". Better luck next round.",
                        emoji: "😔",
                    });
                }
            }

            UI.colorButtons.forEach(button => {
                button.addEventListener("click", () => {
                    if (State.bettingLocked || State.loading) return;
                    UI.colorButtons.forEach(btn => {
                        btn.classList.remove("ring-4", "ring-brand-primary", "scale-105",
                            "shadow-2xl", "shadow-brand-primary/40");
                    });
                    button.classList.add("ring-4", "ring-brand-primary", "scale-105", "shadow-2xl",
                        "shadow-brand-primary/40");
                    State.selectedColor = button.dataset.color;
                    updateSelectedColor(State.selectedColor);
                    if (typeof playColorSelectSound === "function") {
                        playColorSelectSound();
                    }
                    scheduleAutoBet(150);
                });
            });

            UI.betMinus?.addEventListener("click", () => {
                adjustBet(-1);
                scheduleAutoBet(150);
            });
            UI.betPlus?.addEventListener("click", () => {
                adjustBet(1);
                scheduleAutoBet(150);
            });

            UI.betInput?.addEventListener("input", () => {
                if (State.bettingLocked || State.loading) return;
                let raw = String(UI.betInput.value || "").replace(/[^0-9]/g, "");
                if (raw.length > 8) raw = raw.slice(0, 8);
                UI.betInput.value = raw;
                syncInputWidth();
                const value = Number(raw);
                if (Number.isFinite(value) && value > 0) {
                    State.units = value;
                    refreshBetUI({ keepInputFocus: true });
                    scheduleAutoBet(500);
                } else {
                    UI.actualStake.textContent = money(0);
                    UI.payoutAmount.textContent = money(0);
                }
            });

            UI.betInput?.addEventListener("blur", () => {
                if (State.bettingLocked || State.loading) return;
                commitBetInput();
                scheduleAutoBet(100);
            });

            UI.betInput?.addEventListener("keydown", (event) => {
                if (event.key === "Enter") {
                    event.preventDefault();
                    UI.betInput.blur();
                    if (!State.autoBet) {
                        UI.placeBet?.focus();
                    }
                }
            });

            UI.betModeToggle?.addEventListener("click", () => {
                if (State.bettingLocked || State.loading) return;
                State.mode = State.mode === "dollar" ? "percent" : "dollar";
                State.units = minBet;
                refreshBetUI();
                UI.betInput?.focus();
                UI.betInput?.select();
                scheduleAutoBet(150);
            });

            UI.autoBetToggle?.addEventListener("click", () => {
                State.autoBet = !State.autoBet;
                syncAutoBetToggle();
                if (State.autoBet) {
                    scheduleAutoBet(100);
                } else {
                    clearTimeout(State.autoBetTimer);
                }
            });

            UI.placeBet.addEventListener("click", () => placeBet({ auto: false }));

            function applyRound(round) {
                if (!round) return;
                const isNewRound = State.currentRoundId && State.currentRoundId !== round.id;
                State.currentRoundId = round.id;
                updateRound(round.round_number);
                syncTimerFromRound(round);

                if (isNewRound) {
                    State.lastAutoBetRoundId = null;
                    resetSelections();
                }

                if (round.betting_open && bettingWindowOpen()) {
                    unlockBetting();
                    if (State.autoBet) {
                        scheduleAutoBet(200);
                    }
                } else {
                    lockBetting();
                }
            }

            async function pollRound() {
                try {
                    const res = await fetch(roundUrl, {
                        credentials: "same-origin",
                        headers: {
                            "Accept": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                        }
                    });
                    // Keep CSRF meta aligned with cookies refreshed by polling responses.
                    syncCsrfFromCookie();
                    const data = await res.json();
                    if (!data.success) return;

                    if (typeof data.balance !== "undefined") {
                        updateBalance(data.balance);
                    }

                    applyPayout(data.payout);

                    const resultChanged = data.last_result && data.last_result !== State.lastResultShown;
                    if (resultChanged) {
                        State.lastResultShown = data.last_result;
                        updateResult(data.last_result);
                        addHistory(data.last_result);
                    }

                    applyRound(data.round);

                    const hasSettledBets = data.last_settled && Array.isArray(data.my_last_round_bets) &&
                        data.my_last_round_bets.length > 0;
                    if (hasSettledBets) {
                        notifySettledBets(
                            data.my_last_round_bets,
                            data.last_result || data.last_settled.result_color,
                            data.last_settled.id
                        );
                    } else if (resultChanged && typeof playColorRevealSound === "function") {
                        playColorRevealSound();
                    }
                } catch (e) {}
            }

            window.ColorTrading = {
                updateTimer,
                updateBalance,
                updateRound,
                updateResult,
                updateSelectedColor,
                updateDeduction,
                lockBetting,
                unlockBetting,
                resetRound: resetSelections,
                addHistory,
                setLoading
            };

            syncAutoBetToggle();
            refreshBetUI();
            syncBettingWindow();
            pollRound();
            setInterval(pollRound, 1000);
            setInterval(tickLocalTimer, 250);
        });
    </script>
@endpush
