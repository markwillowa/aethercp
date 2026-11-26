<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RedeemCode extends Model
{
    public const STATUSES = ['active', 'disabled', 'expired', 'exhausted'];

    protected $fillable = [
        'campaign_id',
        'code_hash',
        'code_prefix',
        'code_hint',
        'code_type',
        'max_redemptions',
        'redemptions_count',
        'bound_account_id',
        'expires_at',
        'status',
        'created_by',
    ];

    protected $casts = [
        'max_redemptions' => 'integer',
        'redemptions_count' => 'integer',
        'bound_account_id' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(RedeemCampaign::class, 'campaign_id');
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(RedeemCodeReward::class, 'redeem_code_id')->orderBy('sort_order');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(RedeemRedemption::class, 'redeem_code_id');
    }

    public function effectiveStatus(): string
    {
        if ($this->status === 'disabled') {
            return 'disabled';
        }
        if ($this->expires_at && now()->gt($this->expires_at)) {
            return 'expired';
        }
        if ($this->max_redemptions > 0 && $this->redemptions_count >= $this->max_redemptions) {
            return 'exhausted';
        }
        return $this->status;
    }
}
