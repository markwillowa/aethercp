<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Faq extends Model
{
    protected $table = 'faqs';

    protected $fillable = [
        'category_id',
        'question',
        'slug',
        'short_description',
        'answer',
        'keywords',
        'status',
        'is_featured',
        'views_count',
        'seo_title',
        'seo_description',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'published_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $base = Str::slug($model->question);
                $slug = $base ?: 'faq-' . Str::random(6);
                $c = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $c++;
                }
                $model->slug = $slug;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(FaqFeedback::class, 'faq_id');
    }

    public function relatedQuestions(): BelongsToMany
    {
        return $this->belongsToMany(
            Faq::class,
            'faq_related_questions',
            'faq_id',
            'related_faq_id'
        )->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('question', 'LIKE', "%{$term}%")
              ->orWhere('answer', 'LIKE', "%{$term}%")
              ->orWhere('keywords', 'LIKE', "%{$term}%")
              ->orWhere('short_description', 'LIKE', "%{$term}%");
        });
    }

    public function helpfulVotesCount(): int
    {
        return $this->feedback()->where('feedback_type', 'helpful')->count();
    }

    public function notHelpfulVotesCount(): int
    {
        return $this->feedback()->where('feedback_type', 'not_helpful')->count();
    }

    public function helpfulPercentage(): int
    {
        $helpful = $this->helpfulVotesCount();
        $total = $helpful + $this->notHelpfulVotesCount();

        if ($total === 0) {
            return 100;
        }

        return (int) round(($helpful / $total) * 100);
    }
}
