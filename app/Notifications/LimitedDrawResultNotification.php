<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Game;
use App\Models\GameRound;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LimitedDrawResultNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<int, array{user_id:int,name:?string,username:?string}>  $winners
     */
    public function __construct(
        public Game $game,
        public GameRound $round,
        public string $result,
        public string $prizeName,
        public float $prizeAmount,
        public string $currency,
        public array $winners = [],
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $won = $this->result === 'won';
        $winnerNames = collect($this->winners)
            ->map(fn (array $winner) => $winner['name'] ?: ($winner['username'] ?: 'Winner'))
            ->filter()
            ->unique()
            ->values();

        $winnerLabel = $winnerNames->isEmpty()
            ? 'a lucky winner'
            : ($winnerNames->count() === 1
                ? $winnerNames->first()
                : $winnerNames->join(', ', ' and '));

        $prizeLabel = $this->prizeAmount > 0
            ? trim($this->currency.' '.number_format($this->prizeAmount, 2))
            : $this->prizeName;

        return [
            'type' => 'limited_draw_result',
            'game_id' => $this->game->id,
            'game_slug' => $this->game->slug,
            'round_id' => $this->round->id,
            'round_number' => $this->round->round_number,
            'result' => $this->result,
            'prize_name' => $this->prizeName,
            'prize_amount' => $this->prizeAmount,
            'currency' => $this->currency,
            'winners' => $this->winners,
            'title' => $won
                ? 'You won '.$this->prizeName
                : $this->prizeName.' draw result',
            'message' => $won
                ? sprintf('Congratulations! You won %s.', $prizeLabel)
                : sprintf('%s won %s. Better luck next time!', $winnerLabel, $prizeLabel),
        ];
    }
}
