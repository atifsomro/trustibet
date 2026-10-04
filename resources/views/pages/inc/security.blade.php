@php
    $securityUser = auth()->user();
    $gaManagedByAdmin = (bool) $securityUser->google2fa_managed_by_admin;
    $gaEnabled = (bool) $securityUser->google2fa_enabled;
    $gaConfirmed = $gaEnabled && $securityUser->google2fa_confirmed_at;
    $gaPending = $gaEnabled && ! $securityUser->google2fa_confirmed_at && ! $gaManagedByAdmin;
@endphp

<div class="security">
    <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h3>Google Authenticator</h3>
                <p class="mt-2 opacity-70">
                    Protect withdrawals with a one-time code from your authenticator app.
                </p>
            </div>
            <div>
                @if ($gaManagedByAdmin && $gaConfirmed)
                    <span class="inline-flex items-center gap-2 rounded-xl bg-red-500/15 px-4 py-2 text-sm text-red-400">
                        <i class="fa-solid fa-lock"></i>
                        Managed by admin
                    </span>
                @elseif ($gaManagedByAdmin)
                    <span class="inline-flex items-center gap-2 rounded-xl bg-yellow-500/15 px-4 py-2 text-sm text-yellow-400">
                        <i class="fa-solid fa-lock"></i>
                        Managed by admin
                    </span>
                @elseif ($gaConfirmed)
                    <span class="inline-flex items-center gap-2 rounded-xl bg-green-500/15 px-4 py-2 text-sm text-green-400">
                        <i class="fa-solid fa-shield-halved"></i>
                        On
                    </span>
                @elseif ($gaPending)
                    <span class="inline-flex items-center gap-2 rounded-xl bg-yellow-500/15 px-4 py-2 text-sm text-yellow-400">
                        <i class="fa-solid fa-hourglass-half"></i>
                        Pending setup
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 rounded-xl bg-white/5 px-4 py-2 text-sm opacity-70">
                        <i class="fa-solid fa-shield"></i>
                        Off
                    </span>
                @endif
            </div>
        </div>

        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                {{ $errors->first() }}
            </div>
        @endif

        @if ($gaManagedByAdmin)
            <p class="text-sm opacity-80">
                Google Authenticator for this account is controlled by an administrator.
                You cannot enable, change, or disable it from here. Contact support if you need access.
            </p>
        @elseif (! $gaEnabled)
            <p class="mb-6 text-sm opacity-80">
                When turned on, you will enter a 6-digit code from Google Authenticator
                each time you submit a withdrawal request, and also when you log in again after logout.
            </p>
            <form method="POST" action="{{ route('security.google2fa.enable') }}">
                @csrf
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-mobile-screen-button mr-2"></i>
                    Enable Google Authenticator
                </button>
            </form>
        @elseif ($gaPending)
            <p class="mb-4 text-sm opacity-80">
                Open Google Authenticator (or any TOTP app), scan this QR code, then enter the 6-digit code below.
            </p>

            @if (! empty($google2faQrSvg ?? null) && ! empty($google2faSecret ?? null))
                <div class="mb-4 flex justify-center rounded-xl bg-white p-4 w-fit mx-auto">
                    {!! $google2faQrSvg !!}
                </div>
                <p class="mb-6 text-center text-xs opacity-70 break-all">
                    Or enter this key manually:<br>
                    <strong class="tracking-widest">{{ $google2faSecret }}</strong>
                </p>
            @else
                <p class="mb-6 text-sm text-yellow-400">
                    Unable to load your setup QR code. Disable and enable again, or contact support.
                </p>
            @endif

            <form method="POST" action="{{ route('security.google2fa.confirm') }}" class="max-w-md">
                @csrf
                <label for="google2fa_confirm_code" class="mb-2 block text-sm">
                    6-digit code
                </label>
                <input
                    type="text"
                    name="code"
                    id="google2fa_confirm_code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    placeholder="000000"
                    value="{{ old('code') }}"
                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary tracking-widest text-center"
                    required
                >
                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="submit" class="btn-primary">
                        Confirm &amp; Turn On
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('security.google2fa.disable') }}" class="mt-4">
                @csrf
                <button type="submit" class="btn-secondary">
                    Cancel setup
                </button>
            </form>
        @else
            <p class="mb-6 text-sm opacity-80">
                Withdrawals require a code from your authenticator app.
                Enter a current code below if you want to turn this off.
            </p>
            <form method="POST" action="{{ route('security.google2fa.disable') }}" class="max-w-md">
                @csrf
                <label for="google2fa_disable_code" class="mb-2 block text-sm">
                    6-digit code to disable
                </label>
                <input
                    type="text"
                    name="code"
                    id="google2fa_disable_code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    placeholder="000000"
                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary tracking-widest text-center"
                    required
                >
                <button type="submit" class="btn-secondary mt-4">
                    <i class="fa-solid fa-shield-halved mr-2"></i>
                    Turn Off Google Authenticator
                </button>
            </form>
        @endif
    </div>
</div>
