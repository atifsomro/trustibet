@extends('layouts.master')

@section('content')

@php
    $totalRounds = $draws->count();
@endphp

<section class="py-10">
    <div class="container">

        <div class="mb-6">
            <a
                href="{{ route('lotteries.index') }}"
                class="inline-flex items-center gap-2 text-sm opacity-60 transition hover:text-green-500 hover:opacity-100"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Back to Lotteries
            </a>
        </div>

        <div class="sec_heading text-center">
            <span class="sec_subtitle">Lottery Results</span>
            <h1 class="mt-3 text-3xl font-bold md:text-4xl">
                {{ $lottery->title }}
            </h1>
            <p class="mx-auto mt-3 max-w-2xl opacity-70">
                {{ $totalRounds }} completed {{ \Illuminate\Support\Str::plural('round', $totalRounds) }}.
                Scroll to compare winners from each draw.
            </p>
            <a
                href="{{ route('lotteries.show', $lottery) }}"
                class="mt-4 inline-flex items-center gap-2 text-sm text-green-400 hover:underline"
            >
                <i class="fa-solid fa-ticket"></i>
                Buy tickets for the current round
            </a>
        </div>

        <div class="mx-auto mt-10 max-w-3xl space-y-6">
            @foreach ($draws as $draw)
                @php
                    $roundNumber = $totalRounds - $loop->index;
                @endphp

                <div
                    id="draw-{{ $draw->id }}"
                    class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface"
                >
                    <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                    <div class="p-5 md:p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <span class="sec_subtitle">Round {{ $roundNumber }}</span>
                                <h2 class="mt-2 text-2xl font-bold">
                                    Draw Results
                                </h2>
                                <p class="mt-2 text-sm opacity-60">
                                    {{ $draw->completed_at?->format('d M Y h:i A') ?? '—' }}
                                    · {{ number_format($draw->total_tickets) }} tickets
                                </p>
                            </div>

                            <span class="rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2 text-sm font-semibold text-green-400">
                                {{ $draw->total_winners }} / 5 Winners
                            </span>
                        </div>

                        <div class="mt-6">
                            @include('lottery.partials.draw-winners', [
                                'lottery' => $lottery,
                                'draw' => $draw,
                            ])
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

@endsection
