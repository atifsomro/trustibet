<?php

namespace App\Observers;

use App\Mail\UserVerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Social sign-ups (Google, etc.) arrive already verified by the
        // provider - there's nothing for the user to confirm, so don't
        // send the verification email.
        if ($user->isSocialAccount() || $user->email_verified_at !== null) {
            return;
        }

        Mail::to($user->email)
            ->send(new UserVerificationMail($user));
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}