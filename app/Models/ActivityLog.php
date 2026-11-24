<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'account_id' => 'integer',
    ];

    public static function log(string $action, string $description, ?int $accountId = null, array $details = []): static
    {
        return static::create([
            'user_id' => auth()->id(),
            'account_id' => $accountId ?? (auth()->user()?->account_id),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $details,
        ]);
    }

    public static function record(string $action, string $description, array $details = []): static
    {
        return static::log($action, $description, null, $details);
    }
}

