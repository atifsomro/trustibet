@extends('layouts.master')

@section('content')
    <section class="py-8">
        <div class="container">
            @php $skipFlashSwal = true; @endphp
            @include('partials.purchase-alert-host')

            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <h2>My Investments</h2>
                    <p class="mt-2 opacity-70">Track packages, claim daily ROI, and transfer earnings.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('investment') }}" class="btn-primary">Browse Packages</a>
                    @if ($claimableAmount > 0)
                        <form method="POST" action="{{ route('investments.claim') }}">
                            @csrf
                            <button type="submit" class="btn-orange">
                                Claim All ROI (${{ number_format($claimableAmount, 2) }})
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-5 mb-6">
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-6 text-center">
                    <p class="opacity-70">Active Plans</p>
                    <h3 class="mt-2">{{ $stats['active'] }}</h3>
                </div>
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-6 text-center">
                    <p class="opacity-70">Total Investment</p>
                    <h3 class="mt-2 text-brand-primary">${{ number_format($stats['total_invested'], 2) }}</h3>
                </div>
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-6 text-center">
                    <p class="opacity-70">Completed</p>
                    <h3 class="mt-2 text-green-500">{{ $stats['completed'] }}</h3>
                </div>
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-6 text-center">
                    <p class="opacity-70">Total Earned ROI</p>
                    <h3 class="mt-2 text-green-400">${{ number_format($stats['total_earned_roi'], 2) }}</h3>
                </div>
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-6 text-center">
                    <p class="opacity-70">ROI Balance</p>
                    <h3 class="mt-2 text-yellow-500">${{ number_format((float) $wallet->roi_balance, 2) }}</h3>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-6 mb-6">
                <form method="POST" action="{{ route('investments.transfer') }}" class="grid gap-4 lg:grid-cols-3 items-end">
                    @csrf
                    <div>
                        <p class="text-sm opacity-70">Transfer ROI → Withdrawable</p>
                        <p class="mt-1 text-sm">
                            Remaining today: ${{ number_format($remainingTransferLimit, 2) }}
                            / ${{ number_format($dailyTransferLimit, 2) }}
                        </p>
                    </div>
                    <input type="number" step="0.01" min="{{ $minTransferAmount }}" name="amount"
                        class="rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                        placeholder="Amount" required>
                    <button type="submit" class="btn-primary">Transfer</button>
                </form>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8 overflow-x-auto mb-6">
                <h3 class="mb-4 text-lg">ROI Balance History</h3>
                <table class="w-full min-w-[640px] text-[12px] md:text-base">
                    <thead>
                        <tr class="border-b border-brand-border">
                            <th class="py-3 text-left">Date</th>
                            <th class="py-3 text-left">Type</th>
                            <th class="py-3 text-left">Entry</th>
                            <th class="py-3 text-left">Amount</th>
                            <th class="py-3 text-left">Balance After</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roiLedger as $tx)
                            @php
                                $isCredit = (float) $tx->amount > 0;
                            @endphp
                            <tr class="border-b border-brand-border">
                                <td class="py-4">{{ $tx->created_at?->format('d M Y H:i') }}</td>
                                <td class="py-4">{{ $tx->type->label() }}</td>
                                <td class="py-4">
                                    @if ($isCredit)
                                        <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">Credit</span>
                                    @else
                                        <span class="rounded-full bg-red-500/20 px-3 py-1 text-red-400">Debit</span>
                                    @endif
                                </td>
                                <td class="py-4 {{ $isCredit ? 'text-green-500' : 'text-red-400' }}">
                                    {{ $isCredit ? '+' : '' }}${{ number_format((float) $tx->amount, 2) }}
                                </td>
                                <td class="py-4">${{ number_format((float) $tx->balance_after, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center opacity-70">No ROI balance movements yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6 mb-6">
                <form method="GET" class="grid gap-4 lg:grid-cols-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Plan"
                        class="rounded-xl border border-brand-border bg-brand-dark px-2 sm:px-4 py-3 outline-none focus:border-brand-primary">
                    <select name="status"
                        class="rounded-xl border border-brand-border bg-brand-dark px-2 sm:px-4 py-3 outline-none focus:border-brand-primary">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button class="btn-primary">Filter</button>
                    <a href="{{ route('investments.mine') }}" class="btn-orange text-center">Reset</a>
                </form>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8 overflow-x-auto">
                <table class="w-full min-w-[900px] text-[12px] md:text-base">
                    <thead>
                        <tr class="border-b border-brand-border">
                            <th class="py-3 text-left">Plan</th>
                            <th class="py-3 text-left">Amount</th>
                            <th class="py-3 text-left">Daily ROI</th>
                            <th class="py-3 text-left">Progress</th>
                            <th class="py-3 text-left">Start</th>
                            <th class="py-3 text-left">End</th>
                            <th class="py-3 text-left">Status</th>
                            <th class="py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($investments as $investment)
                            <tr class="border-b border-brand-border">
                                <td class="py-4">{{ $investment->package_name }}</td>
                                <td class="py-4">${{ number_format((float) $investment->price, 2) }}</td>
                                <td class="py-4">${{ number_format((float) $investment->daily_roi, 2) }}</td>
                                <td class="py-4">{{ $investment->daysElapsed() }} / {{ $investment->total_days }}</td>
                                <td class="py-4">{{ $investment->starts_at->format('d M Y') }}</td>
                                <td class="py-4">{{ $investment->ends_at->format('d M Y') }}</td>
                                <td class="py-4">
                                    @if ($investment->status === \App\Enums\InvestmentStatus::ACTIVE)
                                        <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">Active</span>
                                    @elseif ($investment->status === \App\Enums\InvestmentStatus::COMPLETED)
                                        <span class="rounded-full bg-gray-500/20 px-3 py-1 text-gray-400">Completed</span>
                                    @else
                                        <span class="rounded-full bg-red-500/20 px-3 py-1 text-red-400">Cancelled</span>
                                    @endif
                                </td>
                                <td class="py-4">
                                    <a href="{{ route('investments.show', $investment) }}"
                                        class="btn-primary text-[12px] sm:text-sm px-3 sm:px-5 py-2">View ROI</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center opacity-70">No investments yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($investments->hasPages())
                    <div class="mt-6">{{ $investments->links() }}</div>
                @endif
            </div>
        </div>
    </section>
@endsection
