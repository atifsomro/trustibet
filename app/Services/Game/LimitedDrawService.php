<?php

declare(strict_types=1);

namespace App\Services\Game;

use App\Actions\Game\SettleLimitedDrawAction;
use App\Enums\GamePlayStatus;
use App\Enums\GameRoundStatus;
use App\Enums\GameType;
use App\Models\Game;
use App\Models\GameRound;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LimitedDrawService
{
    public function __construct(
        protected SettleLimitedDrawAction $settleLimitedDrawAction,
    ) {
    }

    public function primary(): ?Game
    {
        return Game::query()
            ->active()
            ->where('type', GameType::LIMITED_DRAW)
            ->with(['activePackages.activePrizes'])
            ->orderByDesc('is_featured')
            ->ordered()
            ->first();
    }

    public function tick(): int
    {
        $processed = 0;

        $games = Game::query()
            ->active()
            ->where('type', GameType::LIMITED_DRAW)
            ->get();

        foreach ($games as $game) {
            $before = $this->openRound($game);
            $round = $this->sync($game);

            if ($before && $round && $before->id === $round->id && $before->status !== $round->status) {
                $processed++;
            } elseif ($before && $round && $before->status !== GameRoundStatus::SETTLED && $round->status === GameRoundStatus::SETTLED) {
                $processed++;
            }
        }

        return $processed;
    }

    public function sync(Game $game): ?GameRound
    {
        if ($game->type !== GameType::LIMITED_DRAW) {
            return null;
        }

        $this->settleDue($game);

        $drawAt = $this->drawAt($game);

        if (! $drawAt || $drawAt->lte(now())) {
            return $this->latestRound($game);
        }

        $open = $this->openRound($game);

        if ($open) {
            $open->ends_at = $drawAt;
            $open->locks_at = $drawAt;
            $open->save();

            return $open->fresh();
        }

        $alreadySettled = GameRound::query()
            ->where('game_id', $game->id)
            ->where('status', GameRoundStatus::SETTLED)
            ->where('ends_at', $drawAt)
            ->exists();

        if ($alreadySettled) {
            return $this->latestRound($game);
        }

        return $this->createRound($game, $drawAt);
    }

    public function present(Game $game, ?int $userId = null): array
    {
        $game->loadMissing(['activePackages.activePrizes']);
        $round = $this->sync($game);
        $package = $game->activePackages->first();
        $entries = $round
            ? $round->plays()->count()
            : 0;
        $maxEntries = max(0, (int) $game->configValue('max_entries', 0));
        $remaining = $maxEntries > 0 ? max(0, $maxEntries - $entries) : null;
        $percent = $maxEntries > 0
            ? (int) min(100, round(($entries / $maxEntries) * 100))
            : 0;
        $fee = $package ? (float) $package->fee : 0;
        $currency = (string) $game->configValue('currency', 'Rs.');
        $open = $round
            && $round->status === GameRoundStatus::BETTING
            && $round->ends_at
            && now()->lt($round->ends_at)
            && ($maxEntries === 0 || $entries < $maxEntries);
        $userEntries = 0;

        if ($userId && $round) {
            $userEntries = $round->plays()
                ->where('user_id', $userId)
                ->count();
        }

        $maxPerUser = max(1, (int) $game->configValue('max_per_user', 1));

        return [
            'game' => $game,
            'round' => $round,
            'package' => $package,
            'prize_name' => (string) $game->configValue('prize_name', $game->title),
            'prize_value' => (float) $game->configValue('prize_value', 0),
            'prize_image' => $this->publicUrl((string) $game->configValue('prize_image', '')),
            'headline' => (string) ($game->configValue('headline') ?: $game->title),
            'currency' => $currency,
            'fee' => $fee,
            'fee_label' => trim($currency.' '. $this->money($fee)),
            'entries' => $entries,
            'max_entries' => $maxEntries,
            'remaining' => $remaining,
            'percent' => $percent,
            'winner_count' => max(1, (int) $game->configValue('winner_count', 1)),
            'ends_at' => $round?->ends_at,
            'is_open' => $open,
            'user_entries' => $userEntries,
            'can_join' => $open && $package && $userEntries < $maxPerUser,
            'already_joined' => $userEntries >= $maxPerUser,
        ];
    }

    public function drawAt(Game $game): ?Carbon
    {
        $value = $game->configValue('draw_at');

        if (! $value) {
            return null;
        }

        return Carbon::parse($value);
    }

    public function publicUrl(string $path): string
    {
        if ($path === '') {
            return asset('images/draw/prize.png');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    public function money(float $amount): string
    {
        $formatted = number_format($amount, 2);

        return str_ends_with($formatted, '.00')
            ? number_format($amount, 0)
            : $formatted;
    }

    protected function settleDue(Game $game): void
    {
        $rounds = GameRound::query()
            ->where('game_id', $game->id)
            ->whereIn('status', [GameRoundStatus::BETTING, GameRoundStatus::LOCKED])
            ->where('ends_at', '<=', now())
            ->orderBy('round_number')
            ->get();

        foreach ($rounds as $round) {
            $this->settleLimitedDrawAction->execute($round);
        }
    }

    protected function openRound(Game $game): ?GameRound
    {
        return GameRound::query()
            ->where('game_id', $game->id)
            ->whereIn('status', [GameRoundStatus::BETTING, GameRoundStatus::LOCKED])
            ->orderByDesc('round_number')
            ->first();
    }

    protected function latestRound(Game $game): ?GameRound
    {
        return GameRound::query()
            ->where('game_id', $game->id)
            ->orderByDesc('round_number')
            ->first();
    }

    protected function createRound(Game $game, Carbon $drawAt): GameRound
    {
        return DB::transaction(function () use ($game, $drawAt) {
            $lastNumber = (int) GameRound::query()
                ->where('game_id', $game->id)
                ->lockForUpdate()
                ->max('round_number');

            return GameRound::create([
                'game_id' => $game->id,
                'round_number' => $lastNumber + 1,
                'status' => GameRoundStatus::BETTING,
                'starts_at' => now(),
                'locks_at' => $drawAt,
                'ends_at' => $drawAt,
            ]);
        });
    }
}
