@extends('layouts.app')

@section('title', 'Player Marketplace & Trade House')

@section('content')
<div class="space-y-6" x-data="{
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
    <!-- Forum-Style Classic Header Panel -->
    <div class="ro-panel overflow-hidden">
        <div class="ro-panel-header justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                <span>Rune-Midgard Player Marketplace</span>
            </div>
            <div class="flex items-center gap-3 text-[11px] font-mono text-[#FFDA89]">
                <span>Zeny: {{ number_format($characterZeny) }} Z</span>
                <span>&bull;</span>
                <span>CP: {{ number_format($accountCashPoints) }}</span>
            </div>
        </div>

        <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-[#FCF7ED]">
            <div class="space-y-1 max-w-2xl">
                <h1 class="text-2xl font-serif font-bold text-[#2D1D13] tracking-tight">
                    Player Marketplace &amp; Trade House
                </h1>
                <p class="text-xs text-[#5C4632]">
                    Secure consignment trading for in-game storage items and currency with verified escrow.
                </p>
            </div>

            <form action="{{ route('marketplace.character') }}" method="POST" class="inline-flex items-center gap-2 shrink-0">
                @csrf
                <span class="text-xs font-bold text-[#3A2A1C]">Character:</span>
                <select name="char_id" onchange="this.form.submit()">
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
               class="px-3.5 py-2 rounded text-xs font-bold border {{ $tab === $tKey ? 'bg-[#315B88] text-white border-[#1D3553]' : 'bg-[#F5EBD5] text-[#3A2A1C] border-[#C8A66A] hover:bg-[#EBDDBF]' }}">
                {{ $tLabel }}
            </a>
        @endforeach
    </div>

    <!-- TAB 1: BROWSE -->
    @if($tab === 'browse')
        @php $listings = $listings ?? $browseListings ?? collect(); @endphp
        <div class="ro-panel p-4 bg-[#FCF7ED]">
            <form action="{{ route('marketplace.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <input type="hidden" name="tab" value="browse">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by item name or ID..." class="flex-1">
                <select name="type" onchange="this.form.submit()">
                    <option value="all">All Categories</option>
                    <option value="Weapon" {{ request('type') === 'Weapon' ? 'selected' : '' }}>Weapons</option>
                    <option value="Armor" {{ request('type') === 'Armor' ? 'selected' : '' }}>Armor &amp; Shields</option>
                    <option value="Card" {{ request('type') === 'Card' ? 'selected' : '' }}>Monster Cards</option>
                    <option value="Consumable" {{ request('type') === 'Consumable' ? 'selected' : '' }}>Consumables</option>
                    <option value="Etc" {{ request('type') === 'Etc' ? 'selected' : '' }}>Materials &amp; Loot</option>
                </select>
                <select name="currency" onchange="this.form.submit()">
                    <option value="all">Any Currency</option>
                    <option value="zeny" {{ request('currency') === 'zeny' ? 'selected' : '' }}>Zeny (Z)</option>
                    <option value="cashpoints" {{ request('currency') === 'cashpoints' ? 'selected' : '' }}>Cash Points (CP)</option>
                </select>
                <button type="submit" class="ro-btn-primary text-xs shrink-0">Search</button>
            </form>
        </div>

        <div class="ro-panel overflow-hidden">
            <div class="ro-panel-header"><span>Active Consignments</span></div>
            <div class="overflow-x-auto bg-[#FCF7ED]">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-[#DECBB1] bg-[#EBDDBF] text-[#3A2A1C] font-serif uppercase text-[11px]">
                            <th class="py-3 px-4">Item</th>
                            <th class="py-3 px-4">Seller</th>
                            <th class="py-3 px-4 text-center">Stock</th>
                            <th class="py-3 px-4">Unit Price</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DECBB1]">
                        @forelse($listings as $listing)
                            <tr class="hover:bg-[#F5EBD5]">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $listing->item->icon_url ?? ('https://static.divine-pride.net/images/items/item/' . $listing->item->nameid . '.png') }}" class="w-7 h-7 object-contain" alt="">
                                        <div>
                                            <span class="font-bold text-[#2D1D13] block">{{ $listing->item->formatted_name }}</span>
                                            <span class="text-[10px] text-[#77644E] font-mono">ID #{{ $listing->item->nameid }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-[#5C4632]">{{ $listing->seller_name }}</td>
                                <td class="py-3 px-4 text-center font-mono font-bold text-[#3A2A1C]">{{ number_format($listing->remaining_quantity ?? $listing->quantity ?? $listing->amount ?? 1) }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-[#315B88]">{{ number_format($listing->unit_price) }} {{ $listing->currency_type === 'zeny' ? 'Z' : 'CP' }}</td>
                                <td class="py-3 px-4 text-right">
                                    @if(($listing->seller_account_id ?? $listing->account_id) === auth()->user()->account_id || ($listing->seller_user_id ?? 0) === auth()->id())
                                        <span class="text-[10px] font-bold uppercase text-[#77644E]">My Consignment</span>
                                    @else
                                        <button type="button" @click="openBuyModal({{ $listing->toJson() }})" class="ro-btn-primary text-xs cursor-pointer">Buy</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-[#77644E]">No active listings matching your query.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($listings->hasPages())
                <div class="p-4 border-t border-[#DECBB1] bg-[#FCF7ED]">{{ $listings->links() }}</div>
            @endif
        </div>
    @endif

    <!-- TAB 2: STORAGE -->
    @if($tab === 'storage')
        <div class="ro-panel overflow-hidden">
            <div class="ro-panel-header justify-between">
                <span>Account Kafra Storage Vault</span>
                <span class="text-[11px] font-mono text-[#FFDA89]">{{ count($storageItems) }} Entries</span>
            </div>
            <div class="overflow-x-auto bg-[#FCF7ED]">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-[#DECBB1] bg-[#EBDDBF] text-[#3A2A1C] font-serif uppercase text-[11px]">
                            <th class="py-3 px-4">Item Name</th>
                            <th class="py-3 px-4 text-center">Quantity</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DECBB1]">
                        @forelse($storageItems as $sItem)
                            @php
                                $sId = is_array($sItem) ? ($sItem['nameid'] ?? 0) : ($sItem->nameid ?? 0);
                                $sName = is_array($sItem) ? ($sItem['formatted_name'] ?? $sItem['name'] ?? "Item #{$sId}") : ($sItem->formatted_name ?? $sItem->name ?? "Item #{$sId}");
                                $sAmount = is_array($sItem) ? ($sItem['amount'] ?? 0) : ($sItem->amount ?? 0);
                                $sTransferable = is_array($sItem) ? ($sItem['is_transferable'] ?? true) : ($sItem->is_transferable ?? true);
                                $sReason = is_array($sItem) ? ($sItem['restriction_reason'] ?? null) : ($sItem->restriction_reason ?? null);
                            @endphp
                            <tr class="hover:bg-[#F5EBD5]">
                                <td class="py-3 px-4 font-bold text-[#2D1D13]">{{ $sName }} <span class="text-[10px] text-[#77644E] font-mono">#{{ $sId }}</span></td>
                                <td class="py-3 px-4 text-center font-mono font-bold">{{ number_format($sAmount) }}</td>
                                <td class="py-3 px-4 text-right">
                                    @if($sTransferable)
                                        <button type="button" @click="openTransferModal({{ json_encode($sItem) }})" class="ro-btn-primary text-xs cursor-pointer">Deposit into Escrow</button>
                                    @else
                                        <span class="text-[#77644E] italic">{{ $sReason ?? 'Restricted' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-10 text-center text-[#77644E]">No items found in your Kafra storage.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 3: CUSTODY -->
    @if($tab === 'custody')
        <div class="ro-panel overflow-hidden">
            <div class="ro-panel-header"><span>Items in Marketplace Custody Vault</span></div>
            <div class="overflow-x-auto bg-[#FCF7ED]">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-[#DECBB1] bg-[#EBDDBF] text-[#3A2A1C] font-serif uppercase text-[11px]">
                            <th class="py-3 px-4">Item Name</th>
                            <th class="py-3 px-4 text-center">Amount</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DECBB1]">
                        @forelse($custodyItems as $cItem)
                            <tr class="hover:bg-[#F5EBD5]">
                                <td class="py-3 px-4 font-bold text-[#2D1D13]">{{ $cItem->formatted_name }}</td>
                                <td class="py-3 px-4 text-center font-mono font-bold">{{ number_format($cItem->amount) }}</td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <button type="button" @click="openSellModal({{ json_encode($cItem) }})" class="ro-btn-primary text-xs cursor-pointer">Post Consignment</button>
                                    <form action="{{ route('marketplace.withdraw', $cItem->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="ro-btn-gold text-xs cursor-pointer">Withdraw</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-10 text-center text-[#77644E]">No items in custody.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 4: MY LISTINGS -->
    @if($tab === 'my_listings')
        <div class="ro-panel overflow-hidden">
            <div class="ro-panel-header"><span>My Active Consignments</span></div>
            <div class="overflow-x-auto bg-[#FCF7ED]">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-[#DECBB1] bg-[#EBDDBF] text-[#3A2A1C] font-serif uppercase text-[11px]">
                            <th class="py-3 px-4">Item Name</th>
                            <th class="py-3 px-4 text-center">Stock</th>
                            <th class="py-3 px-4">Price</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DECBB1]">
                        @forelse($myListings as $mList)
                            <tr class="hover:bg-[#F5EBD5]">
                                <td class="py-3 px-4 font-bold text-[#2D1D13]">{{ $mList->item->formatted_name }}</td>
                                <td class="py-3 px-4 text-center font-mono">{{ number_format($mList->remaining_quantity ?? $mList->quantity ?? $mList->amount ?? 1) }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-[#315B88]">{{ number_format($mList->unit_price) }} {{ $mList->currency_type === 'zeny' ? 'Z' : 'CP' }}</td>
                                <td class="py-3 px-4 text-right">
                                    <form action="{{ route('marketplace.cancel', $mList->id) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this listing?');">
                                        @csrf
                                        <button type="submit" class="ro-btn-gold text-xs cursor-pointer">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-10 text-center text-[#77644E]">No active consignments.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 5: HISTORY -->
    @if($tab === 'history')
        <div class="ro-panel overflow-hidden">
            <div class="ro-panel-header"><span>Account Trade Ledger &amp; History</span></div>
            <div class="overflow-x-auto bg-[#FCF7ED]">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-[#DECBB1] bg-[#EBDDBF] text-[#3A2A1C] font-serif uppercase text-[11px]">
                            <th class="py-3 px-4">TX Code</th>
                            <th class="py-3 px-4">Item</th>
                            <th class="py-3 px-4 text-center">Qty</th>
                            <th class="py-3 px-4 text-right">Total</th>
                            <th class="py-3 px-4 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DECBB1]">
                        @forelse($history as $tx)
                            @php $txItem = $tx->item; @endphp
                            <tr class="hover:bg-[#F5EBD5]">
                                <td class="py-3 px-4 font-mono font-bold text-[#315B88]">{{ $tx->transaction_code ?? $tx->tx_code }}</td>
                                <td class="py-3 px-4 font-bold text-[#2D1D13]">{{ $txItem ? ($txItem->formatted_name ?? $txItem->item_name) : ($tx->item_name ?? "Item #{$tx->item_id}") }}</td>
                                <td class="py-3 px-4 text-center font-mono">{{ number_format($tx->quantity ?? $tx->amount ?? 1) }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-[#315B88]">{{ number_format($tx->gross_amount ?? $tx->total_price) }} {{ $tx->currency_type === 'zeny' ? 'Z' : 'CP' }}</td>
                                <td class="py-3 px-4 text-right text-[#77644E] font-mono">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-[#77644E]">No transactions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- BUY MODAL -->
    <div x-show="buyModalOpen" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75">
        <div @click.away="buyModalOpen = false" class="ro-panel max-w-md w-full overflow-hidden">
            <template x-if="selectedListing">
                <form action="{{ route('marketplace.buy', '0') }}" :action="'{{ url('/marketplace/buy') }}/' + selectedListing.id" method="POST">
                    @csrf
                    <div class="ro-panel-header justify-between">
                        <span>Confirm Purchase</span>
                        <button type="button" @click="buyModalOpen = false" class="text-[#FFDA89] cursor-pointer">&times;</button>
                    </div>
                    <div class="p-5 space-y-4 bg-[#FCF7ED]">
                        <div class="ro-inner-box">
                            <p class="font-bold text-[#2D1D13]" x-text="selectedListing.item.formatted_name"></p>
                            <p class="text-xs text-[#5C4632]" x-text="'Seller: ' + selectedListing.seller_name"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#3A2A1C] mb-1">Quantity</label>
                            <input type="number" name="quantity" x-model.number="buyQuantity" min="1" :max="selectedListing.remaining_quantity || selectedListing.quantity || selectedListing.amount || 1" class="w-full">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="buyModalOpen = false" class="ro-btn-gold text-xs cursor-pointer">Cancel</button>
                            <button type="submit" class="ro-btn-primary text-xs cursor-pointer">Complete Purchase</button>
                        </div>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- TRANSFER MODAL -->
    <div x-show="transferModalOpen" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75">
        <div @click.away="transferModalOpen = false" class="ro-panel max-w-md w-full overflow-hidden">
            <template x-if="selectedStorageItem">
                <form action="{{ route('marketplace.transfer') }}" method="POST">
                    @csrf
                    <input type="hidden" name="storage_id" :value="selectedStorageItem.storage_id">
                    <input type="hidden" name="nameid" :value="selectedStorageItem.nameid">
                    <input type="hidden" name="source" value="storage">
                    <div class="ro-panel-header justify-between">
                        <span>Deposit into Custody</span>
                        <button type="button" @click="transferModalOpen = false" class="text-[#FFDA89] cursor-pointer">&times;</button>
                    </div>
                    <div class="p-5 space-y-4 bg-[#FCF7ED]">
                        <div class="ro-inner-box">
                            <p class="font-bold text-[#2D1D13]" x-text="selectedStorageItem.formatted_name || selectedStorageItem.name"></p>
                            <p class="text-xs text-[#5C4632]" x-text="'Available: ' + selectedStorageItem.amount"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#3A2A1C] mb-1">Transfer Quantity</label>
                            <input type="number" name="amount" x-model.number="transferAmount" min="1" :max="selectedStorageItem.amount" class="w-full">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="transferModalOpen = false" class="ro-btn-gold text-xs cursor-pointer">Cancel</button>
                            <button type="submit" class="ro-btn-primary text-xs cursor-pointer">Deposit Item</button>
                        </div>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- SELL MODAL -->
    <div x-show="listModalOpen" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75">
        <div @click.away="listModalOpen = false" class="ro-panel max-w-md w-full overflow-hidden">
            <template x-if="selectedCustodyItem">
                <form action="{{ route('marketplace.list') }}" method="POST">
                    @csrf
                    <input type="hidden" name="item_id" :value="selectedCustodyItem.id">
                    <input type="hidden" name="custody_item_id" :value="selectedCustodyItem.id">
                    <div class="ro-panel-header justify-between">
                        <span>Consign for Sale</span>
                        <button type="button" @click="listModalOpen = false" class="text-[#FFDA89] cursor-pointer">&times;</button>
                    </div>
                    <div class="p-5 space-y-4 bg-[#FCF7ED]">
                        <div class="ro-inner-box">
                            <p class="font-bold text-[#2D1D13]" x-text="selectedCustodyItem.formatted_name"></p>
                            <p class="text-xs text-[#5C4632]" x-text="'Available: ' + selectedCustodyItem.amount"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="p-2 rounded border text-xs font-bold cursor-pointer text-center" :class="listCurrency === 'zeny' ? 'bg-[#315B88] text-white border-[#1D3553]' : 'bg-[#F5EBD5] text-[#3A2A1C] border-[#C8A66A]'">
                                <input type="radio" name="currency_type" value="zeny" x-model="listCurrency" class="hidden"> Zeny
                            </label>
                            <label class="p-2 rounded border text-xs font-bold cursor-pointer text-center" :class="listCurrency === 'cashpoints' ? 'bg-[#315B88] text-white border-[#1D3553]' : 'bg-[#F5EBD5] text-[#3A2A1C] border-[#C8A66A]'">
                                <input type="radio" name="currency_type" value="cashpoints" x-model="listCurrency" class="hidden"> Cash Points
                            </label>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-[#3A2A1C] mb-1">Quantity</label>
                                <input type="number" name="quantity" x-model.number="listQuantity" min="1" :max="selectedCustodyItem.amount" class="w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#3A2A1C] mb-1">Price / Unit</label>
                                <input type="number" name="unit_price" x-model.number="listUnitPrice" min="1" class="w-full">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#3A2A1C] mb-1">Duration</label>
                            <select name="duration_hours" x-model.number="listDuration" class="w-full">
                                <option value="24">24 Hours</option>
                                <option value="72">72 Hours</option>
                                <option value="168">7 Days</option>
                                <option value="336">14 Days</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="listModalOpen = false" class="ro-btn-gold text-xs cursor-pointer">Cancel</button>
                            <button type="submit" class="ro-btn-primary text-xs cursor-pointer">Post Consignment</button>
                        </div>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection
