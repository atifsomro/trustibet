@extends('layouts.master')

@section('content')
    <section class="participate py-14 lg:py-20">
        <div class="container">
            <div class="rounded-3xl border border-brand-border bg-brand-surface overflow-hidden">
                <div class="grid lg:grid-cols-2">
                    {{-- Prize Image --}}
                    <div
                        class="p-8 lg:p-12 flex flex-col items-center justify-center border-b lg:border-b-0 lg:border-r border-brand-border">
                        <img src="{{ $draw['prize_image'] }}" alt="{{ $draw['prize_name'] }}" class="w-full max-w-md">
                        <h3 class="p-2 bg-green-400 text-brand-light rounded">See Prize</h3>
                        <div class="rounded-3xl border border-brand-border bg-brand-surface">
                            <div class="flex flex-col">
                                <small class="uppercase tracking-[3px] text-brand-primary">
                                    Hurry Up
                                </small>
                                <h3 class="mt-2">
                                    Draw Ends In
                                </h3>
                                <p class="mt-2">
                                    Don't miss your chance to win the
                                    <span class="text-brand-primary font-semibold">
                                        {{ $draw['prize_name'] }}
                                    </span>.
                                    Join now before the countdown reaches zero.
                                </p>
                            </div>

                            {{-- Right --}}
                            <div class="grid grid-cols-4 gap-4">
                                <div class="rounded-2xl border border-brand-border bg-brand-dark text-center py-5">
                                    <h2 id="days" class="text-brand-primary">
                                        00
                                    </h2>
                                    <span class="uppercase text-xs tracking-widest">
                                        Days
                                    </span>
                                </div>
                                <div class="rounded-2xl border border-brand-border bg-brand-dark text-center py-5">
                                    <h2 id="hours" class="text-brand-primary">
                                        00
                                    </h2>
                                    <span class="uppercase text-xs tracking-widest">
                                        Hours
                                    </span>
                                </div>
                                <div class="rounded-2xl border border-brand-border bg-brand-dark text-center py-5">
                                    <h2 id="minutes" class="text-brand-primary">
                                        00
                                    </h2>
                                    <span class="uppercase text-xs tracking-widest">
                                        Minutes
                                    </span>
                                </div>
                                <div class="rounded-2xl border border-brand-border bg-brand-dark text-center py-5">
                                    <h2 id="seconds" class="text-brand-primary">
                                        00
                                    </h2>
                                    <span class="uppercase text-xs tracking-widest">
                                        Seconds
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Details --}}
                    <div class="p-8 lg:p-12">
                        <small class="uppercase tracking-[3px] text-brand-primary">
                            {{ $draw['game']->badge ?: 'Limited Lucky Draw' }}
                        </small>
                        <h2 class="mt-3">
                            {{ $draw['prize_name'] }}
                        </h2>
                        <p class="mt-4">
                            Pay only <strong class="text-white">{{ $draw['fee_label'] }}</strong>
                            and get a chance to win this amazing prize.
                        </p>
                        <div class="mt-8 space-y-5">
                            <div class="flex justify-between border-b border-brand-border pb-4">
                                <span>Prize Value</span>
                                <strong class="text-brand-primary">
                                    {{ $draw['currency'] }} {{ number_format($draw['prize_value']) }}
                                </strong>
                            </div>
                            <div class="flex justify-between border-b border-brand-border pb-4">
                                <span>Entry Fee</span>
                                <strong>
                                    {{ $draw['fee_label'] }}
                                </strong>
                            </div>
                            <div class="flex justify-between border-b border-brand-border pb-4">
                                <span>Participants</span>
                                <strong>
                                    {{ number_format($draw['entries']) }}
                                </strong>
                            </div>
                            <div class="flex justify-between border-b border-brand-border pb-4">
                                <span>Draw Date</span>
                                <strong>
                                    {{ $draw['ends_at'] ? $draw['ends_at']->format('d M Y, h:i A') : 'Not scheduled' }}
                                </strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Status</span>
                                <span class="{{ $draw['is_open'] ? 'text-green-400' : 'text-orange-400' }}">
                                    {{ $draw['is_open'] ? '● Live' : '● Closed' }}
                                </span>
                            </div>
                        </div>
                        {{-- Terms --}}
                        <label class="mt-8 flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="agreeTerms" class="mt-1">
                            <span>
                                I agree to the Terms & Conditions and understand
                                that participation cannot be cancelled after payment.
                            </span>
                        </label>
                        <small id="termsError" class="block mt-2 text-red-500">
                        </small>
                        @if ($draw['already_joined'])
                            <button type="button" class="btn-primary w-full justify-center mt-8" disabled>
                                <i class="fa-solid fa-circle-check mr-2"></i>
                                You're in this draw
                            </button>
                        @elseif (! $draw['is_open'] || ! $draw['package'])
                            <button type="button" class="btn-primary w-full justify-center mt-8" disabled>
                                Draw closed
                            </button>
                        @elseif (! $user)
                            <a href="{{ route('auth.login') }}" class="btn-primary w-full justify-center mt-8">
                                <i class="fa-solid fa-ticket mr-2"></i>
                                Login to join
                            </a>
                        @else
                            <button id="participateBtn" class="btn-primary w-full justify-center mt-8">
                                <i class="fa-solid fa-ticket mr-2"></i>
                                Pay {{ $draw['fee_label'] }} & Join Draw
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Confirm Participation Modal --}}
    <div id="confirmModal" class="fixed inset-0 z-99 hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div class="w-full max-w-md rounded-3xl border border-brand-border bg-brand-surface p-8">
            <div class="text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-brand-primary/15">
                    <i class="fa-solid fa-ticket text-4xl text-brand-primary"></i>
                </div>
                <h3 class="mt-6">
                    Confirm Participation
                </h3>
                <p class="mt-3">
                    Please review your participation details before continuing.
                </p>
            </div>
            <div class="mt-8 space-y-4">
                <div class="flex justify-between">
                    <span>Prize</span>
                    <strong>{{ $draw['prize_name'] }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Entry Fee</span>
                    <strong class="text-brand-primary">
                        {{ $draw['fee_label'] }}
                    </strong>
                </div>
                <div class="flex justify-between">
                    <span>Wallet Balance</span>
                    <strong id="walletBalance">
                        ${{ number_format((float) $balance, 2) }}
                    </strong>
                </div>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-4">
                <button id="cancelParticipation" class="btn-secondary justify-center">
                    Cancel
                </button>
                <button id="confirmParticipation" class="btn-primary justify-center">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <section class="entry-progress">
        <div class="container">
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-8 lg:p-12">
                <div class="grid lg:grid-cols-12 gap-10 items-center">
                    {{-- Left --}}
                    <div class="lg:col-span-7">
                        <small class="uppercase tracking-[3px] text-green-400 font-bold">
                            Live Statistics
                        </small>
                        <h2 class="mt-3">
                            Entry Progress
                        </h2>
                        <p class="mt-4">
                            Thousands of players have already secured their spot.
                            Don't miss your chance to become our next lucky winner.
                        </p>
                        <div class="mt-8 flex justify-between">
                            <span class="font-medium">
                                <span id="joinedPlayers" class="text-green-400 font-bold">
                                    {{ number_format($draw['entries']) }}
                                </span>
                                Players Joined
                            </span>
                            <span class="font-semibold">
                                {{ $draw['percent'] }}%
                            </span>
                        </div>
                        {{-- Progress Bar --}}
                        <div class="mt-4 h-4 overflow-hidden rounded-full bg-brand-dark">
                            <div id="entryProgress"
                                class="h-full rounded-full bg-brand-primary transition-all duration-1000"
                                style="width:{{ $draw['percent'] }}%">
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span>
                                {{ number_format($draw['entries']) }}
                                @if ($draw['max_entries'] > 0)
                                    / {{ number_format($draw['max_entries']) }} Entries
                                @else
                                    Entries
                                @endif
                            </span>
                            @if ($draw['remaining'] !== null)
                                <span class="text-orange-400">
                                    🔥 {{ number_format($draw['remaining']) }} Remaining
                                </span>
                            @endif
                        </div>
                        <div
                            class="mt-5 inline-flex items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2">

                            <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>

                            <span id="liveJoinMessage" class="text-sm text-green-400">

                                3 players joined in the last minute

                            </span>

                        </div>
                    </div>
                    {{-- Right --}}
                    <div class="lg:col-span-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-2xl border border-brand-border bg-brand-dark p-6 text-center">
                                <i class="fa-solid fa-users text-3xl text-brand-primary"></i>
                                <h3 class="mt-4">
                                    {{ number_format($draw['entries']) }}
                                </h3>
                                <p class="mt-2 text-sm">
                                    Participants
                                </p>
                            </div>
                            <div class="rounded-2xl border border-brand-border bg-brand-dark p-6 text-center">
                                <i class="fa-solid fa-ticket text-3xl text-brand-primary"></i>
                                <h3 class="mt-4">
                                    {{ $draw['max_entries'] > 0 ? number_format($draw['max_entries']) : 'Open' }}
                                </h3>
                                <p class="mt-2 text-sm">
                                    Total Entries
                                </p>
                            </div>
                            <div class="rounded-2xl border border-brand-border bg-brand-dark p-6 text-center">
                                <i class="fa-solid fa-trophy text-3xl text-brand-primary"></i>
                                <h3 class="mt-4">
                                    {{ $draw['winner_count'] }}
                                </h3>
                                <p class="mt-2 text-sm">
                                    Lucky Winner
                                </p>
                            </div>
                            <div class="rounded-2xl border border-brand-border bg-brand-dark p-6 text-center">
                                <i class="fa-solid fa-coins text-3xl text-brand-primary"></i>
                                <h3 class="mt-4">
                                    {{ $draw['fee_label'] }}
                                </h3>
                                <p class="mt-2 text-sm">
                                    Entry Fee
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const endAt = @json(optional($draw['ends_at'])->toIso8601String());
            const daysEl = document.getElementById("days");
            const hoursEl = document.getElementById("hours");
            const minutesEl = document.getElementById("minutes");
            const secondsEl = document.getElementById("seconds");

            function pad(value) {
                return String(value).padStart(2, "0");
            }

            function tickCountdown() {
                if (!endAt || !daysEl) return;
                const diff = Math.max(0, new Date(endAt).getTime() - Date.now());
                const total = Math.floor(diff / 1000);
                daysEl.textContent = pad(Math.floor(total / 86400));
                hoursEl.textContent = pad(Math.floor((total % 86400) / 3600));
                minutesEl.textContent = pad(Math.floor((total % 3600) / 60));
                secondsEl.textContent = pad(total % 60);
            }

            tickCountdown();
            setInterval(tickCountdown, 1000);

            const checkbox = document.getElementById("agreeTerms");
            const error = document.getElementById("termsError");
            const btn = document.getElementById("participateBtn");
            const modal = document.getElementById("confirmModal");
            const cancel = document.getElementById("cancelParticipation");
            const confirm = document.getElementById("confirmParticipation");
            if (!btn || !modal) return;

            btn.addEventListener("click", () => {
                if (!checkbox.checked) {
                    error.textContent = "Please accept Terms & Conditions.";
                    return;
                }
                error.textContent = "";
                modal.classList.remove("hidden");
                modal.classList.add("flex");
                document.body.classList.add("overflow-hidden");
            });

            function closeModal() {
                modal.classList.remove("flex");
                modal.classList.add("hidden");
                document.body.classList.remove("overflow-hidden");
            }

            cancel?.addEventListener("click", closeModal);
            modal.addEventListener("click", (e) => {
                if (e.target === modal) closeModal();
            });
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") closeModal();
            });

            confirm?.addEventListener("click", async () => {
                confirm.disabled = true;
                try {
                    const response = await fetch(@json(route('games.play', $draw['game']->slug)), {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            package_id: @json($draw['package']?->id),
                            idempotency_key: crypto.randomUUID(),
                        }),
                    });
                    const data = await response.json();
                    if (!response.ok || data.success === false) {
                        const message = data.message
                            || Object.values(data.errors || {})[0]?.[0]
                            || "Unable to join this draw.";
                        error.textContent = message;
                        closeModal();
                        return;
                    }
                    window.location.reload();
                } catch (err) {
                    error.textContent = "Unable to join this draw.";
                    closeModal();
                } finally {
                    confirm.disabled = false;
                }
            });
        });
    </script>
@endpush
