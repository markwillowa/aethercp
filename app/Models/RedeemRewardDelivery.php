<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RedeemRewardDelivery extends Model
{
    public const STATUSES = ['pending', 'processing', 'delivered', 'failed', 'retrying'];

    protected $fillable = [
        'redemption_id',
        'reward_id',
        'reward_type',
        'idempotency_key',
        'status',
        'attempts',
        'account_id',
        'character_id',
        'amount',
        'item_id',
        'item_name',
        'payload',
        'destination_reference',
        'error_message',
        'delivered_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'account_id' => 'integer',
        'character_id' => 'integer',
        'amount' => 'integer',
        'item_id' => 'integer',
        'payload' => 'array',
        'delivered_at' => 'datetime',
    ];

    public function redemption(): BelongsTo
    {
        return $this->belongsTo(RedeemRedemption::class, 'redemption_id');
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(RedeemCodeReward::class, 'reward_id');
    }
}
