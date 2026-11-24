<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Contracts\Repositories\GuildRepositoryInterface;
use App\Contracts\Repositories\ItemRepositoryInterface;
use App\Contracts\EmulatorInterface;
use App\Models\PlayerAchievement;
use App\Models\PlayerProfileSetting;
use App\Models\RewardIssuance;
use App\Services\Emulator\JobHelper;
use App\Services\Ranking\BadgeService;
use App\Services\Ranking\RankingEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CharacterViewController extends Controller
{
    public function __construct(
        protected CharacterRepositoryInterface $characters,
        protected GuildRepositoryInterface $guilds,
        protected ItemRepositoryInterface $items,
        protected EmulatorInterface $emulator,
        protected RankingEngineService $rankingEngine,
        protected BadgeService $badgeService
    ) {}

    public function show(string $name)
    {
        $char = $this->characters->findByName($name);
        if (!$char) {
            abort(404, 'Character not found.');
        }

        $privacy = Schema::hasTable('player_profile_settings')
            ? PlayerProfileSetting::forCharacter((int) $char->char_id, (int) $char->account_id)
            : new PlayerProfileSetting([
                'is_public' => true,
                'show_char_info' => true,
                'show_rankings' => true,
                'show_badges' => true,
                'show_achievements' => true,
                'show_stats' => true,
                'show_guild' => true,
                'show_last_login' => false,
            ]);

        $viewer = Auth::user();
        $isOwnerOrAdmin = $viewer && ($viewer->isAdmin() || (int) $viewer->account_id === (int) $char->account_id);

        if (!$privacy->is_public && !$isOwnerOrAdmin) {
            return view('characters.private', ['charName' => $char->name]);
        }

        $guild = ($privacy->show_guild || $isOwnerOrAdmin) && $char->guild_id > 0
            ? $this->guilds->findById($char->guild_id)
            : null;

        $equippedItems = [];
        if ($privacy->show_char_info || $isOwnerOrAdmin) {
            $inventory = $this->characters->getInventory($char->char_id);
            $rawEquipped = array_filter($inventory, fn($i) => !empty($i->equip));

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
        }

        $statColumns = $this->emulator->getStatColumns();
        $jobSpriteUrl = JobHelper::getSpriteUrl((int) $char->class, (string) ($char->sex ?? 'M'));

        $badges = ($privacy->show_badges || $isOwnerOrAdmin)
            ? ($this->badgeService->getActiveBadgesForTargets('character', [(int) $char->char_id])[(int) $char->char_id] ?? [])
            : [];

        $rankingSummary = ($privacy->show_rankings || $privacy->show_stats || $isOwnerOrAdmin)
            ? $this->rankingEngine->getCharacterRankingSummary((int) $char->char_id)
            : ['positions' => [], 'stats' => null, 'history' => collect()];

        $achievements = ($privacy->show_achievements || $isOwnerOrAdmin) && Schema::hasTable('player_achievements')
            ? PlayerAchievement::where('char_id', $char->char_id)->orderByDesc('earned_at')->get()
            : collect();

        $rewardsEarnedCount = Schema::hasTable('reward_issuances')
            ? RewardIssuance::where('char_id', $char->char_id)->where('status', 'delivered')->count()
            : 0;

        return view('characters.show', compact(
            'char',
            'guild',
            'equippedItems',
            'statColumns',
            'jobSpriteUrl',
            'badges',
            'privacy',
            'isOwnerOrAdmin',
            'rankingSummary',
            'achievements',
            'rewardsEarnedCount'
        ));
    }

    public function online(Request $request)
    {
        $onlinePlayers = $this->characters->getOnlineCharacters(100);
        $totalOnline = $this->emulator->getOnlinePlayerCount();

        return view('characters.online', compact('onlinePlayers', 'totalOnline'));
    }

    public function updatePrivacy(Request $request, int $charId)
    {
        $user = Auth::user();
        $char = $this->characters->findById($charId);
        if (!$char || (int) $char->account_id !== (int) $user->account_id) {
            abort(403, 'Unauthorized character privacy update.');
        }

        $privacy = PlayerProfileSetting::forCharacter($charId, (int) $user->account_id);
        $privacy->update([
            'is_public' => $request->has('is_public'),
            'show_char_info' => $request->has('show_char_info'),
            'show_rankings' => $request->has('show_rankings'),
            'show_badges' => $request->has('show_badges'),
            'show_achievements' => $request->has('show_achievements'),
            'show_stats' => $request->has('show_stats'),
            'show_guild' => $request->has('show_guild'),
            'show_last_login' => $request->has('show_last_login'),
            'bio' => trim((string) $request->input('bio', '')) ?: null,
        ]);

        return back()->with('success', "Profile privacy settings saved for {$char->name}.");
    }
}
