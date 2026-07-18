@extends('layouts.master')

@section('content')
    <section class="lottery_plans py-10">
        <div class="container">
            <div class="sec_heading text-center">
                <span class="sec_subtitle">Lucky Draw</span>
                <h2 class="mt-4">Participate in Lucky Draw</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7 mt-8 md:mt-12">
                @php
                    $lotteryPlans = [
                        [
                            'price' => '$1',
                            'prizes' => ['1st Prize: $7.50', '2nd Prize: $4.00 x2', '3rd Prize: $2.00 x3'],
                        ],
                        [
                            'price' => '$2',
                            'prizes' => ['1st Prize: $15.00', '2nd Prize: $8.00 x2', '3rd Prize: $4.00 x3'],
                        ],
                        [
                            'price' => '$3',
                            'prizes' => ['1st Prize: $22.50', '2nd Prize: $12.00 x2', '3rd Prize: $6.00 x3'],
                        ],
                    ];
                @endphp

                @foreach ($lotteryPlans as $index => $plan)
                    <div
                        class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-2 hover:border-green-500 hover:shadow-[0_0_35px_rgba(34,197,94,.18)]">

                        <div
                            class="absolute inset-0 opacity-0 duration-300 group-hover:opacity-100 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
                        </div>

                        <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                        <div class="relative z-10 p-4 md:p-8 text-center">

                            <div
                                class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl border border-green-500/20 bg-gradient-to-br from-green-500/20 to-orange-500/20 transition duration-300 group-hover:scale-110 group-hover:rotate-6">

                                <i class="fa-solid fa-ticket text-3xl text-green-500"></i>

                            </div>

                            <h3 class="mt-6 text-2xl font-bold">
                                Lucky Draw
                            </h3>

                            <div class="mt-3 flex items-end justify-center gap-2">

                                <h2 class="text-green-500 drop-shadow-[0_0_10px_rgba(34,197,94,.4)]">
                                    {{ $plan['price'] }}
                                </h2>

                                <span class="mb-1 opacity-60">
                                    / Ticket
                                </span>

                            </div>

                            <div
                                class="mx-auto mt-6 inline-flex items-center gap-3 rounded-2xl border border-orange-500/20 bg-orange-500/5 px-6 py-3">

                                <i class="fa-regular fa-clock text-orange-400"></i>

                                <span id="timer-{{ $index }}"
                                    class="font-mono text-xl font-bold tracking-[3px] text-orange-400">
                                    05:00
                                </span>

                            </div>

                        </div>

                        <div class="relative z-10 px-4 md:px-8 pb-4md:pb-8">

                            <div
                                class="rounded-2xl border border-green-500/10 bg-gradient-to-b from-brand-dark to-[#07131b] p-5">

                                <ul class="space-y-4">

                                    @foreach ($plan['prizes'] as $prize)
                                        <li class="flex items-center justify-between">

                                            <span class="text-sm opacity-70">
                                                {{ explode(':', $prize)[0] }}
                                            </span>

                                            <span
                                                class="rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-sm font-semibold text-orange-400">

                                                {{ explode(':', $prize)[1] }}

                                            </span>

                                        </li>
                                    @endforeach

                                </ul>

                            </div>

                            <button onclick="toggleModal('modal-{{ $index }}', true)"
                                class="mt-6 block w-full rounded-2xl bg-gradient-to-r from-green-500 to-orange-500 py-4 text-center font-semibold text-white shadow-lg shadow-green-500/20 transition duration-300 hover:scale-[1.02] hover:shadow-orange-500/30">

                                <i class="fa-solid fa-ticket mr-2"></i>

                                Buy Ticket

                            </button>

                            <p class="mt-4 text-center text-xs font-semibold uppercase tracking-[3px] text-orange-400">
                                Sales close in 5 minutes
                            </p>

                        </div>

                    </div>

                    <div id="modal-{{ $index }}"
                        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">

                        <div
                            class="w-full max-w-md rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8 text-center shadow-2xl">

                            <div
                                class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-green-500/20">

                                <i class="fa-solid fa-check text-4xl text-green-500"></i>

                            </div>

                            <h3 class="mb-2 text-2xl font-bold">
                                Success!
                            </h3>

                            <p class="mb-8 opacity-70">
                                You have successfully bought a ticket. Check your account in My Entries.
                            </p>

                            <div class="space-y-3">

                                <a href="{{ route('user-account') }}"
                                    class="block w-full rounded-xl bg-gradient-to-r from-green-500 to-orange-500 py-3 text-center font-semibold text-white">
                                    View My Tickets
                                </a>

                                <button onclick="toggleModal('modal-{{ $index }}', false)"
                                    class="w-full py-3 text-sm opacity-60 transition hover:opacity-100">
                                    Close
                                </button>

                            </div>

                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        function startTimer(duration, displayId) {
            const display = document.getElementById(displayId);

            // Agar element nahi mila to function yahin stop kar do
            if (!display) return;

            let timer = duration;

            setInterval(function() {
                let minutes = Math.floor(timer / 60);
                let seconds = timer % 60;

                display.textContent =
                    String(minutes).padStart(2, "0") +
                    ":" +
                    String(seconds).padStart(2, "0");

                timer--;

                if (timer < 0) {
                    timer = duration;
                }
            }, 1000);
        }
        @for ($i = 0; $i < 4; $i++)
            startTimer(300, 'timer-{{ $i }}');
        @endfor

        function toggleModal(id, show) {
            const modal = document.getElementById(id);
            if (!modal) return;

            if (show) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }
    </script>
@endpush
