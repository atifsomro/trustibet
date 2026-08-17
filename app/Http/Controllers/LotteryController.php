<?php

namespace App\Http\Controllers;

use App\Actions\Lottery\BuyLotteryTicketsAction;
use App\Http\Requests\BuyLotteryTicketRequest;
use App\Models\Lottery;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LotteryController extends Controller
{
    /**
     * Display available lotteries.
     */
    public function index(): View
    {
        $lotteries = Lottery::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('sales_start_at')
            ->get();

        // Completed lotteries are inactive after a successful draw, so keep
        // them in a separate collection for public result browsing.
        $completedLotteries = Lottery::query()
            ->whereHas('draws', function ($query) {
                $query->where('status', 'completed');
            })
            ->with('latestCompletedDraw.winners.ticket', 'latestCompletedDraw.winners.user')
            ->orderByDesc('updated_at')
            ->get();

        return view(
            'lottery.index',
            compact('lotteries', 'completedLotteries')
        );
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

        return view('lottery.show', [
            'lottery' => $lottery,
            'draws' => $draws,
        ]);
    }

    /**
     * Display one specific historical draw.
     */
    public function drawShow(Lottery $lottery, \App\Models\LotteryDraw $draw): View
    {
        abort_unless($draw->lottery_id === $lottery->id, 404);

        abort_unless($draw->status === 'completed', 404);

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
     * Purchase lottery tickets.
     */
    public function buyTickets(
        BuyLotteryTicketRequest $request,
        Lottery $lottery,
        BuyLotteryTicketsAction $action
    ): RedirectResponse {
        $tickets = $action->execute(
            user: auth('web')->user(),
            lottery: $lottery,
            quantity: $request->integer('quantity')
        );

        return redirect()
            ->route('lotteries.show', $lottery)
            ->with(
                'success',
                count($tickets) . ' ticket(s) purchased successfully.'
            );
    }
}