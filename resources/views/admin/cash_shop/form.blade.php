@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Product: ' . $product->name : 'Create New Cash Shop Product')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    itemId: '{{ old('item_id', $product->item_id ?? '') }}',
    itemName: '{{ old('name', $product->name ?? '') }}',
    itemSearchQuery: '',
    searchResults: [],
    isSearching: false,
    selectedItemPreview: null,
    unlimitedStock: {{ old('unlimited_stock', $product->unlimited_stock ?? true) ? 'true' : 'false' }},
    isOnSale: {{ old('promotional_price', $product->promotional_price) ? 'true' : 'false' }},

    async searchItems() {
        if (this.itemSearchQuery.trim().length < 2) {
            this.searchResults = [];
            return;
        }
        this.isSearching = true;
        try {
            const res = await fetch('{{ route('admin.cash-shop.items.search') }}?q=' + encodeURIComponent(this.itemSearchQuery));
            this.searchResults = await res.json();
        } catch (e) {
            console.error(e);
        } finally {
            this.isSearching = false;
        }
    },

    selectItem(it) {
        this.itemId = it.id;
        if (!this.itemName || this.itemName.trim() === '') {
            this.itemName = it.name;
        }
        this.selectedItemPreview = it;
        this.searchResults = [];
        this.itemSearchQuery = '';
    }
}">

    <!-- Top Navigation Breadcrumb -->
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.cash-shop.index') }}" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-white">{{ $isEdit ? 'Edit Product' : 'Create Cash Shop Product' }}</h1>
                <p class="text-xs text-slate-400 mt-0.5">Configure item delivery, pricing, and purchase limitations.</p>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <form action="{{ $isEdit ? route('admin.cash-shop.update', $product->id) : route('admin.cash-shop.store') }}" method="POST" class="space-y-6">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- Card 1: Emulator Item Selection -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                <i data-lucide="database" class="w-4 h-4 text-cyan-400"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">1. Emulator Item Database Selection</h3>
            </div>

            <!-- Item Search Autocomplete -->
            <div class="space-y-2 relative">
                <label class="block text-xs font-semibold text-slate-300">
                    Search rAthena Item (By Name or ID)
                </label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" x-model="itemSearchQuery" @input.debounce.300ms="searchItems()" placeholder="Type item name (e.g. Old Card Album, Tsurugi, Potion) or ID..."
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-4 py-2.5 text-xs text-white placeholder:text-slate-500 focus:outline-none focus:border-cyan-500">
                    <div x-show="isSearching" class="absolute right-3.5 top-1/2 -translate-y-1/2">
                        <i data-lucide="loader" class="w-4 h-4 text-cyan-400 animate-spin"></i>
                    </div>
                </div>

                <!-- Dropdown Search Results -->
                <div x-show="searchResults.length > 0" @click.away="searchResults = []"
                     class="absolute z-50 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-slate-950 border border-slate-800 rounded-xl shadow-2xl p-2 space-y-1">
                    <template x-for="res in searchResults" :key="res.id">
                        <button type="button" @click="selectItem(res)"
                                class="w-full text-left p-2 rounded-lg hover:bg-slate-800/80 flex items-center justify-between gap-3 transition-colors group">
                            <div class="flex items-center gap-2.5">
                                <img :src="res.icon" alt="" class="w-6 h-6 object-contain shrink-0" onerror="this.src='/images/item-placeholder.png'">
                                <div>
                                    <span class="text-xs font-bold text-white group-hover:text-cyan-400 block" x-text="res.name"></span>
                                    <span class="text-[10px] text-slate-400 font-mono" x-text="'ID #' + res.id + ' • ' + res.type"></span>
                                </div>
                            </div>
                            <span class="text-[10px] font-semibold text-cyan-400 bg-cyan-950/60 border border-cyan-500/30 px-2 py-0.5 rounded">
                                Select
                            </span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Item ID & Quantity Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        rAthena Item ID <span class="text-rose-400">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="item_id" x-model="itemId" required min="1" placeholder="e.g. 616"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs font-mono text-cyan-300 focus:outline-none focus:border-cyan-500">
                        <template x-if="itemId">
                            <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0 p-1">
                                <img :src="'https://static.divine-pride.net/images/items/item/' + itemId + '.png'" alt="" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                            </div>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Quantity Delivered Per Purchase <span class="text-rose-400">*</span>
                    </label>
                    <input type="number" name="item_quantity" value="{{ old('item_quantity', $product->item_quantity ?? 1) }}" required min="1" max="30000"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs font-mono text-white focus:outline-none focus:border-cyan-500">
                    <p class="text-[10px] text-slate-500 mt-1">E.g. set 1 for equipment, or 50/100 for potion &amp; berry stacks.</p>
                </div>
            </div>
        </div>

        <!-- Card 2: Product Presentation -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                <i data-lucide="tag" class="w-4 h-4 text-amber-400"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">2. Product Presentation &amp; Category</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Product Display Name <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="name" x-model="itemName" required placeholder="e.g. 5x Old Card Album Pack"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Shop Category <span class="text-rose-400">*</span>
                    </label>
                    <select name="category_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500 cursor-pointer">
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">
                    Product Description
                </label>
                <textarea name="description" rows="3" placeholder="Informative player-facing description..."
                          class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-slate-200 placeholder:text-slate-600 focus:outline-none focus:border-amber-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Custom Image URL (Optional)
                    </label>
                    <input type="url" name="image_url" value="{{ old('image_url', $product->image_url) }}" placeholder="https://... (defaults to Divine Pride sprite)"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Display Sort Order
                    </label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white font-mono focus:outline-none focus:border-amber-500">
                </div>
            </div>
        </div>

        <!-- Card 3: Pricing & Promotions -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                <i data-lucide="coins" class="w-4 h-4 text-emerald-400"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">3. Pricing &amp; Promotional Discounts</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Regular Price (Cash Points) <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="cash_points_price" value="{{ old('cash_points_price', $product->cash_points_price ?? 100) }}" required min="0"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-3.5 pr-10 py-2 text-xs font-mono font-bold text-amber-300 focus:outline-none focus:border-emerald-500">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-500 font-mono">CP</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Promotional Sale Price (Optional)
                    </label>
                    <div class="relative">
                        <input type="number" name="promotional_price" value="{{ old('promotional_price', $product->promotional_price) }}" min="0" placeholder="e.g. 80"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-3.5 pr-10 py-2 text-xs font-mono font-bold text-emerald-300 focus:outline-none focus:border-emerald-500">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-500 font-mono">CP</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Promotion Start Date (Optional)
                    </label>
                    <input type="datetime-local" name="promotion_start_at"
                           value="{{ old('promotion_start_at', $product->promotion_start_at ? $product->promotion_start_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Promotion Expiration Date (Optional)
                    </label>
                    <input type="datetime-local" name="promotion_end_at"
                           value="{{ old('promotion_end_at', $product->promotion_end_at ? $product->promotion_end_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                </div>
            </div>
        </div>

        <!-- Card 4: Stock & Purchase Restrictions -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                <i data-lucide="shield-alert" class="w-4 h-4 text-purple-400"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">4. Stock &amp; Purchase Restrictions</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-semibold text-slate-300">Available Stock</label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="unlimited_stock" value="1" x-model="unlimitedStock" class="rounded bg-slate-950 border-slate-800 text-amber-500">
                            <span class="text-[10px] text-slate-400">Unlimited</span>
                        </label>
                    </div>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" :disabled="unlimitedStock" min="0"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs font-mono text-white disabled:opacity-40 focus:outline-none focus:border-purple-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Lifetime Limit Per Account
                    </label>
                    <input type="number" name="purchase_limit_per_account" value="{{ old('purchase_limit_per_account', $product->purchase_limit_per_account ?? 0) }}" min="0"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs font-mono text-white focus:outline-none focus:border-purple-500">
                    <p class="text-[10px] text-slate-500 mt-1">0 = No lifetime limit.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Daily Limit Per Account
                    </label>
                    <input type="number" name="purchase_limit_daily" value="{{ old('purchase_limit_daily', $product->purchase_limit_daily ?? 0) }}" min="0"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs font-mono text-white focus:outline-none focus:border-purple-500">
                    <p class="text-[10px] text-slate-500 mt-1">0 = No daily limit.</p>
                </div>
            </div>
        </div>

        <!-- Card 5: Item Metadata (Optional Refine & Cards) -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                <i data-lucide="layers" class="w-4 h-4 text-cyan-400"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">5. Inherent Item Metadata (Preserved Upon Delivery)</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Refine Level (0-20)
                    </label>
                    <input type="number" name="refine" value="{{ old('refine', $product->item_metadata['refine'] ?? 0) }}" min="0" max="20"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs font-mono text-amber-300 focus:outline-none focus:border-cyan-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Account Bound Flag
                    </label>
                    <select name="bound" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                        <option value="0" {{ old('bound', $product->item_metadata['bound'] ?? 0) == 0 ? 'selected' : '' }}>Not Bound (Tradeable)</option>
                        <option value="1" {{ old('bound', $product->item_metadata['bound'] ?? 0) == 1 ? 'selected' : '' }}>Account Bound</option>
                        <option value="2" {{ old('bound', $product->item_metadata['bound'] ?? 0) == 2 ? 'selected' : '' }}>Guild Bound</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Card Slot 1 (Item ID)
                    </label>
                    <input type="number" name="card0" value="{{ old('card0', $product->item_metadata['card0'] ?? 0) }}" min="0" placeholder="0 = None"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs font-mono text-rose-300 focus:outline-none focus:border-cyan-500">
                </div>
            </div>
        </div>

        <!-- Card 6: Publication Status -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                <i data-lucide="eye" class="w-4 h-4 text-emerald-400"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">6. Publication &amp; Feature Flags</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Publication Status <span class="text-rose-400">*</span>
                    </label>
                    <select name="status" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-emerald-500 cursor-pointer">
                        <option value="published" {{ old('status', $product->status ?? 'published') === 'published' ? 'selected' : '' }}>Published (Active in Item Mall)</option>
                        <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draft (Hidden from players)</option>
                        <option value="archived" {{ old('status', $product->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="flex items-center gap-4 pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-800 text-amber-500">
                        <span class="text-xs font-semibold text-white">Highlight as Featured Product</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Button Bar -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
            <a href="{{ route('admin.cash-shop.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 transition-all flex items-center gap-2 cursor-pointer active:scale-95">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>{{ $isEdit ? 'Update Product' : 'Create Product' }}</span>
            </button>
        </div>
    </form>
</div>
@endsection
