<?php

declare(strict_types=1);

namespace App\Services\Game;

use App\Actions\Game\SettleColorRoundAction;
use App\Enums\GameRoundStatus;
use App\Enums\GameType;
use App\Models\Game;
use App\Models\GameRound;
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
            return $open;
        }

        return $this->createRound($game);
    }

    protected function advanceEndedRounds(Game $game): void
    {
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
            }
        }
    }

    protected function createRound(Game $game): GameRound
    {
        return DB::transaction(function () use ($game) {
            $lastNumber = (int) GameRound::query()
                ->where('game_id', $game->id)
                ->lockForUpdate()
                ->max('round_number');

            $roundSeconds = max(5, (int) $game->configValue('round_seconds', 10));
            $lockSeconds = max(1, (int) $game->configValue('lock_seconds', 5));
            $lockSeconds = min($lockSeconds, $roundSeconds - 1);

            $startsAt = now();
            $locksAt = $startsAt->copy()->addSeconds($roundSeconds - $lockSeconds);
            $endsAt = $startsAt->copy()->addSeconds($roundSeconds);

            return GameRound::create([
                'game_id' => $game->id,
                'round_number' => $lastNumber + 1,
                'status' => GameRoundStatus::BETTING,
                'starts_at' => $startsAt,
                'locks_at' => $locksAt,
                'ends_at' => $endsAt,
            ]);
        });
    }
}
