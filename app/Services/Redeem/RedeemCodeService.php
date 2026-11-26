<?php

namespace App\Services\Redeem;

use App\Contracts\Repositories\ItemRepositoryInterface;
use App\Models\ActivityLog;
use App\Models\RedeemCampaign;
use App\Models\RedeemCampaignLog;
use App\Models\RedeemCode;
use App\Models\RedeemCodeReward;
use App\Models\RedeemRedemption;
use App\Models\RedeemRewardDelivery;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RedeemCodeService
{
    public const PERMISSIONS = [
        'view_dashboard' => 'View Dashboard',
        'manage_campaigns' => 'Manage Campaigns',
        'generate_codes' => 'Generate Codes',
        'edit_codes' => 'Edit Codes',
        'disable_codes' => 'Disable Codes',
        'manage_rewards' => 'Manage Rewards',
        'view_history' => 'View Redemption History',
        'export_codes' => 'Export Codes',
        'export_logs' => 'Export Redemption Logs',
        'retry_deliveries' => 'Retry Failed Deliveries',
    ];

    public const CHARSETS = [
        'alphanumeric_safe' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789', // Excludes ambiguous 0/O, 1/I
        'alphanumeric' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        'alpha' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'numeric' => '0123456789',
    ];

    public function __construct(
        protected ItemRepositoryInterface $itemRepo
    ) {}

    public function normalizeCode(string $raw): string
    {
        return strtoupper(trim($raw));
    }

    public function hashCode(string $normalizedCode): string
    {
        return hash_hmac('sha256', $normalizedCode, (string) config('app.key'));
    }

    public function makeHint(string $normalizedCode): string
    {
        $len = strlen($normalizedCode);
        if ($len <= 4) {
            return str_repeat('*', $len);
        }
        $prefix = substr($normalizedCode, 0, min(4, (int) floor($len / 3)));
        $suffix = substr($normalizedCode, -min(4, (int) floor($len / 3)));
        return $prefix . '-****-' . $suffix;
    }

    public function generateRandomString(int $length, string $charsetKey = 'alphanumeric_safe'): string
    {
        $chars = self::CHARSETS[$charsetKey] ?? self::CHARSETS['alphanumeric_safe'];
        $maxIdx = strlen($chars) - 1;
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $chars[random_int(0, $maxIdx)];
        }
        return $out;
    }

    public function createCampaign(array $data, User $actor): RedeemCampaign
    {
        $slugBase = Str::slug($data['name']);
        $slug = $slugBase . '-' . strtolower(Str::random(5));

        $campaign = RedeemCampaign::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'banner_url' => $data['banner_url'] ?? null,
            'campaign_type' => $data['campaign_type'] ?? 'general_promotion',
            'instructions' => $data['instructions'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'is_public' => (bool) ($data['is_public'] ?? false),
            'max_total_redemptions' => $data['max_total_redemptions'] ?? null,
            'max_per_account' => max(1, (int) ($data['max_per_account'] ?? 1)),
            'one_per_account' => (bool) ($data['one_per_account'] ?? true),
            'one_per_character' => (bool) ($data['one_per_character'] ?? false),
            'allowed_account_ids' => $data['allowed_account_ids'] ?? null,
            'allowed_character_ids' => $data['allowed_character_ids'] ?? null,
            'account_age_rule' => $data['account_age_rule'] ?? 'any',
            'account_created_after' => $data['account_created_after'] ?? null,
            'account_created_before' => $data['account_created_before'] ?? null,
            'min_account_age_days' => $data['min_account_age_days'] ?? null,
            'min_base_level' => $data['min_base_level'] ?? null,
            'max_base_level' => $data['max_base_level'] ?? null,
            'allowed_classes' => $data['allowed_classes'] ?? null,
            'excluded_classes' => $data['excluded_classes'] ?? null,
            'created_by' => $actor->id,
        ]);

        $this->log($actor, 'campaign.created', "Created campaign [{$campaign->name}]", $campaign->id);
        ActivityLog::log('redeem.campaign_create', "Created redeem campaign [{$campaign->name}].", $actor->account_id);

        return $campaign;
    }

    public function updateCampaign(RedeemCampaign $campaign, array $data, User $actor): RedeemCampaign
    {
        $campaign->update($data);
        $this->log($actor, 'campaign.updated', "Updated campaign [{$campaign->name}]", $campaign->id);
        ActivityLog::log('redeem.campaign_update', "Updated redeem campaign [{$campaign->name}].", $actor->account_id);

        return $campaign->fresh();
    }

    /**
     * Generate single manual, single auto, bulk unique, or shared campaign codes.
     * Returns ['codes' => RedeemCode[], 'plaintext' => string[]].
     * Plaintext codes are NEVER stored in the database.
     */
    public function generateCodes(RedeemCampaign $campaign, array $options, User $actor): array
    {
        $mode = $options['mode'] ?? 'single_auto'; // single_manual, single_auto, bulk, shared
        $prefix = strtoupper(trim((string) ($options['prefix'] ?? '')));
        $length = max(4, min(24, (int) ($options['length'] ?? 8)));
        $charset = (string) ($options['charset'] ?? 'alphanumeric_safe');
        $count = ($mode === 'bulk') ? max(1, min(1000, (int) ($options['count'] ?? 10))) : 1;
        $maxRedemptions = max(1, (int) ($options['max_redemptions'] ?? 1));
        $expiresAt = $options['expires_at'] ?? null;
        $boundAccountId = !empty($options['bound_account_id']) ? (int) $options['bound_account_id'] : null;
        $status = ($options['status'] ?? 'active') === 'disabled' ? 'disabled' : 'active';

        $codeType = match ($mode) {
            'bulk' => 'bulk',
            'shared' => 'shared',
            default => 'single',
        };

        $createdModels = [];
        $plaintextList = [];

        DB::transaction(function () use (
            $campaign, $mode, $prefix, $length, $charset, $count,
            $maxRedemptions, $expiresAt, $boundAccountId, $status, $codeType,
            $options, $actor, &$createdModels, &$plaintextList
        ) {
            for ($i = 0; $i < $count; $i++) {
                $attempts = 0;
                do {
                    $attempts++;
                    if ($attempts > 25) {
                        throw new \RuntimeException('Unable to generate unique redeem code. Increase code length.');
                    }

                    if (in_array($mode, ['single_manual', 'shared'], true) && !empty($options['manual_code'])) {
                        $normalized = $this->normalizeCode((string) $options['manual_code']);
                    } else {
                        $segment = $this->generateRandomString($length, $charset);
                        $normalized = $prefix !== '' ? "{$prefix}-{$segment}" : $segment;
                    }

                    $hash = $this->hashCode($normalized);
                    $exists = RedeemCode::where('code_hash', $hash)->exists();
                    if ($exists && in_array($mode, ['single_manual', 'shared'], true)) {
                        throw new \InvalidArgumentException("Code [{$normalized}] already exists.");
                    }
                } while ($exists);

                $model = RedeemCode::create([
                    'campaign_id' => $campaign->id,
                    'code_hash' => $hash,
                    'code_prefix' => $prefix !== '' ? $prefix : null,
                    'code_hint' => $this->makeHint($normalized),
                    'code_type' => $codeType,
                    'max_redemptions' => $maxRedemptions,
                    'redemptions_count' => 0,
                    'bound_account_id' => $boundAccountId,
                    'expires_at' => $expiresAt,
                    'status' => $status,
                    'created_by' => $actor->id,
                ]);

                $createdModels[] = $model;
                $plaintextList[] = $normalized;
            }
        });

        $this->log(
            $actor,
            'codes.generated',
            "Generated " . count($createdModels) . " {$codeType} code(s) for campaign [{$campaign->name}]",
            $campaign->id,
            count($createdModels) === 1 ? $createdModels[0]->id : null,
            null,
            ['count' => count($createdModels), 'mode' => $mode]
        );
        ActivityLog::log('redeem.codes_generate', "Generated " . count($createdModels) . " redeem code(s) in [{$campaign->name}].", $actor->account_id);

        return [
            'codes' => $createdModels,
            'plaintext' => $plaintextList,
        ];
    }

    public function addReward(RedeemCampaign $campaign, array $data, User $actor): RedeemCodeReward
    {
        $type = $data['reward_type'];
        $itemId = null;
        $itemName = null;

        if ($type === 'item') {
            $itemId = (int) ($data['item_id'] ?? 0);
            if ($itemId <= 0) {
                throw new \InvalidArgumentException('Valid Item ID is required.');
            }
            $itemDb = $this->itemRepo->findById($itemId);
            if (!$itemDb) {
                throw new \InvalidArgumentException("Item #{$itemId} does not exist in the connected rAthena item database.");
            }
            $itemName = $itemDb->name_english ?? $itemDb->name_aegis ?? "Item #{$itemId}";
        }

        $reward = RedeemCodeReward::create([
            'campaign_id' => $campaign->id,
            'redeem_code_id' => $data['redeem_code_id'] ?? null,
            'reward_type' => $type,
            'amount' => max(1, (int) ($data['amount'] ?? 1)),
            'delivery_scope' => $data['delivery_scope'] ?? ($type === 'zeny' ? 'character' : 'account'),
            'item_id' => $itemId,
            'item_name' => $itemName,
            'refine' => max(0, min(20, (int) ($data['refine'] ?? 0))),
            'card0' => max(0, (int) ($data['card0'] ?? 0)),
            'card1' => max(0, (int) ($data['card1'] ?? 0)),
            'card2' => max(0, (int) ($data['card2'] ?? 0)),
            'card3' => max(0, (int) ($data['card3'] ?? 0)),
            'is_bound' => (bool) ($data['is_bound'] ?? false),
            'custom_payload' => $data['custom_payload'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        $this->log($actor, 'reward.added', "Added reward [{$reward->formattedLabel()}] to campaign [{$campaign->name}]", $campaign->id);
        ActivityLog::log('redeem.reward_add', "Added reward [{$reward->formattedLabel()}] to [{$campaign->name}].", $actor->account_id);

        return $reward;
    }

    public function getDashboardStats(?int $campaignId = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $codesQuery = RedeemCode::query();
        $redemptionsQuery = RedeemRedemption::query();
        $deliveriesQuery = RedeemRewardDelivery::where('status', 'delivered');

        if ($campaignId) {
            $codesQuery->where('campaign_id', $campaignId);
            $redemptionsQuery->where('campaign_id', $campaignId);
            $deliveriesQuery->whereHas('redemption', fn ($q) => $q->where('campaign_id', $campaignId));
        }

        if ($dateFrom) {
            $redemptionsQuery->where('created_at', '>=', $dateFrom . ' 00:00:00');
            $deliveriesQuery->where('created_at', '>=', $dateFrom . ' 00:00:00');
        }
        if ($dateTo) {
            $redemptionsQuery->where('created_at', '<=', $dateTo . ' 23:59:59');
            $deliveriesQuery->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        $allCodes = $codesQuery->get();
        $available = 0;
        $expired = 0;
        $exhausted = 0;
        $disabled = 0;

        foreach ($allCodes as $c) {
            match ($c->effectiveStatus()) {
                'active' => $available++,
                'expired' => $expired++,
                'exhausted' => $exhausted++,
                'disabled' => $disabled++,
                default => null,
            };
        }

        return [
            'total_campaigns' => RedeemCampaign::count(),
            'total_codes' => $allCodes->count(),
            'available_codes' => $available,
            'redeemed_codes' => (clone $redemptionsQuery)->count(),
            'expired_codes' => $expired,
            'exhausted_codes' => $exhausted,
            'disabled_codes' => $disabled,
            'total_cash_points' => (int) (clone $deliveriesQuery)->where('reward_type', 'cash_points')->sum('amount'),
            'total_zeny' => (int) (clone $deliveriesQuery)->where('reward_type', 'zeny')->sum('amount'),
            'total_items' => (int) (clone $deliveriesQuery)->where('reward_type', 'item')->sum('amount'),
            'recent_redemptions' => (clone $redemptionsQuery)->with(['campaign', 'deliveries'])->latest('id')->limit(15)->get(),
        ];
    }

    public function log(
        ?User $actor,
        string $action,
        string $details,
        ?int $campaignId = null,
        ?int $codeId = null,
        ?int $redemptionId = null,
        array $metadata = []
    ): void {
        RedeemCampaignLog::create([
            'campaign_id' => $campaignId,
            'redeem_code_id' => $codeId,
            'redemption_id' => $redemptionId,
            'actor_user_id' => $actor?->id,
            'action' => $action,
            'details' => $details,
            'metadata' => $metadata ?: null,
            'ip_address' => request()?->ip(),
        ]);
    }
}
