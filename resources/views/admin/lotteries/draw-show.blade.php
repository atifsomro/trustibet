@extends('admin.base')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">{{ $lottery->title }} — Draw #{{ $draw->id }}</h4>
            <span class="text-muted">Lottery Draw Results</span>
        </div>

        <a
            href="{{ route('admin.lotteries.show', $lottery) }}"
            class="btn btn-secondary"
        >
            <i class="fas fa-arrow-left"></i> Draw History
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Draw Status</small>
                    <strong>{{ ucfirst($draw->status) }}</strong>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Tickets in Draw</small>
                    <strong>{{ number_format($draw->total_tickets) }}</strong>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Winners</small>
                    <strong>{{ number_format($draw->total_winners) }} / 5</strong>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Completed At</small>
                    <strong>{{ $draw->completed_at?->format('d M Y h:i A') ?? '—' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Draw Sales Period</h5>
        </div>
        <div class="card-body">
            <strong>{{ $draw->sales_start_at?->format('d M Y h:i A') ?? 'Any start' }}</strong>
            <span class="mx-2">→</span>
            <strong>{{ $draw->sales_end_at?->format('d M Y h:i A') ?? '—' }}</strong>
        </div>
    </div>

    @php
        $winnerByCategory = $draw->winners->keyBy('prize_category');

        $prizes = collect($draw->prize_snapshot ?? []);

        /*
         * Fallback for draws created before the round snapshot migration.
     */
        if ($prizes->isEmpty()) {
        $prizes = collect([
            ['category' => 'first', 'amount' => $lottery->first_prize],
            ['category' => 'second', 'amount' => $lottery->second_prize],
            ['category' => 'third', 'amount' => $lottery->third_prize],
            ['category' => 'fourth', 'amount' => $lottery->fourth_prize],
            ['category' => 'fifth', 'amount' => $lottery->fifth_prize],
        ]);
    }

    $prizes = $prizes->mapWithKeys(function ($prize) {
        return [
            $prize['category'] => [
                'label' => match ($prize['category']) {
                    'first' => '1st Prize',
                    'second' => '2nd Prize',
                    'third' => '3rd Prize',
                    'fourth' => '4th Prize',
                    'fifth' => '5th Prize',
                    default => ucfirst($prize['category']),
                },
                'amount' => (float) ($prize['amount'] ?? 0),
            ],
        ];
    });
    @endphp

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Winning Results</h5>
            <span class="text-muted">{{ $draw->total_winners }} winner(s)</span>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th width="120">Position</th>
                        <th>Winner</th>
                        <th>Ticket Number</th>
                        <th>Prize</th>
                        <th>Payout</th>
                        <th>Paid At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($prizes as $category => $prize)
                        @php($winner = $winnerByCategory->get($category))

                        <tr>
                            <td><strong>{{ $prize['label'] }}</strong></td>

                            <td>
                                @if ($winner)
                                    <strong>{{ $winner->user?->name ?? 'Unknown User' }}</strong>
                                    @if ($winner->user?->username)
                                        <br>
                                        <small class="text-muted">
                                            {{ '@' . $winner->user->username }}
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted">No winner</span>
                                @endif
                            </td>

                            <td>
                                @if ($winner?->ticket)
                                    <span class="badge bg-light text-dark border font-monospace">
                                        {{ $winner->ticket->ticket_number }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                {{ $lottery->currency }}
                                {{ number_format((float) $prize['amount'], 2) }}
                            </td>

                            <td>
                                @if ($winner)
                                    @if ($winner->payout_status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif ($winner->payout_status === 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            {{ ucfirst($winner->payout_status) }}
                                        </span>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                {{ $winner?->paid_at?->format('d M Y h:i A') ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
