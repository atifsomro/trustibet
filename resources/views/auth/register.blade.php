@extends('layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('content')
    <section class="register py-10 md:py-16 lg:py-20">
        <div class="container">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
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
                    <div class="register__form p-4 sm:p-8 md:p-12 lg:p-16">
                        <h3>
                            Create Account
                        </h3>
                        <p class="mt-2">
                            Fill in your information to create your account.
                        </p>
                        <form id="registerForm" action="{{ route('auth.register') }}" method="POST" class="mt-4 sm:mt-8">
                            @csrf
                            <div class="grid gap-3 sm:gap-6 md:grid-cols-2">
                                {{-- Full Name --}}
                                <div>
                                    <label for="fname" class="mb-2 block">
                                        Full Name
                                    </label>
                                    <input type="text" id="fname" name="name" value="{{ old('name') }}"
                                        placeholder="Enter your full name"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                    <small id="fnameError" class="mt-2 block text-[10px] text-red-500">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </small>
                                </div>
                                {{-- Username --}}
                                <div>
                                    <label for="username" class="mb-2 block">
                                        Username
                                    </label>
                                    <input type="text" id="username" value="{{ old('username') }}" name="username"
                                        placeholder="Choose a username"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                    <small id="unameError" class="mt-2 block text-[10px] text-red-500">
                                        @error('username')
                                            {{ $message }}
                                        @enderror
                                    </small>
                                </div>
                            </div>
                            <div class="grid gap-3 sm:gap-6 md:grid-cols-2 mt-3 sm:mt-6">
                                {{-- Email --}}
                                <div>
                                    <label for="email" class="mb-2 block">
                                        Email Address
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        placeholder="Enter your email"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                    <small id="emailError" class="mt-2 block text-[10px] text-red-500">
                                        @error('email')
                                            {{ $message }}
                                        @enderror
                                    </small>
                                </div>
                                {{-- Country --}}
                                <div>
                                    <label for="country" class="mb-2 block">
                                        Country
                                    </label>
                                    <select id="country_id" name="country_id"
                                        class="w-full rounded-x bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                        <option value="" selected disabled>Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                        
                                    </select>
                                    <small id="countryError" class="mt-2 block text-[10px] text-red-500">
                                        @error('country_id')
                                            {{ $message }}
                                        @enderror
                                    </small>
                                </div>
                            </div>
                            <div class="grid gap-3 sm:gap-6 md:grid-cols-2 mt-3 sm:mt-6">
                                {{-- Phone Number --}}
                                <div>
                                    <label for="phone" class="mb-2 block">
                                        Phone Number
                                    </label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                        placeholder="+92 300 1234567"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                    <small id="phoneError" class="mt-2 block text-[10px] text-red-500">
                                        @error('phone')
                                            {{ $message }}
                                        @enderror
                                    </small>
                                </div>
                                {{-- Password --}}
                                <div>
                                    <label for="password" class="mb-2 block">
                                        Password
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password" placeholder="Enter your password"
                                            class="password-field w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                            required>
                                             <button
                                                type="button"
                                                class="toggle-password absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-white">
                                                <i class="fa-regular fa-eye password-icon"></i>
                                            </button>
                                    </div>
                                    <small id="passwordError" class="mt-2 block text-[10px] text-red-500">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </small>
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
                            <div class="flex flex-col items-center justify-center gap-2">
                                <button type="submit" class="btn-primary mt-4 sm:mt-8 w-full justify-center">
                                    Register
                                </button>
                                {{-- <span>Or login with</span>
                                <a href="#" class="w-10 h-10 p-1 bg-white flex rounded items-center justify-center mx-auto"
                                    title="Login With Google">
                                    <img src="{{ asset('images/google/google.svg') }}" class="w-full" alt="google icon">
                                </a> --}}
                            </div>
                        </form>
                        <p class="mt-3 sm:mt-6 text-center">
                            Already have an account?
                            <a href="{{ route('auth.login') }}" class="text-brand-primary hover:text-brand-primary-hover">
                                Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection