<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceTransaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'listing_id',
        'item_id',
        'seller_user_id',
        'seller_account_id',
        'seller_char_id',
        'seller_char_name',
        'buyer_user_id',
        'buyer_account_id',
        'buyer_char_id',
        'buyer_char_name',
        'currency_type',
        'quantity',
        'unit_price',
        'gross_amount',
        'tax_percent',
        'tax_amount',
        'net_seller_proceeds',
        'status',
        'delivery_status',
    ];

    protected $casts = [
        'seller_account_id' => 'integer',
        'seller_char_id' => 'integer',
        'buyer_account_id' => 'integer',
        'buyer_char_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'gross_amount' => 'integer',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'integer',
        'net_seller_proceeds' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(MarketplaceListing::class, 'listing_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(MarketplaceItem::class, 'item_id');
    }

    public function sellerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function buyerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function getTxCodeAttribute(): string
    {
        return $this->transaction_code ?? '';
    }

    public function getAmountAttribute(): int
    {
        return (int) ($this->quantity ?? 1);
    }

    public function getTotalPriceAttribute(): int
    {
        return (int) ($this->gross_amount ?? 0);
    }

    public function getItemNameAttribute(): string
    {
        return $this->item ? ($this->item->formatted_name ?? $this->item->item_name ?? 'Unknown Item') : 'Unknown Item';
    }
}
