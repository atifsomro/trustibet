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

    public function enableFor(User $user): User
    {
        $user->forceFill([
            'google2fa_enabled' => true,
            'google2fa_secret' => $this->generateSecret(),
            'google2fa_confirmed_at' => null,
        ])->save();

        return $user->refresh();
    }

    public function disableFor(User $user): User
    {
        $user->forceFill([
            'google2fa_enabled' => false,
            'google2fa_secret' => null,
            'google2fa_confirmed_at' => null,
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

    public function requiresChallenge(User $user): bool
    {
        return (bool) $user->google2fa_enabled;
    }

    public function isSetupPending(User $user): bool
    {
        return $user->google2fa_enabled
            && ! empty($user->google2fa_secret)
            && $user->google2fa_confirmed_at === null;
    }
}
