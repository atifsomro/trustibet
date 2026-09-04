<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ROI → Withdrawable Transfer Limits
    |--------------------------------------------------------------------------
    |
    | Users claim daily ROI into a separate ROI balance, then may transfer
    | funds to withdrawable balance subject to these limits (app timezone).
    |
    */

    'roi_transfer' => [

        'daily_limit' => (float) env('INVESTMENT_ROI_DAILY_TRANSFER_LIMIT', 50.00),

        'min_amount' => (float) env('INVESTMENT_ROI_TRANSFER_MIN', 1.00),

    ],

];
