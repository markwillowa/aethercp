<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Ranking\RankingEngineService;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function __construct(
        protected RankingEngineService $rankingEngine
    ) {}

    public function index(Request $request)
    {
        $rawType = (string) $request->query('type', 'lvl');
        $type = $this->rankingEngine->normalizeCategory($rawType);
        $config = $this->rankingEngine->getConfig();

        $page = max(1, (int) $request->query('page', 1));
        $search = trim((string) $request->query('search', ''));
        $classFilter = $request->query('class');

        $leaderboard = $this->rankingEngine->getLeaderboard(
            $type,
            $config['leaderboard_limit'],
            $page,
            [
                'search' => $search,
                'class' => $classFilter,
            ]
        );

        return view('rankings.index', [
            'leaderboard' => $leaderboard,
            'ladder' => $leaderboard['items'],
            'type' => $type,
            'search' => $search,
            'classFilter' => $classFilter,
            'config' => $config,
        ]);
    }

    /**
     * JSON feed endpoint for seamless homepage tab switching & real-time refreshes.
     */
    public function feed(Request $request, string $category)
    {
        $cat = $this->rankingEngine->normalizeCategory($category);
        $config = $this->rankingEngine->getConfig();
        $limit = max(1, min(50, (int) $request->query('limit', $config['homepage_limit'])));

        if (!in_array($cat, $config['enabled_categories'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'This ranking category is currently disabled.',
            ], 404);
        }

        try {
            $payload = $this->rankingEngine->getLeaderboard($cat, $limit, 1);
            return response()->json([
                'ok' => true,
                'data' => $payload,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Unable to load leaderboard data at this moment.',
            ], 500);
        }
    }
}
