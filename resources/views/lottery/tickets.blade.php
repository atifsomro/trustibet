@extends('layouts.master')

@section('content')

<section class="lottery_tickets py-10">

    <div class="container">

        <div class="sec_heading">

            <span class="sec_subtitle">
                My Lottery
            </span>

            <h2 class="mt-4">
                My Tickets
            </h2>

        </div>

        @if ($tickets->count())

            <div class="mt-8 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

                <div class="overflow-x-auto">

                    <table class="w-full text-left">

                        <thead class="border-b border-brand-border bg-brand-dark">

                            <tr>

                                <th class="px-5 py-4 text-sm">
                                    Lottery
                                </th>

                                <th class="px-5 py-4 text-sm">
                                    Ticket Number
                                </th>

                                <th class="px-5 py-4 text-sm">
                                    Price
                                </th>

                                <th class="px-5 py-4 text-sm">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-sm">
                                    Purchased
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($tickets as $ticket)

                                <tr class="border-b border-brand-border last:border-0">

                                    <td class="px-5 py-4">

                                        <a
                                            href="{{ route('lotteries.show', $ticket->lottery) }}"
                                            class="font-semibold transition hover:text-green-500">

                                            {{ $ticket->lottery->title }}

                                        </a>

                                    </td>

                                    <td class="px-5 py-4">

                                        <span
                                            class="inline-flex rounded-xl border border-green-500/20 bg-green-500/10 px-3 py-2 font-mono text-sm text-green-400">

                                            {{ $ticket->ticket_number }}

                                        </span>

                                    </td>

                                    <td class="px-5 py-4">

                                        {{ $ticket->lottery->currency }}
                                        {{ number_format($ticket->price, 2) }}

                                    </td>

                                    <td class="px-5 py-4">

                                        @if ($ticket->status === 'active')

                                            <span
                                                class="rounded-full border border-green-500/20 bg-green-500/10 px-3 py-1 text-xs text-green-400">

                                                Active

                                            </span>

                                        @elseif ($ticket->status === 'winner')

                                            <span
                                                class="rounded-full border border-orange-500/20 bg-orange-500/10 px-3 py-1 text-xs text-orange-400">

                                                Winner

                                            </span>

                                        @elseif ($ticket->status === 'refunded')

                                            <span
                                                class="rounded-full border border-blue-500/20 bg-blue-500/10 px-3 py-1 text-xs text-blue-400">

                                                Refunded

                                            </span>

                                        @else

                                            <span
                                                class="rounded-full border border-gray-500/20 bg-gray-500/10 px-3 py-1 text-xs">

                                                {{ ucfirst($ticket->status) }}

                                            </span>

                                        @endif

                                    </td>

                                    <td class="px-5 py-4 text-sm opacity-60">

                                        {{ $ticket->purchased_at?->format('d M Y h:i A') }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="mt-6">
                {{ $tickets->links() }}
            </div>

        @else

            <div class="mx-auto mt-10 max-w-xl rounded-3xl border border-brand-border bg-brand-surface p-10 text-center">

                <div
                    class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl border border-green-500/20 bg-gradient-to-br from-green-500/20 to-orange-500/20">

                    <i class="fa-solid fa-ticket text-3xl text-green-500"></i>

                </div>

                <h3 class="mt-6 text-2xl font-bold">
                    No Tickets Yet
                </h3>

                <p class="mt-3 opacity-70">
                    You haven't purchased any lottery tickets yet.
                </p>

                <a
                    href="{{ route('lotteries.index') }}"
                    class="mt-6 inline-block rounded-2xl bg-gradient-to-r from-green-500 to-orange-500 px-6 py-3 font-semibold text-white">

                    Browse Lotteries

                </a>

            </div>

        @endif

    </div>

</section>

@endsection