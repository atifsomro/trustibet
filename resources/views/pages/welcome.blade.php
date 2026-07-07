@extends('layouts.master')
@section('content')
    <section class="welcome py-6 lg:pb-6 lg:pt-16">
        <div class="container">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Left Content --}}
                <div class="welcome__content">
                    <small class="uppercase tracking-[3px] text-brand-primary">
                        Welcome to TrustiBet
                    </small>
                    <h1 class="mt-4">
                        Welcome Back!
                    </h1>
                    <h2 class="mt-4">
                        Hello
                        <a href="#" class="text-brand-primary text-2xl">
                            User Name
                        </a>
                    </h2>
                    <p class="mt-6">

                        We're thrilled to have you join the TrustiBet family.
                        Your account is now ready, and an exciting world of casino
                        games, exclusive rewards, and big winning opportunities
                        awaits you.

                    </p>

                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('home') }}" class="btn-primary">
                            <i class="fa-solid fa-play mr-2"></i>
                            Let's Start Winning
                        </a>
                    </div>

                    {{-- Quick Features --}}
                    <div class="mt-10 flex flex-wrap gap-6">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-gift text-brand-primary text-xl"></i>
                            <span>Welcome Bonus</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-shield-halved text-brand-primary text-xl"></i>
                            <span>100% Secure</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-bolt text-brand-primary text-xl"></i>
                            <span>Fast Payouts</span>
                        </div>
                    </div>
                </div>
                {{-- Right Image --}}
                <div class="welcome__image">
                    <img src="{{ asset('images/welcome/welcome.png') }}" alt="Welcome" class="w-full max-w-xl mx-auto">
                </div>
            </div>
            {{-- Welcome Reward Modal --}}
            <div id="welcomeModal"
                class="fixed inset-0 z-999 hidden items-center justify-center bg-black/70 backdrop-blur-xs p-4">
                <div
                    class="relative w-full max-w-2xl rounded-3xl border border-brand-border bg-brand-surface p-8 text-center">
                    {{-- Close --}}
                    <button id="closeWelcomeModal"
                        class="absolute right-5 top-5 w-10 h-10 rounded-full border border-brand-border hover:border-brand-primary transition">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                    {{-- Icon --}}
                    <div
                        class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-brand-primary/15 border border-brand-primary/30">
                        <i class="fa-solid fa-gift text-5xl text-brand-primary"></i>
                    </div>
                    <small class="uppercase tracking-[3px] text-brand-primary">
                        Congratulations 🎉
                    </small>
                    <h2 class="mt-3">
                        Welcome
                        <span class="text-brand-primary text-2xl">
                            User Name
                        </span>
                    </h2>
                    <p class="mt-5">
                        As a welcome gift from <strong class="text-white">TrustiBet</strong>
                        you've received an exclusive joining reward.
                    </p>
                    {{-- Reward Box --}}
                    <div class="mt-8 rounded-2xl border border-brand-primary/30 bg-brand-primary/10 p-6">
                        <span class="block text-gray-300">
                            Joining Bonus
                        </span>
                        <h1 class="mt-2 text-brand-primary">
                            $100
                        </h1>
                        <p class="mt-2">
                            Added instantly to your account.
                        </p>
                    </div>
                    <button id="claimReward" class="btn-primary mt-8 w-full justify-center">
                        <i class="fa-solid fa-coins mr-2"></i>
                        Claim Reward
                    </button>
                </div>
            </div>
        </div>
    </section>
    @include('sections.welcome-about')
@endsection
