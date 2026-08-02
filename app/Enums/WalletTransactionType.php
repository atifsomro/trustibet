<?php

declare(strict_types=1);

namespace App\Enums;

enum WalletTransactionType: string
{
    /**
     * Bonus
     */
    case BONUS_GRANTED = 'bonus_granted';
    case BONUS_EXPIRED = 'bonus_expired';
    case BONUS_VOIDED = 'bonus_voided';

    /**
     * Game
     */
    case GAME_BET = 'game_bet';
    case GAME_WIN = 'game_win';

    /**
     * Deposit
     */
    case DEPOSIT = 'deposit';

    /**
     * Withdrawals
     */
    case WITHDRAW_REQUEST = 'withdraw_request';
    case WITHDRAW_APPROVED = 'withdraw_approved';
    case WITHDRAW_REJECTED = 'withdraw_rejected';
    case WITHDRAW_CANCELLED = 'withdraw_cancelled';

    /**
     * Admin
     */
    case ADMIN_CREDIT = 'admin_credit';
    case ADMIN_DEBIT = 'admin_debit';

    /**
     * Get all values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Credit transaction?
     */
    public function isCredit(): bool
    {
        return in_array($this, [
            self::BONUS_GRANTED,
            self::GAME_WIN,
            self::DEPOSIT,
            self::ADMIN_CREDIT,
        ], true);
    }

    /**
     * Debit transaction?
     */
    public function isDebit(): bool
    {
        return in_array($this, [
            self::GAME_BET,
            self::WITHDRAW_REQUEST,
            self::ADMIN_DEBIT,
            self::BONUS_EXPIRED,
            self::BONUS_VOIDED,
        ], true);
    }

    /**
     * Human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::BONUS_GRANTED => 'Bonus Granted',
            self::BONUS_EXPIRED => 'Bonus Expired',
            self::BONUS_VOIDED => 'Bonus Voided',

            self::GAME_BET => 'Game Bet',
            self::GAME_WIN => 'Game Win',

            self::DEPOSIT => 'Deposit',

            self::WITHDRAW_REQUEST => 'Withdrawal Requested',
            self::WITHDRAW_APPROVED => 'Withdrawal Approved',
            self::WITHDRAW_REJECTED => 'Withdrawal Rejected',
            self::WITHDRAW_CANCELLED => 'Withdrawal Cancelled',

            self::ADMIN_CREDIT => 'Admin Credit',
            self::ADMIN_DEBIT => 'Admin Debit',
        };
    }
}