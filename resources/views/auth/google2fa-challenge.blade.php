@extends('layouts.master')
@section('content')
    <section class="login py-6 sm:py-10 md:py-16 lg:py-20">
        <div class="container">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @elseif (session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @elseif (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="login__wrapper overflow-hidden rounded-3xl border border-brand-border bg-brand-surface">
                <div class="grid lg:grid-cols-2">
                    <div class="login__content hidden lg:flex flex-col justify-center p-14">
                        <small class="uppercase tracking-[3px] text-brand-primary">
                            Extra Security
                        </small>
                        <h2 class="mt-4">
                            Authenticator
                            <span class="text-brand-primary text-inherit">Verification</span>
                        </h2>
                        <p class="mt-6">
                            Open Google Authenticator on your phone and enter the 6-digit code
                            to finish signing in.
                        </p>
                    </div>
                    <div class="login__form p-4 sm:p-8 md:p-12 lg:p-16">
                        <h3>Google Authenticator</h3>
                        <p class="mt-2 text-sm">
                            Enter the code for
                            <strong>{{ $user->email }}</strong>
                        </p>

                        <form action="{{ route('auth.google2fa.challenge.verify') }}" method="POST" class="mt-6">
                            @csrf
                            <div>
                                <label for="code" class="mb-2 block text-[10px] md:text-sm">
                                    6-digit code
                                </label>
                                <input name="code"
                                       type="text"
                                       id="code"
                                       inputmode="numeric"
                                       autocomplete="one-time-code"
                                       maxlength="6"
                                       placeholder="000000"
                                       class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary text-[10px] md:text-sm tracking-widest text-center"
                                       required
                                       autofocus>
                                @error('code')
                                    <small class="mt-2 block text-red-500 text-[10px]">{{ $message }}</small>
                                @enderror
                            </div>
                            <button type="submit" class="btn-primary mt-4 w-full justify-center">
                                Verify &amp; Sign In
                            </button>
                        </form>

                        <p class="mt-4 text-center text-sm">
                            <a href="{{ route('auth.login') }}" class="text-brand-primary hover:text-brand-primary-hover">
                                Back to login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
