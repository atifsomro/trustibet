<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Actions\Investment\BuyInvestmentPackageAction;
use App\Actions\Investment\ClaimInvestmentRoiAction;
use App\Actions\Investment\TransferRoiToWalletAction;
use App\Enums\BalanceType;
use App\Enums\InvestmentRoiStatus;
use App\Enums\InvestmentStatus;
use App\Enums\WalletTransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\InvestmentPackage;
use App\Models\InvestmentRoiLog;
use App\Models\UserInvestment;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class InvestmentController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected BuyInvestmentPackageAction $buyAction,
        protected ClaimInvestmentRoiAction $claimAction,
        protected TransferRoiToWalletAction $transferAction,
    ) {
    }

    public function index(): View
    {
        $user = auth()->user();
        $packages = InvestmentPackage::query()
            ->active()
            ->ordered()
            ->get();

        $wallet = $this->walletService->wallet($user);
        $claimableAmount = (float) InvestmentRoiLog::query()
            ->where('user_id', $user->id)
            ->claimableToday()
            ->sum('amount');

        $activeInvestment = UserInvestment::query()
            ->where('user_id', $user->id)
            ->active()
            ->latest()
            ->first();

        $totalInvested = (float) UserInvestment::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [InvestmentStatus::ACTIVE, InvestmentStatus::COMPLETED])
            ->sum('price');

        return view('pages.investment', [
            'packages' => $packages,
            'wallet' => $wallet,
            'claimableAmount' => $claimableAmount,
            'activeInvestment' => $activeInvestment,
            'totalInvested' => $totalInvested,
            'remainingTransferLimit' => $this->transferAction->remainingDailyLimit($user),
            'dailyTransferLimit' => (float) config('investment.roi_transfer.daily_limit', 50),
            'minTransferAmount' => (float) config('investment.roi_transfer.min_amount', 1),
        ]);
    }

    public function buy(Request $request, InvestmentPackage $package): RedirectResponse|JsonResponse
    {
        try {
            $user = auth()->user();
            $investment = $this->buyAction->execute($user, $package);
            $successMessage = sprintf(
                'Successfully purchased %s for $%s.',
                $investment->package_name,
                number_format((float) $investment->price, 2)
            );

            if ($this->wantsAjax($request)) {
                $wallet = $this->walletService->wallet($user);
                $activeInvestment = UserInvestment::query()
                    ->where('user_id', $user->id)
                    ->active()
                    ->latest()
                    ->first();
                $totalInvested = (float) UserInvestment::query()
                    ->where('user_id', $user->id)
                    ->whereIn('status', [InvestmentStatus::ACTIVE, InvestmentStatus::COMPLETED])
                    ->sum('price');

                return response()->json([
                    'ok' => true,
                    'type' => 'success',
                    'title' => 'Payment Successful',
                    'message' => $successMessage,
                    'action' => [
                        'label' => 'View My Investments',
                        'url' => route('investments.mine'),
                    ],
                    'wallet' => [
                        'withdrawable' => (float) ($wallet->withdrawable_balance ?? 0),
                    ],
                    'stats' => [
                        'active_plan' => $activeInvestment?->package_name ?? 'None',
                        'days_left' => $activeInvestment
                            ? max(0, $activeInvestment->total_days - $activeInvestment->daysElapsed())
                            : null,
                        'total_invested' => $totalInvested,
                    ],
                ]);
            }

            return redirect()
                ->route('investments.mine')
                ->with('success', $successMessage);
        } catch (InsufficientBalanceException $e) {
            return $this->investmentBuyErrorResponse(
                $request,
                'Insufficient withdrawable balance to buy this package.'
            );
        } catch (Throwable $e) {
            report($e);

            $message = method_exists($e, 'errors')
                ? collect($e->errors())->flatten()->first()
                : 'Unable to purchase package.';

            return $this->investmentBuyErrorResponse(
                $request,
                $message ?? 'Unable to purchase package.'
            );
        }
    }

    public function mine(Request $request): View
    {
        $user = auth()->user();

        $query = UserInvestment::query()
            ->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('package_name', 'LIKE', '%' . $request->search . '%');
        }

        $investments = $query->latest()->paginate(15)->withQueryString();

        $wallet = $this->walletService->wallet($user);
        $claimableAmount = (float) InvestmentRoiLog::query()
            ->where('user_id', $user->id)
            ->claimableToday()
            ->sum('amount');

        $totalEarnedRoi = (float) InvestmentRoiLog::query()
            ->where('user_id', $user->id)
            ->where('status', InvestmentRoiStatus::CLAIMED)
            ->sum('amount');

        $roiLedger = WalletTransaction::query()
            ->where('wallet_id', $wallet->id)
            ->where('balance_type', BalanceType::ROI)
            ->whereIn('type', [
                WalletTransactionType::INVESTMENT_ROI_CLAIM,
                WalletTransactionType::INVESTMENT_ROI_TRANSFER,
            ])
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        $stats = [
            'active' => UserInvestment::query()->where('user_id', $user->id)->active()->count(),
            'completed' => UserInvestment::query()->where('user_id', $user->id)->completed()->count(),
            'total_invested' => (float) UserInvestment::query()
                ->where('user_id', $user->id)
                ->whereIn('status', [InvestmentStatus::ACTIVE, InvestmentStatus::COMPLETED])
                ->sum('price'),
            'pending_roi' => $claimableAmount,
            'total_earned_roi' => $totalEarnedRoi,
        ];

        return view('user.investments.index', [
            'investments' => $investments,
            'wallet' => $wallet,
            'stats' => $stats,
            'claimableAmount' => $claimableAmount,
            'roiLedger' => $roiLedger,
            'remainingTransferLimit' => $this->transferAction->remainingDailyLimit($user),
            'dailyTransferLimit' => (float) config('investment.roi_transfer.daily_limit', 50),
            'minTransferAmount' => (float) config('investment.roi_transfer.min_amount', 1),
        ]);
    }

    public function show(UserInvestment $investment): View
    {
        abort_unless($investment->user_id === auth()->id(), 403);

        $roiLogs = $investment->roiLogs()
            ->orderByDesc('roi_date')
            ->paginate(30);

        return view('user.investments.show', [
            'investment' => $investment,
            'roiLogs' => $roiLogs,
        ]);
    }

    public function claim(): RedirectResponse
    {
        try {
            $result = $this->claimAction->execute(auth()->user());

            return back()->with('success', sprintf(
                'Claimed $%s ROI from %d package(s) into your ROI balance.',
                number_format($result['amount'], 2),
                $result['claimed_count']
            ));
        } catch (Throwable $e) {
            $message = method_exists($e, 'errors')
                ? collect($e->errors())->flatten()->first()
                : 'Unable to claim ROI.';

            return back()->with('error', $message ?? 'Unable to claim ROI.');
        }
    }

    public function transfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        try {
            $result = $this->transferAction->execute(
                auth()->user(),
                (float) $validated['amount']
            );

            return back()->with('success', sprintf(
                'Transferred $%s from ROI to withdrawable. Remaining daily limit: $%s.',
                number_format($result['amount'], 2),
                number_format($result['remaining_daily_limit'], 2)
            ));
        } catch (InsufficientBalanceException $e) {
            return back()->with('error', 'Insufficient ROI balance.');
        } catch (Throwable $e) {
            $message = method_exists($e, 'errors')
                ? collect($e->errors())->flatten()->first()
                : 'Unable to transfer ROI balance.';

            return back()->with('error', $message ?? 'Unable to transfer ROI balance.');
        }
    }

    protected function wantsAjax(Request $request): bool
    {
        return $request->expectsJson() || $request->ajax();
    }

    protected function investmentBuyErrorResponse(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($this->wantsAjax($request)) {
            $isInsufficient = str_contains(strtolower($message), 'insufficient');

            return response()->json([
                'ok' => false,
                'type' => $isInsufficient ? 'warning' : 'error',
                'title' => $isInsufficient ? 'Insufficient Balance' : 'Unable to Buy Package',
                'message' => $message,
                'action' => $isInsufficient ? [
                    'label' => 'Deposit Now',
                    'url' => route('deposits.index'),
                ] : null,
            ], 422);
        }

        return back()->with('error', $message);
    }
}
