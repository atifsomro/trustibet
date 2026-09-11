@php
    $isEdit = isset($lottery);
@endphp

<div class="card">
    <div class="card-header">
        <strong>How it works</strong>
    </div>
    <div class="card-body">
        <ol class="pl-3 mb-0">
            <li class="mb-3">
                <strong>Draft</strong>
                <div class="text-muted">Saves the lottery without starting sales.</div>
            </li>
            <li class="mb-3">
                <strong>Selling</strong>
                <div class="text-muted">Starts the countdown immediately. Tickets can be purchased until it ends.</div>
            </li>
            <li class="mb-3">
                <strong>Automatic draw</strong>
                <div class="text-muted">When the countdown reaches zero, winners are drawn and a new round starts automatically.</div>
            </li>
            <li class="mb-0">
                <strong>Active</strong>
                <div class="text-muted">Leave checked to announce and pay winners. Uncheck to run a round without payouts.</div>
            </li>
        </ol>
    </div>
</div>

@if ($isEdit)
    <div class="card">
        <div class="card-header">
            <strong>Current round</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-borderless mb-0">
                <tr>
                    <th width="40%">Status</th>
                    <td>
                        {{ $lottery->status instanceof \App\Enums\LotteryStatus
                            ? $lottery->status->label()
                            : ucfirst((string) $lottery->status) }}
                    </td>
                </tr>
                <tr>
                    <th>Started</th>
                    <td>{{ $lottery->starts_at?->format('d M Y h:i A') ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Ends</th>
                    <td>{{ $lottery->ends_at?->format('d M Y h:i A') ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Tickets</th>
                    <td>{{ number_format($lottery->totalCurrentRoundTickets()) }}</td>
                </tr>
            </table>
        </div>
    </div>
@endif
