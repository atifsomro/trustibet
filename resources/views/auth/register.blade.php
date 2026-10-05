@extends('layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('content')
    <section class="register py-6 sm:py-10 md:py-16 lg:py-20">
        <div class="container">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div
                class="register__wrapper relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-[0_0_45px_rgba(34,197,94,.12),0_0_70px_rgba(249,115,22,.08)]">

                <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="grid lg:grid-cols-2">

                    {{-- Left Side --}}
                    <div
                        class="register__content hidden lg:flex flex-col justify-center p-14 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.10),transparent_50%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.10),transparent_50%)]">
                        <span
                            class="inline-flex w-fit items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2 text-sm font-medium uppercase tracking-[3px] text-green-500">
                            <i class="fa-solid fa-user-plus"></i>
                            Join Our Community
                        </span>
                        <h2 class="mt-4">
                            Create Your
                            <span class="bg-gradient-to-r from-green-500 to-orange-500 bg-clip-text text-transparent">
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
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-green-500/10">
                                    <i class="fa-solid fa-circle-check text-green-500"></i>
                                </span>
                                <span>Quick Registration</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-orange-500/10">
                                    <i class="fa-solid fa-shield-halved text-orange-400"></i>
                                </span>
                                <span>Safe & Secure Platform</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-green-500/10">
                                    <i class="fa-solid fa-gift text-green-500"></i>
                                </span>
                                <span>Exclusive Member Rewards</span>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side --}}
                    <div class="register__form p-4 sm:p-8 md:p-12 lg:p-16">
                        <h3 class="text-green-500">
                            Create Account
                        </h3>
                        <p class="mt-2">
                            Fill in your information to create your account.
                        </p>

                        <form id="registerForm" action="{{ route('auth.register') }}" method="POST" class="mt-4 sm:mt-8">
                            @csrf
                            <input type="hidden" name="referral_code" value="{{ $referralCode ?? '' }}">
                            @if (!empty($referralCode))
                                <div class="mb-4 rounded-xl border border-orange-500/30 bg-orange-500/10 px-4 py-3 text-sm">
                                    Signing up with referral code
                                    <span class="font-semibold text-orange-400 tracking-widest">{{ $referralCode }}</span>
                                </div>
                            @endif

                            <div class="grid gap-3 sm:gap-6 md:grid-cols-2">
                                {{-- Full Name --}}
                                <div>
                                    <label for="fname" class="mb-2 block">
                                        Full Name
                                    </label>
                                    <input type="text" id="fname" name="name" value="{{ old('name') }}"
                                        placeholder="Enter your full name"
                                        class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
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
                                        class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
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
                                        class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
                                        required>
                                    <small id="emailError" class="mt-2 block text-[10px] text-red-500">
                                        @error('email')
                                            {{ $message }}
                                        @enderror
                                    </small>
                                </div>

                                {{-- Country --}}
                                <div>
                                    <label for="country_id" class="mb-2 block">
                                        Country
                                    </label>
                                    <select id="country_id" name="country_id"
                                        class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
                                        required>
                                        <option value="" selected disabled>Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ old('country_id') == $country->id ? 'selected' : '' }}>
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
                                        class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
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
                                        <input type="password" name="password" id="password"
                                            placeholder="Enter your password"
                                            class="password-field w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
                                            required>
                                        <button type="button" class="toggle-password">
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

                            {{-- Password strength --}}
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
                                <button type="submit" class="btn-orange mt-4 sm:mt-8 w-full justify-center">
                                    <i class="fa-solid fa-user-plus mr-2"></i>
                                    Register
                                </button>
                                <span class="text-gray-400">Or register with</span>
                                <a href="{{ route('auth.google') }}"
                                    class="w-10 h-10 p-1 bg-white flex rounded items-center justify-center mx-auto transition hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(34,197,94,.3)]"
                                    title="Register With Google">
                                    <img src="{{ asset('images/google/google.svg') }}" class="w-full" alt="google icon">
                                </a>
                            </div>
                        </form>

                        <p class="mt-3 sm:mt-6 text-center">
                            Already have an account?
                            <a href="{{ route('auth.login') }}"
                                class="font-semibold text-green-500 hover:text-green-400 transition">
                                Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
