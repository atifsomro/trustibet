<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Auth\Google2faService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class Google2faController extends Controller
{
    public function __construct(
        protected Google2faService $google2fa
    ) {}

    public function showSetup(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('auth.login');
        }

        if (! $this->google2fa->isSetupPending($user)) {
            return redirect()->route('auth.google2fa.challenge');
        }

        return view('auth.google2fa-setup', [
            'title' => 'Set Up Authenticator - '.config('app.name'),
            'user' => $user,
            'qrSvg' => $this->google2fa->qrCodeSvg($user),
            'secret' => $user->google2fa_secret,
        ]);
    }

    public function confirmSetup(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        $user = $this->pendingUser($request);

        if (! $user || ! $this->google2fa->isSetupPending($user)) {
            return redirect()->route('auth.login');
        }

        if (! $this->google2fa->verify($user, $request->string('code')->toString())) {
            return back()->with('error', 'Invalid authenticator code. Please try again.');
        }

        $this->google2fa->markConfirmed($user);

        return $this->completeLogin($request, $user);
    }

    public function showChallenge(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('auth.login');
        }

        if ($this->google2fa->isSetupPending($user)) {
            return redirect()->route('auth.google2fa.setup');
        }

        if (! $this->google2fa->requiresChallenge($user)) {
            return redirect()->route('auth.login');
        }

        return view('auth.google2fa-challenge', [
            'title' => 'Authenticator - '.config('app.name'),
            'user' => $user,
        ]);
    }

    public function verifyChallenge(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        $user = $this->pendingUser($request);

        if (! $user || ! $this->google2fa->requiresChallenge($user)) {
            return redirect()->route('auth.login');
        }

        if ($this->google2fa->isSetupPending($user)) {
            return redirect()->route('auth.google2fa.setup');
        }

        if (! $this->google2fa->verify($user, $request->string('code')->toString())) {
            return back()->with('error', 'Invalid authenticator code. Please try again.');
        }

        return $this->completeLogin($request, $user);
    }

    protected function pendingUser(Request $request): ?User
    {
        $userId = $request->session()->get(Google2faService::SESSION_USER_ID);

        if (! $userId) {
            return null;
        }

        return User::query()->find($userId);
    }

    protected function completeLogin(Request $request, User $user): RedirectResponse
    {
        $request->session()->forget(Google2faService::SESSION_USER_ID);
        $request->session()->put(Google2faService::SESSION_PASSED, $user->id);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'You have successfully signed in.');
    }
}
