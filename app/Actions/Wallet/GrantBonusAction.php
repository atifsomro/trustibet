<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\BalanceType;
use App\Enums\BonusStatus;
use App\Enums\BonusType;
use App\Enums\WalletTransactionType;
use App\Models\Bonus;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GrantBonusAction
{
    public function __construct(
        protected CreditWalletAction $creditWalletAction
    ) {
    }

    /**
     * Grant a bonus to the user.
     *
     * This action:
     * - Creates a Bonus record
     * - Credits the user's bonus wallet
     * - Records the ledger transaction
     *
     * @param User $user
     * @param int $amount Amount in cents
     * @param BonusType $bonusType
     * @param Model|null $reference
     * @param array $meta
     * @param \DateTimeInterface|null $expiresAt
     *
     * @return Bonus
     */
    public function execute(
        User $user,
        float $amount,
        BonusType $bonusType = BonusType::WELCOME,
        ?Model $reference = null,
        array $meta = [],
        ?\DateTimeInterface $expiresAt = null
    ): Bonus {

        return DB::transaction(function () use (
            $user,
            $amount,
            $bonusType,
            $reference,
            $meta,
            $expiresAt
        ) {

            /** @var Wallet $wallet */
            $wallet = Wallet::query()
                ->lockForUpdate()
                ->where('user_id', $user->id)
                ->firstOrFail();

            $bonus = new Bonus();

            $bonus->user_id = $user->id;
            $bonus->type = $bonusType;
            $bonus->initial_amount = $amount;
            $bonus->remaining_amount = $amount;
            $bonus->status = BonusStatus::ACTIVE;
            $bonus->activated_at = now();
            $bonus->expires_at = $expiresAt;

            $bonus->save();

            $this->creditWalletAction->execute(
                wallet: $wallet,
                balanceType: BalanceType::BONUS,
                transactionType: WalletTransactionType::BONUS_GRANTED,
                amount: $amount,
                reference: $reference,
                bonus: $bonus,
                meta: $meta,
                lockWallet: false
            );

            return $bonus;
        });
    }
}