<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerAchievement extends Model
{
    protected $fillable = [
        'char_id',
        'achievement_key',
        'title',
        'description',
        'category',
        'icon',
        'color',
        'earned_at',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
    ];
}
