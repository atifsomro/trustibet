<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletReconciliationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected WalletReconciliationService $walletReconciliationService
    ) {
    }

    /**
     * Display all wallets.
     */
    public function index(Request $request): View
    {
        $wallets = Wallet::query()
            ->with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('currency'), function ($query) use ($request) {
                $query->where('currency', $request->currency);
            })
            ->when($request->filled('status'), function ($query) use ($request) {

                match ($request->status) {

                    'positive' => $query->where('withdrawable_balance', '>', 0),

                    'bonus' => $query->where('bonus_balance', '>', 0),

                    'locked' => $query->where('locked_balance', '>', 0),

                    default => null,

                };
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.wallets.index', compact('wallets'));
    }

    /**
     * Display wallet details.
     */
    public function show(Wallet $wallet): View
    {
        $wallet->load([
            'user',
            'transactions' => fn($query) => $query->latest()->limit(10),
            'bonuses' => fn($query) => $query->latest()->limit(10),
            'withdrawals' => fn($query) => $query->latest()->limit(10),
        ]);

        dd($wallet->transactions[0]->transaction_type);
        return view('admin.wallets.show', compact('wallet'));
    }

    /**
     * Display wallet transactions.
     */
    public function transactions(Request $request, Wallet $wallet): View
    {
        $transactions = WalletTransaction::query()
            ->where('wallet_id', $wallet->id)
            ->when($request->filled('balance_type'), function ($query) use ($request) {
                $query->where('balance_type', $request->balance_type);
            })
            ->when($request->filled('transaction_type'), function ($query) use ($request) {
                $query->where('transaction_type', $request->transaction_type);
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.wallets.transactions', compact(
            'wallet',
            'transactions'
        ));
    }

    /**
     * Display wallet bonus history.
     */
    public function bonuses(Wallet $wallet): View
    {
        $bonuses = $wallet->bonuses()
            ->latest()
            ->paginate(20);

        return view('admin.wallets.bonuses', compact(
            'wallet',
            'bonuses'
        ));
    }

    /**
     * Wallet reconciliation.
     */
    public function reconciliation(Wallet $wallet): View
    {
        $wallet->loadMissing('user');
        $report = $this->walletReconciliationService
            ->reconcile($wallet->user);

        return view('admin.wallets.reconciliation', compact(
            'wallet',
            'report'
        ));
    }
}