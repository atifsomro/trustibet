<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    public function show($slug)
    {
        $games = [
            'scratch-card' => [
                'title' => 'Scratch Card',
                'description' => 'Scratch and win exciting rewards.'
            ],
            'dice' => [
                'title' => 'Dice',
                'description' => 'Roll the dice and test your luck.'
            ],
            'wheel' => [
                'title' => 'Lucky Wheel',
                'description' => 'Spin the wheel and win exciting rewards.',
            ],
            'color-trading' => [
                'title' => 'Color Trading',
                'description' => 'Trade colors and win exciting rewards.'
            ],
        ];

        abort_unless(isset($games[$slug]), 404);

        return view('pages.game', [
            'game' => $games[$slug],
            'slug' => $slug,
        ]);
    }
}