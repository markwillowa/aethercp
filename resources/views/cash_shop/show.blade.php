@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="productPurchase({{ (int) $balance }}, {{ (int) $product->effective_price }}, {{ (int) ($product->has_stock_limit ? $product->stock_quantity : 99) }}, {{ (int) ($product->per_account_limit ?: 99) }})">

    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('cash-shop.index') }}" class="hover:text-cyan-400 transition-colors flex items-center gap-1">
            <i data-lucide="store" class="w-3.5 h-3.5"></i>
            <span>Cash Shop</span>
        </a>
        <span>/</span>
        @if($product->category)
            <a href="{{ route('cash-shop.index', ['category' => $product->category->slug]) }}" class="hover:text-cyan-400 transition-colors">
                {{ $product->category->name }}
            </a>
            <span>/</span>
        @endif
        <span class="text-white font-semibold truncate">{{ $product->name }}</span>
    </nav>

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

    <!-- Main Product Card -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl shadow-2xl backdrop-blur-xl overflow-hidden p-6 sm:p-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            
            <!-- Left: Visual Column -->
            <div class="md:col-span-5 flex flex-col items-center">
                <div class="w-full aspect-square max-w-[280px] rounded-3xl bg-slate-950 border border-slate-800 p-8 flex items-center justify-center relative shadow-2xl overflow-hidden group">
                    <div class="absolute inset-0 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-110 transition-transform"></div>
                    <img src="{{ $product->icon_url }}" alt="{{ $product->name }}" class="w-24 h-24 object-contain relative z-10 transition-transform duration-300 group-hover:scale-110" onerror="this.src='/images/item-placeholder.png'">
                    
                    @if($product->is_on_sale)
                        <div class="absolute top-4 left-4 px-2.5 py-1 rounded-xl text-xs font-black uppercase bg-rose-500 text-white shadow-lg">
                            -{{ $product->discount_percent }}% OFF
                        </div>
                    @endif
                </div>

                <!-- Item Attributes -->
                <div class="w-full max-w-[280px] mt-4 space-y-2">
                    <div class="p-3 rounded-2xl bg-slate-950/60 border border-slate-800/80 text-xs space-y-1.5">
                        <div class="flex items-center justify-between text-slate-400">
                            <span>Item ID:</span>
                            <span class="font-mono text-slate-200 font-bold">#{{ $product->item_id }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-400">
                            <span>Units Delivered:</span>
                            <span class="font-mono text-cyan-300 font-bold">{{ $product->quantity }}x</span>
                        </div>
                        @if($product->refine > 0)
                            <div class="flex items-center justify-between text-slate-400">
                                <span>Refinement:</span>
                                <span class="font-mono text-cyan-300 font-bold">+{{ $product->refine }}</span>
                            </div>
                        @endif
                        @if($product->bound > 0)
                            <div class="flex items-center justify-between text-slate-400">
                                <span>Binding:</span>
                                <span class="text-purple-300 font-semibold">Account Bound</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Details & Order Form -->
            <div class="md:col-span-7 space-y-6">
                <!-- Header -->
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">
                            {{ $product->category?->name ?? 'Shop Item' }}
                        </span>
                        @if($product->is_featured)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                Featured Item
                            </span>
                        @endif
                        @if($product->has_stock_limit)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $product->stock_quantity > 0 ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                {{ $product->stock_quantity > 0 ? "Stock: {$product->stock_quantity}" : 'Sold Out' }}
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                In Stock
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        {{ $product->name }}
                    </h1>
                </div>

                <!-- Price Panel -->
                <div class="p-4 rounded-2xl bg-slate-950/90 border border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Purchase Cost</span>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            @if($product->is_on_sale)
                                <span class="text-xs text-slate-500 line-through font-mono">
                                    {{ number_format($product->cash_points_price) }} CP
                                </span>
                            @endif
                            <span class="text-2xl font-black text-purple-300 font-mono">
                                {{ number_format($product->effective_price) }} <span class="text-xs font-mono text-purple-400/80">CP</span>
                            </span>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Your Balance</span>
                        @auth
                            <span class="text-sm font-bold text-slate-200 font-mono block mt-0.5">
                                {{ number_format($balance) }} CP
                            </span>
                        @else
                            <a href="{{ route('login') }}" class="text-xs text-cyan-400 hover:underline">Log in</a>
                        @endauth
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Product Description</h3>
                    <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-800/80 text-xs text-slate-300 leading-relaxed space-y-2">
                        @if(!empty($product->description))
                            <p>{{ $product->description }}</p>
                        @else
                            <p class="text-slate-500 italic">No detailed description provided for this item.</p>
                        @endif
                    </div>
                </div>

                <!-- Delivery Destination Notice -->
                <div class="p-3.5 rounded-2xl bg-cyan-950/30 border border-cyan-500/20 text-cyan-300/90 text-xs flex items-start gap-3">
                    <i data-lucide="inbox" class="w-4 h-4 text-cyan-400 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-bold text-cyan-200">Instant Escrow Delivery</p>
                        <p class="text-[11px] text-cyan-300/70 mt-0.5">
                            Upon purchasing, items are deposited directly into your Escrow Inbox. You can transfer them to Kafra Storage or Character Inventory whenever you choose.
                        </p>
                    </div>
                </div>

                <!-- Purchase Form -->
                <form method="POST" action="{{ route('cash-shop.buy', $product->id) }}" class="space-y-4 pt-2">
                    @csrf

                    <!-- Quantity selection -->
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800">
                        <div>
                            <span class="text-xs font-bold text-white block">Purchase Quantity</span>
                            <span class="text-[10px] text-slate-400 block">Units of this package</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="decrement()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center transition-colors">
                                -
                            </button>
                            <input type="number" name="quantity" x-model="qty" min="1" :max="maxQty" readonly
                                   class="w-12 text-center bg-transparent font-mono font-bold text-white text-sm focus:outline-none">
                            <button type="button" @click="increment()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center transition-colors">
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Total cost & Remaining balance calculation -->
                    <div class="p-4 rounded-2xl bg-slate-950/90 border border-slate-800 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-400">
                            <span>Order Total:</span>
                            <span class="font-mono font-bold text-purple-300" x-text="formatNumber(totalCost) + ' CP'"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span :class="remainingBalance < 0 ? 'text-rose-400 font-bold' : 'text-slate-400'">Balance After Order:</span>
                            <span class="font-mono font-bold" :class="remainingBalance < 0 ? 'text-rose-400' : 'text-emerald-400'" x-text="formatNumber(remainingBalance) + ' CP'"></span>
                        </div>
                    </div>

                    @auth
                        @if($product->has_stock_limit && $product->stock_quantity <= 0)
                            <button type="button" disabled class="w-full py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider bg-slate-800 text-slate-500 cursor-not-allowed">
                                Out of Stock
                            </button>
                        @else
                            <button type="submit" :disabled="remainingBalance < 0"
                                    class="w-full py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-lg shadow-cyan-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
                                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                <span>Complete Purchase (<span x-text="formatNumber(totalCost) + ' CP'"></span>)</span>
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider bg-slate-800 hover:bg-slate-700 text-white transition-all flex items-center justify-center gap-2">
                            <span>Log In to Purchase</span>
                        </a>
                    @endauth
                </form>
            </div>

        </div>
    </div>

</div>

<script>
function productPurchase(balance, unitPrice, stockMax, accountLimit) {
    return {
        qty: 1,
        balance: balance,
        unitPrice: unitPrice,
        maxQty: Math.max(1, Math.min(stockMax, accountLimit)),

        get totalCost() {
            return this.qty * this.unitPrice;
        },

        get remainingBalance() {
            return this.balance - this.totalCost;
        },

        increment() {
            if (this.qty < this.maxQty) this.qty++;
        },

        decrement() {
            if (this.qty > 1) this.qty--;
        },

        formatNumber(num) {
            return new Intl.NumberFormat().format(num || 0);
        }
    };
}
</script>
@endsection
