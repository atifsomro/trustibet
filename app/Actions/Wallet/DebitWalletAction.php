<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\BalanceType;
use App\Enums\WalletTransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Bonus;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DebitWalletAction
{
    public function __construct(
        protected RecordTransactionAction $recordTransactionAction
    ) {
    }

    /**
     * Debit a wallet balance.
     *
     * @throws InsufficientBalanceException
     */
    public function execute(
        Wallet $wallet,
        BalanceType $balanceType,
        WalletTransactionType $transactionType,
        float $amount,
        ?Model $reference = null,
        ?Bonus $bonus = null,
        ?string $idempotencyKey = null,
        array $meta = [],
        bool $lockWallet = true
    ): Wallet {

        return DB::transaction(function () use (
            $wallet,
            $balanceType,
            $transactionType,
            $amount,
            $reference,
            $bonus,
            $idempotencyKey,
            $meta,
            $lockWallet
        ) {

            $query = Wallet::query();

            if ($lockWallet) {
                $query->lockForUpdate();
            }

            /** @var Wallet $wallet */
            $wallet = $query->findOrFail($wallet->id);

            $balanceColumn = match ($balanceType) {
                BalanceType::WITHDRAWABLE => 'withdrawable_balance',
                BalanceType::BONUS => 'bonus_balance',
                BalanceType::LOCKED => 'locked_balance',
            };

            $currentBalance = (int) $wallet->{$balanceColumn};

            if ($currentBalance < $amount) {
                throw new InsufficientBalanceException(
                    balanceType: $balanceType,
                    requestedAmount: $amount,
                    availableAmount: $currentBalance
                );
            }

            $newBalance = $currentBalance - $amount;

            $wallet->{$balanceColumn} = $newBalance;

            // Optimistic locking
            $wallet->version++;

            $wallet->save();

            $this->recordTransactionAction->execute(
                wallet: $wallet,
                balanceType: $balanceType,
                transactionType: $transactionType,

                // Ledger debits are stored as negative values
                amount: -$amount,

                balanceAfter: $newBalance,

                reference: $reference,

                bonus: $bonus,

                idempotencyKey: $idempotencyKey,

                meta: $meta
            );

            return $wallet;
        });
    }
}