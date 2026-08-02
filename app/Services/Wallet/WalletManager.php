<?php

declare(strict_types=1);

namespace App\Services\Wallet;

use App\Exceptions\WalletNotFoundException;
use App\Models\User;
use App\Models\Wallet;

class WalletManager
{
    /**
     * Find wallet by ID.
     *
     * @throws WalletNotFoundException
     */
    public function find(int $walletId): Wallet
    {
        $wallet = Wallet::query()->find($walletId);

        if (! $wallet) {
            throw new WalletNotFoundException();
        }

        return $wallet;
    }

    /**
     * Find wallet by user.
     *
     * @throws WalletNotFoundException
     */
    public function findByUser(User $user): Wallet
    {
        $wallet = Wallet::query()
            ->where('user_id', $user->id)
            ->first();

        if (! $wallet) {
            throw new WalletNotFoundException();
        }

        return $wallet;
    }

    /**
     * Get or create a wallet for the given user.
     */
    public function getOrCreate(User $user): Wallet
    {
        $wallet = Wallet::query()
            ->where('user_id', $user->id)
            ->first();

        if ($wallet instanceof Wallet) {
            return $wallet;
        }

        return $this->create($user);
    }

    /**
     * Create a new wallet.
     */
    public function create(User $user): Wallet
    {
        $wallet = new Wallet();

        $wallet->user_id = $user->id;
        $wallet->withdrawable_balance = 0;
        $wallet->bonus_balance = 0;
        $wallet->locked_balance = 0;
        $wallet->currency = 'USD';
        $wallet->version = 1;

        $wallet->save();

        return $wallet;
    }

    /**
     * Reload the latest wallet data.
     */
    public function refresh(Wallet $wallet): Wallet
    {
        return $wallet->fresh();
    }

    /**
     * Check if a wallet exists for the user.
     */
    public function exists(User $user): bool
    {
        return Wallet::query()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Get the user's withdrawable balance.
     */
    public function withdrawableBalance(User $user): int
    {
        return $this->findByUser($user)->withdrawable_balance;
    }

    /**
     * Get the user's bonus balance.
     */
    public function bonusBalance(User $user): int
    {
        return $this->findByUser($user)->bonus_balance;
    }

    /**
     * Get the user's locked balance.
     */
    public function lockedBalance(User $user): int
    {
        return $this->findByUser($user)->locked_balance;
    }

    /**
     * Get total balance.
     */
    public function totalBalance(User $user): int
    {
        $wallet = $this->findByUser($user);

        return $wallet->withdrawable_balance
            + $wallet->bonus_balance
            + $wallet->locked_balance;
    }
}