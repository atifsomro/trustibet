@php
    $scratchPackages = $packages ?? collect();
    $defaultPackage = $scratchPackages->first();
    $cardCount = 6;
@endphp

<div class="py-4 md:py-6 lg:py-10">
    <div class="scratch_cards_wrapper space-y-8">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="text-center sm:text-start">
                <h3>Scratch Cards</h3>
                <p class="text-gray-400 mt-2">
                    Choose a price, then open any covered card. Each card is one charge.
                </p>
            </div>
            <div id="scratch-result"
                class="w-full md:w-fit min-h-[48px] px-4 py-2 rounded-xl border border-brand-border bg-brand-dark text-center flex items-center justify-center">
                <span id="scratchStatus" class="text-sm sm:text-base text-gray-300">
                    {{ $cardCount }} cards left
                    @if ($defaultPackage)
                        · ${{ number_format((float) $defaultPackage->fee, 2) }} each
                    @endif
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach ($scratchPackages as $pkg)
                @php
                    $topPrize = (float) $pkg->activePrizes->max('prize_amount');
                @endphp
                <button type="button"
                    class="scratch-package border border-brand-border rounded-xl p-3 text-center hover:border-brand-primary {{ $defaultPackage && $pkg->id === $defaultPackage->id ? 'border-brand-primary bg-brand-primary/10' : '' }}"
                    data-package-id="{{ $pkg->id }}"
                    data-fee="{{ $pkg->fee }}"
                    data-top="{{ $topPrize }}">
                    <h4>${{ number_format((float) $pkg->fee, 0) }}</h4>
                    <p class="text-gray-400 text-[10px] sm:text-sm mt-1">Up to ${{ number_format($topPrize, 0) }}</p>
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
            @for ($i = 1; $i <= $cardCount; $i++)
                <button type="button"
                    class="scratch-card group relative aspect-[3/4] rounded-2xl overflow-hidden border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-brand-primary"
                    data-id="{{ $i }}">
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-gradient-to-br from-brand-primary/20 via-transparent to-brand-primary/10"></div>
                    <div class="absolute top-0 left-0 w-full h-6 sm:h-14 border-b border-brand-border bg-brand-dark flex items-center justify-center">
                        <span class="tracking-[6px] text-[10px] sm:text-xl text-brand-primary">SCRATCH</span>
                    </div>
                    <div class="card-body h-full flex flex-col items-center justify-center px-2">
                        <div class="w-12 h-12 md:w-18 md:h-18 lg:w-24 lg:h-24 rounded-full border-2 border-dashed border-brand-primary flex items-center justify-center text-xl md:text-2xl lg:text-4xl">?</div>
                        <div class="mt-6">
                            <span class="card-label px-2 py-1 sm:px-4 sm:py-2 rounded-full bg-brand-dark border border-brand-border text-[8px] sm:text-xl">Card #{{ $i }}</span>
                        </div>
                    </div>
                    <div class="card-footer absolute bottom-0 left-0 w-full py-2 sm:py-4 bg-brand-dark border-t border-brand-border text-center text-[10px] sm:text-xl">
                        Scratch ${{ $defaultPackage ? number_format((float) $defaultPackage->fee, 2) : '0.00' }}
                    </div>
                </button>
            @endfor
        </div>

        <div class="flex justify-center">
            <button id="newScratchBoard" type="button" class="hidden rounded-xl border border-brand-border px-5 py-3 text-sm hover:border-brand-primary">
                New set of cards
            </button>
        </div>
    </div>
</div>

@push('styles')
    <style>
        @keyframes scratch-shake {
            0%, 100% { transform: translateX(0) rotate(0); }
            25% { transform: translateX(-4px) rotate(-0.6deg); }
            75% { transform: translateX(4px) rotate(0.6deg); }
        }

        @keyframes scratch-reveal {
            0% { transform: scale(0.86) rotate(-3deg); filter: blur(6px); opacity: 0; }
            55% { transform: scale(1.05) rotate(1deg); filter: blur(0); opacity: 1; }
            100% { transform: scale(1) rotate(0); filter: blur(0); opacity: 1; }
        }

        .scratch-card.is-opening {
            animation: scratch-shake 0.28s linear infinite;
        }

        .scratch-card .scratch-prize {
            animation: scratch-reveal 0.55s ease;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const cardTotal = {{ $cardCount }};
            let scratchInFlight = false;
            let selectedPackageId = {{ $defaultPackage?->id ?? 'null' }};
            const playUrl = @json(route('games.play', $slug));
            const cards = document.querySelectorAll(".scratch-card");
            const statusText = document.getElementById("scratchStatus");
            const newBoardButton = document.getElementById("newScratchBoard");
            const walletBalance = document.getElementById("wallet-balance");
            const walletBet = document.getElementById("wallet-bet");
            const walletPrize = document.getElementById("wallet-prize");

            function money(value) {
                return "$" + Number(value || 0).toFixed(2);
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
                    statusText.textContent = left + (left === 1 ? " card left" : " cards left") + " · " + money(fee) + " each";
                    newBoardButton.classList.add("hidden");
                }
            }

            function coverCard(card) {
                card.dataset.revealed = "0";
                card.style.pointerEvents = "";
                card.classList.remove("opacity-80");
                card.querySelector(".card-body").innerHTML = `
                    <div class="w-12 h-12 md:w-18 md:h-18 lg:w-24 lg:h-24 rounded-full border-2 border-dashed border-brand-primary flex items-center justify-center text-xl md:text-2xl lg:text-4xl">?</div>
                    <div class="mt-6"><span class="card-label px-2 py-1 sm:px-4 sm:py-2 rounded-full bg-brand-dark border border-brand-border text-[8px] sm:text-xl">Card #${card.dataset.id}</span></div>`;
                const footer = card.querySelector(".card-footer");
                if (footer) footer.textContent = "Scratch " + money(selectedFee());
            }

            function resetBoard() {
                cards.forEach(coverCard);
                if (walletPrize) walletPrize.textContent = "—";
                refreshBoard();
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

                    const fee = selectedFee();
                    const cardNumber = this.dataset.id;
                    scratchInFlight = true;
                    this.classList.add("is-opening");
                    this.style.pointerEvents = "none";
                    this.querySelector(".card-body").innerHTML = `
                        <div class="text-center">
                            <div class="w-8 h-8 md:w-14 md:h-14 border-4 border-brand-primary border-t-transparent rounded-full animate-spin mx-auto"></div>
                            <p class="mt-2 sm:mt-4 text-gray-400">Charging ${money(fee)}...</p>
                        </div>`;

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
                                throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || "Play failed");
                            }
                            return data;
                        })
                        .then(data => {
                            const prizeAmount = Number(data.play?.prize_amount || 0);
                            const reward = data.play?.reward || data.play?.label || money(prizeAmount);
                            const won = prizeAmount > 0;
                            const safeReward = String(reward).replace(/[&<>"']/g, (ch) => ({
                                "&": "&amp;",
                                "<": "&lt;",
                                ">": "&gt;",
                                "\"": "&quot;",
                                "'": "&#39;"
                            }[ch]));
                            this.classList.remove("is-opening");
                            this.dataset.revealed = "1";
                            this.querySelector(".card-body").innerHTML = `
                                <div class="scratch-prize flex flex-col items-center justify-center h-full">
                                    <div class="text-xl sm:text-5xl mb-2 sm:mb-4">${won ? "🎉" : "😔"}</div>
                                    <div class="text-xl sm:text-3xl text-brand-primary">${safeReward}</div>
                                </div>`;
                            const footer = this.querySelector(".card-footer");
                            if (footer) footer.textContent = won ? "Won" : "No prize";
                            if (typeof data.balance !== "undefined" && typeof syncWalletBalance === "function") {
                                syncWalletBalance(data.balance);
                            }
                            if (walletPrize) walletPrize.textContent = money(prizeAmount);
                            scratchInFlight = false;
                            refreshBoard();
                            if (won && typeof confetti === "function") {
                                confetti({ particleCount: 140, spread: 80, origin: { y: 0.65 } });
                            }
                            if (typeof showGameNotice === "function") {
                                showGameNotice({
                                    type: won ? "success" : "info",
                                    title: won ? "You won" : "No prize",
                                    message: won
                                        ? `Card #${cardNumber} revealed <strong>${safeReward}</strong>.`
                                        : `Card #${cardNumber} had no prize. Try another card.`,
                                    emoji: won ? "🎉" : "😔",
                                });
                            }
                        })
                        .catch(err => {
                            scratchInFlight = false;
                            this.classList.remove("is-opening");
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

