<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Auth\Google2faService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * - Admin-locked accounts cannot stay logged in.
 * - User-owned GA requires a one-time OTP after fresh login.
 * - Closing/reopening the browser keeps SESSION_PASSED, so OTP is not asked again.
 */
class EnsureGoogle2faIsVerified
{
    public function __construct(
        protected Google2faService $google2fa
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin', 'admin/*') || $request->routeIs('admin.*')) {
            return $next($request);
        }

        if ($request->routeIs('auth.google2fa.*')) {
            return $next($request);
        }

        $user = $request->user('web');

        if (! $user instanceof User) {
            return $next($request);
        }

        if ($this->google2fa->isAdminLocked($user)) {
            Auth::guard('web')->logout();
            $this->google2fa->clearSessionFlags();

            return redirect()
                ->route('auth.login')
                ->with(
                    'error',
                    'This account is locked by administrator authenticator. Contact support to regain access.'
                );
        }

        if (! $this->google2fa->requiresLoginChallenge($user)) {
            return $next($request);
        }

        if ((int) $request->session()->get(Google2faService::SESSION_PASSED) === (int) $user->id) {
            return $next($request);
        }

        $userId = $user->id;

        Auth::guard('web')->logout();
        $request->session()->forget(Google2faService::SESSION_PASSED);
        $request->session()->put(Google2faService::SESSION_USER_ID, $userId);

        return redirect()
            ->route('auth.google2fa.challenge')
            ->with('info', 'Enter the code from your authenticator app to continue.');
    }
}
