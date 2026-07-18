<ul>
    <li class="relative">

        <button id="notificationBtn"
            class="relative flex h-12 w-12 items-center justify-center rounded-full border border-brand-border bg-brand-surface transition hover:border-brand-primary">

            <i class="fa-regular fa-bell text-lg"></i>

            <span id="notificationCount"
                class="absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] text-white">
                3
            </span>

        </button>

        <div id="notificationDropdown"
            class="hidden fixed top-20 left-3 right-3 z-50 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-2xl md:absolute md:top-auto md:right-0 md:left-auto md:mt-3 md:w-[380px]">

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-brand-border p-4 md:p-5">

                <h4 class="text-base">
                    Notifications
                </h4>

                <button id="markAllRead" class="text-xs md:text-sm text-brand-primary whitespace-nowrap">
                    Mark all as read
                </button>

            </div>

            {{-- Body --}}
            <div class="max-h-[60vh] overflow-y-auto md:max-h-[420px]">

                {{-- Item --}}
                <div class="notification-item unread relative bg-green-500/5 border-b border-brand-border">

                    <span class="notification-dot absolute top-5 right-5 h-2.5 w-2.5 rounded-full bg-green-500">
                    </span>

                    <a href="#" class="flex items-start gap-3 p-4 transition hover:bg-brand-dark md:gap-4 md:p-5">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-500/20 md:h-11 md:w-11">

                            <i class="fa-solid fa-wallet text-green-500"></i>

                        </div>

                        <div class="min-w-0 flex-1">

                            <h6 class="text-sm md:text-base">
                                Deposit Approved
                            </h6>

                            <p class="mt-1 break-words text-xs opacity-70 md:text-sm">
                                Rs.2,000 has been added to your wallet.
                            </p>

                            <span class="text-[11px] opacity-50">
                                2 min ago
                            </span>

                        </div>

                    </a>

                </div>

                {{-- Item --}}
                <div class="notification-item unread relative bg-green-500/5 border-b border-brand-border">

                    <span class="notification-dot absolute top-5 right-5 h-2.5 w-2.5 rounded-full bg-green-500">
                    </span>

                    <a href="#" class="flex items-start gap-3 p-4 transition hover:bg-brand-dark md:gap-4 md:p-5">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-500/20 md:h-11 md:w-11">

                            <i class="fa-solid fa-ticket text-blue-500"></i>

                        </div>

                        <div class="min-w-0 flex-1">

                            <h6 class="text-sm md:text-base">
                                Game Joined
                            </h6>

                            <p class="mt-1 break-words text-xs opacity-70 md:text-sm">
                                You successfully joined the Rs.1 Draw.
                            </p>

                            <span class="text-[11px] opacity-50">
                                20 min ago
                            </span>

                        </div>

                    </a>

                </div>

                {{-- Item --}}
                <div class="notification-item">

                    <a href="#" class="flex items-start gap-3 p-4 transition hover:bg-brand-dark md:gap-4 md:p-5">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-yellow-500/20 md:h-11 md:w-11">

                            <i class="fa-solid fa-gift text-yellow-500"></i>

                        </div>

                        <div class="min-w-0 flex-1">

                            <h6 class="text-sm md:text-base">
                                Referral Bonus
                            </h6>

                            <p class="mt-1 break-words text-xs opacity-70 md:text-sm">
                                You earned Rs.100 referral bonus.
                            </p>

                            <span class="text-[11px] opacity-50">
                                Yesterday
                            </span>

                        </div>

                    </a>

                </div>

            </div>

            {{-- Footer --}}
            <div class="border-t border-brand-border p-4 md:p-5">

                <a href="{{ route('notifications') }}" class="btn-primary w-full text-center">
                    View All Notifications
                </a>

            </div>

        </div>

    </li>
</ul>
