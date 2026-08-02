<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\BalanceType;
use App\Enums\BonusStatus;
use App\Enums\WalletTransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\WalletException;
use App\Models\Bonus;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConsumeBonusAction
{
    public function __construct(
        protected RecordTransactionAction $recordTransactionAction
    ) {
    }

    /**
     * Consume bonus balance.
     *
     * @throws WalletException
     * @throws InsufficientBalanceException
     */
    public function execute(
        Wallet $wallet,
        Bonus $bonus,
        float $amount,
        WalletTransactionType $transactionType = WalletTransactionType::GAME_BET,
        ?Model $reference = null,
        ?string $idempotencyKey = null,
        array $meta = []
    ): Wallet {

        return DB::transaction(function () use (
            $wallet,
            $bonus,
            $amount,
            $transactionType,
            $reference,
            $idempotencyKey,
            $meta
        ) {

            /** @var Wallet $wallet */
            $wallet = Wallet::query()
                ->lockForUpdate()
                ->findOrFail($wallet->id);

            /** @var Bonus $bonus */
            $bonus = Bonus::query()
                ->lockForUpdate()
                ->findOrFail($bonus->id);

            if ($bonus->wallet_id !== $wallet->id) {
                throw new WalletException(
                    'The bonus does not belong to the supplied wallet.'
                );
            }

            if ($bonus->status !== BonusStatus::ACTIVE) {
                throw new WalletException(
                    'The bonus is not active.'
                );
            }

            if ($wallet->bonus_balance < $amount) {
                throw new InsufficientBalanceException(
                    balanceType: BalanceType::BONUS,
                    requestedAmount: $amount,
                    availableAmount: $wallet->bonus_balance
                );
            }

            if ($bonus->remaining_amount < $amount) {
                throw new WalletException(
                    'Bonus remaining amount is insufficient.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Update wallet
            |--------------------------------------------------------------------------
            */

            $wallet->bonus_balance -= $amount;
            $wallet->version++;

            $wallet->save();

            /*
            |--------------------------------------------------------------------------
            | Update bonus
            |--------------------------------------------------------------------------
            */

            $bonus->remaining_amount -= $amount;

            if ($bonus->remaining_amount === 0) {
                $bonus->status = BonusStatus::COMPLETED;
                $bonus->completed_at = now();
            }

            $bonus->save();

            /*
            |--------------------------------------------------------------------------
            | Ledger
            |--------------------------------------------------------------------------
            */

            $this->recordTransactionAction->execute(
                wallet: $wallet,
                balanceType: BalanceType::BONUS,
                transactionType: $transactionType,
                amount: -$amount,
                balanceAfter: $wallet->bonus_balance,
                reference: $reference,
                bonus: $bonus,
                idempotencyKey: $idempotencyKey,
                meta: $meta
            );

            return $wallet;
        });
    }
}