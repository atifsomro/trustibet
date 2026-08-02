<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasWallet
{
    /**
     * User wallet relationship.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Check whether the user has a wallet.
     */
    public function hasWallet(): bool
    {
        return $this->wallet()->exists();
    }

    /**
     * Get the user's wallet.
     */
    public function getWallet(): ?Wallet
    {
        return $this->wallet()->first();
    }
}