<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeAssignment extends Model
{
    protected $fillable = [
        'badge_id',
        'badge_tier_id',
        'target_type',
        'target_id',
        'category',
        'label',
        'stars',
        'color',
        'is_active',
        'is_permanent',
        'assigned_by',
        'assigned_by_user_id',
        'notes',
        'awarded_at',
        'revoked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_permanent' => 'boolean',
        'awarded_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function definition()
    {
        return $this->belongsTo(BadgeDefinition::class, 'badge_id');
    }

    public function tier()
    {
        return $this->belongsTo(BadgeTier::class, 'badge_tier_id');
    }
}
