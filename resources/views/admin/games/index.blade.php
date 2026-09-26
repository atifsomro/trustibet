@extends('admin.base')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Games</h4>
            <a href="{{ route('admin.games.create') }}" class="btn btn-success">Add Game</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.games.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-control">
                            <option value="">All Types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(request('type') === $type->value)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
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
                                <th>Title</th>
                                <th>Type</th>
                                <th>Slug</th>
                                <th>Packages</th>
                                <th>Plays</th>
                                <th>Featured</th>
                                <th>Status</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($games as $game)
                                <tr>
                                    <td>{{ $game->id }}</td>
                                    <td>{{ $game->title }}</td>
                                    <td>{{ $game->type->label() }}</td>
                                    <td>{{ $game->slug }}</td>
                                    <td>{{ $game->packages_count }}</td>
                                    <td>{{ $game->plays_count }}</td>
                                    <td>
                                        @if ($game->is_featured)
                                            <span class="badge badge-warning">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($game->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.games.edit', $game) }}"
                                            class="btn btn-sm btn-info text-white">Edit</a>
                                        <form action="{{ route('admin.games.destroy', $game) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this game and related packages/prizes?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">No games found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($games->hasPages())
                <div class="card-footer">{{ $games->links() }}</div>
            @endif
        </div>
    </div>
@endsection
