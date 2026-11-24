<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardDefinition extends Model
{
    protected $fillable = [
        'name',
        'category',
        'trigger_type',
        'badge_id',
        'badge_tier_id',
        'reward_type',
        'zeny_amount',
        'items_json',
        'policy',
        'period_key',
        'message',
        'is_enabled',
    ];

    protected $casts = [
        'items_json' => 'array',
        'zeny_amount' => 'integer',
        'is_enabled' => 'boolean',
    ];

    public function badge()
    {
        return $this->belongsTo(BadgeDefinition::class, 'badge_id');
    }

    public function tier()
    {
        return $this->belongsTo(BadgeTier::class, 'badge_tier_id');
    }
}
