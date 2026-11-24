<?php

namespace App\Services\Attendance;

use App\Contracts\EmulatorInterface;
use App\Models\AttendanceCampaign;
use App\Models\AttendanceCampaignItem;
use App\Models\AttendanceClaim;
use App\Models\AttendanceDelivery;
use App\Models\PlayerInboxItem;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceDeliveryService
{
    public const MAX_ZENY = 2147483647;
    public const MAX_STORAGE_SLOTS = 600;
    public const MAX_ITEM_STACK = 30000;

    public function __construct(
        protected EmulatorInterface $emulator
    ) {}

    /**
     * Process all reward items associated with a daily claim.
     */
    public function deliverClaimItems(AttendanceClaim $claim, Collection $items, User $user, $targetChar, AttendanceCampaign $campaign): array
    {
        $deliveries = [];

        foreach ($items as $item) {
            $deliveries[] = $this->deliverSingleItem($claim, $item, $user, $targetChar, $campaign);
        }

        return $deliveries;
    }

    /**
     * Deliver an individual reward item with complete tracking.
     */
    public function deliverSingleItem(AttendanceClaim $claim, AttendanceCampaignItem $item, User $user, $targetChar, AttendanceCampaign $campaign): AttendanceDelivery
    {
        $deliveryMethod = $item->delivery_method ?: $campaign->delivery_destination ?: 'inbox';

        $delivery = AttendanceDelivery::create([
            'claim_id' => $claim->id,
            'campaign_item_id' => $item->id,
            'reward_type' => $item->reward_type,
            'delivery_type' => $item->reward_type === 'zeny' ? 'zeny' : $deliveryMethod,
            'status' => 'pending',
            'attempts' => 1,
        ]);

        try {
            if ($item->reward_type === 'zeny') {
                $this->deliverZeny($targetChar, $item->zeny_amount, $delivery);
            } elseif ($item->reward_type === 'cashpoints') {
                $this->deliverCashPoints($user->account_id, $item->cashpoints_amount, $delivery);
            } else {
                // Item Delivery
                match ($deliveryMethod) {
                    'storage' => $this->deliverItemToStorage($user->account_id, $item, $delivery),
                    'character' => $this->deliverItemToCharacter($targetChar, $item, $delivery),
                    default => $this->deliverItemToInbox($user, $targetChar, $item, $campaign, $delivery),
                };
            }
        } catch (\Throwable $e) {
            Log::error("Daily reward delivery failed [Delivery #{$delivery->id}]: " . $e->getMessage(), [
                'claim_id' => $claim->id,
                'item_id' => $item->id,
            ]);

            $delivery->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);
        }

        return $delivery;
    }

    /**
     * Deliver Zeny directly to character balance.
     */
    protected function deliverZeny($char, int $amount, AttendanceDelivery $delivery): void
    {
        if (!$char) {
            throw new \RuntimeException('No character available to receive Zeny.');
        }

        if ($amount <= 0) {
            $delivery->update(['status' => 'delivered', 'delivered_at' => now(), 'destination_target' => 'Zeny skipped (0)']);
            return;
        }

        $conn = DB::connection('ragnarok');
        $charRow = $conn->table('char')->where('char_id', $char->char_id)->first();
        if (!$charRow) {
            throw new \RuntimeException("Character #{$char->char_id} not found in database.");
        }

        $currentZeny = (int) $charRow->zeny;
        $newZeny = min(self::MAX_ZENY, $currentZeny + $amount);

        $conn->table('char')->where('char_id', $char->char_id)->update(['zeny' => $newZeny]);

        $delivery->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'destination_target' => "Char: {$char->name} (#{$char->char_id}) | +{$amount} Zeny (New: " . number_format($newZeny) . " Z)",
        ]);
    }

    /**
     * Credit Cash Points directly into rAthena account variable (#CASHPOINTS).
     */
    protected function deliverCashPoints(int $accountId, int $points, AttendanceDelivery $delivery): void
    {
        if ($points <= 0) {
            $delivery->update(['status' => 'delivered', 'delivered_at' => now()]);
            return;
        }

        $conn = DB::connection('ragnarok');
        $existing = $conn->table('acc_reg_num')
            ->where('account_id', $accountId)
            ->where('key', '#CASHPOINTS')
            ->where('index', 0)
            ->first();

        if ($existing) {
            $newTotal = $existing->value + $points;
            $conn->table('acc_reg_num')
                ->where('account_id', $accountId)
                ->where('key', '#CASHPOINTS')
                ->where('index', 0)
                ->update(['value' => $newTotal]);
        } else {
            $newTotal = $points;
            $conn->table('acc_reg_num')->insert([
                'account_id' => $accountId,
                'key' => '#CASHPOINTS',
                'index' => 0,
                'value' => $points,
            ]);
        }

        $delivery->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'destination_target' => "Account #{$accountId} | +{$points} CP",
        ]);
    }

    /**
     * Deliver item into Player Web Inbox (Safest Default).
     */
    protected function deliverItemToInbox(User $user, $targetChar, AttendanceCampaignItem $item, AttendanceCampaign $campaign, AttendanceDelivery $delivery): void
    {
        $inboxItem = PlayerInboxItem::create([
            'user_id' => $user->id,
            'account_id' => (int) $user->account_id,
            'character_id' => $targetChar ? (int) $targetChar->char_id : null,
            'title' => "Daily Reward: {$item->formatted_title}",
            'message' => "Earned from campaign \"{$campaign->name}\". Ready to be transferred to your Kafra Storage.",
            'source_type' => 'attendance',
            'source_id' => $delivery->id,
            'reward_type' => 'item',
            'item_id' => $item->item_id,
            'item_name' => $item->item_name,
            'quantity' => $item->quantity,
            'refine' => $item->refine,
            'card0' => $item->card0,
            'card1' => $item->card1,
            'card2' => $item->card2,
            'card3' => $item->card3,
            'identified' => $item->identified,
            'bound' => $item->bound,
            'status' => 'unclaimed',
        ]);

        $delivery->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'destination_target' => "AetherCP Inbox #{$inboxItem->id}",
            'external_reference' => "inbox_{$inboxItem->id}",
        ]);
    }

    /**
     * Deliver item directly into Kafra Storage.
     */
    public function deliverItemToStorage(int $accountId, AttendanceCampaignItem $item, ?AttendanceDelivery $delivery = null): bool
    {
        $conn = DB::connection('ragnarok');
        $slotCount = $conn->table('storage')->where('account_id', $accountId)->count();

        $canStack = ($item->refine === 0 && $item->card0 === 0 && $item->card1 === 0 && $item->card2 === 0 && $item->card3 === 0);

        if ($canStack) {
            $existing = $conn->table('storage')
                ->where('account_id', $accountId)
                ->where('nameid', $item->item_id)
                ->where('refine', 0)
                ->where('card0', 0)
                ->first();

            if ($existing && ($existing->amount + $item->quantity) <= self::MAX_ITEM_STACK) {
                $conn->table('storage')->where('id', $existing->id)->increment('amount', $item->quantity);
                if ($delivery) {
                    $delivery->update([
                        'status' => 'delivered',
                        'delivered_at' => now(),
                        'destination_target' => "Kafra Storage (Account #{$accountId}) | Stacked with ID #{$existing->id}",
                    ]);
                }
                return true;
            }
        }

        if ($slotCount >= self::MAX_STORAGE_SLOTS) {
            throw new \RuntimeException("Kafra Storage is full (Maximum " . self::MAX_STORAGE_SLOTS . " items). Please clear space.");
        }

        $id = $conn->table('storage')->insertGetId([
            'account_id' => $accountId,
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
            'bound' => $item->bound,
            'unique_id' => 0,
        ]);

        if ($delivery) {
            $delivery->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'destination_target' => "Kafra Storage (Account #{$accountId}) | Storage Row #{$id}",
            ]);
        }

        return true;
    }

    /**
     * Deliver item into Character Inventory (only if offline or safe).
     */
    protected function deliverItemToCharacter($char, AttendanceCampaignItem $item, AttendanceDelivery $delivery): void
    {
        if (!$char) {
            throw new \RuntimeException('No character selected for direct inventory delivery.');
        }

        $conn = DB::connection('ragnarok');
        $charRow = $conn->table('char')->where('char_id', $char->char_id)->first();
        if (!$charRow) {
            throw new \RuntimeException("Character #{$char->char_id} not found.");
        }

        // Safety check: Character must be offline to prevent inventory rollback
        if ($charRow->online > 0) {
            throw new \RuntimeException("Character {$char->name} is currently online. Please log out or select Inbox delivery.");
        }

        $invCount = $conn->table('inventory')->where('char_id', $char->char_id)->count();
        if ($invCount >= 100) {
            throw new \RuntimeException("Character inventory is full (100 items). Please clear inventory space.");
        }

        $id = $conn->table('inventory')->insertGetId([
            'char_id' => $char->char_id,
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
            'unique_id' => 0,
        ]);

        $delivery->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'destination_target' => "Inventory (Char: {$char->name} #{$char->char_id}) | Inv Row #{$id}",
        ]);
    }

    /**
     * Player claims an item from their web inbox into Kafra Storage.
     */
    public function claimInboxItemToStorage(PlayerInboxItem $inboxItem, User $user): array
    {
        return app(\App\Services\Escrow\EscrowInboxService::class)->claimToStorage($inboxItem, $user);
    }

    /**
     * Admin retry tool for failed deliveries.
     */
    public function retryDelivery(AttendanceDelivery $delivery): bool
    {
        $claim = $delivery->claim;
        $campaignItem = $delivery->item;
        if (!$claim || !$campaignItem) {
            return false;
        }

        $user = User::where('account_id', $claim->account_id)->first();
        $targetChar = $claim->character_id ? $this->emulator->getCharacterRepository()->findById($claim->character_id) : null;
        $campaign = $claim->campaign;

        $delivery->increment('attempts');

        try {
            if ($delivery->reward_type === 'zeny') {
                $this->deliverZeny($targetChar, $campaignItem->zeny_amount, $delivery);
            } else {
                match ($delivery->delivery_type) {
                    'storage' => $this->deliverItemToStorage($claim->account_id, $campaignItem, $delivery),
                    'character_inventory' => $this->deliverItemToCharacter($targetChar, $campaignItem, $delivery),
                    default => $this->deliverItemToInbox($user, $targetChar, $campaignItem, $campaign, $delivery),
                };
            }
            return true;
        } catch (\Throwable $e) {
            $delivery->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
