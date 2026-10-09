@php
    $user = auth()->user();
    $wallet = app(\App\Services\Wallet\WalletManager::class)->getOrCreate($user);
    $txRef = app(\App\Services\Wallet\TransactionReferenceGenerator::class);

    $filters = [
        'q' => trim((string) request()->input('tx_q', '')),
        'type' => (string) request()->input('tx_type', ''),
        'status' => (string) request()->input('tx_status', ''),
        'date' => (string) request()->input('tx_date', ''),
    ];

    $allowedTypes = ['deposit', 'withdraw', 'bonus', 'entry_fee'];
    if ($filters['type'] !== '' && ! in_array($filters['type'], $allowedTypes, true)) {
        $filters['type'] = '';
    }

    $allowedStatuses = ['completed', 'pending', 'failed', 'credited'];
    if ($filters['status'] !== '' && ! in_array($filters['status'], $allowedStatuses, true)) {
        $filters['status'] = '';
    }

    $typeGroups = [
        'deposit' => [\App\Enums\WalletTransactionType::DEPOSIT],
        'withdraw' => [
            \App\Enums\WalletTransactionType::WITHDRAW_REQUEST,
            \App\Enums\WalletTransactionType::WITHDRAW_APPROVED,
            \App\Enums\WalletTransactionType::WITHDRAW_REJECTED,
            \App\Enums\WalletTransactionType::WITHDRAW_CANCELLED,
        ],
        'bonus' => [
            \App\Enums\WalletTransactionType::BONUS_GRANTED,
            \App\Enums\WalletTransactionType::BONUS_EXPIRED,
            \App\Enums\WalletTransactionType::BONUS_VOIDED,
        ],
        'entry_fee' => [
            \App\Enums\WalletTransactionType::GAME_BET,
            \App\Enums\WalletTransactionType::LOTTERY_TICKET_PURCHASE,
        ],
    ];

    $baseQuery = \App\Models\WalletTransaction::query()
        ->where('wallet_id', $wallet->id);

    $totalTransactions = (clone $baseQuery)->count();
    $totalDeposits = (float) (clone $baseQuery)
        ->where('type', \App\Enums\WalletTransactionType::DEPOSIT)
        ->sum('amount');
    $totalWithdrawals = abs((float) (clone $baseQuery)
        ->whereIn('type', [
            \App\Enums\WalletTransactionType::WITHDRAW_REQUEST,
            \App\Enums\WalletTransactionType::WITHDRAW_APPROVED,
        ])
        ->where('amount', '<', 0)
        ->sum('amount'));

    $pendingDeposits = \App\Models\Deposit::query()
        ->where('user_id', $user->id)
        ->pending()
        ->count();
    $pendingWithdrawals = \App\Models\WithdrawalRequest::query()
        ->where('user_id', $user->id)
        ->pending()
        ->count();
    $pendingCount = $pendingDeposits + $pendingWithdrawals;

    $resolveUiStatus = function ($transaction) {
        return match ($transaction->type) {
            \App\Enums\WalletTransactionType::BONUS_GRANTED,
            \App\Enums\WalletTransactionType::GAME_WIN,
            \App\Enums\WalletTransactionType::LOTTERY_PRIZE,
            \App\Enums\WalletTransactionType::INVESTMENT_ROI_CLAIM,
            \App\Enums\WalletTransactionType::INVESTMENT_PRINCIPAL_RETURN => 'credited',
            \App\Enums\WalletTransactionType::WITHDRAW_REJECTED,
            \App\Enums\WalletTransactionType::BONUS_VOIDED,
            \App\Enums\WalletTransactionType::BONUS_EXPIRED => 'failed',
            default => 'completed',
        };
    };

    $resolveMethod = function ($transaction) {
        $meta = $transaction->meta ?? [];
        $reference = $transaction->reference;

        if ($transaction->type === \App\Enums\WalletTransactionType::DEPOSIT) {
            return $reference?->bankAccount?->bank_name
                ?? data_get($meta, 'reference_number')
                ?? 'Deposit';
        }

        if (in_array($transaction->type, [
            \App\Enums\WalletTransactionType::WITHDRAW_REQUEST,
            \App\Enums\WalletTransactionType::WITHDRAW_APPROVED,
            \App\Enums\WalletTransactionType::WITHDRAW_REJECTED,
            \App\Enums\WalletTransactionType::WITHDRAW_CANCELLED,
        ], true)) {
            $method = $reference?->payment_method
                ?? data_get($meta, 'payment_method');

            return $method ? ucwords(str_replace('_', ' ', (string) $method)) : 'Withdrawal';
        }

        if ($transaction->type === \App\Enums\WalletTransactionType::BONUS_GRANTED) {
            return data_get($meta, 'reason')
                ?? data_get($meta, 'source')
                ?? 'Bonus';
        }

        if ($transaction->type === \App\Enums\WalletTransactionType::GAME_BET) {
            return data_get($meta, 'game')
                ? ucwords(str_replace(['-', '_'], ' ', (string) data_get($meta, 'game')))
                : 'Game Entry';
        }

        if ($transaction->type === \App\Enums\WalletTransactionType::LOTTERY_TICKET_PURCHASE) {
            return 'Lottery';
        }

        return $transaction->type?->label() ?? ucfirst((string) $transaction->balance_type?->value);
    };

    $transactionsQuery = (clone $baseQuery)
        ->with([
            'bonus',
            'reference' => function ($morphTo) {
                $morphTo->morphWith([
                    \App\Models\Deposit::class => ['bankAccount'],
                ]);
            },
        ]);

    if ($filters['q'] !== '') {
        $resolved = $txRef->resolveSearch($filters['q']);

        $transactionsQuery->where(function ($query) use ($filters, $resolved) {
            if ($resolved['uuid']) {
                $query->where('uuid', 'like', '%' . $resolved['uuid'] . '%');
            }

            if ($resolved['id']) {
                $query->orWhere('id', $resolved['id']);
            }

            if (! $resolved['uuid'] && ! $resolved['id']) {
                $query->where('uuid', 'like', '%' . $filters['q'] . '%');
            }
        });
    }

    if ($filters['type'] !== '' && isset($typeGroups[$filters['type']])) {
        $transactionsQuery->whereIn('type', $typeGroups[$filters['type']]);
    }

    if ($filters['date'] !== '') {
        $transactionsQuery->whereDate('created_at', $filters['date']);
    }

    if ($filters['status'] === 'pending') {
        // Ledger rows are settled; pending lives on deposit/withdrawal requests.
        $transactionsQuery->whereRaw('1 = 0');
    } elseif ($filters['status'] !== '') {
        $statusTypes = match ($filters['status']) {
            'credited' => [
                \App\Enums\WalletTransactionType::BONUS_GRANTED,
                \App\Enums\WalletTransactionType::GAME_WIN,
                \App\Enums\WalletTransactionType::LOTTERY_PRIZE,
                \App\Enums\WalletTransactionType::INVESTMENT_ROI_CLAIM,
                \App\Enums\WalletTransactionType::INVESTMENT_PRINCIPAL_RETURN,
            ],
            'failed' => [
                \App\Enums\WalletTransactionType::WITHDRAW_REJECTED,
                \App\Enums\WalletTransactionType::BONUS_VOIDED,
                \App\Enums\WalletTransactionType::BONUS_EXPIRED,
            ],
            'completed' => null,
            default => null,
        };

        if ($filters['status'] === 'completed') {
            $transactionsQuery->whereNotIn('type', [
                \App\Enums\WalletTransactionType::BONUS_GRANTED,
                \App\Enums\WalletTransactionType::GAME_WIN,
                \App\Enums\WalletTransactionType::LOTTERY_PRIZE,
                \App\Enums\WalletTransactionType::INVESTMENT_ROI_CLAIM,
                \App\Enums\WalletTransactionType::INVESTMENT_PRINCIPAL_RETURN,
                \App\Enums\WalletTransactionType::WITHDRAW_REJECTED,
                \App\Enums\WalletTransactionType::BONUS_VOIDED,
                \App\Enums\WalletTransactionType::BONUS_EXPIRED,
            ]);
        } elseif ($statusTypes) {
            $transactionsQuery->whereIn('type', $statusTypes);
        }
    }

    $ledgerTransactions = $transactionsQuery
        ->latest('created_at')
        ->latest('id')
        ->paginate(10, ['*'], 'transactions_page')
        ->withQueryString()
        ->fragment('transactions');

    $pendingRows = collect();
    if ($filters['status'] === '' || $filters['status'] === 'pending') {
        if ($filters['type'] === '' || $filters['type'] === 'deposit') {
            $pendingDepositRows = \App\Models\Deposit::query()
                ->with('bankAccount')
                ->where('user_id', $user->id)
                ->pending()
                ->when($filters['q'] !== '', function ($query) use ($filters) {
                    $query->where(function ($inner) use ($filters) {
                        $inner->where('reference_number', 'like', '%' . $filters['q'] . '%');
                        if (ctype_digit($filters['q'])) {
                            $inner->orWhere('id', (int) $filters['q']);
                        }
                    });
                })
                ->when($filters['date'] !== '', fn ($query) => $query->whereDate('created_at', $filters['date']))
                ->latest()
                ->get()
                ->map(function ($deposit) use ($txRef) {
                    return (object) [
                        'id' => 'deposit-' . $deposit->id,
                        'tx_id' => $txRef->forEntity('DEP', (int) $deposit->id, $deposit->created_at),
                        'type_label' => 'Deposit',
                        'amount' => (float) $deposit->amount,
                        'amount_label' => '+$' . number_format((float) $deposit->amount, 2),
                        'amount_class' => 'text-green-500',
                        'method' => $deposit->bankAccount?->bank_name ?? 'Bank Transfer',
                        'date_label' => optional($deposit->created_at)->timezone(config('app.timezone'))->format('d M Y'),
                        'datetime_label' => optional($deposit->created_at)->timezone(config('app.timezone'))->format('d M Y, h:i A'),
                        'status' => 'pending',
                        'balance_type' => 'Withdrawable',
                        'balance_after' => '—',
                        'note' => 'Your deposit is awaiting admin approval.',
                        'uuid' => $deposit->reference_number,
                    ];
                });

            $pendingRows = $pendingRows->concat($pendingDepositRows);
        }

        if ($filters['type'] === '' || $filters['type'] === 'withdraw') {
            $pendingWithdrawalRows = \App\Models\WithdrawalRequest::query()
                ->where('user_id', $user->id)
                ->pending()
                ->when($filters['q'] !== '', function ($query) use ($filters) {
                    if (ctype_digit($filters['q'])) {
                        $query->where('id', (int) $filters['q']);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                })
                ->when($filters['date'] !== '', fn ($query) => $query->whereDate('requested_at', $filters['date']))
                ->latest('requested_at')
                ->get()
                ->map(function ($withdrawal) use ($txRef) {
                    $method = $withdrawal->payment_method
                        ? ucwords(str_replace('_', ' ', (string) $withdrawal->payment_method))
                        : 'Withdrawal';

                    return (object) [
                        'id' => 'withdrawal-' . $withdrawal->id,
                        'tx_id' => $txRef->forEntity('WDR', (int) $withdrawal->id, $withdrawal->requested_at),
                        'type_label' => 'Withdraw',
                        'amount' => -1 * abs((float) $withdrawal->amount),
                        'amount_label' => '-$' . number_format(abs((float) $withdrawal->amount), 2),
                        'amount_class' => 'text-red-500',
                        'method' => $method,
                        'date_label' => optional($withdrawal->requested_at)->timezone(config('app.timezone'))->format('d M Y'),
                        'datetime_label' => optional($withdrawal->requested_at)->timezone(config('app.timezone'))->format('d M Y, h:i A'),
                        'status' => 'pending',
                        'balance_type' => 'Withdrawable',
                        'balance_after' => '—',
                        'note' => 'Your withdrawal request is being reviewed.',
                        'uuid' => null,
                    ];
                });

            $pendingRows = $pendingRows->concat($pendingWithdrawalRows);
        }
    }

    $ledgerRows = $ledgerTransactions->getCollection()->map(function ($transaction) use ($resolveUiStatus, $resolveMethod) {
        $amount = (float) $transaction->amount;
        $status = $resolveUiStatus($transaction);
        $isCredit = $amount > 0;
        $referenceCode = $transaction->reference_code;

        return (object) [
            'id' => 'ledger-' . $transaction->id,
            'tx_id' => $referenceCode,
            'type_label' => $transaction->type?->label() ?? 'Transaction',
            'amount' => $amount,
            'amount_label' => ($isCredit ? '+$' : '-$') . number_format(abs($amount), 2),
            'amount_class' => $isCredit
                ? ($status === 'credited' ? 'text-brand-primary' : 'text-green-500')
                : 'text-red-500',
            'method' => $resolveMethod($transaction),
            'date_label' => optional($transaction->created_at)->timezone(config('app.timezone'))->format('d M Y'),
            'datetime_label' => optional($transaction->created_at)->timezone(config('app.timezone'))->format('d M Y, h:i A'),
            'status' => $status,
            'balance_type' => ucfirst((string) $transaction->balance_type?->value),
            'balance_after' => '$' . number_format((float) $transaction->balance_after, 2),
            'note' => match ($status) {
                'credited' => 'This amount was credited to your wallet.',
                'failed' => 'This transaction did not complete successfully.',
                default => 'This wallet ledger entry is complete.',
            },
            'uuid' => $referenceCode,
        ];
    });

    $displayRows = $filters['status'] === 'pending'
        ? $pendingRows->values()
        : ($ledgerTransactions->currentPage() === 1
            ? $pendingRows->concat($ledgerRows)->values()
            : $ledgerRows->values());

    $statusStyles = [
        'completed' => 'bg-green-500/20 text-green-500',
        'pending' => 'bg-yellow-500/20 text-yellow-500',
        'failed' => 'bg-red-500/20 text-red-500',
        'credited' => 'bg-brand-primary/20 text-brand-primary',
    ];

    $statusLabels = [
        'completed' => 'Completed',
        'pending' => 'Pending',
        'failed' => 'Failed',
        'credited' => 'Credited',
    ];

    $detailsPayload = $displayRows->mapWithKeys(fn ($row) => [
        $row->id => [
            'tx_id' => $row->tx_id,
            'type_label' => $row->type_label,
            'amount_label' => $row->amount_label,
            'amount_class' => $row->amount_class,
            'method' => $row->method,
            'datetime_label' => $row->datetime_label,
            'status' => $row->status,
            'status_label' => $statusLabels[$row->status] ?? ucfirst($row->status),
            'balance_type' => $row->balance_type,
            'balance_after' => $row->balance_after,
            'note' => $row->note,
            'uuid' => $row->uuid,
        ],
    ]);

    $hasActiveFilters = collect($filters)->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty();
@endphp

<div class="transactions py-6" data-transactions-root>
    {{-- Stats --}}
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col gap-2 items-center justify-center">
                <i class="fa-solid fa-receipt text-xl md:text-4xl text-brand-primary"></i>
                <p class="opacity-70">Total Transactions</p>
                <h3 class="mt-2">{{ number_format($totalTransactions) }}</h3>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col gap-2 items-center justify-center">
                <i class="fa-solid fa-arrow-down text-xl md:text-4xl text-green-500"></i>
                <p class="opacity-70">Total Deposits</p>
                <h3 class="mt-2 text-green-500">${{ number_format($totalDeposits, 2) }}</h3>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col gap-2 items-center justify-center">
                <i class="fa-solid fa-arrow-up text-xl md:text-4xl text-red-500"></i>
                <p class="opacity-70">Total Withdrawals</p>
                <h3 class="mt-2 text-red-500">${{ number_format($totalWithdrawals, 2) }}</h3>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
            <div class="flex flex-col gap-2 items-center justify-center">
                <i class="fa-solid fa-clock text-xl md:text-4xl text-yellow-500"></i>
                <p class="opacity-70">Pending</p>
                <h3 class="mt-2 text-yellow-500">{{ number_format($pendingCount) }}</h3>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('user-account') }}#transactions"
        class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-3 sm:p-6">
        <div class="grid gap-4 lg:grid-cols-4">
            <input type="text" name="tx_q" value="{{ $filters['q'] }}" placeholder="Search Transaction ID"
                class="rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">

            <select name="tx_type"
                class="rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="deposit" @selected($filters['type'] === 'deposit')>Deposit</option>
                <option value="withdraw" @selected($filters['type'] === 'withdraw')>Withdraw</option>
                <option value="bonus" @selected($filters['type'] === 'bonus')>Bonus</option>
                <option value="entry_fee" @selected($filters['type'] === 'entry_fee')>Entry Fee</option>
            </select>

            <select name="tx_status"
                class="rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="completed" @selected($filters['status'] === 'completed')>Completed</option>
                <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                <option value="credited" @selected($filters['status'] === 'credited')>Credited</option>
                <option value="failed" @selected($filters['status'] === 'failed')>Failed</option>
            </select>

            <div class="flex gap-3">
                <input type="date" name="tx_date" value="{{ $filters['date'] }}"
                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none [color-scheme:dark] focus:border-brand-primary"
                    onchange="this.form.submit()">
                <button type="submit" class="btn-primary px-4 py-3 shrink-0">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>

        @if ($hasActiveFilters)
            <div class="mt-4">
                <a href="{{ route('user-account') }}#transactions" class="text-sm text-green-400 hover:underline">
                    Clear filters
                </a>
            </div>
        @endif
    </form>

    {{-- Table --}}
    <div class="mt-6 rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
        <div class="mb-8">
            <h3>Transaction History</h3>
            <p class="mt-2 opacity-70">
                View all your wallet activities.
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[950px] text-[12px] md:text-base lg:text-xl">
                <thead>
                    <tr class="border-b border-brand-border">
                        <th class="py-2 sm:py-4 text-left">Transaction ID</th>
                        <th class="py-2 sm:py-4 text-left">Type</th>
                        <th class="py-2 sm:py-4 text-left">Amount</th>
                        <th class="py-2 sm:py-4 text-left">Method</th>
                        <th class="py-2 sm:py-4 text-left">Date</th>
                        <th class="py-2 sm:py-4 text-left">Status</th>
                        <th class="py-2 sm:py-4 text-left">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($displayRows as $row)
                        <tr class="border-b border-brand-border last:border-0">
                            <td class="py-3 sm:py-5">
                                <code class="font-mono text-sm tracking-wide">{{ $row->tx_id }}</code>
                            </td>
                            <td class="py-3 sm:py-5">{{ $row->type_label }}</td>
                            <td class="py-3 sm:py-5 {{ $row->amount_class }}">{{ $row->amount_label }}</td>
                            <td class="py-3 sm:py-5">{{ $row->method }}</td>
                            <td class="py-3 sm:py-5">{{ $row->date_label }}</td>
                            <td class="py-3 sm:py-5">
                                <span class="rounded-full {{ $statusStyles[$row->status] ?? 'bg-brand-dark text-white/70' }} px-3 py-1">
                                    {{ $statusLabels[$row->status] ?? ucfirst($row->status) }}
                                </span>
                            </td>
                            <td class="py-3 sm:py-5">
                                <button type="button"
                                    class="btn-primary text-[12px] sm:text-sm px-3 sm:px-5 py-2"
                                    data-tx-detail="{{ $row->id }}">
                                    Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center opacity-70">
                                @if ($hasActiveFilters)
                                    No transactions match these filters.
                                @else
                                    You have no wallet transactions yet.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($filters['status'] !== 'pending' && $ledgerTransactions->total() > 0)
            <div class="mt-8 border-t border-brand-border pt-6">
                {{ $ledgerTransactions->links('vendor.pagination.brand') }}
            </div>
        @endif
    </div>

    {{-- Details modal --}}
    <div id="txDetailModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-5">
        <div
            class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-3xl border border-brand-border bg-brand-surface shadow-[0_0_45px_rgba(34,197,94,.18)]">
            <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

            <div class="relative p-6 sm:p-8">
                <button type="button" data-tx-detail-close
                    class="absolute right-5 top-5 text-2xl opacity-70 transition hover:opacity-100 hover:text-orange-400">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="text-center">
                    <small class="uppercase tracking-[3px] text-brand-primary">Transaction Details</small>
                    <h3 id="txDetailType" class="mt-3"></h3>
                    <p id="txDetailAmount" class="mt-3 text-2xl font-semibold"></p>
                    <span id="txDetailStatus" class="mt-4 inline-block rounded-full px-3 py-1 text-sm"></span>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                        <p class="text-sm opacity-60">Transaction ID</p>
                        <p id="txDetailId" class="mt-2 break-all font-mono text-sm font-semibold tracking-wide"></p>
                    </div>
                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                        <p class="text-sm opacity-60">Method</p>
                        <p id="txDetailMethod" class="mt-2 font-semibold"></p>
                    </div>
                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                        <p class="text-sm opacity-60">Balance Type</p>
                        <p id="txDetailBalanceType" class="mt-2 font-semibold"></p>
                    </div>
                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-4">
                        <p class="text-sm opacity-60">Balance After</p>
                        <p id="txDetailBalanceAfter" class="mt-2 font-semibold"></p>
                    </div>
                    <div class="rounded-2xl border border-brand-border bg-brand-dark p-4 sm:col-span-2">
                        <p class="text-sm opacity-60">Date</p>
                        <p id="txDetailDate" class="mt-2 font-semibold"></p>
                    </div>
                </div>

                <p id="txDetailNote" class="mt-6 text-center text-sm opacity-70"></p>

                <div class="mt-8 text-center">
                    <button type="button" class="btn-primary" data-tx-detail-close>Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const root = document.querySelector('[data-transactions-root]');
        if (!root || root.dataset.bound === '1') {
            return;
        }
        root.dataset.bound = '1';

        const rows = @json($detailsPayload);
        const statusStyles = @json($statusStyles);
        const modal = document.getElementById('txDetailModal');
        if (!modal) {
            return;
        }

        function openModal(id) {
            const row = rows[id];
            if (!row) {
                return;
            }

            document.getElementById('txDetailType').textContent = row.type_label || '—';
            const amountEl = document.getElementById('txDetailAmount');
            amountEl.textContent = row.amount_label || '—';
            amountEl.className = 'mt-3 text-2xl font-semibold ' + (row.amount_class || '');

            const statusEl = document.getElementById('txDetailStatus');
            statusEl.textContent = row.status_label || '';
            statusEl.className = 'mt-4 inline-block rounded-full px-3 py-1 text-sm ' +
                (statusStyles[row.status] || 'bg-brand-dark text-white/70');

            document.getElementById('txDetailId').textContent = row.tx_id || '—';
            document.getElementById('txDetailMethod').textContent = row.method || '—';
            document.getElementById('txDetailBalanceType').textContent = row.balance_type || '—';
            document.getElementById('txDetailBalanceAfter').textContent = row.balance_after || '—';
            document.getElementById('txDetailDate').textContent = row.datetime_label || '—';
            document.getElementById('txDetailNote').textContent = row.note || '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        root.querySelectorAll('[data-tx-detail]').forEach(function (button) {
            button.addEventListener('click', function () {
                openModal(button.getAttribute('data-tx-detail'));
            });
        });

        modal.querySelectorAll('[data-tx-detail-close]').forEach(function (button) {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    })();
</script>
