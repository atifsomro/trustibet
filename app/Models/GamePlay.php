<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GamePlayStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamePlay extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'game_id',
        'game_package_id',
        'game_round_id',
        'game_prize_id',
        'fee_amount',
        'prize_amount',
        'status',
        'selection',
        'outcome',
        'bet_transaction_id',
        'win_transaction_id',
        'idempotency_key',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'prize_amount' => 'decimal:2',
        'status' => GamePlayStatus::class,
        'selection' => 'array',
        'outcome' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(GamePackage::class, 'game_package_id');
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(GameRound::class, 'game_round_id');
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(GamePrize::class, 'game_prize_id');
    }

    public function betTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'bet_transaction_id');
    }

    public function winTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'win_transaction_id');
    }
}
