@extends('layouts.app')

@section('title', 'Player Marketplace & Bazaar')

@section('content')
<div class="payon-container py-10 space-y-6" x-data="{
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
    openBuyModal(listing) { this.selectedListing = listing; this.buyQuantity = 1; this.buyModalOpen = true; },
    openTransferModal(item) { this.selectedStorageItem = item; this.transferAmount = 1; this.transferModalOpen = true; },
    openSellModal(item) { this.selectedCustodyItem = item; this.listQuantity = 1; this.listUnitPrice = 1000; this.listCurrency = 'zeny'; this.listModalOpen = true; }
}">
    <!-- Payon Bazaar Header -->
    <div class="payon-wood-frame overflow-hidden">
        <div class="payon-wood-header">
            <div class="flex items-center gap-2">
                <i data-lucide="shopping-bag" class="w-4 h-4 text-[#A88950]"></i>
                <span class="font-cinzel uppercase tracking-wider text-sm">Payon Bazaar &amp; Trade House</span>
            </div>
            <div class="flex items-center gap-3 text-[11px] font-mono text-[#E7D8B5]">
                <span>Zeny: {{ number_format($characterZeny) }} Z</span>
                <span>&bull;</span>
                <span>CP: {{ number_format($accountCashPoints) }}</span>
            </div>
        </div>

        <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="font-cinzel text-2xl font-bold text-[#35291F]">Player Marketplace</h1>
                <p class="text-xs text-[#80633B] mt-1">Secure consignment trading for in-game storage items and currency.</p>
            </div>
            <form action="{{ route('marketplace.character') }}" method="POST" class="inline-flex items-center gap-2 shrink-0">
                @csrf
                <span class="text-xs font-bold text-[#35291F]">Character:</span>
                <select name="char_id" onchange="this.form.submit()" class="px-3 py-1.5 rounded text-xs">
                    @foreach($characters as $c)
                        <option value="{{ $c->char_id }}" {{ $activeChar->char_id == $c->char_id ? 'selected' : '' }}>
                            {{ $c->name }} (Lv. {{ $c->base_level }}/{{ $c->job_level }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap items-center gap-2">
        @foreach(['browse' => 'Browse Market', 'storage' => 'Kafra Storage (Deposit)', 'custody' => 'My Custody & Sell', 'my_listings' => 'My Active Listings', 'history' => 'Trade Ledger'] as $tKey => $tLabel)
            <a href="{{ route('marketplace.index', ['tab' => $tKey]) }}"
               class="px-3.5 py-2 rounded text-xs font-bold border {{ $tab === $tKey ? 'bg-[#263D2C] text-[#FFF8E8] border-[#A88950]' : 'bg-[#F8F0DD] text-[#35291F] border-[#A88950] hover:bg-[#E7D8B5]' }}">
                {{ $tLabel }}
            </a>
        @endforeach
    </div>

    <!-- TAB 1: BROWSE -->
    @if($tab === 'browse')
        @php $listings = $listings ?? $browseListings ?? collect(); @endphp
        <form action="{{ route('marketplace.index') }}" method="GET" class="payon-parchment-panel p-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <input type="hidden" name="tab" value="browse">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by item name or ID..." class="flex-1 px-3.5 py-2 rounded text-xs">
            <select name="type" onchange="this.form.submit()" class="px-3 py-2 rounded text-xs">
                <option value="all">All Categories</option>
                <option value="Weapon" {{ request('type') === 'Weapon' ? 'selected' : '' }}>Weapons</option>
                <option value="Armor" {{ request('type') === 'Armor' ? 'selected' : '' }}>Armor &amp; Shields</option>
                <option value="Card" {{ request('type') === 'Card' ? 'selected' : '' }}>Monster Cards</option>
                <option value="Consumable" {{ request('type') === 'Consumable' ? 'selected' : '' }}>Consumables</option>
                <option value="Etc" {{ request('type') === 'Etc' ? 'selected' : '' }}>Materials &amp; Loot</option>
            </select>
            <select name="currency" onchange="this.form.submit()" class="px-3 py-2 rounded text-xs">
                <option value="all">Any Currency</option>
                <option value="zeny" {{ request('currency') === 'zeny' ? 'selected' : '' }}>Zeny (Z)</option>
                <option value="cashpoints" {{ request('currency') === 'cashpoints' ? 'selected' : '' }}>Cash Points (CP)</option>
            </select>
            <button type="submit" class="payon-btn-forest text-xs py-2 px-4 shrink-0">Search</button>
        </form>

        <div class="payon-ranking-board">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="font-cinzel uppercase text-[11px]">
                            <th class="py-3 px-4">Item</th>
                            <th class="py-3 px-4">Seller</th>
                            <th class="py-3 px-4 text-center">Stock</th>
                            <th class="py-3 px-4">Unit Price</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#A88950]/30">
                        @forelse($listings as $listing)
                            <tr class="hover:bg-[#E7D8B5]/60">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $listing->item->icon_url ?? ('https://static.divine-pride.net/images/items/item/' . $listing->item->nameid . '.png') }}" class="w-7 h-7 object-contain" alt="">
                                        <div>
                                            <span class="font-bold text-[#35291F] block">{{ $listing->item->formatted_name }}</span>
                                            <span class="text-[10px] text-[#80633B] font-mono">ID #{{ $listing->item->nameid }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-[#493322]">{{ $listing->seller_name }}</td>
                                <td class="py-3 px-4 text-center font-mono font-bold">{{ number_format($listing->remaining_quantity ?? $listing->quantity ?? $listing->amount ?? 1) }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-[#263D2C]">{{ number_format($listing->unit_price) }} {{ $listing->currency_type === 'zeny' ? 'Z' : 'CP' }}</td>
                                <td class="py-3 px-4 text-right">
                                    @if(($listing->seller_account_id ?? $listing->account_id) === auth()->user()->account_id || ($listing->seller_user_id ?? 0) === auth()->id())
                                        <span class="text-[10px] font-bold uppercase text-[#80633B]">My Consignment</span>
                                    @else
                                        <button type="button" @click="openBuyModal({{ $listing->toJson() }})" class="payon-btn-forest py-1.5 px-3 text-xs">Buy</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-[#80633B]">No active listings matching your query.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($listings->hasPages())
                <div class="p-4 border-t border-[#A88950]/40">{{ $listings->links() }}</div>
            @endif
        </div>
    @endif

    <!-- TAB 2: STORAGE -->
    @if($tab === 'storage')
        <div class="payon-ranking-board">
            <div class="payon-wood-header"><span>Account Kafra Storage Vault</span><span class="text-xs font-mono">{{ count($storageItems) }} Entries</span></div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead><tr class="font-cinzel uppercase text-[11px]"><th class="py-3 px-4">Item Name</th><th class="py-3 px-4 text-center">Qty</th><th class="py-3 px-4 text-right">Action</th></tr></thead>
                    <tbody class="divide-y divide-[#A88950]/30">
                        @forelse($storageItems as $sItem)
                            @php
                                $sId = is_array($sItem) ? ($sItem['nameid'] ?? 0) : ($sItem->nameid ?? 0);
                                $sName = is_array($sItem) ? ($sItem['formatted_name'] ?? $sItem['name'] ?? "Item #{$sId}") : ($sItem->formatted_name ?? $sItem->name ?? "Item #{$sId}");
                                $sAmount = is_array($sItem) ? ($sItem['amount'] ?? 0) : ($sItem->amount ?? 0);
                                $sTransferable = is_array($sItem) ? ($sItem['is_transferable'] ?? true) : ($sItem->is_transferable ?? true);
                                $sReason = is_array($sItem) ? ($sItem['restriction_reason'] ?? null) : ($sItem->restriction_reason ?? null);
                            @endphp
                            <tr class="hover:bg-[#E7D8B5]/60">
                                <td class="py-3 px-4 font-bold text-[#35291F]">{{ $sName }} <span class="text-[10px] text-[#80633B] font-mono">#{{ $sId }}</span></td>
                                <td class="py-3 px-4 text-center font-mono font-bold">{{ number_format($sAmount) }}</td>
                                <td class="py-3 px-4 text-right">
                                    @if($sTransferable)
                                        <button type="button" @click="openTransferModal({{ json_encode($sItem) }})" class="payon-btn-forest py-1.5 px-3 text-xs">Deposit into Escrow</button>
                                    @else
                                        <span class="text-[#80633B] italic">{{ $sReason ?? 'Restricted' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-10 text-center text-[#80633B]">No items found in your Kafra storage.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 3: CUSTODY -->
    @if($tab === 'custody')
        <div class="payon-ranking-board">
            <div class="payon-wood-header"><span>Items in Marketplace Custody Vault</span></div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead><tr class="font-cinzel uppercase text-[11px]"><th class="py-3 px-4">Item Name</th><th class="py-3 px-4 text-center">Amount</th><th class="py-3 px-4 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-[#A88950]/30">
                        @forelse($custodyItems as $cItem)
                            <tr class="hover:bg-[#E7D8B5]/60">
                                <td class="py-3 px-4 font-bold text-[#35291F]">{{ $cItem->formatted_name }}</td>
                                <td class="py-3 px-4 text-center font-mono font-bold">{{ number_format($cItem->amount) }}</td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <button type="button" @click="openSellModal({{ json_encode($cItem) }})" class="payon-btn-forest py-1.5 px-3 text-xs">Post Consignment</button>
                                    <form action="{{ route('marketplace.withdraw', $cItem->id) }}" method="POST" class="inline">@csrf<button type="submit" class="payon-btn-gold py-1.5 px-3 text-xs">Withdraw</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-10 text-center text-[#80633B]">No items in custody.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 4: MY LISTINGS -->
    @if($tab === 'my_listings')
        <div class="payon-ranking-board">
            <div class="payon-wood-header"><span>My Active Consignments</span></div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead><tr class="font-cinzel uppercase text-[11px]"><th class="py-3 px-4">Item Name</th><th class="py-3 px-4 text-center">Stock</th><th class="py-3 px-4">Price</th><th class="py-3 px-4 text-right">Action</th></tr></thead>
                    <tbody class="divide-y divide-[#A88950]/30">
                        @forelse($myListings as $mList)
                            <tr class="hover:bg-[#E7D8B5]/60">
                                <td class="py-3 px-4 font-bold text-[#35291F]">{{ $mList->item->formatted_name }}</td>
                                <td class="py-3 px-4 text-center font-mono">{{ number_format($mList->remaining_quantity ?? $mList->quantity ?? $mList->amount ?? 1) }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-[#263D2C]">{{ number_format($mList->unit_price) }} {{ $mList->currency_type === 'zeny' ? 'Z' : 'CP' }}</td>
                                <td class="py-3 px-4 text-right">
                                    <form action="{{ route('marketplace.cancel', $mList->id) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this listing?');">@csrf<button type="submit" class="payon-btn-gold py-1.5 px-3 text-xs">Cancel</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-10 text-center text-[#80633B]">No active consignments.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 5: HISTORY -->
    @if($tab === 'history')
        <div class="payon-ranking-board">
            <div class="payon-wood-header"><span>Account Trade Ledger &amp; History</span></div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead><tr class="font-cinzel uppercase text-[11px]"><th class="py-3 px-4">TX Code</th><th class="py-3 px-4">Item</th><th class="py-3 px-4 text-center">Qty</th><th class="py-3 px-4 text-right">Total</th><th class="py-3 px-4 text-right">Date</th></tr></thead>
                    <tbody class="divide-y divide-[#A88950]/30">
                        @forelse($history as $tx)
                            @php $txItem = $tx->item; @endphp
                            <tr class="hover:bg-[#E7D8B5]/60">
                                <td class="py-3 px-4 font-mono font-bold text-[#263D2C]">{{ $tx->transaction_code ?? $tx->tx_code }}</td>
                                <td class="py-3 px-4 font-bold text-[#35291F]">{{ $txItem ? ($txItem->formatted_name ?? $txItem->item_name) : ($tx->item_name ?? "Item #{$tx->item_id}") }}</td>
                                <td class="py-3 px-4 text-center font-mono">{{ number_format($tx->quantity ?? $tx->amount ?? 1) }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-[#263D2C]">{{ number_format($tx->gross_amount ?? $tx->total_price) }} {{ $tx->currency_type === 'zeny' ? 'Z' : 'CP' }}</td>
                                <td class="py-3 px-4 text-right text-[#80633B] font-mono">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-[#80633B]">No transactions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Modals -->
    <div x-show="buyModalOpen" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75">
        <div @click.away="buyModalOpen = false" class="payon-wood-frame max-w-md w-full overflow-hidden">
            <template x-if="selectedListing">
                <form action="{{ route('marketplace.buy', '0') }}" :action="'{{ url('/marketplace/buy') }}/' + selectedListing.id" method="POST">
                    @csrf
                    <div class="payon-wood-header"><span>Confirm Purchase</span><button type="button" @click="buyModalOpen = false" class="text-[#FFF8E8] cursor-pointer">&times;</button></div>
                    <div class="p-5 space-y-4">
                        <div class="p-3 rounded bg-[#E7D8B5] border border-[#A88950]"><p class="font-bold text-[#35291F]" x-text="selectedListing.item.formatted_name"></p><p class="text-xs text-[#80633B]" x-text="'Seller: ' + selectedListing.seller_name"></p></div>
                        <div><label class="block text-xs font-bold mb-1">Quantity</label><input type="number" name="quantity" x-model.number="buyQuantity" min="1" :max="selectedListing.remaining_quantity || selectedListing.quantity || selectedListing.amount || 1" class="w-full px-3 py-2 rounded text-xs"></div>
                        <div class="flex justify-end gap-2"><button type="button" @click="buyModalOpen = false" class="payon-btn-gold text-xs">Cancel</button><button type="submit" class="payon-btn-forest text-xs">Complete Purchase</button></div>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <div x-show="transferModalOpen" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75">
        <div @click.away="transferModalOpen = false" class="payon-wood-frame max-w-md w-full overflow-hidden">
            <template x-if="selectedStorageItem">
                <form action="{{ route('marketplace.transfer') }}" method="POST">
                    @csrf
                    <input type="hidden" name="storage_id" :value="selectedStorageItem.storage_id">
                    <input type="hidden" name="nameid" :value="selectedStorageItem.nameid">
                    <input type="hidden" name="source" value="storage">
                    <div class="payon-wood-header"><span>Deposit into Custody</span><button type="button" @click="transferModalOpen = false" class="text-[#FFF8E8] cursor-pointer">&times;</button></div>
                    <div class="p-5 space-y-4">
                        <div class="p-3 rounded bg-[#E7D8B5] border border-[#A88950]"><p class="font-bold text-[#35291F]" x-text="selectedStorageItem.formatted_name || selectedStorageItem.name"></p><p class="text-xs text-[#80633B]" x-text="'Available: ' + selectedStorageItem.amount"></p></div>
                        <div><label class="block text-xs font-bold mb-1">Transfer Quantity</label><input type="number" name="amount" x-model.number="transferAmount" min="1" :max="selectedStorageItem.amount" class="w-full px-3 py-2 rounded text-xs"></div>
                        <div class="flex justify-end gap-2"><button type="button" @click="transferModalOpen = false" class="payon-btn-gold text-xs">Cancel</button><button type="submit" class="payon-btn-forest text-xs">Deposit Item</button></div>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <div x-show="listModalOpen" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75">
        <div @click.away="listModalOpen = false" class="payon-wood-frame max-w-md w-full overflow-hidden">
            <template x-if="selectedCustodyItem">
                <form action="{{ route('marketplace.list') }}" method="POST">
                    @csrf
                    <input type="hidden" name="item_id" :value="selectedCustodyItem.id">
                    <input type="hidden" name="custody_item_id" :value="selectedCustodyItem.id">
                    <div class="payon-wood-header"><span>Consign for Sale</span><button type="button" @click="listModalOpen = false" class="text-[#FFF8E8] cursor-pointer">&times;</button></div>
                    <div class="p-5 space-y-4">
                        <div class="p-3 rounded bg-[#E7D8B5] border border-[#A88950]"><p class="font-bold text-[#35291F]" x-text="selectedCustodyItem.formatted_name"></p><p class="text-xs text-[#80633B]" x-text="'Available: ' + selectedCustodyItem.amount"></p></div>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="p-2 rounded border text-xs font-bold cursor-pointer text-center" :class="listCurrency === 'zeny' ? 'bg-[#263D2C] text-[#FFF8E8] border-[#A88950]' : 'bg-[#F2E8CF] text-[#35291F] border-[#A88950]'">
                                <input type="radio" name="currency_type" value="zeny" x-model="listCurrency" class="hidden"> Zeny
                            </label>
                            <label class="p-2 rounded border text-xs font-bold cursor-pointer text-center" :class="listCurrency === 'cashpoints' ? 'bg-[#263D2C] text-[#FFF8E8] border-[#A88950]' : 'bg-[#F2E8CF] text-[#35291F] border-[#A88950]'">
                                <input type="radio" name="currency_type" value="cashpoints" x-model="listCurrency" class="hidden"> Cash Points
                            </label>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div><label class="block text-xs font-bold mb-1">Quantity</label><input type="number" name="quantity" x-model.number="listQuantity" min="1" :max="selectedCustodyItem.amount" class="w-full px-3 py-2 rounded text-xs"></div>
                            <div><label class="block text-xs font-bold mb-1">Price / Unit</label><input type="number" name="unit_price" x-model.number="listUnitPrice" min="1" class="w-full px-3 py-2 rounded text-xs"></div>
                        </div>
                        <div><label class="block text-xs font-bold mb-1">Duration</label><select name="duration_hours" x-model.number="listDuration" class="w-full px-3 py-2 rounded text-xs"><option value="24">24 Hours</option><option value="72">72 Hours</option><option value="168">7 Days</option><option value="336">14 Days</option></select></div>
                        <div class="flex justify-end gap-2"><button type="button" @click="listModalOpen = false" class="payon-btn-gold text-xs">Cancel</button><button type="submit" class="payon-btn-forest text-xs">Post Consignment</button></div>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection
