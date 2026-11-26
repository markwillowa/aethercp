@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8" x-data="cashShopModal()">
    <!-- News & Media Style Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Cash Shop &amp; Item Mall
            </h1>
            <p class="text-xs text-[#788394] mt-1">
                Spend Cash Points on consumables, refinement supplies, and gear delivered to your Escrow Inbox.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="h-9 px-3.5 rounded-xl bg-[#151B26] border border-[#C6A76A]/30 font-mono text-xs text-[#D8BE8A] inline-flex items-center">
                Balance: <strong class="ml-1">{{ auth()->check() ? number_format($balance) . ' CP' : 'Guest' }}</strong>
            </span>
            @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('donations'))
                <a href="{{ route('donations.index') }}" class="h-9 px-4 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider inline-flex items-center">Top Up</a>
            @endif
            @auth
                <a href="{{ route('cash-shop.history') }}" class="h-9 px-3.5 rounded-xl bg-[#151B26] border border-white/[0.08] text-[#AAB3C1] hover:text-white text-xs font-bold inline-flex items-center">Purchases</a>
                <a href="{{ route('escrow.index') }}" class="h-9 px-3.5 rounded-xl bg-[#151B26] border border-white/[0.08] text-[#D8BE8A] text-xs font-bold inline-flex items-center">Escrow</a>
            @endauth
        </div>
    </div>

    <!-- Search & Sort Bar -->
    <form method="GET" action="{{ route('cash-shop.index') }}" class="prestige-card p-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <input type="hidden" name="category" value="{{ $selectedCategorySlug }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search cash shop products by name or item ID..."
               class="flex-1 h-10 px-4 rounded-xl bg-[#101725] border border-white/10 text-white text-xs focus:border-[#C6A76A] focus:outline-none">
        <select name="sort" onchange="this.form.submit()" class="h-10 px-3 rounded-xl bg-[#101725] border border-white/10 text-xs text-[#D9DEE7]">
            <option value="featured" {{ request('sort', 'featured') === 'featured' ? 'selected' : '' }}>Featured</option>
            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low-High</option>
            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High-Low</option>
            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
        </select>
        <button type="submit" class="h-10 px-5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider cursor-pointer shrink-0">
            Search
        </button>
    </form>

    <!-- Category Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <a href="{{ route('cash-shop.index', ['category' => 'all', 'sort' => request('sort'), 'q' => request('q')]) }}"
           class="px-4 py-2 rounded-xl text-xs font-bold border shrink-0 {{ $selectedCategorySlug === 'all' ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border-[#C6A76A]/50' : 'bg-[#151B26] text-[#AAB3C1] border-white/[0.08]' }}">
            All Categories
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('cash-shop.index', ['category' => $cat->slug, 'sort' => request('sort'), 'q' => request('q')]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold border shrink-0 {{ $selectedCategorySlug === $cat->slug ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border-[#C6A76A]/50' : 'bg-[#151B26] text-[#AAB3C1] border-white/[0.08]' }}">
                {{ $cat->name }} ({{ $cat->published_products_count }})
            </a>
        @endforeach
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($products as $product)
                <div class="prestige-card p-5 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-[10px] font-cinzel uppercase text-[#C6A76A] truncate">{{ $product->category?->name ?? 'Shop Item' }}</span>
                            @if($product->is_on_sale)
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40">-{{ $product->discount_percent }}% OFF</span>
                            @endif
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-xl bg-[#101725] border border-white/10 flex items-center justify-center shrink-0">
                                <img src="{{ $product->icon_url }}" alt="{{ $product->name }}" class="w-8 h-8 object-contain" onerror="this.src='/images/item-placeholder.png'">
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('cash-shop.show', $product->slug) }}" class="font-cinzel text-sm font-bold text-white hover:text-[#D8BE8A] block leading-snug">{{ $product->name }}</a>
                                @if($product->refine > 0)
                                    <span class="text-[10px] font-bold text-[#D8BE8A] font-mono">+{{ $product->refine }} Refine</span>
                                @endif
                            </div>
                        </div>
                        @if(!empty($product->description))
                            <p class="text-xs text-[#788394] line-clamp-2">{{ $product->description }}</p>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-white/[0.08] flex items-center justify-between gap-2">
                        <div>
                            @if($product->is_on_sale)
                                <span class="text-[10px] text-[#788394] line-through font-mono block">{{ number_format($product->cash_points_price) }} CP</span>
                            @endif
                            <span class="font-mono font-bold text-sm text-[#D8BE8A]">{{ number_format($product->effective_price) }} CP</span>
                        </div>
                        <button type="button" @click="openModal({{ json_encode($product) }})" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] text-xs font-bold cursor-pointer">
                            Buy
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        <div>{{ $products->links() }}</div>
    @else
        <div class="prestige-card p-12 text-center text-xs text-[#788394]">No products found matching your criteria.</div>
    @endif

    <!-- Purchase Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="showModal = false">
        <div @click.away="showModal = false" class="w-full max-w-md bg-[#151B26] border border-[#C6A76A]/40 rounded-2xl shadow-2xl p-6 space-y-4">
            <div class="flex items-start justify-between border-b border-white/[0.08] pb-3">
                <h3 class="font-cinzel text-base font-bold text-white" x-text="activeProduct ? activeProduct.name : 'Purchase'"></h3>
                <button type="button" @click="showModal = false" class="text-[#788394] hover:text-white font-bold cursor-pointer">✕</button>
            </div>
            <div class="flex items-center justify-between text-xs text-[#AAB3C1]">
                <span>Quantity:</span>
                <div class="flex items-center gap-2">
                    <button type="button" @click="if(quantity > 1) quantity--" class="w-7 h-7 rounded bg-[#101725] border border-white/10 font-bold cursor-pointer">-</button>
                    <span class="font-mono font-bold w-8 text-center text-white" x-text="quantity"></span>
                    <button type="button" @click="if(quantity < maxAllowedQuantity) quantity++" class="w-7 h-7 rounded bg-[#101725] border border-white/10 font-bold cursor-pointer">+</button>
                </div>
            </div>
            <div class="p-3 rounded-xl bg-[#101725] border border-white/[0.08] text-xs space-y-1">
                <div class="flex justify-between text-[#AAB3C1]"><span>Total Cost</span><strong class="text-[#D8BE8A]" x-text="formatNumber(totalCost) + ' CP'"></strong></div>
                <div class="flex justify-between text-[#AAB3C1]"><span>Remaining Balance</span><strong :class="remainingBalance < 0 ? 'text-rose-400' : 'text-emerald-400'" x-text="formatNumber(remainingBalance) + ' CP'"></strong></div>
            </div>
            <form :action="'/cash-shop/buy/' + (activeProduct ? activeProduct.id : 0)" method="POST">
                @csrf
                <input type="hidden" name="quantity" :value="quantity">
                @auth
                    <button type="submit" :disabled="remainingBalance < 0" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] disabled:opacity-50 text-[#101725] font-cinzel font-bold text-xs uppercase cursor-pointer">
                        Confirm &amp; Deliver to Escrow
                    </button>
                @else
                    <a href="{{ route('login') }}" class="block text-center w-full py-2.5 rounded-xl bg-[#1B2432] text-white font-cinzel font-bold text-xs">Log in to Purchase</a>
                @endauth
            </form>
        </div>
    </div>
</div>

<script>
function cashShopModal() {
    return {
        showModal: false, activeProduct: null, quantity: 1, userBalance: {{ (int) $balance }},
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
