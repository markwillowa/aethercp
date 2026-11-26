<?php

namespace App\Services\Escrow;

use App\Contracts\Repositories\ItemRepositoryInterface;
use App\Models\ActivityLog;
use App\Models\PlayerInboxItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EscrowInboxService
{
    public const MAX_STORAGE_SLOTS = 600;
    public const MAX_INVENTORY_SLOTS = 100;
    public const MAX_STACK_AMOUNT = 30000;

    public function __construct(
        protected ItemRepositoryInterface $itemRepo
    ) {}

    /**
     * Deposit an item/reward into the player's web Escrow Inbox.
     */
    public function depositItem(int $accountId, array $data): PlayerInboxItem
    {
        $userId = $data['user_id'] ?? User::where('account_id', $accountId)->value('id');

        $itemId = (int) ($data['item_id'] ?? 0);
        $itemName = $data['item_name'] ?? null;

        if (empty($itemName) && $itemId > 0) {
            $itemDb = $this->itemRepo->findById($itemId);
            if ($itemDb) {
                $itemName = $itemDb->name_english ?? $itemDb->name_aegis ?? "Item #{$itemId}";
            }
        }

        return PlayerInboxItem::create([
            'user_id' => $userId,
            'account_id' => $accountId,
            'character_id' => $data['character_id'] ?? null,
            'title' => $data['title'] ?? 'Escrow Delivery',
            'message' => $data['message'] ?? null,
            'source_type' => $data['source_type'] ?? 'attendance',
            'source_id' => $data['source_id'] ?? null,
            'reward_type' => $data['reward_type'] ?? 'item',
            'item_id' => $itemId > 0 ? $itemId : null,
            'item_name' => $itemName,
            'quantity' => max(1, (int) ($data['quantity'] ?? 1)),
            'zeny_amount' => (int) ($data['zeny_amount'] ?? 0),
            'cashpoints_amount' => (int) ($data['cashpoints_amount'] ?? 0),
            'refine' => (int) ($data['refine'] ?? 0),
            'card0' => (int) ($data['card0'] ?? 0),
            'card1' => (int) ($data['card1'] ?? 0),
            'card2' => (int) ($data['card2'] ?? 0),
            'card3' => (int) ($data['card3'] ?? 0),
            'identified' => (bool) ($data['identified'] ?? true),
            'bound' => (int) ($data['bound'] ?? 0),
            'status' => 'unclaimed',
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }

    /**
     * Claim an escrow item into in-game Kafra Storage.
     */
    public function claimToStorage(PlayerInboxItem $item, User $user): array
    {
        if ((int) $item->account_id !== (int) $user->account_id) {
            return ['success' => false, 'message' => 'Unauthorized. This escrow item does not belong to your account.'];
        }

        if ($item->status === 'claimed') {
            return ['success' => false, 'message' => 'This item has already been transferred to in-game storage.'];
        }

        if ($item->reward_type !== 'item' || !$item->item_id) {
            return ['success' => false, 'message' => 'Only item rewards can be transferred to in-game storage.'];
        }

        try {
            $conn = DB::connection('ragnarok');

            return DB::transaction(function () use ($conn, $item, $user) {
                // Check current storage capacity
                $currentSlots = $conn->table('storage')->where('account_id', $user->account_id)->count();

                $isCleanStackable = ($item->refine === 0
                    && $item->card0 === 0
                    && $item->card1 === 0
                    && $item->card2 === 0
                    && $item->card3 === 0);

                $merged = false;

                if ($isCleanStackable) {
                    $existing = $conn->table('storage')
                        ->where('account_id', $user->account_id)
                        ->where('nameid', $item->item_id)
                        ->where('refine', 0)
                        ->where('card0', 0)
                        ->where('card1', 0)
                        ->where('card2', 0)
                        ->where('card3', 0)
                        ->where('bound', (int) $item->bound)
                        ->first();

                    if ($existing && ($existing->amount + $item->quantity) <= self::MAX_STACK_AMOUNT) {
                        $conn->table('storage')->where('id', $existing->id)->increment('amount', $item->quantity);
                        $merged = true;
                    }
                }

                if (!$merged) {
                    if ($currentSlots >= self::MAX_STORAGE_SLOTS) {
                        return [
                            'success' => false,
                            'message' => "Your in-game Kafra Storage is currently full ({$currentSlots}/" . self::MAX_STORAGE_SLOTS . " slots). Please free up space before claiming.",
                        ];
                    }

                    $conn->table('storage')->insert([
                        'account_id' => $user->account_id,
                        'nameid' => $item->item_id,
                        'amount' => $item->quantity,
                        'equip' => 0,
                        'identify' => $item->identified ? 1 : 0,
                        'refine' => $item->refine,
                        'attribute' => 0,
                        'card0' => $item->card0,
                        'card1' => $item->card1,
                        'card2' => $item->card2,
                        'card3' => $item->card3,
                        'bound' => $item->bound,
                        'expire_time' => 0,
                    ]);
                }

                $item->update([
                    'status' => 'claimed',
                    'claimed_to' => 'storage',
                    'claimed_character_id' => null,
                    'claimed_at' => now(),
                ]);

                ActivityLog::log(
                    'escrow.claim_storage',
                    "Transferred Escrow Item #{$item->id} ({$item->formatted_title}) to Kafra Storage.",
                    $user->account_id
                );

                return [
                    'success' => true,
                    'message' => "Successfully transferred {$item->formatted_title} to your in-game Kafra Storage!",
                ];
            });
        } catch (\Throwable $e) {
            Log::error("Failed transferring escrow item #{$item->id} to storage: " . $e->getMessage(), [
                'item_id' => $item->id,
                'account_id' => $user->account_id,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'message' => 'Unable to complete transfer to Kafra storage. Please try again or contact support.',
            ];
        }
    }

    /**
     * Claim an escrow item directly into a character's inventory.
     * With critical offline validation and inventory slot safety check.
     */
    public function claimToCharacter(PlayerInboxItem $item, User $user, int $charId): array
    {
        if ((int) $item->account_id !== (int) $user->account_id) {
            return ['success' => false, 'message' => 'Unauthorized. This escrow item does not belong to your account.'];
        }

        if ($item->status === 'claimed') {
            return ['success' => false, 'message' => 'This item has already been transferred into the game.'];
        }

        if (!in_array($item->reward_type, ['item', 'zeny'], true)) {
            return ['success' => false, 'message' => 'This reward type cannot be transferred to a character.'];
        }

        if ($item->reward_type === 'item' && !$item->item_id) {
            return ['success' => false, 'message' => 'Invalid item reward.'];
        }

        if ($item->reward_type === 'zeny' && (int) $item->zeny_amount <= 0) {
            return ['success' => false, 'message' => 'Invalid Zeny amount.'];
        }

        $conn = DB::connection('ragnarok');

        // 1. Validate character ownership and offline status
        $char = $conn->table('char')
            ->where('char_id', $charId)
            ->where('account_id', $user->account_id)
            ->first();

        if (!$char) {
            return [
                'success' => false,
                'message' => 'Selected character not found or does not belong to your account.',
            ];
        }

        if ((int) $char->online > 0) {
            return [
                'success' => false,
                'message' => "Character \"{$char->name}\" is currently ONLINE in-game. Please completely log out of the game first to safely receive inventory items, or transfer to Kafra Storage instead.",
            ];
        }

        try {
            return DB::transaction(function () use ($conn, $item, $user, $char, $charId) {
                if ($item->reward_type === 'zeny') {
                    $maxZeny = 2147483647;
                    $currentZeny = (int) $char->zeny;
                    $addZeny = (int) $item->zeny_amount;
                    if ($currentZeny + $addZeny > $maxZeny) {
                        return [
                            'success' => false,
                            'message' => "Character \"{$char->name}\" would exceed the maximum Zeny limit (" . number_format($maxZeny) . ').',
                        ];
                    }

                    $conn->table('char')->where('char_id', $charId)->update(['zeny' => $currentZeny + $addZeny]);

                    $item->update([
                        'status' => 'claimed',
                        'claimed_to' => 'inventory',
                        'claimed_character_id' => $charId,
                        'claimed_at' => now(),
                    ]);

                    ActivityLog::log(
                        'escrow.claim_character_zeny',
                        "Transferred Escrow Zeny #{$item->id} ({$item->formatted_title}) to {$char->name}.",
                        $user->account_id
                    );

                    return [
                        'success' => true,
                        'message' => "Successfully transferred {$item->formatted_title} to {$char->name}!",
                    ];
                }

                // 2. Validate inventory slot capacity (< 100)
                $currentCount = $conn->table('inventory')
                    ->where('char_id', $charId)
                    ->count();

                $isCleanStackable = ($item->refine === 0
                    && $item->card0 === 0
                    && $item->card1 === 0
                    && $item->card2 === 0
                    && $item->card3 === 0);

                $merged = false;

                if ($isCleanStackable) {
                    $existing = $conn->table('inventory')
                        ->where('char_id', $charId)
                        ->where('nameid', $item->item_id)
                        ->where('refine', 0)
                        ->where('card0', 0)
                        ->where('card1', 0)
                        ->where('card2', 0)
                        ->where('card3', 0)
                        ->where('bound', (int) $item->bound)
                        ->first();

                    if ($existing && ($existing->amount + $item->quantity) <= self::MAX_STACK_AMOUNT) {
                        $conn->table('inventory')->where('id', $existing->id)->increment('amount', $item->quantity);
                        $merged = true;
                    }
                }

                if (!$merged) {
                    if ($currentCount >= self::MAX_INVENTORY_SLOTS) {
                        return [
                            'success' => false,
                            'message' => "Character \"{$char->name}\" inventory is full ({$currentCount}/" . self::MAX_INVENTORY_SLOTS . " slots). Please free up space or transfer to Kafra Storage.",
                        ];
                    }

                    $conn->table('inventory')->insert([
                        'char_id' => $charId,
                        'nameid' => $item->item_id,
                        'amount' => $item->quantity,
                        'equip' => 0,
                        'identify' => $item->identified ? 1 : 0,
                        'refine' => $item->refine,
                        'attribute' => 0,
                        'card0' => $item->card0,
                        'card1' => $item->card1,
                        'card2' => $item->card2,
                        'card3' => $item->card3,
                        'expire_time' => 0,
                        'favorite' => 0,
                        'bound' => $item->bound,
                    ]);
                }

                $item->update([
                    'status' => 'claimed',
                    'claimed_to' => 'inventory',
                    'claimed_character_id' => $charId,
                    'claimed_at' => now(),
                ]);

                ActivityLog::log(
                    'escrow.claim_character',
                    "Transferred Escrow Item #{$item->id} ({$item->formatted_title}) directly to {$char->name}'s inventory.",
                    $user->account_id
                );

                return [
                    'success' => true,
                    'message' => "Successfully transferred {$item->formatted_title} directly to {$char->name}'s inventory!",
                ];
            });
        } catch (\Throwable $e) {
            Log::error("Failed transferring escrow item #{$item->id} to character #{$charId}: " . $e->getMessage(), [
                'item_id' => $item->id,
                'char_id' => $charId,
                'account_id' => $user->account_id,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'message' => 'Unable to complete transfer to character inventory. Please try again or contact support.',
            ];
        }
    }
}
