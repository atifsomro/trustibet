<?php

declare(strict_types=1);

namespace App\Actions\Game;

use App\Enums\BalanceType;
use App\Enums\GamePlayStatus;
use App\Enums\GameRoundStatus;
use App\Enums\WalletTransactionType;
use App\Models\GamePlay;
use App\Models\GamePrize;
use App\Models\GameRound;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;

class SettleLimitedDrawAction
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

            $game = $round->game;
            $winnerCount = max(1, (int) $game->configValue('winner_count', 1));
            $prizeName = (string) $game->configValue('prize_name', $game->title);

            $plays = GamePlay::query()
                ->where('game_round_id', $round->id)
                ->where('status', GamePlayStatus::PENDING)
                ->with(['user', 'package'])
                ->lockForUpdate()
                ->get();

            $winners = $plays->shuffle()->take(min($winnerCount, $plays->count()));
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
                    ],
                    'win_transaction_id' => $winTxnId,
                ]);
            }

            $round->update([
                'status' => GameRoundStatus::SETTLED,
                'result_color' => $prizeName,
                'settled_at' => now(),
            ]);

            return $round->fresh();
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
