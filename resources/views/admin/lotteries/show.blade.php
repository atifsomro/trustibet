@extends('admin.base')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">{{ $lottery->title }}</h4>
            <span class="text-muted">Lottery Details & Draw History</span>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.lotteries.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>

            <a href="{{ route('admin.lotteries.edit', $lottery) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit / New Round
            </a>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Automatic draws:</strong>
        When the countdown reaches zero, winners are drawn and a new round starts immediately.
        Inactive lotteries still sell tickets and still draw, but winners are not announced or paid.
        Cancel a lottery to stop the loop.
    </div>

    {{-- Current lottery summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Status</small>
                    <strong>
                        {{ $lottery->status instanceof \App\Enums\LotteryStatus
                            ? $lottery->status->label()
                            : ucfirst($lottery->status) }}
                    </strong>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Current Round Tickets</small>
                    <strong>{{ number_format($lottery->totalCurrentRoundTickets()) }}</strong>
                    <small class="d-block text-muted">
                        Lifetime: {{ number_format($lottery->totalTicketsSold()) }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Countdown</small>
                    <strong>{{ $lottery->ends_at?->format('d M Y h:i A') ?? '—' }}</strong>
                    <small class="d-block text-muted">
                        Started {{ $lottery->starts_at?->format('d M Y h:i A') ?? '—' }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Prize Pool / Round</small>
                    <strong>
                        {{ $lottery->currency }}
                        {{ number_format((float) $lottery->totalPrizeAmount(), 2) }}
                    </strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Draw history --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Draw History</h5>
            <span class="text-muted">{{ $draws->total() }} draw(s)</span>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sales Period</th>
                        <th>Status</th>
                        <th>Tickets</th>
                        <th>Winners</th>
                        <th>Drawn At</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($draws as $draw)
                        <tr>
                            <td>
                                <strong>{{ $draw->dailyRoundLabel() }}</strong>
                                <br>
                                <small class="text-muted">Draw #{{ $draw->id }}</small>
                            </td>

                            <td>
                                {{ $draw->sales_start_at?->format('d M Y h:i A') ?? 'Any start' }}
                                <br>
                                <small class="text-muted">
                                    to {{ $draw->sales_end_at?->format('d M Y h:i A') ?? '—' }}
                                </small>
                            </td>

                            <td>
                                @php
                                    $badge = match ($draw->status) {
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        'running' => 'warning',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">
                                    {{ ucfirst($draw->status) }}
                                </span>
                                @if ($draw->status === 'completed' && !$draw->winners_announced)
                                    <span class="badge bg-secondary">Winners suppressed</span>
                                @endif
                            </td>

                            <td>{{ number_format($draw->total_tickets) }}</td>

                            <td>
                                {{ number_format($draw->total_winners) }} / 5
                            </td>

                            <td>
                                {{ $draw->completed_at?->format('d M Y h:i A') ?? '—' }}
                            </td>

                            <td>
                                @if ($draw->status === 'completed')
                                    <a
                                        href="{{ route('admin.lotteries.draws.show', [$lottery, $draw]) }}"
                                        class="btn btn-sm btn-primary"
                                    >
                                        <i class="fas fa-trophy"></i> Results
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-trophy fa-2x text-muted mb-2"></i>
                                <p class="mb-0">No draws have been completed yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($draws->hasPages())
            <div class="card-footer">
                {{ $draws->links() }}
            </div>
        @endif
    </div>

    {{-- Ticket purchases --}}
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ticket Purchases</h5>
            <span class="text-muted">{{ $tickets->total() }} ticket(s)</span>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Ticket Number</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Purchased</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>
                                <strong>{{ $ticket->user?->name ?? '—' }}</strong>
                                <br>
                                <small class="text-muted">{{ $ticket->user?->email }}</small>
                            </td>
                            <td><code>{{ $ticket->ticket_number }}</code></td>
                            <td>
                                {{ $lottery->currency }}
                                {{ number_format((float) $ticket->price, 2) }}
                            </td>
                            <td>{{ ucfirst($ticket->status) }}</td>
                            <td>{{ $ticket->purchased_at?->format('d M Y h:i A') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No tickets have been purchased yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tickets->hasPages())
            <div class="card-footer">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
