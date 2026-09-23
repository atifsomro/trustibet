<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Lottery;
use App\Models\LotteryDraw;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LotteryNewRoundNotification extends Notification
{
    use Queueable;

    public string $roundDate;

    public function __construct(
        public Lottery $lottery,
        public LotteryDraw $previousDraw,
        public int $roundNumber,
        ?string $roundDate = null,
    ) {
        $this->roundDate = $roundDate ?? $lottery->currentRoundDate()->toDateString();
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
        return [
            'type' => 'lottery_new_round',
            'lottery_id' => $this->lottery->id,
            'draw_id' => $this->previousDraw->id,
            'round_number' => $this->roundNumber,
            'round_date' => $this->roundDate,
            'title' => 'New round started',
            'message' => sprintf(
                'Round %d · %s of %s is now open. Buy your ticket before the countdown ends.',
                $this->roundNumber,
                \Illuminate\Support\Carbon::parse($this->roundDate)->format('d M Y'),
                $this->lottery->title
            ),
        ];
    }
}
