<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqSearchLog extends Model
{
    protected $table = 'faq_search_logs';
    public $timestamps = false;

    protected $fillable = [
        'search_term',
        'results_count',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'results_count' => 'integer',
    ];
}
