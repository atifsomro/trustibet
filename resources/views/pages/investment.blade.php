@extends('layouts.master')

@push('styles')
    <style>
        .investment-card-cta {
            display: flex;
            justify-content: center;
            padding: 12px 24px 32px !important;
            margin: 0;
            box-sizing: border-box;
        }

        .investment-card-buy-btn {
            display: inline-flex;
            min-width: 150px;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            background-color: #0284c7;
            padding: 0.75rem 2rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: #fff;
            transition: background-color 300ms;
            cursor: pointer;
            border: none;
            margin: 0;
        }

        .investment-card-buy-btn:hover {
            background-color: #0ea5e9;
        }

        .investment-ribbon {
            position: absolute !important;
            top: 0 !important;
            right: 0 !important;
            width: 150px !important;
            height: 150px !important;
            overflow: hidden !important;
            z-index: 50 !important;
            pointer-events: none !important;
        }

        .investment-ribbon__label {
            position: absolute !important;
            top: 30px !important;
            left: -25px !important;
            display: block !important;
            width: 225px !important;
            padding: 10px 0 !important;
            margin: 0 !important;
            background-color: #ff0000 !important;
            color: #ffffff !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            line-height: 1.2 !important;
            letter-spacing: 0.4px !important;
            text-align: center !important;
            text-transform: none !important;
            white-space: nowrap !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3) !important;
            transform: rotate(45deg) !important;
            -webkit-transform: rotate(45deg) !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>
@endpush

@section('content')
    <section class="investment_plans py-8">
        <div class="container">
            @php $skipFlashSwal = true; @endphp
            @include('partials.purchase-alert-host')

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

            <div class="mt-8 grid grid-cols-1 items-stretch gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($packages as $package)
                    <div
                        class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-brand-border bg-brand-surface transition-all duration-300 hover:-translate-y-1 hover:border-brand-primary hover:shadow-[0_0_30px_rgba(2,132,199,.15)]">
                        @if ($package->is_recommended)
                            <div class="investment-ribbon" aria-label="Recommended"
                                style="position:absolute;top:0;right:0;width:150px;height:150px;overflow:hidden;z-index:50;pointer-events:none;">
                                <span class="investment-ribbon__label"
                                    style="position:absolute;top:30px;left:-25px;display:block;width:225px;padding:10px 0;margin:0;background-color:#ff0000;color:#ffffff;font-size:13px;font-weight:700;line-height:1.2;text-align:center;white-space:nowrap;box-shadow:0 4px 10px rgba(0,0,0,.3);transform:rotate(45deg);-webkit-transform:rotate(45deg);">
                                    Recommended
                                </span>
                            </div>
                        @endif

                        {{-- Title --}}
                        <div class="bg-brand-primary px-5 py-4 text-center">
                            <h3 class="text-base font-bold uppercase tracking-wider text-white sm:text-lg">
                                {{ $package->name }}
                            </h3>
                        </div>

                        {{-- Price --}}
                        <div class="border-b border-white/10 bg-brand-dark/40 px-5 py-6 text-center">
                            <p class="flex items-baseline justify-center gap-2">
                                <span class="text-3xl font-bold text-green-500 sm:text-4xl">
                                    ${{ rtrim(rtrim(number_format((float) $package->price, 2, '.', ''), '0'), '.') }}
                                </span>
                                <span class="text-sm opacity-60 sm:text-base">
                                    / {{ $package->durationLabel() }}
                                </span>
                            </p>
                        </div>

                        {{-- Content area --}}
                        <div class="flex flex-1 flex-col px-8 py-8 sm:px-10 sm:py-9">
                            <ul class="space-y-5">
                                @forelse ($package->feature_points ?? [] as $point)
                                    @if (trim((string) $point) !== '')
                                        <li class="flex items-start gap-4">
                                            <span
                                                class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-primary text-white">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                            </span>
                                            <span class="text-[15px] leading-6 opacity-90">{{ $point }}</span>
                                        </li>
                                    @endif
                                @empty
                                    <li class="flex items-start gap-4 opacity-70">
                                        <span
                                            class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-primary text-white">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                        </span>
                                        <span class="text-[15px] leading-6">
                                            Daily ROI: ${{ rtrim(rtrim(number_format((float) $package->daily_roi, 4, '.', ''), '0'), '.') }}
                                        </span>
                                    </li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- CTA: explicit padding so it never sits on the card border --}}
                        <div class="investment-card-cta mt-auto">
                            <form method="POST" action="{{ route('investments.buy', $package) }}"
                                class="js-ajax-purchase"
                                data-purchase-type="investment"
                                data-confirm="Buy {{ $package->name }} for ${{ number_format((float) $package->price, 2) }} from withdrawable balance?">
                                @csrf
                                <button type="submit" class="investment-card-buy-btn">
                                    Buy Now
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center opacity-70">
                        No investment packages available right now.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
