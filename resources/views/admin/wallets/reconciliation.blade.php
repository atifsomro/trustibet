@extends('admin.base')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-0">
            Wallet Reconciliation
        </h4>

        <a href="{{ route('admin.wallets.show', $wallet) }}"
           class="btn btn-secondary">

            Back to Wallet

        </a>

    </div>

    {{-- Wallet Summary --}}
    <div class="row mb-3">

        <div class="col-md-3">

            <div class="card border-success">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Withdrawable
                    </h6>

                    <h3 class="text-success">

                        {{ number_format($wallet->withdrawable_balance , 2) }}

                    </h3>

                    <small>{{ $wallet->currency }}</small>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-primary">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Bonus
                    </h6>

                    <h3 class="text-primary">

                        {{ number_format($wallet->bonus_balance, 2) }}

                    </h3>

                    <small>{{ $wallet->currency }}</small>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-warning">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Locked
                    </h6>

                    <h3 class="text-warning">

                        {{ number_format($wallet->locked_balance , 2) }}

                    </h3>

                    <small>{{ $wallet->currency }}</small>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-dark">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Total Wallet
                    </h6>

                    <h3>

                        {{ number_format(($wallet->withdrawable_balance + $wallet->bonus_balance + $wallet->locked_balance) , 2) }}

                    </h3>

                    <small>{{ $wallet->currency }}</small>

                </div>

            </div>

        </div>

    </div>

    {{-- Reconciliation Result --}}
    <div class="card mb-3">

        <div class="card-header">

            <strong>

                Reconciliation Report

            </strong>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tbody>

                <tr>

                    <th width="35%">
                        Withdrawable Balance
                    </th>

                    <td>

                        {{ number_format($report['wallet']['withdrawable_balance'] , 2) }}

                    </td>

                </tr>

                <tr>

                    <th>
                        Ledger Withdrawable
                    </th>

                    <td>

                        {{ number_format($report['ledger']['withdrawable_balance'] , 2) }}

                    </td>

                </tr>

                <tr>

                    <th>
                        Bonus Balance
                    </th>

                    <td>

                        {{ number_format($report['wallet']['bonus_balance'] , 2) }}

                    </td>

                </tr>

                <tr>

                    <th>
                        Ledger Bonus
                    </th>

                    <td>

                        {{ number_format($report['ledger']['bonus_balance'] , 2) }}

                    </td>

                </tr>

                <tr>

                    <th>
                        Locked Balance
                    </th>

                    <td>

                        {{ number_format($report['wallet']['locked_balance'] , 2) }}

                    </td>

                </tr>

                <tr>

                    <th>
                        Wallet Version
                    </th>

                    <td>

                        {{ $wallet->version }}

                    </td>

                </tr>

                <tr>

                    <th>
                        Total Transactions
                    </th>

                    <td>

                        {{ number_format($report['statistics']['transactions']) }}

                    </td>

                </tr>

                <tr>

                    <th>
                        Total Credits
                    </th>

                    <td>

                        {{ number_format($report['statistics']['credits'] , 2) }}

                    </td>

                </tr>

                <tr>

                    <th>
                        Total Debits
                    </th>

                    <td>

                        {{ number_format($report['statistics']['debits'] , 2) }}

                    </td>

                </tr>

                <tr>

                    <th>
                        Reconciliation Status
                    </th>

                    <td>

                        @if($report['is_balanced'])

                            <span class="badge bg-success">

                                Balanced

                            </span>

                        @else

                            <span class="badge bg-danger">

                                Mismatch Detected

                            </span>

                        @endif

                    </td>

                </tr>

                </tbody>

            </table>

        </div>

    </div>

    {{-- Differences --}}
    @if(!$report['is_balanced'])

        <div class="card border-danger">

            <div class="card-header bg-danger text-white">

                <strong>

                    Balance Differences

                </strong>

            </div>

            <div class="card-body">

                <table class="table table-bordered mb-0">

                    <thead>

                    <tr>

                        <th>Balance Type</th>

                        <th>Difference</th>

                    </tr>

                    </thead>

                    <tbody>

                    @foreach($report['differences'] as $type => $difference)

                        <tr>

                            <td>

                                {{ ucfirst(str_replace('_', ' ', $type)) }}

                            </td>

                            <td>

                                @if($difference == 0)

                                    <span class="text-success">

                                        0.00

                                    </span>

                                @else

                                    <span class="text-danger fw-bold">

                                        {{ number_format($difference , 2) }}

                                    </span>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    @endif

</div>

@endsection