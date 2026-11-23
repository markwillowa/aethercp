<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\RankingRepositoryInterface;
use App\Models\News;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected RankingRepositoryInterface $rankings
    ) {}

    public function index()
    {
        $news = News::published()
            ->pinnedFirst()
            ->limit(5)
            ->get();

        $topPlayers = [];
        try {
            $topPlayers = $this->rankings->getBaseLevelRanking(5);
        } catch (\Throwable $e) {
            $topPlayers = [];
        }

        return view('home', compact('news', 'topPlayers'));
    }
}

