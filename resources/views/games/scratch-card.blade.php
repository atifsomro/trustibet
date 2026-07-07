{{-- ✅ Must Have (pehle ye complete karte hain)
Wallet se bet deduct ho.
Reward wallet me add ho.
"You Won $10" ya "Better Luck Next Time" popup.
Free scratch khatam hone ke baad cards disable ho jayein.
Deposit karke new chances milen.
Premium cards unlock hon.
Play History (Last 10 scratches). --}}

<section class="py-8 md:py-10">
    <div class="container">
        <div class="scratch_cards_wrapper space-y-10">
            {{-- Free Cards --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3>Recommended Scratch Cards</h3>
                        <p class="text-gray-400 mt-2">
                            You have a chance. Choose any card to reveal your reward.
                        </p>
                    </div>
                    <div class="px-4 py-2 rounded-xl bg-brand-primary text-brand-light">
                        1 Free Chance
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
                                class="absolute top-0 left-0 w-full h-14 border-b border-brand-border bg-brand-dark flex items-center justify-center">

                                <span class="tracking-[6px] text-brand-primary">
                                    SCRATCH
                                </span>

                            </div>

                            <div class="card-body h-full flex flex-col items-center justify-center">

                                <div
                                    class="w-24 h-24 rounded-full border-2 border-dashed border-brand-primary flex items-center justify-center text-4xl">

                                    ?

                                </div>

                                <div class="mt-6">

                                    <span class="px-4 py-2 rounded-full bg-brand-dark border border-brand-border">

                                        Card #{{ $i }}

                                    </span>

                                </div>

                            </div>

                            <div
                                class="absolute bottom-0 left-0 w-full py-4 bg-brand-dark border-t border-brand-border text-center">
                                Click To Reveal
                            </div>
                        </button>
                    @endfor
                </div>
            </div>
            {{-- Deposit Section --}}
            <div class="bg-brand-surface border border-brand-border rounded-2xl p-6">
                <div class="flex flex-col gap-2">
                    <h3>Get More Chances</h3>
                    <p class="text-gray-400 mt-2">
                        Deposit funds to unlock more scratch cards and win bigger rewards.
                    </p>
                    <div class="btn btn-primary">
                        <a href="{{ route('deposit') }}">
                            Deposit Money
                        </a>
                    </div>

                </div>

            </div>
            <div>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3>Premium Scratch Cards</h3>
                        <p class="text-gray-400 mt-2">
                            Deposit to unlock higher rewards.
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                    @for ($i = 1; $i <= 4; $i++)
                        <div
                            class="relative aspect-[3/4] rounded-2xl overflow-hidden border border-brand-border bg-brand-surface opacity-70">
                            <div class="absolute inset-0 bg-brand-dark flex flex-col items-center justify-center">
                                <div
                                    class="w-20 h-20 rounded-full border-2 text-scrach-lock border-brand-primary flex items-center justify-center text-4xl">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <h4 class="mt-6">
                                    Locked
                                </h4>
                                <p class="text-gray-400 mt-2 text-center px-5">
                                    Deposit $10
                                    <br>
                                    to unlock
                                </p>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</section>

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
                    <div class="w-14 h-14 border-4 border-brand-primary border-t-transparent rounded-full animate-spin mx-auto"></div>
                    <p class="mt-4 text-gray-400">Revealing...</p>
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

                            this.querySelector(".card-body").innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full">

                        <div class="text-5xl mb-4">
                            🎉
                        </div>

                        <div class="text-3xl text-brand-primary">
                            ${data.reward}
                        </div>

                    </div>
                `;

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
