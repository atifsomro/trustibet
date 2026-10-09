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

    /*
    |--------------------------------------------------------------------------
    | Transaction Reference
    |--------------------------------------------------------------------------
    |
    | Public bank-style reference stored in wallet_transactions.uuid.
    | Example: TBX-260309-A7K2M9XQ
    |
    */

    'transaction_reference' => [

        'prefix' => env('WALLET_TX_PREFIX', 'TBX'),

    ],

];
