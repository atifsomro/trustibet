<?php

declare(strict_types=1);

namespace App\Actions\Game;

use App\Enums\BalanceType;
use App\Enums\GamePlayStatus;
use App\Enums\GameRoundStatus;
use App\Enums\WalletTransactionType;
use App\Models\Game;
use App\Models\GamePlay;
use App\Models\GamePrize;
use App\Models\GameRound;
use App\Models\User;
use App\Notifications\LimitedDrawResultNotification;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SettleLimitedDrawAction
{
    public function __construct(
        protected WalletService $walletService,
    ) {
    }

    public function execute(GameRound $round): GameRound
    {
        $settled = DB::transaction(function () use ($round) {
            $round = GameRound::query()
                ->lockForUpdate()
                ->with('game')
                ->findOrFail($round->id);

            if ($round->status === GameRoundStatus::SETTLED) {
                return [
                    'round' => $round,
                    'plays' => collect(),
                    'winner_ids' => [],
                    'prize_name' => '',
                    'currency' => '$',
                    'should_notify' => false,
                ];
            }

            $game = $round->game;
            $winnerCount = max(1, (int) $game->configValue('winner_count', 1));
            $prizeName = (string) $game->configValue('prize_name', $game->title);
            $currency = (string) $game->configValue('currency', '$');

            $plays = GamePlay::query()
                ->where('game_round_id', $round->id)
                ->where('status', GamePlayStatus::PENDING)
                ->with(['user', 'package'])
                ->lockForUpdate()
                ->get();

            $winners = $this->pickWinners($plays, $game, $winnerCount);
            $winnerIds = $winners->pluck('id')->all();

            foreach ($plays as $play) {
                $won = in_array($play->id, $winnerIds, true);
                $cashPrize = $won ? $this->cashPrize($play) : null;
                $prizeAmount = $cashPrize ? (float) $cashPrize->prize_amount : 0;
                $winTxnId = null;

                if ($won && $prizeAmount > 0) {
                    $winTxn = $this->walletService->creditWithTransaction(
                        user: $play->user,
                        balanceType: BalanceType::WITHDRAWABLE,
                        transactionType: WalletTransactionType::GAME_WIN,
                        amount: $prizeAmount,
                        reference: $play,
                        idempotencyKey: "game-win-{$play->uuid}",
                        meta: [
                            'game' => $game->slug,
                            'round_id' => $round->id,
                            'label' => $cashPrize->label,
                        ]
                    );
                    $winTxnId = $winTxn->id;
                }

                $play->update([
                    'game_prize_id' => $cashPrize?->id,
                    'prize_amount' => $prizeAmount,
                    'status' => $won ? GamePlayStatus::WON : GamePlayStatus::LOST,
                    'outcome' => [
                        'label' => $won ? $prizeName : 'Not selected',
                        'prize_amount' => $prizeAmount,
                        'forced' => $won && $this->isFavoritePlay($play, $game),
                    ],
                    'win_transaction_id' => $winTxnId,
                ]);
            }

            $round->update([
                'status' => GameRoundStatus::SETTLED,
                'result_color' => $prizeName,
                'settled_at' => now(),
            ]);

            // Clear favourite after it is consumed so the next round draws fairly unless set again.
            $this->clearFavoriteUser($game);

            return [
                'round' => $round->fresh(),
                'plays' => $plays,
                'winner_ids' => $winnerIds,
                'prize_name' => $prizeName,
                'currency' => $currency,
                'should_notify' => $plays->isNotEmpty(),
                'game' => $game->fresh(),
            ];
        });

        if ($settled['should_notify']) {
            $this->notifyParticipants(
                game: $settled['game'],
                round: $settled['round'],
                plays: $settled['plays'],
                winnerIds: $settled['winner_ids'],
                prizeName: $settled['prize_name'],
                currency: $settled['currency'],
            );
        }

        return $settled['round'];
    }

    /**
     * @param  Collection<int, GamePlay>  $plays
     * @return Collection<int, GamePlay>
     */
    protected function pickWinners(Collection $plays, Game $game, int $winnerCount): Collection
    {
        if ($plays->isEmpty()) {
            return collect();
        }

        $target = min($winnerCount, $plays->count());
        $selected = collect();
        $favoriteUserId = (int) $game->configValue('favorite_user_id', 0);

        if ($favoriteUserId > 0) {
            $favoritePlay = $plays->firstWhere('user_id', $favoriteUserId);

            if ($favoritePlay) {
                $selected->push($favoritePlay);
            }
        }

        $remaining = $target - $selected->count();

        if ($remaining > 0) {
            $selected = $selected->merge(
                $plays
                    ->reject(fn (GamePlay $play) => $selected->contains('id', $play->id))
                    ->shuffle()
                    ->take($remaining)
                    ->values()
            );
        }

        return $selected->values();
    }

    protected function isFavoritePlay(GamePlay $play, Game $game): bool
    {
        $favoriteUserId = (int) $game->configValue('favorite_user_id', 0);

        return $favoriteUserId > 0 && (int) $play->user_id === $favoriteUserId;
    }

    protected function clearFavoriteUser(Game $game): void
    {
        $config = $game->config ?? [];

        if (! array_key_exists('favorite_user_id', $config)) {
            return;
        }

        unset($config['favorite_user_id']);
        $game->update(['config' => $config]);
    }

    /**
     * @param  Collection<int, GamePlay>  $plays
     * @param  array<int, int>  $winnerIds
     */
    protected function notifyParticipants(
        Game $game,
        GameRound $round,
        Collection $plays,
        array $winnerIds,
        string $prizeName,
        string $currency,
    ): void {
        $winnerPlays = $plays->whereIn('id', $winnerIds)->values();
        $winnersPayload = $winnerPlays->map(fn (GamePlay $play) => [
            'user_id' => (int) $play->user_id,
            'name' => $play->user?->name,
            'username' => $play->user?->username,
        ])->unique('user_id')->values()->all();

        $plays
            ->groupBy('user_id')
            ->each(function (Collection $userPlays) use ($game, $round, $winnerIds, $winnerPlays, $winnersPayload, $prizeName, $currency) {
                /** @var GamePlay|null $first */
                $first = $userPlays->first();
                $user = $first?->user;

                if (! $user instanceof User) {
                    return;
                }

                $won = $userPlays->contains(
                    fn (GamePlay $play) => in_array($play->id, $winnerIds, true)
                );
                $prizeAmount = $won
                    ? (float) ($winnerPlays->firstWhere('user_id', $user->id)?->prize_amount ?? 0)
                    : 0;

                $user->notify(new LimitedDrawResultNotification(
                    game: $game,
                    round: $round,
                    result: $won ? 'won' : 'lost',
                    prizeName: $prizeName,
                    prizeAmount: $prizeAmount,
                    currency: $currency,
                    winners: $winnersPayload,
                ));
            });
    }

    protected function cashPrize(GamePlay $play): ?GamePrize
    {
        return $play->package
            ?->activePrizes()
            ->where('prize_amount', '>', 0)
            ->orderByDesc('prize_amount')
            ->first();
    }
}
