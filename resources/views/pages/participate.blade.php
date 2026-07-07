@extends('layouts.master')

@section('content')
    <section class="participate py-14 lg:py-20">
        <div class="container">
            <div class="rounded-3xl border border-brand-border bg-brand-surface overflow-hidden">
                <div class="grid lg:grid-cols-2">
                    {{-- Prize Image --}}
                    <div
                        class="p-8 lg:p-12 flex flex-col items-center justify-center border-b lg:border-b-0 lg:border-r border-brand-border">
                        <img src="{{ asset('images/draw/prize.png') }}" alt="Prize" class="w-full max-w-md">
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
                                        Honda CG125
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
                            One Rupee Lucky Draw
                        </small>
                        <h2 class="mt-3">
                            Suzuki Bic
                        </h2>
                        <p class="mt-4">
                            Pay only <strong class="text-white">Rs.1</strong>
                            and get a chance to win this amazing prize.
                        </p>
                        <div class="mt-8 space-y-5">
                            <div class="flex justify-between border-b border-brand-border pb-4">
                                <span>Prize Value</span>
                                <strong class="text-brand-primary">
                                    Rs. 280,000
                                </strong>
                            </div>
                            <div class="flex justify-between border-b border-brand-border pb-4">
                                <span>Entry Fee</span>
                                <strong>
                                    Rs. 1
                                </strong>
                            </div>
                            <div class="flex justify-between border-b border-brand-border pb-4">
                                <span>Participants</span>
                                <strong>
                                    28,451
                                </strong>
                            </div>
                            <div class="flex justify-between border-b border-brand-border pb-4">
                                <span>Draw Date</span>
                                <strong>
                                    30 July 2026
                                </strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Status</span>
                                <span class="text-green-400">
                                    ● Live
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
                        <button id="participateBtn" class="btn-primary w-full justify-center mt-8">
                            <i class="fa-solid fa-ticket mr-2"></i>
                            Pay Rs.1 & Join Draw
                        </button>
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
                    <strong>Honda CG125</strong>
                </div>
                <div class="flex justify-between">
                    <span>Entry Fee</span>
                    <strong class="text-brand-primary">
                        Rs.1
                    </strong>
                </div>
                <div class="flex justify-between">
                    <span>Wallet Balance</span>
                    <strong>
                        Rs.500
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
                                    28,451
                                </span>
                                Players Joined
                            </span>
                            <span class="font-semibold">
                                57%
                            </span>
                        </div>
                        {{-- Progress Bar --}}
                        <div class="mt-4 h-4 overflow-hidden rounded-full bg-brand-dark">
                            <div id="entryProgress"
                                class="h-full rounded-full bg-brand-primary transition-all duration-1000" style="width:57%">
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span>
                                28,451 / 50,000 Entries
                            </span>
                            <span class="text-orange-400">
                                🔥 21,549 Remaining
                            </span>
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
                                    28,451
                                </h3>
                                <p class="mt-2 text-sm">
                                    Participants
                                </p>
                            </div>
                            <div class="rounded-2xl border border-brand-border bg-brand-dark p-6 text-center">
                                <i class="fa-solid fa-ticket text-3xl text-brand-primary"></i>
                                <h3 class="mt-4">
                                    50,000
                                </h3>
                                <p class="mt-2 text-sm">
                                    Total Entries
                                </p>
                            </div>
                            <div class="rounded-2xl border border-brand-border bg-brand-dark p-6 text-center">
                                <i class="fa-solid fa-trophy text-3xl text-brand-primary"></i>
                                <h3 class="mt-4">
                                    1
                                </h3>
                                <p class="mt-2 text-sm">
                                    Lucky Winner
                                </p>
                            </div>
                            <div class="rounded-2xl border border-brand-border bg-brand-dark p-6 text-center">
                                <i class="fa-solid fa-coins text-3xl text-brand-primary"></i>
                                <h3 class="mt-4">
                                    Rs.1
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
        // ===== PARTICIPATE PAGE =====
        document.addEventListener("DOMContentLoaded", () => {
            try {
                const checkbox = document.getElementById("agreeTerms");
                const error = document.getElementById("termsError");
                const btn = document.getElementById("participateBtn");
                const modal = document.getElementById("confirmModal");
                const cancel = document.getElementById("cancelParticipation");
                const confirm = document.getElementById("confirmParticipation");
                if (!btn) return;
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
                modal?.addEventListener("click", (e) => {
                    if (e.target === modal) {
                        closeModal();
                    }
                });
                document.addEventListener("keydown", (e) => {
                    if (e.key === "Escape") {
                        closeModal();
                    }
                });
                confirm?.addEventListener("click", () => {
                    closeModal();
                    // Backend payment yahan call hogi.
                });
            } catch (err) {
                console.error(err);
            }
        });
    </script>
@endpush
