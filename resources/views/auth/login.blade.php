@extends('layouts.master')
@section('content')
    <section class="login py-10 md:py-16 lg:py-20">
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
            <div class="login__wrapper overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                <div class="grid lg:grid-cols-2">
                    {{-- Left Side --}}
                    <div class="login__content hidden lg:flex flex-col justify-center p-14">
                        <small class="uppercase tracking-[3px] text-brand-primary">
                            Welcome Back
                        </small>
                        <h2 class="mt-4">
                            Sign In &
                            <span class="text-brand-primary text-inherit">
                                Start Winning
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
                            Login
                        </h3>
                        <p class="mt-2">
                            Enter your account credentials below.
                        </p>
                        <form id="loginForm" action="{{ route('auth.login.post') }}" method="POST" class="mt-4 sm:mt-8">
                            <div>
                                <label for="loginEmail" class="mb-2 block">
                                    Email Address
                                </label>
                                <input type="email" name="email" id="loginEmail" placeholder="Enter your email" autocomplete="email"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
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
                                <input type="password" name="password" id="loginPassword" placeholder="Enter your password" autocomplete="current-password"
                                    class="password-field w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                                     <button
                                        type="button"
                                        class="toggle-password absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-white">
                                        <i class="fa-regular fa-eye password-icon"></i>
                                    </button>
                                </div>
                                <small id="loginPasswordError" class="mt-2 block text-red-500 text-[10px]">
                                    @error('password')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>
                            <a href="{{ route('auth.forgotPasswordForm') }}" class="text-brand-primary hover:text-brand-primary-hover">
                                Forgot Password
                            </a>
                            <div class="flex flex-col items-center justify-center gap-2">
                                <button type="submit" class="btn-primary mt-4 sm:mt-8 w-full justify-center">
                                    Login
                                </button>
                                {{-- <a href="{{ route('forgot.password') }}"
                                    class="block w-fit mt-2 mx-auto text-brand-primary hover:underline text-sm">
                                    Forgot Password ?
                                </a>
                                <span>Or login with</span>
                                <a href="#"
                                    class="w-10 h-10 p-1 bg-white flex rounded items-center justify-center mx-auto"
                                    title="Login With Google">
                                    <img src="{{ asset('images/google/google.svg') }}" class="w-full" alt="google icon">
                                </a> --}}
                            </div>
                        </form>
                        <p class="mt-3 sm:mt-6 text-center">
                            Don't have an account?
                            <a href="{{ route('auth.showRegisterForm') }}" class="text-brand-primary hover:text-brand-primary-hover">
                                Register
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
