<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function boards(): HasMany
    {
        return $this->hasMany(ForumBoard::class, 'category_id')->orderBy('sort_order');
    }

    public function activeBoards(): HasMany
    {
        return $this->hasMany(ForumBoard::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
