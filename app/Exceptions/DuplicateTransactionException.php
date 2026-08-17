<?php

declare(strict_types=1);

namespace App\Exceptions;

class DuplicateTransactionException extends WalletException
{
    public function __construct(?string $idempotencyKey = null)
    {
        parent::__construct(
            $idempotencyKey
                ? "Duplicate transaction detected. Idempotency Key: {$idempotencyKey}"
                : 'Duplicate transaction detected.',
            409
        );
    }
}