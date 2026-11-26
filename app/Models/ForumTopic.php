<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumTopic extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'board_id',
        'user_id',
        'title',
        'slug',
        'is_pinned',
        'is_locked',
        'is_solved',
        'is_announcement',
        'is_approved_guide',
        'accepted_post_id',
        'views_count',
        'replies_count',
        'last_post_id',
        'last_post_user_id',
        'last_activity_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_solved' => 'boolean',
        'is_announcement' => 'boolean',
        'is_approved_guide' => 'boolean',
        'views_count' => 'integer',
        'replies_count' => 'integer',
        'last_activity_at' => 'datetime',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(ForumBoard::class, 'board_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'topic_id')->orderBy('created_at');
    }

    public function firstPost(): HasOne
    {
        return $this->hasOne(ForumPost::class, 'topic_id')->where('is_first_post', true);
    }

    public function lastPost(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'last_post_id');
    }

    public function lastPostUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_post_user_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ForumTopicSubscription::class, 'topic_id');
    }
}
