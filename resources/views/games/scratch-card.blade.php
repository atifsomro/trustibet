<div class="py-4 md:py-6 lg:py-10">
    <div class="scratch_cards_wrapper space-y-10">
        {{-- Free Cards --}}
        <div>
            <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
                <div class="text-center sm:text-start">
                    <h3>Recommended Scratch Cards</h3>
                    <p class="text-gray-400 mt-2">
                        You have a chance. Choose any card to reveal your reward.
                    </p>
                </div>
                <div id="scratch-result"
                    class="w-full md:w-fit min-h-[48px] px-4 py-2 rounded-xl bg-brand-primary text-center text-brand-light flex items-center justify-center">
                    <span class="text-sm sm:text-base">Result...</span>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                @for ($i = 1; $i <= 6; $i++)
                    <button
                        class="scratch-card group relative aspect-[3/4] rounded-2xl overflow-hidden border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-brand-primary"
                        data-id="{{ $i }}">
                        <div
                            class="absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-gradient-to-br from-brand-primary/20 via-transparent to-brand-primary/10">
                        </div>
                        <div
                            class="absolute top-0 left-0 w-full h-6 sm:h-14 border-b border-brand-border bg-brand-dark flex items-center justify-center">
                            <span class="tracking-[6px] text-[10px] sm:text-xl text-brand-primary">
                                SCRATCH
                            </span>
                        </div>
                        <div class="card-body h-full flex flex-col items-center justify-center">
                            <div
                                class="w-12 h-12 md:w-18 md:h-18 lg:w-24 lg:h-24 rounded-full border-2 border-dashed border-brand-primary flex items-center justify-center text-xl md:text-2xl lg:text-4xl">
                                ?
                            </div>
                            <div class="mt-6">
                                <span
                                    class="px-2 py-1 sm:px-4 sm:py-2 rounded-full bg-brand-dark border border-brand-border text-[8px] sm:text-xl">
                                    Card #{{ $i }}
                                </span>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-full py-2 sm:py-4 bg-brand-dark border-t border-brand-border text-center text-[10px] sm:text-xl">
                            Click To Reveal
                        </div>
                    </button>
                @endfor
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let played = false;
            const cards = document.querySelectorAll(".scratch-card");
            cards.forEach(card => {
                card.addEventListener("click", function() {
                    if (played) return;
                    played = true;
                    this.style.pointerEvents = "none";
                    this.querySelector(".card-body").innerHTML = `
                <div class="text-center">
                    <div class="w-8 h-8 md:w-14 md:h-14 border-4 border-brand-primary border-t-transparent rounded-full animate-spin mx-auto"></div>
                    <p class="mt-2 sm:mt-4 text-gray-400">Revealing...</p>
                </div>
            `;
                    fetch("{{ route('scratch.reveal') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                card: this.dataset.id
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            const resultBox = document.getElementById("scratch-result");
                            const resultText = resultBox ? resultBox.querySelector("span") :
                                null;

                            this.querySelector(".card-body").innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="text-xl sm:text-5xl mb-2 sm:mb-4">
                            🎉
                        </div>
                        <div class="text-xl sm:text-3xl text-brand-primary">
                            ${data.reward}
                        </div>
                    </div>
                `;

                            if (resultText) {
                                resultText.textContent = data.reward === '$0' ?
                                    "No luck this time. Try again!" :
                                    data.reward === 'FREE SCRATCH' ?
                                    `You won ${data.reward}!` :
                                    `You won ${data.reward}!`;
                            }

                            cards.forEach(c => {
                                if (c !== this) {
                                    c.classList.add("opacity-40");
                                    c.style.pointerEvents = "none";
                                }
                            });
                        });
                });
            });
        });
    </script>
@endpush
