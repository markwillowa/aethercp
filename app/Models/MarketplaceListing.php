<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceListing extends Model
{
    protected $fillable = [
        'listing_code',
        'item_id',
        'seller_user_id',
        'seller_account_id',
        'seller_char_id',
        'seller_char_name',
        'currency_type',
        'unit_price',
        'total_price',
        'quantity',
        'remaining_quantity',
        'duration_hours',
        'status',
        'expires_at',
        'sold_at',
        'cancelled_at',
    ];

    protected $casts = [
        'seller_account_id' => 'integer',
        'seller_char_id' => 'integer',
        'unit_price' => 'integer',
        'total_price' => 'integer',
        'quantity' => 'integer',
        'remaining_quantity' => 'integer',
        'duration_hours' => 'integer',
        'expires_at' => 'datetime',
        'sold_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(MarketplaceItem::class, 'item_id');
    }

    public function sellerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MarketplaceTransaction::class, 'listing_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('remaining_quantity', '>', 0);
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->status === 'active' && $this->expires_at->isPast());
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture() && $this->remaining_quantity > 0;
    }

    /**
     * Get remaining time in human readable format
     */
    public function getRemainingTimeAttribute(): string
    {
        if ($this->isExpired()) {
            return 'Expired';
        }
        return $this->expires_at->diffForHumans(['parts' => 2, 'short' => true]);
    }
}
