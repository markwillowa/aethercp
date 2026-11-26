<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\GuildRepositoryInterface;
use App\Services\Emulator\GuildEmblemRenderer;
use App\Services\Ranking\BadgeService;
use Illuminate\Http\Request;

class GuildController extends Controller
{
    public function __construct(
        protected GuildRepositoryInterface $guilds,
        protected BadgeService $badgeService
    ) {}

    public function index(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $guildList = $this->guilds->getGuilds(20, $page);

        $guildIds = array_values(array_filter(array_map(fn ($g) => (int) ($g->guild_id ?? 0), $guildList)));
        $guildBadges = $this->badgeService->getActiveBadgesForTargets('guild', $guildIds);

        return view('guilds.index', compact('guildList', 'page', 'guildBadges'));
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

        $guildBadges = $this->badgeService->getActiveBadgesForTargets('guild', [$id])[$id] ?? [];
        $memberIds = array_values(array_filter(array_map(fn ($m) => (int) ($m->char_id ?? 0), $members)));
        $memberBadges = $this->badgeService->getActiveBadgesForTargets('character', $memberIds);

        return view('guilds.show', compact('guild', 'members', 'castles', 'guildBadges', 'memberBadges'));
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

