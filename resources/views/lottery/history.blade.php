@extends('layouts.master')

@section('content')

<section class="lottery_tickets py-10">

    <div class="container">

        <div class="sec_heading">
            <span class="sec_subtitle">My Lottery</span>
            <h2 class="mt-4">My Lottery History</h2>
            <p class="mt-3 max-w-2xl opacity-70">
                Each round you entered, with your tickets and private win details.
            </p>
        </div>

        @if ($history->count())

            <div class="mt-8 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[960px] text-left">
                        <thead class="border-b border-brand-border bg-brand-dark">
                            <tr>
                                <th class="px-5 py-4 text-sm">Lottery</th>
                                <th class="px-5 py-4 text-sm">Round</th>
                                <th class="px-5 py-4 text-sm">Tickets</th>
                                <th class="px-5 py-4 text-sm">Amount</th>
                                <th class="px-5 py-4 text-sm">Purchased</th>
                                <th class="px-5 py-4 text-sm">Drawn</th>
                                <th class="px-5 py-4 text-sm">Status</th>
                                <th class="px-5 py-4 text-sm">Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history as $row)
                                @php
                                    $lottery = $row['lottery'];
                                    $draw = $row['draw'];
                                    $currency = $lottery?->currency ?? '';
                                    $prizes = $draw?->prizeCatalog($lottery) ?? collect();
                                @endphp

                                <tr class="border-b border-brand-border align-top last:border-0">
                                    <td class="px-5 py-4">
                                        <span class="font-semibold">
                                            {{ $lottery?->title ?? 'Lottery' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full border border-orange-500/20 bg-orange-500/10 px-2.5 py-0.5 text-xs font-semibold text-orange-400">Round {{ $row['round_number'] ?? '—' }}</span>
                                        @if ($row['round_date'] ?? null)
                                            <span class="mt-1 block text-[11px] opacity-60">
                                                {{ $row['round_date']->copy()->timezone(config('app.timezone'))->format('d M Y') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="text-sm font-medium">
                                            {{ $row['quantity'] }}
                                            {{ \Illuminate\Support\Str::plural('ticket', $row['quantity']) }}
                                        </p>
                                        <ul class="mt-2 space-y-1">
                                            @foreach ($row['tickets'] as $ticket)
                                                <li class="flex items-center gap-2">
                                                    <code class="font-mono text-xs opacity-80">
                                                        {{ $ticket->ticket_number }}
                                                    </code>
                                                    @if ($ticket->status === 'winner')
                                                        <span class="text-[10px] font-semibold text-green-400">Won</span>
                                                    @elseif ($ticket->status === 'lost')
                                                        <span class="text-[10px] opacity-40">Lost</span>
                                                    @elseif ($ticket->status === 'refunded')
                                                        <span class="text-[10px] text-orange-400">Refunded</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        {{ $currency }}
                                        {{ number_format((float) $row['amount'], 2) }}
                                    </td>

                                    <td class="px-5 py-4 text-sm opacity-60 whitespace-nowrap">
                                        {{ optional($row['purchased_at'])->format('d M Y h:i A') ?? '—' }}
                                    </td>

                                    <td class="px-5 py-4 text-sm opacity-60 whitespace-nowrap">
                                        {{ optional($row['drawn_at'])->format('d M Y h:i A') ?? '—' }}
                                    </td>

                                    <td class="px-5 py-4">
                                        @if ($row['draw_status'] === 'drawn')
                                            <span
                                                class="rounded-full border border-green-500/20 bg-green-500/10 px-3 py-1 text-xs text-green-400">
                                                Drawn
                                            </span>
                                        @elseif ($row['draw_status'] === 'cancelled')
                                            <span
                                                class="rounded-full border border-gray-500/20 bg-gray-500/10 px-3 py-1 text-xs">
                                                Cancelled
                                            </span>
                                        @else
                                            <span
                                                class="rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-xs text-orange-400">
                                                Pending
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        @if ($row['outcome'] === 'won' && $row['wins']->isNotEmpty())
                                            <span
                                                class="rounded-full border border-green-500/30 bg-green-500/10 px-3 py-1 text-xs font-semibold text-green-400">
                                                Won
                                            </span>
                                            <ul class="mt-2 space-y-2">
                                                @foreach ($row['wins'] as $win)
                                                    @php
                                                        $prize = $prizes->get($win->prize_category, [
                                                            'label' => ucfirst((string) $win->prize_category) . ' Prize',
                                                            'amount' => (float) $win->prize_amount,
                                                        ]);
                                                    @endphp
                                                    <li class="text-xs">
                                                        <span class="font-semibold text-green-400">
                                                            {{ $prize['label'] ?? 'Prize' }}
                                                        </span>
                                                        <span class="mt-0.5 block font-mono opacity-70">
                                                            {{ $win->ticket?->ticket_number ?? '—' }}
                                                        </span>
                                                        <span class="mt-0.5 block font-semibold text-orange-400">
                                                            {{ $currency }}
                                                            {{ number_format((float) ($prize['amount'] ?? $win->prize_amount), 2) }}
                                                        </span>
                                                        <span class="mt-0.5 block opacity-50">
                                                            {{ ucfirst((string) $win->payout_status) }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            @if ($draw && $lottery && ! $lottery->trashed() && $draw->winners_announced)
                                                <a href="{{ route('lotteries.draws.show', [$lottery, $draw]) }}"
                                                    class="mt-2 inline-flex text-[11px] font-semibold text-green-400 hover:underline">
                                                    Details →
                                                </a>
                                            @endif
                                        @elseif ($row['outcome'] === 'lost')
                                            <span
                                                class="rounded-full border border-gray-500/20 bg-gray-500/10 px-3 py-1 text-xs">
                                                No win
                                            </span>
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
            @if ($history->hasPages())
                <div
                    class="history_pagination flex items-center justify-center gap-2 border-t border-brand-border px-5 py-4">
                    @if ($history->onFirstPage())
                        <span
                            class="flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg border border-brand-border bg-brand-dark text-sm opacity-30">
                            <i class="fa-solid fa-chevron-left"></i> </span>
                    @else
                        <a href="{{ $history->previousPageUrl() }}"
                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-brand-border bg-brand-dark text-sm transition hover:border-green-500/40 hover:text-green-400">
                            <i class="fa-solid fa-chevron-left"></i> </a>
                    @endif
            
                
                    <span class="min-w-[55px] text-center text-sm font-semibold">
                        {{ $history->currentPage() }} / {{ $history->lastPage() }}
                    </span>
            
                    @if ($history->hasMorePages())
                        <a href="{{ $history->nextPageUrl() }}"
                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-brand-border bg-brand-dark text-sm transition hover:border-green-500/40 hover:text-green-400">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span
                            class="flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg border border-brand-border bg-brand-dark text-sm opacity-30">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            
            @endif
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
