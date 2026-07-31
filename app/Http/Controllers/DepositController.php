<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    /**
     * Display user's deposits.
     */
    public function index()
    {
        $deposits = Deposit::with('bankAccount')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
        $banks = BankAccount::all();

        return view('deposits.index', compact('deposits', 'banks'));
    }

    /**
     * Store a newly created deposit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();

        try {

            $path = $request->file('payment_proof')
                ->store('deposits', 'public');

            Deposit::create([
                'user_id' => Auth::id(),
                'bank_account_id' => $validated['bank_account_id'],
                'amount' => $validated['amount'],
                'currency' => 'PKR', // or config('app.currency')
                'reference_number' => $validated['transaction_id'] ?? null,
                'payment_proof' => $path,
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()
                ->route('deposits.index')
                ->with('success', 'Your deposit request has been submitted successfully and is awaiting admin approval.');

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Unable to submit your deposit request. Please try again.');
        }
    }
}