@extends('layouts.master')

@section('content')

    <section class="lottery_plans py-10">

        <div class="container">

            {{-- Heading --}}
            <div class="sec_heading text-center">

                <span class="sec_subtitle">
                    Lucky Draw
                </span>

                <h2 class="mt-4">
                    Participate in Lucky Draw
                </h2>

                <p class="mx-auto mt-3 max-w-2xl opacity-70">
                    Choose your lucky draw, purchase your tickets and get a chance
                    to win exciting prizes.
                </p>

            </div>

            {{-- Success Message --}}
            @if (session('success'))

                <div
                    class="mx-auto mt-8 max-w-3xl rounded-2xl border border-green-500/20 bg-green-500/10 px-5 py-4 text-sm text-green-400">

                    {{ session('success') }}

                </div>

            @endif

            {{-- Error Messages --}}
            @if ($errors->any())

                <div
                    class="mx-auto mt-8 max-w-3xl rounded-2xl border border-red-500/20 bg-red-500/10 px-5 py-4 text-sm text-red-400">

                    <ul class="space-y-1">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif

            @if ($lotteries->count())

                <div
                    class="mt-8 grid grid-cols-1 gap-7 md:grid-cols-2 lg:grid-cols-3 md:mt-12">

                    @foreach ($lotteries as $lottery)

                        @include('lottery.partials.lottery-card', [
                            'lottery' => $lottery,
                        ])

                    @endforeach

                </div>

            @else

                <div
                    class="mx-auto mt-12 max-w-xl rounded-3xl border border-brand-border bg-brand-surface p-10 text-center">

                    <div
                        class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl border border-green-500/20 bg-gradient-to-br from-green-500/20 to-orange-500/20">

                        <i class="fa-solid fa-ticket text-3xl text-green-500"></i>

                    </div>

                    <h3 class="mt-6 text-2xl font-bold">
                        No Lotteries Available
                    </h3>

                    <p class="mt-3 opacity-70">
                        There are currently no active lotteries available.
                        Please check back later.
                    </p>

                </div>

            @endif

            {{-- Completed Lottery Results --}}
            @if ($completedLotteries->count())

                <div class="mt-16">

                    <div class="sec_heading text-center">
                        <span class="sec_subtitle">
                            Previous Draws
                        </span>

                        <h2 class="mt-4">
                            Lottery Results
                        </h2>

                        <p class="mx-auto mt-3 max-w-2xl opacity-70">
                            View the winners and prize results of completed lotteries.
                        </p>
                    </div>

                    <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

                        @foreach ($completedLotteries as $completedLottery)

                            @php
                                $completedDraw = $completedLottery->latestCompletedDraw;
                            @endphp

                            <div class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

                                <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                                <div class="p-6">

                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-xl font-bold">
                                                {{ $completedLottery->title }}
                                            </h3>

                                            <p class="mt-1 text-xs opacity-50">
                                                Draw #{{ $completedDraw?->id ?? '—' }}
                                                · Drawn {{ $completedDraw?->completed_at?->format('d M Y h:i A') ?? '—' }}
                                            </p>
                                        </div>

                                        <span class="rounded-full border border-green-500/20 bg-green-500/10 px-3 py-1 text-xs font-semibold text-green-400">
                                            Completed
                                        </span>
                                    </div>

                                    <div class="mt-5 space-y-3">
                                        @php
                                            $resultPrizes = [
                                                'first' => ['label' => '1st Prize', 'amount' => $completedLottery->first_prize],
                                                'second' => ['label' => '2nd Prize', 'amount' => $completedLottery->second_prize],
                                                'third' => ['label' => '3rd Prize', 'amount' => $completedLottery->third_prize],
                                                'fourth' => ['label' => '4th Prize', 'amount' => $completedLottery->fourth_prize],
                                                'fifth' => ['label' => '5th Prize', 'amount' => $completedLottery->fifth_prize],
                                            ];
                                            $resultWinners = $completedDraw?->winners?->keyBy('prize_category') ?? collect();
                                        @endphp

                                        @foreach ($resultPrizes as $category => $prize)
                                            @php($winner = $resultWinners->get($category))

                                            <div class="flex items-center justify-between rounded-xl border border-brand-border bg-brand-dark px-4 py-3">
                                                <div>
                                                    <span class="block text-sm opacity-70">{{ $prize['label'] }}</span>
                                                    @if ($winner)
                                                        <span class="mt-1 block text-xs font-mono text-green-400">
                                                            {{ $winner->ticket?->ticket_number ?? '—' }}
                                                        </span>
                                                    @else
                                                        <span class="mt-1 block text-xs opacity-40">No winner</span>
                                                    @endif
                                                </div>

                                                <strong class="text-orange-400">
                                                    {{ $completedLottery->currency }} {{ number_format((float) $prize['amount'], 2) }}
                                                </strong>
                                            </div>
                                        @endforeach
                                    </div>

                                    <a
                                        href="{{ route('lotteries.show', $completedLottery) }}"
                                        class="mt-5 block rounded-2xl bg-gradient-to-r from-green-500 to-orange-500 py-3 text-center font-semibold text-white transition hover:scale-[1.01]">
                                        <i class="fa-solid fa-trophy mr-2"></i>
                                        View Full Results
                                    </a>

                                </div>
                            </div>

                        @endforeach

                    </div>
                </div>

            @endif

        </div>

    </section>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const countdowns = document.querySelectorAll(
        '.lottery-countdown'
    );

    countdowns.forEach(function (element) {

        const endDate = new Date(
            element.dataset.end
        ).getTime();

        function updateCountdown() {

            const now = new Date().getTime();

            const difference = endDate - now;

            if (difference <= 0) {

                element.textContent = '00:00:00';

                return;
            }

            const hours = Math.floor(
                difference / (1000 * 60 * 60)
            );

            const minutes = Math.floor(
                (difference % (1000 * 60 * 60))
                / (1000 * 60)
            );

            const seconds = Math.floor(
                (difference % (1000 * 60))
                / 1000
            );

            element.textContent =
                String(hours).padStart(2, '0') +
                ':' +
                String(minutes).padStart(2, '0') +
                ':' +
                String(seconds).padStart(2, '0');
        }

        updateCountdown();

        setInterval(
            updateCountdown,
            1000
        );

    });

});
</script>
@endpush