<div class="lucky-wheel">
    <div class="wheel_game_wrapper">
        <div class="grid lg:grid-cols-12 gap-6">
            {{-- LEFT --}}
            <div class="lg:col-span-8 order-2 lg:order-1">
                <div class="bg-brand-surface border border-brand-border rounded-2xl p-3 sm:p-6">
                    <div class="flex items-center justify-between mb-8 flex-wrap gap-2">
                        <div class="text-center sm:text-start">
                            <h3>Lucky Wheel</h3>
                            <p class="text-gray-400 mt-2">
                                Spin the wheel and win exciting rewards.
                            </p>
                        </div>
                        <span class="text-green-500 text-center sm:text-start animate-pulse">
                            Live Game
                        </span>
                    </div>
                    <div class="flex flex-col items-center">
                        {{-- Pointer --}}
                        <div class="relative z-20">
                            <div
                                class="w-0 h-0
                                    border-l-18
                                    border-r-18
                                    border-t-32
                                    border-l-transparent
                                    border-r-transparent
                                    border-t-brand-primary">
                            </div>
                        </div>
                        {{-- Wheel --}}
                        <div class="relative -mt-2">
                            <div class="relative w-50 sm:w-80 md:w-96 lg:w-105 aspect-square">
                                {{-- Pointer --}}
                                <div
                                    class="absolute left-1/2 -translate-x-1/2 -top-4 z-20
                                        w-0 h-0
                                        border-l-16
                                        border-r-16
                                        border-t-28
                                        border-l-transparent
                                        border-r-transparent
                                        border-t-red-500">
                                </div>
                                {{-- Wheel --}}
                                <svg id="wheel" viewBox="0 0 420 420" class="duration-6000">
                                    <g id="wheelGroup">
                                    </g>
                                </svg>
                                <div
                                    class="absolute inset-0 rounded-full border-8 border-brand-primary pointer-events-none">
                                </div>
                            </div>
                        </div>
                        <button id="spinWheel" class="btn-primary mt-8 min-w-45">
                            Spin Now
                        </button>
                    </div>
                </div>
            </div>
            {{-- RIGHT --}}
            <div class="lg:col-span-4 order-1 lg:order-2">
                <div class="bg-brand-surface border border-brand-border rounded-2xl p-3 sm:p-6">
                    <h3 class="text-center sm:text-start">
                        Game Panel
                    </h3>
                    {{-- Spins --}}
                    <div class="mt-6">
                        <label>
                            Spins
                        </label>
                        <div class="mt-2 rounded-xl bg-brand-dark border border-brand-border p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">
                                    Free Spins
                                </span>
                                <strong id="freeSpins">
                                    2
                                </strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">
                                    Purchased Spins
                                </span>
                                <strong id="paidSpins">
                                    0
                                </strong>
                            </div>
                        </div>
                    </div>
                    {{-- Deposit --}}
                    <div class="mt-6">
                        <div class="text-[12px] sm:text:sm md:text-base text-center sm:text-start">
                            Deposit To Get More Spins
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <a href="#"
                                class="chance-package flex flex-col items-center text-center rounded-xl border border-brand-border p-2 sm:p-4 hover:border-brand-primary"
                                data-spin="1">
                                <h4>$1</h4>
                                <p class="text-[10px] sm:text-sm text-gray-400">
                                    1 Spin
                                </p>
                            </a>
                            <a href="#"
                                class="chance-package flex flex-col items-center text-center rounded-xl border border-brand-border p-2 sm:p-4 hover:border-brand-primary"
                                data-spin="6">
                                <h4>$5</h4>
                                <p class="text-[10px] sm:text-sm text-gray-400">
                                    6 Spins
                                </p>
                            </a>
                            <a href="#"
                                class="chance-package flex flex-col items-center text-center rounded-xl border border-brand-border p-2 sm:p-4 hover:border-brand-primary"
                                data-spin="15">
                                <h4>$10</h4>
                                <p class="text-[10px] sm:text-sm text-gray-400">
                                    15 Spins
                                </p>
                            </a>
                            <a href="#"
                                class="chance-package flex flex-col items-center text-center rounded-xl border border-brand-border p-2 sm:p-4 hover:border-brand-primary"
                                data-spin="45">
                                <h4>$25</h4>
                                <p class="text-[10px] sm:text-sm text-gray-400">
                                    45 Spins
                                </p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- WIN POPUP --}}
<div id="winPopup" class="fixed inset-0 hidden items-center justify-center bg-black/70 z-50">
    <div class="bg-brand-surface border border-brand-border rounded-2xl p-4 sm:p-8 w-[280px] sm:w-[320px] md:w-[380px] lg:w-[420px] text-center">
        <div class="text-6xl mb-5">
            🎉
        </div>
        <h3 id="popupTitle">
            Congratulations
        </h3>
        <p id="popupReward" class="mt-3 text-gray-400">
            You Won
        </p>
        <button id="closePopup" class="btn-primary w-full mt-6">
            Continue
        </button>
    </div>
</div>

@push('scripts')
    <script>
        let currentRotation = 0;
        let spinning = false;


        document.addEventListener("DOMContentLoaded", () => {
            // logic for deduct value of free spin
            let freeSpins = 2;
            let paidSpins = 0;
            // Message will be in modal
            const popup = document.getElementById("winPopup");
            const popupTitle = document.getElementById("popupTitle");
            const popupReward = document.getElementById("popupReward");
            const closePopup = document.getElementById("closePopup");

            function showModal(title, message, emoji = "🎉") {
                document.querySelector("#winPopup .text-6xl").innerHTML = emoji;
                popupTitle.innerHTML = title;
                popupReward.innerHTML = message;
                popup.classList.remove("hidden");
                popup.classList.add("flex");
            }

            closePopup.onclick = () => {
                popup.classList.add("hidden");
                popup.classList.remove("flex");
            };

            const freeSpinsText = document.getElementById("freeSpins");
            const paidSpinsText = document.getElementById("paidSpins");
            freeSpinsText.innerHTML = freeSpins;
            paidSpinsText.innerHTML = paidSpins;

            const wheelGroup = document.getElementById("wheelGroup");

            wheelGroup.style.transformBox = "fill-box";
            wheelGroup.style.transformOrigin = "center";
            const slices = [{
                    label: "$0",
                    color: "#ef4444",
                    weight: 40
                },
                {
                    label: "2 Spins",
                    color: "#22c55e",
                    weight: 20
                },
                {
                    label: "$10",
                    color: "#3b82f6",
                    weight: 12
                },
                {
                    label: "$1",
                    color: "#f59e0b",
                    weight: 18
                },
                {
                    label: "$0",
                    color: "#ef4444",
                    weight: 40
                },
                {
                    label: "$5",
                    color: "#06b6d4",
                    weight: 8
                },
                {
                    label: "$100",
                    color: "#9333ea",
                    weight: 2
                },
                {
                    label: "$0",
                    color: "#ef4444",
                    weight: 40
                },
                {
                    label: "$3",
                    color: "#14b8a6",
                    weight: 12
                },
                {
                    label: "$0",
                    color: "#ef4444",
                    weight: 40
                },
                {
                    label: "2 Spins",
                    color: "#22c55e",
                    weight: 20
                },
                {
                    label: "$10",
                    color: "#3b82f6",
                    weight: 12
                }
            ];

            function getWeightedReward() {
                let pool = [];
                slices.forEach((slice, index) => {
                    for (let i = 0; i < slice.weight; i++) {
                        pool.push(index);
                    }
                });
                return pool[Math.floor(Math.random() * pool.length)];
            }

            const cx = 210;
            const cy = 210;
            const r = 190;

            slices.forEach((slice, index) => {
                const start = index * 30;
                const centerAngle = start + 15;
                slice.stopAngle = centerAngle;

                const end = start + 30;
                const x1 = cx + r * Math.cos((start - 90) * Math.PI / 180);
                const y1 = cy + r * Math.sin((start - 90) * Math.PI / 180);
                const x2 = cx + r * Math.cos((end - 90) * Math.PI / 180);
                const y2 = cy + r * Math.sin((end - 90) * Math.PI / 180);
                const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
                path.setAttribute("d",
                    `M ${cx} ${cy}
                        L ${x1} ${y1}
                        A ${r} ${r} 0 0 1 ${x2} ${y2}
                        Z`);
                path.setAttribute("fill", slice.color);
                path.setAttribute("stroke", "#111827");
                path.setAttribute("stroke-width", "2");
                wheelGroup.appendChild(path);
                const angle = start + 15;
                const tx = cx + (r - 65) * Math.cos((angle - 90) * Math.PI / 180);
                const ty = cy + (r - 65) * Math.sin((angle - 90) * Math.PI / 180);
                const text = document.createElementNS("http://www.w3.org/2000/svg", "text");
                text.setAttribute("x", tx);
                text.setAttribute("y", ty);
                text.setAttribute("fill", "#fff");
                text.setAttribute("font-size", "18");
                text.setAttribute("font-weight", "700");
                text.setAttribute("text-anchor", "middle");
                text.setAttribute("dominant-baseline", "middle");
                text.setAttribute("transform", `rotate(${angle + 90} ${tx} ${ty})`);
                text.textContent = slice.label;
                wheelGroup.appendChild(text);
            });
            // Center Circle
            const center = document.createElementNS("http://www.w3.org/2000/svg", "circle");
            center.setAttribute("cx", cx);
            center.setAttribute("cy", cy);
            center.setAttribute("r", "48");
            center.setAttribute("fill", "#0f172a");
            center.setAttribute("stroke", "#38bdf8");
            center.setAttribute("stroke-width", "6");
            wheelGroup.appendChild(center);
            // Center Text
            const centerText = document.createElementNS("http://www.w3.org/2000/svg", "text");
            centerText.setAttribute("x", cx);
            centerText.setAttribute("y", cy + 6);
            centerText.setAttribute("fill", "#fff");
            centerText.setAttribute("font-size", "18");
            centerText.setAttribute("font-weight", "700");
            centerText.setAttribute("text-anchor", "middle");
            centerText.textContent = "Luck";
            wheelGroup.appendChild(centerText);

            document.querySelectorAll(".chance-package").forEach(btn => {
                btn.addEventListener("click", () => {
                    paidSpins += Number(btn.dataset.spin);
                    paidSpinsText.innerHTML = paidSpins;
                });
            });

            document.getElementById("spinWheel").onclick = () => {
                if (spinning) return;
                // Check available spins
                if (freeSpins > 0) {
                    freeSpins--;
                    freeSpinsText.innerHTML = freeSpins;
                } else if (paidSpins > 0) {
                    paidSpins--;
                    paidSpinsText.innerHTML = paidSpins;
                } else {
                    showModal(
                        "No Spins Left",
                        "Deposit to get more spins and continue playing."
                    );
                    return;
                }
                spinning = true;

                const index = getWeightedReward();
                const reward = slices[index];
                // Pointer top par hai (12 o'clock)
                const targetAngle = reward.stopAngle;
                const randomOffset = (Math.random() * 8) - 4;
                const finalAngle = 360 - targetAngle + randomOffset;
                // Hamesha aagay hi rotate ho
                currentRotation =
                    Math.ceil(currentRotation / 360) * 360 +
                    (360 * 8) +
                    finalAngle;
                wheelGroup.style.transition =
                    "transform 6s cubic-bezier(.17,.67,.18,1)";
                wheelGroup.style.transform =
                    `rotate(${currentRotation}deg)`;

                setTimeout(() => {
                    spinning = false;

                    if (reward.label === "$0") {
                        showModal(
                            "😔 Oops!",
                            `<strong>${reward.label}</strong>. Try Again!`,
                            "😔"
                        );
                    } else if (reward.label === "2 Spins") {
                        freeSpins += 2;
                        freeSpinsText.innerHTML = freeSpins;
                        showModal(
                            "🎉 Congratulations",
                            "You won <strong>2 Free Spins</strong>.",
                            "🎁"
                        );
                    } else {
                        showModal(
                            "🎉 Congratulations",
                            `You won <strong>${reward.label}</strong>.`,
                            "🏆"
                        );
                    }
                }, 6000);
            }

        });
    </script>
@endpush
