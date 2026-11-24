<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_reference',
        'user_id',
        'account_id',
        'char_id',
        'char_name',
        'subject',
        'category',
        'priority',
        'status',
        'assigned_staff_id',
        'related_reference',
        'map_name',
        'incident_time',
        'last_message_at',
        'resolved_at',
        'closed_at',
        'metadata',
    ];

    protected $casts = [
        'account_id' => 'integer',
        'char_id' => 'integer',
        'assigned_staff_id' => 'integer',
        'last_message_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    public function publicMessages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id')
            ->where('is_internal', false)
            ->orderBy('created_at', 'asc');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class, 'ticket_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(SupportTicketActivity::class, 'ticket_id')->orderBy('created_at', 'desc');
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['resolved', 'closed'], true);
    }

    public function isOpen(): bool
    {
        return !$this->isClosed();
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'account' => 'Account Issues',
            'character' => 'Character Issues',
            'in_game' => 'In-Game Issues',
            'technical' => 'Technical Support',
            'donation' => 'Payment / Donation Issues',
            'bug_report' => 'Bug Reports',
            'player_report' => 'Report a Player',
            default => 'Other',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Low Priority',
            'normal' => 'Normal Priority',
            'high' => 'High Priority',
            'urgent' => 'Urgent Priority',
            default => ucfirst($this->priority),
        };
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'bg-slate-700/50 text-slate-300 border-slate-600',
            'normal' => 'bg-blue-900/40 text-blue-300 border-blue-500/40',
            'high' => 'bg-amber-900/40 text-amber-300 border-amber-500/50',
            'urgent' => 'bg-rose-950/60 text-rose-300 border-rose-500/60 shadow-sm animate-pulse',
            default => 'bg-slate-800 text-slate-300 border-slate-700',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'awaiting_player' => 'Awaiting Response',
            'awaiting_staff' => 'Awaiting Staff',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'open' => 'bg-emerald-950/60 text-emerald-300 border-emerald-500/50',
            'in_progress' => 'bg-cyan-950/60 text-cyan-300 border-cyan-500/50',
            'awaiting_player' => 'bg-amber-950/60 text-amber-300 border-amber-500/50',
            'awaiting_staff' => 'bg-purple-950/60 text-purple-300 border-purple-500/50',
            'resolved' => 'bg-blue-950/60 text-blue-300 border-blue-500/50',
            'closed' => 'bg-slate-900/80 text-slate-400 border-slate-700',
            default => 'bg-slate-800 text-slate-300 border-slate-700',
        };
    }

    /**
     * Generate unique reference: e.g. AETH-YYYYMMDD-XXXXX
     */
    public static function generateReference(): string
    {
        $prefix = 'AETH-' . date('Ymd') . '-';
        do {
            $random = strtoupper(Str::random(5));
            $reference = $prefix . $random;
        } while (static::where('ticket_reference', $reference)->exists());

        return $reference;
    }
}
