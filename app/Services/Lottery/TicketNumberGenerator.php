<?php

declare(strict_types=1);

namespace App\Services\Lottery;

use App\Models\LotteryTicket;
use Illuminate\Support\Str;

class TicketNumberGenerator
{
    /**
     * Generate a unique lottery ticket number.
     */
    public function generate(): string
    {
        do {
            $ticketNumber =
                config('lottery.ticket_number_prefix', 'LT')
                . '-'
                . strtoupper(Str::random(10));

        } while (
            LotteryTicket::where(
                'ticket_number',
                $ticketNumber
            )->exists()
        );

        return $ticketNumber;
    }
}