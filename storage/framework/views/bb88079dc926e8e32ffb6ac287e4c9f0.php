<?php $__env->startSection('title', 'Player Marketplace - Aether Trade House'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{
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
    listDuration: <?php echo e($settings['default_duration_hours']); ?>,

    taxPercent: <?php echo e($settings['tax_percent']); ?>,

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
    <!-- Top Trade House Header & Character Bar -->
    <div class="bg-[#1C1525] border-2 border-[#B69A65]/60 rounded-2xl shadow-2xl p-5 relative overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
            <!-- Title -->
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#E5B358] to-[#806442] p-0.5 shadow-lg shrink-0 flex items-center justify-center text-[#17111F]">
                    <i data-lucide="scale" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold font-cinzel text-[#FCE69B] tracking-wide flex items-center gap-2">
                        Aether Trade House
                    </h1>
                    <p class="text-xs text-[#C8BDAA]">Secure player-to-player escrow trading across Midgard.</p>
                </div>
            </div>

            <!-- Active Character & Currency Bar -->
            <div class="flex flex-wrap items-center gap-4 bg-[#140E1B] border border-[#4A3A5A] rounded-xl p-3">
                <!-- Character Dropdown Switcher -->
                <form action="<?php echo e(route('marketplace.character')); ?>" method="POST" class="flex items-center gap-2">
                    <?php echo csrf_field(); ?>
                    <label class="text-[11px] font-bold text-[#E5B358] uppercase tracking-wider font-cinzel shrink-0">Adventurer:</label>
                    <select name="char_id" onchange="this.form.submit()" class="bg-[#241A2F] text-white text-xs font-bold rounded-lg border border-[#4A3A5A] px-2.5 py-1.5 focus:border-[#D4AF37]">
                        <?php $__currentLoopData = $characters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->char_id); ?>" <?php echo e($activeChar->char_id == $c->char_id ? 'selected' : ''); ?>>
                                <?php echo e($c->name); ?> (Lv.<?php echo e($c->base_level); ?>/<?php echo e($c->job_level); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>

                <div class="h-6 w-px bg-[#3E2E4E] hidden sm:block"></div>

                <!-- Live Zeny -->
                <div class="flex items-center gap-1.5 text-xs font-bold text-amber-300">
                    <i data-lucide="coins" class="w-4 h-4 text-amber-400"></i>
                    <span><?php echo e(number_format($characterZeny)); ?></span>
                    <span class="text-[10px] text-amber-400/80">Zeny</span>
                </div>

                <div class="h-6 w-px bg-[#3E2E4E] hidden sm:block"></div>

                <!-- Live Cash Points -->
                <div class="flex items-center gap-1.5 text-xs font-bold text-purple-300">
                    <i data-lucide="gem" class="w-4 h-4 text-purple-400"></i>
                    <span><?php echo e(number_format($accountCashPoints)); ?></span>
                    <span class="text-[10px] text-purple-400/80">Cash Points</span>
                </div>
            </div>
        </div>

        <?php if(!empty($settings['announcement'])): ?>
            <div class="mt-4 pt-3 border-t border-[#3E2E4E]/80 text-xs text-[#EDE4D2] flex items-center gap-2">
                <i data-lucide="bell" class="w-4 h-4 text-[#D4AF37] shrink-0"></i>
                <span class="italic text-[#C8BDAA]"><?php echo e($settings['announcement']); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Alert Notifications -->
    <?php if(session('success')): ?>
        <div class="p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0 text-emerald-400"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="p-4 rounded-xl bg-rose-500/15 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 text-rose-400"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Trade House Tabs Navigation -->
    <div class="flex items-center gap-2 border-b-2 border-[#3E2E4E] pb-2 overflow-x-auto">
        <a href="<?php echo e(route('marketplace.index', ['tab' => 'browse'])); ?>" 
           class="px-4 py-2.5 rounded-xl text-xs font-bold font-cinzel transition-all flex items-center gap-2 whitespace-nowrap <?php echo e($tab === 'browse' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/60 shadow-md' : 'text-[#C8BDAA] hover:text-white bg-[#1C1525] border border-transparent'); ?>">
            <i data-lucide="search" class="w-4 h-4 text-[#D4AF37]"></i>
            Browse Market
        </a>

        <a href="<?php echo e(route('marketplace.index', ['tab' => 'storage'])); ?>" 
           class="px-4 py-2.5 rounded-xl text-xs font-bold font-cinzel transition-all flex items-center gap-2 whitespace-nowrap <?php echo e($tab === 'storage' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/60 shadow-md' : 'text-[#C8BDAA] hover:text-white bg-[#1C1525] border border-transparent'); ?>">
            <i data-lucide="box" class="w-4 h-4 text-[#D4AF37]"></i>
            Kafra Storage (Deposit)
        </a>

        <a href="<?php echo e(route('marketplace.index', ['tab' => 'custody'])); ?>" 
           class="px-4 py-2.5 rounded-xl text-xs font-bold font-cinzel transition-all flex items-center gap-2 whitespace-nowrap <?php echo e($tab === 'custody' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/60 shadow-md' : 'text-[#C8BDAA] hover:text-white bg-[#1C1525] border border-transparent'); ?>">
            <i data-lucide="package-check" class="w-4 h-4 text-[#D4AF37]"></i>
            My Custody &amp; Sell
        </a>

        <a href="<?php echo e(route('marketplace.index', ['tab' => 'my_listings'])); ?>" 
           class="px-4 py-2.5 rounded-xl text-xs font-bold font-cinzel transition-all flex items-center gap-2 whitespace-nowrap <?php echo e($tab === 'my_listings' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/60 shadow-md' : 'text-[#C8BDAA] hover:text-white bg-[#1C1525] border border-transparent'); ?>">
            <i data-lucide="tag" class="w-4 h-4 text-[#D4AF37]"></i>
            My Active Listings
        </a>

        <a href="<?php echo e(route('marketplace.index', ['tab' => 'history'])); ?>" 
           class="px-4 py-2.5 rounded-xl text-xs font-bold font-cinzel transition-all flex items-center gap-2 whitespace-nowrap <?php echo e($tab === 'history' ? 'bg-[#D4AF37]/20 text-[#FCE69B] border border-[#D4AF37]/60 shadow-md' : 'text-[#C8BDAA] hover:text-white bg-[#1C1525] border border-transparent'); ?>">
            <i data-lucide="scroll" class="w-4 h-4 text-[#D4AF37]"></i>
            Trade Ledger &amp; History
        </a>
    </div>

    <!-- ==================== TAB 1: BROWSE LISTINGS ==================== -->
    <?php if($tab === 'browse'): ?>
        <div class="space-y-4">
            <!-- Search & Filters Toolbar -->
            <div class="bg-[#1C1525] border border-[#3E2E4E] rounded-2xl p-4">
                <form action="<?php echo e(route('marketplace.index')); ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <input type="hidden" name="tab" value="browse">

                    <!-- Keyword Search -->
                    <div class="lg:col-span-2">
                        <label class="text-[10px] font-bold text-[#E5B358] uppercase tracking-wider block mb-1">Search Item</label>
                        <div class="relative">
                            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search by name or Item ID..." class="w-full pl-9 pr-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="text-[10px] font-bold text-[#E5B358] uppercase tracking-wider block mb-1">Category</label>
                        <select name="type" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                            <option value="all">All Categories</option>
                            <option value="Weapon" <?php echo e(request('type') === 'Weapon' ? 'selected' : ''); ?>>Weapons</option>
                            <option value="Armor" <?php echo e(request('type') === 'Armor' ? 'selected' : ''); ?>>Armor &amp; Shields</option>
                            <option value="Card" <?php echo e(request('type') === 'Card' ? 'selected' : ''); ?>>Cards</option>
                            <option value="Consumable" <?php echo e(request('type') === 'Consumable' ? 'selected' : ''); ?>>Consumables</option>
                            <option value="Etc" <?php echo e(request('type') === 'Etc' ? 'selected' : ''); ?>>Materials &amp; Etc</option>
                        </select>
                    </div>

                    <!-- Currency Filter -->
                    <div>
                        <label class="text-[10px] font-bold text-[#E5B358] uppercase tracking-wider block mb-1">Currency</label>
                        <select name="currency" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                            <option value="all">Any Currency</option>
                            <option value="zeny" <?php echo e(request('currency') === 'zeny' ? 'selected' : ''); ?>>Zeny Only</option>
                            <option value="cashpoints" <?php echo e(request('currency') === 'cashpoints' ? 'selected' : ''); ?>>Cash Points Only</option>
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="filter" class="w-4 h-4"></i>
                            Filter Market
                        </button>
                    </div>
                </form>
            </div>

            <!-- Trade House Listings Table -->
            <div class="bg-[#1C1525] border-2 border-[#3E2E4E] rounded-2xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB] font-cinzel">
                                <th class="py-3.5 px-4">Item Description</th>
                                <th class="py-3.5 px-4">Seller</th>
                                <th class="py-3.5 px-4">Stock</th>
                                <th class="py-3.5 px-4">Price / Unit</th>
                                <th class="py-3.5 px-4">Total Buyout</th>
                                <th class="py-3.5 px-4">Time Left</th>
                                <th class="py-3.5 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            <?php $__empty_1 = true; $__currentLoopData = $browseListings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-[#251A33]/60 transition-colors">
                                    <!-- Item Description -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="relative shrink-0">
                                                <img src="<?php echo e($listing->item->icon_url); ?>" alt="Item" class="w-9 h-9 object-contain rounded-lg bg-[#140E1B] border border-[#4A3A5A] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                                <?php if($listing->item->refine > 0): ?>
                                                    <span class="absolute -top-1.5 -right-1.5 px-1 py-0.2 rounded text-[9px] font-bold bg-[#D4AF37] text-black shadow">
                                                        +<?php echo e($listing->item->refine); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-white text-sm font-cinzel"><?php echo e($listing->item->formatted_name); ?></h4>
                                                <p class="text-[10px] text-slate-400">ID #<?php echo e($listing->item->nameid); ?> • <?php echo e($listing->item->item_type); ?></p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Seller -->
                                    <td class="py-3.5 px-4">
                                        <p class="font-medium text-[#EDE4D2]"><?php echo e($listing->seller_char_name); ?></p>
                                    </td>

                                    <!-- Stock -->
                                    <td class="py-3.5 px-4 font-mono font-bold text-white">
                                        <?php echo e(number_format($listing->remaining_quantity)); ?>

                                    </td>

                                    <!-- Unit Price -->
                                    <td class="py-3.5 px-4">
                                        <?php if($listing->currency_type === 'zeny'): ?>
                                            <span class="inline-flex items-center gap-1 font-bold text-amber-300">
                                                <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-400"></i>
                                                <?php echo e(number_format($listing->unit_price)); ?> Z
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 font-bold text-purple-300">
                                                <i data-lucide="gem" class="w-3.5 h-3.5 text-purple-400"></i>
                                                <?php echo e(number_format($listing->unit_price)); ?> CP
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Total Buyout -->
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold text-white font-mono">
                                            <?php echo e(number_format($listing->unit_price * $listing->remaining_quantity)); ?>

                                        </span>
                                    </td>

                                    <!-- Time Left -->
                                    <td class="py-3.5 px-4 text-slate-400">
                                        <?php echo e($listing->remaining_time); ?>

                                    </td>

                                    <!-- Buy Button -->
                                    <td class="py-3.5 px-4 text-right">
                                        <?php if($listing->seller_user_id === auth()->id()): ?>
                                            <span class="text-slate-500 text-[11px] italic">Your Listing</span>
                                        <?php else: ?>
                                            <button @click="openBuyModal(<?php echo e(json_encode($listing)); ?>)" 
                                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110 transition-all inline-flex items-center gap-1.5">
                                                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                                Buy Item
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                        No active items are currently listed in the Trade House.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($browseListings->hasPages()): ?>
                    <div class="p-4 border-t border-[#3E2E4E]">
                        <?php echo e($browseListings->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- ==================== TAB 2: DEPOSIT FROM STORAGE ==================== -->
    <?php if($tab === 'storage'): ?>
        <div class="space-y-4">
            <div class="bg-[#1C1525] border border-[#3E2E4E] rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white font-cinzel">Kafra Storage Inventory</h3>
                    <p class="text-xs text-[#C8BDAA]">Select an item from your in-game storage to transfer into Trade House custody escrow.</p>
                </div>
                <div class="text-[11px] text-[#E5B358] bg-[#140E1B] px-3 py-1.5 rounded-lg border border-[#4A3A5A]">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5 inline mr-1"></i> Character must be offline to deposit
                </div>
            </div>

            <div class="bg-[#1C1525] border-2 border-[#3E2E4E] rounded-2xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB] font-cinzel">
                                <th class="py-3.5 px-4">Item</th>
                                <th class="py-3.5 px-4">Amount in Storage</th>
                                <th class="py-3.5 px-4">Category</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            <?php $__empty_1 = true; $__currentLoopData = $storageItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-[#251A33]/60 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <img src="<?php echo e($sItem['icon_url']); ?>" alt="Item" class="w-8 h-8 object-contain rounded bg-[#140E1B] border border-[#4A3A5A] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <p class="font-bold text-white">
                                                    <?php echo e($sItem['refine'] > 0 ? '+'.$sItem['refine'].' ' : ''); ?><?php echo e($sItem['name']); ?>

                                                </p>
                                                <p class="text-[10px] text-slate-400">ID #<?php echo e($sItem['nameid']); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-white font-mono">
                                        <?php echo e(number_format($sItem['amount'])); ?>

                                    </td>
                                    <td class="py-3.5 px-4 text-slate-300">
                                        <?php echo e($sItem['type']); ?>

                                    </td>
                                    <td class="py-3.5 px-4">
                                        <?php if($sItem['is_transferable']): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Transferable</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30" title="<?php echo e($sItem['restriction_reason']); ?>">Restricted</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <?php if($sItem['is_transferable']): ?>
                                            <button @click="openTransferModal(<?php echo e(json_encode($sItem)); ?>)" 
                                                    class="px-3 py-1.5 rounded-xl text-xs font-bold font-cinzel bg-[#D4AF37]/20 text-[#FCE69B] hover:bg-[#D4AF37]/30 border border-[#D4AF37]/40 transition-all inline-flex items-center gap-1.5">
                                                <i data-lucide="arrow-right-circle" class="w-3.5 h-3.5"></i>
                                                Deposit Item
                                            </button>
                                        <?php else: ?>
                                            <span class="text-slate-500 text-[11px] italic"><?php echo e($sItem['restriction_reason'] ?? 'Not tradeable'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                        Your Kafra storage currently contains no items.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ==================== TAB 3: CUSTODY INVENTORY & SELL ==================== -->
    <?php if($tab === 'custody'): ?>
        <div class="space-y-4">
            <div class="bg-[#1C1525] border border-[#3E2E4E] rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white font-cinzel">Trade House Custody (Escrow)</h3>
                    <p class="text-xs text-[#C8BDAA]">These items are securely held in Trade House custody. You may list them for sale or withdraw them back to Kafra storage.</p>
                </div>
            </div>

            <div class="bg-[#1C1525] border-2 border-[#3E2E4E] rounded-2xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB] font-cinzel">
                                <th class="py-3.5 px-4">Item</th>
                                <th class="py-3.5 px-4">Quantity in Custody</th>
                                <th class="py-3.5 px-4">Category</th>
                                <th class="py-3.5 px-4">Deposited At</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            <?php $__empty_1 = true; $__currentLoopData = $custodyItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-[#251A33]/60 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <img src="<?php echo e($cItem->icon_url); ?>" alt="Item" class="w-8 h-8 object-contain rounded bg-[#140E1B] border border-[#4A3A5A] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <p class="font-bold text-white"><?php echo e($cItem->formatted_name); ?></p>
                                                <p class="text-[10px] text-slate-400">ID #<?php echo e($cItem->nameid); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-white font-mono">
                                        <?php echo e(number_format($cItem->amount)); ?>

                                    </td>
                                    <td class="py-3.5 px-4 text-slate-300">
                                        <?php echo e($cItem->item_type); ?>

                                    </td>
                                    <td class="py-3.5 px-4 text-slate-400">
                                        <?php echo e($cItem->deposited_at->format('M d, Y H:i')); ?>

                                    </td>
                                    <td class="py-3.5 px-4 text-right space-x-2">
                                        <button @click="openSellModal(<?php echo e(json_encode($cItem)); ?>)" 
                                                class="px-3 py-1.5 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110 transition-all inline-flex items-center gap-1.5">
                                            <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                                            List for Sale
                                        </button>

                                        <form action="<?php echo e(route('marketplace.withdraw', $cItem->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Withdraw this item back to your in-game Kafra storage?');">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold font-cinzel bg-[#251A33] text-slate-300 hover:text-white hover:bg-[#352643] border border-[#4A3A5A] transition-all inline-flex items-center gap-1">
                                                <i data-lucide="arrow-left-circle" class="w-3.5 h-3.5"></i>
                                                Withdraw
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                        You currently have no items deposited in Trade House custody.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ==================== TAB 4: MY ACTIVE LISTINGS ==================== -->
    <?php if($tab === 'my_listings'): ?>
        <div class="space-y-4">
            <div class="bg-[#1C1525] border border-[#3E2E4E] rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white font-cinzel">My Active &amp; Recent Listings</h3>
                    <p class="text-xs text-[#C8BDAA]">Track your current items on the market, monitor countdowns, and cancel unsold listings.</p>
                </div>
            </div>

            <div class="bg-[#1C1525] border-2 border-[#3E2E4E] rounded-2xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#3E2E4E] bg-[#241A2F]/90 text-[#F3E5AB] font-cinzel">
                                <th class="py-3.5 px-4">Code</th>
                                <th class="py-3.5 px-4">Item</th>
                                <th class="py-3.5 px-4">Price / Unit</th>
                                <th class="py-3.5 px-4">Stock</th>
                                <th class="py-3.5 px-4">Expires In</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#352643]">
                            <?php $__empty_1 = true; $__currentLoopData = $myListings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mListing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-[#251A33]/60 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-[#E5B358]"><?php echo e($mListing->listing_code); ?></td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <img src="<?php echo e($mListing->item->icon_url); ?>" alt="Item" class="w-8 h-8 object-contain rounded bg-[#140E1B] border border-[#4A3A5A] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <p class="font-bold text-white"><?php echo e($mListing->item->formatted_name); ?></p>
                                                <p class="text-[10px] text-slate-400">ID #<?php echo e($mListing->item->nameid); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <?php if($mListing->currency_type === 'zeny'): ?>
                                            <span class="inline-flex items-center gap-1 font-bold text-amber-300">
                                                <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-400"></i>
                                                <?php echo e(number_format($mListing->unit_price)); ?> Z
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 font-bold text-purple-300">
                                                <i data-lucide="gem" class="w-3.5 h-3.5 text-purple-400"></i>
                                                <?php echo e(number_format($mListing->unit_price)); ?> CP
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-white font-mono">
                                        <?php echo e($mListing->remaining_quantity); ?> / <?php echo e($mListing->quantity); ?>

                                    </td>
                                    <td class="py-3.5 px-4 text-slate-400">
                                        <?php echo e($mListing->remaining_time); ?>

                                    </td>
                                    <td class="py-3.5 px-4">
                                        <?php if($mListing->status === 'active'): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Active</span>
                                        <?php elseif($mListing->status === 'sold'): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">Sold</span>
                                        <?php elseif($mListing->status === 'cancelled'): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Cancelled</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-500/20 text-slate-300 border border-slate-500/30">Expired</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <?php if($mListing->status === 'active'): ?>
                                            <form action="<?php echo e(route('marketplace.cancel', $mListing->id)); ?>" method="POST" onsubmit="return confirm('Cancel this listing and return the item to your custody inventory?');" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="px-3 py-1 rounded-lg text-[11px] font-bold bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 border border-rose-500/40 transition-all">
                                                    Cancel Listing
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-slate-500 text-[11px]">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                        You currently have no active listings in the Trade House.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($myListings->hasPages()): ?>
                    <div class="p-4 border-t border-[#3E2E4E]">
                        <?php echo e($myListings->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- ==================== TAB 5: TRADE HISTORY ==================== -->
    <?php if($tab === 'history'): ?>
        <div class="space-y-4">
            <div class="bg-[#1C1525] border border-[#3E2E4E] rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white font-cinzel">Transaction Ledger &amp; Audit Trail</h3>
                    <p class="text-xs text-[#C8BDAA]">Comprehensive record of all your purchases and sales completed in the Trade House.</p>
                </div>
            </div>

            <div class="bg-[#1C1525] border-2 border-[#3E2E4E] rounded-2xl overflow-hidden shadow-xl">
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
                            <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $isBuyer = ($tx->buyer_user_id === auth()->id());
                                ?>
                                <tr class="hover:bg-[#251A33]/60 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-[#E5B358]"><?php echo e($tx->transaction_code); ?></td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <img src="<?php echo e($tx->item->icon_url); ?>" alt="Item" class="w-7 h-7 object-contain rounded bg-[#140E1B] border border-[#4A3A5A] p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <span class="font-bold text-white"><?php echo e($tx->quantity); ?>x <?php echo e($tx->item->formatted_name); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <?php if($isBuyer): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">Buyer</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">Seller</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-300">
                                        <?php echo e($isBuyer ? $tx->seller_char_name : $tx->buyer_char_name); ?>

                                    </td>
                                    <td class="py-3.5 px-4 font-bold <?php echo e($tx->currency_type === 'zeny' ? 'text-amber-300' : 'text-purple-300'); ?>">
                                        <?php echo e(number_format($tx->gross_amount)); ?> <?php echo e($tx->currency_type === 'zeny' ? 'Z' : 'CP'); ?>

                                    </td>
                                    <td class="py-3.5 px-4 text-rose-300 font-mono">
                                        -<?php echo e(number_format($tx->tax_amount)); ?>

                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-emerald-300 font-mono">
                                        <?php echo e(number_format($isBuyer ? $tx->gross_amount : $tx->net_seller_proceeds)); ?>

                                    </td>
                                    <td class="py-3.5 px-4 text-slate-400">
                                        <?php echo e($tx->created_at->format('M d, Y H:i')); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                        No transactions have been recorded under your account yet.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($history->hasPages()): ?>
                    <div class="p-4 border-t border-[#3E2E4E]">
                        <?php echo e($history->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

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
                <button @click="buyModalOpen = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <template x-if="selectedListing">
                <form :action="'/marketplace/buy/' + selectedListing.id" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <!-- Item Overview -->
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-[#140E1B] border border-[#352643]">
                        <img :src="selectedListing.item.icon_url" class="w-10 h-10 object-contain rounded bg-[#251A33] p-1 border border-[#4A3A5A]">
                        <div>
                            <h4 class="font-bold text-white font-cinzel" x-text="selectedListing.item.formatted_name"></h4>
                            <p class="text-[11px] text-slate-400">Seller: <span class="text-[#E5B358]" x-text="selectedListing.seller_char_name"></span></p>
                        </div>
                    </div>

                    <!-- Quantity Input -->
                    <div>
                        <label class="text-xs font-bold text-slate-300 block mb-1">Purchase Quantity</label>
                        <input type="number" name="quantity" x-model.number="buyQuantity" min="1" :max="selectedListing.remaining_quantity" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                        <span class="text-[10px] text-slate-400 mt-0.5 block" x-text="'Available Stock: ' + selectedListing.remaining_quantity"></span>
                    </div>

                    <!-- Price Calculations -->
                    <div class="p-3 rounded-xl bg-[#140E1B] border border-[#352643] space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Price Per Unit:</span>
                            <span class="font-bold text-white" x-text="new Intl.NumberFormat().format(selectedListing.unit_price) + ' ' + (selectedListing.currency_type === 'zeny' ? 'Zeny' : 'Cash Points')"></span>
                        </div>
                        <div class="flex justify-between text-[#E5B358] font-bold text-sm pt-1 border-t border-[#352643]">
                            <span>Total Buyout Price:</span>
                            <span x-text="new Intl.NumberFormat().format(selectedListing.unit_price * buyQuantity) + ' ' + (selectedListing.currency_type === 'zeny' ? 'Z' : 'CP')"></span>
                        </div>
                    </div>

                    <div class="text-[11px] text-emerald-400/90 flex items-center gap-1.5 bg-emerald-500/10 p-2 rounded-lg border border-emerald-500/20">
                        <i data-lucide="box" class="w-4 h-4 shrink-0"></i>
                        <span>Purchased items are automatically delivered to your Kafra Storage.</span>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="buyModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#251A33] text-slate-300 hover:text-white border border-[#4A3A5A]">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110">Confirm Buyout</button>
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
                <button @click="transferModalOpen = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <template x-if="selectedStorageItem">
                <form action="<?php echo e(route('marketplace.transfer')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="storage_id" :value="selectedStorageItem.storage_id">

                    <div class="flex items-center gap-3 p-3 rounded-xl bg-[#140E1B] border border-[#352643]">
                        <img :src="selectedStorageItem.icon_url" class="w-10 h-10 object-contain rounded bg-[#251A33] p-1 border border-[#4A3A5A]">
                        <div>
                            <h4 class="font-bold text-white font-cinzel" x-text="selectedStorageItem.name"></h4>
                            <p class="text-[11px] text-slate-400" x-text="'Category: ' + selectedStorageItem.type"></p>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-300 block mb-1">Transfer Quantity</label>
                        <input type="number" name="amount" x-model.number="transferAmount" min="1" :max="selectedStorageItem.amount" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                        <span class="text-[10px] text-slate-400 mt-0.5 block" x-text="'Available in storage: ' + selectedStorageItem.amount"></span>
                    </div>

                    <div class="text-[11px] text-amber-300/90 flex items-center gap-1.5 bg-amber-500/10 p-2 rounded-lg border border-amber-500/20">
                        <i data-lucide="shield-check" class="w-4 h-4 shrink-0"></i>
                        <span>This item will be safely held in escrow and can be listed or withdrawn anytime.</span>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="transferModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#251A33] text-slate-300 hover:text-white border border-[#4A3A5A]">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110">Transfer Item</button>
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
                <button @click="listModalOpen = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <template x-if="selectedCustodyItem">
                <form action="<?php echo e(route('marketplace.list')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="item_id" :value="selectedCustodyItem.id">

                    <div class="flex items-center gap-3 p-3 rounded-xl bg-[#140E1B] border border-[#352643]">
                        <img :src="selectedCustodyItem.icon_url" class="w-10 h-10 object-contain rounded bg-[#251A33] p-1 border border-[#4A3A5A]">
                        <div>
                            <h4 class="font-bold text-white font-cinzel" x-text="selectedCustodyItem.item_name"></h4>
                            <p class="text-[11px] text-slate-400" x-text="'Available Quantity: ' + selectedCustodyItem.amount"></p>
                        </div>
                    </div>

                    <!-- Currency Selector -->
                    <div>
                        <label class="text-xs font-bold text-slate-300 block mb-1">Selling Currency</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer" :class="listCurrency === 'zeny' ? 'bg-amber-500/20 border-amber-500/60 text-amber-300 font-bold' : 'bg-[#140E1B] border-[#4A3A5A] text-slate-400'">
                                <input type="radio" name="currency_type" value="zeny" x-model="listCurrency" class="hidden">
                                <i data-lucide="coins" class="w-4 h-4"></i>
                                <span>Zeny</span>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer" :class="listCurrency === 'cashpoints' ? 'bg-purple-500/20 border-purple-500/60 text-purple-300 font-bold' : 'bg-[#140E1B] border-[#4A3A5A] text-slate-400'">
                                <input type="radio" name="currency_type" value="cashpoints" x-model="listCurrency" class="hidden">
                                <i data-lucide="gem" class="w-4 h-4"></i>
                                <span>Cash Points</span>
                            </label>
                        </div>
                    </div>

                    <!-- Quantity & Price -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-300 block mb-1">Quantity</label>
                            <input type="number" name="quantity" x-model.number="listQuantity" min="1" :max="selectedCustodyItem.amount" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-300 block mb-1">Price / Unit</label>
                            <input type="number" name="unit_price" x-model.number="listUnitPrice" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                        </div>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="text-xs font-bold text-slate-300 block mb-1">Listing Duration</label>
                        <select name="duration_hours" x-model.number="listDuration" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#4A3A5A] text-white">
                            <option value="24">24 Hours (1 Day)</option>
                            <option value="72">72 Hours (3 Days)</option>
                            <option value="168">168 Hours (7 Days)</option>
                            <option value="336">336 Hours (14 Days)</option>
                            <option value="720">720 Hours (30 Days)</option>
                        </select>
                    </div>

                    <!-- Proceeds Calculation Preview -->
                    <div class="p-3 rounded-xl bg-[#140E1B] border border-[#352643] space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Gross Sale Price:</span>
                            <span class="font-mono text-white" x-text="new Intl.NumberFormat().format(listUnitPrice * listQuantity)"></span>
                        </div>
                        <div class="flex justify-between text-rose-400">
                            <span x-text="'Sales Tax (' + taxPercent + '%):'"></span>
                            <span class="font-mono" x-text="'-' + new Intl.NumberFormat().format(Math.round((listUnitPrice * listQuantity * taxPercent) / 100))"></span>
                        </div>
                        <div class="flex justify-between text-emerald-300 font-bold pt-1 border-t border-[#352643]">
                            <span>Net Proceeds on Sale:</span>
                            <span class="font-mono" x-text="new Intl.NumberFormat().format(Math.round((listUnitPrice * listQuantity) - ((listUnitPrice * listQuantity * taxPercent) / 100))) + ' ' + (listCurrency === 'zeny' ? 'Z' : 'CP')"></span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="listModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#251A33] text-slate-300 hover:text-white border border-[#4A3A5A]">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold font-cinzel bg-gradient-to-b from-[#E5B358] to-[#B8860B] text-[#17111F] border border-[#FCE69B] shadow hover:brightness-110">Post Listing</button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/marketplace/index.blade.php ENDPATH**/ ?>