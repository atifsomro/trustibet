<header class="sticky top-0 z-50 py-3 lg:py-4 border-b border-brand-border bg-brand-surface">
    <div class="container">
        <div class="content flex items-center justify-between">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('/images/brand/logo.png') }}" class="w-32 md:w-36 lg:w-40 h-12 md:h-14 object-contain"
                    alt="Logo">
            </a>
            {{-- Desktop Menu --}}
            <nav class="hidden md:block">
                <ul class="flex items-center gap-8">
                    <li><a class="hover:text-brand-primary" href="/">Home</a></li>
                    <li><a class="hover:text-brand-primary" href="{{ route('about') }}">About</a></li>
                    <li><a class="hover:text-brand-primary" href="{{ route('user-account') }}">My Account</a></li>
                    <li><a class="hover:text-brand-primary" href="{{ route('winner.history') }}">Winner History</a></li>
                </ul>
            </nav>
            {{-- Right --}}
            <div class="flex items-center gap-3">
                @include('partials.inc.notification')
                <a href="{{ route('login') }}" class="btn btn-secondary hidden md:inline-flex">
                    Login
                </a>
                <a href="#" class="btn btn-primary hidden md:inline-flex">
                    Logout
                </a>
                <button id="MenuToggle" class="flex md:hidden btn btn-primary">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
</header>

{{-- Overlay --}}
<div id="Overlay" class="fixed inset-0 z-40 hidden bg-black/60"></div>

{{-- Mobile Menu --}}
<aside id="MobileMenu" class="fixed top-0 -right-full z-50 h-screen w-60 bg-brand-dark transition-all duration-300">
    <div class="flex h-20 items-center justify-between border-b brand-light px-6">
        <span class="text-base">Menu</span>
        <button id="MenuClose">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>
    <nav class="p-6 space-y-6">
        <ul class="space-y-6">
            <li><a class="hover:text-brand-primary" href="/">Home</a></li>
            <li><a class="hover:text-brand-primary" href="{{ route('about') }}">About</a></li>
            <li><a class="hover:text-brand-primary" href="{{ route('user-account') }}">My Account</a></li>
            <li><a class="hover:text-brand-primary" href="{{ route('winner.history') }}">Winner History</a></li>
        </ul>
        <div class="flex flex-col gap-3">
            <a href="#" class="btn btn-secondary">
                Login
            </a>
            <a href="#" class="btn btn-primary">
                Logout
            </a>
        </div>
    </nav>
</aside>
