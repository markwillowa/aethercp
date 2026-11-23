<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\RankingRepositoryInterface;
use App\Contracts\EmulatorInterface;
use App\Models\News;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected RankingRepositoryInterface $rankings,
        protected EmulatorInterface $emulator
    ) {}

    public function index()
    {
        $news = News::published()
            ->pinnedFirst()
            ->limit(5)
            ->get();

        $topBase = [];
        $topJob = [];
        $topMvp = [];
        $topGuilds = [];

        try {
            $topBase = $this->rankings->getBaseLevelRanking(5);
        } catch (\Throwable $e) {
            $topBase = [];
        }

        try {
            $topJob = $this->rankings->getJobLevelRanking(5);
        } catch (\Throwable $e) {
            $topJob = [];
        }

        try {
            $topMvp = $this->rankings->getMvpRanking(5);
        } catch (\Throwable $e) {
            $topMvp = [];
        }

        try {
            $topGuilds = $this->rankings->getGuildRanking(5);
        } catch (\Throwable $e) {
            $topGuilds = [];
        }

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

        return view('home', compact('news', 'topPlayers', 'topBase', 'topJob', 'topMvp', 'topGuilds', 'serverStatus'));
    }
}
