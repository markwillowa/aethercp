<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RankingCharacterStat extends Model
{
    protected $primaryKey = 'char_id';
    public $incrementing = false;

    protected $fillable = [
        'char_id',
        'account_id',
        'pvp_kills',
        'pvp_deaths',
        'mvp_kills',
        'max_base_reached_at',
        'max_base_level_achieved',
        'max_job_reached_at',
        'max_job_level_achieved',
        'last_event_at',
    ];

    protected $casts = [
        'max_base_reached_at' => 'datetime',
        'max_job_reached_at' => 'datetime',
        'last_event_at' => 'datetime',
    ];
}
