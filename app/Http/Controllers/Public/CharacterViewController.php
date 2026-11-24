<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Contracts\Repositories\GuildRepositoryInterface;
use App\Contracts\Repositories\ItemRepositoryInterface;
use App\Contracts\EmulatorInterface;
use App\Services\Emulator\JobHelper;
use Illuminate\Http\Request;

class CharacterViewController extends Controller
{
    public function __construct(
        protected CharacterRepositoryInterface $characters,
        protected GuildRepositoryInterface $guilds,
        protected ItemRepositoryInterface $items,
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
        $rawEquipped = array_filter($inventory, fn($i) => !empty($i->equip));

        $equippedItems = [];
        foreach ($rawEquipped as $item) {
            $itemDb = $this->items->findById((int) $item->nameid);
            $itemName = $itemDb ? ($itemDb->name_english ?? $itemDb->name_aegis ?? "Item #{$item->nameid}") : "Item #{$item->nameid}";
            $slots = $itemDb ? (int) ($itemDb->slots ?? 0) : 0;

            $cards = [];
            foreach (['card0', 'card1', 'card2', 'card3'] as $cardCol) {
                $cid = (int) ($item->{$cardCol} ?? 0);
                if ($cid > 0 && $cid < 65000) {
                    $cDb = $this->items->findById($cid);
                    $cards[] = [
                        'id' => $cid,
                        'name' => $cDb ? ($cDb->name_english ?? $cDb->name_aegis ?? "Card #{$cid}") : "Card #{$cid}",
                    ];
                }
            }

            $equippedItems[] = [
                'nameid' => (int) $item->nameid,
                'name' => $itemName,
                'refine' => (int) ($item->refine ?? 0),
                'slots' => $slots,
                'slot_label' => JobHelper::getEquipSlotName((int) $item->equip),
                'cards' => $cards,
                'icon_url' => "https://static.divine-pride.net/images/items/item/{$item->nameid}.png",
            ];
        }

        $statColumns = $this->emulator->getStatColumns();
        $jobSpriteUrl = JobHelper::getSpriteUrl((int) $char->class, (string) ($char->sex ?? 'M'));
        $badgesMap = app(\App\Services\Ranking\BadgeService::class)->getActiveBadgesForTargets('character', [(int) $char->char_id]);
        $badges = $badgesMap[(int) $char->char_id] ?? [];

        return view('characters.show', compact('char', 'guild', 'equippedItems', 'statColumns', 'jobSpriteUrl', 'badges'));
    }

    public function online(Request $request)
    {
        $onlinePlayers = $this->characters->getOnlineCharacters(100);
        $totalOnline = $this->emulator->getOnlinePlayerCount();

        return view('characters.online', compact('onlinePlayers', 'totalOnline'));
    }
}
