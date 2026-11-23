@extends('layouts.app')

@section('title', 'Aether Trade House - Player Marketplace')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" x-data="{
    buyModalOpen: false,
    selectedListing: null,
    buyQuantity: 1,

    transferModalOpen: false,
    selectedStorageItem: null,
    transferAmount: 1,

    listModalOpen: false,
    selectedCustodyItem: null,
    listCurrency: 'zeny',
    listUnitPrice: 1000,
    listQuantity: 1,
    listDuration: {{ $settings['default_duration_hours'] }},

    taxPercent: {{ $settings['tax_percent'] }},

    openBuyModal(listing) {
        this.selectedListing = listing;
        this.buyQuantity = 1;
        this.buyModalOpen = true;
    },
    openTransferModal(item) {
        this.selectedStorageItem = item;
        this.transferAmount = 1;
        this.transferModalOpen = true;
    },
    openSellModal(item) {
        this.selectedCustodyItem = item;
        this.listQuantity = 1;
        this.listUnitPrice = 1000;
        this.listCurrency = 'zeny';
        this.listModalOpen = true;
    }
}">

    <!-- 1. TRADE HOUSE HEADER BOARD -->
    <div class="rounded-2xl p-5 relative overflow-hidden shadow-2xl" style="background: linear-gradient(180deg, #241A2E 0%, #17111F 100%); border: 2px solid #806442; box-shadow: 0 10px 30px rgba(0,0,0,0.8), inset 0 0 15px rgba(182,154,101,0.15);">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <!-- Title & Atmosphere -->
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center shrink-0 shadow-lg" style="background: radial-gradient(circle, #E5B358 0%, #806442 100%); border: 2px solid #FCE69B; color: #17111F;">
                    <i data-lucide="scale" class="w-8 h-8"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#B69A65] font-cinzel">Rune-Midgard Exchange</span>
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold font-mono uppercase bg-[#140E1B] text-[#D4AF37] border border-[#806442]">Secure Escrow</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black font-cinzel text-[#FCE69B] tracking-wider uppercase drop-shadow">
                        Aether Trade House
                    </h1>
                    <p class="text-xs text-[#C8BDAA] mt-0.5">Player-to-player consignment exchange for in-game storage items</p>
                </div>
            </div>

            <!-- Active Adventurer & Currency Vault Card -->
            <div class="flex flex-wrap items-center gap-4 p-3.5 rounded-xl" style="background: #140E1B; border: 1px solid #4A3A5A; box-shadow: inset 0 2px 8px rgba(0,0,0,0.6);">
                <!-- Character Dropdown -->
                <form action="{{ route('marketplace.character') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <div class="flex flex-col">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-[#A69A8A] font-cinzel">Active Adventurer</span>
                        <select name="char_id" onchange="this.form.submit()" class="bg-[#241A2F] text-[#EDE4D2] text-xs font-bold rounded-lg border border-[#645077] px-3 py-1.5 focus:border-[#D4AF37] cursor-pointer hover:bg-[#2C2039] transition-colors">
                            @foreach($characters as $c)
                                <option value="{{ $c->char_id }}" {{ $activeChar->char_id == $c->char_id ? 'selected' : '' }}>
                                    {{ $c->name }} (Lv. {{ $c->base_level }}/{{ $c->job_level }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="h-8 w-px bg-[#3E2E4E] hidden sm:block"></div>

                <!-- Live Zeny -->
                <div class="flex flex-col">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-[#A69A8A] font-cinzel">Character Zeny</span>
                    <div class="flex items-center gap-1.5 text-sm font-bold text-amber-300 font-mono">
                        <i data-lucide="coins" class="w-4 h-4 text-amber-400"></i>
                        <span>{{ number_format($characterZeny) }}</span>
                        <span class="text-[10px] text-amber-400/80 font-sans">Z</span>
                    </div>
                </div>

                <div class="h-8 w-px bg-[#3E2E4E] hidden sm:block"></div>

                <!-- Live Cash Points -->
                <div class="flex flex-col">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-[#A69A8A] font-cinzel">Account Cash Points</span>
                    <div class="flex items-center gap-1.5 text-sm font-bold text-purple-300 font-mono">
                        <i data-lucide="gem" class="w-4 h-4 text-purple-400"></i>
                        <span>{{ number_format($accountCashPoints) }}</span>
                        <span class="text-[10px] text-purple-400/80 font-sans">CP</span>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($settings['announcement']))
            <div class="mt-4 pt-3 border-t border-[#3E2E4E] text-xs flex items-center gap-2.5 text-[#EDE4D2]">
                <i data-lucide="bell" class="w-4 h-4 text-[#D4AF37] shrink-0"></i>
                <span class="text-[#D8CDBC] italic">{{ $settings['announcement'] }}</span>
            </div>
        @endif
    </div>

    <!-- Alert Banners -->
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

    <!-- 2. MEDIEVAL STONE NAVIGATION TABS -->
    <div class="flex items-center gap-2 border-b-2 border-[#3E2E4E] pb-2 overflow-x-auto">
        <a href="{{ route('marketplace.index', ['tab' => 'browse']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs font-bold font-cinzel transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'browse' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/60 shadow-md' : 'text-[#C8BDAA] hover:text-white bg-[#1C1525] border border-transparent' }}">
            <i data-lucide="search" class="w-4 h-4 text-[#D4AF37]"></i>
            Browse Market
        </a>

        <a href="{{ route('marketplace.index', ['tab' => 'storage']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs font-bold font-cinzel transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'storage' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/60 shadow-md' : 'text-[#C8BDAA] hover:text-white bg-[#1C1525] border border-transparent' }}">
            <i data-lucide="box" class="w-4 h-4 text-[#D4AF37]"></i>
            Kafra Storage (Deposit)
        </a>

        <a href="{{ route('marketplace.index', ['tab' => 'custody']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs font-bold font-cinzel transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'custody' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/60 shadow-md' : 'text-[#C8BDAA] hover:text-white bg-[#1C1525] border border-transparent' }}">
            <i data-lucide="package-check" class="w-4 h-4 text-[#D4AF37]"></i>
            My Custody &amp; Sell
        </a>

        <a href="{{ route('marketplace.index', ['tab' => 'my_listings']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs font-bold font-cinzel transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'my_listings' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/60 shadow-md' : 'text-[#C8BDAA] hover:text-white bg-[#1C1525] border border-transparent' }}">
            <i data-lucide="tag" class="w-4 h-4 text-[#D4AF37]"></i>
            My Active Listings
        </a>

        <a href="{{ route('marketplace.index', ['tab' => 'history']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs font-bold font-cinzel transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'history' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/60 shadow-md' : 'text-[#C8BDAA] hover:text-white bg-[#1C1525] border border-transparent' }}">
            <i data-lucide="scroll" class="w-4 h-4 text-[#D4AF37]"></i>
            Trade Ledger &amp; History
        </a>
    </div>

    <!-- ==================== TAB 1: BROWSE MARKET ==================== -->
    @if($tab === 'browse')
        <div class="space-y-4">
            <!-- Filter Toolbar -->
            <div class="p-4 rounded-2xl" style="background: #1C1525; border: 1px solid #3E2E4E;">
                <form action="{{ route('marketplace.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                    <input type="hidden" name="tab" value="browse">

                    <!-- Search Input -->
                    <div class="lg:col-span-2">
                        <label class="text-[10px] font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Search Item Name or ID</label>
                        <div class="relative">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="e.g. Blade, Tsurugi, Poring Card..." class="w-full pl-9 pr-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-[#EDE4D2] focus:border-[#D4AF37]">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="text-[10px] font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Category</label>
                        <select name="type" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-[#EDE4D2] focus:border-[#D4AF37]">
                            <option value="all">All Categories</option>
                            <option value="Weapon" {{ request('type') === 'Weapon' ? 'selected' : '' }}>Weapons</option>
                            <option value="Armor" {{ request('type') === 'Armor' ? 'selected' : '' }}>Armor &amp; Shields</option>
                            <option value="Card" {{ request('type') === 'Card' ? 'selected' : '' }}>Cards</option>
                            <option value="Consumable" {{ request('type') === 'Consumable' ? 'selected' : '' }}>Consumables</option>
                            <option value="Etc" {{ request('type') === 'Etc' ? 'selected' : '' }}>Materials &amp; Etc</option>
                        </select>
                    </div>

                    <!-- Currency Filter -->
                    <div>
                        <label class="text-[10px] font-bold text-[#E5B358] uppercase tracking-wider block mb-1 font-cinzel">Currency</label>
                        <select name="currency" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-[#EDE4D2] focus:border-[#D4AF37]">
                            <option value="all">Any Currency</option>
                            <option value="zeny" {{ request('currency') === 'zeny' ? 'selected' : '' }}>Zeny (Z)</option>
                            <option value="cashpoints" {{ request('currency') === 'cashpoints' ? 'selected' : '' }}>Cash Points (CP)</option>
                        </select>
                    </div>

                    <!-- Action Button -->
                    <div>
                        <button type="submit" class="w-full py-2 px-4 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i data-lucide="filter" class="w-4 h-4"></i>
                            Filter Market
                        </button>
                    </div>
                </form>
            </div>

            <!-- Items Table -->
            <div class="rounded-2xl overflow-hidden shadow-2xl" style="background: #1C1525; border: 2px solid #3E2E4E;">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB] font-cinzel">
                                <th class="py-3.5 px-4">Item Description</th>
                                <th class="py-3.5 px-4">Seller Adventurer</th>
                                <th class="py-3.5 px-4">Stock</th>
                                <th class="py-3.5 px-4">Price / Unit</th>
                                <th class="py-3.5 px-4">Total Buyout</th>
                                <th class="py-3.5 px-4">Time Left</th>
                                <th class="py-3.5 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            @forelse($browseListings as $listing)
                                <tr class="hover:bg-[#251A33]/70 transition-colors">
                                    <!-- Item Description -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="relative shrink-0">
                                                <img src="{{ $listing->item->icon_url }}" alt="Item" class="w-9 h-9 object-contain rounded-lg bg-[#140E1B] border border-[#4A3A5A] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                                @if($listing->item->refine > 0)
                                                    <span class="absolute -top-1.5 -right-1.5 px-1 py-0.2 rounded text-[9px] font-bold bg-[#D4AF37] text-black shadow">
                                                        +{{ $listing->item->refine }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-white text-sm font-cinzel">{{ $listing->item->formatted_name }}</h4>
                                                <p class="text-[10px] text-slate-400">ID #{{ $listing->item->nameid }} • {{ $listing->item->item_type }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Seller -->
                                    <td class="py-3.5 px-4">
                                        <span class="font-medium text-[#EDE4D2]">{{ $listing->seller_char_name }}</span>
                                    </td>

                                    <!-- Stock -->
                                    <td class="py-3.5 px-4 font-mono font-bold text-white">
                                        {{ number_format($listing->remaining_quantity) }}
                                    </td>

                                    <!-- Unit Price -->
                                    <td class="py-3.5 px-4">
                                        @if($listing->currency_type === 'zeny')
                                            <span class="inline-flex items-center gap-1 font-bold text-amber-300 font-mono">
                                                <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-400"></i>
                                                {{ number_format($listing->unit_price) }} Z
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 font-bold text-purple-300 font-mono">
                                                <i data-lucide="gem" class="w-3.5 h-3.5 text-purple-400"></i>
                                                {{ number_format($listing->unit_price) }} CP
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Total Buyout -->
                                    <td class="py-3.5 px-4 font-mono font-bold text-white">
                                        {{ number_format($listing->unit_price * $listing->remaining_quantity) }}
                                    </td>

                                    <!-- Time Left -->
                                    <td class="py-3.5 px-4 text-slate-400">
                                        {{ $listing->remaining_time }}
                                    </td>

                                    <!-- Buy Action -->
                                    <td class="py-3.5 px-4 text-right">
                                        @if($listing->seller_user_id === auth()->id())
                                            <span class="text-slate-500 text-[11px] italic font-cinzel">Your Listing</span>
                                        @else
                                            <button @click="openBuyModal({{ json_encode($listing) }})" 
                                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110 transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                                Buy Item
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                        No active listings are currently posted in the Trade House.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($browseListings->hasPages())
                    <div class="p-4 border-t border-[#3E2E4E]">
                        {{ $browseListings->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ==================== TAB 2: DEPOSIT FROM STORAGE ==================== -->
    @if($tab === 'storage')
        <div class="space-y-4">
            <div class="p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3" style="background: #1C1525; border: 1px solid #3E2E4E;">
                <div>
                    <h3 class="text-sm font-bold text-white font-cinzel">In-Game Kafra Storage Inventory</h3>
                    <p class="text-xs text-[#C8BDAA]">Select an item from your personal Kafra storage to move into Trade House escrow custody.</p>
                </div>
                <div class="text-[11px] text-[#E5B358] bg-[#140E1B] px-3 py-1.5 rounded-lg border border-[#4A3A5A] shrink-0">
                    <i data-lucide="shield-alert" class="w-3.5 h-3.5 inline mr-1 text-[#D4AF37]"></i> Character must be logged out of game client
                </div>
            </div>

            <div class="rounded-2xl overflow-hidden shadow-2xl" style="background: #1C1525; border: 2px solid #3E2E4E;">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB] font-cinzel">
                                <th class="py-3.5 px-4">Item</th>
                                <th class="py-3.5 px-4">Storage Stock</th>
                                <th class="py-3.5 px-4">Category</th>
                                <th class="py-3.5 px-4">Trade Status</th>
                                <th class="py-3.5 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            @forelse($storageItems as $sItem)
                                <tr class="hover:bg-[#251A33]/70 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $sItem['icon_url'] }}" alt="Item" class="w-8 h-8 object-contain rounded bg-[#140E1B] border border-[#4A3A5A] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <p class="font-bold text-white font-cinzel">
                                                    {{ $sItem['refine'] > 0 ? '+'.$sItem['refine'].' ' : '' }}{{ $sItem['name'] }}
                                                </p>
                                                <p class="text-[10px] text-slate-400">ID #{{ $sItem['nameid'] }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-white font-mono">
                                        {{ number_format($sItem['amount']) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-300">
                                        {{ $sItem['type'] }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($sItem['is_transferable'])
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Transferable</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30" title="{{ $sItem['restriction_reason'] }}">Restricted</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        @if($sItem['is_transferable'])
                                            <button @click="openTransferModal({{ json_encode($sItem) }})" 
                                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold font-cinzel bg-[#D4AF37]/20 text-[#FCE69B] hover:bg-[#D4AF37]/30 border border-[#D4AF37]/40 transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                                <i data-lucide="arrow-right-circle" class="w-3.5 h-3.5"></i>
                                                Deposit Item
                                            </button>
                                        @else
                                            <span class="text-slate-500 text-[11px] italic">{{ $sItem['restriction_reason'] ?? 'Not tradeable' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                        Your Kafra storage currently contains no items.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== TAB 3: CUSTODY INVENTORY & SELL ==================== -->
    @if($tab === 'custody')
        <div class="space-y-4">
            <div class="p-4 rounded-2xl flex items-center justify-between" style="background: #1C1525; border: 1px solid #3E2E4E;">
                <div>
                    <h3 class="text-sm font-bold text-white font-cinzel">Trade House Custody Vault</h3>
                    <p class="text-xs text-[#C8BDAA]">Items safely escrowed in the Trade House. You may list them for sale or withdraw them back to Kafra storage.</p>
                </div>
            </div>

            <div class="rounded-2xl overflow-hidden shadow-2xl" style="background: #1C1525; border: 2px solid #3E2E4E;">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB] font-cinzel">
                                <th class="py-3.5 px-4">Item</th>
                                <th class="py-3.5 px-4">Vault Quantity</th>
                                <th class="py-3.5 px-4">Category</th>
                                <th class="py-3.5 px-4">Deposited Date</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            @forelse($custodyItems as $cItem)
                                <tr class="hover:bg-[#251A33]/70 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $cItem->icon_url }}" alt="Item" class="w-8 h-8 object-contain rounded bg-[#140E1B] border border-[#4A3A5A] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <p class="font-bold text-white font-cinzel">{{ $cItem->formatted_name }}</p>
                                                <p class="text-[10px] text-slate-400">ID #{{ $cItem->nameid }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-white font-mono">
                                        {{ number_format($cItem->amount) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-300">
                                        {{ $cItem->item_type }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-400">
                                        {{ $cItem->deposited_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right space-x-2">
                                        <button @click="openSellModal({{ json_encode($cItem) }})" 
                                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110 transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                            <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                                            List for Sale
                                        </button>

                                        <form action="{{ route('marketplace.withdraw', $cItem->id) }}" method="POST" class="inline" onsubmit="return confirm('Withdraw this item back to your in-game Kafra storage?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold font-cinzel bg-[#251A33] text-slate-300 hover:text-white hover:bg-[#352643] border border-[#4A3A5A] transition-all inline-flex items-center gap-1 cursor-pointer">
                                                <i data-lucide="arrow-left-circle" class="w-3.5 h-3.5"></i>
                                                Withdraw
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                        You currently have no items deposited in Trade House custody.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== TAB 4: MY ACTIVE LISTINGS ==================== -->
    @if($tab === 'my_listings')
        <div class="space-y-4">
            <div class="p-4 rounded-2xl flex items-center justify-between" style="background: #1C1525; border: 1px solid #3E2E4E;">
                <div>
                    <h3 class="text-sm font-bold text-white font-cinzel">My Active Consignments</h3>
                    <p class="text-xs text-[#C8BDAA]">Track your current items listed on the Trade House, monitor countdowns, and cancel unsold listings.</p>
                </div>
            </div>

            <div class="rounded-2xl overflow-hidden shadow-2xl" style="background: #1C1525; border: 2px solid #3E2E4E;">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB] font-cinzel">
                                <th class="py-3.5 px-4">Code</th>
                                <th class="py-3.5 px-4">Item</th>
                                <th class="py-3.5 px-4">Price / Unit</th>
                                <th class="py-3.5 px-4">Remaining</th>
                                <th class="py-3.5 px-4">Expires In</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            @forelse($myListings as $mListing)
                                <tr class="hover:bg-[#251A33]/70 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-[#E5B358]">{{ $mListing->listing_code }}</td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $mListing->item->icon_url }}" alt="Item" class="w-8 h-8 object-contain rounded bg-[#140E1B] border border-[#4A3A5A] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <p class="font-bold text-white font-cinzel">{{ $mListing->item->formatted_name }}</p>
                                                <p class="text-[10px] text-slate-400">ID #{{ $mListing->item->nameid }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold">
                                        @if($mListing->currency_type === 'zeny')
                                            <span class="inline-flex items-center gap-1 text-amber-300">
                                                <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-400"></i>
                                                {{ number_format($mListing->unit_price) }} Z
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-purple-300">
                                                <i data-lucide="gem" class="w-3.5 h-3.5 text-purple-400"></i>
                                                {{ number_format($mListing->unit_price) }} CP
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-white font-mono">
                                        {{ $mListing->remaining_quantity }} / {{ $mListing->quantity }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-400">
                                        {{ $mListing->remaining_time }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($mListing->status === 'active')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Active</span>
                                        @elseif($mListing->status === 'sold')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">Sold</span>
                                        @elseif($mListing->status === 'cancelled')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Cancelled</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-500/20 text-slate-300 border border-slate-500/30">Expired</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        @if($mListing->status === 'active')
                                            <form action="{{ route('marketplace.cancel', $mListing->id) }}" method="POST" onsubmit="return confirm('Cancel this consignment and return the item to your custody inventory?');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 rounded-lg text-[11px] font-bold bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 border border-rose-500/40 transition-all cursor-pointer">
                                                    Cancel Listing
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-500 text-[11px] italic font-cinzel">Closed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                        You currently have no active listings in the Trade House.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($myListings->hasPages())
                    <div class="p-4 border-t border-[#3E2E4E]">
                        {{ $myListings->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ==================== TAB 5: TRADE HISTORY ==================== -->
    @if($tab === 'history')
        <div class="space-y-4">
            <div class="p-4 rounded-2xl flex items-center justify-between" style="background: #1C1525; border: 1px solid #3E2E4E;">
                <div>
                    <h3 class="text-sm font-bold text-white font-cinzel">Transaction Ledger &amp; Audit Trail</h3>
                    <p class="text-xs text-[#C8BDAA]">Complete immutable history of your purchases and sales completed in the Trade House.</p>
                </div>
            </div>

            <div class="rounded-2xl overflow-hidden shadow-2xl" style="background: #1C1525; border: 2px solid #3E2E4E;">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB] font-cinzel">
                                <th class="py-3.5 px-4">TX Code</th>
                                <th class="py-3.5 px-4">Item</th>
                                <th class="py-3.5 px-4">Role</th>
                                <th class="py-3.5 px-4">Counterparty</th>
                                <th class="py-3.5 px-4">Gross Total</th>
                                <th class="py-3.5 px-4">Tax Fee</th>
                                <th class="py-3.5 px-4">Net Total</th>
                                <th class="py-3.5 px-4">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            @forelse($history as $tx)
                                @php
                                    $isBuyer = ($tx->buyer_user_id === auth()->id());
                                @endphp
                                <tr class="hover:bg-[#251A33]/70 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-[#E5B358]">{{ $tx->transaction_code }}</td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <img src="{{ $tx->item->icon_url }}" alt="Item" class="w-7 h-7 object-contain rounded bg-[#140E1B] border border-[#4A3A5A] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <span class="font-bold text-white font-cinzel">{{ $tx->quantity }}x {{ $tx->item->formatted_name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($isBuyer)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">Buyer</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">Seller</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-[#EDE4D2]">
                                        {{ $isBuyer ? $tx->seller_char_name : $tx->buyer_char_name }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold font-mono {{ $tx->currency_type === 'zeny' ? 'text-amber-300' : 'text-purple-300' }}">
                                        {{ number_format($tx->gross_amount) }} {{ $tx->currency_type === 'zeny' ? 'Z' : 'CP' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-rose-300 font-mono">
                                        -{{ number_format($tx->tax_amount) }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-emerald-300 font-mono">
                                        {{ number_format($isBuyer ? $tx->gross_amount : $tx->net_seller_proceeds) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-400">
                                        {{ $tx->created_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                        No transactions have been recorded under your account yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($history->hasPages())
                    <div class="p-4 border-t border-[#3E2E4E]">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ==================== MODAL 1: BUY ITEM ==================== -->
    <div x-show="buyModalOpen" 
         x-transition.opacity.duration.200ms
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div @click.away="buyModalOpen = false" 
             class="bg-[#1C1525] border-2 border-[#D4AF37] rounded-2xl shadow-2xl p-6 max-w-md w-full space-y-4">
            <div class="flex items-center justify-between border-b border-[#3E2E4E] pb-3">
                <h3 class="text-base font-bold font-cinzel text-[#FCE69B] flex items-center gap-2">
                    <i data-lucide="shopping-cart" class="w-5 h-5 text-[#D4AF37]"></i>
                    Confirm Trade House Purchase
                </h3>
                <button @click="buyModalOpen = false" class="text-slate-400 hover:text-white cursor-pointer text-lg">&times;</button>
            </div>

            <template x-if="selectedListing">
                <form :action="'/marketplace/buy/' + selectedListing.id" method="POST" class="space-y-4">
                    @csrf
                    <!-- Item Overview -->
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-[#140E1B] border border-[#352643]">
                        <img :src="selectedListing.item.icon_url" class="w-10 h-10 object-contain rounded bg-[#251A33] p-1 border border-[#4A3A5A]">
                        <div>
                            <h4 class="font-bold text-white font-cinzel" x-text="selectedListing.item.formatted_name"></h4>
                            <p class="text-[11px] text-slate-400">Seller: <span class="text-[#E5B358] font-bold" x-text="selectedListing.seller_char_name"></span></p>
                        </div>
                    </div>

                    <!-- Quantity Input -->
                    <div>
                        <label class="text-xs font-bold text-[#E5B358] block mb-1 font-cinzel">Purchase Quantity</label>
                        <input type="number" name="quantity" x-model.number="buyQuantity" min="1" :max="selectedListing.remaining_quantity" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:border-[#D4AF37]">
                        <span class="text-[10px] text-slate-400 mt-1 block" x-text="'Available in listing: ' + selectedListing.remaining_quantity"></span>
                    </div>

                    <!-- Price Calculations -->
                    <div class="p-3.5 rounded-xl bg-[#140E1B] border border-[#352643] space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Price Per Unit:</span>
                            <span class="font-bold font-mono text-white" x-text="new Intl.NumberFormat().format(selectedListing.unit_price) + ' ' + (selectedListing.currency_type === 'zeny' ? 'Zeny' : 'Cash Points')"></span>
                        </div>
                        <div class="flex justify-between text-[#E5B358] font-bold text-sm pt-1.5 border-t border-[#352643]">
                            <span>Total Buyout Price:</span>
                            <span class="font-mono text-base" x-text="new Intl.NumberFormat().format(selectedListing.unit_price * buyQuantity) + ' ' + (selectedListing.currency_type === 'zeny' ? 'Z' : 'CP')"></span>
                        </div>
                    </div>

                    <div class="text-[11px] text-emerald-400/90 flex items-center gap-1.5 bg-emerald-500/10 p-2.5 rounded-lg border border-emerald-500/20">
                        <i data-lucide="box" class="w-4 h-4 shrink-0"></i>
                        <span>Purchased items are automatically delivered to your Kafra Storage.</span>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="buyModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#251A33] text-slate-300 hover:text-white border border-[#4A3A5A] cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110 cursor-pointer">Confirm Buyout</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- ==================== MODAL 2: DEPOSIT TO CUSTODY ==================== -->
    <div x-show="transferModalOpen" 
         x-transition.opacity.duration.200ms
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div @click.away="transferModalOpen = false" 
             class="bg-[#1C1525] border-2 border-[#D4AF37] rounded-2xl shadow-2xl p-6 max-w-md w-full space-y-4">
            <div class="flex items-center justify-between border-b border-[#3E2E4E] pb-3">
                <h3 class="text-base font-bold font-cinzel text-[#FCE69B] flex items-center gap-2">
                    <i data-lucide="arrow-right-circle" class="w-5 h-5 text-[#D4AF37]"></i>
                    Deposit to Trade House Escrow
                </h3>
                <button @click="transferModalOpen = false" class="text-slate-400 hover:text-white cursor-pointer text-lg">&times;</button>
            </div>

            <template x-if="selectedStorageItem">
                <form action="{{ route('marketplace.transfer') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="storage_id" :value="selectedStorageItem.storage_id">

                    <div class="flex items-center gap-3 p-3 rounded-xl bg-[#140E1B] border border-[#352643]">
                        <img :src="selectedStorageItem.icon_url" class="w-10 h-10 object-contain rounded bg-[#251A33] p-1 border border-[#4A3A5A]">
                        <div>
                            <h4 class="font-bold text-white font-cinzel" x-text="selectedStorageItem.name"></h4>
                            <p class="text-[11px] text-slate-400" x-text="'Category: ' + selectedStorageItem.type"></p>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-[#E5B358] block mb-1 font-cinzel">Transfer Quantity</label>
                        <input type="number" name="amount" x-model.number="transferAmount" min="1" :max="selectedStorageItem.amount" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:border-[#D4AF37]">
                        <span class="text-[10px] text-slate-400 mt-1 block" x-text="'Available in storage: ' + selectedStorageItem.amount"></span>
                    </div>

                    <div class="text-[11px] text-amber-300/90 flex items-center gap-1.5 bg-amber-500/10 p-2.5 rounded-lg border border-amber-500/20">
                        <i data-lucide="shield-check" class="w-4 h-4 shrink-0"></i>
                        <span>This item will be safely held in escrow and can be listed or withdrawn anytime.</span>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="transferModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#251A33] text-slate-300 hover:text-white border border-[#4A3A5A] cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110 cursor-pointer">Transfer Item</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- ==================== MODAL 3: CREATE LISTING ==================== -->
    <div x-show="listModalOpen" 
         x-transition.opacity.duration.200ms
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;">
        <div @click.away="listModalOpen = false" 
             class="bg-[#1C1525] border-2 border-[#D4AF37] rounded-2xl shadow-2xl p-6 max-w-md w-full space-y-4">
            <div class="flex items-center justify-between border-b border-[#3E2E4E] pb-3">
                <h3 class="text-base font-bold font-cinzel text-[#FCE69B] flex items-center gap-2">
                    <i data-lucide="tag" class="w-5 h-5 text-[#D4AF37]"></i>
                    Create Marketplace Listing
                </h3>
                <button @click="listModalOpen = false" class="text-slate-400 hover:text-white cursor-pointer text-lg">&times;</button>
            </div>

            <template x-if="selectedCustodyItem">
                <form action="{{ route('marketplace.list') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="item_id" :value="selectedCustodyItem.id">

                    <div class="flex items-center gap-3 p-3 rounded-xl bg-[#140E1B] border border-[#352643]">
                        <img :src="selectedCustodyItem.icon_url" class="w-10 h-10 object-contain rounded bg-[#251A33] p-1 border border-[#4A3A5A]">
                        <div>
                            <h4 class="font-bold text-white font-cinzel" x-text="selectedCustodyItem.item_name"></h4>
                            <p class="text-[11px] text-slate-400" x-text="'Available in vault: ' + selectedCustodyItem.amount"></p>
                        </div>
                    </div>

                    <!-- Currency Selector -->
                    <div>
                        <label class="text-xs font-bold text-[#E5B358] block mb-1 font-cinzel">Selling Currency</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-colors" :class="listCurrency === 'zeny' ? 'bg-amber-500/20 border-amber-500/60 text-amber-300 font-bold' : 'bg-[#140E1B] border-[#4A3A5A] text-slate-400'">
                                <input type="radio" name="currency_type" value="zeny" x-model="listCurrency" class="hidden">
                                <i data-lucide="coins" class="w-4 h-4"></i>
                                <span>Zeny</span>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-colors" :class="listCurrency === 'cashpoints' ? 'bg-purple-500/20 border-purple-500/60 text-purple-300 font-bold' : 'bg-[#140E1B] border-[#4A3A5A] text-slate-400'">
                                <input type="radio" name="currency_type" value="cashpoints" x-model="listCurrency" class="hidden">
                                <i data-lucide="gem" class="w-4 h-4"></i>
                                <span>Cash Points</span>
                            </label>
                        </div>
                    </div>

                    <!-- Quantity & Price -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-[#E5B358] block mb-1 font-cinzel">Quantity</label>
                            <input type="number" name="quantity" x-model.number="listQuantity" min="1" :max="selectedCustodyItem.amount" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:border-[#D4AF37]">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-[#E5B358] block mb-1 font-cinzel">Price / Unit</label>
                            <input type="number" name="unit_price" x-model.number="listUnitPrice" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:border-[#D4AF37]">
                        </div>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="text-xs font-bold text-[#E5B358] block mb-1 font-cinzel">Listing Duration</label>
                        <select name="duration_hours" x-model.number="listDuration" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white focus:border-[#D4AF37]">
                            <option value="24">24 Hours (1 Day)</option>
                            <option value="72">72 Hours (3 Days)</option>
                            <option value="168">168 Hours (7 Days)</option>
                            <option value="336">336 Hours (14 Days)</option>
                            <option value="720">720 Hours (30 Days)</option>
                        </select>
                    </div>

                    <!-- Proceeds Calculation Preview -->
                    <div class="p-3.5 rounded-xl bg-[#140E1B] border border-[#352643] space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Gross Sale Price:</span>
                            <span class="font-mono text-white" x-text="new Intl.NumberFormat().format(listUnitPrice * listQuantity)"></span>
                        </div>
                        <div class="flex justify-between text-rose-400">
                            <span x-text="'Sales Tax (' + taxPercent + '%):'"></span>
                            <span class="font-mono" x-text="'-' + new Intl.NumberFormat().format(Math.round((listUnitPrice * listQuantity * taxPercent) / 100))"></span>
                        </div>
                        <div class="flex justify-between text-emerald-300 font-bold pt-1.5 border-t border-[#352643]">
                            <span>Net Proceeds on Sale:</span>
                            <span class="font-mono text-sm" x-text="new Intl.NumberFormat().format(Math.round((listUnitPrice * listQuantity) - ((listUnitPrice * listQuantity * taxPercent) / 100))) + ' ' + (listCurrency === 'zeny' ? 'Z' : 'CP')"></span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="listModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#251A33] text-slate-300 hover:text-white border border-[#4A3A5A] cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110 cursor-pointer">Post Consignment</button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection
