@extends('layouts.master')
@section('content')
    <section class="register py-16 lg:py-20">
        <div class="container">
            <div class="register__wrapper overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                <div class="grid lg:grid-cols-2">
                    {{-- Left Side --}}
                    <div class="register__content hidden lg:flex flex-col justify-center p-14">
                        <small class="uppercase tracking-[3px] text-brand-primary">
                            Join Our Community
                        </small>
                        <h2 class="mt-4">
                            Create Your
                            <span class="text-brand-primary text-inherit">
                                Gaming Account
                            </span>
                        </h2>
                        <p class="mt-6">
                            Register today to access exciting casino games, exclusive
                            promotions, secure payments, and an unforgettable gaming
                            experience.
                        </p>
                        <div class="mt-10 space-y-5">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-circle-check text-brand-primary"></i>
                                <span>Quick Registration</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-shield-halved text-brand-primary"></i>
                                <span>Safe & Secure Platform</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-gift text-brand-primary"></i>
                                <span>Exclusive Member Rewards</span>
                            </div>
                        </div>
                    </div>
                    {{-- Right Side --}}
                    <div class="register__form p-8 md:p-12 lg:p-16">
                        <h3>
                            Create Account
                        </h3>
                        <p class="mt-2">
                            Fill in your information to create your account.
                        </p>
                        <form id="registerForm" class="mt-8">
                            <div class="grid gap-6 md:grid-cols-2">
                                {{-- Full Name --}}
                                <div>
                                    <label for="fname" class="mb-2 block">
                                        Full Name
                                    </label>
                                    <input type="text" id="fname" placeholder="Enter your full name"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                    <small id="fnameError" class="mt-2 block text-[10px] text-red-500"></small>
                                </div>
                                {{-- Username --}}
                                <div>
                                    <label for="uname" class="mb-2 block">
                                        Username
                                    </label>
                                    <input type="text" id="uname" placeholder="Choose a username"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                    <small id="unameError" class="mt-2 block text-[10px] text-red-500"></small>
                                </div>
                            </div>
                            <div class="grid gap-6 md:grid-cols-2 mt-6">
                                {{-- Email --}}
                                <div>
                                    <label for="email" class="mb-2 block">
                                        Email Address
                                    </label>
                                    <input type="email" id="email" placeholder="Enter your email"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                    <small id="emailError" class="mt-2 block text-[10px] text-red-500"></small>
                                </div>
                                {{-- Country --}}
                                <div>
                                    <label for="country" class="mb-2 block">
                                        Country
                                    </label>
                                    <select id="country"
                                        class="w-full rounded-x bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                        <option value="" selected disabled>Select Country</option>
                                        <option value="Pakistan">Pakistan</option>
                                        <option value="India">India</option>
                                        <option value="USA">United States</option>
                                    </select>
                                    <small id="countryError" class="mt-2 block text-[10px] text-red-500"></small>
                                </div>
                            </div>
                            <div class="grid gap-6 md:grid-cols-2 mt-6">
                                {{-- Phone Number --}}
                                <div>
                                    <label for="phone" class="mb-2 block">
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" placeholder="+92 300 1234567"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                    <small id="phoneError" class="mt-2 block text-[10px] text-red-500"></small>
                                </div>
                                {{-- Password --}}
                                <div>
                                    <label for="password" class="mb-2 block">
                                        Password
                                    </label>
                                    <input type="password" id="password" placeholder="Enter your password"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                    <small id="passwordError" class="mt-2 block text-[10px] text-red-500"></small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="h-2 overflow-hidden rounded-full bg-brand-border">
                                    <div id="passwordStrengthBar"
                                        class="h-full w-0 rounded-full transition-all duration-300">
                                    </div>
                                </div>
                                <small id="passwordStrengthText" class="mt-2 block">
                                </small>
                            </div>
                            <button type="submit" class="btn-primary mt-8 w-full justify-center">
                                Register
                            </button>
                        </form>
                        <p class="mt-6 text-center">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-brand-primary hover:text-brand-primary-hover">
                                Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
