@extends('admin.base')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Game Plays</h4>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.game-plays.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control"
                            placeholder="UUID / user..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="game_id" class="form-control">
                            <option value="">All Games</option>
                            @foreach ($games as $g)
                                <option value="{{ $g->id }}" @selected((string) request('game_id') === (string) $g->id)>
                                    {{ $g->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="won" @selected(request('status') === 'won')>Won</option>
                            <option value="lost" @selected(request('status') === 'lost')>Lost</option>
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
                                <th>ID</th>
                                <th>User</th>
                                <th>Game</th>
                                <th>Package</th>
                                <th>Fee</th>
                                <th>Prize</th>
                                <th>Status</th>
                                <th>When</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($plays as $play)
                                <tr>
                                    <td title="{{ $play->uuid }}">#{{ $play->id }}</td>
                                    <td>{{ $play->user?->email ?? '—' }}</td>
                                    <td>{{ $play->game?->title ?? '—' }}</td>
                                    <td>{{ $play->package?->name ?? '—' }}</td>
                                    <td>${{ number_format((float) $play->fee_amount, 2) }}</td>
                                    <td>${{ number_format((float) $play->prize_amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $play->status->value === 'won' ? 'success' : ($play->status->value === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ $play->status->label() }}
                                        </span>
                                    </td>
                                    <td>{{ $play->created_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No plays yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($plays->hasPages())
                <div class="card-footer">{{ $plays->links() }}</div>
            @endif
        </div>
    </div>
@endsection
