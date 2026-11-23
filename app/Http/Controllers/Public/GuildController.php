<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\GuildRepositoryInterface;
use App\Services\Emulator\GuildEmblemRenderer;
use Illuminate\Http\Request;

class GuildController extends Controller
{
    public function __construct(
        protected GuildRepositoryInterface $guilds
    ) {}

    public function index(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $guildList = $this->guilds->getGuilds(20, $page);

        return view('guilds.index', compact('guildList', 'page'));
    }

    public function show(int $id)
    {
        $guild = $this->guilds->findById($id);
        if (!$guild) {
            abort(404, 'Guild not found.');
        }

        $members = $this->guilds->getMembers($id);
        $allCastles = $this->guilds->getCastles();
        $castles = array_filter($allCastles, fn($c) => (int) $c->guild_id === $id);

        return view('guilds.show', compact('guild', 'members', 'castles'));
    }

    public function emblem(int $id)
    {
        $raw = $this->guilds->getEmblemRaw($id);
        $png = GuildEmblemRenderer::renderPng($raw) ?? GuildEmblemRenderer::renderPlaceholder();

        return response($png)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}

