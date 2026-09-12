<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Lottery\DrawLotteryAction;
use App\Enums\LotteryStatus;
use App\Http\Controllers\Controller;
use App\Models\Lottery;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class LotteryController extends Controller
{
    public function index(Request $request)
    {
        $query = Lottery::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('currency', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNotIn('status', LotteryStatus::hiddenFromPublic());
            } elseif ($request->status === 'inactive') {
                $query->where('status', LotteryStatus::INACTIVE->value);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('active')) {
            $query->where(
                'is_active',
                $request->active === '1'
            );
        }

        $lotteries = $query
            ->withCount([
                'draws as completed_draws_count' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->ordered()
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.lotteries.index',
            compact('lotteries')
        );
    }

    /**
     * Display lottery details and draw results.
     */
    /**
     * Display lottery details and all draw history.
     */
    public function show(Lottery $lottery)
    {
        $draws = $lottery->draws()
            ->withCount('winners')
            ->latest('id')
            ->paginate(10, ['*'], 'draws_page');

        $tickets = $lottery->tickets()
            ->with('user')
            ->latest('purchased_at')
            ->paginate(20, ['*'], 'tickets_page');

        return view(
            'admin.lotteries.show',
            compact('lottery', 'draws', 'tickets')
        );
    }

    /**
     * Cross-lottery purchase and draw history.
     */
    public function history(Request $request)
    {
        $tickets = LotteryTicket::query()
            ->with(['user', 'lottery'])
            ->when($request->filled('lottery_id'), function ($query) use ($request) {
                $query->where('lottery_id', $request->integer('lottery_id'));
            })
            ->when($request->filled('user'), function ($query) use ($request) {
                $search = trim((string) $request->input('user'));

                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery
                        ->where('id', $search)
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->where('purchased_at', '>=', $request->date('date_from')->startOfDay());
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->where('purchased_at', '<=', $request->date('date_to')->endOfDay());
            })
            ->latest('purchased_at')
            ->paginate(25, ['*'], 'tickets_page')
            ->withQueryString();

        $draws = LotteryDraw::query()
            ->with(['lottery', 'winners.user', 'winners.ticket'])
            ->when($request->filled('lottery_id'), function ($query) use ($request) {
                $query->where('lottery_id', $request->integer('lottery_id'));
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->where(function ($inner) use ($request) {
                    $inner
                        ->where('completed_at', '>=', $request->date('date_from')->startOfDay())
                        ->orWhere(function ($or) use ($request) {
                            $or->whereNull('completed_at')
                                ->where('created_at', '>=', $request->date('date_from')->startOfDay());
                        });
                });
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->where(function ($inner) use ($request) {
                    $inner
                        ->where('completed_at', '<=', $request->date('date_to')->endOfDay())
                        ->orWhere(function ($or) use ($request) {
                            $or->whereNull('completed_at')
                                ->where('created_at', '<=', $request->date('date_to')->endOfDay());
                        });
                });
            })
            ->latest('id')
            ->paginate(15, ['*'], 'draws_page')
            ->withQueryString();

        $lotteries = Lottery::query()
            ->withTrashed()
            ->orderBy('title')
            ->get(['id', 'title']);

        return view(
            'admin.lotteries.history',
            compact('tickets', 'draws', 'lotteries')
        );
    }

    /**
     * Display the results of one specific draw.
     */
    public function drawShow(Lottery $lottery, LotteryDraw $draw)
    {
        abort_unless($draw->lottery_id === $lottery->id, 404);

        $draw->load([
            'winners.ticket',
            'winners.user',
        ]);

        return view(
            'admin.lotteries.draw-show',
            compact('lottery', 'draw')
        );
    }

    public function create()
    {
        return view('admin.lotteries.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $visibility = $validated['status'];
        unset($validated['status']);

        DB::beginTransaction();

        try {
            $lottery = new Lottery($validated);

            if ($visibility === 'inactive') {
                $lottery->status = LotteryStatus::INACTIVE;
                $lottery->sales_end_at = now()->addSeconds((int) $lottery->duration_seconds);
                $lottery->draw_at = $lottery->sales_end_at;
            } else {
                $lottery->applyCountdown();
                $lottery->status = LotteryStatus::SELLING;
            }

            $lottery->save();

            DB::commit();

            return redirect()
                ->route('admin.lotteries.index')
                ->with(
                    'success',
                    'Lottery created successfully.'
                );

        } catch (Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create lottery.'
                );
        }
    }

    public function edit(Lottery $lottery)
    {
        return view(
            'admin.lotteries.edit',
            compact('lottery')
        );
    }

    public function update(
        Request $request,
        Lottery $lottery
    ) {
        $validated = $this->validateRequest($request);
        $visibility = $validated['status'];
        unset($validated['status']);

        $startNewRound = $request->boolean('start_new_round');
        $previousDuration = (int) $lottery->duration_seconds;
        $wasCompleted = $lottery->isCompleted();
        $wasHidden = $lottery->isDraft()
            || $lottery->isCancelled()
            || $lottery->isInactive();

        DB::beginTransaction();

        try {
            $lottery->fill($validated);

            if ($visibility === 'inactive') {
                $lottery->status = LotteryStatus::INACTIVE;
            } elseif ($wasHidden) {
                $lottery->applyCountdown();
                $lottery->status = LotteryStatus::SELLING;
            } elseif (
                $startNewRound
                && !$lottery->isCancelled()
                && !$lottery->isInactive()
                && ($wasCompleted || $lottery->hasEnded())
            ) {
                $lottery->applyCountdown();
                $lottery->status = LotteryStatus::SELLING;
            } elseif (
                $lottery->starts_at
                && (int) $lottery->duration_seconds !== $previousDuration
                && !$wasCompleted
                && !$lottery->isCancelled()
                && !$lottery->isInactive()
            ) {
                $lottery->recalculateEndsAt();
            }

            $lottery->save();

            DB::commit();

            return redirect()
                ->route('admin.lotteries.show', $lottery)
                ->with(
                    'success',
                    $startNewRound
                        ? 'New lottery round started successfully.'
                        : 'Lottery updated successfully.'
                );

        } catch (Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to update lottery.'
                );
        }
    }

    public function destroy(Lottery $lottery)
    {
        try {
            /*
             * Soft-delete only. Tickets, draws, and winners stay so
             * purchase / draw history remains intact.
             */
            $lottery->delete();

            return back()
                ->with(
                    'success',
                    'Lottery deleted successfully.'
                );

        } catch (Throwable $e) {
            report($e);

            return back()
                ->with(
                    'error',
                    'Unable to delete lottery.'
                );
        }
    }

    /**
     * Draw lottery winners.
     */
    public function draw(
        Lottery $lottery,
        DrawLotteryAction $action
    ): RedirectResponse {
        try {
            $lottery->syncStatus();

            if (!$lottery->canDraw()) {
                return back()->with(
                    'error',
                    'This lottery cannot be drawn at this time.'
                );
            }

            $draw = $action->execute(
                lottery: $lottery,
                admin: auth('admin')->user()
            );

            return redirect()
                ->route('admin.lotteries.show', $lottery)
                ->with(
                    'success',
                    "Lottery draw completed successfully. {$draw->total_winners} winner(s) selected."
                );

        } catch (Throwable $e) {
            report($e);

            return back()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    private function validateRequest(
        Request $request
    ): array {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'ticket_price' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'duration_hours' => [
                'required',
                'integer',
                'min:0',
                'max:8760',
            ],

            'duration_minutes' => [
                'required',
                'integer',
                'min:0',
                'max:59',
            ],

            'duration_seconds' => [
                'required',
                'integer',
                'min:0',
                'max:59',
            ],

            'first_prize' => [
                'required',
                'numeric',
                'min:0',
            ],

            'second_prize' => [
                'required',
                'numeric',
                'min:0',
            ],

            'third_prize' => [
                'required',
                'numeric',
                'min:0',
            ],

            'fourth_prize' => [
                'required',
                'numeric',
                'min:0',
            ],

            'fifth_prize' => [
                'required',
                'numeric',
                'min:0',
            ],

            'max_tickets' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'max_tickets_per_user' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $durationSeconds =
            ((int) $validated['duration_hours'] * 3600)
            + ((int) $validated['duration_minutes'] * 60)
            + ((int) $validated['duration_seconds']);

        if ($durationSeconds < 1) {
            throw ValidationException::withMessages([
                'duration_hours' => 'Duration must be at least 1 second.',
            ]);
        }

        unset(
            $validated['duration_hours'],
            $validated['duration_minutes'],
            $validated['duration_seconds']
        );

        $validated['duration_seconds'] = $durationSeconds;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['max_tickets'] = $request->filled('max_tickets')
            ? (int) $request->input('max_tickets')
            : null;
        $validated['max_tickets_per_user'] = $request->filled('max_tickets_per_user')
            ? (int) $request->input('max_tickets_per_user')
            : null;

        return $validated;
    }
}