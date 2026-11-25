<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerInboxItem extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'character_id',
        'title',
        'message',
        'source_type',
        'source_id',
        'reward_type',
        'item_id',
        'item_name',
        'quantity',
        'zeny_amount',
        'cashpoints_amount',
        'refine',
        'card0',
        'card1',
        'card2',
        'card3',
        'identified',
        'bound',
        'status',
        'claimed_to',
        'claimed_character_id',
        'claimed_at',
        'expires_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'account_id' => 'integer',
        'character_id' => 'integer',
        'item_id' => 'integer',
        'quantity' => 'integer',
        'zeny_amount' => 'integer',
        'cashpoints_amount' => 'integer',
        'refine' => 'integer',
        'card0' => 'integer',
        'card1' => 'integer',
        'card2' => 'integer',
        'card3' => 'integer',
        'identified' => 'boolean',
        'bound' => 'integer',
        'claimed_character_id' => 'integer',
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getIconUrlAttribute(): ?string
    {
        if ($this->reward_type === 'item' && $this->item_id > 0) {
            return "https://static.divine-pride.net/images/items/item/{$this->item_id}.png";
        }
        return null;
    }

    public function getFormattedTitleAttribute(): string
    {
        if ($this->reward_type === 'zeny') {
            return number_format($this->zeny_amount) . ' Zeny';
        }
        if ($this->reward_type === 'cashpoints') {
            return number_format($this->cashpoints_amount) . ' Cash Points';
        }

        $prefix = $this->refine > 0 ? "+{$this->refine} " : '';
        $name = $this->item_name ?: "Item #{$this->item_id}";
        $qty = $this->quantity > 1 ? " ({$this->quantity}x)" : '';

        return "{$prefix}{$name}{$qty}";
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source_type) {
            'attendance', 'daily' => 'Daily Attendance',
            'marketplace' => 'Marketplace Escrow',
            'cash_shop' => 'Cash Shop',
            'ranking' => 'Leaderboard Reward',
            'donation' => 'Donation Package',
            default => ucfirst($this->source_type ?? 'Web System'),
        };
    }

    public function getSourceBadgeColorAttribute(): string
    {
        return match ($this->source_type) {
            'attendance', 'daily' => 'bg-amber-500/20 text-amber-300 border-amber-500/40',
            'marketplace' => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40',
            'cash_shop' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40',
            'ranking' => 'bg-purple-500/20 text-purple-300 border-purple-500/40',
            'donation' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
            default => 'bg-slate-800 text-slate-300 border-slate-700',
        };
    }

    public function getClaimedDestinationLabelAttribute(): string
    {
        if ($this->claimed_to === 'inventory') {
            return $this->claimed_character_id ? "Character #{$this->claimed_character_id}" : 'Character Inventory';
        }
        return 'Kafra Storage';
    }
}
