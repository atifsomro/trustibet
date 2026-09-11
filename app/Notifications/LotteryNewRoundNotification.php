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

    public function __construct(
        public Lottery $lottery,
        public LotteryDraw $previousDraw,
        public int $roundNumber,
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
        return [
            'type' => 'lottery_new_round',
            'lottery_id' => $this->lottery->id,
            'draw_id' => $this->previousDraw->id,
            'round_number' => $this->roundNumber,
            'title' => 'New round started',
            'message' => sprintf(
                'A new round of %s is now open. Buy your ticket before the countdown ends.',
                $this->lottery->title
            ),
        ];
    }
}
