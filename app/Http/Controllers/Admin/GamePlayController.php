<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GamePlay;
use Illuminate\Http\Request;

class GamePlayController extends Controller
{
    public function index(Request $request)
    {
        $query = GamePlay::query()
            ->with(['user', 'game', 'package', 'prize', 'round'])
            ->latest();

        if ($request->filled('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('uuid', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'LIKE', "%{$search}%")
                            ->orWhere('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $plays = $query->paginate(30)->withQueryString();

        return view('admin.game_plays.index', [
            'plays' => $plays,
            'games' => \App\Models\Game::ordered()->get(['id', 'title']),
            'active' => 'game-plays',
        ]);
    }
}
