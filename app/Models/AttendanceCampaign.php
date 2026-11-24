<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceCampaign extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration_days',
        'start_at',
        'end_at',
        'timezone',
        'status',
        'progression_mode',
        'reset_rule',
        'participation_scope',
        'delivery_destination',
        'allow_character_choice',
        'require_in_game_login',
        'repeatable',
        'display_order',
        'min_base_level',
        'min_account_age_days',
        'daily_reset_hour',
        'metadata',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'allow_character_choice' => 'boolean',
        'require_in_game_login' => 'boolean',
        'repeatable' => 'boolean',
        'display_order' => 'integer',
        'min_base_level' => 'integer',
        'min_account_age_days' => 'integer',
        'daily_reset_hour' => 'integer',
        'metadata' => 'array',
    ];

    public function days(): HasMany
    {
        return $this->hasMany(AttendanceCampaignDay::class, 'campaign_id')->orderBy('day_number');
    }

    public function participations(): HasMany
    {
        return $this->hasMany(AttendanceParticipation::class, 'campaign_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(AttendanceClaim::class, 'campaign_id');
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = Carbon::now($this->timezone ?: 'UTC');

        if ($this->start_at && $now->lt($this->start_at)) {
            return false;
        }

        if ($this->end_at && $now->gt($this->end_at)) {
            return false;
        }

        return true;
    }

    public function isAccountScoped(): bool
    {
        return $this->participation_scope === 'account';
    }
}
