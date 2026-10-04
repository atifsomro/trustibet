@extends('layouts.master')
@section('content')
    <section class="login py-10 md:py-16 lg:py-20">
        <div class="container">
            @if (session('info'))
                <div class="alert alert-info">
                    {{ session('info') }}
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
                            <i class="fa-solid fa-envelope-circle-check"></i>
                            Welcome Back
                        </span>
                        <h2 class="mt-4">
                            Verify Email &
                            <span class="bg-gradient-to-r from-green-500 to-orange-500 bg-clip-text text-transparent">
                                Start Your Journey
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
                            Email Verification
                        </h3>
                        <p class="mt-2">
                            Enter six letters code sent to your email
                        </p>

                        <form id="verificationForm" action="{{ route('auth.emailVerification') }}" method="POST"
                            class="mt-2">
                            @csrf
                            <div class="mt-2">
                                <label for="emailVerification" class="mb-2 block text-[10px] md:text-sm">
                                    Verification Code
                                </label>
                                <input name="code" type="text" id="emailVerification" placeholder="Enter code"
                                    class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)] text-[10px] md:text-sm">
                                <small id="emailVerificationError" class="mt-2 block text-red-500 text-[10px]">
                                    @error('code')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>

                            <input type="hidden" name="email" value="{{ session('email') }}">

                            <div class="flex flex-col items-center justify-center gap-2">
                                <button type="submit" class="btn-orange mt-2 w-full justify-center">
                                    <i class="fa-solid fa-circle-check mr-2"></i>
                                    Verify
                                </button>
                                <span class="text-gray-400">Don't received the code?</span>
                                <a href="{{ route('auth.resendCode') }}"
                                    class="font-semibold text-orange-400 hover:text-orange-300 transition">
                                    Resend
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
