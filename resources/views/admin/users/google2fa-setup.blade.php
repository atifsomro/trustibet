@extends('admin.base')

@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Admin Authenticator Setup</h4>
            <p class="text-muted mb-0">
                Scan this QR on behalf of
                <strong>{{ $user->name }}</strong>
                ({{ $user->email }}).
                After you confirm, this user will not be able to log in until you disable authenticator.
            </p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            Back to Users
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body text-center">
                    <div class="d-inline-block bg-white p-3 rounded mb-3">
                        {!! $qrSvg !!}
                    </div>
                    <p class="small text-muted mb-4">
                        Or enter this key manually:<br>
                        <strong class="user-select-all" style="letter-spacing: 0.12em;">{{ $secret }}</strong>
                    </p>

                    <form method="POST" action="{{ route('admin.users.google2fa.setup.confirm', $user) }}" class="text-start mx-auto" style="max-width: 320px;">
                        @csrf
                        <label for="code" class="form-label">6-digit code from your authenticator app</label>
                        <input type="text"
                               name="code"
                               id="code"
                               class="form-control text-center"
                               style="letter-spacing: 0.2em;"
                               inputmode="numeric"
                               autocomplete="one-time-code"
                               maxlength="6"
                               placeholder="000000"
                               required
                               autofocus>
                        <button type="submit" class="btn btn-dark w-100 mt-3">
                            Confirm &amp; Lock User Login
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.users.google2fa.disable', $user) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-link text-danger"
                                onclick="return confirm('Cancel setup and leave authenticator off for this user?');">
                            Cancel setup
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
