@extends('layouts.master')

@section('title', 'Transaction Details')

@section('content')

<div class="container mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">

        <div>
            <h1 class="text-3xl font-bold text-brand-dark">
                Transaction Details
            </h1>

            <p class="text-gray-500 mt-2">
                View complete information about this wallet transaction.
            </p>
        </div>

        <div class="mt-5 lg:mt-0">

            <a href="{{ route('wallet.transactions') }}"
               class="inline-flex items-center px-5 py-3 rounded-xl bg-brand-primary text-white hover:opacity-90">

                <i class="fas fa-arrow-left mr-2"></i>

                Back to Transactions

            </a>

        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Transaction Details --}}
        <div class="lg:col-span-2">

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100">

                <div class="px-6 py-5 border-b">

                    <h2 class="text-xl font-semibold">
                        Transaction Information
                    </h2>

                </div>

                <div class="divide-y">

                    <div class="flex justify-between px-6 py-5">
                        <span class="font-medium text-gray-600">Transaction ID</span>
                        <span>#{{ $transaction->id }}</span>
                    </div>

                    <div class="flex justify-between px-6 py-5">
                        <span class="font-medium text-gray-600">Wallet</span>
                        <span>#{{ $transaction->wallet_id }}</span>
                    </div>

                    <div class="flex justify-between px-6 py-5">
                        <span class="font-medium text-gray-600">Transaction Type</span>

                        <span class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-sm">
                            {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type->value)) }}
                        </span>
                    </div>

                    <div class="flex justify-between px-6 py-5">
                        <span class="font-medium text-gray-600">Balance Type</span>

                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm">
                            {{ ucfirst($transaction->balance_type->value) }}
                        </span>
                    </div>

                    <div class="flex justify-between px-6 py-5">
                        <span class="font-medium text-gray-600">Amount</span>

                        @if($transaction->amount >= 0)

                            <span class="font-semibold text-green-600">
                                +{{ number_format($transaction->amount , 2) }}
                            </span>

                        @else

                            <span class="font-semibold text-red-600">
                                {{ number_format($transaction->amount , 2) }}
                            </span>

                        @endif
                    </div>

                    <div class="flex justify-between px-6 py-5">
                        <span class="font-medium text-gray-600">Balance After</span>

                        <span class="font-semibold">
                            {{ number_format($transaction->balance_after , 2) }}
                        </span>
                    </div>

                    @if($transaction->idempotency_key)

                        <div class="flex justify-between px-6 py-5">
                            <span class="font-medium text-gray-600">Idempotency Key</span>

                            <span class="font-mono text-sm break-all">
                                {{ $transaction->idempotency_key }}
                            </span>
                        </div>

                    @endif

                    <div class="flex justify-between px-6 py-5">
                        <span class="font-medium text-gray-600">Created At</span>

                        <span>
                            {{ $transaction->created_at->format('d M Y h:i:s A') }}
                        </span>
                    </div>

                </div>

            </div>

            {{-- Meta --}}
            @if(!empty($transaction->meta))

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 mt-8">

                    <div class="px-6 py-5 border-b">

                        <h2 class="text-xl font-semibold">
                            Metadata
                        </h2>

                    </div>

                    <div class="p-6">

<pre class="bg-gray-50 rounded-xl p-5 overflow-x-auto text-sm">{{ json_encode($transaction->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

                    </div>

                </div>

            @endif

        </div>

        {{-- Sidebar --}}
        <div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

                <h2 class="text-lg font-semibold mb-6">

                    Reference

                </h2>

                @if($transaction->reference_type)

                    <div class="space-y-5">

                        <div>

                            <p class="text-sm text-gray-500">
                                Reference Type
                            </p>

                            <p class="font-semibold mt-1">
                                {{ class_basename($transaction->reference_type) }}
                            </p>

                        </div>

                        <div>

                            <p class="text-sm text-gray-500">
                                Reference ID
                            </p>

                            <p class="font-semibold mt-1">
                                #{{ $transaction->reference_id }}
                            </p>

                        </div>

                    </div>

                @else

                    <div class="text-center py-8">

                        <i class="fas fa-link text-5xl text-gray-300 mb-4"></i>

                        <p class="text-gray-500">
                            No reference available.
                        </p>

                    </div>

                @endif

            </div>

            @if($transaction->bonus)

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mt-6">

                    <h2 class="text-lg font-semibold mb-6">

                        Bonus

                    </h2>

                    <div class="space-y-4">

                        <div>
                            <p class="text-sm text-gray-500">Bonus ID</p>
                            <p class="font-semibold">
                                #{{ $transaction->bonus->id }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Type</p>
                            <p class="font-semibold">
                                {{ ucfirst($transaction->bonus->type->value) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Remaining</p>
                            <p class="font-semibold">
                                {{ number_format($transaction->bonus->remaining_amount / 100,2) }}
                            </p>
                        </div>

                    </div>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection