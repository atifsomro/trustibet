@extends('admin.base')

@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            Withdrawal Requests
        </h4>
    </div>
    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.withdrawals.index') }}"
                  class="row g-3">

                <div class="col-md-4">

                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search user or withdrawal ID..."
                           value="{{ request('search') }}">

                </div>

                <div class="col-md-3">

                    <select name="status"
                            class="form-control">

                        <option value="">
                            All Status
                        </option>

                        @foreach(\App\Enums\WithdrawalStatus::cases() as $status)

                            <option value="{{ $status->value }}"
                                {{ request('status') == $status->value ? 'selected' : '' }}>

                                {{ ucfirst($status->value) }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3">

                    <input type="text"
                           name="payment_method"
                           class="form-control"
                           placeholder="Payment Method"
                           value="{{ request('payment_method') }}">

                </div>

                <div class="col-md-2">

                    <button class="btn btn-dark w-100">

                        Filter

                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- Withdrawals Table --}}
    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped mb-0">

                    <thead>

                    <tr>

                        <th>#</th>

                        <th>User</th>

                        <th>Amount</th>

                        <th>Payment Method</th>

                        <th>Status</th>

                        <th>Requested</th>

                        <th width="180">
                            Action
                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($withdrawals as $withdrawal)

                        <tr>

                            <td>

                                #{{ $withdrawal->id }}

                            </td>

                            <td>

                                <strong>

                                    {{ $withdrawal->user->name ?? '-' }}

                                </strong>

                                <br>

                                <small class="text-muted">

                                    {{ $withdrawal->user->email ?? '-' }}

                                </small>

                            </td>

                            <td>

                                {{ number_format($withdrawal->amount , 2) }}

                                {{ $withdrawal->wallet->currency ?? '' }}

                            </td>

                            <td>

                                {{ $withdrawal->payment_method ?? '-' }}

                            </td>

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

                            <td>

                                {{ $withdrawal->created_at->format('d M Y h:i A') }}

                            </td>

                            <td>

                                <a href="{{ route('admin.withdrawals.show', $withdrawal) }}"
                                   class="btn btn-sm btn-primary">

                                    View

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7"
                                class="text-center">

                                No withdrawal requests found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer">

            {{ $withdrawals->withQueryString()->links() }}

        </div>

    </div>

</div>

@endsection