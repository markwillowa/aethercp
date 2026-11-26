<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RedeemCampaign extends Model
{
    use SoftDeletes;

    public const TYPES = [
        'general_promotion' => 'General Promotion',
        'welcome_reward' => 'Welcome Reward',
        'launch_event' => 'Launch Event',
        'community_event' => 'Community Event',
        'social_media' => 'Social Media Promotion',
        'giveaway' => 'Giveaway',
        'compensation' => 'Compensation',
        'custom' => 'Custom',
    ];

    public const STATUSES = ['draft', 'scheduled', 'active', 'ended', 'disabled'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_url',
        'campaign_type',
        'instructions',
        'starts_at',
        'ends_at',
        'status',
        'is_public',
        'max_total_redemptions',
        'max_per_account',
        'one_per_account',
        'one_per_character',
        'allowed_account_ids',
        'allowed_character_ids',
        'account_age_rule',
        'account_created_after',
        'account_created_before',
        'min_account_age_days',
        'min_base_level',
        'max_base_level',
        'allowed_classes',
        'excluded_classes',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_public' => 'boolean',
        'one_per_account' => 'boolean',
        'one_per_character' => 'boolean',
        'allowed_account_ids' => 'array',
        'allowed_character_ids' => 'array',
        'account_created_after' => 'datetime',
        'account_created_before' => 'datetime',
        'allowed_classes' => 'array',
        'excluded_classes' => 'array',
    ];

    public function codes(): HasMany
    {
        return $this->hasMany(RedeemCode::class, 'campaign_id');
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(RedeemCodeReward::class, 'campaign_id')->whereNull('redeem_code_id')->orderBy('sort_order');
    }

    public function allRewards(): HasMany
    {
        return $this->hasMany(RedeemCodeReward::class, 'campaign_id')->orderBy('sort_order');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(RedeemRedemption::class, 'campaign_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(RedeemCampaignLog::class, 'campaign_id');
    }

    public function isCurrentlyRedeemable(): bool
    {
        if (!in_array($this->status, ['active', 'scheduled'], true)) {
            return false;
        }
        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && now()->gt($this->ends_at)) {
            return false;
        }
        return true;
    }
}
