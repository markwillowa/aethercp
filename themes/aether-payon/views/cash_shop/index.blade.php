@extends('layouts.app')

@section('content')
<div class="payon-container py-10 space-y-6" x-data="cashShopModal()">
    <!-- Payon Royal Cash Shop Header -->
    <div class="payon-wood-frame overflow-hidden">
        <div class="payon-wood-header">
            <div class="flex items-center gap-2">
                <i data-lucide="store" class="w-4 h-4 text-[#A88950]"></i>
                <span class="font-cinzel uppercase tracking-wider text-sm">Payon Royal Cash Shop</span>
            </div>
            <span class="text-[11px] font-mono text-[#E7D8B5]">
                Balance: {{ auth()->check() ? number_format($balance) . ' CP' : 'Guest' }}
            </span>
        </div>

        <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="font-cinzel text-2xl font-bold text-[#35291F]">Kafra Item Mall &amp; Supplies</h1>
                <p class="text-xs text-[#80633B] mt-1">
                    Acquire consumables, refinement supplies, and gear delivered safely to your <a href="{{ route('escrow.index') }}" class="text-[#263D2C] font-bold hover:underline">Escrow Inbox</a>.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('donations'))
                    <a href="{{ route('donations.index') }}" class="payon-btn-gold text-xs py-2 px-4">Top Up Points</a>
                @endif
                @auth
                    <a href="{{ route('cash-shop.history') }}" class="payon-btn-wood text-xs py-2 px-3.5">Purchases</a>
                    <a href="{{ route('escrow.index') }}" class="payon-btn-forest text-xs py-2 px-3.5">Escrow</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Search & Category Pills -->
    <div class="payon-parchment-panel p-4 space-y-3">
        <form method="GET" action="{{ route('cash-shop.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <input type="hidden" name="category" value="{{ $selectedCategorySlug }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search cash shop products by name or item ID..." class="flex-1 px-3.5 py-2 rounded text-xs">
            <select name="sort" onchange="this.form.submit()" class="px-3 py-2 rounded text-xs">
                <option value="featured" {{ request('sort', 'featured') === 'featured' ? 'selected' : '' }}>Featured</option>
                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low-High</option>
                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High-Low</option>
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
            </select>
            <button type="submit" class="payon-btn-forest text-xs py-2 px-4 shrink-0">Search</button>
        </form>

        <div class="pt-3 border-t border-[#A88950]/40 flex items-center gap-2 overflow-x-auto pb-1">
            <a href="{{ route('cash-shop.index', ['category' => 'all', 'sort' => request('sort'), 'q' => request('q')]) }}"
               class="px-3.5 py-1.5 rounded text-xs font-bold border shrink-0 {{ $selectedCategorySlug === 'all' ? 'bg-[#263D2C] text-[#FFF8E8] border-[#A88950]' : 'bg-[#F2E8CF] text-[#35291F] border-[#A88950] hover:bg-[#E7D8B5]' }}">
                All Categories
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('cash-shop.index', ['category' => $cat->slug, 'sort' => request('sort'), 'q' => request('q')]) }}"
                   class="px-3.5 py-1.5 rounded text-xs font-bold border shrink-0 {{ $selectedCategorySlug === $cat->slug ? 'bg-[#263D2C] text-[#FFF8E8] border-[#A88950]' : 'bg-[#F2E8CF] text-[#35291F] border-[#A88950] hover:bg-[#E7D8B5]' }}">
                    {{ $cat->name }} ({{ $cat->published_products_count }})
                </a>
            @endforeach
        </div>
    </div>

    <!-- Inventory-Inspired Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($products as $product)
                <div class="payon-reward-card p-5 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-[10px] font-cinzel font-bold uppercase text-[#80633B] truncate">{{ $product->category?->name ?? 'Shop Item' }}</span>
                            @if($product->is_on_sale)
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-[#8C2D2D] text-[#FFF8E8]">-{{ $product->discount_percent }}% OFF</span>
                            @endif
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded bg-[#FFF8E8] border border-[#A88950] flex items-center justify-center shrink-0">
                                <img src="{{ $product->icon_url }}" alt="{{ $product->name }}" class="w-8 h-8 object-contain" onerror="this.src='/images/item-placeholder.png'">
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('cash-shop.show', $product->slug) }}" class="font-cinzel text-sm font-bold text-[#35291F] hover:text-[#526D3A] block leading-snug">{{ $product->name }}</a>
                                @if($product->refine > 0)
                                    <span class="text-[10px] font-bold text-[#526D3A] font-mono">+{{ $product->refine }} Refine</span>
                                @endif
                            </div>
                        </div>
                        @if(!empty($product->description))
                            <p class="text-xs text-[#493322] line-clamp-2">{{ $product->description }}</p>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-[#A88950]/40 flex items-center justify-between gap-2">
                        <div>
                            <span class="font-mono text-sm font-bold text-[#263D2C]">{{ number_format($product->effective_price) }} CP</span>
                            @if($product->is_on_sale)
                                <span class="text-[10px] line-through text-[#80633B] block">{{ number_format($product->cash_points_price) }} CP</span>
                            @endif
                        </div>
                        <button type="button" @click='openModal(@json($product))' class="payon-btn-forest py-1.5 px-3 text-xs">Buy</button>
                    </div>
                </div>
            @endforeach
        </div>
        @if($products->hasPages())
            <div>{{ $products->links() }}</div>
        @endif
    @else
        <div class="payon-parchment-panel p-12 text-center text-xs text-[#80633B]">No products found matching your query.</div>
    @endif

    <!-- Purchase Modal -->
    <div x-show="showModal" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75">
        <div @click.away="showModal = false" class="payon-wood-frame max-w-md w-full overflow-hidden">
            <div class="payon-wood-header">
                <span>Confirm Purchase</span>
                <button type="button" @click="showModal = false" class="text-[#FFF8E8] cursor-pointer">&times;</button>
            </div>
            <form method="POST" :action="'{{ url('/cash-shop/buy') }}/' + (activeProduct?.id || 0)" class="p-5 space-y-4">
                @csrf
                <div class="p-3.5 rounded bg-[#E7D8B5] border border-[#A88950] flex items-center gap-3">
                    <img :src="activeProduct?.icon_url" class="w-9 h-9 object-contain" alt="">
                    <div>
                        <p class="font-cinzel font-bold text-sm text-[#35291F]" x-text="activeProduct?.name"></p>
                        <p class="font-mono text-xs text-[#263D2C]" x-text="formatNumber(unitPrice) + ' CP each'"></p>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#35291F] mb-1">Quantity</label>
                    <input type="number" name="quantity" x-model.number="quantity" min="1" :max="maxAllowedQuantity" class="w-full px-3 py-2 rounded text-xs">
                </div>
                <div class="p-3 rounded bg-[#E7D8B5] border border-[#A88950] flex items-center justify-between text-xs font-bold">
                    <span>Total Cost:</span>
                    <span class="font-mono text-sm text-[#263D2C]" x-text="formatNumber(totalCost) + ' CP'"></span>
                </div>
                @auth
                    <button type="submit" :disabled="remainingBalance < 0" class="payon-btn-forest w-full py-2.5">
                        <span>Confirm &amp; Purchase (<span x-text="formatNumber(totalCost) + ' CP'"></span>)</span>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="payon-btn-gold w-full py-2.5">Log in to Purchase</a>
                @endauth
            </form>
        </div>
    </div>
</div>

<script>
function cashShopModal() {
    return {
        showModal: false,
        activeProduct: null,
        quantity: 1,
        userBalance: {{ (int) $balance }},
        openModal(product) { this.activeProduct = product; this.quantity = 1; this.showModal = true; },
        get unitPrice() { return this.activeProduct ? (this.activeProduct.effective_price || this.activeProduct.cash_points_price || 0) : 0; },
        get totalCost() { return this.unitPrice * this.quantity; },
        get remainingBalance() { return this.userBalance - this.totalCost; },
        get maxAllowedQuantity() {
            if (!this.activeProduct) return 99;
            let max = 99;
            if (this.activeProduct.has_stock_limit && this.activeProduct.stock_quantity > 0) max = Math.min(max, this.activeProduct.stock_quantity);
            if (this.activeProduct.per_account_limit > 0) max = Math.min(max, this.activeProduct.per_account_limit);
            return Math.max(1, max);
        },
        formatNumber(num) { return new Intl.NumberFormat().format(num || 0); }
    };
}
</script>
@endsection
