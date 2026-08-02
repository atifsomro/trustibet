<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\WithdrawalStatus;
use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\Wallet\WithdrawalService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function __construct(
        protected WithdrawalService $withdrawalService
    ) {
    }

    /**
     * Display all withdrawal requests.
     */
    public function index(Request $request): View
    {
        $withdrawals = WithdrawalRequest::query()
            ->with([
                'user',
                'wallet',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {

                $search = trim($request->search);

                $query->where(function ($q) use ($search) {

                    $q->where('id', $search)

                        ->orWhereHas('user', function ($userQuery) use ($search) {

                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");

                        });

                });

            })
            ->when($request->filled('status'), function ($query) use ($request) {

                $query->where('status', $request->status);

            })
            ->when($request->filled('payment_method'), function ($query) use ($request) {

                $query->where('payment_method', $request->payment_method);

            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    /**
     * Display withdrawal details.
     */
    public function show(WithdrawalRequest $withdrawal): View
    {
        $withdrawal->load([
            'user',
            'wallet',
        ]);

        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Approve a withdrawal request.
     */
    public function approve(
        WithdrawalRequest $withdrawal
    ): RedirectResponse {

        if ($withdrawal->status !== WithdrawalStatus::PENDING) {

            return back()->with(
                'error',
                'Only pending withdrawal requests can be approved.'
            );

        }

        $this->withdrawalService->approve($withdrawal, auth('admin')->user());

        return redirect()
            ->route('admin.withdrawals.show', $withdrawal)
            ->with(
                'success',
                'Withdrawal request approved successfully.'
            );
    }

    /**
     * Reject a withdrawal request.
     */
    public function reject(
        Request $request,
        WithdrawalRequest $withdrawal
    ): RedirectResponse {

        if ($withdrawal->status !== WithdrawalStatus::PENDING) {

            return back()->with(
                'error',
                'Only pending withdrawal requests can be rejected.'
            );

        }

        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->withdrawalService->reject(
            withdrawal: $withdrawal,
            rejectedBy: auth('admin')->user(),
            remarks: $request->input('admin_notes'),
        );

        return redirect()
            ->route('admin.withdrawals.show', $withdrawal)
            ->with(
                'success',
                'Withdrawal request rejected successfully.'
            );
    }
}