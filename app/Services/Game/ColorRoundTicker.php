<?php

declare(strict_types=1);

namespace App\Services\Game;

use App\Actions\Game\SettleColorRoundAction;
use App\Enums\GameRoundStatus;
use App\Enums\GameType;
use App\Models\Game;
use App\Models\GameRound;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ColorRoundTicker
{
    public function __construct(
        protected SettleColorRoundAction $settleColorRoundAction,
    ) {
    }

    public function tick(): int
    {
        $processed = 0;

        $games = Game::query()
            ->active()
            ->where('type', GameType::COLOR_TRADING)
            ->get();

        foreach ($games as $game) {
            $processed += $this->tickGame($game);
        }

        return $processed;
    }

    protected function tickGame(Game $game): int
    {
        $processed = $this->advanceEndedRounds($game);

        $hasOpen = GameRound::query()
            ->where('game_id', $game->id)
            ->whereIn('status', [
                GameRoundStatus::BETTING,
                GameRoundStatus::LOCKED,
            ])
            ->exists();

        if (! $hasOpen) {
            $this->createRound($game);
            $processed++;
        }

        return $processed;
    }

    /**
     * The round a new stake should join. Prefer the round the player tapped.
     * Never settle or lock that round before the stake is saved.
     */
    public function roundForBet(Game $game, ?int $roundId): GameRound
    {
        if ($roundId) {
            $requested = GameRound::query()
                ->where('game_id', $game->id)
                ->find($roundId);

            if ($requested && $requested->acceptsBets() && ! $this->roundClockEnded($requested)) {
                return $requested;
            }
        }

        $open = GameRound::query()
            ->where('game_id', $game->id)
            ->whereIn('status', [
                GameRoundStatus::BETTING,
                GameRoundStatus::LOCKED,
            ])
            ->orderByDesc('round_number')
            ->first();

        if ($open && $open->acceptsBets() && ! $this->roundClockEnded($open)) {
            return $open;
        }

        return $this->ensureOpenRound($game);
    }

    protected function roundClockEnded(GameRound $round): bool
    {
        return $round->ends_at !== null && now()->gte($round->ends_at);
    }

    public function ensureOpenRound(Game $game): GameRound
    {
        $this->advanceEndedRounds($game);

        $open = GameRound::query()
            ->where('game_id', $game->id)
            ->whereIn('status', [
                GameRoundStatus::BETTING,
                GameRoundStatus::LOCKED,
            ])
            ->orderByDesc('round_number')
            ->first();

        if ($open) {
            if (! $open->server_seed || ! $open->server_seed_hash) {
                $open->server_seed = bin2hex(random_bytes(32));
                $open->server_seed_hash = hash('sha256', $open->server_seed);
                $open->save();
            }

            return $open;
        }

        return $this->createRound($game);
    }

    protected function advanceEndedRounds(Game $game): int
    {
        $lock = null;
        $holdsLock = false;

        try {
            // Polls and bets all try to settle the same round. One request does the work;
            // the rest continue immediately instead of waiting on the round row.
            $lock = Cache::lock('color-round-advance:'.$game->id, 30);
            $holdsLock = $lock->get();
        } catch (\Throwable) {
            return $this->settleDueRounds($game);
        }

        if (! $holdsLock) {
            return 0;
        }

        try {
            return $this->settleDueRounds($game);
        } finally {
            $lock->release();
        }
    }

    protected function settleDueRounds(Game $game): int
    {
        $processed = 0;
        $now = now();

        $openRounds = GameRound::query()
            ->where('game_id', $game->id)
            ->whereIn('status', [
                GameRoundStatus::BETTING,
                GameRoundStatus::LOCKED,
            ])
            ->orderBy('round_number')
            ->get();

        foreach ($openRounds as $round) {
            if (
                $round->status === GameRoundStatus::BETTING
                && $now->gte($round->locks_at)
            ) {
                $round->status = GameRoundStatus::LOCKED;
                $round->save();
                $processed++;
            }

            if (
                in_array($round->status, [GameRoundStatus::BETTING, GameRoundStatus::LOCKED], true)
                && $now->gte($round->ends_at)
            ) {
                if ($round->status === GameRoundStatus::BETTING) {
                    $round->status = GameRoundStatus::LOCKED;
                    $round->save();
                }

                $this->settleColorRoundAction->execute($round);
                $processed++;
            }
        }

        return $processed;
    }

    protected function createRound(Game $game): GameRound
    {
        return DB::transaction(function () use ($game) {
            // Serialize round creation per game to avoid two open rounds.
            Game::query()->whereKey($game->id)->lockForUpdate()->first();

            $existing = GameRound::query()
                ->where('game_id', $game->id)
                ->whereIn('status', [
                    GameRoundStatus::BETTING,
                    GameRoundStatus::LOCKED,
                ])
                ->orderByDesc('round_number')
                ->first();

            if ($existing) {
                if (! $existing->server_seed || ! $existing->server_seed_hash) {
                    $existing->server_seed = bin2hex(random_bytes(32));
                    $existing->server_seed_hash = hash('sha256', $existing->server_seed);
                    $existing->save();
                }

                return $existing;
            }

            $lastNumber = (int) GameRound::query()
                ->where('game_id', $game->id)
                ->max('round_number');

            $roundSeconds = max(5, (int) $game->configValue('round_seconds', 10));
            $lockSeconds = max(1, (int) $game->configValue('lock_seconds', 2));
            $lockSeconds = min($lockSeconds, $roundSeconds - 1);

            $startsAt = now();
            $locksAt = $startsAt->copy()->addSeconds($roundSeconds - $lockSeconds);
            $endsAt = $startsAt->copy()->addSeconds($roundSeconds);

            $serverSeed = bin2hex(random_bytes(32));

            return GameRound::create([
                'game_id' => $game->id,
                'round_number' => $lastNumber + 1,
                'status' => GameRoundStatus::BETTING,
                'server_seed' => $serverSeed,
                'server_seed_hash' => hash('sha256', $serverSeed),
                'starts_at' => $startsAt,
                'locks_at' => $locksAt,
                'ends_at' => $endsAt,
            ]);
        });
    }
}
