@extends('layouts.admin')

@section('title', 'Player Marketplace - Admin Control Panel')

@section('admin_content')
<div class="space-y-6" x-data="{ adminTab: '{{ $activeTab ?? 'listings' }}' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-cinzel text-white flex items-center gap-2">
                <i data-lucide="scale" class="w-6 h-6 text-[#D4AF37]"></i>
                Player Marketplace &amp; Escrow
            </h1>
            <p class="text-xs text-slate-400 mt-1">Manage player-to-player trade house listings, item custody escrow, economy taxes, and trading rules.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('marketplace.index') }}" target="_blank" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#2A1D38] hover:bg-[#38264C] text-[#E5B358] border border-[#523A6E] flex items-center gap-1.5 transition-all shadow-sm">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Visit Trade House
            </a>
            @if(!empty($settings['maintenance_mode']))
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> Maintenance Mode
                </span>
            @elseif(empty($settings['enabled']))
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40">
                    <i data-lucide="power-off" class="w-3.5 h-3.5"></i> Extension Disabled
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Marketplace Active
                </span>
            @endif
        </div>
    </div>

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/20 text-[#FCE69B] flex items-center justify-center shrink-0 border border-[#D4AF37]/30">
                <i data-lucide="tag" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Active Listings</p>
                <h3 class="text-2xl font-bold text-white font-cinzel">{{ number_format($stats['active_listings']) }}</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ number_format($stats['total_items_in_custody']) }} in custody ({{ number_format($stats['total_items_all']) }} total)</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center shrink-0 border border-emerald-500/30">
                <i data-lucide="check-check" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Completed Trades</p>
                <h3 class="text-2xl font-bold text-white font-cinzel">{{ number_format($stats['total_sales_count']) }}</h3>
                <p class="text-[10px] text-emerald-400 mt-0.5">Successful player purchases</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center shrink-0 border border-amber-500/30">
                <i data-lucide="coins" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Zeny Volume</p>
                <h3 class="text-lg font-bold text-amber-300 font-cinzel">{{ number_format($stats['zeny_volume']) }} Z</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Tax Collected: <span class="text-amber-200 font-semibold">{{ number_format($stats['zeny_tax_collected']) }} Z</span></p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-300 flex items-center justify-center shrink-0 border border-purple-500/30">
                <i data-lucide="gem" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Cash Points Volume</p>
                <h3 class="text-lg font-bold text-purple-300 font-cinzel">{{ number_format($stats['points_volume']) }} CP</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Tax Collected: <span class="text-purple-200 font-semibold">{{ number_format($stats['points_tax_collected']) }} CP</span></p>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-2 border-b border-[#3E2E4E] pb-3">
            <button @click="adminTab = 'listings'" :class="adminTab === 'listings' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/50 shadow-sm' : 'text-slate-400 hover:text-white bg-[#1C1525] border border-transparent'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="list" class="w-4 h-4"></i>
                Listings Moderation ({{ $listings->total() }})
            </button>
            <button @click="adminTab = 'custody'" :class="adminTab === 'custody' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/50 shadow-sm' : 'text-slate-400 hover:text-white bg-[#1C1525] border border-transparent'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="archive" class="w-4 h-4"></i>
                Escrow Custody Vault ({{ $custodyItems->total() }})
            </button>
            <button @click="adminTab = 'transactions'" :class="adminTab === 'transactions' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/50 shadow-sm' : 'text-slate-400 hover:text-white bg-[#1C1525] border border-transparent'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="history" class="w-4 h-4"></i>
                Transaction Ledger ({{ $transactions->total() }})
            </button>
            <button @click="adminTab = 'settings'" :class="adminTab === 'settings' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/50 shadow-sm' : 'text-slate-400 hover:text-white bg-[#1C1525] border border-transparent'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="settings-2" class="w-4 h-4"></i>
                Configuration &amp; Economy Rules
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: LISTINGS MODERATION                 -->
        <!-- ========================================== -->
        <div x-show="adminTab === 'listings'" class="space-y-4">
            <!-- Search & Filters -->
            <div class="rounded-2xl bg-[#1C1525] border border-[#3E2E4E] p-4">
                <form method="GET" action="{{ route('admin.marketplace.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="hidden" name="tab" value="listings">
                    
                    <div class="md:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search listing code, seller character, or item ID..." 
                               style="padding-left: 3rem !important; padding-right: 1rem !important; height: 40px;"
                               class="w-full rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white placeholder-slate-500 focus:outline-none focus:border-[#D4AF37] transition-all">
                    </div>

                    <div>
                        <select name="status" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                            <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Listings</option>
                            <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <select name="currency" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                            <option value="" {{ !request('currency') ? 'selected' : '' }}>All Currencies</option>
                            <option value="zeny" {{ request('currency') === 'zeny' ? 'selected' : '' }}>Zeny Only</option>
                            <option value="cashpoints" {{ request('currency') === 'cashpoints' ? 'selected' : '' }}>Cash Points Only</option>
                        </select>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#D4AF37] hover:bg-[#FCE69B] text-black transition-all cursor-pointer shrink-0">
                            Filter
                        </button>
                        @if(request('search') || request('status') || request('currency'))
                            <a href="{{ route('admin.marketplace.index', ['tab' => 'listings']) }}" class="px-2.5 py-2 rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-[#251A33] border border-[#3E2E4E] transition-all shrink-0" title="Reset Filters">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Listings Table -->
            <div class="rounded-2xl bg-[#1C1525] border border-[#3E2E4E] overflow-hidden shadow-lg">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB]">
                                <th class="py-3 px-4 font-cinzel">Listing Code</th>
                                <th class="py-3 px-4 font-cinzel">Item Details</th>
                                <th class="py-3 px-4 font-cinzel">Seller &amp; Account</th>
                                <th class="py-3 px-4 font-cinzel">Unit Price</th>
                                <th class="py-3 px-4 font-cinzel">Stock</th>
                                <th class="py-3 px-4 font-cinzel">Expires In</th>
                                <th class="py-3 px-4 font-cinzel">Status</th>
                                <th class="py-3 px-4 text-right font-cinzel">Moderation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            @forelse($listings as $itemListing)
                                <tr class="hover:bg-[#251A33]/50 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-[#E5B358]">{{ $itemListing->listing_code }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <img src="{{ $itemListing->item->icon_url }}" alt="Item" class="w-8 h-8 object-contain rounded bg-[#140E1B] border border-[#3E2E4E] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <p class="font-bold text-white">
                                                    @if($itemListing->item->refine > 0)
                                                        <span class="text-amber-300 font-mono">+{{ $itemListing->item->refine }}</span>
                                                    @endif
                                                    {{ $itemListing->item->formatted_name }}
                                                </p>
                                                <p class="text-[10px] text-slate-400">ID #{{ $itemListing->item->nameid }} • {{ $itemListing->item->item_type }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-medium text-white">{{ $itemListing->seller_char_name }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $itemListing->sellerUser->username ?? 'Account #'.$itemListing->seller_account_id }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($itemListing->currency_type === 'zeny')
                                            <span class="inline-flex items-center gap-1 font-bold text-amber-300">
                                                <i data-lucide="coins" class="w-3.5 h-3.5"></i>
                                                {{ number_format($itemListing->unit_price) }} Z
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 font-bold text-purple-300">
                                                <i data-lucide="gem" class="w-3.5 h-3.5"></i>
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
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/20 text-slate-300 border border-slate-500/30">Expired</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if($itemListing->status === 'active')
                                            <form action="{{ route('admin.marketplace.cancel', $itemListing->id) }}" method="POST" onsubmit="return confirm('Cancel this listing and release the item back to seller custody?');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-500/20 text-rose-300 hover:bg-rose-500/40 border border-rose-500/30 transition-all cursor-pointer">
                                                    Cancel Listing
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-500 text-[11px]">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-slate-400">
                                        <i data-lucide="inbox" class="w-8 h-8 text-slate-600 mx-auto mb-2"></i>
                                        No marketplace listings match the selected search criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($listings->hasPages())
                    <div class="p-4 border-t border-[#3E2E4E]">
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
            <div class="rounded-2xl bg-[#1C1525] border border-[#3E2E4E] p-4">
                <form method="GET" action="{{ route('admin.marketplace.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="hidden" name="tab" value="custody">
                    
                    <div class="md:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="text" name="custody_search" value="{{ request('custody_search') }}" placeholder="Search item ID, account ID, or username..." 
                               style="padding-left: 3rem !important; padding-right: 1rem !important; height: 40px;"
                               class="w-full rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white placeholder-slate-500 focus:outline-none focus:border-[#D4AF37] transition-all">
                    </div>

                    <div>
                        <select name="custody_status" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                            <option value="all" {{ request('custody_status') === 'all' || !request('custody_status') ? 'selected' : '' }}>All Items in Vault</option>
                            <option value="in_custody" {{ request('custody_status') === 'in_custody' ? 'selected' : '' }}>In Custody (Unlisted)</option>
                            <option value="listed" {{ request('custody_status') === 'listed' ? 'selected' : '' }}>Actively Listed</option>
                            <option value="withdrawn" {{ request('custody_status') === 'withdrawn' ? 'selected' : '' }}>Returned / Withdrawn</option>
                            <option value="sold" {{ request('custody_status') === 'sold' ? 'selected' : '' }}>Sold &amp; Delivered</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#D4AF37] hover:bg-[#FCE69B] text-black transition-all cursor-pointer w-full">
                            Filter Vault
                        </button>
                        @if(request('custody_search') || request('custody_status'))
                            <a href="{{ route('admin.marketplace.index', ['tab' => 'custody']) }}" class="px-2.5 py-2 rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-[#251A33] border border-[#3E2E4E] transition-all shrink-0" title="Reset Filters">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Custody Vault Table -->
            <div class="rounded-2xl bg-[#1C1525] border border-[#3E2E4E] overflow-hidden shadow-lg">
                <div class="p-4 border-b border-[#3E2E4E] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white font-cinzel">Items Held in Marketplace Escrow</h3>
                        <p class="text-[11px] text-slate-400">All player items currently deposited into marketplace custody or active listings.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB]">
                                <th class="py-3 px-4 font-cinzel">Custody ID</th>
                                <th class="py-3 px-4 font-cinzel">Item Info</th>
                                <th class="py-3 px-4 font-cinzel">Account &amp; Character</th>
                                <th class="py-3 px-4 font-cinzel">Quantity</th>
                                <th class="py-3 px-4 font-cinzel">Card Slots / Metadata</th>
                                <th class="py-3 px-4 font-cinzel">Deposited At</th>
                                <th class="py-3 px-4 font-cinzel">Escrow State</th>
                                <th class="py-3 px-4 text-right font-cinzel">Admin Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            @forelse($custodyItems as $custodyItem)
                                <tr class="hover:bg-[#251A33]/50 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-[#E5B358]">#{{ $custodyItem->id }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <img src="{{ $custodyItem->icon_url }}" alt="Item" class="w-8 h-8 object-contain rounded bg-[#140E1B] border border-[#3E2E4E] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <p class="font-bold text-white">
                                                    @if($custodyItem->refine > 0)
                                                        <span class="text-amber-300 font-mono">+{{ $custodyItem->refine }}</span>
                                                    @endif
                                                    {{ $custodyItem->formatted_name }}
                                                </p>
                                                <p class="text-[10px] text-slate-400">ID #{{ $custodyItem->nameid }} • {{ $custodyItem->item_type }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-medium text-white">{{ $custodyItem->user->username ?? 'Account #'.$custodyItem->account_id }}</p>
                                        <p class="text-[10px] text-slate-400">Char ID: {{ $custodyItem->char_id }}</p>
                                    </td>
                                    <td class="py-3 px-4 font-bold text-white">
                                        {{ number_format($custodyItem->amount) }}x
                                    </td>
                                    <td class="py-3 px-4 text-slate-300 font-mono text-[11px]">
                                        @php
                                            $cards = array_filter([$custodyItem->card0, $custodyItem->card1, $custodyItem->card2, $custodyItem->card3]);
                                        @endphp
                                        @if(count($cards) > 0)
                                            <span class="text-amber-300">Cards: [{{ implode(', ', $cards) }}]</span>
                                        @else
                                            <span class="text-slate-500">None</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-400">
                                        {{ $custodyItem->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($custodyItem->status === 'in_custody')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">In Custody</span>
                                        @elseif($custodyItem->status === 'listed')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Actively Listed</span>
                                        @elseif($custodyItem->status === 'withdrawn')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/20 text-slate-300 border border-slate-500/30">Returned</span>
                                        @elseif($custodyItem->status === 'sold')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">Sold</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if(in_array($custodyItem->status, ['in_custody', 'listed']))
                                            <form action="{{ route('admin.marketplace.return', $custodyItem->id) }}" method="POST" onsubmit="return confirm('Forcefully return this item to Account #{{ $custodyItem->account_id }}\'s Kafra storage?');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-500/20 text-amber-300 hover:bg-amber-500/40 border border-amber-500/30 transition-all cursor-pointer">
                                                    Force Return to Storage
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-500 text-[11px]">—</span>
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
                    <div class="p-4 border-t border-[#3E2E4E]">
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
            <div class="rounded-2xl bg-[#1C1525] border border-[#3E2E4E] p-4">
                <form method="GET" action="{{ route('admin.marketplace.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="hidden" name="tab" value="transactions">
                    
                    <div class="md:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="text" name="tx_search" value="{{ request('tx_search') }}" placeholder="Search TX reference code, buyer character, or seller..." 
                               style="padding-left: 3rem !important; padding-right: 1rem !important; height: 40px;"
                               class="w-full rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white placeholder-slate-500 focus:outline-none focus:border-[#D4AF37] transition-all">
                    </div>

                    <div>
                        <select name="tx_currency" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                            <option value="" {{ !request('tx_currency') ? 'selected' : '' }}>All Currencies</option>
                            <option value="zeny" {{ request('tx_currency') === 'zeny' ? 'selected' : '' }}>Zeny Trades Only</option>
                            <option value="cashpoints" {{ request('tx_currency') === 'cashpoints' ? 'selected' : '' }}>Cash Points Trades Only</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#D4AF37] hover:bg-[#FCE69B] text-black transition-all cursor-pointer w-full">
                            Filter Ledger
                        </button>
                        @if(request('tx_search') || request('tx_currency'))
                            <a href="{{ route('admin.marketplace.index', ['tab' => 'transactions']) }}" class="px-2.5 py-2 rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-[#251A33] border border-[#3E2E4E] transition-all shrink-0" title="Reset Filters">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="rounded-2xl bg-[#1C1525] border border-[#3E2E4E] overflow-hidden shadow-lg">
                <div class="p-4 border-b border-[#3E2E4E]">
                    <h3 class="text-sm font-bold text-white font-cinzel">Marketplace Transactions &amp; Economic Ledger</h3>
                    <p class="text-[11px] text-slate-400">Complete historical record of player-to-player sales and taxes collected.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB]">
                                <th class="py-3 px-4 font-cinzel">TX Reference</th>
                                <th class="py-3 px-4 font-cinzel">Purchased Item</th>
                                <th class="py-3 px-4 font-cinzel">Buyer</th>
                                <th class="py-3 px-4 font-cinzel">Seller</th>
                                <th class="py-3 px-4 font-cinzel">Gross Total</th>
                                <th class="py-3 px-4 font-cinzel">Tax Fee</th>
                                <th class="py-3 px-4 font-cinzel">Net Seller Proceeds</th>
                                <th class="py-3 px-4 font-cinzel">Date</th>
                                <th class="py-3 px-4 font-cinzel">Fulfillment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-[#251A33]/50 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-[#E5B358]">{{ $tx->transaction_code }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $tx->item->icon_url }}" alt="Item" class="w-7 h-7 object-contain rounded bg-[#140E1B] p-0.5 border border-[#3E2E4E]" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <span class="font-bold text-white">{{ $tx->quantity }}x {{ $tx->item->formatted_name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 font-medium text-white">{{ $tx->buyer_char_name }}</td>
                                    <td class="py-3 px-4 font-medium text-white">{{ $tx->seller_char_name }}</td>
                                    <td class="py-3 px-4 font-bold {{ $tx->currency_type === 'zeny' ? 'text-amber-300' : 'text-purple-300' }}">
                                        {{ number_format($tx->gross_amount) }} {{ $tx->currency_type === 'zeny' ? 'Z' : 'CP' }}
                                    </td>
                                    <td class="py-3 px-4 text-rose-300 font-mono">
                                        -{{ number_format($tx->tax_amount) }}
                                    </td>
                                    <td class="py-3 px-4 text-emerald-300 font-bold font-mono">
                                        {{ number_format($tx->net_seller_proceeds) }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-400">
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
                    <div class="p-4 border-t border-[#3E2E4E]">
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
                <div class="p-5 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] space-y-4 shadow-sm">
                    <h3 class="text-sm font-bold text-white font-cinzel flex items-center gap-2 border-b border-[#3E2E4E] pb-3">
                        <i data-lucide="power" class="w-4 h-4 text-[#D4AF37]"></i>
                        Extension Activation &amp; Maintenance Status
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#140E1B] border border-[#352643]">
                            <div>
                                <label class="text-xs font-bold text-white block">Enable Marketplace Extension</label>
                                <span class="text-[11px] text-slate-400">Master switch to make the Player Marketplace accessible to registered players.</span>
                            </div>
                            <input type="checkbox" name="module_enabled_marketplace" value="1" {{ !empty($settings['enabled']) ? 'checked' : '' }} class="w-4 h-4 rounded border-[#352643] text-[#D4AF37] focus:ring-[#D4AF37]">
                        </div>

                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#140E1B] border border-[#352643]">
                            <div>
                                <label class="text-xs font-bold text-white block">Marketplace Maintenance Mode</label>
                                <span class="text-[11px] text-slate-400">Temporarily suspend listing creation and purchases while preserving all escrowed items.</span>
                            </div>
                            <input type="checkbox" name="marketplace_maintenance_mode" value="1" {{ !empty($settings['maintenance_mode']) ? 'checked' : '' }} class="w-4 h-4 rounded border-[#352643] text-[#D4AF37] focus:ring-[#D4AF37]">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <!-- Section 2: Operations, Limits & Announcements -->
                    <div class="p-5 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] space-y-4 shadow-sm">
                        <h3 class="text-sm font-bold text-white font-cinzel flex items-center gap-2 border-b border-[#3E2E4E] pb-3">
                            <i data-lucide="sliders" class="w-4 h-4 text-[#D4AF37]"></i>
                            Operations, Announcements &amp; Limits
                        </h3>

                        <div>
                            <label class="text-xs font-bold text-slate-300 block mb-1">Marketplace Announcement</label>
                            <input type="text" name="marketplace_announcement" value="{{ $settings['announcement'] }}" placeholder="e.g. Welcome to the Trade House! 5% tax is currently active." class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Displayed at the top of the Trade House interface</span>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-300 block mb-1">Trade House Rules &amp; Notice</label>
                            <textarea name="marketplace_rules" rows="3" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">{{ $settings['rules'] }}</textarea>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Official rules guidelines displayed in the rules modal for players</span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Max / Player</label>
                                <input type="number" name="marketplace_max_listings_player" value="{{ $settings['max_listings_player'] }}" min="1" max="100" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                                <span class="text-[10px] text-slate-400">Listings/Account</span>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Max / Character</label>
                                <input type="number" name="marketplace_max_listings_char" value="{{ $settings['max_listings_char'] }}" min="1" max="50" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                                <span class="text-[10px] text-slate-400">Listings/Char</span>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Max Items / Listing</label>
                                <input type="number" name="marketplace_max_items_per_listing" value="{{ $settings['max_items_per_listing'] ?? 1000 }}" min="1" max="30000" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                                <span class="text-[10px] text-slate-400">Max Stack</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Minimum Selling Price</label>
                                <input type="number" name="marketplace_min_price" value="{{ $settings['min_price'] }}" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Maximum Selling Price</label>
                                <input type="number" name="marketplace_max_price" value="{{ $settings['max_price'] }}" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 pt-2 border-t border-[#3E2E4E]">
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Min Duration</label>
                                <input type="number" name="marketplace_min_duration_hours" value="{{ $settings['min_duration_hours'] ?? 24 }}" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                                <span class="text-[10px] text-slate-400">Hours</span>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Default Duration</label>
                                <select name="marketplace_default_duration_hours" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                                    <option value="24" {{ $settings['default_duration_hours'] == 24 ? 'selected' : '' }}>24 Hours (1 Day)</option>
                                    <option value="72" {{ $settings['default_duration_hours'] == 72 ? 'selected' : '' }}>72 Hours (3 Days)</option>
                                    <option value="168" {{ $settings['default_duration_hours'] == 168 ? 'selected' : '' }}>168 Hours (7 Days)</option>
                                    <option value="336" {{ $settings['default_duration_hours'] == 336 ? 'selected' : '' }}>336 Hours (14 Days)</option>
                                    <option value="720" {{ $settings['default_duration_hours'] == 720 ? 'selected' : '' }}>720 Hours (30 Days)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Max Duration</label>
                                <input type="number" name="marketplace_max_duration_hours" value="{{ $settings['max_duration_hours'] ?? 720 }}" min="24" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                                <span class="text-[10px] text-slate-400">Hours</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Currencies, Taxes & Item Safety -->
                    <div class="p-5 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] space-y-4 shadow-sm">
                        <h3 class="text-sm font-bold text-white font-cinzel flex items-center gap-2 border-b border-[#3E2E4E] pb-3">
                            <i data-lucide="shield-check" class="w-4 h-4 text-[#D4AF37]"></i>
                            Currencies, Fees, Taxes &amp; Item Safety
                        </h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center justify-between p-3 rounded-xl bg-[#140E1B] border border-[#352643]">
                                <div>
                                    <label class="text-xs font-bold text-white block">Allow Zeny Payments</label>
                                    <span class="text-[10px] text-slate-400">Trade using character Zeny</span>
                                </div>
                                <input type="checkbox" name="marketplace_allow_zeny" value="1" {{ !empty($settings['allow_zeny']) ? 'checked' : '' }} class="w-4 h-4 rounded border-[#352643] text-[#D4AF37] focus:ring-[#D4AF37]">
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-[#140E1B] border border-[#352643]">
                                <div>
                                    <label class="text-xs font-bold text-white block">Allow Cash Points Payments</label>
                                    <span class="text-[10px] text-slate-400">Trade using account CP</span>
                                </div>
                                <input type="checkbox" name="marketplace_allow_cashpoints" value="1" {{ !empty($settings['allow_cashpoints']) ? 'checked' : '' }} class="w-4 h-4 rounded border-[#352643] text-[#D4AF37] focus:ring-[#D4AF37]">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Marketplace Sales Tax (%)</label>
                                <input type="number" step="0.1" name="marketplace_tax_percent" value="{{ $settings['tax_percent'] }}" min="0" max="50" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                                <span class="text-[10px] text-slate-400">Deducted automatically from seller proceeds</span>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Listing Insertion Fee (Zeny)</label>
                                <input type="number" name="marketplace_listing_fee" value="{{ $settings['listing_fee'] }}" min="0" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:outline-none focus:border-[#D4AF37]">
                                <span class="text-[10px] text-slate-400">Flat fee charged from character Zeny</span>
                            </div>
                        </div>

                        <!-- Item Safety Rules -->
                        <div class="space-y-2 pt-2 border-t border-[#3E2E4E]">
                            <p class="text-[11px] font-bold text-[#E5B358] uppercase tracking-wider font-cinzel">Item Safeguards &amp; Restriction Rules</p>

                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_withdrawals" value="1" {{ !empty($settings['allow_withdrawals']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Item Withdrawals
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_stackables" value="1" {{ !empty($settings['allow_stackables']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Stackable Items
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_equipment" value="1" {{ !empty($settings['allow_equipment']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Equipment
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_refined" value="1" {{ !empty($settings['allow_refined']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Refined Equipment
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_cards" value="1" {{ !empty($settings['allow_cards']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Cards &amp; Random Options
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_history_enabled" value="1" {{ !empty($settings['history_enabled']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Enable Marketplace History
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_bound" value="1" {{ !empty($settings['allow_bound']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Bound Items (Caution)
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_rental" value="1" {{ !empty($settings['allow_rental']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Rental Items (Caution)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-xs bg-[#D4AF37] hover:bg-[#FCE69B] text-black border border-[#FCE69B] shadow-md hover:brightness-110 transition-all flex items-center gap-2 cursor-pointer">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Save Marketplace Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
