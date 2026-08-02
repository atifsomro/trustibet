<?php

declare(strict_types=1);

namespace App\Exceptions;

class WalletNotFoundException extends WalletException
{
    public function __construct()
    {
        parent::__construct(
            'Wallet not found.',
            404
        );
    }
}