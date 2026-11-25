<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CashShopCategory;
use App\Models\CashShopProduct;
use App\Models\CashShopPurchase;
use App\Services\CashPoints\CashPointsService;
use App\Services\CashShop\CashShopPurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashShopController extends Controller
{
    public function __construct(
        protected CashPointsService $cashPointsService,
        protected CashShopPurchaseService $purchaseService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $balance = $user && $user->account_id ? $this->cashPointsService->getBalance($user->account_id) : 0;

        $categories = CashShopCategory::where('is_active', true)
            ->withCount('publishedProducts')
            ->orderBy('sort_order')
            ->get();

        $selectedCategorySlug = $request->query('category', 'all');
        $selectedCategory = $selectedCategorySlug !== 'all' ? $categories->firstWhere('slug', $selectedCategorySlug) : null;

        $query = CashShopProduct::published()->with('category');

        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory->id);
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('item_id', 'like', "%{$search}%");
            });
        }

        $sort = $request->query('sort', 'featured');
        match ($sort) {
            'price_asc' => $query->orderBy('cash_points_price', 'asc'),
            'price_desc' => $query->orderBy('cash_points_price', 'desc'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('is_featured')->orderBy('sort_order')->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $featuredProducts = CashShopProduct::published()
            ->featured()
            ->with('category')
            ->take(4)
            ->get();

        return view('cash_shop.index', compact(
            'products',
            'categories',
            'selectedCategorySlug',
            'selectedCategory',
            'featuredProducts',
            'balance'
        ));
    }

    public function show(string $slug)
    {
        $product = CashShopProduct::published()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        $user = Auth::user();
        $balance = $user && $user->account_id ? $this->cashPointsService->getBalance($user->account_id) : 0;

        return view('cash_shop.show', compact('product', 'balance'));
    }

    public function buy(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to purchase items from the Cash Shop.');
        }

        $quantity = max(1, (int) $request->input('quantity', 1));

        try {
            $purchase = $this->purchaseService->purchase(
                $user,
                $id,
                $quantity,
                $request->ip()
            );

            return redirect()->route('escrow.index', ['source' => 'cash_shop'])
                ->with('success', "Purchase [{$purchase->purchase_code}] complete! Your items have arrived in your Escrow Inbox ready for in-game delivery.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $balance = $user->account_id ? $this->cashPointsService->getBalance($user->account_id) : 0;

        $purchases = CashShopPurchase::where('account_id', $user->account_id)
            ->with(['product', 'escrowItem'])
            ->latest()
            ->paginate(15);

        return view('cash_shop.history', compact('purchases', 'balance'));
    }
}
