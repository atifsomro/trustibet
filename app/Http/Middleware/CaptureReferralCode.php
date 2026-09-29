<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureReferralCode
{
    public const SESSION_KEY = 'referral_code';

    public const COOKIE_KEY = 'referral_code';

    public const COOKIE_DAYS = 30;

    /**
     * Persist ?ref=CODE into session and a long-lived cookie
     * so Google OAuth / multi-step signup still retains it.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $code = $request->query('ref');

        if (is_string($code) && $code !== '') {
            $code = strtoupper(trim($code));
            $request->session()->put(self::SESSION_KEY, $code);

            $response = $next($request);

            return $response->withCookie(cookie(
                self::COOKIE_KEY,
                $code,
                self::COOKIE_DAYS * 24 * 60
            ));
        }

        // Restore session from cookie if session is empty
        if (! $request->session()->has(self::SESSION_KEY)) {
            $fromCookie = $request->cookie(self::COOKIE_KEY);
            if (is_string($fromCookie) && $fromCookie !== '') {
                $request->session()->put(self::SESSION_KEY, strtoupper(trim($fromCookie)));
            }
        }

        return $next($request);
    }
}
