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

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

            <p class="text-gray-500 text-sm">
                Total Requests
            </p>

            <h2 class="text-3xl font-bold mt-2">
                {{ $withdrawals->total() }}
            </h2>

        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

            <p class="text-gray-500 text-sm">
                Pending
            </p>

            <h2 class="text-3xl font-bold text-yellow-500 mt-2">
                {{ $withdrawals->where('status', \App\Enums\WithdrawalStatus::PENDING)->count() }}
            </h2>

        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

            <p class="text-gray-500 text-sm">
                Approved
            </p>

            <h2 class="text-3xl font-bold text-green-600 mt-2">
                {{ $withdrawals->where('status', \App\Enums\WithdrawalStatus::APPROVED)->count() }}
            </h2>

        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

            <p class="text-gray-500 text-sm">
                Rejected
            </p>

            <h2 class="text-3xl font-bold text-red-600 mt-2">
                {{ $withdrawals->where('status', \App\Enums\WithdrawalStatus::REJECTED)->count() }}
            </h2>

        </div>

    </div>

    {{-- Table --}}
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-gray-50">

                <tr>

                    <th class="px-6 py-4 text-left">
                        #
                    </th>

                    <th class="px-6 py-4 text-left">
                        Amount
                    </th>

                    <th class="px-6 py-4 text-left">
                        Payment Method
                    </th>

                    <th class="px-6 py-4 text-left">
                        Status
                    </th>

                    <th class="px-6 py-4 text-left">
                        Requested
                    </th>

                    <th class="px-6 py-4 text-left">
                        Processed
                    </th>

                    <th class="px-6 py-4 text-center">
                        Details
                    </th>

                </tr>

                </thead>

                <tbody>

                @forelse($withdrawals as $withdrawal)

                    <tr class="border-t hover:bg-gray-50">

                        <td class="px-6 py-5">

                            #{{ $withdrawal->id }}

                        </td>

                        <td class="px-6 py-5 font-semibold">

                            {{ number_format($withdrawal->amount , 2) }}

                        </td>

                        <td class="px-6 py-5">

                            {{ ucfirst(str_replace('_', ' ', $withdrawal->payment_method)) }}

                        </td>

                        <td class="px-6 py-5">

                            @php

                                $statusClass = match ($withdrawal->status) {

                                    \App\Enums\WithdrawalStatus::PENDING => 'bg-yellow-100 text-yellow-700',

                                    \App\Enums\WithdrawalStatus::APPROVED => 'bg-green-100 text-green-700',

                                    \App\Enums\WithdrawalStatus::REJECTED => 'bg-red-100 text-red-700',

                                    default => 'bg-gray-100 text-gray-700',

                                };

                            @endphp

                            <span class="px-3 py-1 rounded-full text-xs {{ $statusClass }}">

                                {{ ucfirst($withdrawal->status->value) }}

                            </span>

                        </td>

                        <td class="px-6 py-5">

                            {{ $withdrawal->created_at->format('d M Y h:i A') }}

                        </td>

                        <td class="px-6 py-5">

                            {{ optional($withdrawal->processed_at)->format('d M Y h:i A') ?? '-' }}

                        </td>

                        <td class="px-6 py-5 text-center">

                            <button
                                x-data
                                @click="$dispatch('open-withdrawal', { id: {{ $withdrawal->id }} })"
                                class="text-brand-primary hover:underline">

                                View

                            </button>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="py-16 text-center">

                            <i class="fas fa-wallet text-5xl text-gray-300 mb-4"></i>

                            <p class="text-gray-500">

                                You haven't requested any withdrawals yet.

                            </p>

                            <a href="{{ route('wallet.withdrawals.create') }}"
                               class="inline-flex items-center mt-6 px-5 py-3 rounded-xl bg-brand-primary text-white">

                                <i class="fas fa-plus mr-2"></i>

                                Request Withdrawal

                            </a>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        @if($withdrawals->hasPages())

            <div class="border-t px-6 py-5">

                {{ $withdrawals->links() }}

            </div>

        @endif

    </div>

</div>

@endsection