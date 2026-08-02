@extends('admin.base')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-0">
            Wallet Details
        </h4>

        <a href="{{ route('admin.wallets.index') }}"
           class="btn btn-secondary">

            Back

        </a>

    </div>

    <div class="row">

        {{-- Wallet Information --}}
        <div class="col-lg-4 mb-3">

            <div class="card">

                <div class="card-header">

                    <strong>
                        Wallet Information
                    </strong>

                </div>

                <div class="card-body">

                    <table class="table table-borderless mb-0">

                        <tr>

                            <th width="45%">
                                Wallet ID
                            </th>

                            <td>
                                #{{ $wallet->id }}
                            </td>

                        </tr>

                        <tr>

                            <th>
                                User
                            </th>

                            <td>

                                {{ $wallet->user->name }}

                                <br>

                                <small class="text-muted">
                                    {{ $wallet->user->email }}
                                </small>

                            </td>

                        </tr>

                        @if(!empty($wallet->user->username))

                        <tr>

                            <th>
                                Username
                            </th>

                            <td>
                                {{ $wallet->user->username }}
                            </td>

                        </tr>

                        @endif

                        <tr>

                            <th>
                                Currency
                            </th>

                            <td>
                                {{ $wallet->currency }}
                            </td>

                        </tr>

                        <tr>

                            <th>
                                Version
                            </th>

                            <td>
                                {{ $wallet->version }}
                            </td>

                        </tr>

                        <tr>

                            <th>
                                Created
                            </th>

                            <td>
                                {{ $wallet->created_at->format('d M Y h:i A') }}
                            </td>

                        </tr>

                        <tr>

                            <th>
                                Updated
                            </th>

                            <td>
                                {{ $wallet->updated_at->format('d M Y h:i A') }}
                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>

        {{-- Wallet Balances --}}
        <div class="col-lg-8 mb-3">

            <div class="row">

                <div class="col-md-3 mb-3">

                    <div class="card border-success">

                        <div class="card-body text-center">

                            <h6 class="text-muted">
                                Withdrawable
                            </h6>

                            <h4 class="text-success">

                                {{ number_format($wallet->withdrawable_balance , 2) }}

                            </h4>

                            <small>
                                {{ $wallet->currency }}
                            </small>

                        </div>

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <div class="card border-primary">

                        <div class="card-body text-center">

                            <h6 class="text-muted">
                                Bonus
                            </h6>

                            <h4 class="text-primary">

                                {{ number_format($wallet->bonus_balance, 2) }}

                            </h4>

                            <small>
                                {{ $wallet->currency }}
                            </small>

                        </div>

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <div class="card border-warning">

                        <div class="card-body text-center">

                            <h6 class="text-muted">
                                Locked
                            </h6>

                            <h4 class="text-warning">

                                {{ number_format($wallet->locked_balance , 2) }}

                            </h4>

                            <small>
                                {{ $wallet->currency }}
                            </small>

                        </div>

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <div class="card border-dark">

                        <div class="card-body text-center">

                            <h6 class="text-muted">
                                Total
                            </h6>

                            <h4>

                                {{ number_format(($wallet->withdrawable_balance + $wallet->bonus_balance + $wallet->locked_balance) , 2) }}

                            </h4>

                            <small>
                                {{ $wallet->currency }}
                            </small>

                        </div>

                    </div>

                </div>

            </div>

            {{-- Quick Actions --}}
            <div class="card">

                <div class="card-header">

                    <strong>
                        Quick Actions
                    </strong>

                </div>

                <div class="card-body">

                    <div class="btn-group">

                        <a href="{{ route('admin.wallets.transactions', $wallet) }}"
                           class="btn btn-info">

                            Transactions

                        </a>

                        <a href="{{ route('admin.wallets.bonuses', $wallet) }}"
                           class="btn btn-success">

                            Bonuses

                        </a>

                        <a href="{{ route('admin.wallets.reconciliation', $wallet) }}"
                           class="btn btn-warning">

                            Reconciliation

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Recent Transactions --}}
    <div class="card mb-3">

        <div class="card-header d-flex justify-content-between">

            <strong>
                Recent Transactions
            </strong>

            <a href="{{ route('admin.wallets.transactions', $wallet) }}">

                View All

            </a>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped mb-0">

                    <thead>

                    <tr>

                        <th>#</th>
                        <th>Type</th>
                        <th>Balance</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                        <th>Date</th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($wallet->transactions as $transaction)

                        <tr>

                            <td>
                                {{ $transaction->id }}
                            </td>

                            <td>
                                {{ str_replace('_', ' ', ucfirst($transaction->transaction_type->value)) }}
                            </td>

                            <td>
                                {{ ucfirst($transaction->balance_type->value) }}
                            </td>

                            <td>

                                {{ number_format($transaction->amount , 2) }}

                            </td>

                            <td>

                                {{ number_format($transaction->balance_after , 2) }}

                            </td>

                            <td>

                                {{ $transaction->created_at->format('d M Y h:i A') }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="text-center">

                                No transactions found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- Recent Bonuses --}}
    <div class="card mb-3">

        <div class="card-header d-flex justify-content-between">

            <strong>

                Recent Bonuses

            </strong>

            <a href="{{ route('admin.wallets.bonuses', $wallet) }}">

                View All

            </a>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped mb-0">

                    <thead>

                    <tr>

                        <th>#</th>
                        <th>Type</th>
                        <th>Initial</th>
                        <th>Remaining</th>
                        <th>Status</th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($wallet->bonuses as $bonus)

                        <tr>

                            <td>{{ $bonus->id }}</td>

                            <td>{{ ucfirst($bonus->type->value) }}</td>

                            <td>{{ number_format($bonus->initial_amount / 100,2) }}</td>

                            <td>{{ number_format($bonus->remaining_amount / 100,2) }}</td>

                            <td>{{ ucfirst($bonus->status->value) }}</td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="text-center">

                                No bonuses found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- Recent Withdrawals --}}
    <div class="card">

        <div class="card-header">

            <strong>

                Recent Withdrawals

            </strong>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped mb-0">

                    <thead>

                    <tr>

                        <th>#</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($wallet->withdrawals as $withdrawal)

                        <tr>

                            <td>{{ $withdrawal->id }}</td>

                            <td>{{ number_format($withdrawal->amount / 100,2) }}</td>

                            <td>{{ ucfirst($withdrawal->status->value) }}</td>

                            <td>{{ $withdrawal->created_at->format('d M Y h:i A') }}</td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="text-center">

                                No withdrawals found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection