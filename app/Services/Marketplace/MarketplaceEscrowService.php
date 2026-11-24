<?php

namespace App\Services\Marketplace;

use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Contracts\Repositories\ItemRepositoryInterface;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceListing;
use App\Models\MarketplaceTransaction;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MarketplaceEscrowService
{
    public function __construct(
        protected CharacterRepositoryInterface $characterRepo,
        protected ItemRepositoryInterface $itemRepo
    ) {}

    /**
     * Retrieve all marketplace configuration settings with defaults.
     */
    public function getSettings(): array
    {
        return [
            'enabled' => (bool) SiteSetting::get('module_enabled_marketplace', true),
            'maintenance_mode' => (bool) SiteSetting::get('marketplace_maintenance_mode', false),
            'announcement' => SiteSetting::get('marketplace_announcement', 'Welcome to the Aether Trade House! Trade your rare items safely with fellow adventurers.'),
            'rules' => SiteSetting::get('marketplace_rules', 'Items placed in the trade house are securely held in escrow until sold, cancelled, or withdrawn.'),
            'max_listings_player' => (int) SiteSetting::get('marketplace_max_listings_player', 15),
            'max_listings_char' => (int) SiteSetting::get('marketplace_max_listings_char', 8),
            'max_items_per_listing' => (int) SiteSetting::get('marketplace_max_items_per_listing', 1000),
            'min_price' => (int) SiteSetting::get('marketplace_min_price', 1),
            'max_price' => (int) SiteSetting::get('marketplace_max_price', 1000000000),
            'min_duration_hours' => (int) SiteSetting::get('marketplace_min_duration_hours', 24),
            'max_duration_hours' => (int) SiteSetting::get('marketplace_max_duration_hours', 720),
            'default_duration_hours' => (int) SiteSetting::get('marketplace_default_duration_hours', 72),
            'allowed_durations' => [24, 72, 168, 336, 720], // 1 day, 3 days, 7 days, 14 days, 30 days
            'tax_percent' => (float) SiteSetting::get('marketplace_tax_percent', 5.0), // 5% default tax
            'listing_fee' => (int) SiteSetting::get('marketplace_listing_fee', 0),
            'allow_zeny' => (bool) SiteSetting::get('marketplace_allow_zeny', true),
            'allow_cashpoints' => (bool) SiteSetting::get('marketplace_allow_cashpoints', true),
            'allow_withdrawals' => (bool) SiteSetting::get('marketplace_allow_withdrawals', true),
            'allow_stackables' => (bool) SiteSetting::get('marketplace_allow_stackables', true),
            'allow_equipment' => (bool) SiteSetting::get('marketplace_allow_equipment', true),
            'allow_bound' => (bool) SiteSetting::get('marketplace_allow_bound', false),
            'allow_rental' => (bool) SiteSetting::get('marketplace_allow_rental', false),
            'allow_refined' => (bool) SiteSetting::get('marketplace_allow_refined', true),
            'allow_cards' => (bool) SiteSetting::get('marketplace_allow_cards', true),
            'history_enabled' => (bool) SiteSetting::get('marketplace_history_enabled', true),
        ];
    }

    /**
     * Check if marketplace is actively operational for players.
     */
    public function isOperational(): bool
    {
        $settings = $this->getSettings();
        return $settings['enabled'] && !$settings['maintenance_mode'];
    }

    /**
     * Get validated character belonging to account.
     */
    public function getAccountCharacter(int $charId, int $accountId): ?object
    {
        $char = $this->characterRepo->findById($charId);
        if (!$char || (int) $char->account_id !== $accountId) {
            return null;
        }
        return $char;
    }

    /**
     * Get character Zeny balance from rAthena/Hercules emulator char table.
     */
    public function getCharacterZeny(int $charId): int
    {
        try {
            $zeny = DB::connection('ragnarok')->table('char')
                ->where('char_id', $charId)
                ->value('zeny');
            return (int) ($zeny ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Get account Cash Points balance from rAthena/Hercules acc_reg_num.
     */
    public function getAccountCashPoints(int $accountId): int
    {
        try {
            $points = DB::connection('ragnarok')->table('acc_reg_num')
                ->where('account_id', $accountId)
                ->where('key', '#CASHPOINTS')
                ->where('index', 0)
                ->value('value');
            return (int) ($points ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Adjust account Cash Points balance.
     */
    public function adjustCashPoints(int $accountId, int $delta): int
    {
        $conn = DB::connection('ragnarok');
        $existing = $conn->table('acc_reg_num')
            ->where('account_id', $accountId)
            ->where('key', '#CASHPOINTS')
            ->where('index', 0)
            ->first();

        $current = $existing ? (int) $existing->value : 0;
        $newTotal = max(0, $current + $delta);

        if ($existing) {
            $conn->table('acc_reg_num')
                ->where('account_id', $accountId)
                ->where('key', '#CASHPOINTS')
                ->where('index', 0)
                ->update(['value' => $newTotal]);
        } else {
            $conn->table('acc_reg_num')->insert([
                'account_id' => $accountId,
                'key' => '#CASHPOINTS',
                'index' => 0,
                'value' => $newTotal,
            ]);
        }

        // Sync with global_acc_reg_num if exists
        try {
            if ($conn->getSchemaBuilder()->hasTable('global_acc_reg_num')) {
                $hasGlobal = $conn->table('global_acc_reg_num')
                    ->where('account_id', $accountId)
                    ->where('key', '#CASHPOINTS')
                    ->where('index', 0)
                    ->exists();

                if ($hasGlobal) {
                    $conn->table('global_acc_reg_num')
                        ->where('account_id', $accountId)
                        ->where('key', '#CASHPOINTS')
                        ->where('index', 0)
                        ->update(['value' => $newTotal]);
                } else {
                    $conn->table('global_acc_reg_num')->insert([
                        'account_id' => $accountId,
                        'key' => '#CASHPOINTS',
                        'index' => 0,
                        'value' => $newTotal,
                    ]);
                }
            }
        } catch (\Throwable $e) {}

        return $newTotal;
    }

    /**
     * Retrieve items currently inside the player's in-game Kafra storage.
     */
    public function getStorageItems(int $accountId): array
    {
        $settings = $this->getSettings();
        $rawStorage = $this->characterRepo->getStorage($accountId);

        $items = [];
        foreach ($rawStorage as $row) {
            $itemDb = $this->itemRepo->findById($row->nameid);
            $itemName = $itemDb ? ($itemDb->name_english ?? $itemDb->name_aegis ?? "Item #{$row->nameid}") : "Item #{$row->nameid}";
            $itemType = $itemDb ? ($itemDb->type ?? 'Etc') : 'Etc';
            $slots = $itemDb ? (int) ($itemDb->slots ?? 0) : 0;

            // Resolve compounded cards
            $cards = [];
            foreach (['card0', 'card1', 'card2', 'card3'] as $idx => $cardCol) {
                $cId = (int) $row->{$cardCol};
                if ($cId > 0 && $cId < 65000) {
                    $cDb = $this->itemRepo->findById($cId);
                    $cards[] = [
                        'slot' => $idx + 1,
                        'id' => $cId,
                        'name' => $cDb ? ($cDb->name_english ?? $cDb->name_aegis ?? "Card #{$cId}") : "Card #{$cId}",
                    ];
                }
            }

            $prefix = $row->refine > 0 ? "+{$row->refine} " : '';
            $slotText = $slots > 0 ? " [{$slots}]" : '';
            $formattedName = $prefix . $itemName . $slotText;

            $isTransferable = true;
            $restrictionReason = null;

            if ($row->bound > 0 && !$settings['allow_bound']) {
                $isTransferable = false;
                $restrictionReason = 'Account-Bound items cannot be traded.';
            } elseif ($row->expire_time > 0 && !$settings['allow_rental']) {
                $isTransferable = false;
                $restrictionReason = 'Rental/Expiring items cannot be traded.';
            } elseif ($row->refine > 0 && !$settings['allow_refined']) {
                $isTransferable = false;
                $restrictionReason = 'Refined equipment trading is currently disabled.';
            } elseif (($row->card0 > 0 || $row->card1 > 0 || $row->card2 > 0 || $row->card3 > 0) && !$settings['allow_cards']) {
                $isTransferable = false;
                $restrictionReason = 'Carded equipment trading is currently disabled.';
            }

            $items[] = [
                'storage_id' => $row->id,
                'nameid' => $row->nameid,
                'name' => $itemName,
                'formatted_name' => $formattedName,
                'amount' => $row->amount,
                'equip' => $row->equip,
                'identify' => $row->identify,
                'refine' => $row->refine,
                'attribute' => $row->attribute,
                'slots' => $slots,
                'cards' => $cards,
                'card_count' => count($cards),
                'card0' => $row->card0,
                'card1' => $row->card1,
                'card2' => $row->card2,
                'card3' => $row->card3,
                'expire_time' => $row->expire_time,
                'bound' => $row->bound,
                'unique_id' => $row->unique_id,
                'type' => $itemType,
                'subtype' => $itemDb ? ($itemDb->subtype ?? null) : null,
                'attack' => $itemDb ? (int) ($itemDb->attack ?? 0) : 0,
                'defense' => $itemDb ? (int) ($itemDb->defense ?? 0) : 0,
                'weight' => $itemDb ? (float) (($itemDb->weight ?? 0) / 10) : 0,
                'equip_level_min' => $itemDb ? (int) ($itemDb->equip_level_min ?? 0) : 0,
                'weapon_level' => $itemDb ? (int) ($itemDb->weapon_level ?? 0) : 0,
                'icon_url' => "https://static.divine-pride.net/images/items/item/{$row->nameid}.png",
                'is_transferable' => $isTransferable,
                'restriction_reason' => $restrictionReason,
            ];
        }

        return $items;
    }

    /**
     * Transfer an item from in-game Kafra storage into Marketplace custody (Escrow).
     */
    public function transferToCustody(User $user, int $charId, int $storageId, int $amount = 1): MarketplaceItem
    {
        $settings = $this->getSettings();
        if (!$this->isOperational()) {
            throw new \RuntimeException('The Player Marketplace is currently closed for maintenance.');
        }

        $char = $this->getAccountCharacter($charId, $user->account_id);
        if (!$char) {
            throw new \RuntimeException('Invalid character selection or character does not belong to your account.');
        }

        if ((int) $char->online > 0) {
            throw new \RuntimeException('Your character must be logged out of the game before transferring storage items.');
        }

        $conn = DB::connection('ragnarok');

        return DB::transaction(function () use ($conn, $user, $charId, $storageId, $amount, $settings) {
            // Find storage row with lock
            $storageRow = $conn->table('storage')
                ->where('id', $storageId)
                ->where('account_id', $user->account_id)
                ->lockForUpdate()
                ->first();

            if (!$storageRow) {
                throw new \RuntimeException('The selected item could not be found in your Kafra storage.');
            }

            if ($amount <= 0 || $amount > $storageRow->amount) {
                throw new \RuntimeException("Invalid quantity requested ({$amount}). Available: {$storageRow->amount}.");
            }

            // Check restrictions
            if ($storageRow->bound > 0 && !$settings['allow_bound']) {
                throw new \RuntimeException('Bound items cannot be transferred to the trade house.');
            }
            if ($storageRow->expire_time > 0 && !$settings['allow_rental']) {
                throw new \RuntimeException('Rental or expiring items cannot be transferred.');
            }

            // Deduct or remove from in-game storage
            if ($amount === (int) $storageRow->amount) {
                $conn->table('storage')->where('id', $storageId)->delete();
            } else {
                $conn->table('storage')->where('id', $storageId)->decrement('amount', $amount);
            }

            // Resolve item name, type, and inherent slots
            $itemDb = $this->itemRepo->findById($storageRow->nameid);
            $itemName = $itemDb ? ($itemDb->name_english ?? $itemDb->name_aegis ?? "Item #{$storageRow->nameid}") : "Item #{$storageRow->nameid}";
            $itemType = $itemDb ? ($itemDb->type ?? 'Etc') : 'Etc';
            $slots = $itemDb ? (int) ($itemDb->slots ?? 0) : 0;

            // Resolve compounded cards
            $cards = [];
            foreach (['card0', 'card1', 'card2', 'card3'] as $idx => $cardCol) {
                $cId = (int) $storageRow->{$cardCol};
                if ($cId > 0 && $cId < 65000) {
                    $cDb = $this->itemRepo->findById($cId);
                    $cards[] = [
                        'slot' => $idx + 1,
                        'id' => $cId,
                        'name' => $cDb ? ($cDb->name_english ?? $cDb->name_aegis ?? "Card #{$cId}") : "Card #{$cId}",
                    ];
                }
            }

            // Create Marketplace Item in Custody
            return MarketplaceItem::create([
                'user_id' => $user->id,
                'account_id' => $user->account_id,
                'char_id' => $charId,
                'nameid' => $storageRow->nameid,
                'item_name' => $itemName,
                'amount' => $amount,
                'equip' => $storageRow->equip,
                'identify' => $storageRow->identify,
                'refine' => $storageRow->refine,
                'attribute' => $storageRow->attribute,
                'card0' => $storageRow->card0,
                'card1' => $storageRow->card1,
                'card2' => $storageRow->card2,
                'card3' => $storageRow->card3,
                'expire_time' => $storageRow->expire_time,
                'bound' => $storageRow->bound,
                'unique_id' => $storageRow->unique_id,
                'item_type' => $itemType,
                'status' => 'in_custody',
                'deposited_at' => now(),
                'metadata' => [
                    'slots' => $slots,
                    'cards' => $cards,
                ],
            ]);
        });
    }

    /**
     * Withdraw an escrowed item from Marketplace custody back to Kafra storage.
     */
    public function withdrawToStorage(User $user, int $charId, int $marketplaceItemId): bool
    {
        $settings = $this->getSettings();
        if (!$settings['allow_withdrawals']) {
            throw new \RuntimeException('Item withdrawals are currently disabled by the administrator.');
        }

        $char = $this->getAccountCharacter($charId, $user->account_id);
        if (!$char) {
            throw new \RuntimeException('Invalid character selection.');
        }

        if ((int) $char->online > 0) {
            throw new \RuntimeException('Your character must be logged out of the game before withdrawing items.');
        }

        $item = MarketplaceItem::where('id', $marketplaceItemId)
            ->where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if (!$item) {
            throw new \RuntimeException('Marketplace item not found.');
        }

        if ($item->status !== 'in_custody') {
            throw new \RuntimeException('Only items currently in your custody inventory (not actively listed) can be withdrawn.');
        }

        $conn = DB::connection('ragnarok');

        return DB::transaction(function () use ($conn, $item) {
            // Check if stackable merge is possible
            $canStack = ($item->refine == 0 && $item->card0 == 0 && $item->card1 == 0 && $item->card2 == 0 && $item->card3 == 0);
            $merged = false;

            if ($canStack) {
                $existing = $conn->table('storage')
                    ->where('account_id', $item->account_id)
                    ->where('nameid', $item->nameid)
                    ->where('refine', 0)
                    ->where('card0', 0)
                    ->first();

                if ($existing && ($existing->amount + $item->amount) <= 30000) {
                    $conn->table('storage')->where('id', $existing->id)->increment('amount', $item->amount);
                    $merged = true;
                }
            }

            if (!$merged) {
                $conn->table('storage')->insert([
                    'account_id' => $item->account_id,
                    'nameid' => $item->nameid,
                    'amount' => $item->amount,
                    'equip' => $item->equip,
                    'identify' => $item->identify,
                    'refine' => $item->refine,
                    'attribute' => $item->attribute,
                    'card0' => $item->card0,
                    'card1' => $item->card1,
                    'card2' => $item->card2,
                    'card3' => $item->card3,
                    'expire_time' => $item->expire_time,
                    'bound' => $item->bound,
                    'unique_id' => $item->unique_id,
                ]);
            }

            $item->update([
                'status' => 'withdrawn',
                'withdrawn_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Admin force-returns an item from custody or active listing back into the owner's Kafra storage.
     */
    public function adminForceWithdrawToStorage(int $marketplaceItemId): bool
    {
        $item = MarketplaceItem::where('id', $marketplaceItemId)
            ->lockForUpdate()
            ->first();

        if (!$item) {
            throw new \RuntimeException('Marketplace custody item not found.');
        }

        if ($item->status === 'withdrawn') {
            throw new \RuntimeException('This item has already been returned to player storage.');
        }

        if ($item->status === 'sold') {
            throw new \RuntimeException('Cannot return an item that was already purchased and fulfilled.');
        }

        // If the item is actively listed, cancel the listing first
        if ($item->status === 'listed') {
            MarketplaceListing::where('marketplace_item_id', $item->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                ]);
        }

        $conn = DB::connection('ragnarok');

        return DB::transaction(function () use ($conn, $item) {
            $canStack = ($item->refine == 0 && $item->card0 == 0 && $item->card1 == 0 && $item->card2 == 0 && $item->card3 == 0);
            $merged = false;

            if ($canStack) {
                $existing = $conn->table('storage')
                    ->where('account_id', $item->account_id)
                    ->where('nameid', $item->nameid)
                    ->where('refine', 0)
                    ->where('card0', 0)
                    ->first();

                if ($existing && ($existing->amount + $item->amount) <= 30000) {
                    $conn->table('storage')->where('id', $existing->id)->increment('amount', $item->amount);
                    $merged = true;
                }
            }

            if (!$merged) {
                $conn->table('storage')->insert([
                    'account_id' => $item->account_id,
                    'nameid' => $item->nameid,
                    'amount' => $item->amount,
                    'equip' => $item->equip,
                    'identify' => $item->identify,
                    'refine' => $item->refine,
                    'attribute' => $item->attribute,
                    'card0' => $item->card0,
                    'card1' => $item->card1,
                    'card2' => $item->card2,
                    'card3' => $item->card3,
                    'expire_time' => $item->expire_time,
                    'bound' => $item->bound,
                    'unique_id' => $item->unique_id,
                ]);
            }

            $item->update([
                'status' => 'withdrawn',
                'withdrawn_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Create a new marketplace listing from an item in custody.
     */
    public function createListing(
        User $user,
        int $charId,
        int $marketplaceItemId,
        string $currencyType,
        int $unitPrice,
        int $quantity,
        int $durationHours
    ): MarketplaceListing {
        $settings = $this->getSettings();
        if (!$this->isOperational()) {
            throw new \RuntimeException('The Player Marketplace is currently closed for maintenance.');
        }

        $char = $this->getAccountCharacter($charId, $user->account_id);
        if (!$char) {
            throw new \RuntimeException('Invalid character selection.');
        }

        if (!in_array($currencyType, ['zeny', 'cashpoints'])) {
            throw new \RuntimeException('Invalid currency selected.');
        }

        if ($currencyType === 'zeny' && !$settings['allow_zeny']) {
            throw new \RuntimeException('Zeny trading is currently disabled.');
        }

        if ($currencyType === 'cashpoints' && !$settings['allow_cashpoints']) {
            throw new \RuntimeException('Cash Points trading is currently disabled.');
        }

        if ($unitPrice < $settings['min_price'] || $unitPrice > $settings['max_price']) {
            throw new \RuntimeException("Price must be between " . number_format($settings['min_price']) . " and " . number_format($settings['max_price']));
        }

        if (!in_array($durationHours, $settings['allowed_durations'])) {
            $durationHours = $settings['default_duration_hours'];
        }

        // Active listings limit checks
        $activePlayerListings = MarketplaceListing::where('seller_user_id', $user->id)
            ->where('status', 'active')
            ->count();

        if ($activePlayerListings >= $settings['max_listings_player']) {
            throw new \RuntimeException("You have reached the maximum allowed listings ({$settings['max_listings_player']}).");
        }

        $activeCharListings = MarketplaceListing::where('seller_char_id', $charId)
            ->where('status', 'active')
            ->count();

        if ($activeCharListings >= $settings['max_listings_char']) {
            throw new \RuntimeException("This character has reached the maximum allowed listings ({$settings['max_listings_char']}).");
        }

        if ($quantity > $settings['max_items_per_listing']) {
            throw new \RuntimeException("Quantity cannot exceed maximum allowed items per listing ({$settings['max_items_per_listing']}).");
        }

        if ($settings['listing_fee'] > 0) {
            $charZeny = $this->getCharacterZeny($char->char_id);
            if ($charZeny < $settings['listing_fee']) {
                throw new \RuntimeException("Insufficient Zeny for the listing fee of " . number_format($settings['listing_fee']) . " Zeny.");
            }
        }

        return DB::transaction(function () use ($user, $char, $marketplaceItemId, $currencyType, $unitPrice, $quantity, $durationHours, $settings) {
            if ($settings['listing_fee'] > 0) {
                DB::connection('ragnarok')->table('char')
                    ->where('char_id', $char->char_id)
                    ->decrement('zeny', $settings['listing_fee']);
            }

            $item = MarketplaceItem::where('id', $marketplaceItemId)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$item || $item->status !== 'in_custody') {
                throw new \RuntimeException('Item is not available for listing.');
            }

            if ($quantity <= 0 || $quantity > $item->amount) {
                throw new \RuntimeException("Invalid listing quantity ({$quantity}). Available: {$item->amount}.");
            }

            $totalPrice = $unitPrice * $quantity;
            $listingCode = 'MKT-' . strtoupper(Str::random(10));

            $listing = MarketplaceListing::create([
                'listing_code' => $listingCode,
                'item_id' => $item->id,
                'seller_user_id' => $user->id,
                'seller_account_id' => $user->account_id,
                'seller_char_id' => $char->char_id,
                'seller_char_name' => $char->name,
                'currency_type' => $currencyType,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'quantity' => $quantity,
                'remaining_quantity' => $quantity,
                'duration_hours' => $durationHours,
                'status' => 'active',
                'expires_at' => now()->addHours($durationHours),
            ]);

            $item->update(['status' => 'listed']);

            return $listing;
        });
    }

    /**
     * Cancel an active listing and return item to custody inventory.
     */
    public function cancelListing(User $user, int $listingId): bool
    {
        return DB::transaction(function () use ($user, $listingId) {
            $listing = MarketplaceListing::where('id', $listingId)
                ->where('seller_user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$listing || $listing->status !== 'active') {
                throw new \RuntimeException('Listing is not active or could not be found.');
            }

            $listing->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $listing->item->update(['status' => 'in_custody']);

            return true;
        });
    }

    /**
     * Purchase items from an active marketplace listing.
     */
    public function purchaseListing(User $buyer, int $charId, int $listingId, int $quantity = 1): MarketplaceTransaction
    {
        $settings = $this->getSettings();
        if (!$this->isOperational()) {
            throw new \RuntimeException('The Player Marketplace is currently closed for maintenance.');
        }

        $buyerChar = $this->getAccountCharacter($charId, $buyer->account_id);
        if (!$buyerChar) {
            throw new \RuntimeException('Invalid character selected for purchase.');
        }

        if ((int) $buyerChar->online > 0) {
            throw new \RuntimeException('Your character must be logged out of the game before completing marketplace purchases.');
        }

        $conn = DB::connection('ragnarok');

        return DB::transaction(function () use ($conn, $buyer, $buyerChar, $listingId, $quantity, $settings) {
            $listing = MarketplaceListing::where('id', $listingId)
                ->lockForUpdate()
                ->first();

            if (!$listing || !$listing->isActive()) {
                throw new \RuntimeException('This listing is no longer active or has expired.');
            }

            if ($listing->seller_user_id === $buyer->id) {
                throw new \RuntimeException('You cannot purchase your own listings.');
            }

            if ($quantity <= 0 || $quantity > $listing->remaining_quantity) {
                throw new \RuntimeException("Requested quantity ({$quantity}) exceeds available stock ({$listing->remaining_quantity}).");
            }

            $grossAmount = $listing->unit_price * $quantity;
            $taxPercent = $settings['tax_percent'];
            $taxAmount = (int) round(($grossAmount * $taxPercent) / 100);
            $netProceeds = max(0, $grossAmount - $taxAmount);

            // 1. Process payment deduction and credit
            if ($listing->currency_type === 'zeny') {
                $buyerZeny = $this->getCharacterZeny($buyerChar->char_id);
                if ($buyerZeny < $grossAmount) {
                    throw new \RuntimeException('Insufficient Zeny. You need ' . number_format($grossAmount) . ' Zeny.');
                }

                // Deduct Zeny from buyer char
                $conn->table('char')
                    ->where('char_id', $buyerChar->char_id)
                    ->decrement('zeny', $grossAmount);

                // Credit Net Zeny to seller char (capped at MAX_ZENY: 2,147,483,647)
                $conn->table('char')
                    ->where('char_id', $listing->seller_char_id)
                    ->update([
                        'zeny' => DB::raw("LEAST(zeny + {$netProceeds}, 2147483647)")
                    ]);
            } else {
                $buyerPoints = $this->getAccountCashPoints($buyer->account_id);
                if ($buyerPoints < $grossAmount) {
                    throw new \RuntimeException('Insufficient Cash Points. You need ' . number_format($grossAmount) . ' Cash Points.');
                }

                // Deduct Cash Points from buyer account
                $this->adjustCashPoints($buyer->account_id, -$grossAmount);

                // Credit Net Cash Points to seller account
                $this->adjustCashPoints($listing->seller_account_id, $netProceeds);
            }

            // 2. Deliver purchased item to buyer's Escrow Inbox (claimable to Storage or Character)
            $item = $listing->item;
            app(\App\Services\Escrow\EscrowInboxService::class)->depositItem(
                (int) $buyer->account_id,
                [
                    'user_id' => $buyer->id,
                    'source_type' => 'marketplace',
                    'source_id' => $listing->id,
                    'reward_type' => 'item',
                    'item_id' => (int) $item->nameid,
                    'item_name' => $item->name,
                    'quantity' => $quantity,
                    'refine' => (int) ($item->refine ?? 0),
                    'card0' => (int) ($item->card0 ?? 0),
                    'card1' => (int) ($item->card1 ?? 0),
                    'card2' => (int) ($item->card2 ?? 0),
                    'card3' => (int) ($item->card3 ?? 0),
                    'identified' => (bool) ($item->identify ?? true),
                    'bound' => (int) ($item->bound ?? 0),
                    'title' => "Marketplace Purchase: " . ($item->name ?? "Item #{$item->nameid}"),
                    'message' => "Purchased from {$listing->seller_name} ({$quantity}x). Ready to transfer into your game.",
                ]
            );

            // 3. Update listing and item state
            $newRemaining = $listing->remaining_quantity - $quantity;
            $listing->remaining_quantity = $newRemaining;
            if ($newRemaining <= 0) {
                $listing->status = 'sold';
                $listing->sold_at = now();
                $item->status = 'sold';
                $item->save();
            }
            $listing->save();

            // 4. Create Transaction Record
            return MarketplaceTransaction::create([
                'transaction_code' => 'TX-' . strtoupper(Str::random(10)),
                'listing_id' => $listing->id,
                'item_id' => $item->id,
                'seller_user_id' => $listing->seller_user_id,
                'seller_account_id' => $listing->seller_account_id,
                'seller_char_id' => $listing->seller_char_id,
                'seller_char_name' => $listing->seller_char_name,
                'buyer_user_id' => $buyer->id,
                'buyer_account_id' => $buyer->account_id,
                'buyer_char_id' => $buyerChar->char_id,
                'buyer_char_name' => $buyerChar->name,
                'currency_type' => $listing->currency_type,
                'quantity' => $quantity,
                'unit_price' => $listing->unit_price,
                'gross_amount' => $grossAmount,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'net_seller_proceeds' => $netProceeds,
                'status' => 'completed',
                'delivery_status' => 'delivered_to_storage',
            ]);
        });
    }

    /**
     * Expire any listings past their expiration date and release items back to custody.
     */
    public function expireOutdatedListings(): int
    {
        $expiredListings = MarketplaceListing::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($expiredListings as $listing) {
            $listing->update(['status' => 'expired']);
            if ($listing->item && $listing->item->status === 'listed') {
                $listing->item->update(['status' => 'in_custody']);
            }
            $count++;
        }

        return $count;
    }
}
