<?php

declare(strict_types=1);

namespace App\Services\Wallet;

use App\Models\WalletTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TransactionReferenceGenerator
{
    /**
     * Generate a unique bank-style wallet transaction reference.
     *
     * Example: TBX-260309-A7K2M9XQ
     */
    public function generate(?Carbon $at = null): string
    {
        $prefix = $this->prefix();
        $date = ($at ?? now())->format('ymd');

        do {
            $reference = sprintf(
                '%s-%s-%s',
                $prefix,
                $date,
                strtoupper(Str::random(8))
            );
        } while (
            WalletTransaction::query()
                ->where('uuid', $reference)
                ->exists()
        );

        return $reference;
    }

    /**
     * Public display reference for a stored wallet transaction value.
     *
     * New rows store a bank-style code in `uuid`.
     * Legacy UUID rows get a stable readable code from date + id.
     */
    public function display(?string $stored, int $id, Carbon|string|null $createdAt = null): string
    {
        $stored = trim((string) $stored);

        if ($stored !== '' && $this->isModern($stored)) {
            return strtoupper($stored);
        }

        $date = Carbon::parse($createdAt ?? now())->format('ymd');

        return sprintf('%s-%s-%06d', $this->prefix(), $date, $id);
    }

    /**
     * Build a readable reference for non-ledger records (deposits, withdrawals).
     */
    public function forEntity(string $prefix, int $id, Carbon|string|null $at = null): string
    {
        $date = Carbon::parse($at ?? now())->format('ymd');

        return sprintf('%s-%s-%06d', strtoupper($prefix), $date, $id);
    }

    public function isModern(string $value): bool
    {
        $prefix = preg_quote($this->prefix(), '/');

        return (bool) preg_match(
            '/^' . $prefix . '-\d{6}-[A-Z0-9]{6,10}$/i',
            trim($value)
        );
    }

    /**
     * Resolve a user-entered reference into query constraints.
     *
     * @return array{uuid: ?string, id: ?int}
     */
    public function resolveSearch(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            return ['uuid' => null, 'id' => null];
        }

        if ($this->isModern($query)) {
            return ['uuid' => strtoupper($query), 'id' => null];
        }

        $prefix = preg_quote($this->prefix(), '/');
        if (preg_match('/^' . $prefix . '-\d{6}-(\d+)$/i', $query, $matches)) {
            return ['uuid' => null, 'id' => (int) $matches[1]];
        }

        if (ctype_digit($query)) {
            return ['uuid' => null, 'id' => (int) $query];
        }

        return ['uuid' => $query, 'id' => null];
    }

    protected function prefix(): string
    {
        return strtoupper((string) config(
            'wallet.transaction_reference.prefix',
            'TBX'
        ));
    }
}
