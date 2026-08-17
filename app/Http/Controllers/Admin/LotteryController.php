<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Lottery\DrawLotteryAction;
use App\Http\Controllers\Controller;
use App\Models\Lottery;
use App\Models\LotteryDraw;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $query->where('status', $request->status);
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
            ->paginate(10);

        return view(
            'admin.lotteries.show',
            compact('lottery', 'draws')
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

        DB::beginTransaction();

        try {
            Lottery::create($validated);

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

        $previousStart = $lottery->sales_start_at?->copy();
        $previousEnd = $lottery->sales_end_at?->copy();

        $newStart = !empty($validated['sales_start_at'])
            ? \Illuminate\Support\Carbon::parse($validated['sales_start_at'])
            : null;

        $newEnd = \Illuminate\Support\Carbon::parse($validated['sales_end_at']);

        $startSame = $previousStart === null
            ? $newStart === null
            : $previousStart->equalTo($newStart);

        $endSame = $previousEnd === null
            ? false
            : $previousEnd->equalTo($newEnd);

        $periodChanged = !$startSame || !$endSame;

        DB::beginTransaction();

        try {
            $lottery->update($validated);

            /*
             * A completed lottery is reusable. When the admin changes the
             * sales period for the next round, automatically reopen the
             * lottery so the existing frontend can sell tickets again.
             */
            if (
                $periodChanged
                && $lottery->status === \App\Enums\LotteryStatus::COMPLETED
                && !$lottery->isCancelled()
                && $newEnd->isFuture()
            ) {
                $lottery->update([
                    'status' => $newStart && $newStart->isFuture()
                        ? \App\Enums\LotteryStatus::SCHEDULED
                        : \App\Enums\LotteryStatus::SELLING,
                    'is_active' => true,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.lotteries.show', $lottery)
                ->with(
                    'success',
                    $periodChanged
                        ? 'Lottery round updated successfully.'
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
             * Do not delete a lottery after tickets have been sold.
             */
            if ($lottery->tickets()->exists()) {
                return back()->with(
                    'error',
                    'This lottery cannot be deleted because tickets have already been sold.'
                );
            }

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
        return $request->validate([
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

            'sales_start_at' => [
                'nullable',
                'date',
            ],

            'sales_end_at' => [
                'required',
                'date',
                'after_or_equal:sales_start_at',
            ],

            'draw_at' => [
                'nullable',
                'date',
                'after_or_equal:sales_end_at',
            ],

            /*
            |--------------------------------------------------------------------------
            | Five prizes
            |--------------------------------------------------------------------------
            */

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

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:draft,scheduled,selling,ended,drawing,completed,cancelled',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);
    }
}