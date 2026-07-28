@extends('layouts.master')

@section('content')
    <section class="dashboard py-8 lg:py-12">
        <div class="container">
            <div class="grid lg:grid-cols-12 gap-6">
                {{-- Sidebar --}}
                <aside class="lg:col-span-3">
                    <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6 position-static lg:sticky lg:top-24">
                        <div class="text-center">
                            <img src="{{ asset('images/profile/avatar.png') }}"
                                class="w-18 h-18 md:w-24 md:h-24 rounded-full mx-auto border-4 border-brand-primary object-cover">
                            <h4 class="mt-4">
                                {{ auth()->user()->name }}
                            </h4>
                            <p class="text-sm">
                                Premium Member
                            </p>
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

                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="entries">
                                <i class="fa-solid fa-ticket"></i>
                                My Entries
                            </button>

                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="winnings">
                                <i class="fa-solid fa-trophy"></i>
                                Winnings
                            </button>

                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="transactions">
                                <i class="fa-solid fa-credit-card"></i>
                                Transactions
                            </button>

                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="invest">
                                <i class="fa-solid fa-credit-card"></i>
                                Investments
                            </button>
                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="referral">
                                <i class="fa-solid fa-credit-card"></i>
                                Referral
                            </button>

                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="kyc">
                                <i class="fa-solid fa-credit-card"></i>
                                KYC
                            </button>

                            <button type="button"
                                class="account-tab-btn flex w-full items-center gap-3 px-4 py-3 rounded-xl hover:bg-brand-dark"
                                data-tab="settings">
                                <i class="fa-solid fa-gear"></i>
                                Settings
                            </button>

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
                                <a href="{{ route('deposit') }}" class="btn-primary">
                                    Deposit
                                </a>
                                <a href="{{ route('withdraw') }}" class="btn-secondary">
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
                                <h3 class="mt-2">Rs. 2,500</h3>
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
                                <h3 class="mt-2">Rs. 500</h3>
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
                            @include('pages.inc.kyc')
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

            buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const activeTab = this.getAttribute('data-tab');

                    buttons.forEach(function(item) {
                        item.classList.remove('bg-brand-primary', 'text-white');
                        item.classList.add('hover:bg-brand-dark');
                    });

                    this.classList.add('bg-brand-primary', 'text-white');
                    this.classList.remove('hover:bg-brand-dark');

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
                });
            });
        });
    </script>
@endpush
