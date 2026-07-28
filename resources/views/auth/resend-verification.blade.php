@extends('layouts.master')

@section('content')
<section class="login relative overflow-hidden py-10 md:py-16 lg:py-20">
    <div class="container">
        @if(session('info'))
            <div class="mb-6 rounded-xl border border-brand-primary/30 bg-brand-primary/10 px-5 py-4 text-brand-light">
                {{ session('info') }}
            </div>
        @endif
        <div class="overflow-hidden rounded-[32px] border border-brand-border bg-brand-surface shadow-[0_25px_80px_rgba(2,132,199,.15)]">
            <div class="grid lg:grid-cols-2">
                <div class="relative hidden overflow-hidden lg:flex">
                    <div class="relative z-10 flex flex-col justify-center p-14">
                        <span
                            class="inline-flex w-fit rounded-full border border-brand-primary/30 bg-brand-primary/10 px-5 py-2 text-xs font-semibold uppercase tracking-[4px] text-brand-accent">
                            Welcome Back
                        </span>
                        <h2 class="mt-7 text-5xl font-bold leading-tight text-brand-light">
                            Verify Your
                            <span class="text-brand-primary">
                                Email
                            </span>
                            &
                            Continue Playing
                        </h2>
                        <p class="mt-6 max-w-lg leading-8 text-gray-300">
                            Secure your account by verifying your email address.
                            Once verified, you'll be able to access your dashboard,
                            claim rewards, and continue enjoying your favorite games.
                        </p>
                        <div class="mt-10 space-y-5">
                            <div class="flex items-center gap-4 rounded-2xl border border-brand-border bg-white/5 p-5 backdrop-blur">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-primary/10">
                                    <i class="fa-solid fa-shield-halved text-xl text-brand-primary"></i>
                                </div>
                                <div>
                                    <h6 class="font-semibold text-brand-light">
                                        Secure Verification
                                    </h6>
                                    <p class="mt-1 text-sm text-gray-400">
                                        Protected with encrypted authentication.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 rounded-2xl border border-brand-border bg-white/5 p-5 backdrop-blur">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-primary/10">
                                    <i class="fa-solid fa-bolt text-xl text-brand-primary"></i>
                                </div>
                                <div>
                                    <h6 class="font-semibold text-brand-light">
                                        Instant Code Delivery
                                    </h6>
                                    <p class="mt-1 text-sm text-gray-400">
                                        Receive your verification code within seconds.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 rounded-2xl border border-brand-border bg-white/5 p-5 backdrop-blur">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-primary/10">
                                    <i class="fa-solid fa-headset text-xl text-brand-primary"></i>
                                </div>
                                <div>
                                    <h6 class="font-semibold text-brand-light">
                                        24/7 Support
                                    </h6>
                                    <p class="mt-1 text-sm text-gray-400">
                                        Our team is always here whenever you need help.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-center bg-brand-surface p-5 sm:p-8 md:p-12 lg:p-16">
                    <div class="w-full max-w-md">
                        <div class="rounded-3xl border border-brand-border bg-brand-dark/90 p-8 shadow-2xl backdrop-blur">
                            <div class="mb-8 text-center">
                                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-brand-primary/10">
                                    <i class="fa-solid fa-envelope-circle-check text-4xl text-brand-primary"></i>
                                </div>
                                <h3 class="text-3xl font-bold text-brand-light">
                                    Email Verification
                                </h3>
                                <p class="mt-3 text-sm leading-7 text-gray-400">
                                    Enter your registered email address below to receive
                                    a new verification code.
                                </p>
                            </div>
                            {{-- Backend remains unchanged --}}
                            <form id="verificationForm"
                                  action="{{ route('auth.resendCode') }}"
                                  method="POST"
                                  class="space-y-6">
                                @csrf
                                <div>
                                    <label
                                        for="emailVerification"
                                        class="mb-2 block font-medium text-brand-light">
                                        Email Address
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-brand-primary">
                                            <i class="fa-solid fa-envelope"></i>
                                        </span>
                                        <input
                                            name="email"
                                            type="email"
                                            id="emailVerification"
                                            placeholder="Enter your email"
                                            class="h-14 w-full rounded-xl border border-brand-border bg-brand-surface pl-14 pr-4 text-brand-light outline-none transition-all duration-300 placeholder:text-gray-500 focus:border-brand-primary focus:ring-4 focus:ring-brand-primary/20">
                                    </div>
                                    <small
                                        id="emailVerificationError"
                                        class="mt-2 block text-xs text-red-500">
                                        @error('email')
                                            {{ $message }}
                                        @enderror
                                    </small>
                                </div>
                                <button
                                    type="submit"
                                    class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-brand-primary font-semibold text-white transition-all duration-300 hover:scale-[1.02] hover:bg-brand-primary-hover">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    Resend Verification Code
                                </button>
                            </form>
                            <div class="mt-8 border-t border-brand-border pt-6 text-center">
                                <p class="text-sm text-gray-400">
                                    Didn't receive the email?
                                    <span class="text-brand-primary">
                                        Check your spam folder or request a new code.
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection