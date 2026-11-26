<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReputationLevel extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'min_points',
        'badge_color',
        'badge_icon',
        'badge_style',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'min_points' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
