<?php

namespace App\Models;

use App\Contracts\Repositories\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CashShopProduct extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'item_id',
        'item_quantity',
        'cash_points_price',
        'promotional_price',
        'promotion_start_at',
        'promotion_end_at',
        'stock_quantity',
        'unlimited_stock',
        'purchase_limit_per_account',
        'purchase_limit_daily',
        'item_metadata',
        'image_url',
        'status',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'item_id' => 'integer',
        'item_quantity' => 'integer',
        'cash_points_price' => 'integer',
        'promotional_price' => 'integer',
        'stock_quantity' => 'integer',
        'unlimited_stock' => 'boolean',
        'purchase_limit_per_account' => 'integer',
        'purchase_limit_daily' => 'integer',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'item_metadata' => 'array',
        'promotion_start_at' => 'datetime',
        'promotion_end_at' => 'datetime',
    ];

    protected $appends = [
        'effective_price',
        'is_on_sale',
        'discount_percent',
        'icon_url',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prod) {
            if (empty($prod->slug)) {
                $base = Str::slug($prod->name);
                $count = static::where('slug', 'like', "{$base}%")->count();
                $prod->slug = $count > 0 ? "{$base}-" . ($count + 1) : $base;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CashShopCategory::class, 'category_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(CashShopPurchase::class, 'product_id');
    }

    public function getIsOnSaleAttribute(): bool
    {
        if (empty($this->promotional_price) || $this->promotional_price >= $this->cash_points_price) {
            return false;
        }

        $now = now();
        if ($this->promotion_start_at && $now->lt($this->promotion_start_at)) {
            return false;
        }
        if ($this->promotion_end_at && $now->gt($this->promotion_end_at)) {
            return false;
        }

        return true;
    }

    public function getEffectivePriceAttribute(): int
    {
        return $this->is_on_sale ? (int) $this->promotional_price : (int) $this->cash_points_price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (!$this->is_on_sale || $this->cash_points_price <= 0) {
            return 0;
        }
        $diff = $this->cash_points_price - $this->promotional_price;
        return (int) round(($diff / $this->cash_points_price) * 100);
    }

    public function getIconUrlAttribute(): string
    {
        if (!empty($this->image_url)) {
            return $this->image_url;
        }

        return "https://static.divine-pride.net/images/items/item/{$this->item_id}.png";
    }

    public function getQuantityAttribute(): int
    {
        return $this->item_quantity;
    }

    public function getRefineAttribute(): int
    {
        return (int) ($this->item_metadata['refine'] ?? 0);
    }

    public function getSlotsAttribute(): int
    {
        return (int) ($this->item_metadata['slots'] ?? 0);
    }

    public function getBoundAttribute(): int
    {
        return (int) ($this->item_metadata['bound'] ?? 0);
    }

    public function getHasStockLimitAttribute(): bool
    {
        return !$this->unlimited_stock;
    }

    public function getPerAccountLimitAttribute(): int
    {
        return (int) $this->purchase_limit_per_account;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Check if a player account is eligible to purchase this product.
     */
    public function canPurchase(int $accountId, int $quantity = 1, ?int $currentBalance = null): array
    {
        if ($this->status !== 'published') {
            return ['allowed' => false, 'reason' => 'This product is currently not available for purchase.'];
        }

        if (!$this->unlimited_stock && ($this->stock_quantity < $quantity)) {
            return ['allowed' => false, 'reason' => 'Insufficient product stock available.'];
        }

        if ($this->purchase_limit_per_account > 0) {
            $totalPurchased = CashShopPurchase::where('account_id', $accountId)
                ->where('product_id', $this->id)
                ->where('status', 'completed')
                ->sum('item_quantity');

            if (($totalPurchased + $quantity) > $this->purchase_limit_per_account) {
                return ['allowed' => false, 'reason' => "You have reached the lifetime purchase limit ({$this->purchase_limit_per_account}) for this item."];
            }
        }

        if ($this->purchase_limit_daily > 0) {
            $dailyPurchased = CashShopPurchase::where('account_id', $accountId)
                ->where('product_id', $this->id)
                ->where('status', 'completed')
                ->whereDate('created_at', now()->toDateString())
                ->sum('item_quantity');

            if (($dailyPurchased + $quantity) > $this->purchase_limit_daily) {
                return ['allowed' => false, 'reason' => "You have reached the daily purchase limit ({$this->purchase_limit_daily}) for this item."];
            }
        }

        $totalCost = $this->effective_price * $quantity;
        if ($currentBalance !== null && $currentBalance < $totalCost) {
            return ['allowed' => false, 'reason' => 'Insufficient Cash Points. You need ' . number_format($totalCost) . ' CP.'];
        }

        return ['allowed' => true, 'reason' => null];
    }
}
