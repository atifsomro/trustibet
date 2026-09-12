@extends('admin.base')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-0">
            Withdrawal Details
        </h4>

        <a href="{{ route('admin.withdrawals.index') }}"
           class="btn btn-secondary">

            Back

        </a>

    </div>

    <div class="row">

        {{-- Withdrawal Information --}}
        <div class="col-lg-8 mb-3">

            <div class="card">

                <div class="card-header">

                    <strong>

                        Withdrawal Information

                    </strong>

                </div>

                <div class="card-body">

                    <table class="table table-bordered mb-0">

                        <tbody>

                        <tr>

                            <th width="30%">
                                Withdrawal ID
                            </th>

                            <td>

                                #{{ $withdrawal->id }}

                            </td>

                        </tr>

                        <tr>

                            <th>
                                User
                            </th>

                            <td>

                                {{ $withdrawal->user->name }}

                                <br>

                                <small class="text-muted">

                                    {{ $withdrawal->user->email }}

                                </small>

                            </td>

                        </tr>

                        @if(!empty($withdrawal->user->username))

                        <tr>

                            <th>
                                Username
                            </th>

                            <td>

                                {{ $withdrawal->user->username }}

                            </td>

                        </tr>

                        @endif

                        <tr>

                            <th>
                                Amount
                            </th>

                            <td>

                                {{ number_format($withdrawal->amount , 2) }}
                                {{ $withdrawal->wallet->currency }}

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Payment Method
                            </th>

                            <td>

                                {{ $withdrawal->payment_method }}

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Account Details
                            </th>

                            <td>

                                @if(is_array($withdrawal->account_details))
                                    <pre class="mb-0">{{ json_encode($withdrawal->account_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                @else
                                    {!! nl2br(e($withdrawal->account_details ?: '-')) !!}
                                @endif

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Status
                            </th>

                            <td>

                                @php

                                    $badge = match($withdrawal->status){

                                        \App\Enums\WithdrawalStatus::PENDING => 'warning',

                                        \App\Enums\WithdrawalStatus::APPROVED => 'success',

                                        \App\Enums\WithdrawalStatus::REJECTED => 'danger',

                                        default => 'secondary',

                                    };

                                @endphp

                                <span class="badge bg-{{ $badge }}">

                                    {{ ucfirst($withdrawal->status->value) }}

                                </span>

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Requested At
                            </th>

                            <td>

                                {{ $withdrawal->created_at->format('d M Y h:i A') }}

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Processed At
                            </th>

                            <td>

                                {{ optional($withdrawal->processed_at)->format('d M Y h:i A') ?? '-' }}

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Remarks / Notes
                            </th>

                            <td>

                                {!! nl2br(e($withdrawal->remarks ?: '-')) !!}

                            </td>

                        </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        {{-- Wallet Summary --}}
        <div class="col-lg-4 mb-3">

            <div class="card mb-3">

                <div class="card-header">

                    <strong>

                        Wallet

                    </strong>

                </div>

                <div class="card-body">

                    <table class="table table-borderless mb-0">

                        <tr>

                            <th>
                                Withdrawable
                            </th>

                            <td class="text-end">

                                {{ number_format($withdrawal->wallet->withdrawable_balance , 2) }}

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Bonus
                            </th>

                            <td class="text-end">

                                {{ number_format($withdrawal->wallet->bonus_balance , 2) }}

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Locked
                            </th>

                            <td class="text-end">

                                {{ number_format($withdrawal->wallet->locked_balance , 2) }}

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

            @if($withdrawal->status === \App\Enums\WithdrawalStatus::PENDING)

                <div class="card">

                    <div class="card-header">

                        <strong>

                            Actions

                        </strong>

                    </div>

                    <div class="card-body">

                        <form method="POST"
                              action="{{ route('admin.withdrawals.approve', $withdrawal) }}"
                              class="mb-3">

                            @csrf

                            <button class="btn btn-success w-100"
                                    onclick="return confirm('Approve this withdrawal?')">

                                Approve Withdrawal

                            </button>

                        </form>

                        <form method="POST"
                              action="{{ route('admin.withdrawals.reject', $withdrawal) }}">

                            @csrf

                            <div class="mb-3">

                                <label class="form-label">

                                    Rejection Reason

                                </label>

                                <textarea
                                    name="admin_notes"
                                    rows="4"
                                    class="form-control">{{ old('admin_notes') }}</textarea>

                            </div>

                            <button class="btn btn-danger w-100"
                                    onclick="return confirm('Reject this withdrawal?')">

                                Reject Withdrawal

                            </button>

                        </form>

                    </div>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection