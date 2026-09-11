<header class="sticky top-0 z-50 py-3 lg:py-4 border-b border-brand-border bg-brand-surface">
    <div class="container">
        <div class="content flex items-center justify-between">

            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('/images/brand/logo.png') }}" class="w-32 md:w-36 lg:w-40 h-12 md:h-14 object-contain"
                    alt="Logo">
            </a>

            <nav class="hidden lg:block">
                <ul class="flex items-center gap-8">

                    <li>
                        <a href="/"
                            class="{{ request()->is('/') ? 'text-brand-primary' : '' }} hover:text-brand-primary transition">
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="{{ auth()->check() ? route('about') : route('auth.login') }}"
                            class="{{ request()->routeIs('about') ? 'text-brand-primary' : '' }} hover:text-brand-primary transition">
                            About
                        </a>
                    </li>

                    @auth
                        <li>
                            <a href="{{ route('user-account') }}"
                                class="{{ request()->routeIs('user-account') ? 'text-brand-primary' : '' }} hover:text-brand-primary transition">
                                My Account
                            </a>
                        </li>
                    @endauth

                    <li class="relative">
                        <button type="button" onclick="togglePagesDropdown()"
                            class="flex items-center gap-2 transition hover:text-brand-primary {{ request()->routeIs('lotteries.index', 'lotteries.history', 'investment', 'winner.history') ? 'text-brand-primary' : '' }}">
                            Pages
                            <i id="pagesArrow"
                                class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300"></i>
                        </button>

                        <div id="pagesDropdown"
                            class="pages-dropdown absolute right-0 top-full mt-3 w-60 rounded-2xl border border-brand-border bg-brand-surface p-2 shadow-2xl">

                            <a href="{{ auth()->check() ? route('lotteries.index') : route('auth.login') }}"
                                class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-green-500/10 {{ request()->routeIs('lotteries.index') ? 'bg-green-500/10 text-green-500' : '' }}">
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-green-500/10 text-green-500">
                                    <i class="fa-solid fa-ticket text-xs"></i>
                                </span>
                                <span class="text-sm">Lottery</span>
                            </a>

                            <a href="{{ auth()->check() ? route('investment') : route('auth.login') }}"
                                class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-orange-500/10 {{ request()->routeIs('investment') ? 'bg-orange-500/10 text-orange-500' : '' }}">
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-500/10 text-orange-500">
                                    <i class="fa-solid fa-chart-line text-xs"></i>
                                </span>
                                <span class="text-sm">Investment</span>
                            </a>

                            @auth
                                <a href="{{ route('lotteries.history') }}"
                                    class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-green-500/10 {{ request()->routeIs('lotteries.history') ? 'bg-green-500/10 text-green-500' : '' }}">
                                    <span
                                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-green-500/10 text-green-500">
                                        <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                    </span>
                                    <span class="text-sm">My History</span>
                                </a>
                            @endauth

                            <a href="{{ auth()->check() ? route('winner.history') : route('auth.login') }}"
                                class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-orange-500/10 {{ request()->routeIs('winner.history') ? 'bg-orange-500/10 text-orange-500' : '' }}">
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-500/10 text-orange-500">
                                    <i class="fa-solid fa-trophy text-xs"></i>
                                </span>
                                <span class="text-sm">Leader Board</span>
                            </a>

                        </div>
                    </li>

                </ul>
            </nav>

            <div class="flex items-center gap-3">
                @include('partials.inc.notification')

                @if (!auth()->check())
                    <a href="{{ route('auth.login') }}" class="btn btn-secondary hidden lg:inline-flex">
                        Login
                    </a>
                @else
                    <a href="{{ route('auth.logout') }}" class="btn btn-primary hidden lg:inline-flex">
                        Logout
                    </a>
                @endif

                <button id="MenuToggle" class="flex lg:hidden btn btn-primary">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>

        </div>
    </div>
</header>

<div id="Overlay" class="fixed inset-0 z-40 hidden bg-black/70 backdrop-blur-sm"></div>

<aside id="MobileMenu"
    class="fixed top-0 -right-full z-50 h-screen w-72 bg-brand-surface border-l border-brand-border shadow-2xl transition-all duration-300 overflow-y-auto">

    <div
        class="relative border-b border-brand-border bg-gradient-to-r from-green-500/10 via-brand-surface to-orange-500/10">

        <div class="absolute top-0 left-0 h-1 w-full bg-gradient-to-r from-green-500 via-brand-primary to-orange-500">
        </div>

        <div class="flex items-center justify-between px-6 py-5">

            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('/images/brand/logo.png') }}" class="w-28 object-contain" alt="">
            </a>

            <button id="MenuClose"
                class="flex h-10 w-10 items-center justify-center rounded-xl border border-brand-border bg-brand-dark hover:border-green-500 hover:text-green-500 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

    </div>

    <nav class="p-6">

        <ul class="space-y-3">

            <li>
                <a href="/"
                    class="flex items-center gap-4 rounded-2xl border px-5 py-4 transition
                    {{ request()->is('/') ? 'border-green-500 bg-green-500/10 text-green-500' : 'border-transparent bg-brand-dark hover:border-green-500 hover:bg-green-500/10' }}">
                    <i class="fa-solid fa-house w-5"></i>
                    <span>Home</span>
                </a>
            </li>

            <li>
                <a href="{{ route('about') }}"
                    class="flex items-center gap-4 rounded-2xl border px-5 py-4 transition
                    {{ request()->routeIs('about') ? 'border-green-500 bg-green-500/10 text-green-500' : 'border-transparent bg-brand-dark hover:border-green-500 hover:bg-green-500/10' }}">
                    <i class="fa-solid fa-circle-info w-5"></i>
                    <span>About</span>
                </a>
            </li>

            @auth
                <li>
                    <a href="{{ route('user-account') }}"
                        class="flex items-center gap-4 rounded-2xl border px-5 py-4 transition
                        {{ request()->routeIs('user-account') ? 'border-green-500 bg-green-500/10 text-green-500' : 'border-transparent bg-brand-dark hover:border-green-500 hover:bg-green-500/10' }}">
                        <i class="fa-solid fa-user w-5"></i>
                        <span>My Account</span>
                    </a>
                </li>
            @endauth

            <li>
                <button type="button" onclick="toggleMobilePages()"
                    class="flex w-full items-center justify-between rounded-2xl border px-5 py-4 transition
                    {{ request()->routeIs('lotteries.index', 'lotteries.history', 'investment', 'winner.history') ? 'border-orange-500 bg-orange-500/10 text-orange-500' : 'border-transparent bg-brand-dark hover:border-orange-500 hover:bg-orange-500/10' }}">

                    <span class="flex items-center gap-4">
                        <i class="fa-solid fa-layer-group w-5"></i>
                        <span>Pages</span>
                    </span>

                    <i id="mobilePagesArrow" class="fa-solid fa-chevron-down text-xs transition-transform duration-300">
                    </i>
                </button>

                <div id="mobilePagesDropdown" class="pages-mobile-dropdown">

                    <div class="mt-2 space-y-1 rounded-2xl border border-brand-border bg-brand-dark/50 p-2">

                        <a href="{{ auth()->check() ? route('lotteries.index') : route('auth.login') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-3 transition
                            {{ request()->routeIs('lotteries.index') ? 'bg-green-500/10 text-green-500' : 'hover:bg-green-500/10' }}">
                            <i class="fa-solid fa-ticket w-5 text-xs text-green-500"></i>
                            <span class="text-sm">Lottery</span>
                        </a>

                        <a href="{{ auth()->check() ? route('investment') : route('auth.login') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-3 transition
                            {{ request()->routeIs('investment') ? 'bg-orange-500/10 text-orange-500' : 'hover:bg-orange-500/10' }}">
                            <i class="fa-solid fa-chart-line w-5 text-xs text-orange-500"></i>
                            <span class="text-sm">Investment</span>
                        </a>

                        @auth
                            <a href="{{ route('lotteries.history') }}"
                                class="flex items-center gap-3 rounded-xl px-4 py-3 transition
                                {{ request()->routeIs('lotteries.history') ? 'bg-green-500/10 text-green-500' : 'hover:bg-green-500/10' }}">
                                <i class="fa-solid fa-clock-rotate-left w-5 text-xs text-green-500"></i>
                                <span class="text-sm">My History</span>
                            </a>
                        @endauth

                        <a href="{{ auth()->check() ? route('winner.history') : route('auth.login') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-3 transition
                            {{ request()->routeIs('winner.history') ? 'bg-orange-500/10 text-orange-500' : 'hover:bg-orange-500/10' }}">
                            <i class="fa-solid fa-trophy w-5 text-xs text-orange-500"></i>
                            <span class="text-sm">Leader Board</span>
                        </a>

                    </div>

                </div>
            </li>

        </ul>

        <div class="mt-4 space-y-3">

            <a href="{{ route('auth.login') }}"
                class="flex justify-center rounded-xl border border-brand-border py-3 hover:border-green-500 transition">
                Login
            </a>

            <a href="#"
                class="flex justify-center rounded-xl bg-gradient-to-r from-green-500 to-orange-500 py-3 font-semibold text-white shadow-lg shadow-green-500/20 hover:scale-[1.02] transition">
                Logout
            </a>

            <div
                class="mt-4 rounded-2xl border border-brand-border bg-gradient-to-br from-green-500/10 to-orange-500/10 p-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-500/15">
                        <i class="fa-solid fa-headset text-green-500 text-xl"></i>
                    </div>

                    <div>
                        <h6>Need Help?</h6>
                        <p class="text-xs opacity-70">
                            Support available 24/7
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </nav>

</aside>

@push('scripts')
    <script>
        function togglePagesDropdown() {
            document.getElementById('pagesDropdown').classList.toggle('show');
            document.getElementById('pagesArrow').classList.toggle('rotate-180');
        }

        function toggleMobilePages() {
            document.getElementById('mobilePagesDropdown').classList.toggle('show');
            document.getElementById('mobilePagesArrow').classList.toggle('rotate-180');
        }
    </script>
@endpush
