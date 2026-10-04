@extends('layouts.master')

@section('content')
    <section class="py-6 sm:py-10 md:py-16 lg:py-20">
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

            <div class="max-w-lg mx-auto">
                <div
                    class="relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-[0_0_45px_rgba(34,197,94,.12),0_0_70px_rgba(249,115,22,.08)]">

                    <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                    <div
                        class="p-8 lg:p-10 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.08),transparent_45%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.08),transparent_45%)]">
                        <div class="text-center">
                            <div
                                class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-green-500/20 to-orange-500/20 border border-green-500/30 flex items-center justify-center shadow-[0_0_25px_rgba(34,197,94,.2)]">
                                <i class="fa-solid fa-lock text-3xl text-green-500"></i>
                            </div>
                            <h2
                                class="mt-6 md:text-2xl bg-gradient-to-r from-green-500 to-orange-500 bg-clip-text text-transparent">
                                Forgot Password
                            </h2>
                            <p class="mt-3 opacity-70">
                                Enter your registered email address and we'll send you a password reset link.
                            </p>
                        </div>

                        <form id="forgotPasswordForm" action="{{ route('auth.forgotPassword') }}" method="POST"
                            class="mt-2">
                            @csrf
                            <div class="mt-2">
                                <label for="email" class="mb-2 block">
                                    Email Address
                                </label>
                                <input name="email" type="text" id="email" placeholder="Enter your email"
                                    class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]">
                                <small id="emailVerificationError" class="mt-2 block text-red-500 text-[10px]">
                                    @error('email')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>

                            <div class="flex flex-col items-center justify-center gap-2">
                                <button type="submit" class="btn-orange mt-2 w-full justify-center">
                                    <i class="fa-solid fa-paper-plane mr-2"></i>
                                    Send Reset Link
                                </button>
                            </div>

                            <a href="{{ route('auth.login') }}"
                                class="btn-secondary w-full mt-4 text-center transition hover:!border-green-500 hover:!text-green-500">
                                <i class="fa-solid fa-arrow-left mr-2"></i>
                                Back To Login
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
