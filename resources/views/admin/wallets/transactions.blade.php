@extends('admin.base')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-0">
            Wallet Transactions
        </h4>

        <a href="{{ route('admin.wallets.show', $wallet) }}"
           class="btn btn-secondary">

            Back to Wallet

        </a>

    </div>

    {{-- Filters --}}
    <div class="card mb-3">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.wallets.transactions', $wallet) }}"
                  class="row g-3">

                <div class="col-md-4">

                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Transaction ID / Reference..."
                           value="{{ request('search') }}">

                </div>

                <div class="col-md-3">

                    <select name="balance_type"
                            class="form-control">

                        <option value="">
                            All Balance Types
                        </option>

                        @foreach(\App\Enums\BalanceType::cases() as $type)

                            <option value="{{ $type->value }}"
                                {{ request('balance_type') == $type->value ? 'selected' : '' }}>

                                {{ ucfirst($type->value) }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3">

                    <select name="transaction_type"
                            class="form-control">

                        <option value="">
                            All Transaction Types
                        </option>

                        @foreach(\App\Enums\WalletTransactionType::cases() as $type)

                            <option value="{{ $type->value }}"
                                {{ request('transaction_type') == $type->value ? 'selected' : '' }}>

                                {{ ucwords(str_replace('_', ' ', $type->value)) }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-2">

                    <button class="btn btn-dark w-100">

                        Filter

                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- Wallet Summary --}}
    <div class="row mb-3">

        <div class="col-md-3">

            <div class="card border-success">

                <div class="card-body">

                    <h6 class="text-muted">
                        Withdrawable
                    </h6>

                    <h4 class="text-success">

                        {{ number_format($wallet->withdrawable_balance , 2) }}

                    </h4>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-primary">

                <div class="card-body">

                    <h6 class="text-muted">
                        Bonus
                    </h6>

                    <h4 class="text-primary">

                        {{ number_format($wallet->bonus_balance, 2) }}

                    </h4>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-warning">

                <div class="card-body">

                    <h6 class="text-muted">
                        Locked
                    </h6>

                    <h4 class="text-warning">

                        {{ number_format($wallet->locked_balance , 2) }}

                    </h4>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-dark">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total
                    </h6>

                    <h4>

                        {{ number_format(($wallet->withdrawable_balance + $wallet->bonus_balance + $wallet->locked_balance) , 2) }}

                    </h4>

                </div>

            </div>

        </div>

    </div>

    {{-- Transactions --}}
    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped mb-0">

                    <thead>

                    <tr>

                        <th>#</th>

                        <th>Type</th>

                        <th>Balance Type</th>

                        <th>Amount</th>

                        <th>Balance After</th>

                        <th>Reference</th>

                        <th>Bonus</th>

                        <th>Date</th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($transactions as $transaction)

                        <tr>

                            <td>

                                {{ $transaction->id }}

                            </td>

                            <td>

                                <span class="badge bg-info">

                                    {{ ucwords(str_replace('_', ' ', $transaction->transaction_type->value)) }}

                                </span>

                            </td>

                            <td>

                                <span class="badge bg-secondary">

                                    {{ ucfirst($transaction->balance_type->value) }}

                                </span>

                            </td>

                            <td>

                                @if($transaction->amount >= 0)

                                    <span class="text-success fw-bold">

                                        +{{ number_format($transaction->amount , 2) }}

                                    </span>

                                @else

                                    <span class="text-danger fw-bold">

                                        {{ number_format($transaction->amount , 2) }}

                                    </span>

                                @endif

                            </td>

                            <td>

                                {{ number_format($transaction->balance_after , 2) }}

                            </td>

                            <td>

                                @if($transaction->reference)

                                    {{ class_basename($transaction->reference_type) }}

                                    #{{ $transaction->reference_id }}

                                @else

                                    -

                                @endif

                            </td>

                            <td>

                                @if($transaction->bonus)

                                    #{{ $transaction->bonus->id }}

                                @else

                                    -

                                @endif

                            </td>

                            <td>

                                {{ $transaction->created_at->format('d M Y h:i A') }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8"
                                class="text-center">

                                No transactions found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer">

            {{ $transactions->withQueryString()->links() }}

        </div>

    </div>

</div>

@endsection