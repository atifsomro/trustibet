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
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button id="markAllReadPage" type="submit" class="btn-secondary">
                            <i class="fa-solid fa-check-double mr-2"></i>
                            Mark All Read
                        </button>
                    </form>
                </div>
            </div>
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                    <p class="opacity-70">
                        Total Notifications
                    </p>
                    <h2 id="totalNotifications" class="mt-3">
                        {{ $notifications->total() }}
                    </h2>
                </div>
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                    <p class="opacity-70">
                        Unread
                    </p>
                    <h2 id="unreadNotifications" class="mt-3 text-brand-primary">
                        {{ $notifications->getCollection()->whereNull('read_at')->count() }}
                    </h2>
                </div>
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-6">
                    <p class="opacity-70">
                        Read
                    </p>
                    <h2 id="readNotifications" class="mt-3 text-green-500">
                        {{ $notifications->getCollection()->whereNotNull('read_at')->count() }}
                    </h2>
                </div>
            </div>
            <div class="rounded-3xl border border-brand-border bg-brand-surface overflow-hidden">
                <div id="notificationList">
                    @forelse ($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $unread = is_null($notification->read_at);
                            $won = ($data['result'] ?? null) === 'won';
                            $isNewRound = ($data['type'] ?? null) === 'lottery_new_round';
                        @endphp
                        <div class="notification-card {{ $unread ? 'unread bg-green-500/5' : '' }} relative border-b border-brand-border p-6">
                            @if ($unread)
                                <span class="absolute top-8 right-1 w-2 h-2 rounded-full bg-green-500"></span>
                            @endif
                            <div class="flex gap-5">
                                <div class="w-14 h-14 rounded-full {{ $won ? 'bg-orange-500/20' : 'bg-green-500/20' }} flex items-center justify-center">
                                    <i class="fa-solid {{ $won ? 'fa-trophy text-orange-400' : ($isNewRound ? 'fa-rotate text-green-500' : 'fa-ticket text-green-500') }} text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h5>
                                            {{ $data['title'] ?? 'Notification' }}
                                        </h5>
                                        <span class="text-xs opacity-60">
                                            {{ $notification->created_at?->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="opacity-70 mt-2">
                                        {{ $data['message'] ?? '' }}
                                    </p>
                                    <div class="flex gap-3 mt-5">
                                        @if (isset($data['lottery_id']))
                                            <a href="{{ route('lotteries.show', $data['lottery_id']) }}" class="btn-secondary text-sm">
                                                View lottery
                                            </a>
                                        @endif
                                        @if ($unread)
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                                @csrf
                                                <button type="submit" class="btn-secondary text-sm notificationRead">
                                                    Mark Read
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-sm text-green-500">Read</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center opacity-60">
                            You have no notifications yet.
                        </div>
                    @endforelse
                </div>
                @if ($notifications->hasPages())
                    <div class="p-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
