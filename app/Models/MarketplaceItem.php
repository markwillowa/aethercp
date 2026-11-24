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

    protected $appends = [
        'slots',
        'cards',
        'card_count',
        'formatted_name',
        'icon_url',
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
     * Get item inherent slots (0-4) from metadata or emulator database
     */
    public function getSlotsAttribute(): int
    {
        if (is_array($this->metadata) && isset($this->metadata['slots'])) {
            return (int) $this->metadata['slots'];
        }

        try {
            $itemRepo = app(\App\Contracts\Repositories\ItemRepositoryInterface::class);
            $dbItem = $itemRepo->findById($this->nameid);
            if ($dbItem && isset($dbItem->slots)) {
                return (int) $dbItem->slots;
            }
        } catch (\Throwable $e) {
            // fallback
        }

        // If card IDs are present, item has at least that many slots
        $cardSlots = 0;
        if ($this->card0 > 0 && $this->card0 < 65000) $cardSlots++;
        if ($this->card1 > 0 && $this->card1 < 65000) $cardSlots++;
        if ($this->card2 > 0 && $this->card2 < 65000) $cardSlots++;
        if ($this->card3 > 0 && $this->card3 < 65000) $cardSlots++;

        return $cardSlots;
    }

    /**
     * Get compounded cards details
     */
    public function getCardsAttribute(): array
    {
        $cards = [];
        foreach (['card0', 'card1', 'card2', 'card3'] as $idx => $col) {
            $cardId = (int) $this->{$col};
            if ($cardId > 0 && $cardId < 65000) {
                $cards[] = [
                    'slot' => $idx + 1,
                    'id' => $cardId,
                    'name' => $this->getCardName($cardId),
                ];
            }
        }

        return $cards;
    }

    /**
     * Get number of compounded cards
     */
    public function getCardCountAttribute(): int
    {
        return count($this->cards);
    }

    /**
     * Resolve card name from Item repository
     */
    public function getCardName(int $cardId): string
    {
        try {
            $itemRepo = app(\App\Contracts\Repositories\ItemRepositoryInterface::class);
            $cardDb = $itemRepo->findById($cardId);
            return $cardDb ? ($cardDb->name_english ?? $cardDb->name_aegis ?? "Card #{$cardId}") : "Card #{$cardId}";
        } catch (\Throwable $e) {
            return "Card #{$cardId}";
        }
    }

    /**
     * Get formatted item title (e.g. "+7 Stiletto [3]" or "Poring Card")
     */
    public function getFormattedNameAttribute(): string
    {
        $prefix = $this->refine > 0 ? "+{$this->refine} " : '';
        $name = $this->item_name ?: "Item #{$this->nameid}";
        $slots = $this->slots;

        if ($slots > 0 && !preg_match('/\[\d+\]$/', trim($name))) {
            $slotText = " [{$slots}]";
        } else {
            $slotText = '';
        }

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
