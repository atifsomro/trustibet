@extends('layouts.master')

@section('content')
    <section class="investment_plans py-8">
        <div class="container">
            <div class="sec_heading text-center">
                <span class="sec_subtitle">Investment Plans</span>
                <h2 class="mt-4">Choose Your Investment Plan</h2>
                <p class="mt-5 max-w-3xl mx-auto">
                    Select the plan that best matches your investment goals.
                </p>
            </div>
            @include('components.invest-topbar')
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-7">
                @php
                    $plans = [
                        [
                            'name' => 'BABY',
                            'price' => '$10',
                            'features' => [
                                'Daily ROI: $0.027 to $0.20',
                                'Maintenance Fee: 2.5%',
                                'One Time Deposit Fee',
                                '24/7 Customer Support',
                                'Instant Withdrawal',
                            ],
                        ],
                        [
                            'name' => 'STAND',
                            'price' => '$20',
                            'features' => [
                                'Daily ROI: $0.054 to $0.50',
                                'Maintenance Fee: 2.5%',
                                'One Time Deposit Fee',
                                '24/7 Customer Support',
                                'Instant Withdrawal',
                            ],
                        ],
                        [
                            'name' => 'M30S',
                            'price' => '$30',
                            'features' => [
                                'Daily ROI: $0.082 to $0.80',
                                'Maintenance Fee: 2.5%',
                                'One Time Deposit Fee',
                                '24/7 Customer Support',
                                'Instant Withdrawal',
                            ],
                        ],
                        [
                            'name' => 'S90 PRO',
                            'price' => '$40',
                            'features' => [
                                'Daily ROI: $0.10 to $1',
                                'Maintenance Fee: 2.5%',
                                'One Time Deposit Fee',
                                '24/7 Customer Support',
                                'Instant Withdrawal',
                            ],
                            'recommended' => true,
                        ],
                        [
                            'name' => 'LITE',
                            'price' => '$50',
                            'features' => [
                                'Daily ROI: $0.13 to $2',
                                'Maintenance Fee: 2.5%',
                                'One Time Deposit Fee',
                                '24/7 Customer Support',
                                'Instant Withdrawal',
                            ],
                        ],
                        [
                            'name' => 'STANDARD',
                            'price' => '$100',
                            'features' => [
                                'Daily ROI: $0.28 to $5',
                                'Maintenance Fee: 2.5%',
                                'One Time Deposit Fee',
                                '24/7 Customer Support',
                                'Instant Withdrawal',
                            ],
                        ],
                        [
                            'name' => 'ELITE',
                            'price' => '$250',
                            'features' => [
                                'Daily ROI: $0.68 to $15',
                                'Maintenance Fee: 2.5%',
                                'One Time Deposit Fee',
                                '24/7 Customer Support',
                                'Instant Withdrawal',
                            ],
                        ],
                        [
                            'name' => 'SUPER',
                            'price' => '$500',
                            'features' => [
                                'Daily ROI: $1.37 to $40',
                                'Maintenance Fee: 2.5%',
                                'One Time Deposit Fee',
                                '24/7 Customer Support',
                                'Instant Withdrawal',
                            ],
                            'recommended' => true,
                        ],
                        [
                            'name' => 'PRO',
                            'price' => '$1000',
                            'features' => [
                                'Daily ROI: $2.7 to $80',
                                '10% Deposit Bonus',
                                'Maintenance Fee: 2.5%',
                                '24/7 Customer Support',
                                'Instant Withdrawal',
                            ],
                        ],
                        [
                            'name' => 'PRO PLUS',
                            'price' => '$2500',
                            'features' => [
                                'Daily ROI: $6.8 to $150',
                                '50% Deposit Bonus',
                                'Maintenance Fee: 2.5%',
                                '24/7 Customer Support',
                                'Instant Withdrawal',
                            ],
                        ],
                        [
                            'name' => 'BUSINESS',
                            'price' => '$5000',
                            'features' => [
                                'Daily ROI: $13.6 to $350',
                                '200% Deposit Bonus',
                                'Highest Daily Earnings',
                                'Ultra Fast Miner, Trading Bots',
                                'Return Principal Amount After 5 Years',
                            ],
                        ],
                        [
                            'name' => 'BUSINESS PRO',
                            'price' => '$10000',
                            'features' => [
                                'Daily ROI: $27.3 to $900',
                                '300% Deposit Bonus',
                                'Highest Daily Earnings',
                                'Ultra Fast Miner, Trading Bots',
                                'Return Principal Amount After 5 Years',
                            ],
                            'recommended' => true,
                        ],
                    ];
                @endphp

                @foreach ($plans as $plan)
                    <div
                        class="group relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-2 hover:border-green-500 hover:shadow-[0_0_35px_rgba(34,197,94,.18)] {{ isset($plan['recommended']) ? 'border-green-500' : '' }}">

                        <div
                            class="absolute inset-0 opacity-0 duration-300 group-hover:opacity-100 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
                        </div>

                        @if (isset($plan['recommended']))
                            <div class="absolute top-0 right-0 z-10">
                                <span
                                    class="nline-flex items-center rounded-bl-xl bg-brand-primary px-3 py-1 text-xs font-medium text-white">
                                    ⭐ Recommended
                                </span>
                            </div>
                        @endif

                        <div class="relative z-10 p-4 sm:p-7">
                            <div class="flex items-center justify-between">

                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-green-500/20 bg-gradient-to-br from-green-500/20 to-orange-500/20 transition duration-300 group-hover:scale-110 group-hover:rotate-6">
                                    <i class="fa-solid fa-layer-group text-2xl text-green-500"></i>
                                </div>

                                <div class="text-right">
                                    <p class="text-[10px] sm:text-sm opacity-60">Duration</p>
                                    <h6 class="text-orange-400">5 Years</h6>
                                </div>
                            </div>

                            <h3 class="mt-7">{{ $plan['name'] }}</h3>

                            <div class="mt-4 flex items-end gap-2">
                                <h2 class="text-green-500 drop-shadow-[0_0_10px_rgba(34,197,94,.4)]">
                                    {{ $plan['price'] }}
                                </h2>
                                <span class="mb-1 opacity-60">/ Plan</span>
                            </div>
                        </div>

                        <div class="relative z-10 px-4 sm:px-7 pb-4 sm:pb-7">

                            <div
                                class="rounded-2xl border border-green-500/10 bg-gradient-to-b from-brand-dark to-[#07131b] p-5">
                                <ul class="space-y-4">
                                    @foreach ($plan['features'] as $feature)
                                        <li class="flex items-center gap-3">
                                            <i class="fa-solid fa-circle-check text-green-500"></i>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-4">

                                <div
                                    class="rounded-2xl border border-green-500/20 bg-green-500/5 p-2 sm:p-4 text-center transition duration-300 group-hover:border-green-500/40">
                                    <p class="text-xs opacity-60">Status</p>
                                    <h6 class="mt-2 font-semibold text-green-500">● Active</h6>
                                </div>

                                <div
                                    class="rounded-2xl border border-orange-500/20 bg-orange-500/5 p-2 sm:p-4 text-center transition duration-300 group-hover:border-orange-500/40">
                                    <p class="text-xs opacity-60">Category</p>
                                    <h6 class="mt-2 font-semibold text-orange-400">Premium</h6>
                                </div>

                            </div>

                            <a href="{{ route('deposit') }}"
                                class="btn-orange mt-6">
                                Buy Now
                            </a>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
