@extends('admin.base')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Investment #{{ $investment->id }} — ROI History</h4>
            <a href="{{ route('admin.user-investments.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>User</strong>
                        <p>{{ $investment->user?->name }} ({{ $investment->user?->email }})</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Package</strong>
                        <p>{{ $investment->package_name }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>Price</strong>
                        <p>${{ number_format((float) $investment->price, 2) }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>Daily ROI</strong>
                        <p>${{ number_format((float) $investment->daily_roi, 2) }}</p>
                        @if ($investment->daily_roi_updated_at)
                            <small class="text-muted">Updated {{ $investment->daily_roi_updated_at->format('Y-m-d H:i') }}</small>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <strong>Status</strong>
                        <p>{{ $investment->status->label() }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <strong>Starts</strong>
                        <p>{{ $investment->starts_at->format('Y-m-d') }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Ends</strong>
                        <p>{{ $investment->ends_at->format('Y-m-d') }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Progress</strong>
                        <p>Day {{ $investment->daysElapsed() }} / {{ $investment->total_days }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Purchased At</strong>
                        <p>{{ $investment->created_at?->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($investment->status === \App\Enums\InvestmentStatus::ACTIVE)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Update Daily ROI</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Changes apply to this purchased package only. Today’s pending ROI log (if any) is updated;
                        past claimed/expired logs stay unchanged. Future daily ROI uses the new amount.
                    </p>
                    <form method="POST" action="{{ route('admin.user-investments.update-daily-roi', $investment) }}" class="form-inline">
                        @csrf
                        @method('PUT')
                        <div class="form-group mr-2 mb-2">
                            <label for="daily_roi" class="mr-2">Daily ROI ($)</label>
                            <input type="number"
                                step="0.01"
                                min="0.01"
                                name="daily_roi"
                                id="daily_roi"
                                class="form-control"
                                value="{{ old('daily_roi', number_format((float) $investment->daily_roi, 2, '.', '')) }}"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary mb-2">Save Daily ROI</button>
                    </form>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">ROI Logs</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Claimed At</th>
                                <th>Expired At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roiLogs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>{{ $log->roi_date->format('Y-m-d') }}</td>
                                    <td>${{ number_format((float) $log->amount, 2) }}</td>
                                    <td>
                                        @php
                                            $badge = match ($log->status) {
                                                \App\Enums\InvestmentRoiStatus::CLAIMED => 'success',
                                                \App\Enums\InvestmentRoiStatus::PENDING => 'warning',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $badge }}">{{ $log->status->label() }}</span>
                                    </td>
                                    <td>{{ $log->claimed_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td>{{ $log->expired_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No ROI history yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($roiLogs->hasPages())
                <div class="card-footer">
                    {{ $roiLogs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
