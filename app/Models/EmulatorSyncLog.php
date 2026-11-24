<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmulatorSyncLog extends Model
{
    protected $fillable = [
        'sync_type',
        'status',
        'events_processed',
        'events_rejected',
        'error_message',
        'duration_ms',
        'synced_at',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
    ];
}
