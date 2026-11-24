<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeTier extends Model
{
    protected $fillable = [
        'category',
        'tier_name',
        'tier_order',
        'stars',
        'min_kills',
        'color',
        'is_enabled',
    ];

    protected $casts = [
        'tier_order' => 'integer',
        'stars' => 'integer',
        'min_kills' => 'integer',
        'is_enabled' => 'boolean',
    ];

    public function getFormattedLabelAttribute(): string
    {
        $categoryPrefix = strtoupper($this->category);
        return "[{$categoryPrefix}] {$this->tier_name} " . str_repeat('★', $this->stars);
    }
}
