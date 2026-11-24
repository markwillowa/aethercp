<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\RankingRepositoryInterface;
use App\Contracts\EmulatorInterface;
use App\Models\News;
use App\Services\Ranking\RankingEngineService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected RankingRepositoryInterface $rankings,
        protected EmulatorInterface $emulator,
        protected RankingEngineService $rankingEngine
    ) {}

    public function index()
    {
        $news = News::published()
            ->pinnedFirst()
            ->limit(5)
            ->get();

        $rankingConfig = $this->rankingEngine->getConfig();
        $homeLimit = $rankingConfig['homepage_limit'] ?? 5;

        $homeRankings = [
            'lvl' => [],
            'job' => [],
            'pvp' => [],
            'mvp' => [],
            'guild' => [],
        ];

        foreach (['lvl', 'job', 'pvp', 'mvp', 'guild'] as $cat) {
            try {
                $homeRankings[$cat] = $this->rankingEngine->getLeaderboard($cat, $homeLimit, 1);
            } catch (\Throwable $e) {
                $homeRankings[$cat] = [
                    'category' => $cat,
                    'items' => [],
                    'total' => 0,
                    'updated_human' => now()->format('M d, Y H:i:s'),
                ];
            }
        }

        $topBase = $homeRankings['lvl']['items'] ?? [];
        $topJob = $homeRankings['job']['items'] ?? [];
        $topMvp = $homeRankings['mvp']['items'] ?? [];
        $topGuilds = $homeRankings['guild']['items'] ?? [];
        $topPlayers = $topBase;

        $serverStatus = [
            'login' => $this->emulator->checkServerStatus('login'),
            'char' => $this->emulator->checkServerStatus('char'),
            'map' => $this->emulator->checkServerStatus('map'),
            'online_count' => 0,
        ];
        try {
            $serverStatus['online_count'] = $this->emulator->getOnlinePlayerCount();
        } catch (\Throwable $e) {
            $serverStatus['online_count'] = 0;
        }

        return view('home', compact(
            'news',
            'topPlayers',
            'topBase',
            'topJob',
            'topMvp',
            'topGuilds',
            'homeRankings',
            'rankingConfig',
            'serverStatus'
        ));
    }
}
