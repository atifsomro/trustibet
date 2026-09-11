<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\BalanceType;
use App\Enums\WalletTransactionType;
use App\Models\Bonus;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreditWalletAction
{
    public function __construct(
        protected RecordTransactionAction $recordTransactionAction
    ) {
    }

    /**
     * Credit a wallet balance.
     *
     * Idempotent when an idempotency key is provided.
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

            /*
             * ----------------------------------------------------------
             * Idempotency check
             * ----------------------------------------------------------
             *
             * Do this AFTER locking the wallet and BEFORE changing
             * the balance.
             */
            if ($idempotencyKey !== null) {
                $existingTransaction = $wallet->transactions()
                    ->where('idempotency_key', $idempotencyKey)
                    ->first();

                if ($existingTransaction) {
                    return $wallet;
                }
            }

            /*
             * ----------------------------------------------------------
             * Determine balance column
             * ----------------------------------------------------------
             */

            $balanceColumn = $balanceType->column();

            /*
             * ----------------------------------------------------------
             * Update balance
             * ----------------------------------------------------------
             */

            $newBalance = $wallet->{$balanceColumn} + $amount;

            $wallet->{$balanceColumn} = $newBalance;

            /*
             * Optimistic locking.
             */
            $wallet->version++;

            $wallet->save();

            /*
             * ----------------------------------------------------------
             * Record ledger transaction
             * ----------------------------------------------------------
             */

            $this->recordTransactionAction->execute(
                wallet: $wallet,
                balanceType: $balanceType,
                transactionType: $transactionType,
                amount: $amount,
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