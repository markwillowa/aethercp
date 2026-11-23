@extends('layouts.admin')

@section('title', 'Player Marketplace - Admin Control Panel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-cinzel text-white flex items-center gap-2">
                <i data-lucide="shopping-bag" class="w-6 h-6 text-[#D4AF37]"></i>
                Player Marketplace &amp; Escrow
            </h1>
            <p class="text-xs text-slate-400 mt-1">Manage player-to-player trade house listings, item custody escrow, economy taxes, and trading rules.</p>
        </div>
        <div class="flex items-center gap-3">
            @if(!empty($settings['maintenance_mode']))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> Maintenance Mode Active
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Marketplace Operational
                </span>
            @endif
        </div>
    </div>

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/20 text-[#FCE69B] flex items-center justify-center shrink-0">
                <i data-lucide="tag" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Active Listings</p>
                <h3 class="text-2xl font-bold text-white font-cinzel">{{ number_format($stats['active_listings']) }}</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ number_format($stats['total_items_in_custody']) }} items in custody</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center shrink-0">
                <i data-lucide="check-check" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Completed Trades</p>
                <h3 class="text-2xl font-bold text-white font-cinzel">{{ number_format($stats['total_sales_count']) }}</h3>
                <p class="text-[10px] text-emerald-400 mt-0.5">Successful player purchases</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center shrink-0">
                <i data-lucide="coins" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Zeny Volume</p>
                <h3 class="text-lg font-bold text-amber-300 font-cinzel">{{ number_format($stats['zeny_volume']) }} Z</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Tax: <span class="text-white">{{ number_format($stats['zeny_tax_collected']) }} Z</span></p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-300 flex items-center justify-center shrink-0">
                <i data-lucide="gem" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Cash Points Volume</p>
                <h3 class="text-lg font-bold text-purple-300 font-cinzel">{{ number_format($stats['points_volume']) }} CP</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Tax: <span class="text-white">{{ number_format($stats['points_tax_collected']) }} CP</span></p>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div x-data="{ adminTab: 'listings' }" class="space-y-6">
        <!-- Tab Navigation Buttons -->
        <div class="flex items-center gap-2 border-b border-[#3E2E4E] pb-3">
            <button @click="adminTab = 'listings'" :class="adminTab === 'listings' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'text-slate-400 hover:text-white bg-[#1C1525] border border-transparent'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i data-lucide="list" class="w-4 h-4"></i>
                Listings &amp; Moderation ({{ $listings->total() }})
            </button>
            <button @click="adminTab = 'transactions'" :class="adminTab === 'transactions' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'text-slate-400 hover:text-white bg-[#1C1525] border border-transparent'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4"></i>
                Transactions Audit Log ({{ $transactions->total() }})
            </button>
            <button @click="adminTab = 'settings'" :class="adminTab === 'settings' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/40' : 'text-slate-400 hover:text-white bg-[#1C1525] border border-transparent'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i data-lucide="settings-2" class="w-4 h-4"></i>
                Configuration &amp; Economy Rules
            </button>
        </div>

        <!-- Tab 1: Listings & Moderation -->
        <div x-show="adminTab === 'listings'" class="space-y-4">
            <div class="rounded-2xl bg-[#1C1525] border border-[#3E2E4E] overflow-hidden">
                <div class="p-4 border-b border-[#3E2E4E] flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white font-cinzel">Current Trade House Listings</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.marketplace.index', ['status' => 'all']) }}" class="px-2.5 py-1 rounded-lg text-[11px] font-medium {{ request('status') === 'all' || !request('status') ? 'bg-[#D4AF37] text-black font-bold' : 'text-slate-400 hover:text-white bg-[#251A33]' }}">All</a>
                        <a href="{{ route('admin.marketplace.index', ['status' => 'active']) }}" class="px-2.5 py-1 rounded-lg text-[11px] font-medium {{ request('status') === 'active' ? 'bg-[#D4AF37] text-black font-bold' : 'text-slate-400 hover:text-white bg-[#251A33]' }}">Active</a>
                        <a href="{{ route('admin.marketplace.index', ['status' => 'sold']) }}" class="px-2.5 py-1 rounded-lg text-[11px] font-medium {{ request('status') === 'sold' ? 'bg-[#D4AF37] text-black font-bold' : 'text-slate-400 hover:text-white bg-[#251A33]' }}">Sold</a>
                        <a href="{{ route('admin.marketplace.index', ['status' => 'cancelled']) }}" class="px-2.5 py-1 rounded-lg text-[11px] font-medium {{ request('status') === 'cancelled' ? 'bg-[#D4AF37] text-black font-bold' : 'text-slate-400 hover:text-white bg-[#251A33]' }}">Cancelled</a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/80 text-[#F3E5AB]">
                                <th class="py-3 px-4">Code</th>
                                <th class="py-3 px-4">Item Details</th>
                                <th class="py-3 px-4">Seller</th>
                                <th class="py-3 px-4">Price / Unit</th>
                                <th class="py-3 px-4">Stock</th>
                                <th class="py-3 px-4">Expires In</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
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
                                                <p class="font-bold text-white">{{ $itemListing->item->formatted_name }}</p>
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
                                            <form action="{{ route('admin.marketplace.cancel', $itemListing->id) }}" method="POST" onsubmit="return confirm('Cancel this listing and return item to seller custody?');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-500/20 text-rose-300 hover:bg-rose-500/40 border border-rose-500/30 transition-all">
                                                    Cancel Listing
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-500 text-[11px]">No actions</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-slate-400">No marketplace listings match the selected criteria.</td>
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

        <!-- Tab 2: Transactions Audit Log -->
        <div x-show="adminTab === 'transactions'" class="space-y-4">
            <div class="rounded-2xl bg-[#1C1525] border border-[#3E2E4E] overflow-hidden">
                <div class="p-4 border-b border-[#3E2E4E]">
                    <h3 class="text-sm font-bold text-white font-cinzel">Marketplace Transactions Ledger</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/80 text-[#F3E5AB]">
                                <th class="py-3 px-4">TX Reference</th>
                                <th class="py-3 px-4">Purchased Item</th>
                                <th class="py-3 px-4">Buyer</th>
                                <th class="py-3 px-4">Seller</th>
                                <th class="py-3 px-4">Gross Total</th>
                                <th class="py-3 px-4">Tax Fee ({{ $settings['tax_percent'] }}%)</th>
                                <th class="py-3 px-4">Net Proceeds</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Fulfillment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-[#251A33]/50 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-[#E5B358]">{{ $tx->transaction_code }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $tx->item->icon_url }}" alt="Item" class="w-7 h-7 object-contain rounded bg-[#140E1B] p-0.5" onerror="this.src='/images/item-placeholder.png'">
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
                                    <td colspan="9" class="text-center py-8 text-slate-400">No marketplace sales have been recorded yet.</td>
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

        <!-- Tab 3: Configuration & Economy Rules -->
        <div x-show="adminTab === 'settings'" class="space-y-6">
            <form action="{{ route('admin.marketplace.settings') }}" method="POST">
                @csrf

                <!-- Master Activation Card -->
                <div class="p-5 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] space-y-4">
                    <h3 class="text-sm font-bold text-white font-cinzel flex items-center gap-2 border-b border-[#3E2E4E] pb-3">
                        <i data-lucide="power" class="w-4 h-4 text-[#D4AF37]"></i>
                        Extension Activation &amp; Maintenance Status
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#140E1B] border border-[#352643]">
                            <div>
                                <label class="text-xs font-bold text-white block">Enable Marketplace Extension</label>
                                <span class="text-[11px] text-slate-400">Master switch to make the Trade House accessible to registered players.</span>
                            </div>
                            <input type="checkbox" name="module_enabled_marketplace" value="1" {{ !empty($settings['enabled']) ? 'checked' : '' }} class="w-4 h-4 rounded border-[#352643] text-[#D4AF37] focus:ring-[#D4AF37]">
                        </div>

                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#140E1B] border border-[#352643]">
                            <div>
                                <label class="text-xs font-bold text-white block">Maintenance Mode</label>
                                <span class="text-[11px] text-slate-400">Temporarily suspend listing creation and purchases while keeping listings safe.</span>
                            </div>
                            <input type="checkbox" name="marketplace_maintenance_mode" value="1" {{ !empty($settings['maintenance_mode']) ? 'checked' : '' }} class="w-4 h-4 rounded border-[#352643] text-[#D4AF37] focus:ring-[#D4AF37]">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <!-- General & Operation Settings -->
                    <div class="p-5 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] space-y-4">
                        <h3 class="text-sm font-bold text-white font-cinzel flex items-center gap-2 border-b border-[#3E2E4E] pb-3">
                            <i data-lucide="sliders" class="w-4 h-4 text-[#D4AF37]"></i>
                            Operations &amp; Announcements
                        </h3>

                        <div>
                            <label class="text-xs font-bold text-slate-300 block mb-1">Marketplace Announcement</label>
                            <input type="text" name="marketplace_announcement" value="{{ $settings['announcement'] }}" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Displayed at the top of the Trade House interface</span>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-300 block mb-1">Trade House Rules &amp; Notice</label>
                            <textarea name="marketplace_rules" rows="3" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">{{ $settings['rules'] }}</textarea>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Official rules guidelines shown to players</span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Max / Player</label>
                                <input type="number" name="marketplace_max_listings_player" value="{{ $settings['max_listings_player'] }}" min="1" max="100" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Max / Character</label>
                                <input type="number" name="marketplace_max_listings_char" value="{{ $settings['max_listings_char'] }}" min="1" max="50" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Max Items / Listing</label>
                                <input type="number" name="marketplace_max_items_per_listing" value="{{ $settings['max_items_per_listing'] ?? 1000 }}" min="1" max="30000" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Minimum Price</label>
                                <input type="number" name="marketplace_min_price" value="{{ $settings['min_price'] }}" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Maximum Price</label>
                                <input type="number" name="marketplace_max_price" value="{{ $settings['max_price'] }}" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 pt-2 border-t border-[#3E2E4E]">
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Min Duration</label>
                                <input type="number" name="marketplace_min_duration_hours" value="{{ $settings['min_duration_hours'] ?? 24 }}" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                                <span class="text-[10px] text-slate-400">Hours</span>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Default Duration</label>
                                <select name="marketplace_default_duration_hours" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                                    <option value="24" {{ $settings['default_duration_hours'] == 24 ? 'selected' : '' }}>24 Hours (1 Day)</option>
                                    <option value="72" {{ $settings['default_duration_hours'] == 72 ? 'selected' : '' }}>72 Hours (3 Days)</option>
                                    <option value="168" {{ $settings['default_duration_hours'] == 168 ? 'selected' : '' }}>168 Hours (7 Days)</option>
                                    <option value="336" {{ $settings['default_duration_hours'] == 336 ? 'selected' : '' }}>336 Hours (14 Days)</option>
                                    <option value="720" {{ $settings['default_duration_hours'] == 720 ? 'selected' : '' }}>720 Hours (30 Days)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Max Duration</label>
                                <input type="number" name="marketplace_max_duration_hours" value="{{ $settings['max_duration_hours'] ?? 720 }}" min="24" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                                <span class="text-[10px] text-slate-400">Hours</span>
                            </div>
                        </div>
                    </div>

                    <!-- Currencies, Tax & Restrictions -->
                    <div class="p-5 rounded-2xl bg-[#1C1525] border border-[#3E2E4E] space-y-4">
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
                                <input type="number" step="0.1" name="marketplace_tax_percent" value="{{ $settings['tax_percent'] }}" min="0" max="50" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                                <span class="text-[10px] text-slate-400">Deducted from seller proceeds</span>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-300 block mb-1">Listing Insertion Fee (Zeny)</label>
                                <input type="number" name="marketplace_listing_fee" value="{{ $settings['listing_fee'] }}" min="0" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                                <span class="text-[10px] text-slate-400">Flat fee charged upon listing</span>
                            </div>
                        </div>

                        <!-- Item Safety Rules -->
                        <div class="space-y-2 pt-2 border-t border-[#3E2E4E]">
                            <p class="text-[11px] font-bold text-[#E5B358] uppercase tracking-wider font-cinzel">Item Safeguards &amp; Restriction Rules</p>

                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <label class="flex items-center gap-2 text-slate-300">
                                    <input type="checkbox" name="marketplace_allow_withdrawals" value="1" {{ !empty($settings['allow_withdrawals']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Item Withdrawals
                                </label>
                                <label class="flex items-center gap-2 text-slate-300">
                                    <input type="checkbox" name="marketplace_allow_stackables" value="1" {{ !empty($settings['allow_stackables']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Stackable Items
                                </label>
                                <label class="flex items-center gap-2 text-slate-300">
                                    <input type="checkbox" name="marketplace_allow_equipment" value="1" {{ !empty($settings['allow_equipment']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Equipment
                                </label>
                                <label class="flex items-center gap-2 text-slate-300">
                                    <input type="checkbox" name="marketplace_allow_refined" value="1" {{ !empty($settings['allow_refined']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Refined Equipment
                                </label>
                                <label class="flex items-center gap-2 text-slate-300">
                                    <input type="checkbox" name="marketplace_allow_cards" value="1" {{ !empty($settings['allow_cards']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Cards &amp; Item Options
                                </label>
                                <label class="flex items-center gap-2 text-slate-300">
                                    <input type="checkbox" name="marketplace_history_enabled" value="1" {{ !empty($settings['history_enabled']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Enable Marketplace History
                                </label>
                                <label class="flex items-center gap-2 text-slate-300">
                                    <input type="checkbox" name="marketplace_allow_bound" value="1" {{ !empty($settings['allow_bound']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Bound Items (Caution)
                                </label>
                                <label class="flex items-center gap-2 text-slate-300">
                                    <input type="checkbox" name="marketplace_allow_rental" value="1" {{ !empty($settings['allow_rental']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#D4AF37]">
                                    Allow Rental Items (Caution)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-xs bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow-md hover:brightness-110 transition-all flex items-center gap-2 cursor-pointer">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Save Marketplace Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
