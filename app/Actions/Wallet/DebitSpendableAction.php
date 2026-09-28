<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\BalanceType;
use App\Enums\BonusStatus;
use App\Enums\WalletTransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Bonus;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Debit spendable funds preferring bonus_balance (FIFO active bonuses),
 * then withdrawable for any remainder.
 *
 * Used for investments and lottery — never for withdrawals or games.
 */
class DebitSpendableAction
{
    public function __construct(
        protected DebitWalletAction $debitWalletAction,
        protected RecordTransactionAction $recordTransactionAction
    ) {
    }

    public function execute(
        User $user,
        float $amount,
        WalletTransactionType $transactionType,
        ?Model $reference = null,
        ?string $idempotencyKey = null,
        array $meta = []
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new \InvalidArgumentException(
                'Debit amount must be greater than zero.'
            );
        }

        return DB::transaction(function () use (
            $user,
            $amount,
            $transactionType,
            $reference,
            $idempotencyKey,
            $meta
        ) {
            if ($idempotencyKey !== null) {
                $existing = WalletTransaction::query()
                    ->where('idempotency_key', $idempotencyKey)
                    ->first();

                if ($existing) {
                    return $existing;
                }
            }

            /** @var Wallet $wallet */
            $wallet = Wallet::query()
                ->lockForUpdate()
                ->where('user_id', $user->id)
                ->firstOrFail();

            $bonusAvailable = (float) $wallet->bonus_balance;
            $withdrawableAvailable = (float) $wallet->withdrawable_balance;
            $totalAvailable = $bonusAvailable + $withdrawableAvailable;

            if ($totalAvailable < $amount) {
                throw new InsufficientBalanceException(
                    balanceType: BalanceType::WITHDRAWABLE,
                    requestedAmount: $amount,
                    availableAmount: $totalAvailable
                );
            }

            $fromBonus = min($bonusAvailable, $amount);
            $fromWithdrawable = round($amount - $fromBonus, 2);

            $primaryTransaction = null;
            $bonusIndex = 0;
            $bonusOnly = $fromWithdrawable <= 0;

            if ($fromBonus > 0) {
                $remainingToConsume = $fromBonus;

                $bonuses = Bonus::query()
                    ->lockForUpdate()
                    ->where('user_id', $user->id)
                    ->where('status', BonusStatus::ACTIVE)
                    ->where('remaining_amount', '>', 0)
                    ->orderBy('created_at')
                    ->get();

                foreach ($bonuses as $bonus) {
                    if ($remainingToConsume <= 0) {
                        break;
                    }

                    $take = min((float) $bonus->remaining_amount, $remainingToConsume);

                    if ($take <= 0) {
                        continue;
                    }

                    $wallet->bonus_balance = round((float) $wallet->bonus_balance - $take, 2);
                    $wallet->version++;
                    $wallet->save();

                    $bonus->remaining_amount = round((float) $bonus->remaining_amount - $take, 2);

                    if ($bonus->remaining_amount <= 0) {
                        $bonus->remaining_amount = 0;
                        $bonus->status = BonusStatus::COMPLETED;
                        $bonus->completed_at = now();
                    }

                    $bonus->save();

                    // Use the caller's key on the first (or only) bonus txn when
                    // nothing comes from withdrawable, so retries stay idempotent.
                    $bonusKey = null;
                    if ($idempotencyKey !== null) {
                        $bonusKey = ($bonusOnly && $bonusIndex === 0)
                            ? $idempotencyKey
                            : $idempotencyKey . ':bonus:' . $bonusIndex;
                    }

                    $txn = $this->recordTransactionAction->execute(
                        wallet: $wallet,
                        balanceType: BalanceType::BONUS,
                        transactionType: $transactionType,
                        amount: -$take,
                        balanceAfter: (float) $wallet->bonus_balance,
                        reference: $reference,
                        bonus: $bonus,
                        idempotencyKey: $bonusKey,
                        meta: array_merge($meta, [
                            'spend_source' => 'bonus',
                            'bonus_consumed' => $take,
                        ])
                    );

                    $primaryTransaction ??= $txn;
                    $remainingToConsume = round($remainingToConsume - $take, 2);
                    $bonusIndex++;
                }

                if ($remainingToConsume > 0.009) {
                    throw new InsufficientBalanceException(
                        balanceType: BalanceType::BONUS,
                        requestedAmount: $fromBonus,
                        availableAmount: $fromBonus - $remainingToConsume
                    );
                }
            }

            if ($fromWithdrawable > 0) {
                $withdrawKey = null;
                if ($idempotencyKey !== null) {
                    // Bare key goes on the first leg written (bonus if any);
                    // withdrawable uses a suffix when bonus was also used.
                    $withdrawKey = $fromBonus > 0
                        ? $idempotencyKey . ':withdrawable'
                        : $idempotencyKey;
                }

                $txn = $this->debitWalletAction->executeWithTransaction(
                    wallet: $wallet->fresh(),
                    balanceType: BalanceType::WITHDRAWABLE,
                    transactionType: $transactionType,
                    amount: $fromWithdrawable,
                    reference: $reference,
                    idempotencyKey: $withdrawKey,
                    meta: array_merge($meta, [
                        'spend_source' => 'withdrawable',
                        'bonus_used' => $fromBonus,
                        'withdrawable_used' => $fromWithdrawable,
                    ]),
                    lockWallet: true
                );

                $primaryTransaction = $txn;
            }

            if (! $primaryTransaction) {
                throw new \RuntimeException('Spendable debit produced no ledger transaction.');
            }

            return $primaryTransaction;
        });
    }
}
