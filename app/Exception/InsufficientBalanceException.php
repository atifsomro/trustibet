<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\BalanceType;

class InsufficientBalanceException extends WalletException
{
    public function __construct(
        BalanceType $balanceType,
        float $requestedAmount,
        float $availableAmount
    ) {
        parent::__construct(
            sprintf(
                'Insufficient %s. Requested: %d cents, Available: %d cents.',
                $balanceType->label(),
                $requestedAmount,
                $availableAmount
            ),
            422
        );
    }
}