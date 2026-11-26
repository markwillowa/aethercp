<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterDeletionRequest extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'char_id',
        'char_name',
        'status',
        'requested_at',
        'scheduled_delete_at',
        'executed_at',
        'cancelled_at',
        'recovery_status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'account_id' => 'integer',
        'char_id' => 'integer',
        'requested_at' => 'datetime',
        'scheduled_delete_at' => 'datetime',
        'executed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
