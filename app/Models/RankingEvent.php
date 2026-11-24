<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RankingEvent extends Model
{
    protected $fillable = [
        'event_uuid',
        'event_type',
        'char_id',
        'account_id',
        'target_char_id',
        'guild_id',
        'mob_id',
        'value',
        'payload',
        'status',
        'rejection_reason',
        'occurred_at',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'processed_at' => 'datetime',
    ];
}
