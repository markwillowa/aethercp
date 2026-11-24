<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardDeliveryLog extends Model
{
    protected $fillable = [
        'reward_issuance_id',
        'attempt_number',
        'status',
        'details',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
    ];
}
