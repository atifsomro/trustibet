@extends('admin.base')

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Edit Game — {{ $game->title }}</h4>
                <a href="{{ route('admin.games.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.games.update', $game) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.games.form')
                    <button type="submit" class="btn btn-success">Update Game</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Packages (fees)</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.games.packages.store', $game) }}" class="row g-2 mb-4">
                    @csrf
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control" placeholder="Package name" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" min="0" name="fee" class="form-control"
                            placeholder="Fee" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" min="0" name="meta[multiplier]" class="form-control"
                            placeholder="Multiplier">
                    </div>
                    <div class="col-md-2">
                        <input type="number" min="1" name="meta[chances]" class="form-control"
                            placeholder="Chances">
                    </div>
                    <div class="col-md-1">
                        <input type="number" min="0" name="sort_order" class="form-control" value="0"
                            placeholder="Sort">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Add Package</button>
                    </div>
                </form>

                @forelse ($game->packages as $package)
                    <div class="border rounded p-3 mb-4">
                        <form method="POST"
                            action="{{ route('admin.games.packages.update', [$game, $package]) }}"
                            class="row g-2 align-items-end">
                            @csrf
                            @method('PUT')
                            <div class="col-md-3">
                                <label class="small">Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ $package->name }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="small">Fee</label>
                                <input type="number" step="0.01" min="0" name="fee" class="form-control"
                                    value="{{ $package->fee }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="small">Multiplier</label>
                                <input type="number" step="0.01" min="0" name="meta[multiplier]"
                                    class="form-control" value="{{ $package->metaValue('multiplier') }}">
                            </div>
                            <div class="col-md-1">
                                <label class="small">Chances</label>
                                <input type="number" min="1" name="meta[chances]" class="form-control"
                                    value="{{ $package->metaValue('chances') }}">
                            </div>
                            <div class="col-md-1">
                                <label class="small">Sort</label>
                                <input type="number" min="0" name="sort_order" class="form-control"
                                    value="{{ $package->sort_order }}">
                            </div>
                            <div class="col-md-1">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                        id="pkg_active_{{ $package->id }}"
                                        {{ $package->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pkg_active_{{ $package->id }}">Active</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-sm btn-info text-white">Save</button>
                            </div>
                        </form>
                        <form method="POST"
                            action="{{ route('admin.games.packages.destroy', [$game, $package]) }}"
                            class="d-inline"
                            onsubmit="return confirm('Delete this package and its prizes?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger mt-2">Delete Package</button>
                        </form>

                        <hr>
                        <h6>Prizes / Odds for {{ $package->name }}</h6>
                        <form method="POST"
                            action="{{ route('admin.games.packages.prizes.store', [$game, $package]) }}"
                            class="row g-2 mb-3">
                            @csrf
                            <div class="col-md-3">
                                <input type="text" name="label" class="form-control" placeholder="Label"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" step="0.01" min="0" name="prize_amount"
                                    class="form-control" placeholder="Amount" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" min="0" name="weight" class="form-control"
                                    placeholder="Weight" value="1" required>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="meta[color]" class="form-control"
                                    placeholder="Color (optional)">
                            </div>
                            <div class="col-md-2">
                                <input type="number" min="0" name="meta[segment]" class="form-control"
                                    placeholder="Segment #">
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-primary w-100">Add</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Label</th>
                                        <th>Amount</th>
                                        <th>Weight</th>
                                        <th>Color</th>
                                        <th>Segment</th>
                                        <th>Active</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($package->prizes as $prize)
                                        <tr>
                                            <form method="POST"
                                                action="{{ route('admin.games.packages.prizes.update', [$game, $package, $prize]) }}">
                                                @csrf
                                                @method('PUT')
                                                <td>
                                                    <input type="text" name="label" class="form-control form-control-sm"
                                                        value="{{ $prize->label }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" name="prize_amount"
                                                        class="form-control form-control-sm"
                                                        value="{{ $prize->prize_amount }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" min="0" name="weight"
                                                        class="form-control form-control-sm"
                                                        value="{{ $prize->weight }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="meta[color]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $prize->metaValue('color') }}">
                                                </td>
                                                <td>
                                                    <input type="number" min="0" name="meta[segment]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $prize->metaValue('segment') }}">
                                                    <input type="hidden" name="sort_order"
                                                        value="{{ $prize->sort_order }}">
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="is_active" value="1"
                                                        {{ $prize->is_active ? 'checked' : '' }}>
                                                </td>
                                                <td class="text-nowrap">
                                                    <button class="btn btn-xs btn-info text-white btn-sm">Save</button>
                                            </form>
                                            <form method="POST"
                                                action="{{ route('admin.games.packages.prizes.destroy', [$game, $package, $prize]) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('Delete prize?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-xs btn-danger btn-sm">Del</button>
                                            </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No prizes yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No packages yet. Add one above.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
