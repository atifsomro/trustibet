<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Auth\Google2faService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureGoogle2faIsVerified
{
    public function __construct(
        protected Google2faService $google2fa
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Admin panel must never be interrupted by frontend 2FA.
        if ($request->is('admin', 'admin/*') || $request->routeIs('admin.*')) {
            return $next($request);
        }

        // Allow the challenge/setup pages themselves.
        if ($request->routeIs('auth.google2fa.*')) {
            return $next($request);
        }

        $user = $request->user('web');

        if (! $user instanceof User || ! $this->google2fa->requiresChallenge($user)) {
            return $next($request);
        }

        if ((int) $request->session()->get(Google2faService::SESSION_PASSED) === (int) $user->id) {
            return $next($request);
        }

        $userId = $user->id;
        $needsSetup = $this->google2fa->isSetupPending($user);

        Auth::guard('web')->logout();
        $request->session()->forget(Google2faService::SESSION_PASSED);
        $request->session()->put(Google2faService::SESSION_USER_ID, $userId);

        if ($needsSetup) {
            return redirect()->route('auth.google2fa.setup');
        }

        return redirect()->route('auth.google2fa.challenge');
    }
}
