@extends('admin.base')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Purchase & Draw History</h4>
            <span class="text-muted">Ticket purchases and draw execution records</span>
        </div>
        <a href="{{ route('admin.lotteries.index') }}" class="btn btn-secondary">
            Back to Lotteries
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.lotteries.history') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Lottery</label>
                    <select name="lottery_id" class="form-control">
                        <option value="">All lotteries</option>
                        @foreach ($lotteries as $option)
                            <option value="{{ $option->id }}" @selected(request('lottery_id') == $option->id)>
                                {{ $option->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">User (name, email, or ID)</label>
                    <input type="text" name="user" class="form-control" value="{{ request('user') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.lotteries.history') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ticket Purchases</h5>
            <span class="text-muted">{{ $tickets->total() }} ticket(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Lottery</th>
                        <th>User</th>
                        <th>Ticket</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Purchased</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>
                                <a href="{{ route('admin.lotteries.show', $ticket->lottery) }}">
                                    {{ $ticket->lottery?->title ?? '—' }}
                                </a>
                            </td>
                            <td>
                                <strong>{{ $ticket->user?->name ?? '—' }}</strong>
                                <br>
                                <small class="text-muted">{{ $ticket->user?->email }}</small>
                            </td>
                            <td><code>{{ $ticket->ticket_number }}</code></td>
                            <td>
                                {{ $ticket->lottery?->currency }}
                                {{ number_format((float) $ticket->price, 2) }}
                            </td>
                            <td>{{ ucfirst($ticket->status) }}</td>
                            <td>{{ $ticket->purchased_at?->format('d M Y h:i A') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No purchases match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tickets->hasPages())
            <div class="card-footer">{{ $tickets->links() }}</div>
        @endif
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Draw Execution Records</h5>
            <span class="text-muted">{{ $draws->total() }} draw(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Lottery</th>
                        <th>Executed At</th>
                        <th>Tickets</th>
                        <th>Internal Winners</th>
                        <th>Announced</th>
                        <th>Prize Tiers</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($draws as $draw)
                        <tr>
                            <td>
                                <a href="{{ route('admin.lotteries.show', $draw->lottery) }}">
                                    {{ $draw->lottery?->title ?? '—' }}
                                </a>
                                <br>
                                <small class="text-muted">Draw #{{ $draw->id }}</small>
                            </td>
                            <td>{{ $draw->completed_at?->format('d M Y h:i A') ?? '—' }}</td>
                            <td>{{ number_format($draw->total_tickets) }}</td>
                            <td>{{ number_format($draw->winners->count()) }} selected</td>
                            <td>
                                @if ($draw->winners_announced)
                                    <span class="badge bg-success">Announced & paid</span>
                                @else
                                    <span class="badge bg-secondary">Suppressed (inactive lottery)</span>
                                @endif
                            </td>
                            <td>
                                @forelse ($draw->winners as $winner)
                                    <div>
                                        {{ ucfirst($winner->prize_category) }}
                                        — {{ $winner->user?->name ?? '—' }}
                                        @unless ($draw->winners_announced)
                                            <small class="text-muted">(internal only)</small>
                                        @endunless
                                    </div>
                                @empty
                                    <span class="text-muted">None</span>
                                @endforelse
                            </td>
                            <td>
                                @if ($draw->lottery)
                                    <a
                                        href="{{ route('admin.lotteries.draws.show', [$draw->lottery, $draw]) }}"
                                        class="btn btn-sm btn-primary"
                                    >
                                        View
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No draws match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($draws->hasPages())
            <div class="card-footer">{{ $draws->links() }}</div>
        @endif
    </div>

</div>
@endsection
