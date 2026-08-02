<?php

declare(strict_types=1);

namespace App\Services\Wallet;

use App\Actions\Wallet\ApproveWithdrawalAction;
use App\Actions\Wallet\RequestWithdrawalAction;
use App\Actions\Wallet\RejectWithdrawalAction;
use App\Enums\WithdrawalStatus;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Database\Eloquent\Collection;

class WithdrawalService
{
    public function __construct(
        protected RequestWithdrawalAction $requestWithdrawalAction,
        protected ApproveWithdrawalAction $approveWithdrawalAction,
        protected RejectWithdrawalAction $rejectWithdrawalAction
    ) {
    }

    /**
     * Create a withdrawal request.
     */
    public function request(
        User $user,
        float $amount,
        string $paymentMethod,
        array $accountDetails,
        ?string $remarks = null
    ): WithdrawalRequest {

        return $this->requestWithdrawalAction->execute(
            user: $user,
            amount: $amount,
            paymentMethod: $paymentMethod,
            accountDetails: $accountDetails,
            remarks: $remarks
        );
    }

    /**
     * Approve a withdrawal request.
     */
    public function approve(
        WithdrawalRequest $withdrawal,
        User $approvedBy,
        ?string $remarks = null,
        array $meta = []
    ): WithdrawalRequest {

        return $this->approveWithdrawalAction->execute(
            withdrawal: $withdrawal,
            approvedBy: $approvedBy,
            remarks: $remarks,
            meta: $meta
        );
    }

    /**
     * Reject a withdrawal request.
     */
    public function reject(
        WithdrawalRequest $withdrawal,
        User $rejectedBy,
        ?string $remarks = null,
        array $meta = []
    ): WithdrawalRequest {

        return $this->rejectWithdrawalAction->execute(
            withdrawal: $withdrawal,
            rejectedBy: $rejectedBy,
            remarks: $remarks,
            meta: $meta
        );
    }

    /**
     * Get all withdrawals for a user.
     */
    public function history(User $user, int $perPage = 20)
    {
        return WithdrawalRequest::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get pending withdrawals.
     */
    public function pending(): Collection
    {
        return WithdrawalRequest::query()
            ->where('status', WithdrawalStatus::PENDING)
            ->latest()
            ->get();
    }

    /**
     * Get approved withdrawals.
     */
    public function approved(): Collection
    {
        return WithdrawalRequest::query()
            ->where('status', WithdrawalStatus::APPROVED)
            ->latest()
            ->get();
    }

    /**
     * Get rejected withdrawals.
     */
    public function rejected(): Collection
    {
        return WithdrawalRequest::query()
            ->where('status', WithdrawalStatus::REJECTED)
            ->latest()
            ->get();
    }

    /**
     * Find a withdrawal request.
     */
    public function find(int $id): WithdrawalRequest
    {
        return WithdrawalRequest::query()->findOrFail($id);
    }
}