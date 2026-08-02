@extends('admin.base')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-0">
            Wallet Bonuses
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
                  action="{{ route('admin.wallets.bonuses', $wallet) }}"
                  class="row g-3">

                <div class="col-md-4">

                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Bonus ID..."
                           value="{{ request('search') }}">

                </div>

                <div class="col-md-3">

                    <select name="type"
                            class="form-control">

                        <option value="">
                            All Bonus Types
                        </option>

                        @foreach(\App\Enums\BonusType::cases() as $type)

                            <option value="{{ $type->value }}"
                                {{ request('type') == $type->value ? 'selected' : '' }}>

                                {{ ucfirst($type->value) }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3">

                    <select name="status"
                            class="form-control">

                        <option value="">
                            All Status
                        </option>

                        @foreach(\App\Enums\BonusStatus::cases() as $status)

                            <option value="{{ $status->value }}"
                                {{ request('status') == $status->value ? 'selected' : '' }}>

                                {{ ucfirst($status->value) }}

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

        <div class="col-md-4">

            <div class="card border-primary">

                <div class="card-body">

                    <h6 class="text-muted">
                        Current Bonus Balance
                    </h6>

                    <h3 class="text-primary">

                        {{ number_format($wallet->bonus_balance, 2) }}

                    </h3>

                    <small>{{ $wallet->currency }}</small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-success">

                <div class="card-body">

                    <h6 class="text-muted">
                        Active Bonuses
                    </h6>

                    <h3 class="text-success">

                        {{ $bonuses->where('status', \App\Enums\BonusStatus::ACTIVE)->count() }}

                    </h3>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-dark">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total Bonuses
                    </h6>

                    <h3>

                        {{ $bonuses->total() }}

                    </h3>

                </div>

            </div>

        </div>

    </div>

    {{-- Bonuses Table --}}
    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped mb-0">

                    <thead>

                    <tr>

                        <th>#</th>

                        <th>Type</th>

                        <th>Initial Amount</th>

                        <th>Remaining Amount</th>

                        <th>Status</th>

                        <th>Granted</th>

                        <th>Expires</th>

                        <th>Reference</th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($bonuses as $bonus)

                        <tr>

                            <td>

                                {{ $bonus->id }}

                            </td>

                            <td>

                                <span class="badge bg-primary">

                                    {{ ucfirst($bonus->type->value) }}

                                </span>

                            </td>

                            <td>

                                {{ number_format($bonus->initial_amount , 2) }}

                            </td>

                            <td>

                                {{ number_format($bonus->remaining_amount , 2) }}

                            </td>

                            <td>

                                @php

                                    $badge = match($bonus->status){

                                        \App\Enums\BonusStatus::ACTIVE => 'success',

                                        \App\Enums\BonusStatus::COMPLETED => 'primary',

                                        \App\Enums\BonusStatus::EXPIRED => 'danger',

                                        default => 'secondary',

                                    };

                                @endphp

                                <span class="badge bg-{{ $badge }}">

                                    {{ ucfirst($bonus->status->value) }}

                                </span>

                            </td>

                            <td>

                                {{ optional($bonus->activated_at)->format('d M Y h:i A') }}

                            </td>

                            <td>

                                {{ optional($bonus->expires_at)->format('d M Y h:i A') ?? '-' }}

                            </td>

                            <td>

                                @if($bonus->reference)

                                    {{ class_basename($bonus->reference_type) }}

                                    #{{ $bonus->reference_id }}

                                @else

                                    -

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8"
                                class="text-center">

                                No bonus history found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer">

            {{ $bonuses->withQueryString()->links() }}

        </div>

    </div>

</div>

@endsection