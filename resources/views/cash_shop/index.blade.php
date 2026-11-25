@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="cashShopModal()">

    <!-- 1. Hero & Cash Points Wallet Bar -->
    <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-2xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <!-- Title & Info -->
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-600 via-indigo-600 to-purple-600 flex items-center justify-center shrink-0 shadow-lg shadow-cyan-500/20 text-white border border-cyan-400/30">
                    <i data-lucide="store" class="w-7 h-7"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                            Web Item Mall
                        </span>
                        <span class="text-xs text-slate-600">•</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                            Instant Escrow Delivery
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-1">
                        Cash Shop
                    </h1>
                    <p class="text-xs text-slate-400 mt-1 max-w-xl leading-relaxed">
                        Spend your Cash Points on consumables, refinement supplies, rare card albums, and exclusive equipment. All purchases are held safely in your <a href="{{ route('escrow.index') }}" class="text-cyan-400 hover:underline font-semibold">Escrow Inbox</a> for in-game delivery.
                    </p>
                </div>
            </div>

            <!-- Wallet & Quick Actions -->
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <!-- Balance Card -->
                <div class="p-3.5 rounded-2xl bg-slate-950/90 border border-slate-800 shadow-inner flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 font-bold text-sm shrink-0">
                        CP
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Available Balance</span>
                        @auth
                            <span class="text-base sm:text-lg font-black text-amber-300 font-mono block leading-tight">
                                {{ number_format($balance) }} <span class="text-xs font-normal text-slate-400">Points</span>
                            </span>
                        @else
                            <a href="{{ route('login') }}" class="text-xs text-cyan-400 hover:underline font-semibold block">Log in to view</a>
                        @endauth
                    </div>
                </div>

                <!-- Action Buttons -->
                @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('donations'))
                    <a href="{{ route('donations.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-md shadow-amber-500/20 transition-all flex items-center gap-2 active:scale-95">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Get Cash Points</span>
                    </a>
                @endif

                @auth
                    <a href="{{ route('cash-shop.history') }}" class="px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-slate-950/80 hover:bg-slate-800 text-slate-300 border border-slate-800 transition-colors flex items-center gap-1.5">
                        <i data-lucide="receipt" class="w-4 h-4 text-cyan-400"></i>
                        <span>My Purchases</span>
                    </a>
                    <a href="{{ route('escrow.index') }}" class="px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-slate-950/80 hover:bg-slate-800 text-slate-300 border border-slate-800 transition-colors flex items-center gap-1.5">
                        <i data-lucide="inbox" class="w-4 h-4 text-emerald-400"></i>
                        <span>Escrow</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-400 shrink-0"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- 2. Featured Promotional Products -->
    @if($featuredProducts->count() > 0 && $selectedCategorySlug === 'all' && empty(request('q')))
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-400"></i>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">Featured Items &amp; Hot Deals</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($featuredProducts as $item)
                    <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border border-amber-500/30 hover:border-amber-500/60 transition-all shadow-xl group relative overflow-hidden flex flex-col justify-between">
                        <div class="absolute -top-12 -right-12 w-24 h-24 bg-amber-500/10 rounded-full blur-xl pointer-events-none"></div>

                        <div>
                            <!-- Header with badges -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    ★ Featured
                                </span>
                                @if($item->is_on_sale)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/40">
                                        -{{ $item->discount_percent }}% OFF
                                    </span>
                                @endif
                            </div>

                            <!-- Icon & Title -->
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-slate-950 border border-slate-800 p-1 flex items-center justify-center shrink-0 shadow-inner group-hover:scale-105 transition-transform">
                                    <img src="{{ $item->icon_url }}" alt="{{ $item->name }}" class="w-8 h-8 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('cash-shop.show', $item->slug) }}" class="text-sm font-bold text-white hover:text-cyan-400 transition-colors block truncate">
                                        {{ $item->name }}
                                    </a>
                                    <span class="text-[11px] text-slate-400 block truncate">{{ $item->category?->name ?? 'Special' }}</span>
                                </div>
                            </div>

                            @if(!empty($item->description))
                                <p class="text-xs text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                                    {{ $item->description }}
                                </p>
                            @endif
                        </div>

                        <!-- Footer Price & Buy Button -->
                        <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between gap-3">
                            <div>
                                @if($item->is_on_sale)
                                    <span class="text-[11px] text-slate-500 line-through font-mono block leading-none">
                                        {{ number_format($item->cash_points_price) }} CP
                                    </span>
                                @endif
                                <span class="text-sm font-black text-amber-300 font-mono">
                                    {{ number_format($item->effective_price) }} <span class="text-[10px] font-sans text-slate-400">CP</span>
                                </span>
                            </div>

                            <button type="button"
                                    @click="openModal({{ json_encode($item) }})"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 transition-all flex items-center gap-1 shadow-sm active:scale-95 cursor-pointer">
                                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                <span>Buy</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 3. Category Tabs & Filters Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Category Filter Pills -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('cash-shop.index', ['category' => 'all', 'sort' => request('sort'), 'q' => request('q')]) }}"
               class="h-10 px-3.5 rounded-xl text-xs font-semibold border transition-all inline-flex items-center gap-2 {{ $selectedCategorySlug === 'all' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                <i data-lucide="grid" class="w-3.5 h-3.5"></i>
                <span>All Categories</span>
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('cash-shop.index', ['category' => $cat->slug, 'sort' => request('sort'), 'q' => request('q')]) }}"
                   class="h-10 px-3.5 rounded-xl text-xs font-semibold border transition-all inline-flex items-center gap-2 {{ $selectedCategorySlug === $cat->slug ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                    @if(!empty($cat->icon))
                        <i data-lucide="{{ $cat->icon }}" class="w-3.5 h-3.5 text-cyan-400"></i>
                    @endif
                    <span>{{ $cat->name }}</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $selectedCategorySlug === $cat->slug ? 'bg-cyan-400/30 text-cyan-200' : 'bg-slate-800 text-slate-400' }}">
                        {{ $cat->published_products_count }}
                    </span>
                </a>
            @endforeach
        </div>

        <!-- Search & Sorting Controls -->
        <form method="GET" action="{{ route('cash-shop.index') }}" class="flex items-center gap-2 shrink-0">
            <input type="hidden" name="category" value="{{ $selectedCategorySlug }}">
            
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..."
                       class="h-10 pl-9 pr-3 text-xs bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition-colors w-44 sm:w-56 leading-none">
            </div>

            <select name="sort" onchange="this.form.submit()" class="h-10 px-3 text-xs bg-slate-900 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:border-cyan-500 transition-colors cursor-pointer leading-none">
                <option value="featured" {{ request('sort', 'featured') === 'featured' ? 'selected' : '' }}>Featured</option>
                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
            </select>
        </form>
    </div>

    <!-- 4. Products Catalog Grid -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl shadow-2xl backdrop-blur-xl">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between rounded-t-3xl bg-slate-950/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-400">
                    <i data-lucide="package" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-white tracking-wide">
                        {{ $selectedCategory ? $selectedCategory->name : 'All Catalog Items' }}
                    </h2>
                    <p class="text-[11px] text-slate-400">
                        {{ $selectedCategory ? ($selectedCategory->description ?: 'Browse category inventory') : 'Available premium products, services, and packages' }}
                    </p>
                </div>
            </div>
            <span class="text-xs font-mono px-3 py-1 rounded-full bg-slate-950 border border-slate-800 text-slate-300">
                <strong class="text-cyan-400">{{ $products->total() }}</strong> Available
            </span>
        </div>

        @if($products->count() > 0)
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($products as $product)
                    <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 hover:border-slate-700 transition-all shadow-lg flex flex-col justify-between group">
                        <div>
                            <!-- Header Badges -->
                            <div class="flex items-center justify-between gap-1 mb-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800/80 text-slate-400 border border-slate-700/60 truncate max-w-[130px]">
                                    {{ $product->category?->name ?? 'Shop Item' }}
                                </span>
                                @if($product->is_on_sale)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40">
                                        -{{ $product->discount_percent }}% OFF
                                    </span>
                                @elseif($product->is_featured)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        Featured
                                    </span>
                                @endif
                            </div>

                            <!-- Icon & Name -->
                            <div class="flex items-start gap-3.5 mb-2">
                                <div class="w-13 h-13 rounded-2xl bg-slate-900 border border-slate-800 p-1 flex items-center justify-center shrink-0 shadow-inner group-hover:scale-105 transition-transform">
                                    <img src="{{ $product->icon_url }}" alt="{{ $product->name }}" class="w-9 h-9 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('cash-shop.show', $product->slug) }}" class="text-sm font-bold text-white hover:text-cyan-400 transition-colors block leading-snug">
                                        {{ $product->name }}
                                    </a>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        @if($product->refine > 0)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 font-mono">
                                                +{{ $product->refine }} Refine
                                            </span>
                                        @endif
                                        @if($product->quantity > 1)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-mono">
                                                Qty: {{ $product->quantity }}x
                                            </span>
                                        @endif
                                        @if($product->bound > 0)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                                Bound
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if(!empty($product->description))
                                <p class="text-xs text-slate-400 line-clamp-2 mt-2 leading-relaxed">
                                    {{ $product->description }}
                                </p>
                            @endif

                            <!-- Stock & Limit Metadata -->
                            <div class="mt-3 pt-2.5 border-t border-slate-900 flex items-center justify-between text-[11px] text-slate-500">
                                @if($product->has_stock_limit)
                                    <span class="{{ $product->stock_quantity > 0 ? 'text-amber-400 font-semibold' : 'text-rose-400 font-bold' }}">
                                        {{ $product->stock_quantity > 0 ? "Stock: {$product->stock_quantity} left" : 'Out of Stock' }}
                                    </span>
                                @else
                                    <span class="text-emerald-400/80">In Stock</span>
                                @endif

                                @if($product->per_account_limit)
                                    <span>Limit: {{ $product->per_account_limit }}/acc</span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between gap-2">
                            <div>
                                @if($product->is_on_sale)
                                    <span class="text-[11px] text-slate-500 line-through font-mono block leading-none">
                                        {{ number_format($product->cash_points_price) }} CP
                                    </span>
                                @endif
                                <span class="text-base font-black text-amber-300 font-mono">
                                    {{ number_format($product->effective_price) }} <span class="text-xs font-sans text-slate-400">CP</span>
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('cash-shop.show', $product->slug) }}" class="h-9 w-9 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 transition-colors inline-flex items-center justify-center shrink-0" title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <button type="button"
                                        @click="openModal({{ json_encode($product) }})"
                                        class="h-9 px-3.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-md shadow-cyan-500/20 transition-all inline-flex items-center justify-center gap-1.5 active:scale-95 cursor-pointer shrink-0">
                                    <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                                    <span>Buy Now</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($products->hasPages())
                <div class="p-5 border-t border-slate-800">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center mx-auto text-slate-600 mb-3">
                    <i data-lucide="inbox" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-white">No Products Found</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                    There are no products listed in this category or matching your search criteria.
                </p>
                <div class="mt-4">
                    <a href="{{ route('cash-shop.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 transition-colors">
                        Reset Filters
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- 5. Alpine.js Purchase Confirmation Modal -->
    <div x-show="showModal" @keydown.escape.window="showModal = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        
        <div @click.away="showModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-md rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl overflow-hidden p-6 space-y-5 relative">
            
            <!-- Close button -->
            <button type="button" @click="showModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <!-- Modal Header -->
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 shrink-0">
                    <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-white">Confirm Purchase</h3>
                    <p class="text-xs text-slate-400">Review your order before deduction</p>
                </div>
            </div>

            <!-- Product Summary Card -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-slate-900 border border-slate-800 p-1 flex items-center justify-center shrink-0">
                    <img :src="activeProduct?.icon_url || '/images/item-placeholder.png'" :alt="activeProduct?.name" class="w-8 h-8 object-contain">
                </div>
                <div class="min-w-0">
                    <h4 class="text-sm font-bold text-white truncate" x-text="activeProduct?.name"></h4>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <span x-text="activeProduct?.category?.name || 'Shop Item'"></span>
                        <template x-if="activeProduct?.quantity > 1">
                            <span class="text-cyan-400" x-text="' • ' + activeProduct?.quantity + 'x per purchase'"></span>
                        </template>
                    </p>
                    <p class="text-xs font-mono font-bold text-amber-300 mt-1" x-text="formatNumber(unitPrice) + ' CP each'"></p>
                </div>
            </div>

            <!-- Quantity Selector (if purchase limits / stock allow) -->
            <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                <div>
                    <span class="text-xs font-bold text-white block">Quantity</span>
                    <span class="text-[10px] text-slate-400 block">How many packages to buy</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="if(quantity > 1) quantity--" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center transition-colors">
                        -
                    </button>
                    <span class="w-10 text-center font-mono font-bold text-sm text-white" x-text="quantity"></span>
                    <button type="button" @click="if(quantity < maxAllowedQuantity) quantity++" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center transition-colors">
                        +
                    </button>
                </div>
            </div>

            <!-- Balance Calculation Preview -->
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-400">
                    <span>Your Current Balance:</span>
                    <span class="font-mono font-semibold text-slate-200" x-text="formatNumber(userBalance) + ' CP'"></span>
                </div>
                <div class="flex items-center justify-between text-slate-400">
                    <span>Order Total:</span>
                    <span class="font-mono font-bold text-amber-300" x-text="'-' + formatNumber(totalCost) + ' CP'"></span>
                </div>
                <div class="border-t border-slate-800 pt-2 flex items-center justify-between font-bold">
                    <span :class="remainingBalance < 0 ? 'text-rose-400' : 'text-slate-300'">Balance After Purchase:</span>
                    <span class="font-mono text-sm" :class="remainingBalance < 0 ? 'text-rose-400' : 'text-emerald-400'" x-text="formatNumber(remainingBalance) + ' CP'"></span>
                </div>
            </div>

            <!-- Insufficient balance warning -->
            <div x-show="remainingBalance < 0" class="p-3.5 rounded-xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400 shrink-0"></i>
                    <span>Insufficient Cash Points.</span>
                </div>
                @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('donations'))
                    <a href="{{ route('donations.index') }}" class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-500 text-slate-950 hover:bg-amber-400 shrink-0">
                        Top up
                    </a>
                @endif
            </div>

            <!-- Escrow Notice -->
            <div class="p-3 rounded-xl bg-cyan-950/30 border border-cyan-500/20 text-cyan-300/80 text-[11px] flex items-start gap-2">
                <i data-lucide="inbox" class="w-4 h-4 text-cyan-400 shrink-0 mt-0.5"></i>
                <span>Purchased items will be delivered immediately to your <strong>Escrow Inbox</strong>, where you can transfer them to Kafra Storage or Character at any time.</span>
            </div>

            <!-- Purchase Form -->
            <form :action="'/cash-shop/buy/' + (activeProduct ? activeProduct.id : 0)" method="POST" class="pt-1">
                @csrf
                <input type="hidden" name="quantity" :value="quantity">
                
                @auth
                    <button type="submit"
                            :disabled="remainingBalance < 0 || (activeProduct?.has_stock_limit && activeProduct?.stock_quantity < quantity)"
                            class="w-full py-3 rounded-2xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-lg shadow-cyan-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Confirm &amp; Purchase (<span x-text="formatNumber(totalCost) + ' CP'"></span>)</span>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="w-full py-3 rounded-2xl text-xs font-bold uppercase tracking-wider bg-slate-800 hover:bg-slate-700 text-white transition-all flex items-center justify-center gap-2">
                        <span>Log in to Purchase</span>
                    </a>
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
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },

        get unitPrice() {
            if (!this.activeProduct) return 0;
            return this.activeProduct.effective_price || this.activeProduct.cash_points_price || 0;
        },

        get totalCost() {
            return this.unitPrice * this.quantity;
        },

        get remainingBalance() {
            return this.userBalance - this.totalCost;
        },

        get maxAllowedQuantity() {
            if (!this.activeProduct) return 99;
            let max = 99;
            if (this.activeProduct.has_stock_limit && this.activeProduct.stock_quantity > 0) {
                max = Math.min(max, this.activeProduct.stock_quantity);
            }
            if (this.activeProduct.per_account_limit > 0) {
                max = Math.min(max, this.activeProduct.per_account_limit);
            }
            return Math.max(1, max);
        },

        formatNumber(num) {
            return new Intl.NumberFormat().format(num || 0);
        }
    };
}
</script>
@endsection
