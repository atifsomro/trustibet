<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\BalanceType;
use App\Enums\WalletTransactionType;
use App\Models\Bonus;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RecordTransactionAction
{
    /**
     * Create a wallet ledger transaction.
     *
     * This is the ONLY place responsible for creating
     * wallet_transactions records.
     */
    public function execute(
        Wallet $wallet,
        BalanceType $balanceType,
        WalletTransactionType $transactionType,
        float $amount,
        float $balanceAfter,
        ?Model $reference = null,
        ?Bonus $bonus = null,
        ?string $idempotencyKey = null,
        array $meta = []
    ): WalletTransaction {

        $transaction = new WalletTransaction();

        $transaction->uuid = (string) Str::uuid();
        $transaction->wallet_id = $wallet->id;
        $transaction->bonus_id = $bonus?->id;
        $transaction->balance_type = $balanceType;
        $transaction->type = $transactionType;
        $transaction->amount = $amount;
        $transaction->balance_after = $balanceAfter;
        $transaction->reference_type = $reference?->getMorphClass();
        $transaction->reference_id = $reference?->getKey();
        $transaction->idempotency_key = $idempotencyKey;
        $transaction->meta = empty($meta) ? null : $meta;
        $transaction->created_at = Carbon::now();

        $transaction->save();

        /*
        |--------------------------------------------------------------------------
        | Structured Logging
        |--------------------------------------------------------------------------
        */

        Log::info('Wallet transaction recorded.', [
            'transaction_id' => $transaction->id,
            'uuid' => $transaction->uuid,

            'wallet_id' => $wallet->id,
            'user_id' => $wallet->user_id,

            'balance_type' => $balanceType->value,
            'transaction_type' => $transactionType->value,

            'amount' => $amount,
            'balance_after' => $balanceAfter,

            'reference_type' => $reference?->getMorphClass(),
            'reference_id' => $reference?->getKey(),

            'bonus_id' => $bonus?->id,

            'idempotency_key' => $idempotencyKey,
        ]);

        return $transaction;
    }
}
