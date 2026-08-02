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
     * This action:
     * - Locks the wallet row
     * - Updates cached balance
     * - Records the ledger transaction
     *
     * @return Wallet
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
            
            $newBalance = $wallet->{$balanceColumn} + $amount;

            $wallet->{$balanceColumn} = $newBalance;

            // optimistic locking
            $wallet->version++;

            $wallet->save();

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