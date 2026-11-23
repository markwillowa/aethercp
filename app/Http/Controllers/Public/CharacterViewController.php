<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Contracts\Repositories\GuildRepositoryInterface;
use App\Contracts\EmulatorInterface;
use Illuminate\Http\Request;

class CharacterViewController extends Controller
{
    public function __construct(
        protected CharacterRepositoryInterface $characters,
        protected GuildRepositoryInterface $guilds,
        protected EmulatorInterface $emulator
    ) {}

    public function show(string $name)
    {
        $char = $this->characters->findByName($name);
        if (!$char) {
            abort(404, 'Character not found.');
        }

        $guild = $char->guild_id > 0 ? $this->guilds->findById($char->guild_id) : null;
        $inventory = $this->characters->getInventory($char->char_id);
        $equippedItems = array_filter($inventory, fn($i) => !empty($i->equip));
        $statColumns = $this->emulator->getStatColumns();

        return view('characters.show', compact('char', 'guild', 'equippedItems', 'statColumns'));
    }

    public function online(Request $request)
    {
        $onlinePlayers = $this->characters->getOnlineCharacters(100);
        $totalOnline = $this->emulator->getOnlinePlayerCount();

        return view('characters.online', compact('onlinePlayers', 'totalOnline'));
    }
}

