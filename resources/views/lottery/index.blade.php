@extends('layouts.master')

@section('content')

    <section class="lottery_plans py-10">
        <div class="container">
            {{-- Heading --}}
            <div class="sec_heading text-center flex flex-col items-center content-center gap-1 mb-1">
                {{-- <h2 class="mt-4">
                    Participate in Lucky Draw
                </h2>

                <p class="mx-auto mt-3 max-w-2xl opacity-70">
                    Choose your lucky draw, purchase your tickets and get a chance
                    to win exciting prizes.
                </p> --}}
                <span class="sec_subtitle">
                    Participate in Lucky Draw
                </span>
                @auth
                    <a href="{{ route('lotteries.history') }}"
                        class="inline-flex items-center gap-2 text-sm text-green-400 hover:underline">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        My Lottery History
                    </a>
                @endauth
            </div>
            @include('lottery.partials.new-round-banner')
            @include('lottery.partials.user-balance')
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
                    class="mt-8 grid grid-cols-1 gap-7 md:mt-12 {{ $lotteries->count() === 1 ? 'mx-auto max-w-md' : ($lotteries->count() === 2 ? 'mx-auto max-w-4xl md:grid-cols-2' : 'md:grid-cols-2 lg:grid-cols-3') }}">

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

            {{-- Previous Draw Results (one card per lottery) --}}
            {{-- @if (($resultLotteries ?? collect())->count())
                <div class="mt-10">

                    <div class="sec_heading text-center">
                        <span class="sec_subtitle">
                            Lottery Results Previous Draws
                        </span>
                    </div>

                    <div
                        class="mt-8 grid grid-cols-1 gap-6 {{ $resultLotteries->count() === 1 ? 'mx-auto max-w-md' : ($resultLotteries->count() === 2 ? 'mx-auto max-w-4xl md:grid-cols-2' : 'md:grid-cols-2 lg:grid-cols-3') }}">

                        @foreach ($resultLotteries as $resultLottery)
                            @php
                                $latestDraw = $resultLottery->latestAnnouncedDraw;
                                $roundCount = (int) ($resultLottery->announced_draws_count ?? 0);
                            @endphp

                            @if (!$latestDraw)
                                @continue
                            @endif

                            <div class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

                                <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500">
                                </div>

                                <div class="p-2 md:p-4">

                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-sm font-bold">
                                                {{ $resultLottery->title }}
                                            </h3>

                                            <p class="mt-1 text-xs opacity-50">
                                                {{ $roundCount }}
                                                {{ \Illuminate\Support\Str::plural('round', $roundCount) }} completed
                                                · Latest {{ $latestDraw->completed_at?->format('d M Y h:i A') ?? '—' }}
                                            </p>
                                        </div>

                                        <span
                                            class="rounded-full border border-green-500/20 bg-green-500/10 px-2 py-1 text-xs font-semibold text-green-400">
                                            Drawn
                                        </span>
                                    </div>

                                    <div class="mt-2 space-y-2">
                                        @php
                                            $resultPrizes = [
                                                'first' => [
                                                    'label' => '1st Prize',
                                                    'amount' => $resultLottery->first_prize,
                                                ],
                                                'second' => [
                                                    'label' => '2nd Prize',
                                                    'amount' => $resultLottery->second_prize,
                                                ],
                                                'third' => [
                                                    'label' => '3rd Prize',
                                                    'amount' => $resultLottery->third_prize,
                                                ],
                                                'fourth' => [
                                                    'label' => '4th Prize',
                                                    'amount' => $resultLottery->fourth_prize,
                                                ],
                                                'fifth' => [
                                                    'label' => '5th Prize',
                                                    'amount' => $resultLottery->fifth_prize,
                                                ],
                                            ];
                                            $personal = $latestDraw->personalResult(auth('web')->id());
                                        @endphp

                                        @foreach ($resultPrizes as $category => $prize)
                                            <div
                                                class="flex items-center justify-between rounded-xl border border-brand-border bg-brand-dark px-2 py-2">
                                                <div>
                                                    <span class="block text-xs opacity-70">{{ $prize['label'] }}</span>
                                                </div>

                                                <strong class="text-xs text-orange-400">
                                                    {{ $resultLottery->currency }}
                                                    {{ number_format((float) $prize['amount'], 2) }}
                                                </strong>
                                            </div>
                                        @endforeach

                                        <div
                                            class="rounded-xl border {{ $personal['outcome'] === 'won' ? 'border-green-500/30 bg-green-500/10' : 'border-brand-border bg-brand-dark' }} px-2 py-2 text-center">
                                            @if ($personal['outcome'] === 'won')
                                                <span class="text-xs font-semibold text-green-400">You won in the latest
                                                    round</span>
                                            @elseif ($personal['outcome'] === 'lost')
                                                <span class="text-xs font-semibold text-orange-400">No win in the latest
                                                    round</span>
                                            @else
                                                <span class="text-xs opacity-50">You did not enter the latest round</span>
                                            @endif
                                            <span class="mt-1 block text-xs opacity-40">Results are private</span>
                                        </div>
                                    </div>

                                    <p class="mt-2 text-center text-xs opacity-50">
                                        Open results to see your private history for every draw.
                                    </p>

                                    <a href="{{ route('lotteries.results', $resultLottery) }}"
                                        class="mt-2 block rounded-2xl bg-gradient-to-r from-green-500 to-orange-500 py-3 text-center font-semibold text-white transition hover:scale-[1.01]">
                                        <i class="fa-solid fa-trophy mr-2"></i>
                                        View Results
                                    </a>

                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            @endif --}}

            <div>

    </section>

@endsection

@push('scripts')
    @include('lottery.partials.countdown-scripts')
@endpush
