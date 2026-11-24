<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceCampaignItem extends Model
{
    protected $fillable = [
        'campaign_day_id',
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
        'delivery_method',
        'metadata',
    ];

    protected $casts = [
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
        'metadata' => 'array',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(AttendanceCampaignDay::class, 'campaign_day_id');
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
}
