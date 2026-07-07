@extends('layouts.master')

@section('content')
    <section class="withdraw py-8">
        <div class="container">

            <div class="grid xl:grid-cols-12 gap-6">

                {{-- Withdraw Form --}}
                <div class="xl:col-span-8">

                    <div class="rounded-3xl border border-brand-border bg-brand-surface p-8">

                        <div class="mb-8">

                            <h2>Withdraw Funds</h2>

                            <p class="mt-2 opacity-70">
                                Withdraw your winnings securely using your preferred payment method.
                            </p>

                        </div>

                        {{-- Balance --}}
                        <div class="grid md:grid-cols-2 gap-5 mb-8">

                            <div class="rounded-2xl bg-brand-dark p-6">

                                <p class="opacity-70">
                                    Available Balance
                                </p>

                                <h3 class="mt-2 text-green-500">
                                    Rs. 25,500
                                </h3>

                            </div>

                            <div class="rounded-2xl bg-brand-dark p-6">

                                <p class="opacity-70">
                                    Minimum Withdrawal
                                </p>

                                <h3 class="mt-2">
                                    Rs.100
                                </h3>

                            </div>

                        </div>

                        <form>

                            {{-- Method --}}
                            <h4>Select Withdrawal Method</h4>
                            <div class="grid md:grid-cols-3 gap-5 mt-5">
                                <label class="payment-method rounded-2xl border border-brand-primary p-6 cursor-pointer">
                                    <input type="radio" name="method" value="easypaisa">
                                    <div class="bg-white rounded-full w-20 h-20 flex items-center content-center m-auto">
                                        <img src="{{ asset('images/account/easypaisa.png') }}" class="h-14 mx-auto">
                                    </div>
                                    <h5 class="text-center mt-4">
                                        EasyPaisa
                                    </h5>
                                </label>
                                <label class="payment-method rounded-2xl border border-brand-border p-6 cursor-pointer">
                                    <input type="radio" name="method" value="jazzcash">
                                    <div class="bg-white rounded-full w-20 h-20 flex items-center content-center m-auto">
                                        <img src="{{ asset('images/account/jazzcash.png') }}" class="h-14 mx-auto">
                                    </div>
                                    <h5 class="text-center mt-4">
                                        JazzCash
                                    </h5>
                                </label>
                                <label class="payment-method rounded-2xl border border-brand-border p-6 cursor-pointer">
                                    <input type="radio" name="method" value="binance">
                                    <div class="bg-white rounded-full w-20 h-20 flex items-center content-center m-auto">
                                        <img src="{{ asset('images/account/binance.png') }}" class="h-14 mx-auto">
                                    </div>
                                    <h5 class="text-center mt-4">
                                        Binance
                                    </h5>
                                </label>
                            </div>
                            {{-- Amount --}}
                            <div class="mt-8">
                                <label class="block mb-2">
                                    Withdrawal Amount
                                </label>
                                <input type="number" name="amount" placeholder="Enter amount"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3">
                            </div>
                            {{-- Account --}}
                            <div class="grid md:grid-cols-2 gap-6 mt-8">
                                <div>
                                    <label class="block mb-2">
                                        Account Holder Name
                                    </label>
                                    <input type="text" name="account_name"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3">
                                </div>
                                <div>
                                    <label class="block mb-2">
                                        Mobile Number / Wallet Address
                                    </label>

                                    <input type="text" name="account_number"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3">

                                </div>

                            </div>

                            <div class="mt-6">

                                <label class="block mb-2">
                                    Memo / Tag (Optional)
                                </label>

                                <input type="text" name="memo"
                                    placeholder="Required only for supported crypto networks"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3">

                            </div>

                            <button type="submit" class="btn-primary mt-8">

                                <i class="fa-solid fa-money-bill-transfer mr-2"></i>

                                Submit Withdrawal

                            </button>

                        </form>

                    </div>

                </div>

                {{-- Summary --}}
                <div class="xl:col-span-4">

                    <div class="rounded-3xl border border-brand-border bg-brand-surface p-8">

                        <h3>Withdrawal Summary</h3>

                        <div class="space-y-5 mt-8">

                            <div class="flex justify-between">

                                <span>Amount</span>

                                <span>Rs.0</span>
                            </div>
                            <div class="flex justify-between">

                                <span>Processing Fee</span>

                                <span>Rs.0</span>

                            </div>

                            <div class="flex justify-between text-brand-primary font-bold">

                                <span>You'll Receive</span>

                                <span>Rs.0</span>

                            </div>

                        </div>

                    </div>

                    <div class="rounded-3xl border border-brand-border bg-brand-surface p-8 mt-6">
                        <h4>Important Notes</h4>
                        <ul class="space-y-2 mt-2">
                            <li>✅ Minimum withdrawal: Rs.100</li>
                            <li>✅ Requests are reviewed within 24 hours.</li>
                            <li>✅ Make sure your payment details are correct.</li>
                            <li>✅ Incorrect details may delay your payment.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
