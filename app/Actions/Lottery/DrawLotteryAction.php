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
use App\Notifications\LotteryNewRoundNotification;
use App\Notifications\LotteryResultNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DrawLotteryAction
{
    public function __construct(
        protected CreditWalletAction $creditWalletAction
    ) {
    }

    /**
     * Draw winners for a lottery round.
     *
     * Always executes at ends_at regardless of is_active.
     *
     * - is_active = true: announce winners, pay prizes, notify win/loss.
     * - is_active = false: still select winners internally for audit,
     *   but persist every participant as lost, skip payouts, notify lost.
     */
    public function execute(
        Lottery $lottery,
        ?Admin $admin = null
    ): LotteryDraw {
        return DB::transaction(function () use ($lottery, $admin): LotteryDraw {
            $lottery = Lottery::query()
                ->lockForUpdate()
                ->findOrFail($lottery->id);

            $lottery->syncStatus();
            $lottery->refresh();

            if (!$lottery->canDraw()) {
                throw new RuntimeException(
                    'This lottery cannot be drawn at this time.'
                );
            }

            $tickets = $this->eligibleTickets($lottery);
            $announceWinners = (bool) $lottery->is_active;

            $lottery->startDrawing();

            $draw = LotteryDraw::create([
                'lottery_id' => $lottery->id,
                'status' => 'running',
                'total_tickets' => $tickets->count(),
                'total_winners' => 0,
                'winners_announced' => $announceWinners,
                'drawn_by' => $admin?->id,
                'sales_start_at' => $lottery->periodStart(),
                'sales_end_at' => $lottery->periodEnd(),
                'prize_snapshot' => $lottery->prizes(),
                'started_at' => now(),
            ]);

            if ($tickets->isEmpty()) {
                $this->finalizeDraw($lottery, $draw, 0);

                return $draw->fresh([
                    'lottery',
                    'winners.ticket',
                    'winners.user',
                ]);
            }

            $prizeSlots = $this->getPrizeSlots($lottery);
            $availableTickets = $tickets->shuffle();
            $winnerUserIds = [];
            $selectedWinners = [];

            foreach ($prizeSlots as $slot) {
                $eligibleTickets = $availableTickets
                    ->filter(fn (LotteryTicket $ticket) => !in_array($ticket->user_id, $winnerUserIds, true))
                    ->values();

                if ($eligibleTickets->isEmpty()) {
                    break;
                }

                /** @var LotteryTicket $ticket */
                $ticket = $eligibleTickets->random();
                $winnerUserIds[] = $ticket->user_id;

                $availableTickets = $availableTickets
                    ->reject(fn (LotteryTicket $availableTicket) => $availableTicket->id === $ticket->id)
                    ->values();

                $selectedWinners[] = [
                    'ticket' => $ticket,
                    'slot' => $slot,
                ];
            }

            foreach ($selectedWinners as $selected) {
                $this->persistWinner(
                    lottery: $lottery,
                    draw: $draw,
                    ticket: $selected['ticket'],
                    prizeCategory: $selected['slot']['category'],
                    prizePosition: $selected['slot']['position'],
                    prizeAmount: $selected['slot']['amount'],
                    announceAndPay: $announceWinners,
                );
            }

            $this->markRoundTicketsLost($lottery);

            $this->notifyParticipants(
                lottery: $lottery,
                draw: $draw,
                tickets: $tickets,
                selectedWinners: $selectedWinners,
                announceWinners: $announceWinners,
            );

            $this->finalizeDraw($lottery, $draw, $announceWinners ? count($selectedWinners) : 0);

            $this->notifyNewRound($lottery, $draw, $tickets);

            return $draw->fresh([
                'lottery',
                'winners.ticket',
                'winners.user',
            ]);
        });
    }

    protected function eligibleTickets(Lottery $lottery): Collection
    {
        $query = LotteryTicket::query()
            ->where('lottery_id', $lottery->id)
            ->where('status', 'active');

        if ($lottery->periodStart()) {
            $query->where('purchased_at', '>=', $lottery->periodStart());
        }

        if ($lottery->periodEnd()) {
            $query->where('purchased_at', '<=', $lottery->periodEnd());
        }

        return $query->get();
    }

    protected function finalizeDraw(Lottery $lottery, LotteryDraw $draw, int $totalWinners): void
    {
        $draw->update([
            'status' => 'completed',
            'total_winners' => $totalWinners,
            'completed_at' => now(),
        ]);

        if ($lottery->isCancelled()) {
            $lottery->markCompleted();

            return;
        }

        $lottery->startNextRound();
    }

    /**
     * Mark remaining current-round tickets as lost.
     */
    protected function markRoundTicketsLost(Lottery $lottery): void
    {
        $query = LotteryTicket::query()
            ->where('lottery_id', $lottery->id)
            ->where('status', 'active');

        if ($lottery->periodStart()) {
            $query->where('purchased_at', '>=', $lottery->periodStart());
        }

        if ($lottery->periodEnd()) {
            $query->where('purchased_at', '<=', $lottery->periodEnd());
        }

        $query->update(['status' => 'lost']);
    }

    /**
     * @param  Collection<int, LotteryTicket>  $tickets
     * @param  array<int, array{ticket: LotteryTicket, slot: array{category: string, position: int, amount: float}}>  $selectedWinners
     */
    protected function notifyParticipants(
        Lottery $lottery,
        LotteryDraw $draw,
        Collection $tickets,
        array $selectedWinners,
        bool $announceWinners
    ): void {
        $winnersByUserId = [];

        foreach ($selectedWinners as $selected) {
            $winnersByUserId[$selected['ticket']->user_id] = $selected;
        }

        $userIds = $tickets->pluck('user_id')->unique()->filter()->values();

        $users = User::query()
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');

        foreach ($userIds as $userId) {
            $user = $users->get($userId);

            if (!$user) {
                continue;
            }

            $winner = $announceWinners ? ($winnersByUserId[$userId] ?? null) : null;

            $user->notify(new LotteryResultNotification(
                lottery: $lottery,
                draw: $draw,
                result: $winner ? 'won' : 'lost',
                prizeTier: $winner['slot']['category'] ?? null,
                prizeAmount: isset($winner['slot']['amount']) ? (float) $winner['slot']['amount'] : null,
            ));
        }
    }

    /**
     * @param  Collection<int, LotteryTicket>  $tickets
     */
    protected function notifyNewRound(Lottery $lottery, LotteryDraw $draw, Collection $tickets): void
    {
        if ($lottery->isCancelled() || $lottery->isCompleted()) {
            return;
        }

        $userIds = $tickets->pluck('user_id')->unique()->filter()->values();

        if ($userIds->isEmpty()) {
            return;
        }

        $users = User::query()
            ->whereIn('id', $userIds)
            ->get();

        $roundNumber = $lottery->currentRoundNumber();

        foreach ($users as $user) {
            $user->notify(new LotteryNewRoundNotification(
                lottery: $lottery,
                previousDraw: $draw,
                roundNumber: $roundNumber,
            ));
        }
    }

    /**
     * @return array<int, array{category: string, position: int, amount: float}>
     */
    protected function getPrizeSlots(Lottery $lottery): array
    {
        return [
            ['category' => 'first', 'position' => 1, 'amount' => (float) $lottery->first_prize],
            ['category' => 'second', 'position' => 1, 'amount' => (float) $lottery->second_prize],
            ['category' => 'third', 'position' => 1, 'amount' => (float) $lottery->third_prize],
            ['category' => 'fourth', 'position' => 1, 'amount' => (float) $lottery->fourth_prize],
            ['category' => 'fifth', 'position' => 1, 'amount' => (float) $lottery->fifth_prize],
        ];
    }

    protected function persistWinner(
        Lottery $lottery,
        LotteryDraw $draw,
        LotteryTicket $ticket,
        string $prizeCategory,
        int $prizePosition,
        float $prizeAmount,
        bool $announceAndPay
    ): LotteryWinner {
        $user = $ticket->user;

        if (!$user) {
            throw new RuntimeException(
                "Ticket #{$ticket->id} does not have a valid user."
            );
        }

        $winner = LotteryWinner::create([
            'lottery_id' => $lottery->id,
            'draw_id' => $draw->id,
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'prize_category' => $prizeCategory,
            'prize_position' => $prizePosition,
            'prize_amount' => $prizeAmount,
            'payout_status' => $announceAndPay ? 'pending' : 'suppressed',
        ]);

        if (!$announceAndPay) {
            return $winner;
        }

        $wallet = $user->wallet;

        if (!$wallet) {
            throw new RuntimeException(
                "User #{$user->id} does not have a wallet."
            );
        }

        $ticket->update([
            'status' => 'winner',
        ]);

        $idempotencyKey = 'lottery-prize:' . $winner->id;

        $this->creditWalletAction->execute(
            wallet: $wallet,
            balanceType: BalanceType::WITHDRAWABLE,
            transactionType: WalletTransactionType::LOTTERY_PRIZE,
            amount: $prizeAmount,
            reference: $winner,
            idempotencyKey: $idempotencyKey,
            meta: [
                'lottery_id' => $lottery->id,
                'draw_id' => $draw->id,
                'ticket_id' => $ticket->id,
                'winner_id' => $winner->id,
                'prize_category' => $prizeCategory,
                'prize_position' => $prizePosition,
            ],
        );

        $transaction = $wallet->transactions()
            ->where('idempotency_key', $idempotencyKey)
            ->latest('id')
            ->first();

        $winner->update([
            'payout_status' => 'paid',
            'payout_transaction_id' => $transaction?->id,
            'paid_at' => now(),
        ]);

        return $winner->fresh();
    }
}
