<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Lottery;
use App\Models\LotteryDraw;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LotteryResultNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Lottery $lottery,
        public LotteryDraw $draw,
        public string $result,
        public ?string $prizeTier = null,
        public ?float $prizeAmount = null,
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
        $tierLabel = $this->prizeTierLabel();

        return [
            'type' => 'lottery_result',
            'lottery_id' => $this->lottery->id,
            'draw_id' => $this->draw->id,
            'result' => $this->result,
            'prize_tier' => $this->prizeTier,
            'prize_label' => $tierLabel,
            'prize_amount' => $this->prizeAmount,
            'currency' => $this->lottery->currency,
            'title' => $won
                ? 'You won '.$this->lottery->title
                : $this->lottery->title.' result',
            'message' => $won
                ? sprintf(
                    'Congratulations! You won the %s (%s %s).',
                    $tierLabel ?? 'prize',
                    $this->lottery->currency,
                    number_format((float) $this->prizeAmount, 2)
                )
                : 'The draw is complete. This time you did not win a prize.',
        ];
    }

    protected function prizeTierLabel(): ?string
    {
        return match ($this->prizeTier) {
            'first' => '1st Prize',
            'second' => '2nd Prize',
            'third' => '3rd Prize',
            'fourth' => '4th Prize',
            'fifth' => '5th Prize',
            default => null,
        };
    }
}
