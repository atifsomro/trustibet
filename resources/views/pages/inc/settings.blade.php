@php
    $settingsUser = auth()->user();
    $prefs = $settingsUser->notificationPreferences();
    $requiresCurrentPassword = $settingsUser->hasChosenPassword();
    $emailVerified = $settingsUser->email_verified_at !== null;
    $hasPhone = filled($settingsUser->phone);
    $kycStatus = $kyc?->status;
    $kycLabel = match ($kycStatus) {
        'approved' => 'Approved',
        'pending' => 'Pending',
        'rejected' => 'Rejected',
        default => 'Not submitted',
    };
    $kycClass = match ($kycStatus) {
        'approved' => 'text-green-500',
        'rejected' => 'text-red-500',
        default => 'text-yellow-500',
    };
    $lastLogin = $settingsUser->lastLoginLabel()
        ?: 'This session - '.\App\Models\User::describeUserAgent(request()->userAgent());
@endphp

<div class="settings">
    {{-- Change Password --}}
    <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
        <div class="mb-8">
            <h3>Change Password</h3>
            <p class="mt-2 opacity-70">
                Keep your account secure by updating your password regularly.
            </p>
        </div>
        <form id="changePasswordForm" method="POST" action="{{ route('settings.password.update') }}">
            @csrf
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label for="current_password" class="mb-2 block">
                        Current Password
                    </label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password" required
                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                </div>
                <div>
                    <label for="new_password" class="mb-2 block">
                        New Password
                    </label>
                    <input type="password" id="new_password" name="new_password" autocomplete="new-password" required
                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                </div>
                <div>
                    <label for="confirm_password" class="mb-2 block">
                        Confirm Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required
                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                </div>
            </div>
            <button type="submit" class="btn-primary mt-8">
                <i class="fa-solid fa-lock mr-2"></i>
                Update Password
            </button>
        </form>
    </div>
    {{-- Notification Settings --}}
    <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8 mt-3 sm:mt-6">
        <h3>Notification Preferences</h3>
        <p class="mt-2 opacity-70">
            Choose which notifications you want to receive.
        </p>
        <form class="mt-8" method="POST" action="{{ route('settings.notifications.update') }}">
            @csrf
            <div class="space-y-5">
                <label class="flex items-center justify-between">
                    <span>Email Notifications</span>
                    <input type="checkbox" name="email_notifications" value="1" @checked(old('email_notifications', $prefs['email']))>
                </label>
                <label class="flex items-center justify-between">
                    <span>SMS Notifications</span>
                    <input type="checkbox" name="sms_notifications" value="1" @checked(old('sms_notifications', $prefs['sms']))>
                </label>
                <label class="flex items-center justify-between">
                    <span>Promotional Offers</span>
                    <input type="checkbox" name="offers_notifications" value="1" @checked(old('offers_notifications', $prefs['offers']))>
                </label>
                <label class="flex items-center justify-between">
                    <span>Prize Winner Alerts</span>
                    <input type="checkbox" name="winner_notifications" value="1" @checked(old('winner_notifications', $prefs['winner']))>
                </label>
                <label class="flex items-center justify-between">
                    <span>Deposit & Withdrawal Updates</span>
                    <input type="checkbox" name="transaction_notifications" value="1" @checked(old('transaction_notifications', $prefs['transaction']))>
                </label>
            </div>
            <button type="submit" class="btn-primary mt-8">
                Save Preferences
            </button>
        </form>
    </div>
    {{-- Security --}}
    <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8 mt-3 sm:mt-6">
        <h3>Account Security</h3>
        <div class="grid md:grid-cols-2 gap-6 mt-8">
            <div class="rounded-2xl bg-brand-dark p-6">
                <h5>Email Verification</h5>
                <p class="mt-2 {{ $emailVerified ? 'text-green-500' : 'text-yellow-500' }}">
                    {{ $emailVerified ? 'Verified' : 'Not verified' }}
                </p>
            </div>
            <div class="rounded-2xl bg-brand-dark p-6">
                <h5>Phone Verification</h5>
                <p class="mt-2 {{ $hasPhone ? 'text-green-500' : 'text-yellow-500' }}">
                    {{ $hasPhone ? 'Added' : 'Not added' }}
                </p>
            </div>
            <div class="rounded-2xl bg-brand-dark p-6">
                <h5>KYC Status</h5>
                <p class="mt-2 {{ $kycClass }}">
                    {{ $kycLabel }}
                </p>
            </div>
            <div class="rounded-2xl bg-brand-dark p-6">
                <h5>Last Login</h5>
                <p class="mt-2">
                    {{ $lastLogin }}
                </p>
            </div>
        </div>
    </div>
    {{-- Danger Zone --}}
    <div class="rounded-3xl border border-red-500/30 bg-red-500/10 p-4 md:p-8 mt-3 sm:mt-6">
        <h3 class="text-red-500">
            Danger Zone
        </h3>
        <p class="mt-3 opacity-70">
            Logout from your account or permanently delete your account.
        </p>
        <div class="flex flex-wrap gap-4 mt-8">
            <a href="{{ route('auth.logout') }}" class="btn-secondary">
                <i class="fa-solid fa-right-from-bracket mr-2"></i>
                Logout
            </a>
            <button type="button" id="deleteAccountBtn" class="rounded-xl bg-red-600 px-6 py-3 text-white hover:bg-red-700 duration-300">
                <i class="fa-solid fa-trash mr-2"></i>
                Delete Account
            </button>
        </div>
        <form id="deleteAccountForm" method="POST" action="{{ route('settings.account.destroy') }}" class="hidden">
            @csrf
            <input type="hidden" name="password" id="deleteAccountPassword">
            <input type="hidden" name="confirmation" id="deleteAccountConfirmation">
        </form>
    </div>
</div>

@push('scripts')
    <script>
        document.getElementById('deleteAccountBtn')?.addEventListener('click', function() {
            const form = document.getElementById('deleteAccountForm');
            const needsPassword = @json($requiresCurrentPassword);

            Swal.fire({
                title: 'Delete account?',
                text: needsPassword
                    ? 'This permanently closes your account. Enter your password to confirm.'
                    : 'This permanently closes your account. Type DELETE to confirm.',
                icon: 'warning',
                input: needsPassword ? 'password' : 'text',
                inputPlaceholder: needsPassword ? 'Current password' : 'DELETE',
                showCancelButton: true,
                confirmButtonText: 'Delete account',
                confirmButtonColor: '#dc2626',
                focusCancel: true,
                preConfirm: function(value) {
                    if (!value) {
                        Swal.showValidationMessage(needsPassword ? 'Password is required.' : 'Type DELETE to confirm.');
                        return false;
                    }
                    if (!needsPassword && value !== 'DELETE') {
                        Swal.showValidationMessage('Type DELETE to confirm.');
                        return false;
                    }
                    return value;
                }
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }
                if (needsPassword) {
                    document.getElementById('deleteAccountPassword').value = result.value;
                } else {
                    document.getElementById('deleteAccountConfirmation').value = result.value;
                }
                form.submit();
            });
        });
    </script>
@endpush
