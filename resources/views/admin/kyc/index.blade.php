@extends('admin.base')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                KYC Verification
            </h4>
        </div>

        <div class="card">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-striped mb-0">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Full Name</th>
                                <th>Country</th>
                                <th>ID Type</th>
                                <th>Status</th>
                                <th>Submitted At</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($kycs as $kyc)
                                <tr>

                                    <td>{{ $kyc->id }}</td>

                                    <td>
                                        {{ $kyc->user->name ?? 'N/A' }}
                                        <br>
                                        <small>{{ $kyc->user->email ?? '' }}</small>
                                    </td>

                                    <td>{{ $kyc->full_name }}</td>

                                    <td>{{ $kyc->country }}</td>

                                    <td>{{ ucfirst($kyc->id_type) }}</td>

                                    <td>
                                        @if ($kyc->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($kyc->status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($kyc->status == 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-secondary">
                                                {{ ucfirst($kyc->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>{{ $kyc->created_at->format('d M Y H:i') }}</td>

                                    <td>
                                        <a href="{{ route('admin.kyc.show', $kyc) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="text-center">
                                        No KYC requests found.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="card-footer">
                {{ $kycs->links() }}
            </div>

        </div>

    </div>
@endsection
