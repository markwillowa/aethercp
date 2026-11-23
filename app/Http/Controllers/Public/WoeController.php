<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\GuildRepositoryInterface;

class WoeController extends Controller
{
    public function __construct(
        protected GuildRepositoryInterface $guilds
    ) {}

    public function index()
    {
        $castles = $this->guilds->getCastles();

        $castleNames = [
            0 => 'Neuschwanstein', 1 => 'Hohenschwangau', 2 => 'Nuernberg', 3 => 'Wuerzburg', 4 => 'Rothenburg',
            5 => 'Repherion', 6 => 'Eeyolbriggar', 7 => 'Yesnelph', 8 => 'Bergel', 9 => 'Mersetzdeitz',
            10 => 'Bright Arbor', 11 => 'Scarlet Palace', 12 => 'Holy Shadow', 13 => 'Sacred Altar', 14 => 'Bamboo Grove',
            15 => 'Kriemhild', 16 => 'Swanhild', 17 => 'Fadhgridh', 18 => 'Skoegul', 19 => 'Gondul',
        ];

        return view('woe.index', compact('castles', 'castleNames'));
    }
}

