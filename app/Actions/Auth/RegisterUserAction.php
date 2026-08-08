<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Enums\BonusType;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    /**
     * Register a new user.
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
        return DB::transaction(function () use ($request) {
            $user = new User();
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->country_id = $request->country_id;
            $user->password = Hash::make($request->password);
            $user->referral_code = User::generateUniqueCode('referral_code');
            $user->verification_code = User::generateUniqueCode('verification_code');
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