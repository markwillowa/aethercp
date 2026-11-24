<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaqFeedback extends Model
{
    protected $table = 'faq_feedback';
    public $timestamps = false;

    protected $fillable = [
        'faq_id',
        'account_id',
        'feedback_type',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function faq(): BelongsTo
    {
        return $this->belongsTo(Faq::class, 'faq_id');
    }
}
