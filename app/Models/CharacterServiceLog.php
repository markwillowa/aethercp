<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterServiceLog extends Model
{
    protected $fillable = [
        'actor_user_id',
        'actor_type',
        'account_id',
        'char_id',
        'action_type',
        'previous_values',
        'updated_values',
        'service_cost',
        'transaction_reference',
        'result',
        'failure_reason',
        'ip_address',
    ];

    protected $casts = [
        'account_id' => 'integer',
        'char_id' => 'integer',
        'previous_values' => 'array',
        'updated_values' => 'array',
        'service_cost' => 'integer',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
