<?php

namespace App\Services\Donation;

use App\Models\Donation;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InGameRewardService
{
    /**
     * Deliver all rewards (Cash Points + in-game Storage items) for a donation.
     */
    public function fulfillDonation(Donation $donation): array
    {
        $result = [
            'points_delivered' => false,
            'items_delivered' => false,
            'messages' => [],
        ];

        try {
            // 1. Deliver Cash Points
            if ($donation->points_awarded > 0 && !$donation->points_delivered) {
                $newBalance = $this->creditCashPoints($donation->account_id, $donation->points_awarded);
                $donation->points_delivered = true;
                $result['points_delivered'] = true;
                $result['messages'][] = "Credited {$donation->points_awarded} Cash Points (New Balance: {$newBalance}).";
            }

            // 2. Deliver In-Game Item Rewards to Escrow Inbox (claimable to Storage or Character)
            $package = $donation->package;
            if ($package && $package->item_id > 0 && $package->item_amount > 0 && !$donation->items_delivered) {
                app(\App\Services\Escrow\EscrowInboxService::class)->depositItem(
                    (int) $donation->account_id,
                    [
                        'source_type' => 'donation',
                        'source_id' => $donation->id,
                        'reward_type' => 'item',
                        'item_id' => (int) $package->item_id,
                        'item_name' => $package->item_name,
                        'quantity' => (int) $package->item_amount,
                        'title' => "Donation Package: " . ($package->name ?? "Reward Item"),
                        'message' => "Thank you for supporting the server! Ready to transfer into your game.",
                    ]
                );

                $donation->items_delivered = true;
                $result['items_delivered'] = true;
                $itemName = $package->item_name ?: "Item #{$package->item_id}";
                $result['messages'][] = "Delivered {$package->item_amount}x {$itemName} to Escrow Inbox.";
            }

            $donation->save();

            // Log activity
            $summary = implode(' ', $result['messages']);
            ActivityLog::log(
                'donation.reward_delivered',
                "Donation #{$donation->id} fulfilled: {$summary}",
                $donation->account_id
            );

        } catch (\Throwable $e) {
            Log::error("Failed fulfilling donation #{$donation->id}: " . $e->getMessage(), [
                'exception' => $e,
                'donation' => $donation->toArray(),
            ]);
            $result['messages'][] = "Error delivering rewards: " . $e->getMessage();
        }

        return $result;
    }

    /**
     * Credit Cash Points directly into rAthena/Hercules account variables (#CASHPOINTS).
     */
    public function creditCashPoints(int $accountId, int $points): int
    {
        $conn = DB::connection('ragnarok');

        // Check if row exists in acc_reg_num
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
                'value' => $newTotal,
            ]);
        }

        // Also synchronize global_acc_reg_num if table exists
        try {
            $hasGlobal = $conn->getSchemaBuilder()->hasTable('global_acc_reg_num');
            if ($hasGlobal) {
                $existingGlobal = $conn->table('global_acc_reg_num')
                    ->where('account_id', $accountId)
                    ->where('key', '#CASHPOINTS')
                    ->where('index', 0)
                    ->first();

                if ($existingGlobal) {
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
        } catch (\Throwable $e) {
            // Non-fatal if global_acc_reg_num doesn't exist
        }

        return (int) $newTotal;
    }

    /**
     * Deliver an item directly into the account's in-game Kafra Storage.
     */
    public function deliverItemToStorage(int $accountId, int $itemId, int $amount = 1): bool
    {
        $conn = DB::connection('ragnarok');

        // Check if an existing stack of this item already exists in storage
        $existing = $conn->table('storage')
            ->where('account_id', $accountId)
            ->where('nameid', $itemId)
            ->where('equip', 0)
            ->where('card0', 0)
            ->where('card1', 0)
            ->where('card2', 0)
            ->where('card3', 0)
            ->where('refine', 0)
            ->first();

        // If an existing unrefined/uncarded stack exists, increment the amount (up to 30000 per stack)
        if ($existing && ($existing->amount + $amount) <= 30000) {
            return (bool) $conn->table('storage')
                ->where('id', $existing->id)
                ->increment('amount', $amount);
        }

        // Insert new item row into Kafra storage
        return $conn->table('storage')->insert([
            'account_id' => $accountId,
            'nameid' => $itemId,
            'amount' => $amount,
            'equip' => 0,
            'identify' => 1,
            'refine' => 0,
            'attribute' => 0,
            'card0' => 0,
            'card1' => 0,
            'card2' => 0,
            'card3' => 0,
            'expire_time' => 0,
            'bound' => 0,
            'unique_id' => 0,
        ]);
    }

    /**
     * Retrieve current #CASHPOINTS balance for an account.
     */
    public function getCashPoints(int $accountId): int
    {
        try {
            $row = DB::connection('ragnarok')->table('acc_reg_num')
                ->where('account_id', $accountId)
                ->where('key', '#CASHPOINTS')
                ->where('index', 0)
                ->value('value');

            return (int) ($row ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }
}

