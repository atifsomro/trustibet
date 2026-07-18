<header class="sticky top-0 z-50 py-3 lg:py-4 border-b border-brand-border bg-brand-surface">
    <div class="container">
        <div class="content flex items-center justify-between">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('/images/brand/logo.png') }}" class="w-32 md:w-36 lg:w-40 h-12 md:h-14 object-contain"
                    alt="Logo">
            </a>
            {{-- Desktop Menu --}}
            <nav class="hidden lg:block">
                <ul class="flex items-center gap-8">
                    <li><a class="hover:text-brand-primary" href="/">Home</a></li>
                    <li><a class="hover:text-brand-primary" href="{{ route('about') }}">About</a></li>
                    <li><a class="hover:text-brand-primary" href="{{ route('lottery') }}">Lottery</a></li>
                    <li><a class="hover:text-brand-primary" href="{{ route('investment') }}">Investment</a></li>
                    <li><a class="hover:text-brand-primary" href="{{ route('user-account') }}">My Account</a></li>
                    <li><a class="hover:text-brand-primary" href="{{ route('winner.history') }}">Leader Board</a></li>
                </ul>
            </nav>
            {{-- Right --}}
            <div class="flex items-center gap-3">
                @include('partials.inc.notification')
                <a href="{{ route('login') }}" class="btn btn-secondary hidden lg:inline-flex">
                    Login
                </a>
                <a href="#" class="btn btn-primary hidden lg:inline-flex">
                    Logout
                </a>
                <button id="MenuToggle" class="flex lg:hidden btn btn-primary">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
</header>

{{-- Overlay --}}
<div id="Overlay" class="fixed inset-0 z-40 hidden bg-black/70 backdrop-blur-sm"></div>
{{-- Mobile Menu --}}
<aside id="MobileMenu"
    class="fixed top-0 -right-full z-50 h-screen w-72 bg-brand-surface border-l border-brand-border shadow-2xl transition-all duration-300 overflow-y-auto">

    {{-- Top --}}
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

    {{-- Menu --}}
    <nav class="p-6">

        <ul class="space-y-3">

            <li>
                <a href="/"
                    class="flex items-center gap-4 rounded-2xl border border-transparent bg-brand-dark px-5 py-4 transition hover:border-green-500 hover:bg-green-500/10">
                    <i class="fa-solid fa-house text-green-500 w-5"></i>
                    <span>Home</span>
                </a>
            </li>
            <li>
                <a href="{{ route('about') }}"
                    class="flex items-center gap-4 rounded-2xl border border-transparent bg-brand-dark px-5 py-4 transition hover:border-green-500 hover:bg-green-500/10">
                    <i class="fa-solid fa-circle-info text-green-500 w-5"></i>
                    <span>About</span>
                </a>
            </li>
            <li>
                <a href="{{ route('lottery') }}"
                    class="flex items-center gap-4 rounded-2xl border border-transparent bg-brand-dark px-5 py-4 transition hover:border-orange-500 hover:bg-orange-500/10">
                    <i class="fa-solid fa-ticket text-orange-500 w-5"></i>
                    <span>Lottery</span>
                </a>
            </li>
            <li>
                <a href="{{ route('investment') }}"
                    class="flex items-center gap-4 rounded-2xl border border-transparent bg-brand-dark px-5 py-4 transition hover:border-orange-500 hover:bg-orange-500/10">
                    <i class="fa-solid fa-chart-line text-orange-500 w-5"></i>
                    <span>Investment</span>
                </a>
            </li>
            <li>
                <a href="{{ route('user-account') }}"
                    class="flex items-center gap-4 rounded-2xl border border-transparent bg-brand-dark px-5 py-4 transition hover:border-green-500 hover:bg-green-500/10">
                    <i class="fa-solid fa-user text-green-500 w-5"></i>
                    <span>My Account</span>
                </a>
            </li>
            <li>
                <a href="{{ route('winner.history') }}"
                    class="flex items-center gap-4 rounded-2xl border border-transparent bg-brand-dark px-5 py-4 transition hover:border-orange-500 hover:bg-orange-500/10">
                    <i class="fa-solid fa-trophy text-orange-500 w-5"></i>
                    <span>Leader Board</span>
                </a>
            </li>
        </ul>
        {{-- Buttons --}}
        <div class="mt-4 space-y-3">
            <a href="{{ route('login') }}"
                class="flex justify-center rounded-xl border border-brand-border py-3 hover:border-green-500 transition">
                Login
            </a>
            <a href="#"
                class="flex justify-center rounded-xl bg-gradient-to-r from-green-500 to-orange-500 py-3 font-semibold text-white shadow-lg shadow-green-500/20 hover:scale-[1.02] transition">
                Logout
            </a>
            {{-- Bottom Card --}}
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
