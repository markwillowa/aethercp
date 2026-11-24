@extends('layouts.admin')

@section('title', 'Player Marketplace Administration')

@section('admin_content')
<div class="space-y-6" x-data="{ adminTab: '{{ request('tab', 'listings') }}' }">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl backdrop-blur">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center font-bold shrink-0 shadow-inner">
                <i data-lucide="store" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-wide uppercase">Player Marketplace Management</h1>
                <p class="text-xs text-slate-400">Monitor active listings, audit escrow custody items, oversee transaction volume, and regulate economy rules.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('marketplace.index') }}" target="_blank" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="external-link" class="w-4 h-4 text-cyan-400"></i>
                <span>View Trade House</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0 text-emerald-400"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 text-rose-400"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Metric Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-4 shadow-sm backdrop-blur">
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0 border border-cyan-500/30">
                <i data-lucide="tag" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Active Listings</p>
                <h3 class="text-2xl font-bold text-white">{{ number_format($stats['active_listings_count']) }}</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Custody Items: <span class="text-cyan-400 font-semibold">{{ number_format($stats['escrow_items_count']) }}</span></p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-4 shadow-sm backdrop-blur">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
                <i data-lucide="check-check" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Completed Trades</p>
                <h3 class="text-2xl font-bold text-white">{{ number_format($stats['total_sales_count']) }}</h3>
                <p class="text-[10px] text-emerald-400 mt-0.5">Successful player purchases</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-4 shadow-sm backdrop-blur">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0 border border-amber-500/30">
                <i data-lucide="coins" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Zeny Volume</p>
                <h3 class="text-lg font-bold text-amber-300 font-mono">{{ number_format($stats['zeny_volume']) }} Z</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Tax Collected: <span class="text-amber-200 font-semibold font-mono">{{ number_format($stats['zeny_tax_collected']) }} Z</span></p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-4 shadow-sm backdrop-blur">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center shrink-0 border border-purple-500/30">
                <i data-lucide="gem" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Cash Points Volume</p>
                <h3 class="text-lg font-bold text-purple-300 font-mono">{{ number_format($stats['points_volume']) }} CP</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Tax Collected: <span class="text-purple-200 font-semibold font-mono">{{ number_format($stats['points_tax_collected']) }} CP</span></p>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
            <button @click="adminTab = 'listings'" :class="adminTab === 'listings' ? 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900 border border-slate-800'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="list" class="w-4 h-4"></i>
                Listings Moderation ({{ $listings->total() }})
            </button>
            <button @click="adminTab = 'custody'" :class="adminTab === 'custody' ? 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900 border border-slate-800'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="archive" class="w-4 h-4"></i>
                Escrow Custody Vault ({{ $custodyItems->total() }})
            </button>
            <button @click="adminTab = 'transactions'" :class="adminTab === 'transactions' ? 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900 border border-slate-800'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="history" class="w-4 h-4"></i>
                Transaction Ledger ({{ $transactions->total() }})
            </button>
            <button @click="adminTab = 'settings'" :class="adminTab === 'settings' ? 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900 border border-slate-800'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="settings-2" class="w-4 h-4"></i>
                Configuration &amp; Economy Rules
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: LISTINGS MODERATION                 -->
        <!-- ========================================== -->
        <div x-show="adminTab === 'listings'" class="space-y-4">
            <!-- Search & Filters -->
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 backdrop-blur shadow-lg">
                <form method="GET" action="{{ route('admin.marketplace.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="hidden" name="tab" value="listings">
                    
                    <div class="md:col-span-2 relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <i data-lucide="search" class="w-4 h-4 text-cyan-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search listing code, seller character, or item ID..." 
                               style="padding-left: 44px !important; padding-right: 16px !important; height: 42px;"
                               class="w-full rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>

                    <div>
                        <select name="status" style="height: 42px;" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Listings</option>
                            <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <select name="currency" style="height: 42px;" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            <option value="" {{ !request('currency') ? 'selected' : '' }}>All Currencies</option>
                            <option value="zeny" {{ request('currency') === 'zeny' ? 'selected' : '' }}>Zeny Only</option>
                            <option value="cashpoints" {{ request('currency') === 'cashpoints' ? 'selected' : '' }}>Cash Points Only</option>
                        </select>
                        <button type="submit" style="height: 42px;" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 transition-all cursor-pointer shrink-0">
                            Filter
                        </button>
                        @if(request('search') || request('status') || request('currency'))
                            <a href="{{ route('admin.marketplace.index', ['tab' => 'listings']) }}" style="height: 42px; width: 42px;" class="rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-slate-800 border border-slate-700 transition-all shrink-0 flex items-center justify-center" title="Reset Filters">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Listings Table -->
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-lg backdrop-blur">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-950/80 text-slate-300 uppercase tracking-wider text-[11px] font-semibold">
                                <th class="py-3 px-4">Listing Code</th>
                                <th class="py-3 px-4">Item Details</th>
                                <th class="py-3 px-4">Seller &amp; Account</th>
                                <th class="py-3 px-4">Unit Price</th>
                                <th class="py-3 px-4">Stock</th>
                                <th class="py-3 px-4">Expires In</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Moderation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @forelse($listings as $itemListing)
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-cyan-400">{{ $itemListing->listing_code }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <img src="{{ $itemListing->item->icon_url }}" alt="Item" class="w-8 h-8 object-contain rounded bg-slate-950 border border-slate-800 p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <p class="font-bold text-white">{{ $itemListing->item->formatted_name }}</p>
                                                    @if($itemListing->item->slots > 0)
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold font-mono bg-slate-800 text-cyan-300 border border-cyan-500/30">
                                                            {{ $itemListing->item->slots }} {{ Str::plural('SLOT', $itemListing->item->slots) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(!empty($itemListing->item->cards))
                                                    <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                                        @foreach($itemListing->item->cards as $c)
                                                            <span class="px-1.5 py-0.5 rounded text-[8px] bg-rose-950/60 border border-rose-500/40 text-rose-300">
                                                                ◆ {{ $c['name'] }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <p class="text-[10px] text-slate-400 mt-0.5 font-mono">ID #{{ $itemListing->item->nameid }} • {{ $itemListing->item->item_type }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-medium text-white">{{ $itemListing->seller_char_name }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $itemListing->sellerUser->username ?? 'Account #'.$itemListing->seller_account_id }}</p>
                                    </td>
                                    <td class="py-3 px-4 font-mono">
                                        @if($itemListing->currency_type === 'zeny')
                                            <span class="inline-flex items-center gap-1 font-bold text-amber-300">
                                                <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-400"></i>
                                                {{ number_format($itemListing->unit_price) }} Z
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 font-bold text-purple-300">
                                                <i data-lucide="gem" class="w-3.5 h-3.5 text-purple-400"></i>
                                                {{ number_format($itemListing->unit_price) }} CP
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-white">{{ $itemListing->remaining_quantity }}</span> / {{ $itemListing->quantity }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-300">
                                        {{ $itemListing->remaining_time }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($itemListing->status === 'active')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Active</span>
                                        @elseif($itemListing->status === 'sold')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">Sold</span>
                                        @elseif($itemListing->status === 'cancelled')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Cancelled</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300">Expired</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if($itemListing->status === 'active')
                                            <form action="{{ route('admin.marketplace.moderate', $itemListing->id) }}" method="POST" onsubmit="return confirm('Cancel this listing and refund the item to seller escrow vault?');" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="action" value="cancel">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-rose-950/60 border border-rose-500/40 text-rose-300 hover:bg-rose-900 transition-colors cursor-pointer" title="Cancel Listing &amp; Return Item">
                                                    Cancel
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-500 text-[11px] italic">Settled</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-slate-400">
                                        <i data-lucide="package" class="w-8 h-8 text-slate-600 mx-auto mb-2"></i>
                                        No marketplace listings match the selected filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($listings->hasPages())
                    <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                        {{ $listings->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: ESCROW CUSTODY VAULT                -->
        <!-- ========================================== -->
        <div x-show="adminTab === 'custody'" class="space-y-4">
            <!-- Search & Filters -->
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 backdrop-blur shadow-lg">
                <form method="GET" action="{{ route('admin.marketplace.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="hidden" name="tab" value="custody">
                    
                    <div class="md:col-span-2 relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <i data-lucide="search" class="w-4 h-4 text-cyan-400"></i>
                        </div>
                        <input type="text" name="custody_search" value="{{ request('custody_search') }}" placeholder="Search item ID, account ID, or username..." 
                               style="padding-left: 44px !important; padding-right: 16px !important; height: 42px;"
                               class="w-full rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>

                    <div>
                        <select name="custody_status" style="height: 42px;" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            <option value="all" {{ request('custody_status') === 'all' || !request('custody_status') ? 'selected' : '' }}>All Items in Vault</option>
                            <option value="in_custody" {{ request('custody_status') === 'in_custody' ? 'selected' : '' }}>In Custody (Unlisted)</option>
                            <option value="listed" {{ request('custody_status') === 'listed' ? 'selected' : '' }}>Actively Listed</option>
                            <option value="withdrawn" {{ request('custody_status') === 'withdrawn' ? 'selected' : '' }}>Returned / Withdrawn</option>
                            <option value="sold" {{ request('custody_status') === 'sold' ? 'selected' : '' }}>Sold &amp; Delivered</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" style="height: 42px;" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 transition-all cursor-pointer w-full">
                            Filter Vault
                        </button>
                        @if(request('custody_search') || request('custody_status'))
                            <a href="{{ route('admin.marketplace.index', ['tab' => 'custody']) }}" style="height: 42px; width: 42px;" class="rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-slate-800 border border-slate-700 transition-all shrink-0 flex items-center justify-center" title="Reset Filters">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Custody Vault Table -->
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-lg backdrop-blur">
                <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white">Items Held in Marketplace Escrow</h3>
                        <p class="text-[11px] text-slate-400">All player items currently deposited into marketplace custody or active listings.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-950/80 text-slate-300 uppercase tracking-wider text-[11px] font-semibold">
                                <th class="py-3 px-4">Custody ID</th>
                                <th class="py-3 px-4">Item Info</th>
                                <th class="py-3 px-4">Account &amp; Character</th>
                                <th class="py-3 px-4">Quantity</th>
                                <th class="py-3 px-4">Card Slots / Metadata</th>
                                <th class="py-3 px-4">Vault Status</th>
                                <th class="py-3 px-4">Deposited Date</th>
                                <th class="py-3 px-4 text-right">Emergency Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @forelse($custodyItems as $cItem)
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-cyan-400">#{{ $cItem->id }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $cItem->icon_url }}" alt="Item" class="w-7 h-7 object-contain rounded bg-slate-950 p-0.5 border border-slate-800" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-white">{{ $cItem->formatted_name }}</span>
                                                    @if($cItem->slots > 0)
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold font-mono bg-slate-800 text-cyan-300 border border-cyan-500/30">
                                                            {{ $cItem->slots }} {{ Str::plural('SLOT', $cItem->slots) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-[10px] text-slate-400 block font-mono">ID #{{ $cItem->nameid }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-medium text-white">{{ $cItem->user->username ?? 'User #'.$cItem->account_id }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $cItem->character->name ?? 'Char #'.$cItem->char_id }}</p>
                                    </td>
                                    <td class="py-3 px-4 font-bold text-white">{{ number_format($cItem->amount) }}</td>
                                    <td class="py-3 px-4">
                                        @if(!empty($cItem->cards))
                                            <div class="flex items-center gap-1 flex-wrap">
                                                @foreach($cItem->cards as $card)
                                                    <span class="px-1.5 py-0.5 rounded text-[8px] bg-rose-950/60 border border-rose-500/40 text-rose-300">
                                                        {{ $card['name'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-500 italic">None</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($cItem->status === 'in_custody')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">In Custody</span>
                                        @elseif($cItem->status === 'listed')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Listed</span>
                                        @elseif($cItem->status === 'sold')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Sold</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-700 text-slate-300">Withdrawn</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">
                                        {{ $cItem->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if(in_array($cItem->status, ['in_custody', 'listed']))
                                            <form action="{{ route('admin.marketplace.custody.refund', $cItem->id) }}" method="POST" onsubmit="return confirm('Emergency Return: This will deposit the item directly back into the character inventory in-game. Proceed?');" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 hover:bg-cyan-500/20 transition-colors cursor-pointer" title="Direct In-Game Refund">
                                                    Refund to Char
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-500 text-[11px] italic">Archived</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-slate-400">
                                        <i data-lucide="archive" class="w-8 h-8 text-slate-600 mx-auto mb-2"></i>
                                        No escrow custody items match the query.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($custodyItems->hasPages())
                    <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                        {{ $custodyItems->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: TRANSACTION LEDGER                  -->
        <!-- ========================================== -->
        <div x-show="adminTab === 'transactions'" class="space-y-4">
            <!-- Search & Filters -->
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 backdrop-blur shadow-lg">
                <form method="GET" action="{{ route('admin.marketplace.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="hidden" name="tab" value="transactions">
                    
                    <div class="md:col-span-2 relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <i data-lucide="search" class="w-4 h-4 text-cyan-400"></i>
                        </div>
                        <input type="text" name="tx_search" value="{{ request('tx_search') }}" placeholder="Search TX reference code, buyer character, or seller..." 
                               style="padding-left: 44px !important; padding-right: 16px !important; height: 42px;"
                               class="w-full rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>

                    <div>
                        <select name="tx_currency" style="height: 42px;" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            <option value="" {{ !request('tx_currency') ? 'selected' : '' }}>All Currencies</option>
                            <option value="zeny" {{ request('tx_currency') === 'zeny' ? 'selected' : '' }}>Zeny Trades Only</option>
                            <option value="cashpoints" {{ request('tx_currency') === 'cashpoints' ? 'selected' : '' }}>Cash Points Trades Only</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" style="height: 42px;" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 transition-all cursor-pointer w-full">
                            Filter Ledger
                        </button>
                        @if(request('tx_search') || request('tx_currency'))
                            <a href="{{ route('admin.marketplace.index', ['tab' => 'transactions']) }}" style="height: 42px; width: 42px;" class="rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-slate-800 border border-slate-700 transition-all shrink-0 flex items-center justify-center" title="Reset Filters">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-lg backdrop-blur">
                <div class="p-4 border-b border-slate-800">
                    <h3 class="text-sm font-bold text-white">Marketplace Transactions &amp; Economic Ledger</h3>
                    <p class="text-[11px] text-slate-400">Complete historical record of player-to-player sales and taxes collected.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-950/80 text-slate-300 uppercase tracking-wider text-[11px] font-semibold">
                                <th class="py-3 px-4">TX Reference</th>
                                <th class="py-3 px-4">Purchased Item</th>
                                <th class="py-3 px-4">Buyer</th>
                                <th class="py-3 px-4">Seller</th>
                                <th class="py-3 px-4">Gross Total</th>
                                <th class="py-3 px-4">Tax Fee</th>
                                <th class="py-3 px-4">Net Seller Proceeds</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Fulfillment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-cyan-400">{{ $tx->transaction_code }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $tx->item->icon_url }}" alt="Item" class="w-7 h-7 object-contain rounded bg-slate-950 p-0.5 border border-slate-800" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-white">{{ $tx->quantity }}x {{ $tx->item->formatted_name }}</span>
                                                    @if($tx->item->slots > 0)
                                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-bold font-mono bg-slate-800 text-cyan-300 border border-cyan-500/30">
                                                            {{ $tx->item->slots }} {{ Str::plural('SLOT', $tx->item->slots) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 font-medium text-white">{{ $tx->buyer_char_name }}</td>
                                    <td class="py-3 px-4 font-medium text-white">{{ $tx->seller_char_name }}</td>
                                    <td class="py-3 px-4 font-bold font-mono {{ $tx->currency_type === 'zeny' ? 'text-amber-300' : 'text-purple-300' }}">
                                        {{ number_format($tx->gross_amount) }} {{ $tx->currency_type === 'zeny' ? 'Z' : 'CP' }}
                                    </td>
                                    <td class="py-3 px-4 text-rose-300 font-mono">
                                        -{{ number_format($tx->tax_amount) }}
                                    </td>
                                    <td class="py-3 px-4 text-emerald-300 font-bold font-mono">
                                        {{ number_format($tx->net_seller_proceeds) }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">
                                        {{ $tx->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            <i data-lucide="check" class="w-3 h-3"></i> Delivered
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-10 text-slate-400">
                                        <i data-lucide="history" class="w-8 h-8 text-slate-600 mx-auto mb-2"></i>
                                        No marketplace sales recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 4: CONFIGURATION & ECONOMY RULES       -->
        <!-- ========================================== -->
        <div x-show="adminTab === 'settings'" class="space-y-6">
            <form action="{{ route('admin.marketplace.settings') }}" method="POST">
                @csrf

                <!-- Section 1: Master Activation & Maintenance -->
                <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-sm backdrop-blur">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i data-lucide="power" class="w-4 h-4 text-cyan-400"></i>
                        Extension Activation &amp; Maintenance Status
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-950 border border-slate-800">
                            <div>
                                <label class="text-xs font-bold text-white block">Enable Marketplace Extension</label>
                                <span class="text-[11px] text-slate-400">Master switch to make the Player Marketplace accessible to registered players.</span>
                            </div>
                            <input type="checkbox" name="module_enabled_marketplace" value="1" {{ !empty($settings['enabled']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                        </div>

                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-950 border border-slate-800">
                            <div>
                                <label class="text-xs font-bold text-white block">Marketplace Maintenance Mode</label>
                                <span class="text-[11px] text-slate-400">Temporarily suspend listing creation and purchases while preserving all escrowed items.</span>
                            </div>
                            <input type="checkbox" name="marketplace_maintenance_mode" value="1" {{ !empty($settings['maintenance_mode']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <!-- Section 2: Operations, Limits & Announcements -->
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-sm backdrop-blur">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                            <i data-lucide="sliders" class="w-4 h-4 text-cyan-400"></i>
                            Operations, Announcements &amp; Limits
                        </h3>

                        <div>
                            <label class="text-xs font-semibold text-slate-300 block mb-1">Marketplace Announcement</label>
                            <input type="text" name="marketplace_announcement" value="{{ $settings['announcement'] }}" placeholder="e.g. Welcome to the Trade House! 5% tax is currently active." class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Displayed at the top of the Trade House interface</span>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-300 block mb-1">Trade House Rules &amp; Notice</label>
                            <textarea name="marketplace_rules" rows="3" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">{{ $settings['rules'] }}</textarea>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Official rules guidelines displayed in the rules modal for players</span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Max / Player</label>
                                <input type="number" name="marketplace_max_listings_player" value="{{ $settings['max_listings_player'] }}" min="1" max="100" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Listings/Account</span>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Max / Character</label>
                                <input type="number" name="marketplace_max_listings_char" value="{{ $settings['max_listings_char'] }}" min="1" max="50" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Listings/Char</span>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Max Items / Listing</label>
                                <input type="number" name="marketplace_max_items_per_listing" value="{{ $settings['max_items_per_listing'] ?? 1000 }}" min="1" max="30000" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Max Stack</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Minimum Selling Price</label>
                                <input type="number" name="marketplace_min_price" value="{{ $settings['min_price'] }}" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Maximum Selling Price</label>
                                <input type="number" name="marketplace_max_price" value="{{ $settings['max_price'] }}" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 pt-2 border-t border-slate-800">
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Min Duration</label>
                                <input type="number" name="marketplace_min_duration_hours" value="{{ $settings['min_duration_hours'] ?? 24 }}" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Hours</span>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Default Duration</label>
                                <select name="marketplace_default_duration_hours" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                    <option value="24" {{ $settings['default_duration_hours'] == 24 ? 'selected' : '' }}>24 Hours (1 Day)</option>
                                    <option value="72" {{ $settings['default_duration_hours'] == 72 ? 'selected' : '' }}>72 Hours (3 Days)</option>
                                    <option value="168" {{ $settings['default_duration_hours'] == 168 ? 'selected' : '' }}>168 Hours (7 Days)</option>
                                    <option value="336" {{ $settings['default_duration_hours'] == 336 ? 'selected' : '' }}>336 Hours (14 Days)</option>
                                    <option value="720" {{ $settings['default_duration_hours'] == 720 ? 'selected' : '' }}>720 Hours (30 Days)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Max Duration</label>
                                <input type="number" name="marketplace_max_duration_hours" value="{{ $settings['max_duration_hours'] ?? 720 }}" min="24" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Hours</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Currencies, Taxes & Item Safety -->
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-sm backdrop-blur">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                            <i data-lucide="shield-check" class="w-4 h-4 text-cyan-400"></i>
                            Currencies, Fees, Taxes &amp; Item Safety
                        </h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800">
                                <div>
                                    <label class="text-xs font-bold text-white block">Allow Zeny Payments</label>
                                    <span class="text-[10px] text-slate-400">Trade using character Zeny</span>
                                </div>
                                <input type="checkbox" name="marketplace_allow_zeny" value="1" {{ !empty($settings['allow_zeny']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800">
                                <div>
                                    <label class="text-xs font-bold text-white block">Allow Cash Points Payments</label>
                                    <span class="text-[10px] text-slate-400">Trade using account CP</span>
                                </div>
                                <input type="checkbox" name="marketplace_allow_cashpoints" value="1" {{ !empty($settings['allow_cashpoints']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Marketplace Sales Tax (%)</label>
                                <input type="number" step="0.1" name="marketplace_tax_percent" value="{{ $settings['tax_percent'] }}" min="0" max="50" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Deducted automatically from seller proceeds</span>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Listing Insertion Fee (Zeny)</label>
                                <input type="number" name="marketplace_listing_fee" value="{{ $settings['listing_fee'] }}" min="0" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Flat fee charged from character Zeny</span>
                            </div>
                        </div>

                        <!-- Item Safety Rules -->
                        <div class="space-y-2 pt-2 border-t border-slate-800">
                            <p class="text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Item Safeguards &amp; Restriction Rules</p>

                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_withdrawals" value="1" {{ !empty($settings['allow_withdrawals']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Item Withdrawals
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_stackables" value="1" {{ !empty($settings['allow_stackables']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Stackable Items
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_equipment" value="1" {{ !empty($settings['allow_equipment']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Equipment
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_refined" value="1" {{ !empty($settings['allow_refined']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Refined Equipment
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_cards" value="1" {{ !empty($settings['allow_cards']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Cards &amp; Random Options
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_history_enabled" value="1" {{ !empty($settings['history_enabled']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Enable Marketplace History
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_bound" value="1" {{ !empty($settings['allow_bound']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Bound Items (Caution)
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_rental" value="1" {{ !empty($settings['allow_rental']) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Rental Items (Caution)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-xs bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-md transition-all flex items-center gap-2 cursor-pointer">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Save Marketplace Settings</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
