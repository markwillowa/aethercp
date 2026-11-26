<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReputationAuditLog extends Model
{
    protected $fillable = [
        'admin_user_id',
        'recipient_user_id',
        'points_delta',
        'previous_total',
        'new_total',
        'reason',
        'transaction_id',
    ];

    protected $casts = [
        'points_delta' => 'integer',
        'previous_total' => 'integer',
        'new_total' => 'integer',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(ReputationTransaction::class, 'transaction_id');
    }
}
