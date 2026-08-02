@extends('layouts.master')

@section('title', 'Bonus History')

@section('content')

<div class="container mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">

        <div>
            <h1 class="text-3xl font-bold text-brand-dark">
                Bonus History
            </h1>

            <p class="text-gray-500 mt-2">
                View all bonuses received and their current status.
            </p>
        </div>

        <div class="mt-5 lg:mt-0">

            <a href="{{ route('wallet.index') }}"
               class="inline-flex items-center px-5 py-3 rounded-xl bg-brand-primary text-white hover:opacity-90">

                <i class="fas fa-arrow-left mr-2"></i>

                Back to Wallet

            </a>

        </div>

    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

            <p class="text-sm text-gray-500">
                Total Bonuses
            </p>

            <h2 class="text-3xl font-bold mt-2">
                {{ $bonuses->total() }}
            </h2>

        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

            <p class="text-sm text-gray-500">
                Active Bonuses
            </p>

            <h2 class="text-3xl font-bold text-green-600 mt-2">
                {{ $bonuses->where('status', \App\Enums\BonusStatus::ACTIVE)->count() }}
            </h2>

        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

            <p class="text-sm text-gray-500">
                Remaining Bonus
            </p>

            <h2 class="text-3xl font-bold text-blue-600 mt-2">
                {{ number_format($bonuses->sum('remaining_amount') , 2) }}
            </h2>

        </div>

    </div>

    {{-- Bonus Table --}}
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-gray-50">

                <tr>

                    <th class="px-6 py-4 text-left">#</th>
                    <th class="px-6 py-4 text-left">Type</th>
                    <th class="px-6 py-4 text-left">Initial</th>
                    <th class="px-6 py-4 text-left">Remaining</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-left">Granted</th>
                    <th class="px-6 py-4 text-left">Expires</th>

                </tr>

                </thead>

                <tbody>

                @forelse($bonuses as $bonus)

                    <tr class="border-t">

                        <td class="px-6 py-5">
                            #{{ $bonus->id }}
                        </td>

                        <td class="px-6 py-5">
                            {{ ucfirst($bonus->type->value) }}
                        </td>

                        <td class="px-6 py-5">
                            {{ number_format($bonus->initial_amount , 2) }}
                        </td>

                        <td class="px-6 py-5">
                            {{ number_format($bonus->remaining_amount , 2) }}
                        </td>

                        <td class="px-6 py-5">

                            @php

                                $classes = match($bonus->status) {
                                    \App\Enums\BonusStatus::ACTIVE => 'bg-green-100 text-green-700',
                                    \App\Enums\BonusStatus::COMPLETED => 'bg-blue-100 text-blue-700',
                                    \App\Enums\BonusStatus::EXPIRED => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };

                            @endphp

                            <span class="px-3 py-1 rounded-full text-xs {{ $classes }}">
                                {{ ucfirst($bonus->status->value) }}
                            </span>

                        </td>

                        <td class="px-6 py-5">
                            {{ optional($bonus->activated_at)->format('d M Y') }}
                        </td>

                        <td class="px-6 py-5">

                            @if($bonus->expires_at)

                                {{ $bonus->expires_at->format('d M Y') }}

                            @else

                                —

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="py-16 text-center">

                            <i class="fas fa-gift text-5xl text-gray-300 mb-4"></i>

                            <p class="text-gray-500">

                                No bonus history found.

                            </p>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        @if($bonuses->hasPages())

            <div class="border-t px-6 py-4">

                {{ $bonuses->links() }}

            </div>

        @endif

    </div>

</div>

@endsection