@extends('admin.base')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Users</h4>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('admin.users.index') }}"
                  class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search name, email, username..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Account status</label>
                    <select name="status" class="form-control">
                        <option value="">All statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
                        <option value="blocked" @selected(request('status') === 'blocked')>Permanently blocked</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button class="btn btn-dark flex-grow-1">Filter</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Wallet</th>
                        <th>Authenticator</th>
                        <th>Registered</th>
                        <th>Actions</th>
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
                                @if($user->isPermanentlyBlocked())
                                    <span class="badge bg-danger">Permanently blocked</span>
                                @elseif($user->isSuspended())
                                    <span class="badge bg-warning text-dark">Suspended</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
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
                                    @if($user->google2fa_confirmed_at && $user->google2fa_managed_by_admin)
                                        <span class="badge bg-danger mb-1 d-inline-block">On (admin)</span>
                                    @elseif($user->google2fa_confirmed_at)
                                        <span class="badge bg-success mb-1 d-inline-block">On (user)</span>
                                    @elseif($user->google2fa_managed_by_admin)
                                        <span class="badge bg-warning text-dark mb-1 d-inline-block">Admin scan pending</span>
                                        <a href="{{ route('admin.users.google2fa.setup', $user) }}"
                                           class="btn btn-sm btn-outline-primary mb-1">
                                            Open QR
                                        </a>
                                    @else
                                        <span class="badge bg-warning text-dark mb-1 d-inline-block">Pending setup</span>
                                    @endif
                                    <form method="POST"
                                          action="{{ route('admin.users.google2fa.disable', $user) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Disable Google Authenticator for this user? They will sign in without a code.');">
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
                                          onsubmit="return confirm('Enable admin authenticator for this user? You will scan the QR. After confirm, they must enter a code from your authenticator app to sign in.');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            Enable
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-nowrap">
                                @if($user->isPermanentlyBlocked())
                                    <form method="POST"
                                          action="{{ route('admin.users.unblock', $user) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Unblock this account and set it back to Active?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            Unblock
                                        </button>
                                    </form>
                                @elseif($user->isSuspended())
                                    <form method="POST"
                                          action="{{ route('admin.users.unsuspend', $user) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Unsuspend this account?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            Unsuspend
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('admin.users.block', $user) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Permanently block this account? The user will not be able to log in.');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Block
                                        </button>
                                    </form>
                                @else
                                    <form method="POST"
                                          action="{{ route('admin.users.suspend', $user) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Suspend this account? The user will not be able to log in until unsuspended.');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning">
                                            Suspend
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('admin.users.block', $user) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Permanently block this account? The user will not be able to log in.');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Block
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No users found.</td>
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
