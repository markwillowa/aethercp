<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReputationTransaction extends Model
{
    protected $fillable = [
        'recipient_user_id',
        'source_user_id',
        'event_type',
        'points',
        'topic_id',
        'post_id',
        'event_reference',
        'description',
        'is_reversal',
        'reversed_transaction_id',
    ];

    protected $casts = [
        'points' => 'integer',
        'is_reversal' => 'boolean',
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }
}
