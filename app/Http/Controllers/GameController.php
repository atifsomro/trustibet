<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Game\EnterLimitedDrawAction;
use App\Actions\Game\PlaceColorBetAction;
use App\Actions\Game\PlayDiceAction;
use App\Actions\Game\PlayScratchCardAction;
use App\Actions\Game\SpinWheelAction;
use App\Enums\GameType;
use App\Exceptions\ColorRoundClosedException;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Game;
use App\Models\GamePackage;
use App\Models\GamePlay;
use App\Models\GameRound;
use App\Models\GameSession;
use App\Models\User;
use App\Services\Game\ColorPayoutSchedule;
use App\Services\Game\ColorRoundTicker;
use App\Services\Game\FreeSpinCounter;
use App\Services\Game\LimitedDrawService;
use App\Services\Game\PackageCreditLedger;
use App\Services\Wallet\WalletService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GameController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected ColorRoundTicker $colorRoundTicker,
        protected ColorPayoutSchedule $colorPayoutSchedule,
        protected FreeSpinCounter $freeSpinCounter,
        protected PackageCreditLedger $packageCredits,
        protected PlayScratchCardAction $playScratchCardAction,
        protected PlayDiceAction $playDiceAction,
        protected SpinWheelAction $spinWheelAction,
        protected PlaceColorBetAction $placeColorBetAction,
        protected EnterLimitedDrawAction $enterLimitedDrawAction,
        protected LimitedDrawService $limitedDrawService,
    ) {
    }

    public function show(string $slug)
    {
        $game = Game::query()
            ->active()
            ->where('slug', $slug)
            ->with([
                'activePackages.activePrizes',
            ])
            ->firstOrFail();

        if ($game->type === GameType::LIMITED_DRAW) {
            return redirect()->route('participate');
        }

        $user = auth()->user();
        $balances = $this->walletService->balances($user);

        $round = null;
        $payout = null;
        if ($game->type === GameType::COLOR_TRADING) {
            $round = $this->colorRoundTicker->ensureOpenRound($game);
            $payout = $this->colorPayoutSchedule->current($game);
        }

        $freeSpinsRemaining = 0;
        if ($game->type === GameType::WHEEL) {
            $freeSpinsRemaining = $this->freeSpinCounter->remaining($user, $game);
        }

        return view('pages.game', [
            'gameModel' => $game,
            'game' => [
                'title' => $game->title,
                'description' => $game->description,
            ],
            'slug' => $game->slug,
            'packages' => $game->activePackages,
            'balance' => (float) $balances['withdrawable'],
            'balances' => $balances,
            'round' => $round,
            'payout' => $payout,
            'minBet' => (float) $game->configValue('min_bet', 1),
            'freeSpinsRemaining' => $freeSpinsRemaining,
            'packageCredits' => $credits = $this->packageCreditMap($user, $game),
            'resumePackageId' => $this->resumePackageId($user, $game, $credits),
            'resumeNumber' => $this->resumeNumber($user, $game),
            'user' => $user,
        ]);
    }

    public function participate()
    {
        $game = $this->limitedDrawService->primary();

        if (! $game) {
            abort(404);
        }

        $user = auth()->user();
        $draw = $this->limitedDrawService->present($game, $user?->id);
        $balance = null;

        if ($user) {
            $balance = (float) $this->walletService->balances($user)['withdrawable'];
        }

        return view('pages.participate', [
            'draw' => $draw,
            'balance' => $balance,
            'user' => $user,
        ]);
    }

    public function play(Request $request, string $slug): JsonResponse
    {
        $game = Game::query()->active()->where('slug', $slug)->firstOrFail();
        $user = $request->user();

        $validated = $request->validate([
            'package_id' => ['required', 'integer', 'exists:game_packages,id'],
            'idempotency_key' => ['nullable', 'string', 'max:100'],
            'card' => ['nullable', 'integer', 'min:1'],
            'number' => ['nullable', 'integer', 'min:1'],
            'use_free_spin' => ['nullable', 'boolean'],
        ]);

        $package = GamePackage::query()
            ->where('id', $validated['package_id'])
            ->where('game_id', $game->id)
            ->where('is_active', true)
            ->firstOrFail();

        try {
            $play = match ($game->type) {
                GameType::SCRATCH_CARD => $this->playScratchCardAction->execute(
                    user: $user,
                    game: $game,
                    package: $package,
                    cardIndex: $validated['card'] ?? null,
                    idempotencyKey: $validated['idempotency_key'] ?? null,
                ),
                GameType::DICE => $this->playDiceAction->execute(
                    user: $user,
                    game: $game,
                    package: $package,
                    selectedNumber: (int) ($validated['number'] ?? 0),
                    idempotencyKey: $validated['idempotency_key'] ?? null,
                ),
                GameType::WHEEL => $this->spinWheelAction->execute(
                    user: $user,
                    game: $game,
                    package: $package,
                    useFreeSpin: (bool) ($validated['use_free_spin'] ?? false),
                    idempotencyKey: $validated['idempotency_key'] ?? null,
                ),
                GameType::LIMITED_DRAW => $this->enterLimitedDrawAction->execute(
                    user: $user,
                    game: $game,
                    package: $package,
                    idempotencyKey: $validated['idempotency_key'] ?? null,
                ),
                default => throw ValidationException::withMessages([
                    'game' => 'Use the bet endpoint for this game.',
                ]),
            };
        } catch (InsufficientBalanceException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $this->rememberSession($user, $game, $package->id, $play->selection ?? []);

        $balances = $this->walletService->balances($user);
        $freeSpinsRemaining = 0;

        if ($game->type === GameType::WHEEL) {
            $freeSpinsRemaining = $this->freeSpinCounter->remaining($user, $game);
        }

        $creditState = $this->packageCredits->isBundled($package)
            ? [
                'remaining' => $this->packageCredits->remaining($user, $package),
                'allowance' => $this->packageCredits->allowance($package),
            ]
            : null;

        return response()->json([
            'success' => true,
            'play' => $this->playPayload($play),
            'balance' => (float) $balances['withdrawable'],
            'free_spins_remaining' => $freeSpinsRemaining,
            'charged_amount' => (float) $play->fee_amount,
            'package_credits_remaining' => $creditState['remaining'] ?? null,
            'package_allowance' => $creditState['allowance'] ?? null,
        ]);
    }

    public function placeBet(Request $request, string $slug): JsonResponse
    {
        try {
            $game = Game::query()->active()->where('slug', $slug)->firstOrFail();

            if ($game->type !== GameType::COLOR_TRADING) {
                throw ValidationException::withMessages([
                    'game' => 'Betting is only available for color trading.',
                ]);
            }

            $minBet = (float) $game->configValue('min_bet', 1);

            $validated = $request->validate([
                'amount' => ['required', 'numeric', 'min:'.$minBet],
                'color' => ['required', 'string', 'max:40'],
                'mode' => ['nullable', 'string', 'in:dollar,percent'],
                'round_id' => ['nullable', 'integer'],
                'idempotency_key' => ['nullable', 'string', 'max:100'],
            ]);

            $round = $this->colorRoundTicker->roundForBet(
                $game,
                isset($validated['round_id']) ? (int) $validated['round_id'] : null,
            );

            $play = $this->commitColorBet($request->user(), $game, $round, $validated);
        } catch (InsufficientBalanceException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first() ?: $e->getMessage();

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        } catch (QueryException $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'The bet could not be saved. Please try again.',
            ], 503);
        }

        $balances = $this->walletService->balances($request->user());
        $placedRound = $play->round ?? $round->fresh();

        return response()->json([
            'success' => true,
            'play' => $this->playPayload($play),
            'round' => $placedRound ? $this->roundPayload($placedRound) : null,
            'payout' => $this->colorPayoutSchedule->current($game),
            'balance' => (float) $balances['withdrawable'],
        ]);
    }

    /**
     * Save the stake on the open round. If that round is drawn while the
     * request is in flight, the same stake is placed on the next round.
     *
     * @param  array<string, mixed>  $validated
     */
    protected function commitColorBet(User $user, Game $game, GameRound $round, array $validated): GamePlay
    {
        $idempotencyKey = $validated['idempotency_key'] ?? null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                return $this->placeColorBetAction->execute(
                    user: $user,
                    game: $game,
                    round: $round,
                    color: (string) $validated['color'],
                    amount: (float) $validated['amount'],
                    mode: (string) ($validated['mode'] ?? 'dollar'),
                    idempotencyKey: is_string($idempotencyKey) ? $idempotencyKey : null,
                );
            } catch (ColorRoundClosedException) {
                $round = $this->colorRoundTicker->ensureOpenRound($game);
            }
        }

        throw ValidationException::withMessages([
            'round' => 'Betting is closed for this round.',
        ]);
    }

    public function currentRound(string $slug): JsonResponse
    {
        $game = Game::query()->active()->where('slug', $slug)->firstOrFail();

        if ($game->type !== GameType::COLOR_TRADING) {
            abort(404);
        }

        $round = $this->colorRoundTicker->ensureOpenRound($game);
        $user = auth()->user();
        $balances = $this->walletService->balances($user);

        $myPending = GamePlay::query()
            ->where('user_id', $user->id)
            ->where('game_round_id', $round->id)
            ->get()
            ->map(fn (GamePlay $play) => $this->playPayload($play));

        $lastSettled = \App\Models\GameRound::query()
            ->where('game_id', $game->id)
            ->where('status', \App\Enums\GameRoundStatus::SETTLED)
            ->latest('round_number')
            ->first();

        $myLastRoundBets = collect();
        if ($lastSettled) {
            $myLastRoundBets = GamePlay::query()
                ->where('user_id', $user->id)
                ->where('game_round_id', $lastSettled->id)
                ->get()
                ->map(fn (GamePlay $play) => $this->playPayload($play));
        }

        return response()->json([
            'success' => true,
            'round' => $this->roundPayload($round),
            'payout' => $this->colorPayoutSchedule->current($game),
            'last_result' => $lastSettled?->result_color,
            'last_settled' => $lastSettled ? $this->roundPayload($lastSettled) : null,
            'my_bets' => $myPending,
            'my_last_round_bets' => $myLastRoundBets->values(),
            'balance' => (float) $balances['withdrawable'],
        ]);
    }

    public function remember(Request $request, string $slug): JsonResponse
    {
        $game = Game::query()->active()->where('slug', $slug)->firstOrFail();
        $validated = $request->validate([
            'package_id' => ['nullable', 'integer'],
            'number' => ['nullable', 'integer', 'min:1', 'max:36'],
        ]);

        $packageId = null;
        if (! empty($validated['package_id'])) {
            $packageId = GamePackage::query()
                ->where('id', $validated['package_id'])
                ->where('game_id', $game->id)
                ->where('is_active', true)
                ->value('id');
        }

        $selection = [];
        if (! empty($validated['number'])) {
            $selection['number'] = (int) $validated['number'];
        }

        $this->rememberSession($request->user(), $game, $packageId ? (int) $packageId : null, $selection);

        return response()->json(['success' => true]);
    }

    protected function rememberSession($user, Game $game, ?int $packageId, array $selection = []): void
    {
        $session = GameSession::query()->firstOrNew([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        if ($packageId) {
            $session->game_package_id = $packageId;
        }

        $current = $session->selection ?? [];
        if (isset($selection['number'])) {
            $current['number'] = (int) $selection['number'];
        }
        $session->selection = $current;
        $session->save();
    }

    protected function resumeNumber($user, Game $game): ?int
    {
        $session = GameSession::query()
            ->where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->first();

        $number = data_get($session?->selection, 'number');

        return $number ? (int) $number : null;
    }

    protected function packageCreditMap($user, Game $game): array
    {
        if (! in_array($game->type, [GameType::WHEEL, GameType::DICE], true)) {
            return [];
        }

        $map = [];

        foreach ($game->activePackages as $package) {
            if (! $this->packageCredits->isBundled($package)) {
                continue;
            }

            $map[$package->id] = [
                'remaining' => $this->packageCredits->remaining($user, $package),
                'allowance' => $this->packageCredits->allowance($package),
            ];
        }

        return $map;
    }

    protected function resumePackageId($user, Game $game, array $credits): ?int
    {
        $savedId = GameSession::query()
            ->where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->value('game_package_id');

        if ($savedId && $game->activePackages->contains('id', (int) $savedId)) {
            return (int) $savedId;
        }

        $openIds = collect($credits)
            ->filter(fn (array $row) => ($row['remaining'] ?? 0) > 0)
            ->keys()
            ->map(fn ($id) => (int) $id)
            ->all();

        $query = GamePlay::query()
            ->where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->latest('id');

        if ($openIds !== []) {
            $latestOpen = (clone $query)->whereIn('game_package_id', $openIds)->value('game_package_id');

            return $latestOpen ? (int) $latestOpen : $openIds[0];
        }

        $latest = $query->value('game_package_id');

        return $latest ? (int) $latest : null;
    }

    protected function playPayload(GamePlay $play): array
    {
        return [
            'uuid' => $play->uuid,
            'ticket_number' => $play->ticket_number,
            'status' => $play->status->value,
            'fee_amount' => (float) $play->fee_amount,
            'prize_amount' => (float) $play->prize_amount,
            'selection' => $play->selection,
            'outcome' => $play->outcome,
            'label' => data_get($play->outcome, 'label'),
            'reward' => data_get($play->outcome, 'label')
                ?? ('$'.number_format((float) $play->prize_amount, 2)),
        ];
    }

    protected function roundPayload(\App\Models\GameRound $round): array
    {
        $now = now();
        $secondsLeft = max(0, ($round->ends_at?->getTimestamp() ?? 0) - $now->getTimestamp());

        return [
            'id' => $round->id,
            'round_number' => $round->round_number,
            'status' => $round->status->value,
            'result_color' => $round->result_color,
            'server_seed_hash' => $round->server_seed_hash,
            'server_seed' => $round->revealServerSeed(),
            'fairness_roll' => $round->status === \App\Enums\GameRoundStatus::SETTLED
                ? $round->fairness_roll
                : null,
            'starts_at' => $round->starts_at?->toIso8601String(),
            'locks_at' => $round->locks_at?->toIso8601String(),
            'ends_at' => $round->ends_at?->toIso8601String(),
            'seconds_left' => (int) $secondsLeft,
            'betting_open' => $round->acceptsBets()
                && ($round->ends_at === null || $now->lt($round->ends_at)),
        ];
    }
}
