<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Leaderboard\LeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __construct(private readonly LeaderboardService $leaderboard)
    {
    }

    public function index(): View
    {
        return view('pages.leaderboard.index', [
            'board' => $this->leaderboard->today(),
        ]);
    }

    /**
     * Popup details. Looked up by today's rank (not user id) so only users
     * currently on the board can be viewed and no internal ids are exposed.
     */
    public function show(int $rank): JsonResponse
    {
        abort_unless($rank >= 1 && $rank <= LeaderboardService::LIMIT, 404);

        $details = $this->leaderboard->detailsByRank($rank);

        abort_if($details === null, 404);

        return response()->json($details);
    }
}
