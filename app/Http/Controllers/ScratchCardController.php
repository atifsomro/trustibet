<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
class ScratchCardController extends Controller
{
    public function reveal(Request $request)
    {
        $rewards = [
            '$0',
            '$2',
            '$5',
            '$10',
            'FREE SCRATCH',
            '$0',
            '$0',
        ];
        return response()->json([
            'success' => true,
            'reward' => $rewards[array_rand($rewards)]
        ]);
    }
}