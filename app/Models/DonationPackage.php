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
        'item_id',
        'item_amount',
        'item_name',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'points' => 'integer',
        'bonus_points' => 'integer',
        'item_id' => 'integer',
        'item_amount' => 'integer',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }
}

