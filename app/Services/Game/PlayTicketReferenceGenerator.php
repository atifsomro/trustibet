<?php

declare(strict_types=1);

namespace App\Services\Game;

use App\Models\GamePlay;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PlayTicketReferenceGenerator
{
    /**
     * Generate a unique bank-style game ticket reference.
     *
     * Example: TKT-260309-A7K2M9XQ
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
            GamePlay::query()
                ->where('uuid', $reference)
                ->exists()
        );

        return $reference;
    }

    /**
     * Public display ticket for a stored game play value.
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

    public function isModern(string $value): bool
    {
        $prefix = preg_quote($this->prefix(), '/');

        return (bool) preg_match(
            '/^' . $prefix . '-\d{6}-[A-Z0-9]{6,10}$/i',
            trim($value)
        );
    }

    protected function prefix(): string
    {
        return strtoupper((string) config(
            'games.ticket_reference.prefix',
            'TKT'
        ));
    }
}
