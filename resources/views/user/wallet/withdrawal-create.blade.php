@extends('layouts.master')

@section('title', 'Request Withdrawal')

@section('content')

<div class="container mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">

        <div>

            <h1 class="text-3xl font-bold text-brand-dark">
                Request Withdrawal
            </h1>

            <p class="text-gray-500 mt-2">
                Submit a withdrawal request from your withdrawable wallet balance.
            </p>

        </div>

        <div class="mt-5 lg:mt-0">

            <a href="{{ route('wallet.withdrawals') }}"
               class="inline-flex items-center px-5 py-3 rounded-xl border border-brand-primary text-brand-primary hover:bg-brand-primary hover:text-white transition">

                <i class="fas fa-arrow-left mr-2"></i>

                Back

            </a>

        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Form --}}
        <div class="lg:col-span-2">

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100">

                <div class="px-6 py-5 border-b">

                    <h2 class="text-xl font-semibold">
                        Withdrawal Information
                    </h2>

                </div>

                <form action="{{ route('wallet.withdrawals.store') }}"
                      method="POST"
                      class="p-6 space-y-6">

                    @csrf

                    {{-- Amount --}}
                    <div>

                        <label class="block text-sm font-medium mb-2">
                            Withdrawal Amount
                        </label>

                        <input
                            type="number"
                            name="amount"
                            min="1"
                            step="1"
                            value="{{ old('amount') }}"
                            class="w-full rounded-xl border-gray-300 focus:border-brand-primary focus:ring-brand-primary"
                            placeholder="Enter amount">

                        @error('amount')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Payment Method --}}
                    <div>

                        <label class="block text-sm font-medium mb-2">
                            Payment Method
                        </label>

                        <select
                            name="payment_method"
                            id="payment_method"
                            class="w-full rounded-xl border-gray-300 focus:border-brand-primary focus:ring-brand-primary">

                            <option value="">
                                Select Payment Method
                            </option>

                            <option value="bank_transfer"
                                @selected(old('payment_method')=='bank_transfer')>
                                Bank Transfer
                            </option>

                            <option value="jazzcash"
                                @selected(old('payment_method')=='jazzcash')>
                                JazzCash
                            </option>

                            <option value="easypaisa"
                                @selected(old('payment_method')=='easypaisa')>
                                EasyPaisa
                            </option>

                            <option value="paypal"
                                @selected(old('payment_method')=='paypal')>
                                PayPal
                            </option>

                        </select>

                        @error('payment_method')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Account Details --}}
                    <div>

                        <label class="block text-sm font-medium mb-2">
                            Account Details
                        </label>

                        <textarea
                            name="account_details"
                            rows="6"
                            class="w-full rounded-xl border-gray-300 focus:border-brand-primary focus:ring-brand-primary"
                            placeholder="Provide complete account details where you want to receive the payment.">{{ old('account_details') }}</textarea>

                        @error('account_details')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Remarks --}}
                    <div>

                        <label class="block text-sm font-medium mb-2">
                            Remarks (Optional)
                        </label>

                        <textarea
                            name="remarks"
                            rows="4"
                            class="w-full rounded-xl border-gray-300 focus:border-brand-primary focus:ring-brand-primary"
                            placeholder="Any additional information...">{{ old('remarks') }}</textarea>

                        @error('remarks')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror

                    </div>

                    <div class="pt-4">

                        <button
                            type="submit"
                            class="inline-flex items-center px-8 py-3 rounded-xl bg-brand-primary text-white font-semibold hover:opacity-90 transition">

                            <i class="fas fa-paper-plane mr-2"></i>

                            Submit Withdrawal Request

                        </button>

                    </div>

                </form>

            </div>

        </div>

        {{-- Sidebar --}}
        <div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

                <h2 class="text-lg font-semibold mb-6">

                    Wallet Summary

                </h2>

                <div class="space-y-5">

                    <div class="flex justify-between">

                        <span class="text-gray-500">
                            Withdrawable
                        </span>

                        <span class="font-semibold text-green-600">
                            {{ number_format($wallet->withdrawable_balance , 2) }}
                            {{ $wallet->currency }}
                        </span>

                    </div>

                    <div class="flex justify-between">

                        <span class="text-gray-500">
                            Bonus Balance
                        </span>

                        <span class="font-semibold text-blue-600">
                            {{ number_format($wallet->bonus_balance, 2) }}
                            {{ $wallet->currency }}
                        </span>

                    </div>

                    <div class="flex justify-between">

                        <span class="text-gray-500">
                            Locked Balance
                        </span>

                        <span class="font-semibold text-orange-600">
                            {{ number_format($wallet->locked_balance , 2) }}
                            {{ $wallet->currency }}
                        </span>

                    </div>

                    <hr>

                    <div class="flex justify-between text-lg">

                        <span class="font-semibold">
                            Available
                        </span>

                        <span class="font-bold text-brand-primary">
                            {{ number_format($wallet->withdrawable_balance , 2) }}
                            {{ $wallet->currency }}
                        </span>

                    </div>

                </div>

            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mt-6">

                <h2 class="text-lg font-semibold mb-5">

                    Withdrawal Guidelines

                </h2>

                <ul class="space-y-3 text-sm text-gray-600">

                    <li class="flex">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                        Only your withdrawable balance can be withdrawn.
                    </li>

                    <li class="flex">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                        Bonus balance cannot be withdrawn directly.
                    </li>

                    <li class="flex">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                        Requests require administrator approval.
                    </li>

                    <li class="flex">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                        Processing time may take 24–48 hours.
                    </li>

                    <li class="flex">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                        Ensure your payment details are correct before submitting.
                    </li>

                </ul>

            </div>

        </div>

    </div>

</div>

@endsection