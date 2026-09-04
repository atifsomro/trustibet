@extends('admin.base')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Investment Packages</h4>
            <a href="{{ route('admin.investment-packages.create') }}" class="btn btn-success">
                Add Package
            </a>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.investment-packages.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control"
                            placeholder="Search package..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-dark">Filter</button>
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
                                <th>Name</th>
                                <th>Price</th>
                                <th>Daily ROI</th>
                                <th>Total Days</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($packages as $package)
                                <tr>
                                    <td>{{ $package->id }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td>${{ number_format((float) $package->price, 2) }}</td>
                                    <td>${{ number_format((float) $package->daily_roi, 2) }}</td>
                                    <td>{{ $package->total_days }} days</td>
                                    <td>{{ \Illuminate\Support\Str::limit($package->description, 40) }}</td>
                                    <td>
                                        @if ($package->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.investment-packages.edit', $package) }}"
                                            class="btn btn-sm btn-info text-white">Edit</a>
                                        <form action="{{ route('admin.investment-packages.destroy', $package) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete / archive this package?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No packages found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($packages->hasPages())
                <div class="card-footer">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
