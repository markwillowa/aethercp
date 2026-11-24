<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeDefinition extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'category',
        'icon_type',
        'icon',
        'color',
        'visual_style',
        'priority',
        'is_enabled',
        'is_permanent',
        'auto_condition_type',
        'auto_condition_value',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_permanent' => 'boolean',
        'priority' => 'integer',
    ];

    public function getIconUrlAttribute(): ?string
    {
        if ($this->icon_type === 'image' && !empty($this->icon)) {
            return asset($this->icon);
        }
        return null;
    }
}
