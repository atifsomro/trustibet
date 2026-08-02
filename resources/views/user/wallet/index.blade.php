@extends('layouts.master')

@section('title', 'My Wallet')

@section('content')

<div class="container mx-auto px-4 py-8">

    {{-- Page Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-brand-dark">
                My Wallet
            </h1>

            <p class="text-gray-500 mt-2">
                View your balances, transactions, bonuses and withdrawal requests.
            </p>
        </div>

        <div class="flex flex-wrap gap-3 mt-5 lg:mt-0">

            <a href="{{ route('deposits.index') }}"
               class="inline-flex items-center px-5 py-3 rounded-xl bg-brand-primary text-white font-semibold hover:opacity-90 transition">

                <i class="fas fa-plus-circle mr-2"></i>
                Deposit
            </a>

            <a href="{{ route('wallet.withdrawals.create') }}"
               class="inline-flex items-center px-5 py-3 rounded-xl border border-brand-primary text-brand-primary font-semibold hover:bg-brand-primary hover:text-white transition">

                <i class="fas fa-wallet mr-2"></i>
                Withdraw
            </a>

        </div>
    </div>

    {{-- Wallet Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        {{-- Withdrawable --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

            <div class="flex justify-between items-center">

                <div>

                    <p class="text-sm text-gray-500">
                        Withdrawable Balance
                    </p>

                    <h2 class="text-3xl font-bold text-green-600 mt-2">
                        {{ number_format($wallet->withdrawable_balance , 2) }}
                        <span class="text-base text-gray-500">
                            {{ $wallet->currency }}
                        </span>
                    </h2>

                </div>

                <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center">

                    <i class="fas fa-money-bill-wave text-2xl text-green-600"></i>

                </div>

            </div>

        </div>

        {{-- Bonus --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

            <div class="flex justify-between items-center">

                <div>

                    <p class="text-sm text-gray-500">
                        Bonus Balance
                    </p>

                    <h2 class="text-3xl font-bold text-blue-600 mt-2">
                        {{ number_format($wallet->bonus_balance, 2) }}
                        <span class="text-base text-gray-500">
                            {{ $wallet->currency }}
                        </span>
                    </h2>

                </div>

                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center">

                    <i class="fas fa-gift text-2xl text-blue-600"></i>

                </div>

            </div>

        </div>

        {{-- Locked --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

            <div class="flex justify-between items-center">

                <div>

                    <p class="text-sm text-gray-500">
                        Locked Balance
                    </p>

                    <h2 class="text-3xl font-bold text-orange-600 mt-2">
                        {{ number_format($wallet->locked_balance , 2) }}
                        <span class="text-base text-gray-500">
                            {{ $wallet->currency }}
                        </span>
                    </h2>

                </div>

                <div class="w-14 h-14 rounded-2xl bg-orange-100 flex items-center justify-center">

                    <i class="fas fa-lock text-2xl text-orange-600"></i>

                </div>

            </div>

        </div>

        {{-- Total --}}
        <div class="bg-gradient-to-r from-brand-primary to-indigo-600 rounded-3xl shadow-sm p-6 text-white">

            <div class="flex justify-between items-center">

                <div>

                    <p class="text-sm opacity-80">
                        Total Wallet Value
                    </p>

                    <h2 class="text-3xl font-bold mt-2">

                        {{ number_format(($wallet->withdrawable_balance + $wallet->bonus_balance + $wallet->locked_balance) , 2) }}

                        <span class="text-base">
                            {{ $wallet->currency }}
                        </span>

                    </h2>

                </div>

                <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">

                    <i class="fas fa-wallet text-2xl"></i>

                </div>

            </div>

        </div>

    </div>

    {{-- Quick Statistics --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mt-8">

        <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">

            <p class="text-sm text-gray-500">Transactions</p>

            <h3 class="text-2xl font-bold mt-2">
                {{ $recentTransactions->count() }}
            </h3>

        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">

            <p class="text-sm text-gray-500">Active Bonuses</p>

            <h3 class="text-2xl font-bold mt-2">
                {{ $recentBonuses->count() }}
            </h3>

        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">

            <p class="text-sm text-gray-500">Withdrawals</p>

            <h3 class="text-2xl font-bold mt-2">
                {{ $recentWithdrawals->count() }}
            </h3>

        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">

            <p class="text-sm text-gray-500">
                Wallet Status
            </p>

            <h3 class="text-lg font-semibold text-green-600 mt-2">
                Active
            </h3>

        </div>

    </div>

</div>

@endsection