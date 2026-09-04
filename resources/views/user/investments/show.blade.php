@extends('layouts.master')

@section('content')
    <section class="py-8">
        <div class="container">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <h2>{{ $investment->package_name }} — ROI History</h2>
                    <p class="mt-2 opacity-70">
                        Daily ROI must be claimed the same day or it expires.
                    </p>
                </div>
                <a href="{{ route('investments.mine') }}" class="btn-primary">Back</a>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4 mb-6">
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-4">
                    <p class="opacity-70 text-sm">Price</p>
                    <p class="mt-1 text-xl">${{ number_format((float) $investment->price, 2) }}</p>
                </div>
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-4">
                    <p class="opacity-70 text-sm">Daily ROI</p>
                    <p class="mt-1 text-xl">${{ number_format((float) $investment->daily_roi, 2) }}</p>
                </div>
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-4">
                    <p class="opacity-70 text-sm">Progress</p>
                    <p class="mt-1 text-xl">{{ $investment->daysElapsed() }} / {{ $investment->total_days }}</p>
                </div>
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-4">
                    <p class="opacity-70 text-sm">Status</p>
                    <p class="mt-1 text-xl">{{ $investment->status->label() }}</p>
                </div>
            </div>

            <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8 overflow-x-auto">
                <table class="w-full min-w-[700px]">
                    <thead>
                        <tr class="border-b border-brand-border">
                            <th class="py-3 text-left">Date</th>
                            <th class="py-3 text-left">Amount</th>
                            <th class="py-3 text-left">Status</th>
                            <th class="py-3 text-left">Claimed</th>
                            <th class="py-3 text-left">Expired</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roiLogs as $log)
                            <tr class="border-b border-brand-border">
                                <td class="py-4">{{ $log->roi_date->format('d M Y') }}</td>
                                <td class="py-4">${{ number_format((float) $log->amount, 2) }}</td>
                                <td class="py-4">
                                    @if ($log->status === \App\Enums\InvestmentRoiStatus::CLAIMED)
                                        <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">Claimed</span>
                                    @elseif ($log->status === \App\Enums\InvestmentRoiStatus::PENDING)
                                        <span class="rounded-full bg-yellow-500/20 px-3 py-1 text-yellow-500">Pending</span>
                                    @else
                                        <span class="rounded-full bg-red-500/20 px-3 py-1 text-red-400">Expired</span>
                                    @endif
                                </td>
                                <td class="py-4">{{ $log->claimed_at?->format('d M Y H:i') ?? '—' }}</td>
                                <td class="py-4">{{ $log->expired_at?->format('d M Y H:i') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center opacity-70">No ROI history yet. First ROI is available the day after purchase.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($roiLogs->hasPages())
                    <div class="mt-6">{{ $roiLogs->links() }}</div>
                @endif
            </div>
        </div>
    </section>
@endsection
