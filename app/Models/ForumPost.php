<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
        'is_first_post',
        'is_accepted_answer',
        'likes_count',
        'edited_at',
        'edited_by',
    ];

    protected $casts = [
        'is_first_post' => 'boolean',
        'is_accepted_answer' => 'boolean',
        'likes_count' => 'integer',
        'edited_at' => 'datetime',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(ForumPostReaction::class, 'post_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ForumPostAttachment::class, 'post_id');
    }
}
