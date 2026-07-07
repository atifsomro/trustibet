<ul>
    <li class="relative">
        <button id="notificationBtn"
            class="relative w-12 h-12 rounded-full border border-brand-border bg-brand-surface flex items-center justify-center hover:border-brand-primary duration-300">
            <i class="fa-regular fa-bell text-lg"></i>
            <span id="notificationCount"
                class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-[10px] flex items-center justify-center">
                3
            </span>
        </button>

        <div id="notificationDropdown"
            class="hidden absolute right-0 mt-3 w-[380px] rounded-3xl border border-brand-border bg-brand-surface shadow-2xl z-50 overflow-hidden">

            <div class="flex items-center justify-between p-5 border-b border-brand-border">
                <h4>Notifications</h4>
                <button id="markAllRead" class="text-sm text-brand-primary">
                    Mark all as read
                </button>
            </div>

            <div class="max-h-420 overflow-y-auto">

                <div class="notification-item unread relative bg-green-500/5 border-b border-brand-border">
                    <span class="notification-dot absolute top-5 right-5 w-2.5 h-2.5 rounded-full bg-green-500"></span>
                    <a href="#" class="flex gap-4 p-5 hover:bg-brand-dark duration-300">
                        <div class="w-11 h-11 rounded-full bg-green-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-wallet text-green-500"></i>
                        </div>
                        <div>
                            <h6>Deposit Approved</h6>
                            <p class="text-sm opacity-70 mt-1">
                                Rs.2,000 has been added to your wallet.
                            </p>
                            <span class="text-xs opacity-50">
                                2 min ago
                            </span>
                        </div>
                    </a>
                </div>

                <div class="notification-item unread relative bg-green-500/5 border-b border-brand-border">
                    <span class="notification-dot absolute top-5 right-5 w-2.5 h-2.5 rounded-full bg-green-500"></span>
                    <a href="#" class="flex gap-4 p-5 hover:bg-brand-dark duration-300">
                        <div class="w-11 h-11 rounded-full bg-blue-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-ticket text-blue-500"></i>
                        </div>
                        <div>
                            <h6>Game Joined</h6>
                            <p class="text-sm opacity-70 mt-1">
                                You successfully joined the Rs.1 Draw.
                            </p>
                            <span class="text-xs opacity-50">
                                20 min ago
                            </span>
                        </div>
                    </a>
                </div>

                <div class="notification-item relative">
                    <a href="#" class="flex gap-4 p-5 hover:bg-brand-dark duration-300">
                        <div class="w-11 h-11 rounded-full bg-yellow-500/20 flex items-center justify-center">
                            <i class="fa-solid fa-gift text-yellow-500"></i>
                        </div>
                        <div>
                            <h6>Referral Bonus</h6>
                            <p class="text-sm opacity-70 mt-1">
                                You earned Rs.100 referral bonus.
                            </p>
                            <span class="text-xs opacity-50">
                                Yesterday
                            </span>
                        </div>
                    </a>
                </div>

            </div>

            <div class="p-5 border-t border-brand-border">
                <a href="{{ route('notifications') }}" class="btn-primary w-full text-center">
                    View All Notifications
                </a>
            </div>

        </div>
    </li>
</ul>
