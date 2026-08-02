<?php

declare(strict_types=1);

namespace App\Exceptions;

class InvalidBalanceTypeException extends WalletException
{
    public function __construct(string $balanceType)
    {
        parent::__construct(
            sprintf(
                'Invalid balance type [%s].',
                $balanceType
            ),
            422
        );
    }
}