<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceParticipation extends Model
{
    protected $fillable = [
        'campaign_id',
        'user_id',
        'account_id',
        'character_id',
        'current_progress',
        'current_streak',
        'highest_streak',
        'total_claimed_days',
        'last_attendance_date',
        'last_claimed_at',
        'started_at',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'account_id' => 'integer',
        'character_id' => 'integer',
        'current_progress' => 'integer',
        'current_streak' => 'integer',
        'highest_streak' => 'integer',
        'total_claimed_days' => 'integer',
        'last_attendance_date' => 'date',
        'last_claimed_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AttendanceCampaign::class, 'campaign_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(AttendanceClaim::class, 'participation_id')->orderBy('day_number');
    }
}
