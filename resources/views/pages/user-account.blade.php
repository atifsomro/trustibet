@extends('layouts.master')
@push('styles')
    <style>
        /* ===========================
       dashboard
    =========================== */
        .dashboard .candle-chart {
            position: relative;
            height: 190px;
            overflow: hidden;
            border-radius: 14px;
            background: linear-gradient(180deg,
                    rgba(255, 255, 255, 0.025),
                    transparent);
        }

        .dashboard .chart-line {
            position: absolute;
            left: 0;
            width: 100%;
            height: 1px;
            background: currentColor;
            opacity: 0.05;
        }

        .dashboard .chart-line-1 {
            top: 25%;
        }

        .dashboard .chart-line-2 {
            top: 50%;
        }

        .dashboard .chart-line-3 {
            top: 75%;
        }

        .dashboard .candles {
            position: absolute;
            inset: 10px 8px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            gap: 10px;
        }

        .dashboard .candle {
            position: relative;
            width: 10px;
            border-radius: 2px;
            animation: dashboardCandleMove 1.8s ease-in-out infinite;
        }

        .dashboard .candle::before,
        .dashboard .candle::after {
            content: "";
            position: absolute;
            left: 50%;
            width: 2px;
            transform: translateX(-50%);
            background: currentColor;
            opacity: 0.9;
        }

        .dashboard .candle::before {
            top: -18px;
            height: 18px;
        }

        .dashboard .candle::after {
            bottom: -18px;
            height: 18px;
        }

        .dashboard .candle span {
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 2px;
            background: currentColor;
            box-shadow: 0 0 12px currentColor;
        }

        .dashboard .green {
            color: #22c55e;
        }

        .dashboard .red {
            color: #ef4444;
        }

        .dashboard .c1 {
            height: 48px;
            animation-delay: -0.2s;
        }

        .dashboard .c2 {
            height: 78px;
            animation-delay: -0.7s;
        }

        .dashboard .c3 {
            height: 58px;
            animation-delay: -1.1s;
        }

        .dashboard .c4 {
            height: 92px;
            animation-delay: -0.4s;
        }

        .dashboard .c5 {
            height: 65px;
            animation-delay: -1.5s;
        }

        .dashboard .c6 {
            height: 105px;
            animation-delay: -0.9s;
        }

        .dashboard .c7 {
            height: 55px;
            animation-delay: -1.3s;
        }

        .dashboard .c8 {
            height: 120px;
            animation-delay: -0.3s;
        }

        .dashboard .c9 {
            height: 82px;
            animation-delay: -1.7s;
        }

        .dashboard .c10 {
            height: 68px;
            animation-delay: -0.6s;
        }

        .dashboard .c11 {
            height: 110px;
            animation-delay: -1.2s;
        }

        .dashboard .c12 {
            height: 90px;
            animation-delay: -0.8s;
        }

        .dashboard .c13 {
            height: 62px;
            animation-delay: -1.6s;
        }

        .dashboard .c14 {
            height: 125px;
            animation-delay: -0.5s;
        }

        .dashboard .c15 {
            height: 98px;
            animation-delay: -1.4s;
        }

        .dashboard .c16 {
            height: 72px;
            animation-delay: -0.1s;
        }

        .dashboard .c17 {
            height: 115px;
            animation-delay: -1s;
        }

        .dashboard .c18 {
            height: 88px;
            animation-delay: -0.7s;
        }

        @keyframes dashboardCandleMove {

            0%,
            100% {
                transform: translateY(12px);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        @media (max-width: 767px) {
            .dashboard .candle-chart {
                height: 150px;
            }

            .dashboard .candles {
                gap: 5px;
            }

            .dashboard .candle {
                width: 7px;
            }

            .dashboard .candle::before,
            .dashboard .candle::after {
                height: 12px;
            }

            .dashboard .candle::before {
                top: -12px;
            }

            .dashboard .candle::after {
                bottom: -12px;
            }
        }
    </style>
@endpush
@section('content')
    <section class="dashboard py-8 lg:py-12">
        <div class="container">
            <div class="grid lg:grid-cols-12 gap-6">
                {{-- Sidebar --}}
                <aside class="lg:col-span-3">
                    <div
                        class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6 position-static lg:sticky lg:top-24">
                        <div class="text-center">
                            <img src="{{ auth()->user()->avatar ?? asset('images/profile/avatar.png') }}"
                                class="w-18 h-18 md:w-24 md:h-24 rounded-full mx-auto border-4 border-brand-primary object-cover">
                            <h4 class="mt-4">
                                {{ auth()->user()->name }}

                                @if (isset($kyc) && $kyc)
                                    @if ($kyc->status == 'approved')
                                        <span class="mt-2 inline-block text-sm text-green-500">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </span>
                                    @elseif($kyc->status == 'pending')
                                        <span class="mt-2 inline-block text-sm text-yellow-500">
                                            <i class="fa-solid fa-hourglass-half"></i>
                                        </span>
                                    @elseif($kyc->status == 'rejected')
                                        <span class="mt-2 inline-block text-sm text-red-500">
                                            <i class="fa-solid fa-file-circle-xmark"></i>
                                        </span>
                                    @endif
                                @endif
                            </h4>
                        </div>

                        <nav class="mt-8 space-y-2" aria-label="Account navigation">
                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl bg-brand-primary text-white"
                                data-tab="dashboard">
                                <i class="fa-solid fa-house"></i>
                                Dashboard
                            </button>

                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="profile">
                                <i class="fa-solid fa-user"></i>
                                Profile
                            </button>

                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="wallet">
                                <i class="fa-solid fa-wallet"></i>
                                Wallet
                            </button>

                            {{-- <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="entries">
                                <i class="fa-solid fa-ticket"></i>
                                My Entries
                            </button> --}}

                            {{-- <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="winnings">
                                <i class="fa-solid fa-trophy"></i>
                                Winnings
                            </button> --}}

                            {{-- <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="transactions">
                                <i class="fa-solid fa-credit-card"></i>
                                Transactions
                            </button> --}}

                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="invest">
                                <i class="fa-solid fa-credit-card"></i>
                                Investments
                            </button>
                            {{-- <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="referral">
                                <i class="fa-solid fa-credit-card"></i>
                                Referral
                            </button> --}}

                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="kyc">
                                <i class="fa-solid fa-credit-card"></i>
                                KYC
                            </button>

                            {{-- <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="settings">
                                <i class="fa-solid fa-gear"></i>
                                Settings
                            </button> --}}

                            <a href="#"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/20 text-red-400">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Logout
                            </a>
                        </nav>
                    </div>
                </aside>

                {{-- Content --}}
                <div class="lg:col-span-9 min-w-0">
                    <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                            <div>
                                <small id="tab-label" class="uppercase tracking-[3px] text-brand-primary">
                                    Dashboard
                                </small>
                                <h2 id="tab-title" class="mt-3">
                                    Welcome Dear
                                    <span class="text-brand-primary">
                                        {{ auth()->user()->name }} 👋
                                    </span>
                                </h2>
                                <p id="tab-description" class="mt-4">
                                    Welcome to your TrustiBet dashboard. Manage your wallet,
                                    entries, winnings and account from one place.
                                </p>
                            </div>
                            <div class="flex gap-3 flex-wrap">
                                <a href="{{ route('deposits.index') }}" class="btn-primary">
                                    Deposit
                                </a>
                                <a href="{{ route('wallet.withdrawals') }}" class="btn-secondary">
                                    Withdraw
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">

                        <div id="dashboard-panel" class="account-tab-panel grid sm:grid-cols-2 xl:grid-cols-4 gap-5">
                            <div class="rounded-2xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                                <i class="fa-solid fa-wallet text-3xl text-brand-primary"></i>
                                <p class="mt-5 text-sm">Wallet Balance</p>
                                <h3 class="mt-2">$ {{ number_format(auth()->user()->wallet->withdrawable_balance, 2) }}
                                </h3>
                            </div>
                            <div class="rounded-2xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                                <i class="fa-solid fa-ticket text-3xl text-brand-primary"></i>
                                <p class="mt-5 text-sm">Active Entries</p>
                                <h3 class="mt-2">14</h3>
                            </div>
                            <div class="rounded-2xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                                <i class="fa-solid fa-trophy text-3xl text-brand-primary"></i>
                                <p class="mt-5 text-sm">Total Wins</p>
                                <h3 class="mt-2">5</h3>
                            </div>
                            <div class="rounded-2xl border border-brand-border bg-brand-surface p-3 sm:p-6">
                                <i class="fa-solid fa-gift text-3xl text-brand-primary"></i>
                                <p class="mt-5 text-sm">Bonus Balance</p>
                                <h3 class="mt-2">$ {{ number_format(auth()->user()->wallet->bonus_balance, 2) }}</h3>
                            </div>
                        </div>
                        {{-- Candlestick --}}
                        <div
                            class="mt-5 overflow-hidden rounded-2xl border border-brand-border bg-brand-surface p-4 sm:p-6">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <span class="text-xs uppercase tracking-wider opacity-50">Live Activity</span>
                                    <h3 class="mt-1 text-lg font-semibold">Winning Trends</h3>
                                </div>

                                <div class="flex items-center gap-4 text-xs opacity-60">
                                    <span class="flex items-center gap-1.5">
                                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                        Up
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                        Down
                                    </span>
                                </div>
                            </div>

                            <div class="candle-chart">
                                <div class="chart-line chart-line-1"></div>
                                <div class="chart-line chart-line-2"></div>
                                <div class="chart-line chart-line-3"></div>

                                <div class="candles">
                                    <div class="candle green c1"><span></span></div>
                                    <div class="candle red c2"><span></span></div>
                                    <div class="candle green c3"><span></span></div>
                                    <div class="candle green c4"><span></span></div>
                                    <div class="candle red c5"><span></span></div>
                                    <div class="candle green c6"><span></span></div>
                                    <div class="candle red c7"><span></span></div>
                                    <div class="candle green c8"><span></span></div>
                                    <div class="candle green c9"><span></span></div>
                                    <div class="candle red c10"><span></span></div>
                                    <div class="candle green c11"><span></span></div>
                                    <div class="candle green c12"><span></span></div>
                                    <div class="candle red c13"><span></span></div>
                                    <div class="candle green c14"><span></span></div>
                                    <div class="candle green c15"><span></span></div>
                                    <div class="candle red c16"><span></span></div>
                                    <div class="candle green c17"><span></span></div>
                                    <div class="candle green c18"><span></span></div>
                                </div>
                            </div>
                        </div>

                        <div id="profile-panel"
                            class="account-tab-panel hidden rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-5 overflow-hidden">
                            @include('pages.inc.profile-tab')
                        </div>

                        <div id="wallet-panel"
                            class="account-tab-panel hidden rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-5 overflow-hidden">
                            @include('pages.inc.wallet')
                        </div>

                        <div id="entries-panel"
                            class="account-tab-panel hidden rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-5 overflow-hidden">
                            @include('pages.inc.entries')
                        </div>

                        <div id="winnings-panel"
                            class="account-tab-panel hidden rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-5 overflow-hidden">
                            @include('pages.inc.wining')
                        </div>

                        <div id="transactions-panel"
                            class="account-tab-panel hidden rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-5 overflow-hidden">
                            @include('pages.inc.transection')
                        </div>
                        <div id="invest-panel"
                            class="account-tab-panel hidden rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-5 overflow-hidden">
                            @include('pages.inc.invest')
                        </div>

                        <div id="referral-panel"
                            class="account-tab-panel hidden rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-5 overflow-hidden">
                            @include('pages.inc.referral')
                        </div>

                        <div id="kyc-panel"
                            class="account-tab-panel hidden rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-5 overflow-hidden">
                            @include('user.kyc.kyc')
                        </div>

                        <div id="settings-panel"
                            class="account-tab-panel hidden rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-5 overflow-hidden">
                            @include('pages.inc.settings')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            try {
                const codeText = document.getElementById("referralCodeText");
                const linkText = document.getElementById("referralLinkText");
                const copyCodeBtn = document.getElementById("copyReferralCode");
                const copyLinkBtn = document.getElementById("copyReferralLink");

                function copyToClipboard(text, button) {
                    navigator.clipboard.writeText(text).then(() => {
                        const originalText = button.innerHTML;
                        button.innerHTML = '<i class="fa-solid fa-check mr-2"></i>Copied';
                        setTimeout(() => {
                            button.innerHTML = originalText;
                        }, 2000);
                    });
                }
                copyCodeBtn?.addEventListener("click", () => {
                    copyToClipboard(codeText.innerText, copyCodeBtn);
                });
                copyLinkBtn?.addEventListener("click", () => {
                    copyToClipboard(linkText.innerText, copyLinkBtn);
                });
            } catch (error) {
                console.error(error);
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.account-tab-btn');
            const panels = document.querySelectorAll('.account-tab-panel');
            const label = document.getElementById('tab-label');
            const title = document.getElementById('tab-title');
            const description = document.getElementById('tab-description');

            const tabContent = {
                dashboard: {
                    label: 'Dashboard',
                    title: 'Welcome Dear <span class="text-brand-primary">{{ auth()->user()->name }} 👋</span>',
                    description: 'Welcome to your TrustiBet dashboard. Manage your wallet, entries, winnings and account from one place.'
                },
                profile: {
                    label: 'Profile',
                    title: 'Profile Overview',
                    description: 'Update your personal details and account preferences here.'
                },
                wallet: {
                    label: 'Wallet',
                    title: 'Wallet Overview',
                    description: 'Track your deposits, withdrawals and balance from one place.'
                },
                entries: {
                    label: 'My Entries',
                    title: 'My Entries',
                    description: 'Review your active tickets and upcoming plays here.'
                },
                winnings: {
                    label: 'Winnings',
                    title: 'Winnings',
                    description: 'See your recent wins and payout status here.'
                },
                transactions: {
                    label: 'Transactions',
                    title: 'Transactions',
                    description: 'View your full transaction history and payment activity.'
                },
                invest: {
                    label: 'Investments',
                    title: 'Investments',
                    description: 'Review your investment packages and returns here.'
                },
                referral: {
                    label: 'Referral',
                    title: 'Referral',
                    description: 'View your full Referral history and activity.'
                },
                kyc: {
                    label: 'KYC',
                    title: 'KYC',
                    description: 'View your full KYC activity.'
                },
                settings: {
                    label: 'Settings',
                    title: 'Account Settings',
                    description: 'Adjust your security settings and notification preferences.'
                }
            };

            function selectTab(activeTab) {
                const activeButton = Array.from(buttons).find(function(button) {
                    return button.getAttribute('data-tab') === activeTab;
                });

                if (!activeButton) {
                    activeTab = 'dashboard';
                }

                buttons.forEach(function(item) {
                    const isActive = item === activeButton || item.getAttribute('data-tab') === activeTab;
                    item.classList.toggle('bg-brand-primary', isActive);
                    item.classList.toggle('text-white', isActive);
                    item.classList.toggle('hover:bg-brand-dark', !isActive);
                    item.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });

                panels.forEach(function(panel) {
                    panel.classList.add('hidden');
                });

                const activePanel = document.getElementById(activeTab + '-panel');
                if (activePanel) {
                    activePanel.classList.remove('hidden');
                }

                if (tabContent[activeTab]) {
                    label.textContent = tabContent[activeTab].label;
                    title.innerHTML = tabContent[activeTab].title;
                    description.textContent = tabContent[activeTab].description;
                }
                history.replaceState(null, '', '#' + activeTab);
            }

            buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                    selectTab(this.getAttribute('data-tab'));
                });
            });

            selectTab(window.location.hash.slice(1) || 'dashboard');
        });
    </script>
    <script>
        $('#profileImage').on('change', function () {
            const file = this.files[0];
            if (!file) return;
            // Instant local preview (before upload completes)
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#profilePreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
            // Upload via AJAX
            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '{{ route('profile.avatar.update') }}', // adjust to your route
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    // optional: show a small spinner/loading state on the avatar
                },
                success: function (response) {
                    // Replace preview with the actual saved URL (handles CDN paths, cache-busting, etc.)
                    $('#profilePreview').attr('src', response.avatar_url + '?t=' + Date.now());
                    $(".object-cover").attr('src', response.avatar_url + '?t=' + Date.now());
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Upload failed. Please try again.');
                    // optionally revert preview to old avatar here
                }
            });
        });
    </script>
@endpush
