@extends('layouts.master')

@section('title', 'Wallet Transactions')

@section('content')

<div class="container mx-auto px-4 py-8">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-brand-dark">
                Wallet Transactions
            </h1>

            <p class="text-gray-500 mt-2">
                View your complete wallet transaction history.
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

    {{-- Filters --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-8">

        <form method="GET">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Balance Type
                    </label>

                    <select
                        name="balance_type"
                        class="w-full rounded-xl border-gray-300">

                        <option value="">All</option>

                        <option value="withdrawable"
                            @selected(request('balance_type')=='withdrawable')>
                            Withdrawable
                        </option>

                        <option value="bonus"
                            @selected(request('balance_type')=='bonus')>
                            Bonus
                        </option>

                        <option value="locked"
                            @selected(request('balance_type')=='locked')>
                            Locked
                        </option>

                    </select>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Transaction Type
                    </label>

                    <input
                        type="text"
                        name="transaction_type"
                        value="{{ request('transaction_type') }}"
                        class="w-full rounded-xl border-gray-300"
                        placeholder="Deposit / Win / Bet">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        From
                    </label>

                    <input
                        type="date"
                        name="from"
                        value="{{ request('from') }}"
                        class="w-full rounded-xl border-gray-300">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        To
                    </label>

                    <input
                        type="date"
                        name="to"
                        value="{{ request('to') }}"
                        class="w-full rounded-xl border-gray-300">

                </div>

                <div class="flex items-end">

                    <button
                        class="w-full bg-brand-primary text-white rounded-xl py-3">

                        <i class="fas fa-search mr-2"></i>

                        Filter

                    </button>

                </div>

            </div>

        </form>

    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-gray-50">

                <tr>

                    <th class="px-6 py-4 text-left">
                        #
                    </th>

                    <th class="px-6 py-4 text-left">
                        Type
                    </th>

                    <th class="px-6 py-4 text-left">
                        Balance
                    </th>

                    <th class="px-6 py-4 text-left">
                        Amount
                    </th>

                    <th class="px-6 py-4 text-left">
                        Balance After
                    </th>

                    <th class="px-6 py-4 text-left">
                        Date
                    </th>

                    <th class="px-6 py-4 text-center">
                        Action
                    </th>

                </tr>

                </thead>

                <tbody>

                @forelse($transactions as $transaction)

                    <tr class="border-t">

                        <td class="px-6 py-5">

                            {{ $transaction->id }}

                        </td>

                        <td class="px-6 py-5">

                            <span
                                class="inline-flex px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs">

                                {{ ucfirst(str_replace('_',' ',$transaction->transaction_type->value)) }}

                            </span>

                        </td>

                        <td class="px-6 py-5">

                            {{ ucfirst($transaction->balance_type->value) }}

                        </td>

                        <td class="px-6 py-5">

                            @if($transaction->amount > 0)

                                <span class="text-green-600 font-semibold">

                                    +{{ number_format($transaction->amount/100,2) }}

                                </span>

                            @else

                                <span class="text-red-600 font-semibold">

                                    {{ number_format($transaction->amount/100,2) }}

                                </span>

                            @endif

                        </td>

                        <td class="px-6 py-5">

                            {{ number_format($transaction->balance_after/100,2) }}

                        </td>

                        <td class="px-6 py-5">

                            {{ $transaction->created_at->format('d M Y h:i A') }}

                        </td>

                        <td class="px-6 py-5 text-center">

                            <a
                                href="{{ route('wallet.transactions.show',$transaction) }}"
                                class="text-brand-primary hover:underline">

                                View

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="text-center py-16 text-gray-500">

                            <i class="fas fa-wallet text-5xl mb-5"></i>

                            <p>

                                No transactions found.

                            </p>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        @if($transactions->hasPages())

            <div class="border-t px-6 py-4">

                {{ $transactions->withQueryString()->links() }}

            </div>

        @endif

    </div>

</div>

@endsection