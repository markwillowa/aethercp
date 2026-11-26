<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumUserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'identity_mode',
        'selected_char_id',
        'reputation_points',
        'forum_title',
        'signature',
    ];

    protected $casts = [
        'selected_char_id' => 'integer',
        'reputation_points' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function forUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'identity_mode' => 'username',
                'reputation_points' => 0,
            ]
        );
    }
}
