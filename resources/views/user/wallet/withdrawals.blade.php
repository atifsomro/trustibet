@extends('layouts.master')

@section('title', 'Withdrawal History')

@section('content')

    <div class="container mx-auto px-4 py-8">

        {{-- Header --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">

            <div>
                <h1 class="text-3xl font-bold text-brand-dark">
                    Withdrawal History
                </h1>

                <p class="text-gray-500 mt-2">
                    View all your withdrawal requests and their current status.
                </p>
            </div>

            <div class="mt-5 lg:mt-0 flex gap-3">

                <a href="{{ route('wallet.index') }}"
                    class="inline-flex items-center px-5 py-3 rounded-xl border border-brand-primary text-brand-primary hover:bg-brand-primary hover:text-white transition">

                    <i class="fas fa-arrow-left mr-2"></i>

                    Wallet

                </a>

                <a href="{{ route('wallet.withdrawals.create') }}"
                    class="inline-flex items-center px-5 py-3 rounded-xl bg-brand-primary text-white hover:opacity-90 transition">

                    <i class="fas fa-wallet mr-2"></i>

                    New Withdrawal

                </a>

            </div>

        </div>

        {{-- Summary --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

            <div class="bg-brand-dark rounded-3xl shadow-sm border border-gray-100 p-6">

                <p class="text-gray-500 text-sm">
                    Total Requests
                </p>

                <h2 class="text-3xl font-bold mt-2">
                    {{ $withdrawals->total() }}
                </h2>

            </div>

            <div class="bg-brand-dark rounded-3xl shadow-sm border border-gray-100 p-6">

                <p class="text-gray-500 text-sm">
                    Pending
                </p>

                <h2 class="text-3xl font-bold text-yellow-500 mt-2">
                    {{ $withdrawals->where('status', \App\Enums\WithdrawalStatus::PENDING)->count() }}
                </h2>

            </div>

            <div class="bg-brand-dark rounded-3xl shadow-sm border border-gray-100 p-6">

                <p class="text-gray-500 text-sm">
                    Approved
                </p>

                <h2 class="text-3xl font-bold text-green-600 mt-2">
                    {{ $withdrawals->where('status', \App\Enums\WithdrawalStatus::APPROVED)->count() }}
                </h2>

            </div>

            <div class="bg-brand-dark rounded-3xl shadow-sm border border-gray-100 p-6">

                <p class="text-gray-500 text-sm">
                    Rejected
                </p>

                <h2 class="text-3xl font-bold text-red-600 mt-2">
                    {{ $withdrawals->where('status', \App\Enums\WithdrawalStatus::REJECTED)->count() }}
                </h2>

            </div>

        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">

            <div class="overflow-x-auto">
                <table class="min-w-[900px] w-full">

                    <thead class="border-b border-brand-border bg-brand-dark">
                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                #
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Amount
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Payment Method
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Status
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Requested
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Processed
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-brand-border">

                        @forelse($withdrawals as $withdrawal)
                            <tr>

                                <td class="px-6 py-5">
                                    <span class="text-sm font-semibold text-brand-accent">
                                        #{{ $withdrawal->id }}
                                    </span>
                                </td>

                                <td class="px-6 py-5">
                                    <span class="text-sm font-semibold text-brand-light">
                                        {{ number_format($withdrawal->amount, 2) }}
                                    </span>
                                </td>

                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center gap-2 text-sm text-gray-300">
                                        <span
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-brand-border bg-brand-dark">
                                            <i class="fas fa-wallet text-xs text-brand-accent"></i>
                                        </span>

                                        {{ ucfirst(str_replace('_', ' ', $withdrawal->payment_method)) }}
                                    </span>
                                </td>

                                <td class="px-6 py-5">

                                    @php

                                        $statusClass = match ($withdrawal->status) {
                                            \App\Enums\WithdrawalStatus::PENDING
                                                => 'border-yellow-500/20 bg-yellow-500/10 text-yellow-400',

                                            \App\Enums\WithdrawalStatus::APPROVED
                                                => 'border-green-500/20 bg-green-500/10 text-green-400',

                                            \App\Enums\WithdrawalStatus::REJECTED
                                                => 'border-red-500/20 bg-red-500/10 text-red-400',

                                            default => 'border-gray-500/20 bg-gray-500/10 text-gray-400',
                                        };

                                    @endphp

                                    <span
                                        class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium {{ $statusClass }}">

                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                        {{ ucfirst($withdrawal->status->value) }}

                                    </span>

                                </td>

                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-2 text-sm text-gray-300">
                                        <i class="fas fa-calendar-alt text-xs text-brand-accent"></i>

                                        {{ $withdrawal->created_at->format('d M Y') }}
                                    </div>

                                    <span class="mt-1 block text-xs text-gray-500">
                                        {{ $withdrawal->created_at->format('h:i A') }}
                                    </span>

                                </td>

                                <td class="px-6 py-5">

                                    @if ($withdrawal->processed_at)
                                        <div class="flex items-center gap-2 text-sm text-gray-300">
                                            <i class="fas fa-check-circle text-xs text-green-400"></i>

                                            {{ $withdrawal->processed_at->format('d M Y') }}
                                        </div>

                                        <span class="mt-1 block text-xs text-gray-500">
                                            {{ $withdrawal->processed_at->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">
                                            —
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="px-6 py-16 text-center">

                                    <div
                                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-brand-border bg-brand-dark">

                                        <i class="fas fa-wallet text-2xl text-brand-accent"></i>

                                    </div>

                                    <p class="mt-5 text-sm text-gray-400">
                                        You haven't requested any withdrawals yet.
                                    </p>

                                    <a href="{{ route('wallet.withdrawals.create') }}"
                                        class="mt-6 inline-flex items-center gap-2 rounded-xl bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-primary-hover">
                                        <i class="fas fa-plus text-xs"></i>
                                        Request Withdrawal
                                    </a>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

            @if ($withdrawals->hasPages())
                <div class="border-t border-brand-border bg-brand-dark px-6 py-5">
                    {{ $withdrawals->links() }}
                </div>
            @endif

        </div>

    </div>

@endsection
