<?php

namespace App\Http\Controllers;

use App\Actions\Lottery\BuyLotteryTicketsAction;
use App\Enums\LotteryStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Http\Requests\BuyLotteryTicketRequest;
use App\Models\Lottery;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Paginator;

class LotteryController extends Controller
{
    /**
     * Display available lotteries.
     */
    public function index(): View
    {
        $userId = auth('web')->id();

        $lotteries = Lottery::query()
            ->whereNotIn('status', [
                ...LotteryStatus::hiddenFromPublic(),
                LotteryStatus::COMPLETED->value,
            ])
            ->with([
                'latestAnnouncedDraw.winners' => function ($query) use ($userId) {
                    if ($userId) {
                        $query->where('user_id', $userId)->with('ticket');
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->ordered()
            ->get();

        $resultLotteries = Lottery::query()
            ->visible()
            ->whereHas('draws', function ($query) {
                $query->where('status', 'completed')
                    ->where('winners_announced', true);
            })
            ->with([
                'latestAnnouncedDraw.winners' => function ($query) use ($userId) {
                    if ($userId) {
                        $query->where('user_id', $userId)->with('ticket');
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->withCount([
                'draws as announced_draws_count' => function ($query) {
                    $query->where('status', 'completed')
                        ->where('winners_announced', true);
                },
            ])
            ->ordered()
            ->get();

        return view(
            'lottery.index',
            array_merge(
                compact('lotteries', 'resultLotteries'),
                $this->lotteryWalletSummary()
            )
        );
    }

    /**
     * Display announced draw results for a lottery.
     */
    public function results(Request $request, Lottery $lottery): View|RedirectResponse
    {
        abort_unless($lottery->isVisibleToPublic(), 404);

        $perPage = 10;

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'round' => ['nullable', 'integer', 'min:1'],
            'prize' => ['nullable', 'in:first,second,third,fourth,fifth'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'my_wins' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:newest,oldest'],
            'draw' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $baseQuery = $lottery->draws()
            ->where('status', 'completed')
            ->where('winners_announced', true);

        $totalRounds = (clone $baseQuery)->count();

        abort_unless($totalRounds > 0, 404);

        $dailyRounds = LotteryDraw::dailyRoundsFor(
            $lottery->draws()
                ->where('status', 'completed')
                ->orderBy('id')
                ->get(['id', 'lottery_id', 'sales_start_at', 'completed_at', 'created_at'])
        );

        $roundNumbers = $dailyRounds->map(fn (array $round) => $round['number']);
        $roundDates = $dailyRounds->map(fn (array $round) => $round['date']);
        $announcedIds = (clone $baseQuery)->pluck('id');
        $maxDailyRound = (int) ($roundNumbers->only($announcedIds->all())->max() ?: 1);

        $latestDrawId = (clone $baseQuery)->latest('id')->value('id');

        $focusDrawId = isset($filters['draw']) ? (int) $filters['draw'] : null;

        if ($focusDrawId) {
            $orderedIds = (clone $baseQuery)->latest('id')->pluck('id');
            $index = $orderedIds->search($focusDrawId);

            if ($index !== false) {
                $targetPage = (int) floor($index / $perPage) + 1;

                if ($request->integer('page', 1) !== $targetPage) {
                    return redirect()->route('lotteries.results', [
                        'lottery' => $lottery,
                        'page' => $targetPage,
                        'draw' => $focusDrawId,
                    ]);
                }
            }
        }

        $filteredQuery = clone $baseQuery;
        $this->applyResultsFilters($filteredQuery, $filters, $roundNumbers);

        $sort = $filters['sort'] ?? 'newest';
        $filteredQuery = $sort === 'oldest'
            ? $filteredQuery->orderBy('id')
            : $filteredQuery->orderByDesc('id');

        $draws = $filteredQuery
            ->with([
                'winners' => function ($query) {
                    $userId = auth('web')->id();

                    if ($userId) {
                        $query->where('user_id', $userId)->with('ticket');
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->paginate($perPage)
            ->withQueryString();

        $hasActiveFilters = collect($filters)
            ->except(['draw', 'page', 'sort'])
            ->filter(fn($value) => $value !== null && $value !== '')
            ->isNotEmpty()
            || (($filters['sort'] ?? 'newest') !== 'newest');

        return view('lottery.results', [
            'lottery' => $lottery,
            'draws' => $draws,
            'totalRounds' => $totalRounds,
            'matchedRounds' => $draws->total(),
            'focusDrawId' => $focusDrawId,
            'roundNumbers' => $roundNumbers,
            'roundDates' => $roundDates,
            'maxDailyRound' => $maxDailyRound,
            'latestDrawId' => $latestDrawId,
            'filters' => [
                'q' => $filters['q'] ?? '',
                'round' => $filters['round'] ?? '',
                'prize' => $filters['prize'] ?? '',
                'from' => $filters['from'] ?? '',
                'to' => $filters['to'] ?? '',
                'my_wins' => (bool) ($filters['my_wins'] ?? false),
                'sort' => $sort,
            ],
            'hasActiveFilters' => $hasActiveFilters,
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\LotteryDraw>  $query
     * @param  array<string, mixed>  $filters
     * @param  \Illuminate\Support\Collection<int, int>  $roundNumbers
     */
    protected function applyResultsFilters($query, array $filters, Collection $roundNumbers): void
    {
        $userId = auth('web')->id();
        $search = trim((string) ($filters['q'] ?? ''));

        if ($search !== '') {
            if (!$userId) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($builder) use ($search, $userId) {
                    if (ctype_digit($search)) {
                        $builder->where('lottery_draws.id', (int) $search);
                    }

                    $builder->orWhereHas('winners', function ($winnerQuery) use ($search, $userId) {
                        $winnerQuery->where('user_id', $userId)
                            ->whereHas('ticket', function ($ticketQuery) use ($search) {
                                $ticketQuery->where('ticket_number', 'like', "%{$search}%");
                            });
                    })->orWhereExists(function ($exists) use ($search, $userId) {
                        $exists->selectRaw('1')
                            ->from('lottery_tickets')
                            ->whereColumn('lottery_tickets.lottery_id', 'lottery_draws.lottery_id')
                            ->where('lottery_tickets.user_id', $userId)
                            ->where('lottery_tickets.ticket_number', 'like', "%{$search}%")
                            ->where(function ($period) {
                                $period->whereNull('lottery_draws.sales_start_at')
                                    ->orWhereColumn('lottery_tickets.purchased_at', '>=', 'lottery_draws.sales_start_at');
                            })
                            ->where(function ($period) {
                                $period->whereNull('lottery_draws.sales_end_at')
                                    ->orWhereColumn('lottery_tickets.purchased_at', '<=', 'lottery_draws.sales_end_at');
                            });
                    });
                });
            }
        }

        if (!empty($filters['round'])) {
            $matchingIds = $roundNumbers
                ->filter(fn ($number) => (int) $number === (int) $filters['round'])
                ->keys();

            if ($matchingIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('id', $matchingIds->all());
            }
        }

        if (!empty($filters['prize'])) {
            $query->whereHas('winners', function ($winnerQuery) use ($filters, $userId) {
                $winnerQuery->where('prize_category', $filters['prize']);

                if ($userId) {
                    $winnerQuery->where('user_id', $userId);
                } else {
                    $winnerQuery->whereRaw('1 = 0');
                }
            });
        }

        if (!empty($filters['from'])) {
            $query->whereDate('completed_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('completed_at', '<=', $filters['to']);
        }

        if (!empty($filters['my_wins'])) {
            $query->whereHas('winners', function ($winnerQuery) use ($userId) {
                if ($userId) {
                    $winnerQuery->where('user_id', $userId);
                } else {
                    $winnerQuery->whereRaw('1 = 0');
                }
            });
        }
    }

    /**
     * Display lottery details.
     */
    public function show(Lottery $lottery): View
    {
        abort_unless($lottery->isVisibleToPublic(), 404);

        $userId = auth('web')->id();

        $draws = $lottery->draws()
            ->where('status', 'completed')
            ->with([
                'winners' => function ($query) use ($userId) {
                    if ($userId) {
                        $query->where('user_id', $userId)->with('ticket');
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->latest('id')
            ->paginate(10);

        $userTickets = $userId
            ? $lottery->tickets()
                ->where('user_id', $userId)
                ->latest('purchased_at')
                ->get()
            : collect();

        return view('lottery.show', array_merge([
            'lottery' => $lottery,
            'draws' => $draws,
            'userTickets' => $userTickets,
        ], $this->lotteryWalletSummary()));
    }
    // public function show(Lottery $lottery): View
    // {
    //     abort_unless($lottery->isVisibleToPublic(), 404);

    //     $userId = auth('web')->id();

    //     $draws = $lottery->draws()
    //         ->where('status', 'completed')
    //         ->with([
    //             'winners' => function ($query) use ($userId) {
    //                 if ($userId) {
    //                     $query->where('user_id', $userId)->with('ticket');
    //                 } else {
    //                     $query->whereRaw('1 = 0');
    //                 }
    //             },
    //         ])
    //         ->latest('id')
    //         ->get();

    //     $userTickets = $userId
    //         ? $lottery->tickets()
    //             ->where('user_id', $userId)
    //             ->latest('purchased_at')
    //             ->get()
    //         : collect();

    //     return view('lottery.show', array_merge([
    //         'lottery' => $lottery,
    //         'draws' => $draws,
    //         'userTickets' => $userTickets,
    //     ], $this->lotteryWalletSummary()));
    // }

    /**
     * Display one specific historical draw (private to the viewer).
     */
    public function drawShow(Lottery $lottery, LotteryDraw $draw): View
    {
        abort_unless($lottery->isVisibleToPublic(), 404);
        abort_unless($draw->lottery_id === $lottery->id, 404);

        abort_unless($draw->status === 'completed', 404);
        abort_unless($draw->winners_announced, 404);

        $userId = auth('web')->id();

        $draw->load([
            'winners' => function ($query) use ($userId) {
                if ($userId) {
                    $query->where('user_id', $userId)->with('ticket');
                } else {
                    $query->whereRaw('1 = 0');
                }
            },
        ]);

        return view(
            'lottery.draw-show',
            compact('lottery', 'draw')
        );
    }

    /**
     * The authenticated user's lottery purchase and result history,
     * grouped by lottery round / draw.
     */
    public function history(): View
    {
        $userId = (int) auth('web')->id();
    
        $tickets = LotteryTicket::query()
            ->with(['lottery', 'winner'])
            ->where('user_id', $userId)
            ->orderByDesc('purchased_at')
            ->get();
    
        $lotteryIds = $tickets->pluck('lottery_id')->unique()->values();
    
        $drawsByLottery = LotteryDraw::query()
            ->with([
                'winners' => fn ($query) => $query
                    ->where('user_id', $userId)
                    ->with('ticket'),
            ])
            ->whereIn('lottery_id', $lotteryIds)
            ->where('status', 'completed')
            ->orderBy('id')
            ->get()
            ->groupBy('lottery_id');
    
        $history = collect();
    
        foreach ($tickets->groupBy('lottery_id') as $lotteryId => $lotteryTickets) {
            /** @var \App\Models\Lottery|null $lottery */
            $lottery = $lotteryTickets->first()?->lottery;
            $lotteryDraws = $drawsByLottery->get($lotteryId, collect());
    
            $dailyRounds = LotteryDraw::dailyRoundsFor($lotteryDraws);
    
            $assignedIds = collect();
    
            foreach ($lotteryDraws as $draw) {
                $roundTickets = $lotteryTickets->filter(
                    fn (LotteryTicket $ticket) => ! $assignedIds->contains($ticket->id)
                        && $this->ticketBelongsToDraw($ticket, $draw)
                );
    
                if ($roundTickets->isEmpty()) {
                    continue;
                }
    
                $assignedIds = $assignedIds->merge($roundTickets->pluck('id'));
                $wins = $draw->winners;
    
                if ($wins->isNotEmpty()) {
                    $outcome = 'won';
                    $drawStatus = 'drawn';
                } elseif ($draw->winners_announced) {
                    $outcome = 'lost';
                    $drawStatus = 'drawn';
                } else {
                    $outcome = 'pending';
                    $drawStatus = 'pending';
                }
    
                $round = $dailyRounds->get((int) $draw->id);

                $history->push([
                    'lottery' => $lottery,
                    'draw' => $draw,
                    'round_number' => $round['number'] ?? null,
                    'round_date' => $round['date'] ?? $draw->roundDate(),
                    'tickets' => $roundTickets->values(),
                    'quantity' => $roundTickets->count(),
                    'amount' => $roundTickets->sum(fn (LotteryTicket $ticket) => (float) $ticket->price),
                    'purchased_at' => $roundTickets->min('purchased_at'),
                    'drawn_at' => $draw->completed_at,
                    'draw_status' => $drawStatus,
                    'outcome' => $outcome,
                    'wins' => $wins,
                    'prize_total' => $wins->sum(fn ($win) => (float) $win->prize_amount),
                ]);
            }
    
            $openTickets = $lotteryTickets
                ->reject(fn (LotteryTicket $ticket) => $assignedIds->contains($ticket->id))
                ->values();
    
            if ($openTickets->isEmpty()) {
                continue;
            }
    
            $drawStatus = 'pending';
            $outcome = 'not_yet_drawn';
    
            if ($lottery?->isCancelled()) {
                $drawStatus = 'cancelled';
                $outcome = 'cancelled';
            } elseif ($openTickets->every(
                fn (LotteryTicket $ticket) => in_array($ticket->status, ['winner', 'lost', 'refunded', 'cancelled'], true)
            )) {
                $drawStatus = 'drawn';
                $outcome = $openTickets->contains(fn (LotteryTicket $ticket) => $ticket->isWinner())
                    ? 'won'
                    : 'lost';
            }
    
            $openPurchasedAt = $openTickets->min('purchased_at');
            $openDay = ($openPurchasedAt ? \Illuminate\Support\Carbon::parse($openPurchasedAt) : now())
                ->copy()
                ->timezone(config('app.timezone'))
                ->startOfDay();
            $openRoundNumber = $lottery && $lottery->currentRoundDate()->equalTo($openDay)
                ? $lottery->currentRoundNumber()
                : LotteryDraw::query()
                    ->where('lottery_id', $lotteryId)
                    ->where('status', 'completed')
                    ->startedOnDate($openDay->toDateString())
                    ->count() + 1;

            $history->push([
                'lottery' => $lottery,
                'draw' => null,
                'round_number' => $openRoundNumber,
                'round_date' => $openDay,
                'tickets' => $openTickets,
                'quantity' => $openTickets->count(),
                'amount' => $openTickets->sum(fn (LotteryTicket $ticket) => (float) $ticket->price),
                'purchased_at' => $openTickets->min('purchased_at'),
                'drawn_at' => null,
                'draw_status' => $drawStatus,
                'outcome' => $outcome,
                'wins' => collect(),
                'prize_total' => 0.0,
            ]);
        }
    
        $history = $history
            ->sortByDesc(fn (array $row) => optional($row['purchased_at'])->timestamp ?? 0)
            ->values();
    
        // Paginate the assembled collection (10 per page)
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('page');
    
        $paginatedHistory = new \Illuminate\Pagination\LengthAwarePaginator(
            $history->forPage($currentPage, $perPage)->values(),
            $history->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    
        return view('lottery.history', array_merge(
            ['history' => $paginatedHistory],
            $this->lotteryWalletSummary()
        ));
    }

    /**
     * Whether a ticket was purchased inside a draw's sales window.
     */
    protected function ticketBelongsToDraw(LotteryTicket $ticket, LotteryDraw $draw): bool
    {
        if (!$ticket->purchased_at) {
            return false;
        }

        if ($draw->sales_start_at && $ticket->purchased_at->lt($draw->sales_start_at)) {
            return false;
        }

        if ($draw->sales_end_at && $ticket->purchased_at->gt($draw->sales_end_at)) {
            return false;
        }

        return true;
    }

    /**
     * Manual / fallback draw for a single due lottery.
     * Primary draws run via `php artisan lottery:draw-due` (scheduler).
     */
    public function drawDue(Request $request, Lottery $lottery, \App\Actions\Lottery\DrawLotteryAction $action)
    {
        $lottery->syncStatus();
        $lottery->refresh();

        $previousDraw = $lottery->draws()
            ->where('status', 'completed')
            ->where('winners_announced', true)
            ->latest('id')
            ->first();

        if (!$lottery->canDraw()) {
            return response()->json($this->livePayload(
                lottery: $lottery->fresh(),
                drawn: false,
                alreadyDrawn: $lottery->isCompleted() || $lottery->currentRoundHasDraw(),
                personalDraw: $previousDraw,
            ));
        }

        try {
            $draw = $action->execute($lottery);

            return response()->json($this->livePayload(
                lottery: $lottery->fresh(),
                drawn: true,
                alreadyDrawn: false,
                personalDraw: $draw,
                winnersAnnounced: (bool) $draw->winners_announced,
                totalWinners: (int) $draw->total_winners,
            ));
        } catch (\Throwable $exception) {
            report($exception);

            // Always return timer fields so short (seconds) countdowns can
            // retry/recover instead of freezing on 00:00:00.
            $fresh = $lottery->fresh();

            return response()->json(array_merge(
                $this->livePayload(
                    lottery: $fresh,
                    drawn: false,
                    alreadyDrawn: $fresh?->currentRoundHasDraw() ?? false,
                    personalDraw: $previousDraw,
                ),
                [
                    'message' => $exception->getMessage(),
                    'retry' => true,
                ]
            ), 409);
        }
    }

    /**
     * HTML fragment used to refresh a lottery card without a full page reload.
     */
    public function liveHtml(Request $request, Lottery $lottery)
    {
        abort_unless($lottery->isVisibleToPublic(), 404);

        $lottery->refresh();
        $lottery->syncStatus();
        $lottery->refresh();

        $wallet = $this->lotteryWalletSummary();

        if ($request->query('view') === 'show-actions') {
            return response()
                ->view('lottery.partials.show-live-actions', array_merge(
                    compact('lottery'),
                    $wallet
                ))
                ->header('Cache-Control', 'no-store');
        }

        return response()
            ->view('lottery.partials.lottery-card', array_merge(
                compact('lottery'),
                $wallet
            ))
            ->header('Cache-Control', 'no-store');
    }

    /**
     * @return array<string, mixed>
     */
    protected function livePayload(
        Lottery $lottery,
        bool $drawn,
        bool $alreadyDrawn,
        ?LotteryDraw $personalDraw = null,
        ?bool $winnersAnnounced = null,
        ?int $totalWinners = null,
    ): array {
        $userId = auth('web')->id();
        $wallet = $this->lotteryWalletSummary();

        if ($personalDraw && $userId) {
            $personalDraw->unsetRelation('winners');
            $personalDraw->load([
                'winners' => function ($query) use ($userId) {
                    $query->where('user_id', $userId)->with('ticket');
                },
            ]);
        }

        $personal = $personalDraw?->personalResult($userId);

        $personalPayload = null;

        if ($personal) {
            $prizes = $personalDraw?->prizeCatalog($lottery) ?? collect();
            $wins = collect($personal['wins'])->map(function ($win) use ($lottery, $prizes) {
                $prize = $prizes->get($win->prize_category, []);

                return [
                    'category' => $win->prize_category,
                    'label' => $prize['label'] ?? ucfirst((string) $win->prize_category),
                    'amount' => (float) ($prize['amount'] ?? $win->prize_amount),
                    'ticket_number' => $win->ticket?->ticket_number,
                    'currency' => $lottery->currency,
                ];
            })->values();

            $personalPayload = [
                'outcome' => $personal['outcome'],
                'wins' => $wins,
            ];
        }

        return [
            'drawn' => $drawn,
            'already_drawn' => $alreadyDrawn,
            'restarted' => $drawn,
            'lottery_id' => $lottery->id,
            'lottery_title' => $lottery->title,
            'ends_at' => $lottery->ends_at?->toIso8601String(),
            'server_now' => now()->toIso8601String(),
            'round_number' => $lottery->currentRoundNumber(),
            'round_date' => $lottery->currentRoundDate()->toDateString(),
            'round_label' => $lottery->roundLabel(),
            'is_sales_open' => $lottery->isSalesOpen(),
            'remaining_tickets' => $lottery->remainingTickets(),
            'user_tickets' => $userId ? $lottery->ticketsForCurrentRoundUser($userId) : 0,
            'max_tickets_per_user' => $lottery->max_tickets_per_user,
            'winners_announced' => $winnersAnnounced,
            'total_winners' => $totalWinners,
            'wallet' => [
                'used' => $wallet['usedBalance'],
                'remaining' => $wallet['remainingBalance'],
                'currency' => $wallet['walletCurrency'],
            ],
            'personal_result' => $personalPayload,
            'live_html_url' => route('lotteries.live-html', $lottery),
            'buy_form_html' => view(
                'lottery.partials.buy-ticket-form',
                array_merge(compact('lottery'), [
                    'remainingBalance' => $wallet['remainingBalance'],
                ])
            )->render(),
        ];
    }

    /**
     * Manual / fallback draw for every due lottery.
     * Primary draws run via the scheduler — do not rely on page visits.
     */
    public function drawDueAll(\App\Actions\Lottery\DrawLotteryAction $action)
    {
        $drawnIds = [];

        foreach (Lottery::query()->dueForDraw()->get() as $lottery) {
            try {
                $action->execute($lottery);
                $drawnIds[] = $lottery->id;
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return response()->json([
            'drawn' => $drawnIds !== [],
            'drawn_ids' => $drawnIds,
        ]);
    }

    /**
     * Purchase lottery tickets.
     */
    public function buyTickets(
        BuyLotteryTicketRequest $request,
        Lottery $lottery,
        BuyLotteryTicketsAction $action
    ): RedirectResponse|JsonResponse {
        abort_unless($lottery->isVisibleToPublic(), 404);

        $user = auth('web')->user();
        $balance = (float) ($user?->wallet?->withdrawable_balance ?? 0);
        $ticketPrice = (float) $lottery->ticket_price;

        if ($balance < $ticketPrice) {
            return $this->insufficientLotteryBalanceResponse($request);
        }

        try {
            $action->execute(
                user: $user,
                lottery: $lottery,
                quantity: 1
            );
        } catch (InsufficientBalanceException $exception) {
            return $this->insufficientLotteryBalanceResponse($request);
        } catch (ValidationException $exception) {
            $messages = collect($exception->errors())->flatten()->implode(' ');

            if (str_contains(strtolower($messages), 'insufficient')
                || str_contains(strtolower($messages), 'balance')
            ) {
                return $this->insufficientLotteryBalanceResponse($request);
            }

            if ($this->wantsAjax($request)) {
                return response()->json([
                    'ok' => false,
                    'type' => 'error',
                    'title' => 'Unable to Buy Ticket',
                    'message' => $messages !== '' ? $messages : 'Unable to purchase ticket.',
                ], 422);
            }

            throw $exception;
        }

        $lottery->refresh();
        $user->unsetRelation('wallet');
        $user->load('wallet');

        $successMessage = 'Ticket purchased successfully.';

        if ($this->wantsAjax($request)) {
            return response()->json([
                'ok' => true,
                'type' => 'success',
                'title' => 'Payment Successful',
                'message' => $successMessage,
                'lottery' => $this->lotteryPurchaseState($lottery, $user->id),
                'wallet' => $this->lotteryWalletSummary(),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', $successMessage);
    }

    /**
     * Used lottery spend and remaining spendable wallet balance.
     *
     * @return array{usedBalance: float, remainingBalance: float, walletCurrency: string}
     */
    protected function lotteryWalletSummary(): array
    {
        $user = auth('web')->user();

        if (!$user) {
            return [
                'usedBalance' => 0.0,
                'remainingBalance' => 0.0,
                'walletCurrency' => 'USD',
            ];
        }

        $wallet = $user->wallet;

        $used = (float) LotteryTicket::query()
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['refunded', 'cancelled'])
            ->sum('price');

        return [
            'usedBalance' => $used,
            'remainingBalance' => (float) ($wallet?->withdrawable_balance ?? 0),
            'walletCurrency' => $wallet?->currency ?? 'USD',
        ];
    }

    protected function wantsAjax(Request $request): bool
    {
        return $request->expectsJson() || $request->ajax();
    }

    protected function insufficientLotteryBalanceResponse(Request $request): RedirectResponse|JsonResponse
    {
        $message = 'Your balance is insufficient. Please deposit amount to buy lottery tickets.';

        if ($this->wantsAjax($request)) {
            return response()->json([
                'ok' => false,
                'type' => 'warning',
                'title' => 'Insufficient Balance',
                'message' => $message,
                'action' => [
                    'label' => 'Deposit Now',
                    'url' => route('deposits.index'),
                ],
            ], 422);
        }

        return redirect()
            ->route('deposits.index')
            ->with('warning', $message);
    }

    /**
     * @return array{
     *     id: int,
     *     remaining_tickets: int|null,
     *     remaining_label: string,
     *     tickets_sold: int,
     *     user_tickets: int,
     *     max_tickets_per_user: int|null,
     *     user_tickets_label: string,
     *     can_buy: bool,
     *     sales_open: bool
     * }
     */
    protected function lotteryPurchaseState(Lottery $lottery, ?int $userId): array
    {
        $remainingTickets = $lottery->remainingTickets();
        $maxPerUser = $lottery->max_tickets_per_user;
        $userTickets = $userId ? $lottery->ticketsForCurrentRoundUser($userId) : 0;
        $salesOpen = $lottery->isSalesOpen();
        $canBuy = $userId && $salesOpen && $lottery->maxPurchaseQuantityForUser($userId) > 0;

        return [
            'id' => $lottery->id,
            'remaining_tickets' => $remainingTickets,
            'remaining_label' => $remainingTickets === null
                ? 'Unlimited Tickets'
                : number_format($remainingTickets) . ' Tickets Remaining',
            'tickets_sold' => $lottery->totalCurrentRoundTickets(),
            'user_tickets' => $userTickets,
            'max_tickets_per_user' => $maxPerUser,
            'user_tickets_label' => $maxPerUser === null
                ? $userTickets . ' purchased'
                : $userTickets . '/' . $maxPerUser . ' Purchased',
            'can_buy' => $canBuy,
            'sales_open' => $salesOpen,
        ];
    }
}
