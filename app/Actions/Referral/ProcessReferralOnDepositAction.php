<?php

declare(strict_types=1);

namespace App\Actions\Referral;

use App\Enums\BonusType;
use App\Enums\ReferralStatus;
use App\Models\Deposit;
use App\Models\Referral;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;

class ProcessReferralOnDepositAction
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    /**
     * Handle referral side-effects after a deposit is approved.
     *
     * 1. Invitee first deposit → create pending referral earning
     * 2. If referrer already deposited (or this deposit is the referrer's) → unlock
     */
    public function execute(Deposit $deposit): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $user = $deposit->user;

        if (! $user) {
            return;
        }

        DB::transaction(function () use ($deposit, $user) {
            $this->createPendingForInvitee($deposit, $user);
            $this->unlockPendingForReferrer($user);
        });
    }

    protected function isEnabled(): bool
    {
        $enabled = config(
            'settings.referral_enabled',
            config('wallet.referral.enabled', true)
        );

        if (is_string($enabled)) {
            return in_array(strtolower($enabled), ['1', 'true', 'yes', 'on'], true);
        }

        return (bool) $enabled;
    }

    protected function bonusPercent(): float
    {
        return (float) config(
            'settings.referral_bonus_percent',
            config('wallet.referral.bonus_percent', 5)
        );
    }

    /**
     * On the invitee's first approved deposit, create a pending referral row.
     */
    protected function createPendingForInvitee(Deposit $deposit, User $invitee): void
    {
        if (! $invitee->referred_by) {
            return;
        }

        // Only first approved deposit counts (this one is already approved in-tx)
        $approvedCount = Deposit::query()
            ->where('user_id', $invitee->id)
            ->where('status', 'approved')
            ->count();

        if ($approvedCount !== 1) {
            return;
        }

        if (Referral::query()->where('referred_user_id', $invitee->id)->exists()) {
            return;
        }

        $referrer = User::query()->find($invitee->referred_by);

        if (! $referrer || $referrer->id === $invitee->id) {
            return;
        }

        $percent = $this->bonusPercent();
        $depositAmount = (float) $deposit->amount;
        $bonusAmount = round($depositAmount * ($percent / 100), 2);

        if ($bonusAmount <= 0) {
            return;
        }

        $referral = Referral::query()->create([
            'referrer_id' => $referrer->id,
            'referred_user_id' => $invitee->id,
            'deposit_id' => $deposit->id,
            'deposit_amount' => $depositAmount,
            'bonus_percent' => $percent,
            'bonus_amount' => $bonusAmount,
            'status' => ReferralStatus::PENDING,
        ]);

        // Unlock immediately if referrer already has an approved deposit
        if ($referrer->hasApprovedDeposit()) {
            $this->unlockReferral($referral);
        }
    }

    /**
     * When the depositor is a referrer, unlock all their pending earnings.
     */
    protected function unlockPendingForReferrer(User $user): void
    {
        $pending = Referral::query()
            ->where('referrer_id', $user->id)
            ->where('status', ReferralStatus::PENDING)
            ->lockForUpdate()
            ->get();

        foreach ($pending as $referral) {
            $this->unlockReferral($referral);
        }
    }

    protected function unlockReferral(Referral $referral): void
    {
        if (! $referral->isPending()) {
            return;
        }

        $referrer = $referral->referrer;

        if (! $referrer) {
            return;
        }

        $bonus = $this->walletService->grantBonus(
            user: $referrer,
            amount: (float) $referral->bonus_amount,
            bonusType: BonusType::REFERRAL,
            reference: $referral,
            meta: [
                'reason' => 'Referral Bonus',
                'referral_id' => $referral->id,
                'referred_user_id' => $referral->referred_user_id,
                'deposit_id' => $referral->deposit_id,
                'deposit_amount' => $referral->deposit_amount,
                'bonus_percent' => $referral->bonus_percent,
            ]
        );

        $referral->status = ReferralStatus::UNLOCKED;
        $referral->bonus_id = $bonus->id;
        $referral->unlocked_at = now();
        $referral->save();
    }
}
