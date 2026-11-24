<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceCampaignDay extends Model
{
    protected $fillable = [
        'campaign_id',
        'day_number',
        'title',
        'description',
        'is_milestone',
    ];

    protected $casts = [
        'day_number' => 'integer',
        'is_milestone' => 'boolean',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AttendanceCampaign::class, 'campaign_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AttendanceCampaignItem::class, 'campaign_day_id');
    }
}
