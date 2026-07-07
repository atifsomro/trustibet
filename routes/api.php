<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/games', function () {

    $response = Http::withoutVerifying()
        ->get('https://www.freetogame.com/api/games');

    return response()->json($response->json());

});
