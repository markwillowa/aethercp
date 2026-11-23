<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\RankingRepositoryInterface;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function __construct(
        protected RankingRepositoryInterface $rankings
    ) {}

    public function index(Request $request)
    {
        $type = $request->query('type', 'base');
        $limit = 50;

        $ladder = match ($type) {
            'job' => $this->rankings->getJobLevelRanking($limit),
            'zeny' => $this->rankings->getZenyRanking($limit),
            'mvp' => $this->rankings->getMvpRanking($limit),
            'guild' => $this->rankings->getGuildRanking($limit),
            default => $this->rankings->getBaseLevelRanking($limit),
        };

        return view('rankings.index', compact('ladder', 'type'));
    }
}

