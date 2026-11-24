<?php

namespace App\Services\Ranking;

use App\Contracts\EmulatorInterface;
use App\Models\BadgeAssignment;
use App\Models\RewardDeliveryLog;
use App\Models\RewardDefinition;
use App\Models\RewardIssuance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RewardService
{
    public const MAX_ZENY = 2147483647;
    public const MAX_STORAGE_SLOTS = 600;
    public const MAX_ITEM_STACK = 30000;

    public function __construct(
        protected EmulatorInterface $emulator
    ) {}

    /**
     * Evaluate and issue eligible rewards when a badge or tier is assigned.
     */
    public function evaluateAndIssueForBadgeAssignment(BadgeAssignment $assignment): void
    {
        if (!Schema::hasTable('reward_definitions') || !$assignment->is_active) {
            return;
        }

        $query = RewardDefinition::where('is_enabled', true);

        if ($assignment->badge_id) {
            $query->where('trigger_type', 'badge')->where('badge_id', $assignment->badge_id);
        } elseif ($assignment->badge_tier_id) {
            $query->where('trigger_type', 'tier')->where('badge_tier_id', $assignment->badge_tier_id);
        } else {
            return;
        }

        $definitions = $query->get();
        foreach ($definitions as $def) {
            $this->issueReward($def, $assignment->target_type, (int) $assignment->target_id);
        }
    }

    /**
     * Create an idempotent reward issuance record and attempt delivery.
     */
    public function issueReward(RewardDefinition $def, string $targetType, int $targetId, ?string $uniqueSuffix = null): ?RewardIssuance
    {
        $scope = match ($def->policy) {
            'per_season', 'per_period' => ($def->period_key ?: now()->format('Y-m')),
            'repeatable' => $uniqueSuffix ?: Str::uuid()->toString(),
            default => 'lifetime',
        };

        $idempotencyKey = "rew_{$def->id}_{$targetType}_{$targetId}_{$scope}";

        if (RewardIssuance::where('idempotency_key', $idempotencyKey)->exists()) {
            return null;
        }

        $resolved = $this->resolveRecipient($targetType, $targetId);

        $issuance = RewardIssuance::create([
            'idempotency_key' => $idempotencyKey,
            'transaction_ref' => 'REW-' . strtoupper(Str::random(10)),
            'reward_definition_id' => $def->id,
            'target_type' => $targetType,
            'char_id' => $resolved['char_id'],
            'account_id' => $resolved['account_id'],
            'guild_id' => $targetType === 'guild' ? $targetId : null,
            'reward_type' => $def->reward_type,
            'zeny_amount' => $def->zeny_amount,
            'items_json' => $def->items_json,
            'status' => 'pending',
            'attempts' => 0,
        ]);

        $this->deliverIssuance($issuance);

        return $issuance->fresh();
    }

    /**
     * Safely deliver a pending or failed reward issuance to rAthena.
     */
    public function deliverIssuance(RewardIssuance $issuance): bool
    {
        // Atomic lock transition to prevent concurrent duplicate execution
        $locked = RewardIssuance::where('id', $issuance->id)
            ->whereIn('status', ['pending', 'failed', 'retry_required'])
            ->update(['status' => 'processing']);

        if ($locked === 0) {
            return false;
        }

        $issuance->refresh();
        $attemptNumber = $issuance->attempts + 1;

        try {
            if (!$issuance->char_id || !$issuance->account_id) {
                $resolved = $this->resolveRecipient(
                    $issuance->target_type,
                    (int) ($issuance->target_type === 'guild' ? $issuance->guild_id : $issuance->char_id)
                );
                $issuance->char_id = $resolved['char_id'];
                $issuance->account_id = $resolved['account_id'];
            }

            if (!$issuance->char_id || !$issuance->account_id) {
                throw new \RuntimeException('Recipient character or account could not be resolved in rAthena.');
            }

            $conn = DB::connection('ragnarok');
            $details = [];

            // 1. Zeny Delivery (Safe character balance update, never Kafra item)
            if (in_array($issuance->reward_type, ['zeny', 'multi_item'], true) && $issuance->zeny_amount > 0) {
                $charTable = $this->emulator->getTableName('char');
                $updated = $conn->table($charTable)
                    ->where('char_id', $issuance->char_id)
                    ->update([
                        'zeny' => DB::raw('LEAST(zeny + ' . (int) $issuance->zeny_amount . ', ' . self::MAX_ZENY . ')'),
                    ]);

                if ($updated === 0 && !$conn->table($charTable)->where('char_id', $issuance->char_id)->exists()) {
                    throw new \RuntimeException("Character #{$issuance->char_id} not found for Zeny credit.");
                }
                $details[] = 'Credited ' . number_format($issuance->zeny_amount) . " Zeny to char #{$issuance->char_id}";
            }

            // 2. Item Delivery (Direct to Kafra Storage)
            $items = is_array($issuance->items_json) ? $issuance->items_json : [];
            if (in_array($issuance->reward_type, ['item', 'multi_item'], true) && !empty($items)) {
                $storageTable = $this->emulator->getTableName('storage');
                $currentSlots = $conn->table($storageTable)->where('account_id', $issuance->account_id)->count();

                foreach ($items as $itemEntry) {
                    $nameid = (int) ($itemEntry['nameid'] ?? 0);
                    $amount = max(1, (int) ($itemEntry['amount'] ?? 1));
                    if ($nameid <= 0) {
                        continue;
                    }

                    // Try merging into existing clean stack within MAX_ITEM_STACK
                    $existing = $conn->table($storageTable)
                        ->where('account_id', $issuance->account_id)
                        ->where('nameid', $nameid)
                        ->where('refine', 0)
                        ->where('card0', 0)
                        ->where('bound', 0)
                        ->first();

                    if ($existing && ($existing->amount + $amount) <= self::MAX_ITEM_STACK) {
                        $conn->table($storageTable)->where('id', $existing->id)->increment('amount', $amount);
                    } else {
                        if ($currentSlots >= self::MAX_STORAGE_SLOTS) {
                            throw new \RuntimeException("Kafra storage full ({$currentSlots}/" . self::MAX_STORAGE_SLOTS . " slots) for account #{$issuance->account_id}.");
                        }
                        $conn->table($storageTable)->insert([
                            'account_id' => $issuance->account_id,
                            'nameid' => $nameid,
                            'amount' => min($amount, self::MAX_ITEM_STACK),
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
                        $currentSlots++;
                    }
                    $details[] = "Delivered {$amount}x Item #{$nameid} to Kafra storage (Acc #{$issuance->account_id})";
                }
            }

            $issuance->status = 'delivered';
            $issuance->attempts = $attemptNumber;
            $issuance->last_error = null;
            $issuance->delivered_at = now();
            $issuance->save();

            RewardDeliveryLog::create([
                'reward_issuance_id' => $issuance->id,
                'attempt_number' => $attemptNumber,
                'status' => 'delivered',
                'details' => implode(' | ', $details) ?: 'Reward delivered.',
                'attempted_at' => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            $issuance->status = $attemptNumber >= 5 ? 'failed' : 'retry_required';
            $issuance->attempts = $attemptNumber;
            $issuance->last_error = Str::limit($e->getMessage(), 500);
            $issuance->save();

            RewardDeliveryLog::create([
                'reward_issuance_id' => $issuance->id,
                'attempt_number' => $attemptNumber,
                'status' => 'failed',
                'details' => $e->getMessage(),
                'attempted_at' => now(),
            ]);

            Log::error('RewardService delivery failed', [
                'transaction_ref' => $issuance->transaction_ref,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function retryFailedDeliveries(): array
    {
        $pending = RewardIssuance::whereIn('status', ['retry_required', 'failed'])->get();
        $succeeded = 0;
        $failed = 0;

        foreach ($pending as $issuance) {
            if ($this->deliverIssuance($issuance)) {
                $succeeded++;
            } else {
                $failed++;
            }
        }

        return ['retried' => $pending->count(), 'succeeded' => $succeeded, 'failed' => $failed];
    }

    protected function resolveRecipient(string $targetType, int $targetId): array
    {
        $conn = DB::connection('ragnarok');
        $charTable = $this->emulator->getTableName('char');

        if ($targetType === 'guild') {
            $guildTable = $this->emulator->getTableName('guild');
            $guild = $conn->table($guildTable)->where('guild_id', $targetId)->first();
            if ($guild && $guild->char_id) {
                $char = $conn->table($charTable)->where('char_id', $guild->char_id)->first();
                if ($char) {
                    return ['char_id' => (int) $char->char_id, 'account_id' => (int) $char->account_id];
                }
            }
            return ['char_id' => null, 'account_id' => null];
        }

        $char = $conn->table($charTable)->where('char_id', $targetId)->first();
        return $char
            ? ['char_id' => (int) $char->char_id, 'account_id' => (int) $char->account_id]
            : ['char_id' => null, 'account_id' => null];
    }
}
