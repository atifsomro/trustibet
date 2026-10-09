<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class Google2faService
{
    public const SESSION_USER_ID = 'google2fa_user_id';

    public const SESSION_PASSED = 'google2fa_passed';

    protected Google2FA $google2fa;

    public function __construct(?Google2FA $google2fa = null)
    {
        $this->google2fa = $google2fa ?? new Google2FA;
    }

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * User self-service enable (Security tab). User owns the secret.
     */
    public function enableFor(User $user): User
    {
        $user->forceFill([
            'google2fa_enabled' => true,
            'google2fa_secret' => $this->generateSecret(),
            'google2fa_confirmed_at' => null,
            'google2fa_managed_by_admin' => false,
        ])->save();

        return $user->refresh();
    }

    /**
     * Admin enable — admin scans the QR and holds the authenticator.
     * After confirm, login asks for a code. A correct code signs the user in.
     * The user does not have the secret, so they cannot produce that code.
     */
    public function enableForAdmin(User $user): User
    {
        $user->forceFill([
            'google2fa_enabled' => true,
            'google2fa_secret' => $this->generateSecret(),
            'google2fa_confirmed_at' => null,
            'google2fa_managed_by_admin' => true,
        ])->save();

        return $user->refresh();
    }

    public function disableFor(User $user): User
    {
        $user->forceFill([
            'google2fa_enabled' => false,
            'google2fa_secret' => null,
            'google2fa_confirmed_at' => null,
            'google2fa_managed_by_admin' => false,
        ])->save();

        return $user->refresh();
    }

    public function verify(User $user, string $code): bool
    {
        if (empty($user->google2fa_secret)) {
            return false;
        }

        return $this->google2fa->verifyKey(
            $user->google2fa_secret,
            preg_replace('/\s+/', '', $code) ?? '',
            1
        );
    }

    public function otpAuthUrl(User $user): string
    {
        $company = (string) config('app.name', 'TrustiBet');

        return $this->google2fa->getQRCodeUrl(
            $company,
            $user->email,
            (string) $user->google2fa_secret
        );
    }

    public function qrCodeSvg(User $user): string
    {
        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(220),
                new SvgImageBackEnd
            )
        );

        return $writer->writeString($this->otpAuthUrl($user));
    }

    public function markConfirmed(User $user): void
    {
        if ($user->google2fa_confirmed_at === null) {
            $user->forceFill(['google2fa_confirmed_at' => now()])->save();
        }
    }

    public function isSetupPending(User $user): bool
    {
        return $user->google2fa_enabled
            && ! empty($user->google2fa_secret)
            && $user->google2fa_confirmed_at === null;
    }

    public function isManagedByAdmin(User $user): bool
    {
        return (bool) $user->google2fa_managed_by_admin;
    }

    /**
     * Fully active authenticator (enabled and confirmed).
     */
    public function isConfirmed(User $user): bool
    {
        return $user->google2fa_enabled
            && ! empty($user->google2fa_secret)
            && $user->google2fa_confirmed_at !== null;
    }

    /**
     * Ask for a code on login when authenticator is confirmed.
     * Admin-managed accounts use the same page: only a correct code signs in.
     */
    public function requiresLoginChallenge(User $user): bool
    {
        return $this->isConfirmed($user);
    }

    public function markSessionPassed(User $user): void
    {
        session()->put(self::SESSION_PASSED, $user->id);
    }

    public function clearSessionFlags(): void
    {
        session()->forget([
            self::SESSION_USER_ID,
            self::SESSION_PASSED,
        ]);
    }
}
