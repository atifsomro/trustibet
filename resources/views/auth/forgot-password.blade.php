@extends('layouts.master')

@section('content')
    <section class="py-16">
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
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-8 lg:p-10">
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto rounded-full bg-brand-primary/10 flex items-center justify-center">
                            <i class="fa-solid fa-lock text-3xl text-brand-primary"></i>
                        </div>
                        <h2 class="mt-6 md:text-2xl">
                            Forgot Password
                        </h2>
                        <p class="mt-3 opacity-70">
                            Enter your registered email address and we'll send you a password reset link.
                        </p>
                    </div>
                    <form id="forgotPasswordForm" action="{{ route('auth.forgotPassword') }}" method="POST" class="mt-2">
                        @csrf
                        <div class="mt-2">
                            <label for="emailVerification" class="mb-2 block">
                                Email Address
                            </label>
                            <input name="email" type="text" id="email" placeholder="Enter your email"
                                class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                            <small id="emailVerificationError" class="mt-2 block text-red-500 text-[10px]">
                                @error('email')
                                    {{ $message }}
                                @enderror
                            </small>
                        </div>
                        <div class="flex flex-col items-center justify-center gap-2">
                            <button type="submit" class="btn-primary mt-2 w-full justify-center">
                                Send Reset Link
                            </button>
                        </div>
                        <a href="{{ route('auth.login') }}" class="btn-secondary w-full mt-4 text-center">
                            <i class="fa-solid fa-arrow-left mr-2"></i>
                            Back To Login
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
