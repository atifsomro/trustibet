@php
    $user = auth()->user();
    $walletService = app(\App\Services\Wallet\WalletService::class);
    $wallet = $walletService->wallet($user);

    $activeCount = \App\Models\UserInvestment::query()->where('user_id', $user->id)->active()->count();
    $completedCount = \App\Models\UserInvestment::query()->where('user_id', $user->id)->completed()->count();
    $totalInvested = (float) \App\Models\UserInvestment::query()
        ->where('user_id', $user->id)
        ->whereIn('status', [
            \App\Enums\InvestmentStatus::ACTIVE,
            \App\Enums\InvestmentStatus::COMPLETED,
        ])
        ->sum('price');
    $claimableAmount = (float) \App\Models\InvestmentRoiLog::query()
        ->where('user_id', $user->id)
        ->claimableToday()
        ->sum('amount');

    $accountInvestments = \App\Models\UserInvestment::query()
        ->where('user_id', $user->id)
        ->latest()
        ->limit(10)
        ->get();
@endphp

<div class="investment">
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col items-center justify-center gap-2">
                <i class="fa-solid fa-layer-group text-xl sm:text-4xl text-brand-primary"></i>
                <p class="opacity-70">Active Plans</p>
                <h3 class="mt-2">{{ $activeCount }}</h3>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col items-center justify-center gap-2">
                <i class="fa-solid fa-wallet text-xl sm:text-4xl text-brand-primary"></i>
                <p class="opacity-70">Total Investment</p>
                <h3 class="mt-2 text-brand-primary">${{ number_format($totalInvested, 2) }}</h3>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col items-center justify-center gap-2">
                <i class="fa-solid fa-circle-check text-xl sm:text-4xl text-green-500"></i>
                <p class="opacity-70">Completed Plans</p>
                <h3 class="mt-2 text-green-500">{{ $completedCount }}</h3>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col items-center justify-center gap-2">
                <i class="fa-solid fa-coins text-xl sm:text-4xl text-yellow-500"></i>
                <p class="opacity-70">ROI Balance</p>
                <h3 class="mt-2 text-yellow-500">${{ number_format((float) $wallet->roi_balance, 2) }}</h3>
            </div>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('investment') }}" class="btn-primary">Browse Packages</a>
        <a href="{{ route('investments.mine') }}" class="btn-orange">Manage Investments</a>
        @if ($claimableAmount > 0)
            <form method="POST" action="{{ route('investments.claim') }}">
                @csrf
                <button type="submit" class="btn-primary">
                    Claim All ROI (${{ number_format($claimableAmount, 2) }})
                </button>
            </form>
        @endif
    </div>

    <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
        <div class="mb-8">
            <h3>Investment Plans</h3>
            <p class="mt-2 opacity-70">Your recent purchased plans.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[950px] text-[12px] md:text-base lg:text-xl">
                <thead>
                    <tr class="border-b border-brand-border">
                        <th class="py-2 sm:py-4 text-left">Plan</th>
                        <th class="py-2 sm:py-4 text-left">Amount</th>
                        <th class="py-2 sm:py-4 text-left">Duration</th>
                        <th class="py-2 sm:py-4 text-left">Start Date</th>
                        <th class="py-2 sm:py-4 text-left">End Date</th>
                        <th class="py-2 sm:py-4 text-left">Status</th>
                        <th class="py-2 sm:py-4 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accountInvestments as $investment)
                        <tr class="border-b border-brand-border">
                            <td class="py-3 sm:py-5">{{ $investment->package_name }}</td>
                            <td class="py-3 sm:py-5">${{ number_format((float) $investment->price, 2) }}</td>
                            <td class="py-3 sm:py-5">{{ $investment->total_days }} Days</td>
                            <td class="py-3 sm:py-5">{{ $investment->starts_at->format('d M Y') }}</td>
                            <td class="py-3 sm:py-5">{{ $investment->ends_at->format('d M Y') }}</td>
                            <td class="py-3 sm:py-5">
                                @if ($investment->status === \App\Enums\InvestmentStatus::ACTIVE)
                                    <span class="rounded-full bg-green-500/20 px-3 py-1 text-green-500">Active</span>
                                @elseif ($investment->status === \App\Enums\InvestmentStatus::COMPLETED)
                                    <span class="rounded-full bg-gray-500/20 px-3 py-1 text-gray-400">Completed</span>
                                @else
                                    <span class="rounded-full bg-red-500/20 px-3 py-1 text-red-400">Cancelled</span>
                                @endif
                            </td>
                            <td class="py-3 sm:py-5">
                                <a href="{{ route('investments.show', $investment) }}"
                                    class="btn-primary text-[12px] sm:text-sm px-3 sm:px-5 py-2">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center opacity-70">No investments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
