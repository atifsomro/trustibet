@extends('layouts.master')

@section('title', 'Request Withdrawal')

@section('content')

<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div>
            <h2 class=" text-white">
                Request Withdrawal
            </h2>
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

            <div class="bg-brand-dark rounded-3xl shadow-sm border border-gray-100">

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
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
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
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">

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

                            <option value="crypto"
                                @selected(old('payment_method')=='crypto')>
                                Crypto
                            </option>

                        </select>

                        @error('payment_method')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Account Title --}}
                    <div>

                        <label class="block text-sm font-medium mb-2">
                            Account Title
                        </label>

                        <input
                            type="text"
                            name="account_title"
                            value="{{ old('account_title') }}"
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                            placeholder="Name on the receiving account">

                        @error('account_title')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Account Number --}}
                    <div>

                        <label class="block text-sm font-medium mb-2">
                            Account Number
                        </label>

                        <input
                            type="text"
                            name="account_number"
                            value="{{ old('account_number') }}"
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                            placeholder="Account / IBAN / mobile number">

                        @error('account_number')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Bank Name — only shown for Bank Transfer --}}
                    <div id="bank_name_field" style="{{ old('payment_method') === 'bank_transfer' ? '' : 'display:none;' }}">

                        <label class="block text-sm font-medium mb-2">
                            Bank Name
                        </label>

                        <input
                            type="text"
                            name="bank_name"
                            value="{{ old('bank_name') }}"
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                            placeholder="e.g. HBL, Meezan Bank">

                        @error('bank_name')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- Crypto Source — only shown for Crypto --}}
                    <div id="crypto_source_field" style="{{ old('payment_method') === 'crypto' ? '' : 'display:none;' }}">

                        <label class="block text-sm font-medium mb-2">
                            Crypto Exchange / Source
                        </label>

                        <input
                            type="text"
                            name="crypto_source"
                            value="{{ old('crypto_source') }}"
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                            placeholder="e.g. Binance, Trust Wallet">

                        @error('crypto_source')
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
                            class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
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

            <div class="bg-brand-dark rounded-3xl shadow-sm border border-gray-100 p-6">

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

            <div class="bg-brand-dark rounded-3xl shadow-sm border border-gray-100 p-6 mt-6">

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
                        Processing time may take 30-60 minutes.
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var methodSelect = document.getElementById('payment_method');
        var bankField = document.getElementById('bank_name_field');
        var cryptoField = document.getElementById('crypto_source_field');

        if (!methodSelect || !bankField || !cryptoField) {
            return;
        }

        var bankInput = bankField.querySelector('input');
        var cryptoInput = cryptoField.querySelector('input');

        function toggleFields() {
            var value = methodSelect.value;
            var isBank = value === 'bank_transfer';
            var isCrypto = value === 'crypto';

            bankField.style.display = isBank ? '' : 'none';
            cryptoField.style.display = isCrypto ? '' : 'none';

            // Clear out the hidden field's value so a stale bank name
            // isn't submitted alongside a crypto withdrawal (or vice versa).
            if (!isBank && bankInput) {
                bankInput.value = '';
            }
            if (!isCrypto && cryptoInput) {
                cryptoInput.value = '';
            }
        }

        methodSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>

@endsection