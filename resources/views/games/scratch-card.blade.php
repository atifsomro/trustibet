@php
    $scratchPackages = $packages ?? collect();
    $defaultPackage = $scratchPackages->first();
    $cardCount = 6;
@endphp

<div class="py-0 md:py-6 lg:py-10 scratch_card">
    <div class="scratch_cards_wrapper space-y-3 md:space-y-8">

        {{-- Header --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="text-center sm:text-start">
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-3 py-1 text-xs sm:text-sm font-medium text-green-500">
                    <i class="fa-solid fa-ticket"></i>
                    Scratch Cards
                </span>
                <p class="text-gray-400 mt-2">
                    Choose a price, then open any covered card. Each card is one charge.
                </p>
            </div>
            <div id="scratch-result"
                class="w-full md:w-fit px-4 py-2 rounded-xl border border-green-500/30 bg-brand-dark text-center flex items-center justify-center">
                <span id="scratchStatus" class="text-[10px] sm:text-base text-green-500">
                    {{ $cardCount }} cards left
                    @if ($defaultPackage)
                        · ${{ number_format((float) $defaultPackage->fee, 2) }} each
                    @endif
                </span>
            </div>
        </div>

        {{-- Packages --}}
        <div class="grid grid-cols-3 md:grid-cols-4 gap-3">
            @foreach ($scratchPackages as $pkg)
                @php
                    $topPrize = (float) $pkg->activePrizes->max('prize_amount');
                @endphp

                <button type="button"
                    class="scratch-package group relative overflow-hidden rounded-2xl border border-brand-border bg-brand-surface text-center transition-all duration-300 hover:-translate-y-1 hover:border-green-500 hover:shadow-[0_0_25px_rgba(34,197,94,.18)] {{ $defaultPackage && $pkg->id === $defaultPackage->id ? 'border-brand-primary bg-brand-primary/10' : '' }}"
                    data-package-id="{{ $pkg->id }}" data-fee="{{ $pkg->fee }}" data-top="{{ $topPrize }}">

                    <div class="h-1 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                    <div class="p-2 sm:p-4">
                        <h4 class="transition group-hover:text-green-500">${{ number_format((float) $pkg->fee, 0) }}
                        </h4>

                        <p class="mt-1 text-orange-400 text-[8px] sm:text-sm">
                            Up to ${{ number_format($topPrize, 0) }}
                        </p>
                    </div>
                </button>
            @endforeach
        </div>

        {{-- Cards --}}
        <div class="grid grid-cols-3 gap-5">
            @for ($i = 1; $i <= $cardCount; $i++)
                <button type="button"
                    class="scratch-card group relative aspect-[3/4] overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-2 hover:border-green-500 hover:shadow-[0_0_35px_rgba(34,197,94,.18)]"
                    data-id="{{ $i }}">

                    <div
                        class="absolute inset-0 opacity-0 transition duration-300 group-hover:opacity-100 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
                    </div>

                    <div
                        class="absolute top-0 left-0 z-10 h-1 sm:h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500">
                    </div>

                    <div
                        class="absolute top-0 left-0 w-full h-4 sm:h-14 border-b border-brand-border bg-brand-dark flex items-center justify-center">
                        <span
                            class="sm:tracking-[6px] tracking-[3px] text-[6px] sm:text-xl font-bold text-green-500">SCRATCH</span>
                    </div>

                    <div class="card-body relative h-full flex flex-col items-center justify-center px-2">
                        <div
                            class="w-6 h-6 md:w-18 md:h-18 lg:w-24 lg:h-24 rounded-full border-2 border-dashed border-orange-400 text-orange-400 flex items-center justify-center text-[8px] sm:text-xl md:text-2xl lg:text-4xl transition group-hover:scale-110">
                            ?</div>
                        <div class="mt-1 sm:mt-6">
                            <span
                                class="card-label px-2 py-1 sm:px-4 sm:py-2 rounded-full bg-brand-dark border border-green-500/30 text-green-500 text-[5px] sm:text-xl">Card
                                #{{ $i }}</span>
                        </div>
                    </div>

                    <div
                        class="card-footer absolute bottom-0 left-0 w-full py-1 sm:py-4 bg-gradient-to-r from-green-500 to-orange-500 text-white font-semibold text-center text-[6px] sm:text-xl">
                        Scratch ${{ $defaultPackage ? number_format((float) $defaultPackage->fee, 2) : '0.00' }}
                    </div>
                </button>
            @endfor
        </div>

        {{-- New set --}}
        <div class="flex justify-center">
            <button id="newScratchBoard" type="button" class="hidden btn-orange !w-auto px-8">
                <i class="fa-solid fa-rotate-right mr-2"></i>
                New set of cards
            </button>
        </div>
    </div>
</div>

@push('styles')
    <style>
        @keyframes scratch-reveal {
            0% {
                transform: scale(0.92);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .scratch-card .scratch-prize {
            animation: scratch-reveal 0.4s ease;
        }

        .scratch-card .card-body {
            position: relative;
            overflow: hidden;
        }

        .scratch-stage {
            position: absolute;
            inset: 0;
            z-index: 6;
            pointer-events: none;
        }

        .scratch-stage canvas {
            display: block;
            width: 100%;
            height: 100%;
        }

        .scratch-card.is-opening {
            cursor: wait;
        }

        .scratch-underlay {
            position: absolute;
            inset: 0;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const cardTotal = {{ $cardCount }};
            let scratchInFlight = false;
            let selectedPackageId = {{ $defaultPackage?->id ?? 'null' }};
            let activeScratch = null;
            const playUrl = @json(route('games.play', $slug));
            const cards = document.querySelectorAll(".scratch-card");
            const statusText = document.getElementById("scratchStatus");
            const newBoardButton = document.getElementById("newScratchBoard");
            const walletBet = document.getElementById("wallet-bet");
            const walletPrize = document.getElementById("wallet-prize");

            function money(value) {
                return "$" + Number(value || 0).toFixed(2);
            }

            function escapeHtml(value) {
                return String(value).replace(/[&<>"']/g, (ch) => ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    "\"": "&quot;",
                    "'": "&#39;"
                } [ch]));
            }

            function selectedPackageButton() {
                return document.querySelector(`.scratch-package[data-package-id="${selectedPackageId}"]`);
            }

            function selectedFee() {
                return Number(selectedPackageButton()?.dataset.fee || 0);
            }

            function openedCount() {
                return document.querySelectorAll('.scratch-card[data-revealed="1"]').length;
            }

            function refreshBoard() {
                const fee = selectedFee();
                const left = cardTotal - openedCount();
                cards.forEach(card => {
                    if (card.dataset.revealed === "1") return;
                    const footer = card.querySelector(".card-footer");
                    if (footer) footer.textContent = "Scratch " + money(fee);
                });
                if (walletBet) walletBet.textContent = left > 0 ? money(fee) : "—";
                if (left === 0) {
                    statusText.textContent = "All " + cardTotal + " cards opened";
                    newBoardButton.classList.remove("hidden");
                } else {
                    statusText.textContent = left + (left === 1 ? " card left" : " cards left") + " · " + money(
                        fee) + " each";
                    newBoardButton.classList.add("hidden");
                }
            }

            function coverMarkup(cardId) {
                return `
                    <div class="w-6 h-6 md:w-18 md:h-18 lg:w-24 lg:h-24 rounded-full border-2 border-dashed border-orange-400 text-orange-400 flex items-center justify-center text-[8px] sm:text-xl md:text-2xl lg:text-4xl">?</div>
                    <div class="mt-1 sm:mt-6">
                        <span class="card-label px-2 py-1 sm:px-4 sm:py-2 rounded-full bg-brand-dark border border-green-500/30 text-green-500 text-[5px] sm:text-xl">Card #${cardId}</span>
                    </div>`;
            }

            function coverCard(card) {
                if (activeScratch && activeScratch.card === card) {
                    activeScratch.cancel();
                    activeScratch = null;
                }
                card.dataset.revealed = "0";
                card.style.pointerEvents = "";
                card.classList.remove("opacity-80", "is-opening");
                card.querySelector(".card-body").innerHTML = coverMarkup(card.dataset.id);
                const footer = card.querySelector(".card-footer");
                if (footer) footer.textContent = "Scratch " + money(selectedFee());
            }

            function resetBoard() {
                cards.forEach(coverCard);
                if (walletPrize) walletPrize.textContent = "—";
                refreshBoard();
            }

            function paintFoil(ctx, width, height) {
                const gradient = ctx.createLinearGradient(0, 0, width, height);
                gradient.addColorStop(0, "#8b95a7");
                gradient.addColorStop(0.28, "#d7dde8");
                gradient.addColorStop(0.52, "#9aa6b8");
                gradient.addColorStop(0.78, "#eef2f7");
                gradient.addColorStop(1, "#6b7280");
                ctx.globalCompositeOperation = "source-over";
                ctx.fillStyle = gradient;
                ctx.fillRect(0, 0, width, height);

                // Soft metallic speckles
                for (let i = 0; i < 140; i++) {
                    const x = Math.random() * width;
                    const y = Math.random() * height;
                    const a = 0.08 + Math.random() * 0.18;
                    ctx.fillStyle = Math.random() > 0.5 ?
                        `rgba(255,255,255,${a})` :
                        `rgba(15,23,42,${a * 0.55})`;
                    ctx.fillRect(x, y, 1 + Math.random() * 2, 1 + Math.random() * 2);
                }

                ctx.fillStyle = "rgba(15, 23, 42, 0.22)";
                ctx.font = `700 ${Math.max(12, Math.floor(width * 0.12))}px Inter, Arial, sans-serif`;
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.letterSpacing = "4px";
                ctx.fillText("SCRATCH", width / 2, height / 2);
            }

            function startCanvasScratch(card, body) {
                const rect = body.getBoundingClientRect();
                const width = Math.max(1, Math.round(rect.width));
                const height = Math.max(1, Math.round(rect.height));
                const dpr = Math.min(window.devicePixelRatio || 1, 2);

                body.innerHTML = `
                    <div class="scratch-underlay">${coverMarkup(card.dataset.id)}</div>
                    <div class="scratch-stage">
                        <canvas></canvas>
                    </div>`;

                const canvas = body.querySelector("canvas");
                const ctx = canvas.getContext("2d", {
                    willReadFrequently: false
                });
                canvas.width = Math.round(width * dpr);
                canvas.height = Math.round(height * dpr);
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                paintFoil(ctx, width, height);

                const brush = Math.max(16, Math.min(width, height) * 0.16);
                const paths = [
                    [{ x: 0.08, y: 0.22 }, { x: 0.92, y: 0.28 }],
                    [{ x: 0.12, y: 0.40 }, { x: 0.88, y: 0.48 }],
                    [{ x: 0.10, y: 0.58 }, { x: 0.90, y: 0.62 }],
                    [{ x: 0.15, y: 0.74 }, { x: 0.85, y: 0.80 }],
                    [{ x: 0.20, y: 0.30 }, { x: 0.80, y: 0.78 }],
                    [{ x: 0.78, y: 0.24 }, { x: 0.22, y: 0.82 }],
                ];

                let cancelled = false;
                let rafId = 0;
                let pathIndex = 0;
                let progress = 0;
                let lastX = paths[0][0].x * width;
                let lastY = paths[0][0].y * height;

                function eraseAt(x, y, radius) {
                    ctx.globalCompositeOperation = "destination-out";
                    ctx.beginPath();
                    ctx.fillStyle = "#000";
                    ctx.arc(x, y, radius, 0, Math.PI * 2);
                    ctx.fill();

                    // Soft trail between points
                    ctx.lineWidth = radius * 1.65;
                    ctx.lineCap = "round";
                    ctx.strokeStyle = "#000";
                    ctx.beginPath();
                    ctx.moveTo(lastX, lastY);
                    ctx.lineTo(x, y);
                    ctx.stroke();
                    lastX = x;
                    lastY = y;
                }

                function tick() {
                    if (cancelled) return;
                    const path = paths[pathIndex];
                    const from = path[0];
                    const to = path[1];
                    progress += 0.045;
                    const t = Math.min(1, progress);
                    const x = (from.x + (to.x - from.x) * t) * width + (Math.random() - 0.5) * 4;
                    const y = (from.y + (to.y - from.y) * t) * height + (Math.random() - 0.5) * 4;
                    eraseAt(x, y, brush * (0.85 + Math.random() * 0.3));

                    if (t >= 1) {
                        pathIndex += 1;
                        progress = 0;
                        if (pathIndex >= paths.length) {
                            // Keep gently clearing leftover foil until finished
                            eraseAt(
                                Math.random() * width,
                                Math.random() * height,
                                brush * (0.9 + Math.random() * 0.5)
                            );
                            pathIndex = paths.length - 1;
                            progress = 0.35;
                        } else {
                            lastX = paths[pathIndex][0].x * width;
                            lastY = paths[pathIndex][0].y * height;
                        }
                    }
                    rafId = requestAnimationFrame(tick);
                }

                rafId = requestAnimationFrame(tick);

                return {
                    card,
                    canvas,
                    body,
                    cancel() {
                        cancelled = true;
                        if (rafId) cancelAnimationFrame(rafId);
                    },
                    finish(onDone) {
                        // Sweep remaining foil quickly
                        let clearStep = 0;
                        const clear = () => {
                            if (cancelled) return;
                            clearStep += 1;
                            for (let i = 0; i < 10; i++) {
                                eraseAt(
                                    Math.random() * width,
                                    Math.random() * height,
                                    brush * (1.1 + Math.random())
                                );
                            }
                            if (clearStep < 12) {
                                rafId = requestAnimationFrame(clear);
                            } else {
                                cancelled = true;
                                if (rafId) cancelAnimationFrame(rafId);
                                const stage = body.querySelector(".scratch-stage");
                                if (stage) stage.remove();
                                if (typeof onDone === "function") onDone();
                            }
                        };
                        clear();
                    },
                    setPrizeHtml(html) {
                        const underlay = body.querySelector(".scratch-underlay");
                        if (underlay) underlay.innerHTML = html;
                    }
                };
            }

            document.querySelectorAll(".scratch-package").forEach(btn => {
                btn.addEventListener("click", () => {
                    if (scratchInFlight) return;
                    const nextId = Number(btn.dataset.packageId);
                    if (nextId === selectedPackageId) return;
                    if (openedCount() > 0) {
                        resetBoard();
                    }
                    document.querySelectorAll(".scratch-package").forEach(b => {
                        b.classList.remove("border-brand-primary", "bg-brand-primary/10");
                    });
                    btn.classList.add("border-brand-primary", "bg-brand-primary/10");
                    selectedPackageId = nextId;
                    refreshBoard();
                });
            });

            newBoardButton.addEventListener("click", () => {
                if (scratchInFlight) return;
                resetBoard();
            });

            cards.forEach(card => {
                card.addEventListener("click", function() {
                    if (scratchInFlight || this.dataset.revealed === "1") return;
                    if (!selectedPackageId) return;

                    const cardNumber = this.dataset.id;
                    scratchInFlight = true;
                    const scratchStartedAt = Date.now();
                    const scratchMinMs = 1500;
                    const body = this.querySelector(".card-body");
                    const footer = this.querySelector(".card-footer");
                    this.classList.add("is-opening");
                    this.style.pointerEvents = "none";
                    if (footer) footer.textContent = "Scratching...";

                    activeScratch = startCanvasScratch(this, body);
                    if (typeof startScratchSound === "function") {
                        startScratchSound();
                    }

                    fetch(playUrl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                package_id: selectedPackageId,
                                card: Number(this.dataset.id),
                                idempotency_key: crypto.randomUUID()
                            })
                        })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok || !data.success) {
                                throw new Error(data.message || Object.values(data.errors ||
                                {})[0]?.[0] || "Play failed");
                            }
                            return data;
                        })
                        .then(data => {
                            const prizeAmount = Number(data.play?.prize_amount || 0);
                            const reward = data.play?.reward || data.play?.label || money(prizeAmount);
                            const won = prizeAmount > 0;
                            const safeReward = escapeHtml(reward);
                            const prizeHtml = `
                                <div class="scratch-prize flex flex-col items-center justify-center h-full">
                                    <div class="text-[10px] md:text-5xl mb-2 sm:mb-4">${won ? "🎉" : "😔"}</div>
                                    <div class="text-[10px] md:text-3xl text-brand-primary">${safeReward}</div>
                                </div>`;

                            if (activeScratch) {
                                activeScratch.setPrizeHtml(prizeHtml);
                            }

                            const waitMs = Math.max(0, scratchMinMs - (Date.now() - scratchStartedAt));
                            setTimeout(() => {
                                const finishReveal = () => {
                                    this.classList.remove("is-opening");
                                    this.dataset.revealed = "1";
                                    body.innerHTML = prizeHtml;
                                    if (footer) footer.textContent = won ? "Won" : "No prize";
                                    if (typeof data.balance !== "undefined" &&
                                        typeof syncWalletBalance === "function") {
                                        syncWalletBalance(data.balance);
                                    }
                                    if (walletPrize) walletPrize.textContent = money(prizeAmount);
                                    scratchInFlight = false;
                                    activeScratch = null;
                                    refreshBoard();
                                    if (typeof playGameOutcomeSound === "function") {
                                        playGameOutcomeSound(won);
                                    }
                                    if (won && typeof confetti === "function") {
                                        confetti({
                                            particleCount: 140,
                                            spread: 80,
                                            origin: {
                                                y: 0.65
                                            }
                                        });
                                    }
                                    if (typeof showGameNotice === "function") {
                                        showGameNotice({
                                            type: won ? "success" : "info",
                                            title: won ? "You won" : "No prize",
                                            message: won ?
                                                `Card #${cardNumber} revealed <strong>${safeReward}</strong>.` :
                                                `Card #${cardNumber} had no prize. Try another card.`,
                                            emoji: won ? "🎉" : "😔",
                                        });
                                    }
                                };

                                if (typeof stopScratchSound === "function") {
                                    stopScratchSound();
                                }
                                if (activeScratch) {
                                    activeScratch.finish(finishReveal);
                                } else {
                                    finishReveal();
                                }
                            }, waitMs);
                        })
                        .catch(err => {
                            scratchInFlight = false;
                            this.classList.remove("is-opening");
                            if (typeof stopScratchSound === "function") {
                                stopScratchSound();
                            }
                            if (activeScratch) {
                                activeScratch.cancel();
                                activeScratch = null;
                            }
                            coverCard(this);
                            refreshBoard();
                            if (typeof showGameNotice === "function") {
                                showGameNotice({
                                    type: "error",
                                    title: "Scratch failed",
                                    message: err.message || "Unable to play right now.",
                                    emoji: "😔",
                                });
                            }
                        });
                });
            });

            refreshBoard();
        });
    </script>
@endpush
