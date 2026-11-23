<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketplaceItem extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'char_id',
        'nameid',
        'item_name',
        'amount',
        'equip',
        'identify',
        'refine',
        'attribute',
        'card0',
        'card1',
        'card2',
        'card3',
        'expire_time',
        'bound',
        'unique_id',
        'item_type',
        'status',
        'deposited_at',
        'withdrawn_at',
        'metadata',
    ];

    protected $casts = [
        'account_id' => 'integer',
        'char_id' => 'integer',
        'nameid' => 'integer',
        'amount' => 'integer',
        'equip' => 'integer',
        'identify' => 'integer',
        'refine' => 'integer',
        'attribute' => 'integer',
        'card0' => 'integer',
        'card1' => 'integer',
        'card2' => 'integer',
        'card3' => 'integer',
        'expire_time' => 'integer',
        'bound' => 'integer',
        'unique_id' => 'integer',
        'deposited_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(MarketplaceListing::class, 'item_id');
    }

    public function activeListing(): HasOne
    {
        return $this->hasOne(MarketplaceListing::class, 'item_id')
            ->where('status', 'active');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MarketplaceTransaction::class, 'item_id');
    }

    /**
     * Get formatted item title (e.g. "+7 Stiletto [3]" or "Poring Card")
     */
    public function getFormattedNameAttribute(): string
    {
        $prefix = $this->refine > 0 ? "+{$this->refine} " : '';
        $name = $this->item_name ?: "Item #{$this->nameid}";
        
        $cardSlots = 0;
        if ($this->card0 > 0) $cardSlots++;
        if ($this->card1 > 0) $cardSlots++;
        if ($this->card2 > 0) $cardSlots++;
        if ($this->card3 > 0) $cardSlots++;

        $slotText = $cardSlots > 0 ? " [{$cardSlots}]" : '';

        return $prefix . $name . $slotText;
    }

    /**
     * Get item icon URL
     */
    public function getIconUrlAttribute(): string
    {
        return "https://static.divine-pride.net/images/items/item/{$this->nameid}.png";
    }
}
