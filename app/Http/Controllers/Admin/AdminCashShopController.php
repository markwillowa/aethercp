<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\ItemRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CashShopCategory;
use App\Models\CashShopProduct;
use App\Models\CashShopPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCashShopController extends Controller
{
    public function __construct(
        protected ItemRepositoryInterface $itemRepo
    ) {}

    public function index(Request $request)
    {
        $query = CashShopProduct::with('category')->latest('sort_order');

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_id', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($catId = $request->query('category_id')) {
            $query->where('category_id', $catId);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = CashShopCategory::orderBy('sort_order')->get();

        $stats = [
            'total_products' => CashShopProduct::count(),
            'published_products' => CashShopProduct::where('status', 'published')->count(),
            'total_purchases' => CashShopPurchase::where('status', 'completed')->count(),
            'total_cp_spent' => (int) CashShopPurchase::where('status', 'completed')->sum('cash_points_spent'),
            'total_categories' => $categories->count(),
        ];

        return view('admin.cash_shop.index', compact('products', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = CashShopCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.cash_shop.form', [
            'product' => new CashShopProduct(),
            'categories' => $categories,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:cash_shop_categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'item_id' => 'required|integer|min:1',
            'item_quantity' => 'required|integer|min:1|max:30000',
            'cash_points_price' => 'required|integer|min:0',
            'promotional_price' => 'nullable|integer|min:0',
            'promotion_start_at' => 'nullable|date',
            'promotion_end_at' => 'nullable|date|after_or_equal:promotion_start_at',
            'stock_quantity' => 'nullable|integer|min:0',
            'unlimited_stock' => 'nullable|boolean',
            'purchase_limit_per_account' => 'nullable|integer|min:0',
            'purchase_limit_daily' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'image_url' => 'nullable|url|max:255',
            // Metadata
            'refine' => 'nullable|integer|min:0|max:20',
            'card0' => 'nullable|integer|min:0',
            'card1' => 'nullable|integer|min:0',
            'card2' => 'nullable|integer|min:0',
            'card3' => 'nullable|integer|min:0',
            'bound' => 'nullable|integer|min:0|max:5',
        ]);

        $metadata = [
            'refine' => (int) ($validated['refine'] ?? 0),
            'card0' => (int) ($validated['card0'] ?? 0),
            'card1' => (int) ($validated['card1'] ?? 0),
            'card2' => (int) ($validated['card2'] ?? 0),
            'card3' => (int) ($validated['card3'] ?? 0),
            'bound' => (int) ($validated['bound'] ?? 0),
        ];

        $validated['unlimited_stock'] = $request->boolean('unlimited_stock');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['item_metadata'] = $metadata;
        $validated['slug'] = Str::slug($validated['name']);

        $product = CashShopProduct::create($validated);

        ActivityLog::log('cash_shop.product_created', "Created Cash Shop product: {$product->name} (ID: {$product->id})");

        return redirect()->route('admin.cash-shop.index')->with('success', "Product '{$product->name}' created successfully!");
    }

    public function edit(CashShopProduct $product)
    {
        $categories = CashShopCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.cash_shop.form', [
            'product' => $product,
            'categories' => $categories,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, CashShopProduct $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:cash_shop_categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'item_id' => 'required|integer|min:1',
            'item_quantity' => 'required|integer|min:1|max:30000',
            'cash_points_price' => 'required|integer|min:0',
            'promotional_price' => 'nullable|integer|min:0',
            'promotion_start_at' => 'nullable|date',
            'promotion_end_at' => 'nullable|date|after_or_equal:promotion_start_at',
            'stock_quantity' => 'nullable|integer|min:0',
            'unlimited_stock' => 'nullable|boolean',
            'purchase_limit_per_account' => 'nullable|integer|min:0',
            'purchase_limit_daily' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'image_url' => 'nullable|url|max:255',
            // Metadata
            'refine' => 'nullable|integer|min:0|max:20',
            'card0' => 'nullable|integer|min:0',
            'card1' => 'nullable|integer|min:0',
            'card2' => 'nullable|integer|min:0',
            'card3' => 'nullable|integer|min:0',
            'bound' => 'nullable|integer|min:0|max:5',
        ]);

        $metadata = [
            'refine' => (int) ($validated['refine'] ?? 0),
            'card0' => (int) ($validated['card0'] ?? 0),
            'card1' => (int) ($validated['card1'] ?? 0),
            'card2' => (int) ($validated['card2'] ?? 0),
            'card3' => (int) ($validated['card3'] ?? 0),
            'bound' => (int) ($validated['bound'] ?? 0),
        ];

        $validated['unlimited_stock'] = $request->boolean('unlimited_stock');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['item_metadata'] = $metadata;

        $product->update($validated);

        ActivityLog::log('cash_shop.product_updated', "Updated Cash Shop product: {$product->name} (ID: {$product->id})");

        return redirect()->route('admin.cash-shop.index')->with('success', "Product '{$product->name}' updated successfully!");
    }

    public function destroy(CashShopProduct $product)
    {
        $name = $product->name;
        $product->delete();

        ActivityLog::log('cash_shop.product_deleted', "Deleted Cash Shop product: {$name}");

        return back()->with('success', "Product '{$name}' deleted.");
    }

    public function duplicate(CashShopProduct $product)
    {
        $replica = $product->replicate();
        $replica->name = $product->name . ' (Copy)';
        $replica->slug = Str::slug($replica->name) . '-' . Str::random(4);
        $replica->status = 'draft';
        $replica->save();

        ActivityLog::log('cash_shop.product_duplicated', "Duplicated product '{$product->name}' -> '{$replica->name}'");

        return redirect()->route('admin.cash-shop.edit', $replica->id)->with('success', "Duplicated as draft '{$replica->name}'.");
    }

    // ==================== CATEGORIES ====================

    public function categories()
    {
        $categories = CashShopCategory::withCount('products')->orderBy('sort_order')->get();
        return view('admin.cash_shop.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['icon'] = $validated['icon'] ?: 'tag';
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured');

        $cat = CashShopCategory::create($validated);

        ActivityLog::log('cash_shop.category_created', "Created Cash Shop category: {$cat->name}");

        return back()->with('success', "Category '{$cat->name}' created.");
    }

    public function updateCategory(Request $request, CashShopCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['icon'] = $validated['icon'] ?: 'tag';
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        $category->update($validated);

        ActivityLog::log('cash_shop.category_updated', "Updated Cash Shop category: {$category->name}");

        return back()->with('success', "Category '{$category->name}' updated.");
    }

    public function destroyCategory(CashShopCategory $category)
    {
        $name = $category->name;
        $category->delete();

        ActivityLog::log('cash_shop.category_deleted', "Deleted Cash Shop category: {$name}");

        return back()->with('success', "Category '{$name}' deleted.");
    }

    // ==================== PURCHASES LEDGER ====================

    public function purchases(Request $request)
    {
        $query = CashShopPurchase::with(['user', 'product', 'escrowItem'])->latest();

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_code', 'like', "%{$search}%")
                  ->orWhere('account_id', 'like', "%{$search}%")
                  ->orWhere('product_name_snapshot', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $purchases = $query->paginate(20)->withQueryString();

        $stats = [
            'total_sales' => CashShopPurchase::where('status', 'completed')->count(),
            'total_cp' => (int) CashShopPurchase::where('status', 'completed')->sum('cash_points_spent'),
            'failed_count' => CashShopPurchase::where('status', 'failed')->count(),
        ];

        return view('admin.cash_shop.purchases', compact('purchases', 'stats'));
    }

    // ==================== AJAX ITEM SEARCH ====================

    public function searchItems(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $items = $this->itemRepo->search($query, null, 15);

        $results = [];
        foreach ($items as $item) {
            $itemId = (int) ($item->id ?? $item->nameid ?? 0);
            $results[] = [
                'id' => $itemId,
                'name' => $item->name_english ?? $item->name_aegis ?? "Item #{$itemId}",
                'type' => $item->type ?? 'Item',
                'slots' => (int) ($item->slots ?? 0),
                'icon' => "https://static.divine-pride.net/images/items/item/{$itemId}.png",
            ];
        }

        return response()->json($results);
    }
}
