<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Auth\Google2faService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SecurityController extends Controller
{
    public function __construct(
        protected Google2faService $google2fa
    ) {}

    public function enable(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($this->google2fa->isManagedByAdmin($user)) {
            return $this->toSecurity()
                ->with('error', 'Authenticator is managed by an administrator. Contact support.');
        }

        if ($this->google2fa->isConfirmed($user)) {
            return $this->toSecurity()
                ->with('error', 'Google Authenticator is already turned on.');
        }

        if (! $this->google2fa->isSetupPending($user)) {
            $this->google2fa->enableFor($user);
        }

        return $this->toSecurity()
            ->with('success', 'Scan the QR code with Google Authenticator, then enter the 6-digit code to finish setup.');
    }

    public function confirm(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        if ($validator->fails()) {
            return $this->toSecurity()
                ->withErrors($validator)
                ->withInput();
        }

        $user = $request->user();

        if ($this->google2fa->isManagedByAdmin($user)) {
            return $this->toSecurity()
                ->with('error', 'Authenticator is managed by an administrator. Contact support.');
        }

        if (! $this->google2fa->isSetupPending($user)) {
            return $this->toSecurity()
                ->with('error', 'No authenticator setup is pending. Enable Google Authenticator first.');
        }

        if (! $this->google2fa->verify($user, $request->string('code')->toString())) {
            return $this->toSecurity()
                ->with('error', 'Invalid authenticator code. Please try again.');
        }

        $this->google2fa->markConfirmed($user);
        $this->google2fa->markSessionPassed($user);

        return $this->toSecurity()
            ->with('success', 'Google Authenticator is now turned on for your account.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($this->google2fa->isManagedByAdmin($user)) {
            return $this->toSecurity()
                ->with('error', 'Authenticator is managed by an administrator. Only an admin can disable it.');
        }

        if (! $user->google2fa_enabled) {
            return $this->toSecurity()
                ->with('error', 'Google Authenticator is already off.');
        }

        if ($this->google2fa->isConfirmed($user)) {
            $validator = Validator::make($request->all(), [
                'code' => ['required', 'string', 'regex:/^\d{6}$/'],
            ]);

            if ($validator->fails()) {
                return $this->toSecurity()
                    ->withErrors($validator)
                    ->withInput();
            }

            if (! $this->google2fa->verify($user, $request->string('code')->toString())) {
                return $this->toSecurity()
                    ->with('error', 'Invalid authenticator code. Please try again.');
            }
        }

        $this->google2fa->disableFor($user);
        $this->google2fa->clearSessionFlags();

        return $this->toSecurity()
            ->with('success', 'Google Authenticator has been turned off.');
    }

    protected function toSecurity(): RedirectResponse
    {
        return redirect()->to(route('user-account').'#security');
    }
}
