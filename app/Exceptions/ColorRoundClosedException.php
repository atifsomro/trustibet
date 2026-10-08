<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class ColorRoundClosedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('This round has already been drawn.');
    }
}
