@extends('layouts.app')

@section('title', 'Player Marketplace & Trade House')

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

    <!-- 1. Header & Active Character Summary Card -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <!-- Title & Bio -->
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-500 flex items-center justify-center shrink-0 shadow-lg text-slate-950 font-bold">
                    <i data-lucide="scale" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-cyan-400 uppercase tracking-wider">Player Exchange</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-slate-800 text-slate-300 border border-slate-700">Verified Escrow</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold aether-gradient-text tracking-tight mt-0.5">
                        Player Marketplace
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">Secure consignment trading for in-game storage items and currency</p>
                </div>
            </div>

            <!-- Active Character & Currency Info -->
            <div class="flex flex-wrap items-center gap-4 p-3.5 rounded-xl bg-slate-950/80 border border-slate-800 shadow-inner">
                <!-- Character Dropdown -->
                <form action="{{ route('marketplace.character') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <div class="flex flex-col">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Trading Character</span>
                        <select name="char_id" onchange="this.form.submit()" class="bg-slate-900 text-slate-200 text-xs font-semibold rounded-lg border border-slate-700 px-3 py-1.5 focus:border-cyan-500 cursor-pointer hover:bg-slate-800 transition-colors">
                            @foreach($characters as $c)
                                <option value="{{ $c->char_id }}" {{ $activeChar->char_id == $c->char_id ? 'selected' : '' }}>
                                    {{ $c->name }} (Lv. {{ $c->base_level }}/{{ $c->job_level }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="h-8 w-px bg-slate-800 hidden sm:block"></div>

                <!-- Live Zeny -->
                <div class="flex flex-col">
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Character Zeny</span>
                    <div class="flex items-center gap-1.5 text-sm font-bold text-amber-300 font-mono">
                        <i data-lucide="coins" class="w-4 h-4 text-amber-400"></i>
                        <span>{{ number_format($characterZeny) }}</span>
                        <span class="text-[10px] text-amber-400/80">Z</span>
                    </div>
                </div>

                <div class="h-8 w-px bg-slate-800 hidden sm:block"></div>

                <!-- Live Cash Points -->
                <div class="flex flex-col">
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Cash Points</span>
                    <div class="flex items-center gap-1.5 text-sm font-bold text-purple-300 font-mono">
                        <i data-lucide="gem" class="w-4 h-4 text-purple-400"></i>
                        <span>{{ number_format($accountCashPoints) }}</span>
                        <span class="text-[10px] text-purple-400/80">CP</span>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($settings['announcement']))
            <div class="mt-4 pt-3 border-t border-slate-800 text-xs flex items-center gap-2.5 text-slate-300">
                <i data-lucide="bell" class="w-4 h-4 text-cyan-400 shrink-0"></i>
                <span class="italic text-slate-300">{{ $settings['announcement'] }}</span>
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

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 text-rose-400"></i>
            <div>
                <span class="font-bold block">Validation Error:</span>
                <ul class="list-disc list-inside mt-0.5 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- 2. Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 pb-2 overflow-x-auto">
        <a href="{{ route('marketplace.index', ['tab' => 'browse']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'browse' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900/60 border border-slate-800' }}">
            <i data-lucide="search" class="w-4 h-4 text-cyan-400"></i>
            <span>Browse Market</span>
        </a>

        <a href="{{ route('marketplace.index', ['tab' => 'storage']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'storage' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900/60 border border-slate-800' }}">
            <i data-lucide="box" class="w-4 h-4 text-cyan-400"></i>
            <span>Kafra Storage (Deposit)</span>
        </a>

        <a href="{{ route('marketplace.index', ['tab' => 'custody']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'custody' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900/60 border border-slate-800' }}">
            <i data-lucide="package-check" class="w-4 h-4 text-cyan-400"></i>
            <span>My Custody &amp; Sell</span>
        </a>

        <a href="{{ route('marketplace.index', ['tab' => 'my_listings']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'my_listings' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900/60 border border-slate-800' }}">
            <i data-lucide="tag" class="w-4 h-4 text-cyan-400"></i>
            <span>My Active Listings</span>
        </a>

        <a href="{{ route('marketplace.index', ['tab' => 'history']) }}" 
           class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'history' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900/60 border border-slate-800' }}">
            <i data-lucide="scroll" class="w-4 h-4 text-cyan-400"></i>
            <span>Trade Ledger &amp; History</span>
        </a>
    </div>

    <!-- ==================== TAB 1: BROWSE MARKET ==================== -->
    @if($tab === 'browse')
        @php $listings = $listings ?? $browseListings ?? collect(); @endphp
        <div class="space-y-4">
            <!-- Search & Filter Console -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-lg">
                <form action="{{ route('marketplace.index') }}" method="GET" class="space-y-3">
                    <input type="hidden" name="tab" value="browse">

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <div class="relative flex-1" style="position: relative; display: flex; align-items: center;">
                            <div class="search-icon-centered" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); -webkit-transform: translateY(-50%); display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10; width: 16px; height: 16px; line-height: 0;">
                                <i data-lucide="search" class="w-4 h-4 text-cyan-400" style="width: 16px; height: 16px; display: block;"></i>
                            </div>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by item name or ID (e.g. Blade, Tsurugi, Poring Card)..." 
                                   style="width: 100%; padding-left: 48px !important; padding-right: 1.25rem !important; height: 44px; display: block;"
                                   class="bg-slate-950 border border-slate-800 rounded-xl text-xs font-medium text-slate-100 placeholder:text-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all shadow-inner">
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 cursor-pointer flex items-center gap-1.5 shadow-md shadow-cyan-500/20">
                                <i data-lucide="search" class="w-4 h-4"></i>
                                <span>Search Market</span>
                            </button>
                            @if(request('q') || (request('type') && request('type') !== 'all') || (request('currency') && request('currency') !== 'all') || request('sort'))
                                <a href="{{ route('marketplace.index', ['tab' => 'browse']) }}" class="px-3.5 py-2.5 rounded-xl text-xs font-medium bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 cursor-pointer flex items-center gap-1" title="Reset Filters">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Reset</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Category & Currency Filter Selectors -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-slate-800">
                        <div>
                            <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1 flex items-center gap-1">
                                <i data-lucide="layers" class="w-3 h-3 text-cyan-400"></i>
                                <span>Item Category</span>
                            </label>
                            <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs font-medium text-slate-200 focus:outline-none focus:border-cyan-500 cursor-pointer">
                                <option value="all">All Categories</option>
                                <option value="Weapon" {{ request('type') === 'Weapon' ? 'selected' : '' }}>Weapons</option>
                                <option value="Armor" {{ request('type') === 'Armor' ? 'selected' : '' }}>Armor &amp; Shields</option>
                                <option value="Card" {{ request('type') === 'Card' ? 'selected' : '' }}>Monster Cards</option>
                                <option value="Consumable" {{ request('type') === 'Consumable' ? 'selected' : '' }}>Potions &amp; Consumables</option>
                                <option value="Etc" {{ request('type') === 'Etc' ? 'selected' : '' }}>Materials &amp; Loot</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1 flex items-center gap-1">
                                <i data-lucide="coins" class="w-3 h-3 text-cyan-400"></i>
                                <span>Accepted Currency</span>
                            </label>
                            <select name="currency" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs font-medium text-slate-200 focus:outline-none focus:border-cyan-500 cursor-pointer">
                                <option value="all">Any Currency (Zeny &amp; CP)</option>
                                <option value="zeny" {{ request('currency') === 'zeny' ? 'selected' : '' }}>Zeny (Z) Only</option>
                                <option value="cashpoints" {{ request('currency') === 'cashpoints' ? 'selected' : '' }}>Cash Points (CP) Only</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1 flex items-center gap-1">
                                <i data-lucide="arrow-up-down" class="w-3 h-3 text-cyan-400"></i>
                                <span>Sort Order</span>
                            </label>
                            <select name="sort" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs font-medium text-slate-200 focus:outline-none focus:border-cyan-500 cursor-pointer">
                                <option value="newest" {{ request('sort') === 'newest' || !request('sort') ? 'selected' : '' }}>Recently Listed</option>
                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Lowest First</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: Highest First</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Items Table -->
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-950/80 text-slate-400 text-[11px] font-semibold uppercase tracking-wider">
                                <th class="py-3.5 px-4">Item &amp; Specifications</th>
                                <th class="py-3.5 px-4">Seller Character</th>
                                <th class="py-3.5 px-4 text-center">Expires In</th>
                                <th class="py-3.5 px-4 text-center">Stock</th>
                                <th class="py-3.5 px-4">Price / Unit</th>
                                <th class="py-3.5 px-4">Total Buyout</th>
                                <th class="py-3.5 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($listings as $listing)
                                <tr class="hover:bg-slate-800/40 transition-colors group">
                                    <!-- Item Name, Icon, Refine & Sockets -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0 relative shadow-inner">
                                                @if(!empty($listing->item->item_data['image_url']))
                                                    <img src="{{ $listing->item->item_data['image_url'] }}" alt="{{ $listing->item->formatted_name }}" class="w-7 h-7 object-contain" loading="lazy">
                                                @else
                                                    <i data-lucide="package" class="w-5 h-5 text-cyan-400"></i>
                                                @endif

                                                @if($listing->item->refine > 0)
                                                    <span class="absolute -top-1 -right-1 px-1 rounded text-[9px] font-bold font-mono bg-cyan-600 text-white shadow">
                                                        +{{ $listing->item->refine }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <h4 class="font-bold text-white group-hover:text-cyan-400 text-sm transition-colors">
                                                        {{ $listing->item->formatted_name }}
                                                    </h4>
                                                    @if($listing->item->slots > 0)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-black font-mono tracking-wider bg-slate-800 text-cyan-300 border border-cyan-500/30 shadow-sm">
                                                            <i data-lucide="layers" class="w-3 h-3 text-cyan-400"></i>
                                                            <span>{{ $listing->item->slots }} {{ Str::plural('SLOT', $listing->item->slots) }}</span>
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Compounded Cards / Open Sockets -->
                                                @if($listing->item->slots > 0)
                                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                                        @for($s = 0; $s < $listing->item->slots; $s++)
                                                            @php
                                                                $cardInSlot = $listing->item->cards[$s] ?? null;
                                                            @endphp
                                                            @if($cardInSlot)
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-medium bg-rose-950/60 border border-rose-500/40 text-rose-300" title="{{ $cardInSlot['name'] }}">
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                                                    <span>{{ $cardInSlot['name'] }}</span>
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-mono text-slate-500 bg-slate-950 border border-dashed border-slate-700">
                                                                    <span class="w-1.5 h-1.5 rounded-full border border-slate-600"></span>
                                                                    <span>Empty Slot</span>
                                                                </span>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                @endif

                                                <p class="text-[10px] text-slate-400 mt-0.5 font-mono">
                                                    ID #{{ $listing->item->nameid }} • <span class="text-slate-300">{{ $listing->item->item_type }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Seller -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                                            <span class="text-xs font-semibold text-slate-200">{{ $listing->seller_name }}</span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 font-mono">{{ $listing->listing_code }}</span>
                                    </td>

                                    <!-- Expires In -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="text-[11px] font-mono text-slate-300">
                                            {{ $listing->expires_at->diffForHumans(['parts' => 2, 'short' => true]) }}
                                        </span>
                                    </td>

                                    <!-- Stock Available -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-950 border border-slate-800 text-cyan-300">
                                            {{ number_format($listing->remaining_quantity ?? $listing->quantity ?? $listing->amount ?? 1) }}
                                        </span>
                                    </td>

                                    <!-- Unit Price -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1 font-mono font-bold {{ $listing->currency_type === 'zeny' ? 'text-amber-300' : 'text-purple-300' }}">
                                            @if($listing->currency_type === 'zeny')
                                                <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-400"></i>
                                            @else
                                                <i data-lucide="gem" class="w-3.5 h-3.5 text-purple-400"></i>
                                            @endif
                                            <span>{{ number_format($listing->unit_price) }}</span>
                                            <span class="text-[10px] opacity-75">{{ $listing->currency_type === 'zeny' ? 'Z' : 'CP' }}</span>
                                        </div>
                                    </td>

                                    <!-- Total Buyout Price -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1 font-mono font-bold text-sm {{ $listing->currency_type === 'zeny' ? 'text-amber-300' : 'text-purple-300' }}">
                                            <span>{{ number_format($listing->unit_price * ($listing->remaining_quantity ?? $listing->quantity ?? $listing->amount ?? 1)) }}</span>
                                            <span class="text-[10px] opacity-75">{{ $listing->currency_type === 'zeny' ? 'Z' : 'CP' }}</span>
                                        </div>
                                    </td>

                                    <!-- Action Button -->
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        @if(($listing->seller_account_id ?? $listing->account_id) === auth()->user()->account_id || ($listing->seller_user_id ?? 0) === auth()->id())
                                            <span class="text-[10px] font-mono font-semibold uppercase text-slate-500 bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-700">
                                                My Consignment
                                            </span>
                                        @else
                                            <button type="button" @click="openBuyModal({{ $listing->toJson() }})" class="px-4 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 shadow-sm cursor-pointer inline-flex items-center gap-1">
                                                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                                <span>Buy Item</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <i data-lucide="inbox" class="w-10 h-10 text-slate-600 mx-auto mb-2"></i>
                                        <p class="text-sm font-bold text-white">No active listings matching your query</p>
                                        <p class="text-xs text-slate-500 mt-1">Be the first adventurer to consign an item from your Kafra storage!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($listings->hasPages())
                    <div class="p-4 border-t border-slate-800">
                        {{ $listings->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ==================== TAB 2: KAFRA STORAGE (DEPOSIT) ==================== -->
    @if($tab === 'storage')
        <div class="space-y-4">
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="box" class="w-4 h-4 text-cyan-400"></i>
                        <span>Account Kafra Storage Vault</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Transfer tradable items into secure marketplace custody to consign them.</p>
                </div>
                <span class="text-xs font-mono text-cyan-400 bg-cyan-950/60 border border-cyan-500/30 px-3 py-1 rounded-lg">
                    {{ count($storageItems) }} Storage Entries
                </span>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-950/80 text-slate-400 text-[11px] font-semibold uppercase tracking-wider">
                                <th class="py-3.5 px-4">Item Name</th>
                                <th class="py-3.5 px-4 text-center">Quantity In Storage</th>
                                <th class="py-3.5 px-4 text-center">Bound / Trade Policy</th>
                                <th class="py-3.5 px-4 text-right">Transfer to Escrow</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($storageItems as $sItem)
                                @php
                                    $sId = is_array($sItem) ? ($sItem['nameid'] ?? 0) : ($sItem->nameid ?? 0);
                                    $sName = is_array($sItem) ? ($sItem['formatted_name'] ?? $sItem['name'] ?? "Item #{$sId}") : ($sItem->formatted_name ?? $sItem->name ?? "Item #{$sId}");
                                    $sAmount = is_array($sItem) ? ($sItem['amount'] ?? 0) : ($sItem->amount ?? 0);
                                    $sSlots = is_array($sItem) ? ($sItem['slots'] ?? 0) : ($sItem->slots ?? 0);
                                    $sCards = is_array($sItem) ? ($sItem['cards'] ?? []) : ($sItem->cards ?? []);
                                    $sTransferable = is_array($sItem) ? ($sItem['is_transferable'] ?? true) : ($sItem->is_transferable ?? true);
                                    $sReason = is_array($sItem) ? ($sItem['restriction_reason'] ?? null) : ($sItem->restriction_reason ?? null);
                                    $sIcon = is_array($sItem) ? ($sItem['icon_url'] ?? "https://static.divine-pride.net/images/items/item/{$sId}.png") : ($sItem->icon_url ?? "https://static.divine-pride.net/images/items/item/{$sId}.png");
                                @endphp
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0">
                                                <img src="{{ $sIcon }}" alt="Item" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <h4 class="font-bold text-white text-xs">{{ $sName }}</h4>
                                                    @if($sSlots > 0)
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold font-mono bg-slate-800 text-cyan-300 border border-cyan-500/30">
                                                            {{ $sSlots }} {{ Str::plural('SLOT', $sSlots) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(!empty($sCards))
                                                    <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                                        @foreach($sCards as $card)
                                                            <span class="px-1.5 py-0.2 rounded text-[8px] bg-rose-950/60 border border-rose-500/40 text-rose-300">
                                                                ◆ {{ $card['name'] }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <span class="text-[10px] text-slate-500 font-mono">ID #{{ $sId }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-200">
                                        {{ number_format($sAmount) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($sTransferable)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-950 text-emerald-400 border border-emerald-500/30">
                                                Transferable
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-950 text-rose-400 border border-rose-500/30" title="{{ $sReason ?? 'Restricted' }}">
                                                Restricted
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        @if($sTransferable)
                                            <button type="button" @click="openTransferModal({{ json_encode($sItem) }})" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 cursor-pointer inline-flex items-center gap-1 transition-colors">
                                                <i data-lucide="arrow-down-to-dot" class="w-3.5 h-3.5 text-cyan-400"></i>
                                                <span>Deposit into Escrow</span>
                                            </button>
                                        @else
                                            <span class="text-slate-500 text-xs italic">{{ $sReason ?? 'Cannot Consign' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-slate-400">
                                        No items currently found in your account Kafra storage.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== TAB 3: MY CUSTODY & SELL ==================== -->
    @if($tab === 'custody')
        <div class="space-y-4">
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="package-check" class="w-4 h-4 text-cyan-400"></i>
                        <span>Items in Marketplace Custody Vault</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">These items are safely in escrow. You can consign them for sale or withdraw them back to Kafra storage.</p>
                </div>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-950/80 text-slate-400 text-[11px] font-semibold uppercase tracking-wider">
                                <th class="py-3.5 px-4">Item Name</th>
                                <th class="py-3.5 px-4 text-center">Custody Amount</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($custodyItems as $cItem)
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0">
                                                <i data-lucide="package" class="w-5 h-5 text-cyan-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-white text-xs">{{ $cItem->formatted_name }}</h4>
                                                <span class="text-[10px] text-slate-500 font-mono">ID #{{ $cItem->nameid }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-200">
                                        {{ number_format($cItem->amount) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-cyan-950 text-cyan-400 border border-cyan-500/30">
                                            In Custody
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" @click="openSellModal({{ json_encode($cItem) }})" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 cursor-pointer shadow-sm">
                                                Post Consignment
                                            </button>
                                            <form action="{{ route('marketplace.withdraw', $cItem->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 cursor-pointer">
                                                    Withdraw
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-slate-400">
                                        You currently have no items deposited into custody. Visit the Kafra Storage tab to deposit items.
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
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="tag" class="w-4 h-4 text-cyan-400"></i>
                    <span>My Active Consignments</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Manage items you currently have listed for sale in the public market.</p>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-950/80 text-slate-400 text-[11px] font-semibold uppercase tracking-wider">
                                <th class="py-3.5 px-4">Item Name</th>
                                <th class="py-3.5 px-4 text-center">Stock</th>
                                <th class="py-3.5 px-4">Price</th>
                                <th class="py-3.5 px-4 text-center">Expires In</th>
                                <th class="py-3.5 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($myListings as $mList)
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <h4 class="font-bold text-white text-xs">{{ $mList->item->formatted_name }}</h4>
                                        <span class="text-[10px] text-slate-500 font-mono">{{ $mList->listing_code }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-cyan-300">
                                        {{ number_format($mList->remaining_quantity ?? $mList->quantity ?? $mList->amount ?? 1) }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold {{ $mList->currency_type === 'zeny' ? 'text-amber-300' : 'text-purple-300' }}">
                                        {{ number_format($mList->unit_price) }} {{ $mList->currency_type === 'zeny' ? 'Z' : 'CP' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono text-slate-300">
                                        {{ $mList->expires_at->diffForHumans() }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <form action="{{ route('marketplace.cancel', $mList->id) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this listing and return item to your custody vault?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-rose-950/80 hover:bg-rose-900 text-rose-300 border border-rose-500/30 cursor-pointer">
                                                Cancel Listing
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-slate-400">
                                        You do not have any active marketplace consignments.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== TAB 5: TRADE LEDGER & HISTORY ==================== -->
    @if($tab === 'history')
        <div class="space-y-4">
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-cyan-400"></i>
                    <span>Account Trade Ledger &amp; History</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Audit log of your purchases, sales, and escrow movements.</p>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-950/80 text-slate-400 text-[11px] font-semibold uppercase tracking-wider">
                                <th class="py-3.5 px-4">TX Code</th>
                                <th class="py-3.5 px-4">Item &amp; Specifications</th>
                                <th class="py-3.5 px-4 text-center">Role</th>
                                <th class="py-3.5 px-4">Counterparty</th>
                                <th class="py-3.5 px-4 text-center">Quantity</th>
                                <th class="py-3.5 px-4 text-right">Unit Price</th>
                                <th class="py-3.5 px-4 text-right">Total Amount</th>
                                <th class="py-3.5 px-4 text-right">Date &amp; Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($history as $tx)
                                @php
                                    $isBuyer = ($tx->buyer_user_id === auth()->id() || $tx->buyer_account_id === auth()->user()->account_id);
                                    $txItem = $tx->item;
                                    $itemIcon = $txItem ? ($txItem->icon_url ?? "https://static.divine-pride.net/images/items/item/{$txItem->nameid}.png") : "https://static.divine-pride.net/images/items/item/{$tx->item_id}.png";
                                    $itemName = $txItem ? ($txItem->formatted_name ?? $txItem->item_name ?? "Item #{$txItem->nameid}") : ($tx->item_name ?? "Item #{$tx->item_id}");
                                    $itemSlots = $txItem ? ($txItem->slots ?? 0) : 0;
                                    $itemCards = $txItem ? ($txItem->cards ?? []) : [];
                                @endphp
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-semibold text-cyan-400 whitespace-nowrap">
                                        {{ $tx->transaction_code ?? $tx->tx_code }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0 p-1">
                                                <img src="{{ $itemIcon }}" alt="Item" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <h4 class="font-bold text-white text-xs">{{ $itemName }}</h4>
                                                    @if($itemSlots > 0)
                                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold font-mono bg-cyan-950 text-cyan-300 border border-cyan-500/30">
                                                            {{ $itemSlots }}S
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(!empty($itemCards))
                                                    <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                                        @foreach($itemCards as $cCard)
                                                            <span class="px-1.5 py-0.2 rounded text-[8px] bg-rose-950/60 border border-rose-500/40 text-rose-300">
                                                                ◆ {{ $cCard['name'] ?? 'Card' }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <span class="text-[10px] text-slate-500 font-mono">
                                                    ID #{{ $txItem->nameid ?? $tx->item_id }}
                                                    @if($txItem && $txItem->item_type)
                                                        • <span class="text-slate-400">{{ $txItem->item_type }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($isBuyer)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                                                Buyer
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                                Seller
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($isBuyer)
                                            <div class="flex items-center gap-1.5 text-xs text-slate-300">
                                                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                                                <span class="font-medium">{{ $tx->seller_char_name ?? 'Player' }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1.5 text-xs text-slate-300">
                                                <i data-lucide="user-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                                                <span class="font-medium">{{ $tx->buyer_char_name ?? 'Player' }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-200 whitespace-nowrap">
                                        {{ number_format($tx->quantity ?? $tx->amount ?? 1) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-semibold text-slate-300 whitespace-nowrap">
                                        {{ number_format($tx->unit_price) }} <span class="text-[10px] opacity-75">{{ $tx->currency_type === 'zeny' ? 'Z' : 'CP' }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="font-mono font-bold text-sm {{ $tx->currency_type === 'zeny' ? 'text-amber-300' : 'text-purple-300' }}">
                                            {{ number_format($tx->gross_amount ?? $tx->total_price) }} <span class="text-[10px]">{{ $tx->currency_type === 'zeny' ? 'Z' : 'CP' }}</span>
                                        </div>
                                        @if(!$isBuyer && ($tx->tax_amount ?? 0) > 0)
                                            <div class="text-[10px] text-slate-400 mt-0.5 font-mono">
                                                Net: <span class="text-emerald-400 font-semibold">{{ number_format($tx->net_seller_proceeds) }}</span>
                                                <span class="text-rose-400">(-{{ number_format($tx->tax_amount) }})</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                        {{ $tx->created_at->format('M d, Y H:i') }}
                                        <span class="text-[10px] text-slate-500 block">{{ $tx->created_at->diffForHumans() }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400">
                                        <i data-lucide="history" class="w-8 h-8 text-slate-600 mx-auto mb-2"></i>
                                        <p class="font-bold text-slate-300">No transactions recorded yet</p>
                                        <p class="text-xs text-slate-500 mt-1">Purchases and sales made in the trade house will appear here with complete audit trail.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($history instanceof \Illuminate\Pagination\LengthAwarePaginator && $history->hasPages())
                    <div class="p-4 border-t border-slate-800 bg-slate-950/60">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ==================== BUY MODAL ==================== -->
    <div x-show="buyModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div @click.away="buyModalOpen = false" class="rounded-2xl bg-slate-900 border border-slate-800 p-6 max-w-md w-full space-y-4 shadow-2xl">
            <template x-if="selectedListing">
                <form action="{{ route('marketplace.buy', '0') }}" :action="'{{ url('/marketplace/buy') }}/' + selectedListing.id" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="shopping-cart" class="w-4 h-4 text-cyan-400"></i>
                            <span>Confirm Purchase</span>
                        </h3>
                        <button type="button" @click="buyModalOpen = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <h4 class="font-bold text-white text-sm" x-text="selectedListing.item.formatted_name"></h4>
                        <div class="flex justify-between text-xs text-slate-400">
                            <span>Seller:</span>
                            <span class="text-slate-200 font-semibold" x-text="selectedListing.seller_name"></span>
                        </div>
                        <div class="flex justify-between text-xs text-slate-400">
                            <span>Price / Unit:</span>
                            <span class="font-mono font-bold text-slate-200" x-text="new Intl.NumberFormat().format(selectedListing.unit_price) + ' ' + (selectedListing.currency_type === 'zeny' ? 'Z' : 'CP')"></span>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Quantity to Purchase</label>
                            <span class="text-[11px] text-slate-400">Available Stock: <strong class="text-cyan-400 font-mono" x-text="selectedListing.remaining_quantity || selectedListing.quantity || selectedListing.amount || 1"></strong></span>
                        </div>
                        <input type="number" name="quantity" x-model.number="buyQuantity" min="1" :max="selectedListing.remaining_quantity || selectedListing.quantity || selectedListing.amount || 1" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex justify-between items-center text-xs">
                        <span class="font-semibold text-slate-300">Total Due:</span>
                        <span class="font-mono font-bold text-base text-cyan-400" x-text="new Intl.NumberFormat().format(selectedListing.unit_price * buyQuantity) + ' ' + (selectedListing.currency_type === 'zeny' ? 'Z' : 'CP')"></span>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="buyModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:text-white border border-slate-700 cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 cursor-pointer shadow-md">Complete Purchase</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- ==================== TRANSFER (DEPOSIT) MODAL ==================== -->
    <div x-show="transferModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div @click.away="transferModalOpen = false" class="rounded-2xl bg-slate-900 border border-slate-800 p-6 max-w-md w-full space-y-4 shadow-2xl">
            <template x-if="selectedStorageItem">
                <form action="{{ route('marketplace.transfer') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="storage_id" :value="selectedStorageItem.storage_id">
                    <input type="hidden" name="nameid" :value="selectedStorageItem.nameid">
                    <input type="hidden" name="source" value="storage">

                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="arrow-down-to-dot" class="w-4 h-4 text-cyan-400"></i>
                            <span>Deposit into Custody</span>
                        </h3>
                        <button type="button" @click="transferModalOpen = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Item Details Card -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center shrink-0 p-1">
                                <img :src="selectedStorageItem.icon_url" alt="Item" class="w-8 h-8 object-contain" onerror="this.src='/images/item-placeholder.png'">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-white text-sm" x-text="selectedStorageItem.formatted_name || selectedStorageItem.name"></h4>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="text-[10px] text-cyan-400 font-mono" x-text="'ID #' + selectedStorageItem.nameid"></span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-medium" x-text="selectedStorageItem.type + (selectedStorageItem.subtype ? ' • ' + selectedStorageItem.subtype : '')"></span>
                                    <template x-if="selectedStorageItem.slots > 0">
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-cyan-950 text-cyan-300 border border-cyan-500/30 font-bold font-mono" x-text="selectedStorageItem.slots + ' SLOTS'"></span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Combat Stats (Attack, Defense, Weight, Req Level) -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-800 text-[11px]" x-show="selectedStorageItem.attack > 0 || selectedStorageItem.defense > 0 || selectedStorageItem.weight > 0 || selectedStorageItem.equip_level_min > 0">
                            <template x-if="selectedStorageItem.attack > 0">
                                <div class="bg-slate-900/80 p-1.5 rounded-lg border border-slate-800">
                                    <span class="text-slate-500 block text-[9px] uppercase font-semibold">Atk</span>
                                    <span class="font-mono font-bold text-slate-200" x-text="selectedStorageItem.attack"></span>
                                </div>
                            </template>
                            <template x-if="selectedStorageItem.defense > 0">
                                <div class="bg-slate-900/80 p-1.5 rounded-lg border border-slate-800">
                                    <span class="text-slate-500 block text-[9px] uppercase font-semibold">Def</span>
                                    <span class="font-mono font-bold text-slate-200" x-text="selectedStorageItem.defense"></span>
                                </div>
                            </template>
                            <template x-if="selectedStorageItem.weight > 0">
                                <div class="bg-slate-900/80 p-1.5 rounded-lg border border-slate-800">
                                    <span class="text-slate-500 block text-[9px] uppercase font-semibold">Weight</span>
                                    <span class="font-mono font-bold text-slate-200" x-text="selectedStorageItem.weight"></span>
                                </div>
                            </template>
                            <template x-if="selectedStorageItem.equip_level_min > 0">
                                <div class="bg-slate-900/80 p-1.5 rounded-lg border border-slate-800">
                                    <span class="text-slate-500 block text-[9px] uppercase font-semibold">Req Lv</span>
                                    <span class="font-mono font-bold text-slate-200" x-text="selectedStorageItem.equip_level_min"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Compounded Cards -->
                        <template x-if="selectedStorageItem.cards && selectedStorageItem.cards.length > 0">
                            <div class="pt-2 border-t border-slate-800 space-y-1">
                                <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Compounded Cards:</span>
                                <div class="space-y-1">
                                    <template x-for="card in selectedStorageItem.cards" :key="card.slot">
                                        <div class="flex items-center gap-1.5 text-[11px] text-rose-300 bg-rose-950/40 border border-rose-500/30 px-2 py-1 rounded-lg">
                                            <span class="font-bold text-[9px] px-1 py-0.2 rounded bg-rose-900/60 font-mono" x-text="'SLOT ' + card.slot"></span>
                                            <span class="font-medium" x-text="card.name"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Storage Availability -->
                        <div class="pt-2 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                            <span>Available in Kafra Storage:</span>
                            <span class="font-mono font-bold text-slate-100" x-text="new Intl.NumberFormat().format(selectedStorageItem.amount)"></span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Transfer Quantity</label>
                        <input type="number" name="amount" x-model.number="transferAmount" min="1" :max="selectedStorageItem.amount" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                        <span class="text-[10px] text-slate-500 mt-1 block">Maximum amount transferable from this stack: <strong class="text-slate-300" x-text="selectedStorageItem.amount"></strong></span>
                    </div>

                    <div class="p-3 rounded-xl bg-cyan-950/40 border border-cyan-500/30 text-xs text-cyan-300 flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 shrink-0 text-cyan-400"></i>
                        <span>Item will be held in secure escrow custody and can be consigned or returned anytime.</span>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="transferModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:text-white border border-slate-700 cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 cursor-pointer shadow-md">Deposit Item</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- ==================== SELL (CONSIGN) MODAL ==================== -->
    <div x-show="listModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div @click.away="listModalOpen = false" class="rounded-2xl bg-slate-900 border border-slate-800 p-6 max-w-md w-full space-y-4 shadow-2xl">
            <template x-if="selectedCustodyItem">
                <form action="{{ route('marketplace.list') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="item_id" :value="selectedCustodyItem.id">
                    <input type="hidden" name="custody_item_id" :value="selectedCustodyItem.id">

                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="tag" class="w-4 h-4 text-cyan-400"></i>
                            <span>Consign for Sale</span>
                        </h3>
                        <button type="button" @click="listModalOpen = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800">
                        <h4 class="font-bold text-white text-sm" x-text="selectedCustodyItem.formatted_name"></h4>
                        <span class="text-xs text-slate-400">Available: <strong class="text-slate-200" x-text="selectedCustodyItem.amount"></strong></span>
                    </div>

                    <!-- Currency Radio Selection -->
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1.5">Currency Type</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-colors" :class="listCurrency === 'zeny' ? 'bg-amber-500/10 border-amber-500/40 text-amber-300 font-bold' : 'bg-slate-950 border-slate-800 text-slate-400'">
                                <input type="radio" name="currency_type" value="zeny" x-model="listCurrency" class="hidden">
                                <i data-lucide="coins" class="w-4 h-4 text-amber-400"></i>
                                <span>Zeny</span>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-colors" :class="listCurrency === 'cashpoints' ? 'bg-purple-500/10 border-purple-500/40 text-purple-300 font-bold' : 'bg-slate-950 border-slate-800 text-slate-400'">
                                <input type="radio" name="currency_type" value="cashpoints" x-model="listCurrency" class="hidden">
                                <i data-lucide="gem" class="w-4 h-4 text-purple-400"></i>
                                <span>Cash Points</span>
                            </label>
                        </div>
                    </div>

                    <!-- Quantity & Price -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Quantity</label>
                            <input type="number" name="quantity" x-model.number="listQuantity" min="1" :max="selectedCustodyItem.amount" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Price / Unit</label>
                            <input type="number" name="unit_price" x-model.number="listUnitPrice" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Listing Duration</label>
                        <select name="duration_hours" x-model.number="listDuration" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                            <option value="24">24 Hours (1 Day)</option>
                            <option value="72">72 Hours (3 Days)</option>
                            <option value="168">168 Hours (7 Days)</option>
                            <option value="336">336 Hours (14 Days)</option>
                            <option value="720">720 Hours (30 Days)</option>
                        </select>
                    </div>

                    <!-- Proceeds Calculation Preview -->
                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Gross Sale Price:</span>
                            <span class="font-mono text-white" x-text="new Intl.NumberFormat().format(listUnitPrice * listQuantity)"></span>
                        </div>
                        <div class="flex justify-between text-rose-400">
                            <span x-text="'Sales Tax (' + taxPercent + '%):'"></span>
                            <span class="font-mono" x-text="'-' + new Intl.NumberFormat().format(Math.round((listUnitPrice * listQuantity * taxPercent) / 100))"></span>
                        </div>
                        <div class="flex justify-between text-emerald-400 font-bold pt-1.5 border-t border-slate-800">
                            <span>Net Proceeds on Sale:</span>
                            <span class="font-mono text-sm" x-text="new Intl.NumberFormat().format(Math.round((listUnitPrice * listQuantity) - ((listUnitPrice * listQuantity * taxPercent) / 100))) + ' ' + (listCurrency === 'zeny' ? 'Z' : 'CP')"></span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="listModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:text-white border border-slate-700 cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 cursor-pointer shadow-md">Post Consignment</button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection
