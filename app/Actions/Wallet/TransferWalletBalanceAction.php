<?php

declare(strict_types=1);

namespace App\Actions\Wallet;

use App\Enums\BalanceType;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransferWalletBalanceAction
{
    /**
     * Transfer balance between wallet balance buckets.
     *
     * This method DOES NOT create ledger transactions.
     * The calling action is responsible for recording the
     * appropriate business transaction(s).
     */
    public function execute(
        Wallet $wallet,
        BalanceType $from,
        BalanceType $to,
        float $amount
    ): Wallet {

        if ($amount <= 0) {
            throw new InvalidArgumentException('Transfer amount must be greater than zero.');
        }

        if ($from === $to) {
            throw new InvalidArgumentException('Source and destination balance cannot be the same.');
        }

        return DB::transaction(function () use (
            $wallet,
            $from,
            $to,
            $amount
        ) {

            /** @var Wallet $wallet */
            $wallet = Wallet::query()
                ->lockForUpdate()
                ->findOrFail($wallet->id);

            $fromColumn = $this->getColumn($from);
            $toColumn = $this->getColumn($to);

            if ($wallet->{$fromColumn} < $amount) {
                throw new InvalidArgumentException(
                    'Insufficient balance for transfer.'
                );
            }

            $wallet->{$fromColumn} -= $amount;
            $wallet->{$toColumn} += $amount;

            $wallet->version++;

            $wallet->save();

            return $wallet;
        });
    }

    /**
     * Resolve wallet balance column.
     */
    protected function getColumn(BalanceType $balanceType): string
    {
        return match ($balanceType) {
            BalanceType::WITHDRAWABLE => 'withdrawable_balance',
            BalanceType::BONUS => 'bonus_balance',
            BalanceType::LOCKED => 'locked_balance',
        };
    }
}