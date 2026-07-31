@extends('layouts.master')

@section('content')
    <section class="deposit py-8">
        <div class="container">
            @if ($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="grid xl:grid-cols-12 gap-6">
                {{-- Deposit Form --}}
                <div class="xl:col-span-8">
                    <div class="rounded-3xl border border-brand-border bg-brand-surface p-8">
                        <div class="mb-8">
                            <small class="uppercase tracking-[3px] text-brand-primary">
                                TrustiBet Wallet
                            </small>
                            <h2 class="mt-3">
                                Deposit Funds
                            </h2>
                            <p class="mt-3 opacity-70">
                                Choose your preferred payment method, complete the payment, upload the proof, and your
                                balance will be credited after admin approval.
                            </p>
                        </div>
                        {{-- Balance Cards --}}
                        <div class="grid md:grid-cols-2 gap-5">
                            <div class="rounded-2xl bg-brand-dark p-6">
                                <span class="opacity-70">
                                    Available Balance
                                </span>
                                <h3 class="mt-2 text-green-500">
                                    $0
                                </h3>
                            </div>
                            <div class="rounded-2xl bg-brand-dark p-6">
                                <span class="opacity-70">
                                    Minimum Deposit
                                </span>
                                <h3 class="mt-2">
                                    $0
                                </h3>
                            </div>
                        </div>
                        <form class="mt-8" action="{{ route('deposits.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                            @csrf
                            {{-- Payment Methods --}}
                            <h4>
                                Select Payment Method
                            </h4>
                            <div class="grid md:grid-cols-4 gap-5 mt-5">
                                {{--<label
                                    class="payment-method rounded-2xl border border-brand-primary p-6 cursor-pointer">
                                    <input type="radio" name="payment_method" value="easypaisa" checked
                                        class="hidden bg-white">
                                    <div class="bg-white rounded-full w-20 h-20 flex items-center content-center m-auto">
                                        <img src="{{ asset('images/account/easypaisa.png') }}" class="h-14 mx-auto">
                                    </div>
                                    <h5 class="text-center mt-4">
                                        EasyPaisa
                                    </h5>
                                </label>
                                <label class="payment-method rounded-2xl border border-brand-border p-6 cursor-pointer">
                                    <input type="radio" name="payment_method" value="jazzcash" class="hidden bg-white">
                                    <div class="bg-white rounded-full w-20 h-20 flex items-center content-center m-auto">
                                        <img src="{{ asset('images/account/jazzcash.png') }}" class="h-14 mx-auto">
                                    </div>
                                    <h5 class="text-center mt-4">
                                        JazzCash
                                    </h5>
                                </label>
                                <label class="payment-method rounded-2xl border border-brand-border p-6 cursor-pointer">
                                    <input type="radio" name="payment_method" value="binance" class="hidden bg-white">
                                    <div class="bg-white rounded-full w-20 h-20 flex items-center content-center m-auto">
                                        <img src="{{ asset('images/account/binance.png') }}" class="h-14 mx-auto">
                                    </div>
                                    <h5 class="text-center mt-4">
                                        Binance
                                    </h5>
                                </label>--}}
                                @foreach ($banks as $key => $bank)
                                    <label class="payment-method rounded-2xl border border-brand-primary p-6 cursor-pointer">
                                        <input type="radio" name="bank_account_id" value="{{ $bank->id }}" @checked(intval($key) == 0)
                                            class="hidden bg-white">
                                        <div class="bg-white rounded-full w-20 h-20 flex items-center content-center m-auto">
                                            <img src="{{ Storage::url($bank->picture) }}"
                                                class="h-14 mx-auto">
                                        </div>
                                        <h5 class="text-center mt-4">
                                            {{ $bank->bank_name }}
                                        </h5>
                                    </label>
                                @endforeach
                            </div>
                            {{-- Amount --}}
                            <div class="mt-8">
                                <label class="block mb-2">
                                    Deposit Amount
                                </label>
                                <input id="depositAmount" type="number" name="amount" placeholder="Enter Deposit Amount"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                            </div>
                            {{-- Payment Details --}}
                            <div class="rounded-2xl bg-brand-dark p-6 mt-8">
                                <div class="flex items-center justify-between">
                                    <h4>
                                        Payment Details
                                    </h4>
                                    <button id="copyAccount" type="button" class="btn-primary text-sm">
                                        Copy
                                    </button>
                                </div>
                                <div class="mt-6">
                                    <p class="opacity-70">
                                        Account Name
                                    </p>
                                    <h5 id="accountName">
                                        TrustiBet
                                    </h5>
                                </div>
                                <div class="mt-5">
                                    <p class="opacity-70">
                                        Account Number
                                    </p>
                                    <h5 id="accountNumber">
                                        03451234567
                                    </h5>
                                </div>
                                <div class="mt-5">
                                    <p class="opacity-70">
                                        Network
                                    </p>
                                    <h5 id="networkName">
                                        EasyPaisa
                                    </h5>
                                </div>
                            </div>
                            {{-- QR Code --}}
                            <div class="rounded-2xl bg-brand-dark p-6 mt-8">
                                <h4>
                                    Scan QR Code
                                </h4>
                                <div class="bg-white p-2 w-50 h-50 flex items-center content-center m-auto">
                                    <img id="paymentQR" src="{{ asset('images/account/jazzcashqr.png') }}"
                                        class="w-full h-full mx-auto">
                                </div>
                            </div>
                            {{-- Screenshot --}}
                            <div class="mt-8">
                                <label class="block mb-2">
                                    Upload Payment Screenshot
                                </label>
                                <img id="paymentPreview" src=""
                                    class="hidden mt-5 w-52 rounded-xl border border-brand-border">
                                <input type="file" name="payment_proof" id="paymentProof" accept=".jpg,.jpeg,.png,.webp"
                                    class="block w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3">
                                <small class="opacity-60">
                                    JPG, PNG, WEBP (Max 5MB)
                                </small>
                            </div>
                            {{-- Transaction --}}
                            <div class="mt-8">
                                <label class="block mb-2">
                                    Transaction ID
                                    <span class="opacity-60">
                                        (Optional)
                                    </span>
                                </label>
                                <input type="text" name="transaction_id" placeholder="Enter Transaction ID"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3">
                            </div>
                            <button type="submit" class="btn-primary mt-8">
                                <i class="fa-solid fa-paper-plane mr-2"></i>
                                Submit Deposit
                            </button>
                        </form>
                    </div>
                </div>
                {{-- Right Sidebar --}}
                <div class="xl:col-span-4">
                    {{-- Deposit Summary --}}
                    <div class="rounded-3xl border border-brand-border bg-brand-surface p-8">
                        <h3>
                            Deposit Summary
                        </h3>
                        <div class="space-y-5 mt-8">
                            <div class="flex items-center justify-between">
                                <span>
                                    Payment Method
                                </span>
                                <span id="summaryMethod">
                                    EasyPaisa
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>
                                    Deposit Amount
                                </span>
                                <span id="summaryAmount">
                                    Rs.0
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>
                                    Processing Fee
                                </span>
                                <span>
                                    Rs.0
                                </span>
                            </div>
                            <hr class="border-brand-border">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold">
                                    You'll Receive
                                </span>
                                <span id="summaryReceive" class="text-brand-primary font-bold text-xl">
                                    Rs.0
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- Processing Time --}}
                    <div class="rounded-3xl border border-brand-border bg-brand-surface p-8 mt-6">
                        <h3>
                            Processing Time
                        </h3>
                        <div class="space-y-5 mt-6">
                            <div class="flex justify-between">
                                <span>
                                    EasyPaisa
                                </span>
                                <span class="text-green-500">
                                    5 - 30 Minutes
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span>
                                    JazzCash
                                </span>
                                <span class="text-green-500">
                                    5 - 30 Minutes
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span>
                                    Binance
                                </span>
                                <span class="text-green-500">
                                    5 - 10 Minutes
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- Instructions --}}
                    <div class="rounded-3xl border border-brand-border bg-brand-surface p-8 mt-6">
                        <h3>
                            Deposit Instructions
                        </h3>
                        <ul class="space-y-4 mt-6">
                            <li class="flex gap-3">
                                <i class="fa-solid fa-circle-check text-brand-primary mt-1"></i>
                                <span>
                                    Select your preferred payment method.
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <i class="fa-solid fa-circle-check text-brand-primary mt-1"></i>
                                <span>
                                    Send payment to the provided account.
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <i class="fa-solid fa-circle-check text-brand-primary mt-1"></i>
                                <span>
                                    Upload payment screenshot.
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <i class="fa-solid fa-circle-check text-brand-primary mt-1"></i>
                                <span>
                                    Submit the deposit request.
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <i class="fa-solid fa-circle-check text-brand-primary mt-1"></i>
                                <span>
                                    Balance will be updated after admin approval.
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            {{-- Recent Deposits --}}
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-8 mt-6">
                <div class="flex items-center justify-between">
                    <h3>
                        Recent Deposits
                    </h3>
                    <a href="#" class="text-brand-primary">
                        View All
                    </a>
                </div>
                <div class="overflow-x-auto mt-8">
                    <table class="w-full min-w-[850px]">
                        <thead>
                            <tr class="border-b border-brand-border">
                                <th class="py-4 text-left">Date</th>
                                <th class="py-4 text-left">Method</th>
                                <th class="py-4 text-left">Amount</th>
                                <th class="py-4 text-left">Transaction ID</th>
                                <th class="py-4 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deposits as $deposit)
                                <tr class="border-b border-brand-border">
                                    <td class="py-5">
                                        {{ $deposit->created_at }}
                                    </td>
                                    <td class="py-5">
                                        {{ $deposit->bankAccount->bank_name }}
                                    </td>
                                    <td class="py-5">
                                        {{ $deposit->bankAccount->currency }} {{ $deposit->amount }}
                                    </td>
                                    <td class="py-5">
                                        {{ $deposit->reference_number }}
                                    </td>
                                    <td class="py-5">
                                        <span class="px-4 py-1 rounded-full bg-yellow-500/20 text-yellow-500">
                                            {{ ucfirst($deposit->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            try {
                const methods = document.querySelectorAll('input[name="payment_method"]');
                const accountName = document.getElementById("accountName");
                const accountNumber = document.getElementById("accountNumber");
                const networkName = document.getElementById("networkName");
                const paymentQR = document.getElementById("paymentQR");
                const depositAmount = document.getElementById("depositAmount");
                const summaryMethod = document.getElementById("summaryMethod");
                const summaryAmount = document.getElementById("summaryAmount");
                const summaryReceive = document.getElementById("summaryReceive");
                const copyAccount = document.getElementById("copyAccount");
                const paymentProof = document.getElementById("paymentProof");
                const preview = document.getElementById("paymentPreview");
                const paymentData = {
                    easypaisa: {
                        name: "TrustiBet",
                        number: "03451234567",
                        network: "EasyPaisa",
                        qr: "/images/account/easypaisaqr.png"
                    },
                    jazzcash: {
                        name: "TrustiBet",
                        number: "03001234567",
                        network: "JazzCash",
                        qr: "/images/account/jazzcashqr.png"
                    },
                    binance: {
                        name: "TrustiBet",
                        number: "TJ4xM4Y5L9xxxxxxxxxxxxxxxx",
                        network: "Binanace",
                        qr: "/images/account/binanceqr.png"
                    }
                };
                const updateSelection = method => {
                    const data = paymentData[method.value];
                    accountName.textContent = data.name;
                    accountNumber.textContent = data.number;
                    networkName.textContent = data.network;
                    paymentQR.src = data.qr;
                    summaryMethod.textContent = method.value.charAt(0).toUpperCase() + method.value.slice(1);
                    document.querySelectorAll(".payment-method").forEach(card => {
                        card.classList.remove("border-brand-primary");
                        card.classList.add("border-brand-border");
                    });
                    const selectedCard = method.closest(".payment-method");
                    if (selectedCard) {
                        selectedCard.classList.remove("border-brand-border");
                        selectedCard.classList.add("border-brand-primary");
                    }
                };
                methods.forEach(method => {
                    method.addEventListener("change", () => updateSelection(method));
                    if (method.checked) updateSelection(method);
                });
                depositAmount?.addEventListener("input", () => {
                    const amount = parseFloat(depositAmount.value) || 0;
                    summaryAmount.textContent = "Rs." + amount.toLocaleString();
                    summaryReceive.textContent = "Rs." + amount.toLocaleString();
                });
                copyAccount?.addEventListener("click", () => {
                    navigator.clipboard.writeText(accountNumber.textContent);
                    const old = copyAccount.innerHTML;
                    copyAccount.innerHTML = '<i class="fa-solid fa-check mr-2"></i>Copied';
                    setTimeout(() => {
                        copyAccount.innerHTML = old;
                    }, 2000);
                });
                paymentProof?.addEventListener("change", e => {
                    const file = e.target.files[0];
                    if (!file || !preview) return;
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove("hidden");
                });
            } catch (error) {
                console.error(error);
            }
        });
    </script>
@endpush