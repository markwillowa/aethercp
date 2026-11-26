<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAdminNote extends Model
{
    protected $fillable = [
        'char_id',
        'admin_user_id',
        'note',
    ];

    protected $casts = [
        'char_id' => 'integer',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
