<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Investment\UpdateUserInvestmentDailyRoiAction;
use App\Enums\InvestmentStatus;
use App\Http\Controllers\Controller;
use App\Models\InvestmentPackage;
use App\Models\UserInvestment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class UserInvestmentController extends Controller
{
    public function __construct(
        protected UpdateUserInvestmentDailyRoiAction $updateDailyRoiAction,
    ) {
    }

    public function index(Request $request)
    {
        $query = UserInvestment::query()
            ->with(['user', 'package']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('package_name', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhere('username', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('package_id')) {
            $query->where('investment_package_id', $request->package_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $investments = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $packages = InvestmentPackage::query()
            ->withTrashed()
            ->ordered()
            ->get();

        return view('admin.user_investments.index', [
            'investments' => $investments,
            'packages' => $packages,
            'statuses' => InvestmentStatus::cases(),
            'active' => 'user-investments',
        ]);
    }

    public function show(UserInvestment $userInvestment)
    {
        $userInvestment->load(['user', 'package']);

        $roiLogs = $userInvestment->roiLogs()
            ->orderByDesc('roi_date')
            ->paginate(30);

        return view('admin.user_investments.show', [
            'investment' => $userInvestment,
            'roiLogs' => $roiLogs,
            'active' => 'user-investments',
        ]);
    }

    public function updateDailyRoi(Request $request, UserInvestment $userInvestment): RedirectResponse
    {
        $validated = $request->validate([
            'daily_roi' => ['required', 'numeric', 'min:0.01'],
        ]);

        try {
            $result = $this->updateDailyRoiAction->execute(
                $userInvestment,
                (float) $validated['daily_roi']
            );

            $message = sprintf(
                'Daily ROI updated from $%s to $%s.',
                number_format($result['previous_daily_roi'], 2),
                number_format($result['daily_roi'], 2)
            );

            if ($result['pending_log_updated']) {
                $message .= " Today's pending ROI log amount was also updated.";
            }

            return redirect()
                ->route('admin.user-investments.show', $userInvestment)
                ->with('success', $message);
        } catch (Throwable $e) {
            $message = method_exists($e, 'errors')
                ? collect($e->errors())->flatten()->first()
                : 'Unable to update daily ROI.';

            return back()
                ->withInput()
                ->with('error', $message ?? 'Unable to update daily ROI.');
        }
    }
}
