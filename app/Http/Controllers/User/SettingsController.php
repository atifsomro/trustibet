<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'same:confirm_password'],
            'confirm_password' => ['required', 'string'],
        ], [
            'new_password.same' => 'The password confirmation does not match.',
            'current_password.current_password' => 'The current password is incorrect.',
        ]);

        if ($validator->fails()) {
            return $this->backToSettings()
                ->withErrors($validator)
                ->withInput($request->except(['current_password', 'new_password', 'confirm_password']));
        }

        $user->password = $request->string('new_password')->toString();
        $user->password_set_at = now();
        $user->save();

        return $this->backToSettings()
            ->with('success', 'Password updated successfully.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->notification_preferences = [
            'email' => $request->boolean('email_notifications'),
            'sms' => $request->boolean('sms_notifications'),
            'offers' => $request->boolean('offers_notifications'),
            'winner' => $request->boolean('winner_notifications'),
            'transaction' => $request->boolean('transaction_notifications'),
        ];
        $user->save();

        return $this->backToSettings()
            ->with('success', 'Notification preferences saved.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasChosenPassword()) {
            $validator = Validator::make($request->all(), [
                'password' => ['required', 'string', 'current_password'],
            ], [
                'password.current_password' => 'The password is incorrect.',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'confirmation' => ['required', 'in:DELETE'],
            ], [
                'confirmation.in' => 'Type DELETE to confirm account deletion.',
            ]);
        }

        if ($validator->fails()) {
            return $this->backToSettings()
                ->withErrors($validator);
        }

        $id = $user->id;

        $user->forceFill([
            'google_id' => null,
            'provider' => null,
            'avatar' => null,
            'name' => 'Deleted User',
            'username' => 'deleted'.$id,
            'email' => 'deleted-'.$id.'@deleted.invalid',
            'phone' => null,
            'password' => Str::random(40),
            'password_set_at' => now(),
            'verification_code' => null,
            'password_reset_token' => null,
            'google2fa_enabled' => false,
            'google2fa_secret' => null,
            'google2fa_confirmed_at' => null,
            'google2fa_managed_by_admin' => false,
            'account_status' => AccountStatus::BLOCKED,
            'notification_preferences' => null,
        ])->save();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('auth.login')
            ->with('success', 'Your account has been deleted.');
    }

    protected function backToSettings(): RedirectResponse
    {
        return redirect()->to(route('user-account').'#settings');
    }
}
