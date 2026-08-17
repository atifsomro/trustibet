<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\RequestWithdrawalRequest;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\Wallet\BonusService;
use App\Services\Wallet\WalletManager;
use App\Services\Wallet\WalletService;
use App\Services\Wallet\WithdrawalService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected WalletManager $walletManager,
        protected WalletService $walletService,
        protected BonusService $bonusService,
        protected WithdrawalService $withdrawalService
    ) {
    }

    /**
     * Wallet dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $wallet = $this->walletManager->getOrCreate($user);

        $recentTransactions = WalletTransaction::query()
            ->where('wallet_id', $wallet->id)
            ->latest()
            ->limit(10)
            ->get();

        $recentBonuses = $this->bonusService
            ->activeBonuses($user)
            ->take(5);

        $recentWithdrawals = $this->withdrawalService
            ->history($user)
            ->take(5);

        return view('user.wallet.index', [
            'wallet' => $wallet,
            'balances' => $this->walletService->balances($user),
            'recentTransactions' => $recentTransactions,
            'recentBonuses' => $recentBonuses,
            'recentWithdrawals' => $recentWithdrawals,
        ]);
    }

    /**
     * Wallet transactions.
     */
    public function transactions(Request $request): View
    {
        $wallet = $this->walletManager->getOrCreate($request->user());

        $transactions = WalletTransaction::query()
            ->where('wallet_id', $wallet->id)
            ->latest()
            ->paginate(20);

        return view('user.wallet.transactions', compact(
            'wallet',
            'transactions'
        ));
    }
    public function showTransaction(WalletTransaction $transaction): View
    {
        abort_unless(
            $transaction->wallet->user_id === auth()->id(),
            403
        );

        return view('user.wallet.transaction-show', [
            'transaction' => $transaction,
        ]);
    }
    /**
     * Bonus history.
     */
    public function bonuses(Request $request): View
    {
        $bonuses = $this->bonusService
            ->history($request->user());

        return view('user.wallet.bonuses', compact(
            'bonuses'
        ));
    }

    /**
     * Withdrawal history.
     */
    public function withdrawals(Request $request): View
    {
        $withdrawals = $this->withdrawalService
            ->history($request->user());

        return view('user.wallet.withdrawals', compact(
            'withdrawals'
        ));
    }

    /**
     * Show withdrawal form.
     */
    public function createWithdrawal(Request $request): View
    {
        $wallet = $this->walletManager
            ->getOrCreate($request->user());

        return view('user.wallet.withdrawal-create', compact(
            'wallet'
        ));
    }

    /**
     * Store withdrawal request.
     */
    public function storeWithdrawal(
        RequestWithdrawalRequest $request
    ): RedirectResponse {
        $this->withdrawalService->request(
            user: $request->user(),
            amount: (float) $request->amount,
            paymentMethod: $request->payment_method,
            accountDetails: $request->account_details,
            remarks: $request->remarks
        );

        return redirect()
            ->route('wallet.withdrawals')
            ->with('success', 'Withdrawal request submitted successfully.');
    }

    /**
     * Wallet balance API.
     */
    public function balance(Request $request): JsonResponse
    {
        return response()->json(
            $this->walletService->balances(
                $request->user()
            )
        );
    }
}