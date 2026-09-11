@extends('admin.base')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Purchased Packages</h4>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.user-investments.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control"
                            placeholder="User / package..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="package_id" class="form-control">
                            <option value="">All Packages</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}"
                                    {{ (string) request('package_id') === (string) $package->id ? 'selected' : '' }}>
                                    {{ $package->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}"
                                    {{ request('status') === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-dark w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Package</th>
                                <th>Price</th>
                                <th>Daily ROI</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Purchased</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($investments as $investment)
                                <tr>
                                    <td>{{ $investment->id }}</td>
                                    <td>
                                        {{ $investment->user?->name ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ $investment->user?->email }}</small>
                                    </td>
                                    <td>{{ $investment->package_name }}</td>
                                    <td>${{ number_format((float) $investment->price, 2) }}</td>
                                    <td>${{ number_format((float) $investment->daily_roi, 2) }}</td>
                                    <td>
                                        Day {{ $investment->daysElapsed() }} / {{ $investment->total_days }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $investment->starts_at->format('Y-m-d') }} →
                                            {{ $investment->ends_at->format('Y-m-d') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $investment->status === \App\Enums\InvestmentStatus::ACTIVE ? 'success' : ($investment->status === \App\Enums\InvestmentStatus::COMPLETED ? 'info' : 'secondary') }}">
                                            {{ $investment->status->label() }}
                                        </span>
                                    </td>
                                    <td>{{ $investment->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.user-investments.show', $investment) }}"
                                            class="btn btn-sm btn-primary">View ROI</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">No purchased packages found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($investments->hasPages())
                <div class="card-footer">
                    {{ $investments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
