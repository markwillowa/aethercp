@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8" x-data="cashShopModal()">

    <!-- Wood Board Header (Matches Donate Layout) -->
    <div class="chronicles-wood-board flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Treasury Item Mall
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Exchange Cash Points for consumables, refinement supplies, and rare equipment delivered to your Escrow Inbox
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <div class="px-3.5 py-2 rounded-xl bg-[#24160A] border border-[#8B5A2B] text-xs flex items-center gap-2">
                <i data-lucide="gem" class="w-4 h-4 text-[#FFD700]"></i>
                <span class="text-[#EAD2B0]">Balance:</span>
                @auth
                    <strong class="font-mono text-[#FFD700]">{{ number_format($balance) }} CP</strong>
                @else
                    <a href="{{ route('login') }}" class="text-[#FFE4A0] underline font-bold">Log in</a>
                @endauth
            </div>
            @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('donations'))
                <a href="{{ route('donations.index') }}" class="chronicles-btn-red text-xs py-2 px-4 w-auto">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    <span>Get Cash Points</span>
                </a>
            @endif
            @auth
                <a href="{{ route('cash-shop.history') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
                    <i data-lucide="receipt" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                    <span>My Purchases</span>
                </a>
                <a href="{{ route('escrow.index') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
                    <i data-lucide="inbox" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                    <span>Escrow</span>
                </a>
            @endauth
        </div>
    </div>

    <!-- Search & Category Filter Card -->
    <div class="chronicles-panel-ivy space-y-4">
        <form method="GET" action="{{ route('cash-shop.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <input type="hidden" name="category" value="{{ $selectedCategorySlug }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search cash shop products by name or item ID..."
                   class="flex-1 px-4 py-2 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] text-xs text-[#4A2200] focus:border-[#9E5600] focus:outline-none">
            <select name="sort" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] text-xs font-bold text-[#4A2200]">
                <option value="featured" {{ request('sort', 'featured') === 'featured' ? 'selected' : '' }}>Featured First</option>
                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest Items</option>
            </select>
            <button type="submit" class="px-5 py-2 rounded-xl bg-[#4A2200] hover:bg-[#5E2C00] text-[#FFE4A0] border border-[#8B5A2B] text-xs font-cinzel font-bold cursor-pointer">
                Search
            </button>
        </form>

        <div class="pt-3 border-t border-[#DECBB1] flex items-center gap-2 overflow-x-auto pb-1">
            <a href="{{ route('cash-shop.index', ['category' => 'all', 'sort' => request('sort'), 'q' => request('q')]) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border shrink-0 {{ $selectedCategorySlug === 'all' ? 'bg-[#4A2200] text-[#FFE4A0] border-[#D4AF37]' : 'bg-[#FAF4EA] text-[#5A3D28] border-[#DECBB1] hover:bg-[#EFE4D0]' }}">
                All Categories
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('cash-shop.index', ['category' => $cat->slug, 'sort' => request('sort'), 'q' => request('q')]) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold border shrink-0 {{ $selectedCategorySlug === $cat->slug ? 'bg-[#4A2200] text-[#FFE4A0] border-[#D4AF37]' : 'bg-[#FAF4EA] text-[#5A3D28] border-[#DECBB1] hover:bg-[#EFE4D0]' }}">
                    {{ $cat->name }} ({{ $cat->published_products_count }})
                </a>
            @endforeach
        </div>
    </div>

    <!-- Featured Deals -->
    @if($featuredProducts->count() > 0 && $selectedCategorySlug === 'all' && empty(request('q')))
        <div class="space-y-3">
            <h2 class="font-cinzel text-lg font-bold text-[#FFE4A0] uppercase tracking-wide">★ Featured Treasury Deals</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($featuredProducts as $item)
                    <div class="chronicles-panel-ivy flex flex-col justify-between space-y-3 border-2 border-[#D4AF37]">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-cinzel text-[10px] font-black uppercase text-[#9E5600]">★ Featured</span>
                                @if($item->is_on_sale)
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-[#D4441D] text-white">-{{ $item->discount_percent }}% OFF</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-white border border-[#DECBB1] flex items-center justify-center shrink-0">
                                    <img src="{{ $item->icon_url }}" alt="{{ $item->name }}" class="w-8 h-8 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('cash-shop.show', $item->slug) }}" class="font-cinzel text-sm font-bold text-[#4A2200] hover:text-[#D4441D] block truncate">{{ $item->name }}</a>
                                    <span class="text-[11px] text-[#795E43] block truncate">{{ $item->category?->name ?? 'Special' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-[#DECBB1] flex items-center justify-between">
                            <span class="font-mono font-bold text-base text-[#D4441D]">{{ number_format($item->effective_price) }} CP</span>
                            <button type="button" @click="openModal({{ json_encode($item) }})" class="chronicles-btn-red text-xs py-1.5 px-3.5 w-auto cursor-pointer">Buy</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Catalog Grid -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-cinzel text-lg font-bold text-[#FFE4A0] uppercase tracking-wide">
                {{ $selectedCategory ? $selectedCategory->name : 'All Catalog Items' }}
            </h2>
            <span class="text-xs font-mono text-[#DECBB1]">{{ $products->total() }} Available</span>
        </div>

        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($products as $product)
                    <div class="chronicles-panel-ivy flex flex-col justify-between space-y-4">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-[10px] font-cinzel font-bold uppercase text-[#9E5600] truncate">{{ $product->category?->name ?? 'Shop Item' }}</span>
                                @if($product->is_on_sale)
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-[#D4441D] text-white">-{{ $product->discount_percent }}% OFF</span>
                                @endif
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 rounded-xl bg-white border border-[#DECBB1] flex items-center justify-center shrink-0">
                                    <img src="{{ $product->icon_url }}" alt="{{ $product->name }}" class="w-8 h-8 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('cash-shop.show', $product->slug) }}" class="font-cinzel text-sm font-bold text-[#4A2200] hover:text-[#D4441D] block leading-snug">{{ $product->name }}</a>
                                    @if($product->refine > 0)
                                        <span class="text-[10px] font-bold text-[#D4441D] font-mono">+{{ $product->refine }} Refine</span>
                                    @endif
                                </div>
                            </div>
                            @if(!empty($product->description))
                                <p class="text-xs text-[#6B5541] line-clamp-2">{{ $product->description }}</p>
                            @endif
                        </div>

                        <div class="pt-3 border-t border-[#DECBB1] flex items-center justify-between gap-2">
                            <div>
                                @if($product->is_on_sale)
                                    <span class="text-[10px] text-[#8C7A68] line-through font-mono block">{{ number_format($product->cash_points_price) }} CP</span>
                                @endif
                                <span class="font-mono font-bold text-base text-[#D4441D]">{{ number_format($product->effective_price) }} CP</span>
                            </div>
                            <button type="button" @click="openModal({{ json_encode($product) }})" class="px-3.5 py-1.5 rounded-xl bg-[#4A2200] hover:bg-[#5E2C00] text-[#FFE4A0] border border-[#8B5A2B] text-xs font-bold cursor-pointer">
                                Purchase
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div>{{ $products->links() }}</div>
        @else
            <div class="chronicles-panel-ivy text-center py-12 text-xs text-[#8C7A68]">No products found in this category.</div>
        @endif
    </div>

    <!-- Chronicles Purchase Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm" @keydown.escape.window="showModal = false">
        <div @click.away="showModal = false" class="w-full max-w-md bg-[#FAF4EA] border-2 border-[#8B5A2B] rounded-2xl shadow-2xl p-6 space-y-4">
            <div class="flex items-start justify-between border-b border-[#DECBB1] pb-3">
                <h3 class="font-cinzel text-base font-bold text-[#4A2200]" x-text="activeProduct ? activeProduct.name : 'Purchase'"></h3>
                <button type="button" @click="showModal = false" class="text-[#795E43] hover:text-[#4A2200] font-bold cursor-pointer">✕</button>
            </div>
            <div class="flex items-center justify-between text-xs text-[#5A3D28]">
                <span>Quantity:</span>
                <div class="flex items-center gap-2">
                    <button type="button" @click="if(quantity > 1) quantity--" class="w-7 h-7 rounded bg-[#EFE4D0] border border-[#DECBB1] font-bold cursor-pointer">-</button>
                    <span class="font-mono font-bold w-8 text-center" x-text="quantity"></span>
                    <button type="button" @click="if(quantity < maxAllowedQuantity) quantity++" class="w-7 h-7 rounded bg-[#EFE4D0] border border-[#DECBB1] font-bold cursor-pointer">+</button>
                </div>
            </div>
            <div class="p-3 rounded-xl bg-[#EFE4D0] border border-[#DECBB1] text-xs space-y-1 text-[#4A2200]">
                <div class="flex justify-between"><span>Total Cost</span><strong class="text-[#D4441D]" x-text="formatNumber(totalCost) + ' CP'"></strong></div>
                <div class="flex justify-between"><span>Remaining Balance</span><strong :class="remainingBalance < 0 ? 'text-[#A82508]' : 'text-[#1E4620]'" x-text="formatNumber(remainingBalance) + ' CP'"></strong></div>
            </div>
            <form :action="'/cash-shop/buy/' + (activeProduct ? activeProduct.id : 0)" method="POST">
                @csrf
                <input type="hidden" name="quantity" :value="quantity">
                @auth
                    <button type="submit" :disabled="remainingBalance < 0" class="w-full py-2.5 rounded-xl bg-[#4A2200] hover:bg-[#5E2C00] disabled:opacity-50 text-[#FFE4A0] font-cinzel font-bold text-xs cursor-pointer">
                        Confirm &amp; Deliver to Escrow
                    </button>
                @else
                    <a href="{{ route('login') }}" class="block text-center w-full py-2.5 rounded-xl bg-[#4A2200] text-[#FFE4A0] font-cinzel font-bold text-xs">Log in to Purchase</a>
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
