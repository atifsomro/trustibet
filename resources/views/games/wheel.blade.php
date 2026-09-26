@php
    $wheelPackages = $packages ?? collect();
    $defaultWheel = ($resumePackageId ?? null)
        ? ($wheelPackages->firstWhere('id', $resumePackageId) ?? $wheelPackages->first())
        : $wheelPackages->first();
    $wheelSlices = $defaultWheel
        ? $defaultWheel->activePrizes->map(function ($prize, $index) {
            return [
                'id' => $prize->id,
                'label' => $prize->label,
                'color' => $prize->metaValue('color') ?: ['#ef4444', '#22c55e', '#3b82f6', '#f59e0b', '#06b6d4', '#9333ea'][$index % 6],
                'weight' => (int) $prize->weight,
                'prize_amount' => (float) $prize->prize_amount,
                'segment' => (int) ($prize->metaValue('segment') ?? $index),
            ];
        })->values()
        : collect();
@endphp

<div class="lucky-wheel">
    <div class="wheel_game_wrapper">
        <div class="grid lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 order-2 lg:order-1">
                <div class="bg-brand-surface border border-brand-border rounded-2xl p-3 sm:p-6">
                    <div class="flex items-center justify-between mb-8 flex-wrap gap-2">
                        <div class="text-center sm:text-start">
                            <h3>Lucky Wheel</h3>
                            <p class="text-gray-400 mt-2">Spin the wheel and win exciting rewards.</p>
                        </div>
                        <span class="text-green-500 text-center sm:text-start animate-pulse">Live Game</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="relative z-20">
                            <div class="w-0 h-0 border-l-18 border-r-18 border-t-32 border-l-transparent border-r-transparent border-t-brand-primary"></div>
                        </div>
                        <div class="relative -mt-2">
                            <div class="relative w-50 sm:w-80 md:w-96 lg:w-105 aspect-square">
                                <div class="absolute left-1/2 -translate-x-1/2 -top-4 z-20 w-0 h-0 border-l-16 border-r-16 border-t-28 border-l-transparent border-r-transparent border-t-red-500"></div>
                                <svg id="wheel" viewBox="0 0 420 420" class="duration-6000">
                                    <g id="wheelGroup"></g>
                                </svg>
                                <div class="absolute inset-0 rounded-full border-8 border-brand-primary pointer-events-none"></div>
                            </div>
                        </div>
                        <button id="spinWheel" type="button" class="btn-primary mt-8 min-w-45">Spin Now</button>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-4 order-1 lg:order-2">
                <div class="bg-brand-surface border border-brand-border rounded-2xl p-3 sm:p-6">
                    <h3 class="text-center sm:text-start">Game Panel</h3>
                    <div class="mt-6">
                        <label>Spins</label>
                        <div class="mt-2 rounded-xl bg-brand-dark border border-brand-border p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Free Spins</span>
                                <strong id="freeSpins">{{ $freeSpinsRemaining ?? 0 }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Spins Left</span>
                                <strong id="packageSpins">0</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">This Spin</span>
                                <strong id="paidSpins">$0.00</strong>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <div class="text-[12px] sm:text-sm md:text-base text-center sm:text-start">
                            Choose a spin package
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            @foreach ($wheelPackages as $pkg)
                                @php
                                    $spins = (int) $pkg->metaValue('spins', 1);
                                    $pkgPrizes = $pkg->activePrizes->map(function ($p, $i) {
                                        return [
                                            'id' => $p->id,
                                            'label' => $p->label,
                                            'color' => $p->metaValue('color') ?: ['#ef4444', '#22c55e', '#3b82f6', '#f59e0b', '#06b6d4', '#9333ea'][$i % 6],
                                            'weight' => (int) $p->weight,
                                            'prize_amount' => (float) $p->prize_amount,
                                            'segment' => (int) ($p->metaValue('segment') ?? $i),
                                        ];
                                    })->values();
                                @endphp
                                <button type="button"
                                    class="chance-package flex flex-col items-center text-center rounded-xl border border-brand-border p-2 sm:p-4 hover:border-brand-primary {{ $defaultWheel && $pkg->id === $defaultWheel->id ? 'border-brand-primary' : '' }}"
                                    data-package-id="{{ $pkg->id }}"
                                    data-fee="{{ $pkg->fee }}"
                                    data-allowance="{{ $spins }}"
                                    data-remaining="{{ (int) data_get($packageCredits ?? [], $pkg->id.'.remaining', 0) }}"
                                    data-prizes='@json($pkgPrizes)'>
                                    <h4>${{ number_format((float) $pkg->fee, 0) }}</h4>
                                    <p class="package-caption text-[10px] sm:text-sm text-gray-400 mt-1">
                                        @php $left = (int) data_get($packageCredits ?? [], $pkg->id.'.remaining', 0); @endphp
                                        @if ($left > 0)
                                            {{ $left }} left
                                        @else
                                            {{ $spins }} {{ $spins === 1 ? 'Spin' : 'Spins' }}
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
</div>

<div id="winPopup" class="fixed inset-0 hidden items-center justify-center bg-black/70 z-50">
    <div class="bg-brand-surface border border-brand-border rounded-2xl p-4 sm:p-8 w-[280px] sm:w-[320px] md:w-[380px] text-center">
        <div class="text-6xl mb-5">🎉</div>
        <h3 id="popupTitle">Congratulations</h3>
        <p id="popupReward" class="mt-3 text-gray-400">You Won</p>
        <button id="closePopup" type="button" class="btn-primary w-full mt-6">Continue</button>
    </div>
</div>

@push('scripts')
    <script>
        let currentRotation = 0;
        let spinning = false;

        document.addEventListener("DOMContentLoaded", () => {
            let freeSpins = {{ (int) ($freeSpinsRemaining ?? 0) }};
            const serverResumeId = @json($resumePackageId ?? null);
            const sessionUrl = @json(route('games.session', $slug));
            let selectedPackageId = serverResumeId || {{ $defaultWheel?->id ?? 'null' }};
            let slices = @json($wheelSlices);
            const packageSpinsText = document.getElementById("packageSpins");
            const playUrl = @json(route('games.play', $slug));
            const popup = document.getElementById("winPopup");
            const popupTitle = document.getElementById("popupTitle");
            const popupReward = document.getElementById("popupReward");
            const freeSpinsText = document.getElementById("freeSpins");
            const paidSpinsText = document.getElementById("paidSpins");
            const walletBalance = document.getElementById("wallet-balance");
            const walletBet = document.getElementById("wallet-bet");
            const walletPrize = document.getElementById("wallet-prize");
            const wheelGroup = document.getElementById("wheelGroup");

            wheelGroup.style.transformBox = "fill-box";
            wheelGroup.style.transformOrigin = "center";

            function showModal(title, message, emoji = "🎉", type = "info") {
                const isSuccess = type === "success" || emoji === "🏆" || emoji === "🎁" || emoji === "🎉";
                const isError = type === "error" || emoji === "😔";
                showGameNotice({
                    type: isSuccess ? "success" : (isError ? "error" : "warning"),
                    title: title.replace(/^[^\w]+/, "").trim() || title,
                    message: message,
                    emoji: emoji,
                });
            }

            document.getElementById("closePopup").onclick = () => {
                popup.classList.add("hidden");
                popup.classList.remove("flex");
            };

            function saveWheelSession() {
                if (!selectedPackageId) return;
                fetch(sessionUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ package_id: selectedPackageId })
                }).catch(() => {});
            }

            function selectedPackageButton() {
                return document.querySelector(`.chance-package[data-package-id="${selectedPackageId}"]`);
            }

            function refreshSpinCost() {
                const btn = selectedPackageButton();
                const remaining = btn ? Number(btn.dataset.remaining || 0) : 0;
                const allowance = btn ? Number(btn.dataset.allowance || 0) : 0;
                const fee = btn ? Number(btn.dataset.fee || 0) : 0;

                if (packageSpinsText) {
                    if (remaining > 0) {
                        packageSpinsText.textContent = remaining + " left";
                    } else if (allowance > 0) {
                        packageSpinsText.textContent = allowance + (allowance === 1 ? " spin" : " spins");
                    } else {
                        packageSpinsText.textContent = "0";
                    }
                }

                const caption = btn ? btn.querySelector(".package-caption") : null;
                if (caption) {
                    caption.textContent = remaining > 0
                        ? (remaining + " left")
                        : (allowance + (allowance === 1 ? " Spin" : " Spins"));
                }

                let chargeLabel = "$" + fee.toFixed(2);
                if (freeSpins > 0) {
                    chargeLabel = "Free";
                } else if (remaining > 0) {
                    chargeLabel = "Included";
                }

                paidSpinsText.textContent = chargeLabel;
                if (walletBet) {
                    const charge = (freeSpins > 0 || remaining > 0) ? 0 : fee;
                    walletBet.textContent = "$" + charge.toFixed(2);
                }
            }

            function drawWheel(data) {
                slices = data;
                wheelGroup.innerHTML = "";
                const count = Math.max(slices.length, 1);
                const step = 360 / count;
                const cx = 210, cy = 210, r = 190;

                slices.forEach((slice, index) => {
                    const start = index * step;
                    slice.stopAngle = start + (step / 2);
                    const end = start + step;
                    const x1 = cx + r * Math.cos((start - 90) * Math.PI / 180);
                    const y1 = cy + r * Math.sin((start - 90) * Math.PI / 180);
                    const x2 = cx + r * Math.cos((end - 90) * Math.PI / 180);
                    const y2 = cy + r * Math.sin((end - 90) * Math.PI / 180);
                    const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
                    path.setAttribute("d", `M ${cx} ${cy} L ${x1} ${y1} A ${r} ${r} 0 0 1 ${x2} ${y2} Z`);
                    path.setAttribute("fill", slice.color || "#3b82f6");
                    path.setAttribute("stroke", "#111827");
                    path.setAttribute("stroke-width", "2");
                    wheelGroup.appendChild(path);

                    const angle = start + (step / 2);
                    const tx = cx + (r - 65) * Math.cos((angle - 90) * Math.PI / 180);
                    const ty = cy + (r - 65) * Math.sin((angle - 90) * Math.PI / 180);
                    const text = document.createElementNS("http://www.w3.org/2000/svg", "text");
                    text.setAttribute("x", tx);
                    text.setAttribute("y", ty);
                    text.setAttribute("fill", "#fff");
                    text.setAttribute("font-size", count > 10 ? "12" : "16");
                    text.setAttribute("font-weight", "700");
                    text.setAttribute("text-anchor", "middle");
                    text.setAttribute("dominant-baseline", "middle");
                    text.setAttribute("transform", `rotate(${angle + 90} ${tx} ${ty})`);
                    text.textContent = slice.label;
                    wheelGroup.appendChild(text);
                });

                const center = document.createElementNS("http://www.w3.org/2000/svg", "circle");
                center.setAttribute("cx", cx);
                center.setAttribute("cy", cy);
                center.setAttribute("r", "48");
                center.setAttribute("fill", "#0f172a");
                center.setAttribute("stroke", "#38bdf8");
                center.setAttribute("stroke-width", "6");
                wheelGroup.appendChild(center);

                const centerText = document.createElementNS("http://www.w3.org/2000/svg", "text");
                centerText.setAttribute("x", cx);
                centerText.setAttribute("y", cy + 6);
                centerText.setAttribute("fill", "#fff");
                centerText.setAttribute("font-size", "18");
                centerText.setAttribute("font-weight", "700");
                centerText.setAttribute("text-anchor", "middle");
                centerText.textContent = "Luck";
                wheelGroup.appendChild(centerText);
            }

            const resumed = selectedPackageButton();
            if (resumed) {
                document.querySelectorAll(".chance-package").forEach(b => b.classList.remove("border-brand-primary"));
                resumed.classList.add("border-brand-primary");
                try {
                    slices = JSON.parse(resumed.dataset.prizes || "[]");
                } catch (e) {}
            } else if (selectedPackageId) {
                selectedPackageId = {{ $defaultWheel?->id ?? 'null' }};
            }
            drawWheel(slices);

            document.querySelectorAll(".chance-package").forEach(btn => {
                btn.addEventListener("click", () => {
                    document.querySelectorAll(".chance-package").forEach(b => b.classList.remove("border-brand-primary"));
                    btn.classList.add("border-brand-primary");
                    selectedPackageId = Number(btn.dataset.packageId);
                    saveWheelSession();
                    refreshSpinCost();
                    try {
                        drawWheel(JSON.parse(btn.dataset.prizes || "[]"));
                    } catch (e) {}
                });
            });

            refreshSpinCost();

            document.getElementById("spinWheel").onclick = () => {
                if (spinning) return;
                if (!selectedPackageId) {
                    showModal("No Package", "Select a spin package first.", "😔");
                    return;
                }

                const useFree = freeSpins > 0;
                spinning = true;

                fetch(playUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            package_id: selectedPackageId,
                            use_free_spin: useFree,
                            idempotency_key: crypto.randomUUID()
                        })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || "Spin failed");
                        }
                        return data;
                    })
                    .then(data => {
                        freeSpins = Number(data.free_spins_remaining || 0);
                        freeSpinsText.textContent = freeSpins;
                        const selectedBtn = selectedPackageButton();
                        if (selectedBtn && data.package_credits_remaining != null) {
                            selectedBtn.dataset.remaining = String(data.package_credits_remaining);
                        }
                        refreshSpinCost();
                        if (typeof data.balance !== "undefined" && typeof syncWalletBalance === "function") {
                            syncWalletBalance(data.balance);
                        }

                        const prizeId = data.play?.outcome ? null : null;
                        const label = data.play?.label || data.play?.outcome?.label || "$0";
                        const prizeAmount = Number(data.play?.prize_amount || 0);
                        let index = slices.findIndex(s => s.label === label);
                        if (index < 0 && data.play?.outcome?.segment != null) {
                            index = Number(data.play.outcome.segment) % Math.max(slices.length, 1);
                        }
                        if (index < 0) index = 0;

                        const reward = slices[index] || { stopAngle: 0, label };
                        const targetAngle = reward.stopAngle || ((360 / Math.max(slices.length, 1)) * index + 15);
                        const randomOffset = (Math.random() * 8) - 4;
                        const finalAngle = 360 - targetAngle + randomOffset;
                        currentRotation = Math.ceil(currentRotation / 360) * 360 + (360 * 8) + finalAngle;
                        wheelGroup.style.transition = "transform 6s cubic-bezier(.17,.67,.18,1)";
                        wheelGroup.style.transform = `rotate(${currentRotation}deg)`;

                        if (walletPrize) walletPrize.textContent = "$" + prizeAmount.toFixed(2);

                        setTimeout(() => {
                            spinning = false;
                            if (prizeAmount <= 0) {
                                showModal("😔 Oops!", `<strong>${label}</strong>. Try Again!`, "😔");
                            } else {
                                showModal("🎉 Congratulations", `You won <strong>${label}</strong>.`, "🏆");
                            }
                        }, 6000);
                    })
                    .catch(err => {
                        spinning = false;
                        showModal("Unable to spin", err.message || "Try again.", "😔");
                    });
            };
        });
    </script>
@endpush
