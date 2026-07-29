<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class DepositController extends Controller
{
    /**
     * Display all deposits.
     */
    public function index(Request $request)
    {
        $query = Deposit::with([
            'user',
            'bankAccount',
            'approvedBy'
        ]);
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }
        // Status filter
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }
        $deposits = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();
        return view('admin.deposits.index',compact('deposits'));
    }



    /**
     * Show deposit details.
     */
    public function show(Deposit $deposit)
    {
        $deposit->load([
            'user',
            'bankAccount',
            'approvedBy'
        ]);


        return view(
            'admin.deposits.show',
            compact('deposit')
        );
    }



    /**
     * Approve deposit.
     */
    public function approve(Request $request, Deposit $deposit)
    {
        $request->validate([
            'admin_remarks' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ]);


        DB::beginTransaction();

        try {


            if (!$deposit->isPending()) {

                return back()
                    ->with(
                        'error',
                        'This deposit has already been processed.'
                    );
            }



            /*
            |--------------------------------------------------------------------------
            | Wallet Credit Logic
            |--------------------------------------------------------------------------
            |
            | Add your wallet balance update here.
            |
            | Example:
            |
            | $user->increment(
            |     'wallet_balance',
            |     $deposit->amount
            | );
            |
            */


            $deposit->approve(
                auth()->id(),
                $request->admin_remarks
            );


            DB::commit();


            return redirect()
                ->route(
                    'admin.deposits.index'
                )
                ->with(
                    'success',
                    'Deposit approved successfully.'
                );


        } catch (Throwable $e) {

            DB::rollBack();

            report($e);


            return back()
                ->with(
                    'error',
                    'Unable to approve deposit.'
                );
        }
    }




    /**
     * Reject deposit.
     */
    public function reject(Request $request, Deposit $deposit)
    {
        $request->validate([
            'admin_remarks' => [
                'required',
                'string',
                'max:1000'
            ]
        ]);


        DB::beginTransaction();

        try {


            if (!$deposit->isPending()) {

                return back()
                    ->with(
                        'error',
                        'This deposit has already been processed.'
                    );
            }



            $deposit->reject(
                auth()->id(),
                $request->admin_remarks
            );


            DB::commit();


            return redirect()
                ->route(
                    'admin.deposits.index'
                )
                ->with(
                    'success',
                    'Deposit rejected successfully.'
                );


        } catch (Throwable $e) {


            DB::rollBack();

            report($e);


            return back()
                ->with(
                    'error',
                    'Unable to reject deposit.'
                );
        }
    }
}