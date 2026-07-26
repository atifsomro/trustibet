@extends('layouts.master')
@section('content')
    <section class="login py-10 md:py-16 lg:py-20">
        <div class="container">
            @if(session('info'))
                <div class="alert alert-info">
                    {{ session('info') }}
                </div>
            @endif
            <div class="login__wrapper overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                <div class="grid lg:grid-cols-2">
                    {{-- Left Side --}}
                    <div class="login__content hidden lg:flex flex-col justify-center p-14">
                        <small class="uppercase tracking-[3px] text-brand-primary">
                            Welcome Back
                        </small>
                        <h2 class="mt-4">
                            Verify Email &
                            <span class="text-brand-primary text-inherit">
                                Start Your Journey
                            </span>
                        </h2>
                        <p class="mt-6">
                            Access your account to enjoy your favorite casino games,
                            track your winnings, and continue your gaming journey.
                        </p>
                        <div class="mt-10 space-y-5">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-shield-halved text-brand-primary"></i>
                                <span>100% Secure Login</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-bolt text-brand-primary"></i>
                                <span>Fast & Smooth Experience</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-headset text-brand-primary"></i>
                                <span>24/7 Customer Support</span>
                            </div>
                        </div>
                    </div>
                    {{-- Right Side --}}
                    <div class="login__form p-4 sm:p-8 md:p-12 lg:p-16">
                        <h3>
                            Email Verification
                        </h3>
                        <p class="mt-2">
                            Enter six letters code sent to your email
                        </p>
                        <form id="verificationForm" action="{{ route('auth.emailVerification') }}" method="POST" class="mt-4 sm:mt-8">
                            @csrf
                            <div class="mt-4">
                                <label for="emailVerification" class="mb-2 block">
                                    Verification Code
                                </label>
                                <input name="code" type="text" id="emailVerification" placeholder="Enter your password"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                                <small id="emailVerificationError" class="mt-2 block text-red-500 text-[10px]">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>
                            <input type="hidden" name="email" value="{{ session('email') }}">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <button type="submit" class="btn-primary mt-4 sm:mt-8 w-full justify-center">
                                    Verify
                                </button>
                                Don't received the code?
                                <a href="{{ route('auth.resendCode') }}" class="text-brand-primary hover:text-brand-primary-hover">
                                    Resend
                                </a>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
