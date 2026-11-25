<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CashShopCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cat) {
            if (empty($cat->slug)) {
                $cat->slug = Str::slug($cat->name);
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(CashShopProduct::class, 'category_id')->orderBy('sort_order');
    }

    public function publishedProducts(): HasMany
    {
        return $this->hasMany(CashShopProduct::class, 'category_id')
            ->where('status', 'published')
            ->orderBy('sort_order');
    }
}
