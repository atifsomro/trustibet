<?php

declare(strict_types=1);

namespace App\Services\Wallet;

use App\Actions\Wallet\GrantBonusAction;
use App\Enums\BonusStatus;
use App\Enums\BonusType;
use App\Exceptions\WalletException;
use App\Models\Bonus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BonusService
{
    public function __construct(
        protected GrantBonusAction $grantBonusAction
    ) {
    }

    /**
     * Grant a bonus to the user.
     *
     * @throws WalletException
     */
    public function grant(
        User $user,
        float $amount,
        BonusType $type,
        ?Model $reference = null,
        array $meta = [],
        ?\DateTimeInterface $expiresAt = null
    ): Bonus {

        if ($amount <= 0) {
            throw new WalletException('Bonus amount must be greater than zero.');
        }

        return $this->grantBonusAction->execute(
            user: $user,
            amount: $amount,
            bonusType: $type,
            reference: $reference,
            meta: $meta,
            expiresAt: $expiresAt
        );
    }

    /**
     * Grant the welcome bonus once.
     *
     * @throws WalletException
     */
    public function grantWelcomeBonus(
        User $user,
        float $amount,
        ?\DateTimeInterface $expiresAt = null
    ): Bonus {

        if ($this->hasReceivedBonus($user, BonusType::WELCOME)) {
            throw new WalletException('Welcome bonus has already been granted.');
        }

        return $this->grant(
            user: $user,
            amount: $amount,
            type: BonusType::WELCOME,
            expiresAt: $expiresAt
        );
    }

    /**
     * Check whether the user has already received
     * a specific bonus type.
     */
    public function hasReceivedBonus(
        User $user,
        BonusType $type
    ): bool {

        return Bonus::query()
            ->where('user_id', $user->id)
            ->where('type', $type)
            ->exists();
    }

    /**
     * Get all active bonuses.
     */
    public function activeBonuses(User $user): Collection
    {
        return Bonus::query()
            ->where('user_id', $user->id)
            ->where('status', BonusStatus::ACTIVE)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Get total remaining bonus balance.
     */
    public function remainingBalance(User $user): int
    {
        return (int) Bonus::query()
            ->where('user_id', $user->id)
            ->where('status', BonusStatus::ACTIVE)
            ->sum('remaining_amount');
    }

    /**
     * Get bonus history.
     */
    public function history(User $user, int $perPage = 20)
    {
        return Bonus::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Check if the user has any active bonus.
     */
    public function hasActiveBonus(User $user): bool
    {
        return Bonus::query()
            ->where('user_id', $user->id)
            ->where('status', BonusStatus::ACTIVE)
            ->exists();
    }
}