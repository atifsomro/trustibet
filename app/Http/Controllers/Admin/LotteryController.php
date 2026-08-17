<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Lottery\DrawLotteryAction;
use App\Http\Controllers\Controller;
use App\Models\Lottery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class LotteryController extends Controller
{
    /**
     * Display lotteries.
     */
    public function index(Request $request)
    {
        $query = Lottery::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('currency', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Active Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('active')) {
            $query->where(
                'is_active',
                $request->active === '1'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Draw Counters
        |--------------------------------------------------------------------------
        |
        | Load the counters with the main query instead of querying the
        | database separately from the Blade file.
        |
        */

        $lotteries = $query
            ->withCount([
                'draws as completed_draws_count' => function ($query) {
                    $query->where('status', 'completed');
                },

                'draws as total_draws_count',
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
     * Show create form.
     */
    public function create()
    {
        return view('admin.lotteries.create');
    }


    /**
     * Store lottery.
     */
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


    /**
     * Show edit form.
     */
    public function edit(Lottery $lottery)
    {
        return view(
            'admin.lotteries.edit',
            compact('lottery')
        );
    }


    /**
     * Update lottery.
     */
    public function update(
        Request $request,
        Lottery $lottery
    ) {
        $validated = $this->validateRequest($request);

        DB::beginTransaction();

        try {

            $lottery->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.lotteries.index')
                ->with(
                    'success',
                    'Lottery updated successfully.'
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


    /**
     * Delete lottery.
     */
    public function destroy(Lottery $lottery)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Do not delete a lottery if tickets exist.
            |--------------------------------------------------------------------------
            */

            if ($lottery->tickets()->exists()) {
                return back()->with(
                    'error',
                    'This lottery cannot be deleted because tickets have been purchased.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Do not delete a lottery if draws exist.
            |--------------------------------------------------------------------------
            */

            if ($lottery->draws()->exists()) {
                return back()->with(
                    'error',
                    'This lottery cannot be deleted because draw records exist.'
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

            $draw = $action->execute(
                lottery: $lottery,
                admin: auth('admin')->user()
            );

            return redirect()
                ->route(
                    'admin.lotteries.show',
                    $lottery
                )
                ->with(
                    'success',
                    "Lottery draw completed successfully. {$draw->total_winners} winner(s) selected."
                );

        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Show exception message to admin.
            |--------------------------------------------------------------------------
            */

            report($e);

            return redirect()
                ->back()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }


    /**
     * Validate lottery request.
     */
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

            'second_prize_winners' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'third_prize' => [
                'required',
                'numeric',
                'min:0',
            ],

            'third_prize_winners' => [
                'nullable',
                'integer',
                'min:1',
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