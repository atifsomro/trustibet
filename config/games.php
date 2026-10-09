<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Game Ticket Reference
    |--------------------------------------------------------------------------
    |
    | Public bank-style ticket stored in game_plays.uuid.
    | Example: TKT-260309-A7K2M9XQ
    |
    */

    'ticket_reference' => [

        'prefix' => env('GAME_TICKET_PREFIX', 'TKT'),

    ],

];
