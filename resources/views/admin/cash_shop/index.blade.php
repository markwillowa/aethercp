@extends('layouts.admin')

@section('title', 'Cash Shop & Web Item Mall Management')

@section('content')
<div class="space-y-6">

    <!-- Header Hero -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-amber-500/10 text-amber-400 border border-amber-500/30">
                    Monetization &amp; Economy
                </span>
                <span class="text-xs text-slate-500">•</span>
                <span class="text-xs text-slate-400">Cash Points (#CASHPOINTS)</span>
            </div>
            <h1 class="text-2xl font-black text-white mt-1">Cash Shop / Item Mall</h1>
            <p class="text-xs text-slate-400 mt-0.5">Manage published products, promotional pricing, categories, and review purchase logs.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.cash-shop.categories') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-700/60 transition-colors flex items-center gap-1.5">
                <i data-lucide="folder-kanban" class="w-4 h-4 text-cyan-400"></i>
                <span>Categories ({{ $stats['total_categories'] }})</span>
            </a>
            <a href="{{ route('admin.cash-shop.purchases') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-700/60 transition-colors flex items-center gap-1.5">
                <i data-lucide="receipt" class="w-4 h-4 text-purple-400"></i>
                <span>Purchase Ledger</span>
            </a>
            <a href="{{ route('admin.cash-shop.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-md shadow-amber-500/20 transition-all flex items-center gap-1.5 active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>New Product</span>
            </a>
        </div>
    </div>

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Products</p>
                <p class="text-xl font-black text-white mt-0.5">{{ number_format($stats['total_products']) }}</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Published Active</p>
                <p class="text-xl font-black text-white mt-0.5">{{ number_format($stats['published_products']) }}</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 shrink-0">
                <i data-lucide="receipt" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Purchases</p>
                <p class="text-xl font-black text-white mt-0.5">{{ number_format($stats['total_purchases']) }}</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
                <i data-lucide="coins" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">CP Economy Volume</p>
                <p class="text-xl font-black text-white mt-0.5">{{ number_format($stats['total_cp_spent']) }} <span class="text-xs text-purple-300 font-mono">CP</span></p>
            </div>
        </div>
    </div>

    <!-- Filter Console -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
        <form action="{{ route('admin.cash-shop.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products by name, item ID, or slug..."
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-4 py-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-amber-500">
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <select name="category_id" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 cursor-pointer">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:border-amber-500 cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white transition-colors cursor-pointer">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'category_id', 'status']))
                    <a href="{{ route('admin.cash-shop.index') }}" class="px-3 py-2 rounded-xl text-xs font-medium bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors" title="Clear Filters">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Product Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4">Product &amp; Item</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Price (CP)</th>
                        <th class="py-3.5 px-4 text-center">Stock</th>
                        <th class="py-3.5 px-4 text-center">Limits</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0 p-1 relative overflow-hidden shadow-inner">
                                        <img src="{{ $p->icon_url }}" alt="{{ $p->name }}" class="w-7 h-7 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                        @if(($p->item_metadata['refine'] ?? 0) > 0)
                                            <span class="absolute -top-1 -right-1 px-1 rounded text-[8px] font-bold bg-amber-600 text-white font-mono shadow">
                                                +{{ $p->item_metadata['refine'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-bold text-white text-sm">{{ $p->name }}</span>
                                            @if($p->is_featured)
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                    Featured
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            ID #{{ $p->item_id }} • {{ $p->item_quantity }}x per purchase
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $p->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap font-mono font-bold">
                                @if($p->is_on_sale)
                                    <div>
                                        <span class="text-emerald-400 text-sm">{{ number_format($p->effective_price) }} CP</span>
                                        <span class="text-[10px] line-through text-slate-500 block">{{ number_format($p->cash_points_price) }} CP</span>
                                    </div>
                                @else
                                    <span class="text-amber-300 text-sm">{{ number_format($p->cash_points_price) }} CP</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($p->unlimited_stock)
                                    <span class="text-emerald-400 text-[11px] font-semibold">Unlimited</span>
                                @else
                                    <span class="font-mono font-bold {{ $p->stock_quantity <= 5 ? 'text-rose-400' : 'text-slate-200' }}">
                                        {{ number_format($p->stock_quantity) }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center text-[10px] text-slate-400 whitespace-nowrap">
                                @if($p->purchase_limit_per_account > 0 || $p->purchase_limit_daily > 0)
                                    <div>
                                        @if($p->purchase_limit_per_account > 0)
                                            <span>Lifetime: {{ $p->purchase_limit_per_account }}</span>
                                        @endif
                                        @if($p->purchase_limit_daily > 0)
                                            <span class="block">Daily: {{ $p->purchase_limit_daily }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-600">—</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $p->status === 'published' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($p->status === 'draft' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700') }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.cash-shop.edit', $p->id) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Edit Product">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.cash-shop.duplicate', $p->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-cyan-300 transition-colors cursor-pointer" title="Duplicate as Draft">
                                            <i data-lucide="copy" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.cash-shop.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-950 text-slate-400 hover:text-rose-400 transition-colors cursor-pointer" title="Delete Product">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i data-lucide="shopping-bag" class="w-10 h-10 text-slate-600 mx-auto mb-2"></i>
                                <p class="text-sm font-bold text-white">No products found</p>
                                <p class="text-xs text-slate-500 mt-1">Get started by creating your first Cash Shop product.</p>
                                <a href="{{ route('admin.cash-shop.create') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-amber-500 text-slate-950 hover:bg-amber-400 transition-colors">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    <span>Create Product</span>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/60">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
