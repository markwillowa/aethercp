<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceDelivery extends Model
{
    protected $fillable = [
        'claim_id',
        'campaign_item_id',
        'reward_type',
        'delivery_type',
        'destination_target',
        'status',
        'attempts',
        'delivered_at',
        'failure_reason',
        'external_reference',
    ];

    protected $casts = [
        'claim_id' => 'integer',
        'campaign_item_id' => 'integer',
        'attempts' => 'integer',
        'delivered_at' => 'datetime',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(AttendanceClaim::class, 'claim_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(AttendanceCampaignItem::class, 'campaign_item_id');
    }
}
