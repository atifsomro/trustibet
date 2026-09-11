@extends('layouts.master')

@section('content')
    <section class="investment_plans py-8">
        <div class="container">
            @if (session('success'))
                <div class="mb-4 rounded-xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-green-400">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            <div class="sec_heading text-center">
                <span class="sec_subtitle">Investment Plans</span>
                <h2 class="mt-4">Choose Your Investment Plan</h2>
                <p class="mt-5 max-w-3xl mx-auto">
                    Select the plan that best matches your investment goals. Daily ROI is claimed into your ROI balance.
                </p>
            </div>

            @include('components.invest-topbar')

            <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('investments.mine') }}" class="btn-primary">My Investments</a>
                    @if ($claimableAmount > 0)
                        <form method="POST" action="{{ route('investments.claim') }}">
                            @csrf
                            <button type="submit" class="btn-orange">
                                Claim All ROI (${{ number_format($claimableAmount, 2) }})
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-6">
                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <h4>ROI Balance</h4>
                        <p class="mt-2 text-2xl text-brand-primary">${{ number_format((float) $wallet->roi_balance, 2) }}</p>
                        <p class="mt-2 opacity-70 text-sm">
                            Daily transfer limit: ${{ number_format($dailyTransferLimit, 2) }}
                            · Remaining today: ${{ number_format($remainingTransferLimit, 2) }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('investments.transfer') }}" class="space-y-3">
                        @csrf
                        <label class="block text-sm opacity-70">Transfer ROI → Withdrawable</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="number" step="0.01" min="{{ $minTransferAmount }}" name="amount"
                                max="{{ min((float) $wallet->roi_balance, $remainingTransferLimit) }}"
                                class="flex-1 rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                placeholder="Amount (min ${{ number_format($minTransferAmount, 2) }})" required>
                            <button type="submit" class="btn-primary">Transfer</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-7 mt-8">
                @forelse ($packages as $package)
                    <div
                        class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-2 hover:border-green-500 hover:shadow-[0_0_35px_rgba(34,197,94,.18)]">
                        <div
                            class="absolute inset-0 opacity-0 duration-300 group-hover:opacity-100 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
                        </div>

                        <div class="relative z-10 p-4 sm:p-7">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-green-500/20 bg-gradient-to-br from-green-500/20 to-orange-500/20 transition duration-300 group-hover:scale-110 group-hover:rotate-6">
                                    <i class="fa-solid fa-layer-group text-2xl text-green-500"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] sm:text-sm opacity-60">Duration</p>
                                    <h6 class="text-orange-400">{{ $package->total_days }} Days</h6>
                                </div>
                            </div>

                            <h3 class="mt-7">{{ $package->name }}</h3>

                            <div class="mt-4 flex items-end gap-2">
                                <h2 class="text-green-500 drop-shadow-[0_0_10px_rgba(34,197,94,.4)]">
                                    ${{ number_format((float) $package->price, 2) }}
                                </h2>
                                <span class="mb-1 opacity-60">/ Plan</span>
                            </div>
                        </div>

                        <div class="relative z-10 px-4 sm:px-7 pb-4 sm:pb-7">
                            <div
                                class="rounded-2xl border border-green-500/10 bg-gradient-to-b from-brand-dark to-[#07131b] p-5">
                                <ul class="space-y-4">
                                    <li class="flex items-center gap-3">
                                        <i class="fa-solid fa-circle-check text-green-500"></i>
                                        <span>Daily ROI: ${{ number_format((float) $package->daily_roi, 2) }}</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <i class="fa-solid fa-circle-check text-green-500"></i>
                                        <span>Duration: {{ $package->total_days }} days</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <i class="fa-solid fa-circle-check text-green-500"></i>
                                        <span>Principal returned at maturity</span>
                                    </li>
                                    @if ($package->description)
                                        @foreach (preg_split('/\r\n|\r|\n|,/', $package->description) as $line)
                                            @if (trim($line) !== '')
                                                <li class="flex items-center gap-3">
                                                    <i class="fa-solid fa-circle-check text-green-500"></i>
                                                    <span>{{ trim($line) }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                </ul>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div
                                    class="rounded-2xl border border-green-500/20 bg-green-500/5 p-2 sm:p-4 text-center">
                                    <p class="text-xs opacity-60">Status</p>
                                    <h6 class="mt-2 font-semibold text-green-500">● Active</h6>
                                </div>
                                <div
                                    class="rounded-2xl border border-orange-500/20 bg-orange-500/5 p-2 sm:p-4 text-center">
                                    <p class="text-xs opacity-60">Daily ROI</p>
                                    <h6 class="mt-2 font-semibold text-orange-400">
                                        ${{ number_format((float) $package->daily_roi, 2) }}
                                    </h6>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('investments.buy', $package) }}" class="mt-6"
                                onsubmit="return confirm('Buy {{ $package->name }} for ${{ number_format((float) $package->price, 2) }} from withdrawable balance?')">
                                @csrf
                                <button type="submit" class="btn-orange w-full">Buy Now</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center opacity-70 py-12">
                        No investment packages available right now.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
