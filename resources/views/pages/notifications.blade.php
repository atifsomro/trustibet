@extends('layouts.master')

@section('content')
    <section class="py-8">
        <div class="container">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">
                <div>
                    <small class="uppercase tracking-[3px] text-brand-primary">
                        TrustiBet
                    </small>
                    <h2 class="mt-2">
                        Notifications
                    </h2>
                    <p class="mt-2 opacity-70">
                        Stay updated with all your account activities.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button id="markAllReadPage" class="btn-secondary">
                        <i class="fa-solid fa-check-double mr-2"></i>
                        Mark All Read
                    </button>
                </div>
            </div>
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                    <p class="opacity-70">
                        Total Notifications
                    </p>
                    <h2 id="totalNotifications" class="mt-3">
                        0
                    </h2>
                </div>
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                    <p class="opacity-70">
                        Unread
                    </p>
                    <h2 id="unreadNotifications" class="mt-3 text-brand-primary">
                        0
                    </h2>
                </div>
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                    <p class="opacity-70">
                        Read
                    </p>
                    <h2 id="readNotifications" class="mt-3 text-green-500">
                        0
                    </h2>
                </div>
            </div>
            <div class="rounded-3xl border border-brand-border bg-brand-surface overflow-hidden">
                <div class="p-6 border-b border-brand-border">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <input type="text" placeholder="Search notifications..."
                            class="flex-1 rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                        <select
                            class="rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                            <option>All Notifications</option>
                            <option>Deposit</option>
                            <option>Withdraw</option>
                            <option>KYC</option>
                            <option>Referral</option>
                            <option>Game</option>
                        </select>
                    </div>
                </div>
                <div id="notificationList">
                    <div class="notification-card unread relative bg-green-500/5 border-b border-brand-border p-6">
                        <span class="absolute top-8 right-1 w-2 h-2 rounded-full bg-green-500"></span>
                        <div class="flex gap-5">
                            <div class="w-14 h-14 rounded-full bg-green-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-wallet text-green-500 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h5>
                                        Deposit Approved
                                    </h5>
                                    <span class="text-xs opacity-60">
                                        2 min ago
                                    </span>
                                </div>
                                <p class="opacity-70 mt-2">
                                    Rs.2,000 has been added to your wallet successfully.
                                </p>
                                <div class="flex gap-3 mt-5">
                                    <button class="btn-secondary text-sm notificationRead">
                                        Mark Read
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="notification-card unread relative bg-green-500/5 border-b border-brand-border p-6">
                        <span class="absolute top-8 right-1 w-2 h-2 rounded-full bg-green-500"></span>
                        <div class="flex gap-5">
                            <div class="w-14 h-14 rounded-full bg-blue-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-ticket text-blue-500 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h5>
                                        Game Joined
                                    </h5>
                                    <span class="text-xs opacity-60">
                                        20 min ago
                                    </span>
                                </div>
                                <p class="opacity-70 mt-2">
                                    You successfully joined today's Rs.1 Lucky Draw.
                                </p>
                                <div class="flex gap-3 mt-5">
                                    <button class="btn-secondary text-sm notificationRead">
                                        Mark Read
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="notification-card p-6">
                        <div class="flex gap-5">
                            <div class="w-14 h-14 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-gift text-yellow-500 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h5>
                                        Referral Bonus
                                    </h5>
                                    <span class="text-xs opacity-60">
                                        Yesterday
                                    </span>
                                </div>
                                <p class="opacity-70 mt-2">
                                    You received Rs.100 referral reward.
                                </p>
                                <div class="mt-5">
                                    <span class="text-sm text-green-500">
                                        Read
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="notificationPagination" class="flex justify-center items-center gap-2 p-6"></div>
            </div>
        </div>
    </section>
@endsection
