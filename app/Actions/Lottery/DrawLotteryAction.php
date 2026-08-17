<?php

declare(strict_types=1);

namespace App\Actions\Lottery;

use App\Actions\Wallet\CreditWalletAction;
use App\Enums\BalanceType;
use App\Enums\WalletTransactionType;
use App\Models\Admin;
use App\Models\Lottery;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Models\LotteryWinner;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class DrawLotteryAction
{
    public function __construct(
        protected CreditWalletAction $creditWalletAction
    ) {
    }

    /**
     * Draw winners for a lottery.
     *
     * Business rules:
     *
     * - Lottery must be ended.
     * - A lottery can be drawn repeatedly; each configured sales period is a separate round.
     * - There are maximum 5 prize slots.
     * - One user can win only once per draw round.
     * - If there are fewer unique users than prize slots,
     *   the remaining prize slots stay empty.
     * - The winning ticket is selected randomly.
     * - Prize is immediately credited to the user's withdrawable wallet.
     */
    public function execute(
        Lottery $lottery,
        Admin $admin
    ): LotteryDraw {
        /*
        |--------------------------------------------------------------------------
        | Validate Lottery
        |--------------------------------------------------------------------------
        */

        if (!$lottery->isEnded()) {
            throw new RuntimeException(
                'This lottery cannot be drawn. The lottery must be ended first.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate draw for the CURRENT round only
        |--------------------------------------------------------------------------
        |
        | Completed draws from previous rounds are historical records and do
        | not prevent the next round from being drawn.
        |
        */

        $existingDrawQuery = $lottery->draws()
            ->whereIn('status', [
                'pending',
                'running',
                'completed',
            ]);

        if ($lottery->sales_start_at) {
            $existingDrawQuery->where(
                'sales_start_at',
                $lottery->sales_start_at
            );
        } else {
            $existingDrawQuery->whereNull('sales_start_at');
        }

        $existingDrawQuery->where(
            'sales_end_at',
            $lottery->sales_end_at
        );

        if ($existingDrawQuery->exists()) {
            throw new RuntimeException(
                'This lottery round has already been drawn.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Get Eligible Tickets for CURRENT round
        |--------------------------------------------------------------------------
        |
        | Tickets from earlier rounds remain in the database for history but
        | do not participate in the new draw.
        |
        */

        $ticketsQuery = LotteryTicket::query()
            ->where('lottery_id', $lottery->id)
            ->where('status', 'active');

        if ($lottery->sales_start_at) {
            $ticketsQuery->where(
                'purchased_at',
                '>=',
                $lottery->sales_start_at
            );
        }

        $ticketsQuery->where(
            'purchased_at',
            '<=',
            $lottery->sales_end_at
        );

        $tickets = $ticketsQuery->get();

        $totalTickets = $tickets->count();

        /*
        |--------------------------------------------------------------------------
        | Check Tickets
        |--------------------------------------------------------------------------
        */

        if ($totalTickets === 0) {
            throw new RuntimeException(
                'There are no eligible tickets for this lottery.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Count Unique Users
        |--------------------------------------------------------------------------
        |
        | Important:
        |
        | We do NOT compare total tickets with total prize slots.
        |
        | Example:
        |
        | 100 tickets
        | 3 unique users
        | 5 prize slots
        |
        | Result:
        |
        | 3 winners
        | 2 empty prize slots
        |
        */

        $uniqueUserCount = $tickets
            ->pluck('user_id')
            ->unique()
            ->count();

        if ($uniqueUserCount === 0) {
            throw new RuntimeException(
                'There are no eligible users for this lottery.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Prize Slots
        |--------------------------------------------------------------------------
        */

        $prizeSlots = $this->getPrizeSlots($lottery);

        /*
        |--------------------------------------------------------------------------
        | Number Of Winners
        |--------------------------------------------------------------------------
        |
        | We can never have more winners than unique users.
        |
        */

        $totalWinners = min(
            count($prizeSlots),
            $uniqueUserCount
        );

        /*
        |--------------------------------------------------------------------------
        | Create Draw Record
        |--------------------------------------------------------------------------
        */

        $draw = LotteryDraw::create([
            'lottery_id'    => $lottery->id,
            'status'        => 'pending',
            'total_tickets' => $totalTickets,
            'total_winners' => $totalWinners,
            'drawn_by'       => $admin->id,
            'sales_start_at' => $lottery->sales_start_at,
            'sales_end_at'   => $lottery->sales_end_at,
            'prize_snapshot' => $lottery->prizes(),
        ]);

        try {

            DB::transaction(function () use (
                $lottery,
                $draw,
                $tickets,
                $prizeSlots
            ): void {

                /*
                |--------------------------------------------------------------------------
                | Lock Lottery
                |--------------------------------------------------------------------------
                */

                $lockedLottery = Lottery::query()
                    ->lockForUpdate()
                    ->findOrFail($lottery->id);

                /*
                |--------------------------------------------------------------------------
                | Re-check Lottery Status
                |--------------------------------------------------------------------------
                */

                if (!$lockedLottery->isEnded()) {
                    throw new RuntimeException(
                        'This lottery is no longer available for drawing.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Re-check current round after locking
                |--------------------------------------------------------------------------
                */

                $duplicateDrawQuery = $lockedLottery->draws()
                    ->whereIn('status', [
                        'pending',
                        'running',
                        'completed',
                    ]);

                if ($lockedLottery->sales_start_at) {
                    $duplicateDrawQuery->where(
                        'sales_start_at',
                        $lockedLottery->sales_start_at
                    );
                } else {
                    $duplicateDrawQuery->whereNull('sales_start_at');
                }

                $duplicateDrawQuery->where(
                    'sales_end_at',
                    $lockedLottery->sales_end_at
                );

                if ($duplicateDrawQuery->exists()) {
                    throw new RuntimeException(
                        'This lottery round has already been drawn.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Start Draw
                |--------------------------------------------------------------------------
                */

                $lockedLottery->status = 'drawing';
                $lockedLottery->save();

                $draw->update([
                    'status'     => 'running',
                    'started_at' => now(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Randomize Tickets
                |--------------------------------------------------------------------------
                |
                | Randomizing the entire collection first ensures that when
                | a user has multiple tickets, any one of their tickets
                | can potentially be selected.
                |
                */

                $availableTickets = $tickets->shuffle();

                /*
                |--------------------------------------------------------------------------
                | Track Users Who Already Won
                |--------------------------------------------------------------------------
                |
                | This is the important part of the new requirement.
                |
                | Once a user wins, ALL of their remaining tickets in the current round become
                | ineligible for the remaining prize slots.
                |
                */

                $winnerUserIds = [];

                /*
                |--------------------------------------------------------------------------
                | Select Winners
                |--------------------------------------------------------------------------
                */

                foreach ($prizeSlots as $slot) {

                    /*
                    |--------------------------------------------------------------------------
                    | Find Tickets Belonging To Users Who Have Not Won
                    |--------------------------------------------------------------------------
                    */

                    $eligibleTickets = $availableTickets
                        ->filter(function (LotteryTicket $ticket) use (
                            $winnerUserIds
                        ) {
                            return !in_array(
                                $ticket->user_id,
                                $winnerUserIds,
                                true
                            );
                        })
                        ->values();

                    /*
                    |--------------------------------------------------------------------------
                    | No More Unique Users
                    |--------------------------------------------------------------------------
                    |
                    | Remaining prize slots stay empty.
                    |
                    */

                    if ($eligibleTickets->isEmpty()) {
                        break;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Select Random Ticket
                    |--------------------------------------------------------------------------
                    */

                    /** @var LotteryTicket $ticket */
                    $ticket = $eligibleTickets->random();

                    /*
                    |--------------------------------------------------------------------------
                    | Mark User As Winner
                    |--------------------------------------------------------------------------
                    */

                    $winnerUserIds[] = $ticket->user_id;

                    /*
                    |--------------------------------------------------------------------------
                    | Remove Selected Ticket
                    |--------------------------------------------------------------------------
                    |
                    | The selected ticket should not be selected again.
                    |
                    */

                    $availableTickets = $availableTickets
                        ->reject(function (LotteryTicket $availableTicket) use (
                            $ticket
                        ) {
                            return $availableTicket->id === $ticket->id;
                        })
                        ->values();

                    /*
                    |--------------------------------------------------------------------------
                    | Create Winner And Pay Prize
                    |--------------------------------------------------------------------------
                    */

                    $this->createWinnerAndPayPrize(
                        lottery: $lockedLottery,
                        draw: $draw,
                        ticket: $ticket,
                        prizeCategory: $slot['category'],
                        prizePosition: $slot['position'],
                        prizeAmount: $slot['amount']
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Complete Draw
                |--------------------------------------------------------------------------
                */

                $draw->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Complete Lottery
                |--------------------------------------------------------------------------
                */

                $lockedLottery->status = 'completed';
                $lockedLottery->save();
            });

            return $draw->fresh([
                'lottery',
                'winners.ticket',
                'winners.user',
            ]);

        } catch (Throwable $exception) {

            /*
            |--------------------------------------------------------------------------
            | Mark Draw As Failed
            |--------------------------------------------------------------------------
            |
            | The transaction has already rolled back.
            |
            | The draw record was created before the transaction,
            | therefore we can safely mark it as failed.
            |
            */

            $draw->update([
                'status'        => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Build all prize slots.
     *
     * Current lottery structure:
     *
     * 1 × First
     * 1 × Second
     * 1 × Third
     * 1 × Fourth
     * 1 × Fifth
     */
    protected function getPrizeSlots(
        Lottery $lottery
    ): array {

        return [
            [
                'category' => 'first',
                'position' => 1,
                'amount'   => (float) $lottery->first_prize,
            ],

            [
                'category' => 'second',
                'position' => 1,
                'amount'   => (float) $lottery->second_prize,
            ],

            [
                'category' => 'third',
                'position' => 1,
                'amount'   => (float) $lottery->third_prize,
            ],

            [
                'category' => 'fourth',
                'position' => 1,
                'amount'   => (float) $lottery->fourth_prize,
            ],

            [
                'category' => 'fifth',
                'position' => 1,
                'amount'   => (float) $lottery->fifth_prize,
            ],
        ];
    }

    /**
     * Create winner record and pay the prize.
     */
    protected function createWinnerAndPayPrize(
        Lottery $lottery,
        LotteryDraw $draw,
        LotteryTicket $ticket,
        string $prizeCategory,
        int $prizePosition,
        float $prizeAmount
    ): LotteryWinner {

        /*
        |--------------------------------------------------------------------------
        | Get Ticket Owner
        |--------------------------------------------------------------------------
        */

        $user = $ticket->user;

        if (!$user) {
            throw new RuntimeException(
                "Ticket #{$ticket->id} does not have a valid user."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Get User Wallet
        |--------------------------------------------------------------------------
        */

        $wallet = $user->wallet;

        if (!$wallet) {
            throw new RuntimeException(
                "User #{$user->id} does not have a wallet."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Winner Record
        |--------------------------------------------------------------------------
        */

        $winner = LotteryWinner::create([
            'lottery_id'     => $lottery->id,
            'draw_id'        => $draw->id,
            'ticket_id'      => $ticket->id,
            'user_id'        => $user->id,
            'prize_category' => $prizeCategory,
            'prize_position' => $prizePosition,
            'prize_amount'   => $prizeAmount,
            'payout_status'  => 'pending',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Mark Ticket As Winner
        |--------------------------------------------------------------------------
        |
        | Do NOT set winner_id here because lottery_tickets does not
        | contain a winner_id column.
        |
        */

        $ticket->update([
            'status' => 'winner',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Credit Prize To Withdrawable Wallet
        |--------------------------------------------------------------------------
        */

        $idempotencyKey = 'lottery-prize:' . $winner->id;

        $this->creditWalletAction->execute(
            wallet: $wallet,
            balanceType: BalanceType::WITHDRAWABLE,
            transactionType: WalletTransactionType::LOTTERY_PRIZE,
            amount: $prizeAmount,
            reference: $winner,
            idempotencyKey: $idempotencyKey,
            meta: [
                'lottery_id'     => $lottery->id,
                'draw_id'        => $draw->id,
                'ticket_id'      => $ticket->id,
                'winner_id'      => $winner->id,
                'prize_category' => $prizeCategory,
                'prize_position' => $prizePosition,
            ],
        );

        /*
        |--------------------------------------------------------------------------
        | Get Wallet Transaction
        |--------------------------------------------------------------------------
        |
        | CreditWalletAction returns the wallet rather than the transaction.
        | Therefore retrieve the transaction using the idempotency key.
        |
        */

        $transaction = $wallet->transactions()
            ->where('idempotency_key', $idempotencyKey)
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Mark Payout As Paid
        |--------------------------------------------------------------------------
        */

        $winner->update([
            'payout_status'         => 'paid',
            'payout_transaction_id' => $transaction?->id,
            'paid_at'               => now(),
        ]);

        return $winner->fresh();
    }
}