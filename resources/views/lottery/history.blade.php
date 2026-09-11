@extends('layouts.master')

@section('content')

<section class="lottery_tickets py-10">

    <div class="container">

        <div class="sec_heading">
            <span class="sec_subtitle">My Lottery</span>
            <h2 class="mt-4">My Lottery History</h2>
            <p class="mt-3 max-w-2xl opacity-70">
                Every lottery you entered, with purchase details and draw outcome.
            </p>
        </div>

        @if ($history->count())

            <div class="mt-8 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="border-b border-brand-border bg-brand-dark">
                            <tr>
                                <th class="px-5 py-4 text-sm">Lottery</th>
                                <th class="px-5 py-4 text-sm">Tickets</th>
                                <th class="px-5 py-4 text-sm">Amount</th>
                                <th class="px-5 py-4 text-sm">Purchased</th>
                                <th class="px-5 py-4 text-sm">Draw</th>
                                <th class="px-5 py-4 text-sm">Outcome</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history as $row)
                                <tr class="border-b border-brand-border last:border-0">
                                    <td class="px-5 py-4">
                                        <a
                                            href="{{ route('lotteries.show', $row['lottery']) }}"
                                            class="font-semibold transition hover:text-green-500"
                                        >
                                            {{ $row['lottery']?->title ?? 'Lottery' }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-4">
                                        {{ $row['quantity'] }}
                                    </td>
                                    <td class="px-5 py-4">
                                        {{ $row['lottery']?->currency }}
                                        {{ number_format((float) $row['amount'], 2) }}
                                    </td>
                                    <td class="px-5 py-4 text-sm opacity-60">
                                        {{ optional($row['purchased_at'])->format('d M Y h:i A') }}
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($row['draw_status'] === 'drawn')
                                            <span class="rounded-full border border-green-500/20 bg-green-500/10 px-3 py-1 text-xs text-green-400">Drawn</span>
                                        @elseif ($row['draw_status'] === 'cancelled')
                                            <span class="rounded-full border border-gray-500/20 bg-gray-500/10 px-3 py-1 text-xs">Cancelled</span>
                                        @else
                                            <span class="rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-xs text-orange-400">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($row['outcome'] === 'won')
                                            <span class="rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-xs text-orange-400">Won</span>
                                        @elseif ($row['outcome'] === 'lost')
                                            <span class="rounded-full border border-gray-500/20 bg-gray-500/10 px-3 py-1 text-xs">Lost</span>
                                        @elseif ($row['outcome'] === 'cancelled')
                                            <span class="text-xs opacity-50">Cancelled</span>
                                        @else
                                            <span class="text-xs opacity-50">Not yet drawn</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @else

            <div class="mx-auto mt-10 max-w-xl rounded-3xl border border-brand-border bg-brand-surface p-10 text-center">
                <div
                    class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl border border-green-500/20 bg-gradient-to-br from-green-500/20 to-orange-500/20">
                    <i class="fa-solid fa-ticket text-3xl text-green-500"></i>
                </div>
                <h3 class="mt-6 text-2xl font-bold">No lottery history yet</h3>
                <p class="mt-3 opacity-70">
                    You haven't purchased any lottery tickets yet.
                </p>
                <a
                    href="{{ route('lotteries.index') }}"
                    class="mt-6 inline-block rounded-2xl bg-gradient-to-r from-green-500 to-orange-500 px-6 py-3 font-semibold text-white"
                >
                    Browse Lotteries
                </a>
            </div>

        @endif

    </div>

</section>

@endsection
