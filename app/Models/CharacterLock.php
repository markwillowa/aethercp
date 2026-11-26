<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacterLock extends Model
{
    protected $fillable = [
        'char_id',
        'account_id',
        'is_locked',
        'lock_scope',
        'reason',
        'locked_by',
        'locked_at',
        'unlocked_by',
        'unlocked_at',
    ];

    protected $casts = [
        'char_id' => 'integer',
        'account_id' => 'integer',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'unlocked_at' => 'datetime',
    ];
}
