<?php

namespace App\Http\Controllers;

use App\Actions\Lottery\BuyLotteryTicketsAction;
use App\Enums\LotteryStatus;
use App\Http\Requests\BuyLotteryTicketRequest;
use App\Models\Lottery;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class LotteryController extends Controller
{
    /**
     * Display available lotteries.
     */
    public function index(): View
    {
        $lotteries = Lottery::query()
            ->whereNotIn('status', [
                LotteryStatus::DRAFT->value,
                LotteryStatus::CANCELLED->value,
                LotteryStatus::COMPLETED->value,
            ])
            ->with('latestAnnouncedDraw.winners.ticket', 'latestAnnouncedDraw.winners.user')
            ->withCount([
                'draws as completed_draws_count' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('starts_at')
            ->get();

        $resultLotteries = Lottery::query()
            ->whereHas('draws', function ($query) {
                $query->where('status', 'completed')
                    ->where('winners_announced', true);
            })
            ->with([
                'latestAnnouncedDraw.winners.ticket',
                'latestAnnouncedDraw.winners.user',
            ])
            ->withCount([
                'draws as announced_draws_count' => function ($query) {
                    $query->where('status', 'completed')
                        ->where('winners_announced', true);
                },
            ])
            ->get()
            ->sortByDesc(fn (Lottery $lottery) => $lottery->latestAnnouncedDraw?->id ?? 0)
            ->values();

        return view(
            'lottery.index',
            array_merge(
                compact('lotteries', 'resultLotteries'),
                $this->lotteryWalletSummary()
            )
        );
    }

    /**
     * Display all announced draw results for a lottery.
     */
    public function results(Lottery $lottery): View
    {
        $draws = $lottery->draws()
            ->where('status', 'completed')
            ->where('winners_announced', true)
            ->with([
                'winners.ticket',
                'winners.user',
            ])
            ->latest('id')
            ->get();

        abort_unless($draws->count() > 0, 404);

        return view('lottery.results', [
            'lottery' => $lottery,
            'draws' => $draws,
        ]);
    }

    /**
     * Display lottery details.
     */
    public function show(Lottery $lottery): View
    {
        $draws = $lottery->draws()
            ->where('status', 'completed')
            ->with([
                'winners.ticket',
                'winners.user',
            ])
            ->latest('id')
            ->get();

        $userTickets = auth('web')->check()
            ? $lottery->tickets()
                ->where('user_id', auth('web')->id())
                ->latest('purchased_at')
                ->get()
            : collect();

        return view('lottery.show', array_merge([
            'lottery' => $lottery,
            'draws' => $draws,
            'userTickets' => $userTickets,
        ], $this->lotteryWalletSummary()));
    }

    /**
     * Display one specific historical draw.
     */
    public function drawShow(Lottery $lottery, LotteryDraw $draw): View
    {
        abort_unless($draw->lottery_id === $lottery->id, 404);

        abort_unless($draw->status === 'completed', 404);
        abort_unless($draw->winners_announced, 404);

        $draw->load([
            'winners.ticket',
            'winners.user',
        ]);

        return view(
            'lottery.draw-show',
            compact('lottery', 'draw')
        );
    }

    /**
     * The authenticated user's lottery purchase and result history.
     */
    public function history(): View
    {
        $tickets = LotteryTicket::query()
            ->with(['lottery.latestCompletedDraw.winners'])
            ->where('user_id', auth('web')->id())
            ->latest('purchased_at')
            ->get();

        $history = $tickets
            ->groupBy('lottery_id')
            ->map(function (Collection $lotteryTickets) {
                /** @var LotteryTicket $first */
                $first = $lotteryTickets->first();
                $lottery = $first->lottery;

                $amount = $lotteryTickets->sum(fn (LotteryTicket $ticket) => (float) $ticket->price);
                $hasWinner = $lotteryTickets->contains(fn (LotteryTicket $ticket) => $ticket->isWinner());
                $allResolved = $lotteryTickets->every(fn (LotteryTicket $ticket) => in_array($ticket->status, ['winner', 'lost', 'refunded', 'cancelled'], true));

                $drawStatus = 'pending';
                $outcome = 'not_yet_drawn';

                if ($lottery?->isCancelled()) {
                    $drawStatus = 'cancelled';
                    $outcome = 'cancelled';
                } elseif ($allResolved || $lottery?->isCompleted()) {
                    $drawStatus = 'drawn';
                    $outcome = $hasWinner ? 'won' : 'lost';
                }

                return [
                    'lottery' => $lottery,
                    'tickets' => $lotteryTickets,
                    'quantity' => $lotteryTickets->count(),
                    'amount' => $amount,
                    'purchased_at' => $lotteryTickets->min('purchased_at'),
                    'draw_status' => $drawStatus,
                    'outcome' => $outcome,
                ];
            })
            ->values();

        return view('lottery.history', array_merge(
            compact('history'),
            $this->lotteryWalletSummary()
        ));
    }

    /**
     * Manual / fallback draw for a single due lottery.
     * Primary draws run via `php artisan lottery:draw-due` (scheduler).
     */
    public function drawDue(\Illuminate\Http\Request $request, Lottery $lottery, \App\Actions\Lottery\DrawLotteryAction $action)
    {
        $lottery->syncStatus();
        $lottery->refresh();

        if (!$lottery->canDraw()) {
            return response()->json([
                'drawn' => false,
                'already_drawn' => $lottery->isCompleted() || $lottery->currentRoundHasDraw(),
                'ends_at' => $lottery->fresh()->ends_at?->toIso8601String(),
            ]);
        }

        try {
            $draw = $action->execute($lottery);

            return response()->json([
                'drawn' => true,
                'restarted' => true,
                'ends_at' => $lottery->fresh()->ends_at?->toIso8601String(),
                'winners_announced' => (bool) $draw->winners_announced,
                'total_winners' => $draw->total_winners,
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'drawn' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    /**
     * Manual / fallback draw for every due lottery.
     * Primary draws run via the scheduler — do not rely on page visits.
     */
    public function drawDueAll(\App\Actions\Lottery\DrawLotteryAction $action)
    {
        $drawnIds = [];

        foreach (Lottery::query()->dueForDraw()->get() as $lottery) {
            try {
                $action->execute($lottery);
                $drawnIds[] = $lottery->id;
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return response()->json([
            'drawn' => $drawnIds !== [],
            'drawn_ids' => $drawnIds,
        ]);
    }

    /**
     * Purchase lottery tickets.
     */
    public function buyTickets(
        BuyLotteryTicketRequest $request,
        Lottery $lottery,
        BuyLotteryTicketsAction $action
    ): RedirectResponse {
        $action->execute(
            user: auth('web')->user(),
            lottery: $lottery,
            quantity: 1
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Ticket purchased successfully.'
            );
    }

    /**
     * Used lottery spend and remaining spendable wallet balance.
     *
     * @return array{usedBalance: float, remainingBalance: float, walletCurrency: string}
     */
    protected function lotteryWalletSummary(): array
    {
        $user = auth('web')->user();

        if (!$user) {
            return [
                'usedBalance' => 0.0,
                'remainingBalance' => 0.0,
                'walletCurrency' => 'USD',
            ];
        }

        $wallet = $user->wallet;

        $used = (float) LotteryTicket::query()
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['refunded', 'cancelled'])
            ->sum('price');

        return [
            'usedBalance' => $used,
            'remainingBalance' => (float) ($wallet?->withdrawable_balance ?? 0),
            'walletCurrency' => $wallet?->currency ?? 'USD',
        ];
    }
}
