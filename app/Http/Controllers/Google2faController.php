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

    public function showChallenge(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('auth.login');
        }

        if (! $this->google2fa->requiresLoginChallenge($user)) {
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

        if (! $user) {
            return redirect()->route('auth.login');
        }

        if (! $this->google2fa->requiresLoginChallenge($user)) {
            return redirect()->route('auth.login');
        }

        if (! $this->google2fa->verify($user, $request->string('code')->toString())) {
            return back()->with('error', 'Invalid authenticator code. Please try again.');
        }

        $request->session()->forget(Google2faService::SESSION_USER_ID);
        $this->google2fa->markSessionPassed($user);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'You have successfully signed in.');
    }

    protected function pendingUser(Request $request): ?User
    {
        $userId = $request->session()->get(Google2faService::SESSION_USER_ID);

        if (! $userId) {
            return null;
        }

        return User::query()->find($userId);
    }
}
