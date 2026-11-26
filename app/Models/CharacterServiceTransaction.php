<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterServiceTransaction extends Model
{
    protected $fillable = [
        'idempotency_key',
        'user_id',
        'account_id',
        'char_id',
        'char_name',
        'service_type',
        'cash_points_cost',
        'status',
        'old_values',
        'new_values',
        'failure_reason',
        'refunded_by',
        'refunded_at',
        'ip_address',
    ];

    protected $casts = [
        'account_id' => 'integer',
        'char_id' => 'integer',
        'cash_points_cost' => 'integer',
        'old_values' => 'array',
        'new_values' => 'array',
        'refunded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
