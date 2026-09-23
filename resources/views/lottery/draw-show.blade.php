@extends('layouts.master')

@section('content')

@php
    $personal = $draw->personalResult(auth('web')->id());
@endphp

<section class="py-10">
    <div class="container">

        <div class="mb-6">
            <a
                href="{{ route('lotteries.results', ['lottery' => $lottery, 'draw' => $draw->id]) }}"
                class="inline-flex items-center gap-2 text-sm opacity-60 transition hover:text-green-500 hover:opacity-100"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Back to Results
            </a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
            <div class="h-1.5 w-full bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

            <div class="p-5 md:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="sec_subtitle">Private Result</span>
                        <h1 class="mt-3 text-3xl font-bold">
                            {{ $lottery->title }}
                        </h1>
                        <p class="mt-2 text-sm opacity-60">
                            {{ $draw->dailyRoundLabel() }}
                            · {{ $draw->completed_at?->format('d M Y h:i A') }}
                        </p>
                    </div>

                    @if ($personal['outcome'] === 'won')
                        <span class="rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2 text-sm font-semibold text-green-400">
                            You won
                        </span>
                    @elseif ($personal['outcome'] === 'lost')
                        <span class="rounded-full border border-orange-500/20 bg-orange-500/10 px-4 py-2 text-sm font-semibold text-orange-400">
                            No win
                        </span>
                    @else
                        <span class="rounded-full border border-brand-border bg-brand-dark px-4 py-2 text-sm font-semibold opacity-60">
                            Not entered
                        </span>
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-5">
                        <span class="block text-xs uppercase tracking-wider opacity-50">
                            Tickets in round
                        </span>
                        <strong class="mt-2 block text-2xl">
                            {{ number_format($draw->total_tickets) }}
                        </strong>
                    </div>

                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-5">
                        <span class="block text-xs uppercase tracking-wider opacity-50">
                            Drawn at
                        </span>
                        <strong class="mt-2 block text-sm">
                            {{ $draw->completed_at?->format('d M Y h:i A') ?? '—' }}
                        </strong>
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-2xl font-bold">Your result</h2>
                    <p class="mt-2 text-sm opacity-60">
                        Other players cannot see this. Winner lists are never published.
                    </p>

                    <div class="mt-5">
                        @include('lottery.partials.personal-result', [
                            'lottery' => $lottery,
                            'draw' => $draw,
                            'compact' => false,
                        ])
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>
@endsection
