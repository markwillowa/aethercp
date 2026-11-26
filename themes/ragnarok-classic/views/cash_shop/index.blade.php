@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="cashShopModal()">
    <!-- Forum-Style Classic Header Panel -->
    <div class="ro-panel overflow-hidden">
        <div class="ro-panel-header justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="store" class="w-4 h-4"></i>
                <span>Web Item Mall &amp; Cash Shop</span>
            </div>
            <span class="text-[11px] font-mono text-[#FFDA89]">
                Balance: {{ auth()->check() ? number_format($balance) . ' CP' : 'Guest' }}
            </span>
        </div>

        <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-[#FCF7ED]">
            <div class="space-y-1 max-w-2xl">
                <h1 class="text-2xl font-serif font-bold text-[#2D1D13] tracking-tight">
                    Kafra Cash Shop
                </h1>
                <p class="text-xs text-[#5C4632]">
                    Spend Cash Points on consumables, refinement supplies, and gear delivered safely to your <a href="{{ route('escrow.index') }}" class="text-[#315B88] font-bold hover:underline">Escrow Inbox</a>.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('donations'))
                    <a href="{{ route('donations.index') }}" class="ro-btn-gold text-xs whitespace-nowrap">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                        <span>Get Cash Points</span>
                    </a>
                @endif
                @auth
                    <a href="{{ route('cash-shop.history') }}" class="ro-btn-primary text-xs whitespace-nowrap">
                        <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                        <span>My Purchases</span>
                    </a>
                    <a href="{{ route('escrow.index') }}" class="ro-btn-wood text-xs whitespace-nowrap py-1.5 px-3">
                        <i data-lucide="inbox" class="w-3.5 h-3.5 text-[#FFDA89]"></i>
                        <span>Escrow</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Search, Sort & Category Pills -->
    <div class="ro-panel p-4 bg-[#FCF7ED] space-y-3">
        <form method="GET" action="{{ route('cash-shop.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <input type="hidden" name="category" value="{{ $selectedCategorySlug }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search cash shop products by name or item ID..." class="flex-1">
            <select name="sort" onchange="this.form.submit()">
                <option value="featured" {{ request('sort', 'featured') === 'featured' ? 'selected' : '' }}>Featured First</option>
                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest Items</option>
            </select>
            <button type="submit" class="ro-btn-primary text-xs shrink-0">
                <i data-lucide="search" class="w-3.5 h-3.5"></i>
                <span>Search</span>
            </button>
            @if(request('q') || request('sort') || ($selectedCategorySlug && $selectedCategorySlug !== 'all'))
                <a href="{{ route('cash-shop.index') }}" class="ro-btn-gold text-xs shrink-0">Reset</a>
            @endif
        </form>

        <div class="pt-3 border-t border-[#DECBB1] flex items-center gap-2 overflow-x-auto pb-1">
            <a href="{{ route('cash-shop.index', ['category' => 'all', 'sort' => request('sort'), 'q' => request('q')]) }}"
               class="px-3 py-1.5 rounded text-xs font-bold border shrink-0 {{ $selectedCategorySlug === 'all' ? 'bg-[#315B88] text-white border-[#1D3553]' : 'bg-[#F5EBD5] text-[#3A2A1C] border-[#C8A66A] hover:bg-[#EBDDBF]' }}">
                All Categories
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('cash-shop.index', ['category' => $cat->slug, 'sort' => request('sort'), 'q' => request('q')]) }}"
                   class="px-3 py-1.5 rounded text-xs font-bold border shrink-0 {{ $selectedCategorySlug === $cat->slug ? 'bg-[#315B88] text-white border-[#1D3553]' : 'bg-[#F5EBD5] text-[#3A2A1C] border-[#C8A66A] hover:bg-[#EBDDBF]' }}">
                    {{ $cat->name }} ({{ $cat->published_products_count }})
                </a>
            @endforeach
        </div>
    </div>

    <!-- Catalog Grid -->
    <div class="ro-panel overflow-hidden">
        <div class="ro-panel-header justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="package" class="w-4 h-4"></i>
                <span>{{ $selectedCategory ? $selectedCategory->name : 'All Catalog Items' }}</span>
            </div>
            <span class="text-[11px] font-mono text-[#FFDA89]">{{ $products->total() }} Available</span>
        </div>

        @if($products->count() > 0)
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-[#F5EBD5]">
                @foreach($products as $product)
                    <div class="ro-inner-box flex flex-col justify-between space-y-3">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-[10px] font-bold uppercase text-[#8C5E1E] truncate">{{ $product->category?->name ?? 'Shop Item' }}</span>
                                @if($product->is_on_sale)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#FEE2E2] text-[#B91C1C] border border-[#FCA5A5]">-{{ $product->discount_percent }}% OFF</span>
                                @endif
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded bg-[#F5EBD5] border border-[#C8A66A] flex items-center justify-center shrink-0">
                                    <img src="{{ $product->icon_url }}" alt="{{ $product->name }}" class="w-8 h-8 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('cash-shop.show', $product->slug) }}" class="font-serif text-sm font-bold text-[#2D1D13] hover:text-[#315B88] block leading-snug">{{ $product->name }}</a>
                                    @if($product->refine > 0)
                                        <span class="text-[10px] font-bold text-[#315B88] font-mono">+{{ $product->refine }} Refine</span>
                                    @endif
                                </div>
                            </div>

                            @if(!empty($product->description))
                                <p class="text-xs text-[#5C4632] line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                            @endif
                        </div>

                        <div class="pt-3 border-t border-[#DECBB1] flex items-center justify-between gap-2">
                            <div>
                                <span class="font-mono text-sm font-bold text-[#315B88]">{{ number_format($product->effective_price) }} CP</span>
                                @if($product->is_on_sale)
                                    <span class="text-[10px] line-through text-[#77644E] block">{{ number_format($product->cash_points_price) }} CP</span>
                                @endif
                            </div>
                            <button type="button" @click='openModal(@json($product))' class="ro-btn-primary text-xs cursor-pointer">
                                Buy
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($products->hasPages())
                <div class="p-4 border-t border-[#DECBB1] bg-[#FCF7ED]">{{ $products->links() }}</div>
            @endif
        @else
            <div class="p-10 text-center text-xs text-[#77644E] bg-[#FCF7ED]">No products found matching your criteria.</div>
        @endif
    </div>

    <!-- Purchase Modal -->
    <div x-show="showModal" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75">
        <div @click.away="showModal = false" class="ro-panel max-w-md w-full overflow-hidden">
            <div class="ro-panel-header justify-between">
                <span>Confirm Cash Shop Purchase</span>
                <button type="button" @click="showModal = false" class="text-[#FFDA89] cursor-pointer">&times;</button>
            </div>
            <form method="POST" :action="'{{ url('/cash-shop/buy') }}/' + (activeProduct?.id || 0)" class="p-5 space-y-4 bg-[#FCF7ED]">
                @csrf
                <div class="ro-inner-box flex items-center gap-3">
                    <img :src="activeProduct?.icon_url" class="w-9 h-9 object-contain" alt="">
                    <div>
                        <p class="font-serif font-bold text-[#2D1D13] text-sm" x-text="activeProduct?.name"></p>
                        <p class="font-mono text-xs text-[#315B88]" x-text="formatNumber(unitPrice) + ' CP each'"></p>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#3A2A1C] mb-1">Quantity</label>
                    <input type="number" name="quantity" x-model.number="quantity" min="1" :max="maxAllowedQuantity" class="w-full">
                </div>
                <div class="ro-inner-box flex items-center justify-between text-xs font-bold text-[#3A2A1C]">
                    <span>Total Cost:</span>
                    <span class="font-mono text-sm text-[#315B88]" x-text="formatNumber(totalCost) + ' CP'"></span>
                </div>
                @auth
                    <button type="submit" :disabled="remainingBalance < 0" class="ro-btn-primary w-full justify-center py-2.5 text-xs cursor-pointer">
                        <span>Confirm &amp; Purchase (<span x-text="formatNumber(totalCost) + ' CP'"></span>)</span>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="ro-btn-gold w-full justify-center py-2.5 text-xs">Log in to Purchase</a>
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
        openModal(product) {
            this.activeProduct = product;
            this.quantity = 1;
            this.showModal = true;
        },
        get unitPrice() {
            if (!this.activeProduct) return 0;
            return this.activeProduct.effective_price || this.activeProduct.cash_points_price || 0;
        },
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
