<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Enums\BonusType;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterUserAction
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    /**
     * Register a new user from the standard email/password form.
     *
     * Flow:
     * - Create user
     * - Create wallet
     * - Grant welcome bonus (if enabled)
     *
     * @return User
     */
    public function execute(Request $request): User
    {
        return $this->createUser([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'country_id' => $request->country_id,
            'password' => $request->password,
        ]);
    }

    /**
     * Register a new user coming from a social provider (e.g. Google).
     *
     * Runs through the exact same wallet-creation / welcome-bonus flow
     * as {@see execute()} so social sign-ups are never shortchanged.
     *
     * Expected $data keys:
     *  - name (required)
     *  - email (required)
     *  - username (optional - auto-generated from email if omitted)
     *  - provider (e.g. 'google')
     *  - provider_id / google_id
     *  - avatar (optional)
     *  - phone / country_id (optional - nullable on social accounts)
     *
     * @return User
     */
    public function executeForSocial(array $data): User
    {
        return $this->createUser($data, isSocial: true);
    }

    /**
     * Shared creation routine used by both the standard registration
     * form and social (Google) registration.
     *
     * This is the ONLY place responsible for creating users, so both
     * flows always get: a referral code, a wallet, and a welcome bonus
     * (if enabled).
     */
    protected function createUser(array $data, bool $isSocial = false): User
    {
        return DB::transaction(function () use ($data, $isSocial) {
            $user = new User();
            $user->name = $data['name'];
            $user->username = $data['username']
                ?? User::generateUniqueUsernameFrom($data['email']);
            $user->email = $data['email'];
            $user->phone = $data['phone'] ?? null;
            $user->country_id = $data['country_id'] ?? null;
            $user->referral_code = User::generateUniqueCode('referral_code');

            if ($isSocial) {
                // Social accounts don't have (or need) a usable password.
                // A random hash is stored so the password column stays
                // populated for anything that assumes it's non-null.
                $user->password = Hash::make($data['password'] ?? Str::random(32));

                $user->provider = $data['provider'] ?? 'google';
                $user->google_id = $data['google_id'] ?? $data['provider_id'] ?? null;
                $user->avatar = $data['avatar'] ?? null;

                // Google has already verified this email address for us,
                // so there's no email verification step to complete.
                $user->verification_code = null;
                $user->email_verified_at = now();
            } else {
                $user->password = Hash::make($data['password']);
                $user->verification_code = User::generateUniqueCode('verification_code');
            }

            $user->save();

            /*
            |--------------------------------------------------------------------------
            | Create Wallet
            |--------------------------------------------------------------------------
            */

            $this->walletService->wallet($user);

            /*
            |--------------------------------------------------------------------------
            | Grant Welcome Bonus
            |--------------------------------------------------------------------------
            */

            if (config('wallet.welcome_bonus.enabled')) {
                $expiresAt = null;
                if (config('wallet.welcome_bonus.expires_days')) {
                    $expiresAt = now()->addDays(
                        config('wallet.welcome_bonus.expires_days')
                    );
                }
                $this->walletService->grantBonus(
                    user: $user,
                    amount: (float) config('wallet.welcome_bonus.amount'),
                    bonusType: BonusType::WELCOME,
                    reference: $user,
                    meta: [
                        'reason' => 'Welcome Bonus',
                    ],
                    expiresAt: $expiresAt
                );
            }

            return $user;
        });
    }
}
