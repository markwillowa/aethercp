<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceClaim extends Model
{
    protected $fillable = [
        'participation_id',
        'campaign_id',
        'campaign_day_id',
        'day_number',
        'account_id',
        'character_id',
        'claimed_at',
        'status',
        'idempotency_key',
    ];

    protected $casts = [
        'participation_id' => 'integer',
        'campaign_id' => 'integer',
        'campaign_day_id' => 'integer',
        'day_number' => 'integer',
        'account_id' => 'integer',
        'character_id' => 'integer',
        'claimed_at' => 'datetime',
    ];

    public function participation(): BelongsTo
    {
        return $this->belongsTo(AttendanceParticipation::class, 'participation_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AttendanceCampaign::class, 'campaign_id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(AttendanceCampaignDay::class, 'campaign_day_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(AttendanceDelivery::class, 'claim_id');
    }
}
