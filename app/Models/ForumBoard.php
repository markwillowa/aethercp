<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumBoard extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
        'is_locked',
        'allow_topics_roles',
        'topics_count',
        'posts_count',
        'last_topic_id',
        'last_post_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'allow_topics_roles' => 'array',
        'sort_order' => 'integer',
        'topics_count' => 'integer',
        'posts_count' => 'integer',
        'last_post_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    public function topics(): HasMany
    {
        return $this->hasMany(ForumTopic::class, 'board_id');
    }

    public function lastTopic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'last_topic_id');
    }

    public function canUserCreateTopic(?User $user): bool
    {
        if (!$user || !$this->is_active || $this->is_locked) {
            return false;
        }

        $allowedRoles = $this->allow_topics_roles;
        if (empty($allowedRoles)) {
            return true;
        }

        return in_array($user->role, $allowedRoles, true);
    }
}
