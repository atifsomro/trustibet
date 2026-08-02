<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class WalletException extends Exception
{
    /**
     * Create a new wallet exception.
     */
    public function __construct(
        string $message = 'Wallet operation failed.',
        int $code = 400,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}