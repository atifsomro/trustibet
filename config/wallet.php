<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Welcome Bonus
    |--------------------------------------------------------------------------
    */

    'welcome_bonus' => [

        'enabled' => true,

        /*
         * Amount stored in USD.
         */
        'amount' => 50.00,

        /*
         * Null means never expires.
         */
        'expires_days' => null,

    ],

    /*
    |--------------------------------------------------------------------------
    | Referral Bonus
    |--------------------------------------------------------------------------
    |
    | Overridden by admin settings (config('settings.referral_*')) when set.
    |
    */

    'referral' => [

        'enabled' => true,

        /*
         * Percentage of the invitee's first approved deposit.
         */
        'bonus_percent' => 5,

    ],

];
