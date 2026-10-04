<?php

declare(strict_types=1);

namespace App\Actions\Game;

use App\Enums\BalanceType;
use App\Enums\GamePlayStatus;
use App\Enums\GameRoundStatus;
use App\Enums\WalletTransactionType;
use App\Models\GamePlay;
use App\Models\GameRound;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;

class SettleColorRoundAction
{
    public function __construct(
        protected WalletService $walletService,
    ) {
    }

    public function execute(GameRound $round): GameRound
    {
        return DB::transaction(function () use ($round) {
            $round = GameRound::query()
                ->lockForUpdate()
                ->with('game')
                ->findOrFail($round->id);

            if ($round->status === GameRoundStatus::SETTLED) {
                return $round;
            }

            if (! $round->server_seed) {
                $round->server_seed = bin2hex(random_bytes(32));
                $round->server_seed_hash = hash('sha256', $round->server_seed);
            }

            $picked = $round->result_color
                ? ['color' => $round->result_color, 'roll' => $round->fairness_roll]
                : $this->pickResultColor($round);

            $round->result_color = $picked['color'];
            $round->fairness_roll = $picked['roll'];
            $round->status = GameRoundStatus::SETTLED;
            $round->settled_at = now();
            $round->save();

            $resultColor = $round->result_color;

            $plays = GamePlay::query()
                ->where('game_round_id', $round->id)
                ->where('status', GamePlayStatus::PENDING)
                ->with(['user', 'package.activePrizes'])
                ->lockForUpdate()
                ->get();

            foreach ($plays as $play) {
                $selected = strtolower((string) data_get($play->selection, 'color', ''));
                $won = $selected !== '' && $selected === strtolower($resultColor);
                $prizeAmount = 0.0;
                $prizeId = null;
                $winTxnId = null;
                $label = 'Lose';

                if ($won) {
                    $selectionMultiplier = (float) data_get($play->selection, 'payout_multiplier', 0);

                    if ($selectionMultiplier > 0) {
                        $prizeAmount = round((float) $play->fee_amount * $selectionMultiplier, 2);
                        $rate = data_get($play->selection, 'payout_rate');
                        $label = $rate !== null
                            ? "{$rate}% Win"
                            : number_format($selectionMultiplier, 2).'x Win';
                    } else {
                        $winPrize = $play->package?->winPrize();
                        $multiplier = (float) ($play->package?->metaValue('multiplier', 2) ?? 2);

                        if ($winPrize) {
                            $prizeAmount = (float) $winPrize->prize_amount;
                            $prizeId = $winPrize->id;
                            $label = $winPrize->label;
                        } else {
                            $prizeAmount = round((float) $play->fee_amount * $multiplier, 2);
                            $label = "{$multiplier}x Win";
                        }
                    }

                    if ($prizeAmount > 0) {
                        $winTxn = $this->walletService->creditWithTransaction(
                            user: $play->user,
                            balanceType: BalanceType::WITHDRAWABLE,
                            transactionType: WalletTransactionType::GAME_WIN,
                            amount: $prizeAmount,
                            reference: $play,
                            idempotencyKey: 'game-win-'.$play->uuid,
                            meta: [
                                'game' => $round->game->slug,
                                'round_id' => $round->id,
                                'result_color' => $resultColor,
                            ]
                        );
                        $winTxnId = $winTxn->id;
                    }
                }

                $play->update([
                    'game_prize_id' => $prizeId,
                    'prize_amount' => $prizeAmount,
                    'status' => $won && $prizeAmount > 0
                        ? GamePlayStatus::WON
                        : GamePlayStatus::LOST,
                    'outcome' => [
                        'result_color' => $resultColor,
                        'selected' => $selected,
                        'label' => $label,
                        'prize_amount' => $prizeAmount,
                        'fairness_roll' => $round->fairness_roll,
                        'server_seed_hash' => $round->server_seed_hash,
                    ],
                    'win_transaction_id' => $winTxnId,
                ]);
            }

            return $round->fresh();
        });
    }

    /**
     * @return array{color: string, roll: int|null}
     */
    protected function pickResultColor(GameRound $round): array
    {
        $game = $round->game;

        // Color trading results must come from the game's configured colors
        // (equal chance). Prize weights on packages are for payouts only —
        // aggregating them skewed outcomes when packages were misconfigured.
        $colors = collect($game->configValue('colors', [
            'green', 'red', 'blue', 'yellow', 'orange',
            'purple', 'pink', 'cyan', 'white', 'black',
        ]))
            ->map(fn ($color) => strtolower(trim((string) $color)))
            ->filter()
            ->values();

        if ($colors->isEmpty()) {
            return ['color' => 'green', 'roll' => 1];
        }

        $roll = $this->rollFromSeed($round, $colors->count());
        $index = $roll - 1;

        return [
            'color' => (string) $colors[$index],
            'roll' => $roll,
        ];
    }

    protected function rollFromSeed(GameRound $round, int $total): int
    {
        $seed = (string) $round->server_seed;
        $message = $round->id.':'.$round->round_number;
        $digest = hash_hmac('sha256', $message, $seed);
        $value = hexdec(substr($digest, 0, 8));

        return ($value % max(1, $total)) + 1;
    }
}
