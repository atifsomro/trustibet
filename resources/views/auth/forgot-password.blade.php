@extends('layouts.master')
{{-- Login
   ↓
Forgot Password
   ↓
Email enter
   ↓
Send Reset Link
   ↓
📧 User ko email aati hai

https://trustibet.com/reset-password/eyJ0eXAiOiJKV1Qi...

   ↓
User email me link par click karta hai
   ↓
Reset Password page open hota hai
   ↓
New Password
Confirm Password
   ↓
Reset
   ↓
Login --}}

@section('content')
    <section class="py-16">
        <div class="container">
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

                    <form id="forgotPasswordForm" class="mt-8">

                        <div>

                            <label class="mb-2 block">
                                Email Address
                            </label>

                            <input type="email" id="email" name="email" placeholder="Enter your email"
                                class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                required>

                        </div>

                        <button type="submit" class="btn-primary w-full mt-8">

                            <i class="fa-solid fa-paper-plane mr-2"></i>

                            Send Reset Link

                        </button>

                        <a href="{{ route('auth.login') }}" class="btn-secondary w-full mt-4 text-center">

                            <i class="fa-solid fa-arrow-left mr-2"></i>

                            Back To Login

                        </a>

                    </form>

                    <div id="successMessage"
                        class="hidden mt-6 rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-center">

                        <i class="fa-solid fa-circle-check text-3xl text-green-500"></i>

                        <h5 class="mt-3">
                            Reset Link Sent
                        </h5>

                        <p class="mt-2 opacity-70">
                            Please check your email inbox for the password reset link.
                        </p>

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
