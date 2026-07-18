<div class="settings">
    {{-- Change Password --}}
    <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
        <div class="mb-8">
            <h3>Change Password</h3>
            <p class="mt-2 opacity-70">
                Keep your account secure by updating your password regularly.
            </p>
        </div>
        <form id="changePasswordForm" method="POST">
            @csrf
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label for="current_password" class="mb-2 block">
                        Current Password
                    </label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                </div>
                <div>
                    <label for="new_password" class="mb-2 block">
                        New Password
                    </label>
                    <input type="password" id="new_password" name="new_password" autocomplete="new-password"
                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                </div>
                <div>
                    <label for="confirm_password" class="mb-2 block">
                        Confirm Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password"
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
        <form class="mt-8">
            <div class="space-y-5">
                <label class="flex items-center justify-between">
                    <span>Email Notifications</span>
                    <input type="checkbox" name="email_notifications" checked>
                </label>
                <label class="flex items-center justify-between">
                    <span>SMS Notifications</span>
                    <input type="checkbox" name="sms_notifications">
                </label>
                <label class="flex items-center justify-between">
                    <span>Promotional Offers</span>
                    <input type="checkbox" name="offers_notifications" checked>
                </label>
                <label class="flex items-center justify-between">
                    <span>Prize Winner Alerts</span>
                    <input type="checkbox" name="winner_notifications" checked>
                </label>
                <label class="flex items-center justify-between">
                    <span>Deposit & Withdrawal Updates</span>
                    <input type="checkbox" name="transaction_notifications" checked>
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
                <p class="mt-2 text-green-500">
                    Verified
                </p>
            </div>
            <div class="rounded-2xl bg-brand-dark p-6">
                <h5>Phone Verification</h5>
                <p class="mt-2 text-green-500">
                    Verified
                </p>
            </div>
            <div class="rounded-2xl bg-brand-dark p-6">
                <h5>KYC Status</h5>
                <p class="mt-2 text-yellow-500">
                    Pending
                </p>
            </div>
            <div class="rounded-2xl bg-brand-dark p-6">
                <h5>Last Login</h5>
                <p class="mt-2">
                    18 Jul 2026 - Chrome Windows
                </p>
            </div>
        </div>
    </div>
    {{-- Language --}}
    <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8 mt-3 sm:mt-6">
        <h3>Regional Settings</h3>
        <form class="grid md:grid-cols-2 gap-6 mt-8">
            <div>
                <label class="mb-2 block">
                    Language
                </label>
                <select name="language" class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3">
                    <option value="en">English</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block">
                    Currency
                </label>
                <select name="currency" class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3">
                    <option value="PKR">
                        PKR
                    </option>
                </select>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="btn-primary">
                    Save Settings
                </button>
            </div>
        </form>
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
            <button type="button" class="btn-secondary">
                <i class="fa-solid fa-right-from-bracket mr-2"></i>
                Logout
            </button>
            <button type="button" class="rounded-xl bg-red-600 px-6 py-3 text-white hover:bg-red-700 duration-300">
                <i class="fa-solid fa-trash mr-2"></i>
                Delete Account
            </button>
        </div>
    </div>
</div>
