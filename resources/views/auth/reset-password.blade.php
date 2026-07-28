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
            @if ($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="max-w-lg mx-auto">
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-8 lg:p-10">
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto rounded-full bg-brand-primary/10 flex items-center justify-center">
                            <i class="fa-solid fa-key text-3xl text-brand-primary"></i>
                        </div>
                        <h2 class="mt-6 md:text-2xl"">
                                        Reset Password
                                    </h2>
                                    <p class=" mt-3 opacity-70">
                            Create a new secure password for your account.
                            </p>
                    </div>
                    <form id="resetPasswordForm" action="{{ route('auth.password.update') }}" method="POST" class="mt-8">
                        @csrf
                        <div>
                            <label class="mb-2 block">
                                New Password
                            </label>
                            <div class="relative">
                                <input type="password" id="newPassword" name="password" placeholder="Enter new password"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 pr-12 outline-none focus:border-brand-primary"
                                    required>
                                <button type="button" class="togglePassword absolute right-4 top-1/2 -translate-y-1/2"
                                    data-target="newPassword">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <small id="emailVerificationError" class="mt-2 block text-red-500 text-[10px]">
                                @error('password')
                                    {{ $message }}
                                @enderror
                            </small>
                        </div>
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mt-6">
                            <label class="mb-2 block">
                                Confirm Password
                            </label>
                            <div class="relative">
                                <input type="password" id="confirmPassword" name="password_confirmation"
                                    placeholder="Confirm new password"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 pr-12 outline-none focus:border-brand-primary"
                                    required>
                                <button type="button" class="togglePassword absolute right-4 top-1/2 -translate-y-1/2"
                                    data-target="confirmPassword">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <small id="emailVerificationError" class="mt-2 block text-red-500 text-[10px]">
                                @error('password_confirmation')
                                    {{ $message }}
                                @enderror
                            </small>
                        </div>
                        <div class="mt-8">
                            <button type="submit" class="btn-primary w-full">
                                <i class="fa-solid fa-lock mr-2"></i>
                                Reset Password
                            </button>
                        </div>
                        <a href="{{ route('auth.login') }}" class="btn-secondary w-full mt-4 text-center">
                            <i class="fa-solid fa-arrow-left mr-2"></i>
                            Back To Login
                        </a>
                    </form>
                    <div id="resetSuccess"
                        class="hidden mt-6 rounded-2xl border border-green-500/30 bg-green-500/10 p-4 text-center">
                        <i class="fa-solid fa-circle-check text-3xl text-green-500"></i>
                        <h5 class="mt-3">
                            Password Updated
                        </h5>
                        <p class="mt-2 opacity-70">
                            Your password has been changed successfully.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection