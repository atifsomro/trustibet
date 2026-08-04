@extends('admin.base')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">

            <div>

                <h3 class="mb-2">
                    KYC Verification Details
                </h3>

                @if ($kyc->status == 'pending')
                    <span class="badge badge-warning">
                        Pending Verification
                    </span>
                @elseif($kyc->status == 'approved')
                    <span class="badge badge-success">
                        Approved
                    </span>
                @else
                    <span class="badge badge-danger">
                        Rejected
                    </span>
                @endif

            </div>

            <div>

                <a href="{{ route('admin.kyc.index') }}" class="btn btn-secondary">

                    <i class="fa fa-arrow-left"></i>

                    Back

                </a>

                @if ($kyc->status == 'pending')
                    <form action="{{ route('admin.kyc.approve', $kyc) }}" method="POST" class="d-inline">

                        @csrf

                        <button type="submit" class="btn btn-success">

                            <i class="fa fa-check"></i>

                            Approve

                        </button>

                    </form>

                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">

                        <i class="fa fa-times"></i>

                        Reject

                    </button>
                @endif

            </div>

        </div>

        <div class="row">

            {{-- User Information --}}
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">👤 User Information</h5>
                    </div>

                    <div class="card-body">

                        <table class="table table-bordered mb-0">

                            <tr>
                                <th width="35%">User Name</th>
                                <td>{{ $kyc->user->name ?? 'N/A' }}</td>
                            </tr>

                            <tr>
                                <th>Email</th>
                                <td>{{ $kyc->user->email ?? 'N/A' }}</td>
                            </tr>

                            <tr>
                                <th>User ID</th>
                                <td>{{ $kyc->user_id }}</td>
                            </tr>

                        </table>

                    </div>
                </div>
            </div>

            {{-- KYC Information --}}
            <div class="col-lg-6 mb-4">
                <div class="card h-100">

                    <div class="card-header">
                        <h5 class="mb-0">📄 KYC Information</h5>
                    </div>

                    <div class="card-body">

                        <table class="table table-bordered mb-0">

                            <tr>
                                <th width="35%">Full Name</th>
                                <td>{{ $kyc->full_name }}</td>
                            </tr>

                            <tr>
                                <th>Date of Birth</th>
                                <td>{{ $kyc->date_of_birth->format('d M Y') }}</td>
                            </tr>

                            <tr>
                                <th>Country</th>
                                <td>{{ $kyc->country }}</td>
                            </tr>

                            <tr>
                                <th>ID Type</th>
                                <td>{{ ucfirst($kyc->id_type) }}</td>
                            </tr>

                            <tr>
                                <th>ID Number</th>
                                <td>{{ $kyc->id_number }}</td>
                            </tr>

                            <tr>
                                <th>Address</th>
                                <td>{{ $kyc->address }}</td>
                            </tr>

                            <tr>
                                <th>City</th>
                                <td>{{ $kyc->city }}</td>
                            </tr>

                        </table>

                    </div>

                </div>
            </div>

        </div>


        {{-- Documents --}}
        <div class="card mb-4">

            <div class="card-header">
                <h5 class="mb-0">🖼️ Uploaded Documents</h5>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4 text-center">

                        <h6>Front ID</h6>

                        <a href="{{ asset('storage/' . $kyc->front_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $kyc->front_image) }}" class="img-fluid rounded border"
                                style="height:220px; object-fit:cover;">
                        </a>

                    </div>

                    <div class="col-md-4 text-center">

                        <h6>Back ID</h6>

                        <a href="{{ asset('storage/' . $kyc->back_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $kyc->back_image) }}" class="img-fluid rounded border"
                                style="height:220px; object-fit:cover;">
                        </a>

                    </div>

                    <div class="col-md-4 text-center">

                        <h6>Selfie</h6>

                        <a href="{{ asset('storage/' . $kyc->selfie_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $kyc->selfie_image) }}" class="img-fluid rounded border"
                                style="height:220px; object-fit:cover;">
                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- Verification Information --}}
        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">📋 Verification Status</h5>
            </div>

            <div class="card-body">

                <table class="table table-bordered mb-0">

                    <tr>
                        <th width="20%">Status</th>
                        <td>

                            @if ($kyc->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($kyc->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @else
                                <span class="badge badge-danger">Rejected</span>
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <th>Submitted At</th>
                        <td>{{ $kyc->created_at->format('d M Y h:i A') }}</td>
                    </tr>

                    <tr>
                        <th>Verified At</th>
                        <td>{{ $kyc->verified_at ? $kyc->verified_at->format('d M Y h:i A') : '-' }}</td>
                    </tr>

                    <tr>
                        <th>Rejection Reason</th>
                        <td>{{ $kyc->rejection_reason ?? '-' }}</td>
                    </tr>

                </table>

            </div>

        </div>

    </div>
    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.kyc.reject', $kyc) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">
                            Reject KYC
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>
                                Rejection Reason
                            </label>
                            <textarea name="rejection_reason" class="form-control" rows="5" placeholder="Enter rejection reason..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times"></i>
                            Reject KYC
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
