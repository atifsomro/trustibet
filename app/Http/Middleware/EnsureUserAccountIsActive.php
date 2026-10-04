<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Auth\Google2faService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserAccountIsActive
{
    public function __construct(
        protected Google2faService $google2fa
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin', 'admin/*') || $request->routeIs('admin.*')) {
            return $next($request);
        }

        $user = $request->user('web');

        if (! $user instanceof User || $user->canLogin()) {
            return $next($request);
        }

        Auth::guard('web')->logout();
        $this->google2fa->clearSessionFlags();

        return redirect()
            ->route('auth.login')
            ->with('error', $user->accountRestrictionMessage());
    }
}
