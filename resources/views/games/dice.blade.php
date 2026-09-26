@php
    $dicePackages = $packages ?? collect();
    $faces = (int) ($gameModel->configValue('faces', 6) ?? 6);
    $defaultDice = ($resumePackageId ?? null)
        ? ($dicePackages->firstWhere('id', $resumePackageId) ?? $dicePackages->first())
        : $dicePackages->first();
@endphp

<div class="dice-game">
    <div class="dice_game_wrapper">
        <div class="grid lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 order-2 lg:order-1">
                <div class="bg-brand-surface border border-brand-border rounded-2xl p-3 md:p-6">
                    <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
                        <div>
                            <h3>Dice Game</h3>
                            <p class="text-gray-400 mt-2">
                                Predict the dice roll and multiply your winnings.
                            </p>
                        </div>
                        <span class="text-green-500 animate-pulse">Live Game</span>
                    </div>
                    <div class="flex justify-center py-6 md:py-12">
                        <div id="dice"
                            class="dice-stage"
                            data-face="1"
                            aria-label="Dice showing 1"
                            style="width:min(55vw,150px);height:min(55vw,150px);border-radius:1.1rem;background:#0b1220;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                            <svg id="diceSvg" viewBox="0 0 220 220" width="125" height="125" xmlns="http://www.w3.org/2000/svg" style="max-width:82%;max-height:82%;display:block;filter:drop-shadow(0 10px 14px rgba(0,0,0,.45));">
                                <defs>
                                    <linearGradient id="topFaceGrad" x1="0.15" y1="0" x2="0.9" y2="1">
                                        <stop offset="0%" stop-color="#ffffff"></stop>
                                        <stop offset="45%" stop-color="#f4f0fb"></stop>
                                        <stop offset="100%" stop-color="#ddd4ef"></stop>
                                    </linearGradient>
                                    <linearGradient id="leftFaceGrad" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#cfc4e4"></stop>
                                        <stop offset="55%" stop-color="#b4a6cf"></stop>
                                        <stop offset="100%" stop-color="#8f7fb0"></stop>
                                    </linearGradient>
                                    <linearGradient id="rightFaceGrad" x1="0.2" y1="0" x2="0.7" y2="1">
                                        <stop offset="0%" stop-color="#ebe3f6"></stop>
                                        <stop offset="50%" stop-color="#d2c6e6"></stop>
                                        <stop offset="100%" stop-color="#a897c4"></stop>
                                    </linearGradient>
                                    <radialGradient id="pipRedGrad" cx="32%" cy="28%" r="70%">
                                        <stop offset="0%" stop-color="#ff8aa3"></stop>
                                        <stop offset="55%" stop-color="#ef4444"></stop>
                                        <stop offset="100%" stop-color="#b91c1c"></stop>
                                    </radialGradient>
                                    <radialGradient id="pipPurpleGrad" cx="32%" cy="28%" r="70%">
                                        <stop offset="0%" stop-color="#c4b5fd"></stop>
                                        <stop offset="55%" stop-color="#7c3aed"></stop>
                                        <stop offset="100%" stop-color="#4c1d95"></stop>
                                    </radialGradient>
                                    <filter id="pipDepth" x="-40%" y="-40%" width="180%" height="180%">
                                        <feDropShadow dx="0.6" dy="1.1" stdDeviation="0.7" flood-color="#000" flood-opacity="0.35"></feDropShadow>
                                    </filter>
                                    <clipPath id="topFaceClip">
                                        <path d="M110 28 L188 70 L110 112 L32 70 Z"></path>
                                    </clipPath>
                                    <clipPath id="leftFaceClip">
                                        <path d="M32 70 L110 112 L110 188 L32 146 Z"></path>
                                    </clipPath>
                                    <clipPath id="rightFaceClip">
                                        <path d="M110 112 L188 70 L188 146 L110 188 Z"></path>
                                    </clipPath>
                                </defs>

                                <path d="M110 28 L188 70 L110 112 L32 70 Z" fill="url(#topFaceGrad)" stroke="#cfc4e3" stroke-width="1.2"></path>
                                <path d="M32 70 L110 112 L110 188 L32 146 Z" fill="url(#leftFaceGrad)" stroke="#9b8bb8" stroke-width="1.2"></path>
                                <path d="M110 112 L188 70 L188 146 L110 188 Z" fill="url(#rightFaceGrad)" stroke="#b5a6cf" stroke-width="1.2"></path>

                                {{-- Soft edge highlights --}}
                                <path d="M110 28 L188 70 L110 112" fill="none" stroke="rgba(255,255,255,0.45)" stroke-width="1.4"></path>
                                <path d="M110 28 L32 70 L110 112" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1"></path>

                                <g id="topPips" clip-path="url(#topFaceClip)" filter="url(#pipDepth)" fill="url(#pipRedGrad)">
                                    <circle class="pip" data-slot="1" cx="88" cy="57" r="6.5" opacity="0"></circle>
                                    <circle class="pip" data-slot="2" cx="110" cy="57" r="6.5" opacity="0"></circle>
                                    <circle class="pip" data-slot="3" cx="132" cy="57" r="6.5" opacity="0"></circle>
                                    <circle class="pip" data-slot="4" cx="88" cy="70" r="6.5" opacity="0"></circle>
                                    <circle class="pip" data-slot="5" cx="110" cy="70" r="7" opacity="1"></circle>
                                    <circle class="pip" data-slot="6" cx="132" cy="70" r="6.5" opacity="0"></circle>
                                    <circle class="pip" data-slot="7" cx="88" cy="83" r="6.5" opacity="0"></circle>
                                    <circle class="pip" data-slot="8" cx="110" cy="83" r="6.5" opacity="0"></circle>
                                    <circle class="pip" data-slot="9" cx="132" cy="83" r="6.5" opacity="0"></circle>
                                </g>

                                <g clip-path="url(#leftFaceClip)" filter="url(#pipDepth)" fill="url(#pipPurpleGrad)">
                                    <circle cx="58" cy="108" r="5.5"></circle>
                                    <circle cx="78" cy="148" r="5.5"></circle>
                                </g>
                                <g clip-path="url(#rightFaceClip)" filter="url(#pipDepth)" fill="url(#pipPurpleGrad)">
                                    <circle cx="160" cy="108" r="5.5"></circle>
                                    <circle cx="149" cy="132" r="5.5"></circle>
                                    <circle cx="138" cy="156" r="5.5"></circle>
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 sm:gap-4">
                        @for ($i = 1; $i <= $faces; $i++)
                            <button type="button"
                                class="dice-number rounded-xl border border-brand-border bg-brand-dark py-4 hover:border-brand-primary transition"
                                data-number="{{ $i }}">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="lg:col-span-4 order-1 lg:order-2">
                <div class="bg-brand-surface border border-brand-border rounded-2xl p-3 md:p-6">
                    <h3>Game Panel</h3>
                    <div class="mt-3 md:mt-6">
                        <label>Chances Left</label>
                        <div class="mt-2 rounded-xl bg-brand-dark border border-brand-border p-4">
                            <div class="flex justify-between">
                                <span class="text-gray-400">This Roll</span>
                                <strong id="diceCharge">$0.00</strong>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-gray-400">Remaining</span>
                                <strong id="diceChances">0</strong>
                            </div>
                        </div>
                        <input id="amount" type="hidden"
                            value="{{ $defaultDice ? number_format((float) $defaultDice->fee, 2) : '0.00' }}">
                    </div>
                    <button id="rollDice" type="button" class="btn-primary w-full mt-3 md:mt-6">
                        Roll Dice
                    </button>
                    <div id="diceResult"
                        class="mt-3 md:mt-6 border border-brand-border rounded-xl p-3 md:p-5 bg-brand-dark text-center text-[12px] sm:text-sm md:text-base">
                        Choose a number &amp; package, then Roll
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        @foreach ($dicePackages as $pkg)
                            @php
                                $chances = (int) $pkg->metaValue('chances', 1);
                            @endphp
                            <button type="button"
                                class="chance-package border border-brand-border rounded-xl p-3 text-center hover:border-brand-primary {{ $defaultDice && $pkg->id === $defaultDice->id ? 'border-brand-primary' : '' }}"
                                data-package-id="{{ $pkg->id }}"
                                data-fee="{{ $pkg->fee }}"
                                data-chances="{{ $chances }}"
                                data-remaining="{{ (int) data_get($packageCredits ?? [], $pkg->id.'.remaining', 0) }}">
                                <h4>${{ number_format((float) $pkg->fee, 0) }}</h4>
                                <p class="package-caption text-gray-400 text-[10px] sm:text-sm mt-1">
                                    @php $left = (int) data_get($packageCredits ?? [], $pkg->id.'.remaining', 0); @endphp
                                    @if ($left > 0)
                                        {{ $left }} left
                                    @else
                                        {{ $chances }} {{ $chances === 1 ? 'Chance' : 'Chances' }}
                                    @endif
                                </p>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const serverResumeId = @json($resumePackageId ?? null);
            const sessionUrl = @json(route('games.session', $slug));
            let selected = @json($resumeNumber ?? null);
            let selectedPackageId = serverResumeId || {{ $defaultDice?->id ?? 'null' }};
            let rolling = false;
            const dice = document.getElementById("dice");
            const topPips = dice.querySelectorAll("#topPips .pip");
            const resultBox = document.getElementById("diceResult");
            const amountInput = document.getElementById("amount");
            const diceCharge = document.getElementById("diceCharge");
            const diceChances = document.getElementById("diceChances");
            const playUrl = @json(route('games.play', $slug));
            const walletBalance = document.getElementById("wallet-balance");
            const walletBet = document.getElementById("wallet-bet");
            const walletPrize = document.getElementById("wallet-prize");

            // Which pip slots light up for each face value
            const faceSlots = {
                1: [5],
                2: [1, 9],
                3: [1, 5, 9],
                4: [1, 3, 7, 9],
                5: [1, 3, 5, 7, 9],
                6: [1, 3, 4, 6, 7, 9],
            };

            function showDiceFace(value) {
                const face = Math.min(6, Math.max(1, Number(value) || 1));
                dice.dataset.face = String(face);
                dice.setAttribute("aria-label", "Dice showing " + face);

                const active = new Set(faceSlots[face] || []);
                topPips.forEach((pip) => {
                    const slot = Number(pip.getAttribute("data-slot"));
                    // Use attribute (not style) so nothing fights CSS later
                    pip.setAttribute("opacity", active.has(slot) ? "1" : "0");
                    pip.style.opacity = "";
                });
            }

            function resetDiceVisual() {
                showDiceFace(1);
            }

            function saveDiceSession() {
                fetch(sessionUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        package_id: selectedPackageId,
                        number: selected ? Number(selected) : null
                    })
                }).catch(() => {});
            }

            function selectedPackageButton() {
                return document.querySelector(`.chance-package[data-package-id="${selectedPackageId}"]`);
            }

            function refreshDiceCost() {
                const btn = selectedPackageButton();
                const remaining = btn ? Number(btn.dataset.remaining || 0) : 0;
                const allowance = btn ? Number(btn.dataset.chances || 0) : 0;
                const fee = btn ? Number(btn.dataset.fee || 0) : 0;
                if (diceChances) {
                    if (remaining > 0) {
                        diceChances.textContent = remaining + " left";
                    } else if (allowance > 0) {
                        diceChances.textContent = allowance + (allowance === 1 ? " chance" : " chances");
                    } else {
                        diceChances.textContent = "0";
                    }
                }
                const caption = btn ? btn.querySelector(".package-caption") : null;
                if (caption) {
                    caption.textContent = remaining > 0
                        ? (remaining + " left")
                        : (allowance + (allowance === 1 ? " Chance" : " Chances"));
                }
                const chargeLabel = remaining > 0 ? "Included" : ("$" + fee.toFixed(2));
                if (diceCharge) diceCharge.textContent = chargeLabel;
                if (amountInput) amountInput.value = fee.toFixed(2);
                if (walletBet) walletBet.textContent = "$" + (remaining > 0 ? 0 : fee).toFixed(2);
            }

            const resumed = selectedPackageButton();
            if (resumed) {
                document.querySelectorAll(".chance-package").forEach(b => b.classList.remove("border-brand-primary"));
                resumed.classList.add("border-brand-primary");
            } else if (selectedPackageId) {
                selectedPackageId = {{ $defaultDice?->id ?? 'null' }};
            }
            if (selected) {
                const numberButton = document.querySelector(`.dice-number[data-number="${selected}"]`);
                if (numberButton) {
                    numberButton.classList.add("border-brand-primary", "bg-brand-primary");
                } else {
                    selected = null;
                }
            }
            showDiceFace(1);
            refreshDiceCost();

            document.querySelectorAll(".dice-number").forEach(btn => {
                btn.addEventListener("click", () => {
                    document.querySelectorAll(".dice-number").forEach(b => {
                        b.classList.remove("border-brand-primary", "bg-brand-primary");
                    });
                    btn.classList.add("border-brand-primary", "bg-brand-primary");
                    selected = btn.dataset.number;
                    saveDiceSession();
                });
            });

            document.querySelectorAll(".chance-package").forEach(btn => {
                btn.addEventListener("click", () => {
                    document.querySelectorAll(".chance-package").forEach(b => {
                        b.classList.remove("border-brand-primary");
                    });
                    btn.classList.add("border-brand-primary");
                    selectedPackageId = Number(btn.dataset.packageId);
                    saveDiceSession();
                    refreshDiceCost();
                });
            });

            function launchConfetti() {
                if (typeof confetti === "function") {
                    confetti({ particleCount: 180, spread: 90, origin: { y: 0.6 } });
                }
            }

            function showWin(title, message) {
                showGameNotice({
                    type: 'success',
                    title: title,
                    message: message,
                    emoji: '🏆',
                    confirmText: 'Awesome',
                });
            }

            document.getElementById("rollDice").addEventListener("click", () => {
                if (rolling) return;
                if (!selected) {
                    showGameNotice({
                        type: 'warning',
                        title: 'Pick a number',
                        message: 'Select a dice number before you roll.',
                        emoji: '🎲',
                    });
                    return;
                }
                if (!selectedPackageId) {
                    showGameNotice({
                        type: 'warning',
                        title: 'Pick a package',
                        message: 'Choose a bet package to continue.',
                        emoji: '💳',
                    });
                    return;
                }

                rolling = true;
                dice.classList.add("dice-rolling");
                resultBox.innerHTML = "Rolling... 🎲";

                fetch(playUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            package_id: selectedPackageId,
                            number: Number(selected),
                            idempotency_key: crypto.randomUUID()
                        })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || "Roll failed");
                        }
                        return data;
                    })
                    .then(data => {
                        setTimeout(() => {
                            const roll = Number(data.play?.outcome?.roll || 0);
                            const prize = Number(data.play?.prize_amount || 0);
                            const won = data.play?.status === "won";
                            dice.classList.remove("dice-rolling");
                            showDiceFace(roll);
                            rolling = false;

                            if (typeof data.balance !== "undefined" && typeof syncWalletBalance === "function") {
                                syncWalletBalance(data.balance);
                            }
                            if (walletPrize) {
                                walletPrize.textContent = "$" + prize.toFixed(2);
                            }
                            const selectedBtn = selectedPackageButton();
                            if (selectedBtn && data.package_credits_remaining != null) {
                                selectedBtn.dataset.remaining = String(data.package_credits_remaining);
                            }
                            refreshDiceCost();

                            if (won) {
                                resultBox.innerHTML = "🎉 You Win $" + prize.toFixed(2);
                                showWin("You Win!", "Prize: <strong>$" + prize.toFixed(2) + "</strong>");
                                launchConfetti();
                            } else {
                                resultBox.innerHTML = "😔 Better Luck Next Time (rolled " + roll + ")";
                            }
                        }, 700);
                    })
                    .catch(err => {
                        rolling = false;
                        resetDiceVisual();
                        dice.classList.remove("dice-rolling");
                        resultBox.innerHTML = err.message || "Unable to roll";
                        showGameNotice({
                            type: 'error',
                            title: 'Roll failed',
                            message: err.message || 'Unable to roll right now.',
                            emoji: '😔',
                        });
                    });
            });
        });
    </script>
@endpush
