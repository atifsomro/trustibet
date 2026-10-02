@extends('admin.base')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Users</h4>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('admin.users.index') }}"
                  class="row g-3">
                <div class="col-md-10">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search name, email, username..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-dark w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Wallet</th>
                        <th>Authenticator</th>
                        <th>Registered</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->username ?? '-' }}</td>
                            <td>
                                @if($user->wallet)
                                    <a href="{{ route('admin.wallets.show', $user->wallet) }}"
                                       class="btn btn-sm btn-primary">
                                        View Wallet
                                    </a>
                                @else
                                    <span class="badge bg-secondary">No Wallet</span>
                                @endif
                            </td>
                            <td>
                                @if($user->google2fa_enabled)
                                    @if($user->google2fa_confirmed_at)
                                        <span class="badge bg-success mb-1 d-inline-block">On</span>
                                    @else
                                        <span class="badge bg-warning text-dark mb-1 d-inline-block">Pending setup</span>
                                    @endif
                                    <form method="POST"
                                          action="{{ route('admin.users.google2fa.disable', $user) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Disable Google Authenticator for this user?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Disable
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary mb-1 d-inline-block">Off</span>
                                    <form method="POST"
                                          action="{{ route('admin.users.google2fa.enable', $user) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Enable Google Authenticator for this user? They will need to scan a QR code on next login.');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            Enable
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No users found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>

</div>

@endsection
