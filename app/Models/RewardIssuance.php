<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardIssuance extends Model
{
    protected $fillable = [
        'idempotency_key',
        'transaction_ref',
        'reward_definition_id',
        'target_type',
        'char_id',
        'account_id',
        'guild_id',
        'reward_type',
        'zeny_amount',
        'items_json',
        'status',
        'attempts',
        'last_error',
        'delivered_at',
    ];

    protected $casts = [
        'items_json' => 'array',
        'zeny_amount' => 'integer',
        'delivered_at' => 'datetime',
    ];

    public function definition()
    {
        return $this->belongsTo(RewardDefinition::class, 'reward_definition_id');
    }

    public function logs()
    {
        return $this->hasMany(RewardDeliveryLog::class, 'reward_issuance_id');
    }
}
