<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RedeemRedemption extends Model
{
    public const STATUSES = ['pending', 'processing', 'completed', 'partially_failed', 'failed', 'cancelled'];
    public const DELIVERY_STATUSES = ['pending', 'processing', 'delivered', 'failed', 'retrying'];

    protected $fillable = [
        'campaign_id',
        'redeem_code_id',
        'user_id',
        'account_id',
        'username_snapshot',
        'character_id',
        'character_name_snapshot',
        'code_hint',
        'status',
        'delivery_status',
        'idempotency_key',
        'ip_address',
        'reward_summary',
        'redeemed_at',
        'completed_at',
    ];

    protected $casts = [
        'account_id' => 'integer',
        'character_id' => 'integer',
        'reward_summary' => 'array',
        'redeemed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(RedeemCampaign::class, 'campaign_id');
    }

    public function code(): BelongsTo
    {
        return $this->belongsTo(RedeemCode::class, 'redeem_code_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(RedeemRewardDelivery::class, 'redemption_id');
    }
}
