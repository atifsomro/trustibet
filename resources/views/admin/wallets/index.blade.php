@extends('admin.base')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-0">
            Wallets
        </h4>

    </div>

    {{-- Filters --}}
    <div class="card mb-3">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.wallets.index') }}"
                  class="row g-3">

                <div class="col-md-4">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search user..."
                        value="{{ request('search') }}">

                </div>

                <div class="col-md-2">

                    <select
                        name="status"
                        class="form-control">

                        <option value="">
                            All Wallets
                        </option>

                        <option value="positive"
                            {{ request('status') == 'positive' ? 'selected' : '' }}>
                            Positive Balance
                        </option>

                        <option value="bonus"
                            {{ request('status') == 'bonus' ? 'selected' : '' }}>
                            Bonus Balance
                        </option>

                        <option value="locked"
                            {{ request('status') == 'locked' ? 'selected' : '' }}>
                            Locked Balance
                        </option>

                    </select>

                </div>

                <div class="col-md-2">

                    <select
                        name="currency"
                        class="form-control">

                        <option value="">
                            Currency
                        </option>

                        <option value="USD"
                            {{ request('currency') == 'USD' ? 'selected' : '' }}>
                            USD
                        </option>

                        <option value="PKR"
                            {{ request('currency') == 'PKR' ? 'selected' : '' }}>
                            PKR
                        </option>

                    </select>

                </div>

                <div class="col-md-2">

                    <button class="btn btn-dark">

                        Filter

                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- Wallets Table --}}
    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped mb-0">

                    <thead>

                    <tr>

                        <th>#</th>

                        <th>User</th>

                        <th>Withdrawable</th>

                        <th>Bonus</th>

                        <th>Locked</th>

                        <th>Total</th>

                        <th>Currency</th>

                        {{-- <th>Version</th> --}}

                        {{-- <th width="260">
                            Action
                        </th> --}}

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($wallets as $wallet)

                        <tr>

                            <td>

                                {{ $wallet->id }}

                            </td>

                            <td>

                                <strong>

                                    {{ $wallet->user->name ?? '-' }}

                                </strong>

                                <br>

                                <small>

                                    {{ $wallet->user->email ?? '-' }}

                                </small>

                            </td>

                            <td>

                                {{ number_format($wallet->withdrawable_balance, 2) }}

                            </td>

                            <td>

                                {{ number_format($wallet->bonus_balance, 2) }}

                            </td>

                            <td>

                                {{ number_format($wallet->locked_balance , 2) }}

                            </td>

                            <td>

                                <strong>

                                    {{ number_format(($wallet->withdrawable_balance + $wallet->bonus_balance + $wallet->locked_balance) , 2) }}

                                </strong>

                            </td>

                            <td>

                                {{ $wallet->currency }}

                            </td>

                            {{-- <td>

                                {{ $wallet->version }}

                            </td> --}}

                            {{-- <td>

                                <div class="btn-group btn-group-sm">

                                    <a href="{{ route('admin.wallets.show', $wallet) }}"
                                       class="btn btn-primary">

                                        View

                                    </a>

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

                                        Report

                                    </a>

                                </div>

                            </td> --}}

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9"
                                class="text-center">

                                No wallets found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer">

            {{ $wallets->links() }}

        </div>

    </div>

</div>

@endsection