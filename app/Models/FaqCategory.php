<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FaqCategory extends Model
{
    protected $table = 'faq_categories';

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

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $base = Str::slug($model->name);
                $slug = $base;
                $c = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $c++;
                }
                $model->slug = $slug;
            }
        });
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'category_id');
    }

    public function publishedFaqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'category_id')
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderBy('id', 'desc');
    }
}
