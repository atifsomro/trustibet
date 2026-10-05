@extends('layouts.master')
@section('content')
    <section class="login py-6 sm:py-10 md:py-16 lg:py-20">
        <div class="container">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @elseif (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div
                class="login__wrapper relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-[0_0_45px_rgba(34,197,94,.12),0_0_70px_rgba(249,115,22,.08)]">

                <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="grid lg:grid-cols-2">

                    {{-- Left Side --}}
                    <div
                        class="login__content hidden lg:flex flex-col justify-center p-14 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.10),transparent_50%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.10),transparent_50%)]">
                        <span
                            class="inline-flex w-fit items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2 text-sm font-medium uppercase tracking-[3px] text-green-500">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Welcome Back
                        </span>
                        <h2 class="mt-4">
                            Sign In &
                            <span class="bg-gradient-to-r from-green-500 to-orange-500 bg-clip-text text-transparent">
                                Start Winning
                            </span>
                        </h2>
                        <p class="mt-6">
                            Access your account to enjoy your favorite casino games,
                            track your winnings, and continue your gaming journey.
                        </p>
                        <div class="mt-10 space-y-5">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-green-500/10">
                                    <i class="fa-solid fa-shield-halved text-green-500"></i>
                                </span>
                                <span>100% Secure Login</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-orange-500/10">
                                    <i class="fa-solid fa-bolt text-orange-400"></i>
                                </span>
                                <span>Fast & Smooth Experience</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-green-500/10">
                                    <i class="fa-solid fa-headset text-green-500"></i>
                                </span>
                                <span>24/7 Customer Support</span>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side --}}
                    <div class="login__form p-4 sm:p-8 md:p-12 lg:p-16">
                        <h3 class="text-green-500">
                            Login
                        </h3>
                        <p class="mt-2">
                            Enter your account credentials below.
                        </p>

                        <form id="loginForm" action="{{ route('auth.login.post') }}" method="POST" class="mt-4 sm:mt-8">
                            @csrf
                            <div>
                                <label for="loginEmail" class="mb-2 block">
                                    Email Address
                                </label>
                                <input type="email" name="email" id="loginEmail" placeholder="Enter your email"
                                    autocomplete="email"
                                    class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)] transition">
                                <small id="loginEmailError" class="mt-2 block text-red-500 text-[10px]">
                                    @error('email')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>

                            <div class="mt-4">
                                <label for="loginPassword" class="mb-2 block">
                                    Password
                                </label>
                                <div class="relative">
                                    <input type="password" name="password" id="loginPassword"
                                        placeholder="Enter your password" autocomplete="current-password"
                                        class="password-field w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)] transition">
                                    <button type="button" class="toggle-password">
                                        <i class="fa-regular fa-eye password-icon"></i>
                                    </button>
                                </div>
                                <small id="loginPasswordError" class="mt-2 block text-red-500 text-[10px]">
                                    @error('password')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>

                            <a href="{{ route('auth.forgotPasswordForm') }}"
                                class="text-orange-400 hover:text-orange-300 transition">
                                Forgot Password
                            </a>

                            <div class="flex flex-col items-center justify-center gap-2">
                                <button type="submit" class="btn-orange mt-4 sm:mt-8 w-full justify-center">
                                    <i class="fa-solid fa-right-to-bracket mr-2"></i>
                                    Login
                                </button>
                                <span class="text-gray-400">Or login with</span>
                                <a href="{{ route('auth.google') }}"
                                    class="w-10 h-10 p-1 bg-white flex rounded items-center justify-center mx-auto transition hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(34,197,94,.3)]"
                                    title="Login With Google">
                                    <img src="{{ asset('images/google/google.svg') }}" class="w-full" alt="google icon">
                                </a>
                            </div>
                        </form>

                        <p class="mt-3 sm:mt-6 text-center">
                            Don't have an account?
                            <a href="{{ route('auth.showRegisterForm') }}"
                                class="font-semibold text-green-500 hover:text-green-400 transition">
                                Register
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
