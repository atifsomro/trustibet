@php
    $headerNotifications = auth('web')->check()
        ? auth('web')->user()->notifications()->latest()->limit(8)->get()
        : collect();
    $unreadCount = auth('web')->check()
        ? auth('web')->user()->unreadNotifications()->count()
        : 0;
@endphp

<ul>
    <li class="relative">

        <button id="notificationBtn"
            class="relative flex h-12 w-12 items-center justify-center rounded-full border border-brand-border bg-brand-surface transition hover:border-brand-primary">

            <i class="fa-regular fa-bell text-lg"></i>

            <span id="notificationCount"
                class="absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] text-white {{ $unreadCount ? '' : 'hidden' }}">
                {{ $unreadCount }}
            </span>

        </button>

        <div id="notificationDropdown"
            class="hidden fixed top-20 left-3 right-3 z-50 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-2xl md:absolute md:top-auto md:right-0 md:left-auto md:mt-3 md:w-[380px]">

            <div class="flex items-center justify-between border-b border-brand-border p-4 md:p-5">
                <h4 class="text-base">
                    Notifications
                </h4>
                @auth
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button id="markAllRead" type="submit" class="text-xs md:text-sm text-brand-primary whitespace-nowrap">
                            Mark all as read
                        </button>
                    </form>
                @endauth
            </div>

            <div class="max-h-[60vh] overflow-y-auto md:max-h-[420px]">
                @forelse ($headerNotifications as $notification)
                    @php
                        $data = $notification->data;
                        $unread = is_null($notification->read_at);
                        $won = ($data['result'] ?? null) === 'won';
                        $isNewRound = ($data['type'] ?? null) === 'lottery_new_round';
                    @endphp
                    <div class="notification-item {{ $unread ? 'unread bg-green-500/5' : '' }} relative border-b border-brand-border">
                        @if ($unread)
                            <span class="notification-dot absolute top-5 right-5 h-2.5 w-2.5 rounded-full bg-green-500"></span>
                        @endif
                        <a href="{{ isset($data['lottery_id']) ? route('lotteries.show', $data['lottery_id']) : route('notifications') }}"
                            class="flex items-start gap-3 p-4 transition hover:bg-brand-dark md:gap-4 md:p-5">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $won ? 'bg-orange-500/20' : 'bg-green-500/20' }} md:h-11 md:w-11">
                                <i class="fa-solid {{ $won ? 'fa-trophy text-orange-400' : ($isNewRound ? 'fa-rotate text-green-500' : 'fa-ticket text-green-500') }}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h6 class="text-sm md:text-base">
                                    {{ $data['title'] ?? 'Notification' }}
                                </h6>
                                <p class="mt-1 break-words text-xs opacity-70 md:text-sm">
                                    {{ $data['message'] ?? '' }}
                                </p>
                                <span class="text-[11px] opacity-50">
                                    {{ $notification->created_at?->diffForHumans() }}
                                </span>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="p-6 text-center text-sm opacity-60">
                        @auth
                            No notifications yet.
                        @else
                            Login to see notifications.
                        @endauth
                    </div>
                @endforelse
            </div>

            <div class="border-t border-brand-border p-4 md:p-5">
                <a href="{{ route('notifications') }}" class="btn-primary w-full text-center">
                    View All Notifications
                </a>
            </div>

        </div>

    </li>
</ul>
