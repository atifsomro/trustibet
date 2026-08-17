<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    /**
     * Render the exception as an HTTP response.
     *
     * This is called automatically by Laravel's exception
     * handler for any exception that defines it, so no
     * try/catch is needed at call sites.
     */
    public function render(Request $request): JsonResponse|RedirectResponse
    {
        $status = $this->getCode() >= 400 && $this->getCode() < 600
            ? $this->getCode()
            : 400;

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
            ], $status);
        }

        return back()->withErrors([
            'wallet' => $this->getMessage(),
        ]);
    }
}