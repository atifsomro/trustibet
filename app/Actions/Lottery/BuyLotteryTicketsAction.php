<?php

declare(strict_types=1);

namespace App\Actions\Lottery;

use App\Enums\BalanceType;
use App\Enums\WalletTransactionType;
use App\Models\Lottery;
use App\Models\LotteryTicket;
use App\Models\User;
use App\Services\Lottery\TicketNumberGenerator;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BuyLotteryTicketsAction
{
    public function __construct(
        protected WalletService $walletService,
        protected TicketNumberGenerator $ticketNumberGenerator,
    ) {
    }

    /**
     * Purchase lottery tickets.
     *
     * @return array<int, LotteryTicket>
     */
    public function execute(
        User $user,
        Lottery $lottery,
        int $quantity
    ): array {
        return DB::transaction(function () use (
            $user,
            $lottery,
            $quantity
        ) {
            /*
             * Lock the lottery row.
             *
             * This prevents concurrent purchases from
             * exceeding the lottery's maximum ticket limit.
             */
            $lottery = Lottery::query()
                ->lockForUpdate()
                ->findOrFail($lottery->id);
            /*
             * Validate quantity.
             */
            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'You must purchase at least one ticket.',
                ]);
            }

            /*
             * Make sure the lottery is currently selling tickets.
             */
            if ($lottery->hasEnded()) {
                throw ValidationException::withMessages([
                    'lottery' => 'This lottery has ended. Ticket sales are closed.',
                ]);
            }

            if (!$lottery->canBuyTickets()) {
                throw ValidationException::withMessages([
                    'lottery' => 'Tickets are not currently available for this lottery.',
                ]);
            }

            /*
             * Per-lottery maximum tickets a single user can purchase.
             * null means unlimited for that user.
             */
            $maxTicketsPerUser = $lottery->max_tickets_per_user;

            if ($maxTicketsPerUser !== null) {
                $existingTickets = $lottery->ticketsForCurrentRoundUser(
                    $user->id
                );

                $remainingUserTickets = max(
                    0,
                    (int) $maxTicketsPerUser - $existingTickets
                );

                if ($quantity > $remainingUserTickets) {
                    throw ValidationException::withMessages([
                        'quantity' => sprintf(
                            'You can purchase only %d more ticket(s) for this lottery.',
                            $remainingUserTickets
                        ),
                    ]);
                }
            }

            /*
             * Check lottery-wide ticket limit.
             *
             * null means unlimited.
             */
            $remainingLotteryTickets = $lottery->remainingTickets();

            if (
                $remainingLotteryTickets !== null &&
                $quantity > $remainingLotteryTickets
            ) {
                throw ValidationException::withMessages([
                    'quantity' => sprintf(
                        'Only %d ticket(s) are remaining for this lottery.',
                        $remainingLotteryTickets
                    ),
                ]);
            }

            /*
             * Calculate ticket price.
             */
            $ticketPrice = (float) $lottery->ticket_price;

            /*
             * Calculate total purchase amount.
             */
            $totalAmount = round(
                $ticketPrice * $quantity,
                2
            );

            /*
             * Create a unique purchase ID.
             *
             * This lets us identify the exact wallet transaction
             * associated with this purchase.
             */
            $purchaseId = (string) Str::uuid();

            $idempotencyKey = 'lottery-purchase:' . $purchaseId;

            /*
             * Debit user's withdrawable balance.
             *
             * Lottery ticket purchases are NOT moved into
             * locked_balance.
             *
             * The money is considered spent immediately.
             */
            $transaction = $this->walletService->debitWithTransaction(
                user: $user,
                balanceType: BalanceType::WITHDRAWABLE,
                transactionType: WalletTransactionType::LOTTERY_TICKET_PURCHASE,
                amount: $totalAmount,
                reference: $lottery,
                idempotencyKey: $idempotencyKey,
                meta: [
                    'purchase_id' => $purchaseId,
                    'lottery_id' => $lottery->id,
                    'quantity' => $quantity,
                    'ticket_price' => $ticketPrice,
                    'total_amount' => $totalAmount,
                ],
            );

            /*
             * Create the individual lottery tickets.
             */
            $tickets = [];

            for ($i = 0; $i < $quantity; $i++) {
                $tickets[] = LotteryTicket::create([
                    'lottery_id' => $lottery->id,
                    'user_id' => $user->id,
                    'ticket_number' => $this->ticketNumberGenerator->generate(),
                    'price' => $ticketPrice,
                    'status' => 'active',
                    'purchase_transaction_id' => $transaction->id,
                    'purchased_at' => now(),
                ]);
            }

            return $tickets;
        });
    }
}