<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashShopPurchase extends Model
{
    protected $fillable = [
        'purchase_code',
        'user_id',
        'account_id',
        'product_id',
        'product_name_snapshot',
        'item_id',
        'item_quantity',
        'cash_points_spent',
        'escrow_item_id',
        'status',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'account_id' => 'integer',
        'product_id' => 'integer',
        'item_id' => 'integer',
        'item_quantity' => 'integer',
        'cash_points_spent' => 'integer',
        'escrow_item_id' => 'integer',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(CashShopProduct::class, 'product_id');
    }

    public function escrowItem(): BelongsTo
    {
        return $this->belongsTo(PlayerInboxItem::class, 'escrow_item_id');
    }
}
