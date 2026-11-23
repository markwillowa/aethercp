<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationPackage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'points',
        'bonus_points',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'points' => 'integer',
        'bonus_points' => 'integer',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }
}

