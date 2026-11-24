<?php $__env->startSection('title', 'Player Marketplace Administration'); ?>

<?php $__env->startSection('admin_content'); ?>
<div class="space-y-6" x-data="{ adminTab: '<?php echo e(request('tab', 'listings')); ?>' }">

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
            <a href="<?php echo e(route('marketplace.index')); ?>" target="_blank" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="external-link" class="w-4 h-4 text-cyan-400"></i>
                <span>View Trade House</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if(session('success')): ?>
        <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0 text-emerald-400"></i>
            <span class="font-medium"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 text-rose-400"></i>
            <span class="font-medium"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Metric Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-4 shadow-sm backdrop-blur">
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0 border border-cyan-500/30">
                <i data-lucide="tag" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Active Listings</p>
                <h3 class="text-2xl font-bold text-white"><?php echo e(number_format($stats['active_listings_count'] ?? $stats['active_listings'] ?? 0)); ?></h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Custody Items: <span class="text-cyan-400 font-semibold"><?php echo e(number_format($stats['escrow_items_count'] ?? $stats['total_items_in_custody'] ?? 0)); ?></span></p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-4 shadow-sm backdrop-blur">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
                <i data-lucide="check-check" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Completed Trades</p>
                <h3 class="text-2xl font-bold text-white"><?php echo e(number_format($stats['total_sales_count'])); ?></h3>
                <p class="text-[10px] text-emerald-400 mt-0.5">Successful player purchases</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-4 shadow-sm backdrop-blur">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0 border border-amber-500/30">
                <i data-lucide="coins" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Zeny Volume</p>
                <h3 class="text-lg font-bold text-amber-300 font-mono"><?php echo e(number_format($stats['zeny_volume'])); ?> Z</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Tax Collected: <span class="text-amber-200 font-semibold font-mono"><?php echo e(number_format($stats['zeny_tax_collected'])); ?> Z</span></p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center gap-4 shadow-sm backdrop-blur">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center shrink-0 border border-purple-500/30">
                <i data-lucide="gem" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Cash Points Volume</p>
                <h3 class="text-lg font-bold text-purple-300 font-mono"><?php echo e(number_format($stats['points_volume'])); ?> CP</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Tax Collected: <span class="text-purple-200 font-semibold font-mono"><?php echo e(number_format($stats['points_tax_collected'])); ?> CP</span></p>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
            <button @click="adminTab = 'listings'" :class="adminTab === 'listings' ? 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900 border border-slate-800'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="list" class="w-4 h-4"></i>
                Listings Moderation (<?php echo e($listings->total()); ?>)
            </button>
            <button @click="adminTab = 'custody'" :class="adminTab === 'custody' ? 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900 border border-slate-800'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="archive" class="w-4 h-4"></i>
                Escrow Custody Vault (<?php echo e($custodyItems->total()); ?>)
            </button>
            <button @click="adminTab = 'transactions'" :class="adminTab === 'transactions' ? 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white bg-slate-900 border border-slate-800'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="history" class="w-4 h-4"></i>
                Transaction Ledger (<?php echo e($transactions->total()); ?>)
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
                <form method="GET" action="<?php echo e(route('admin.marketplace.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="hidden" name="tab" value="listings">
                    
                    <div class="md:col-span-2 relative" style="position: relative;">
                        <div style="position: absolute; left: 14px; top: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10;">
                            <i data-lucide="search" class="w-4 h-4 text-cyan-400" style="width: 16px; height: 16px;"></i>
                        </div>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search listing code, seller character, or item ID..." 
                               style="padding-left: 44px !important; padding-right: 16px !important; height: 42px;"
                               class="w-full rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>

                    <div>
                        <select name="status" style="height: 42px;" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            <option value="all" <?php echo e(request('status') === 'all' || !request('status') ? 'selected' : ''); ?>>All Statuses</option>
                            <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active Listings</option>
                            <option value="sold" <?php echo e(request('status') === 'sold' ? 'selected' : ''); ?>>Sold</option>
                            <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                            <option value="expired" <?php echo e(request('status') === 'expired' ? 'selected' : ''); ?>>Expired</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <select name="currency" style="height: 42px;" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            <option value="" <?php echo e(!request('currency') ? 'selected' : ''); ?>>All Currencies</option>
                            <option value="zeny" <?php echo e(request('currency') === 'zeny' ? 'selected' : ''); ?>>Zeny Only</option>
                            <option value="cashpoints" <?php echo e(request('currency') === 'cashpoints' ? 'selected' : ''); ?>>Cash Points Only</option>
                        </select>
                        <button type="submit" style="height: 42px;" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 transition-all cursor-pointer shrink-0">
                            Filter
                        </button>
                        <?php if(request('search') || request('status') || request('currency')): ?>
                            <a href="<?php echo e(route('admin.marketplace.index', ['tab' => 'listings'])); ?>" style="height: 42px; width: 42px;" class="rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-slate-800 border border-slate-700 transition-all shrink-0 flex items-center justify-center" title="Reset Filters">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        <?php endif; ?>
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
                            <?php $__empty_1 = true; $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemListing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-cyan-400"><?php echo e($itemListing->listing_code); ?></td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <img src="<?php echo e($itemListing->item->icon_url); ?>" alt="Item" class="w-8 h-8 object-contain rounded bg-slate-950 border border-slate-800 p-0.5" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <p class="font-bold text-white"><?php echo e($itemListing->item->formatted_name); ?></p>
                                                    <?php if($itemListing->item->slots > 0): ?>
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold font-mono bg-slate-800 text-cyan-300 border border-cyan-500/30">
                                                            <?php echo e($itemListing->item->slots); ?> <?php echo e(Str::plural('SLOT', $itemListing->item->slots)); ?>

                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if(!empty($itemListing->item->cards)): ?>
                                                    <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                                        <?php $__currentLoopData = $itemListing->item->cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span class="px-1.5 py-0.5 rounded text-[8px] bg-rose-950/60 border border-rose-500/40 text-rose-300">
                                                                ◆ <?php echo e($c['name']); ?>

                                                            </span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <p class="text-[10px] text-slate-400 mt-0.5 font-mono">ID #<?php echo e($itemListing->item->nameid); ?> • <?php echo e($itemListing->item->item_type); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-medium text-white"><?php echo e($itemListing->seller_char_name); ?></p>
                                        <p class="text-[10px] text-slate-400"><?php echo e($itemListing->sellerUser->username ?? 'Account #'.$itemListing->seller_account_id); ?></p>
                                    </td>
                                    <td class="py-3 px-4 font-mono">
                                        <?php if($itemListing->currency_type === 'zeny'): ?>
                                            <span class="inline-flex items-center gap-1 font-bold text-amber-300">
                                                <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-400"></i>
                                                <?php echo e(number_format($itemListing->unit_price)); ?> Z
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 font-bold text-purple-300">
                                                <i data-lucide="gem" class="w-3.5 h-3.5 text-purple-400"></i>
                                                <?php echo e(number_format($itemListing->unit_price)); ?> CP
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-white"><?php echo e($itemListing->remaining_quantity); ?></span> / <?php echo e($itemListing->quantity); ?>

                                    </td>
                                    <td class="py-3 px-4 text-slate-300">
                                        <?php echo e($itemListing->remaining_time); ?>

                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if($itemListing->status === 'active'): ?>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Active</span>
                                        <?php elseif($itemListing->status === 'sold'): ?>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">Sold</span>
                                        <?php elseif($itemListing->status === 'cancelled'): ?>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Cancelled</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300">Expired</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <?php if($itemListing->status === 'active'): ?>
                                            <form action="<?php echo e(route('admin.marketplace.moderate', $itemListing->id)); ?>" method="POST" onsubmit="return confirm('Cancel this listing and refund the item to seller escrow vault?');" class="inline-block">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="action" value="cancel">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-rose-950/60 border border-rose-500/40 text-rose-300 hover:bg-rose-900 transition-colors cursor-pointer" title="Cancel Listing &amp; Return Item">
                                                    Cancel
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-slate-500 text-[11px] italic">Settled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-slate-400">
                                        <i data-lucide="package" class="w-8 h-8 text-slate-600 mx-auto mb-2"></i>
                                        No marketplace listings match the selected filters.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($listings->hasPages()): ?>
                    <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                        <?php echo e($listings->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: ESCROW CUSTODY VAULT                -->
        <!-- ========================================== -->
        <div x-show="adminTab === 'custody'" class="space-y-4">
            <!-- Search & Filters -->
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 backdrop-blur shadow-lg">
                <form method="GET" action="<?php echo e(route('admin.marketplace.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="hidden" name="tab" value="custody">
                    
                    <div class="md:col-span-2 relative" style="position: relative;">
                        <div style="position: absolute; left: 14px; top: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10;">
                            <i data-lucide="search" class="w-4 h-4 text-cyan-400" style="width: 16px; height: 16px;"></i>
                        </div>
                        <input type="text" name="custody_search" value="<?php echo e(request('custody_search')); ?>" placeholder="Search item ID, account ID, or username..." 
                               style="padding-left: 44px !important; padding-right: 16px !important; height: 42px;"
                               class="w-full rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>

                    <div>
                        <select name="custody_status" style="height: 42px;" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            <option value="all" <?php echo e(request('custody_status') === 'all' || !request('custody_status') ? 'selected' : ''); ?>>All Items in Vault</option>
                            <option value="in_custody" <?php echo e(request('custody_status') === 'in_custody' ? 'selected' : ''); ?>>In Custody (Unlisted)</option>
                            <option value="listed" <?php echo e(request('custody_status') === 'listed' ? 'selected' : ''); ?>>Actively Listed</option>
                            <option value="withdrawn" <?php echo e(request('custody_status') === 'withdrawn' ? 'selected' : ''); ?>>Returned / Withdrawn</option>
                            <option value="sold" <?php echo e(request('custody_status') === 'sold' ? 'selected' : ''); ?>>Sold &amp; Delivered</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" style="height: 42px;" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 transition-all cursor-pointer w-full">
                            Filter Vault
                        </button>
                        <?php if(request('custody_search') || request('custody_status')): ?>
                            <a href="<?php echo e(route('admin.marketplace.index', ['tab' => 'custody'])); ?>" style="height: 42px; width: 42px;" class="rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-slate-800 border border-slate-700 transition-all shrink-0 flex items-center justify-center" title="Reset Filters">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        <?php endif; ?>
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
                            <?php $__empty_1 = true; $__currentLoopData = $custodyItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-cyan-400">#<?php echo e($cItem->id); ?></td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <img src="<?php echo e($cItem->icon_url); ?>" alt="Item" class="w-7 h-7 object-contain rounded bg-slate-950 p-0.5 border border-slate-800" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-white"><?php echo e($cItem->formatted_name); ?></span>
                                                    <?php if($cItem->slots > 0): ?>
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold font-mono bg-slate-800 text-cyan-300 border border-cyan-500/30">
                                                            <?php echo e($cItem->slots); ?> <?php echo e(Str::plural('SLOT', $cItem->slots)); ?>

                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="text-[10px] text-slate-400 block font-mono">ID #<?php echo e($cItem->nameid); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-medium text-white"><?php echo e($cItem->user->username ?? 'User #'.$cItem->account_id); ?></p>
                                        <p class="text-[10px] text-slate-400"><?php echo e($cItem->character->name ?? 'Char #'.$cItem->char_id); ?></p>
                                    </td>
                                    <td class="py-3 px-4 font-bold text-white"><?php echo e(number_format($cItem->amount)); ?></td>
                                    <td class="py-3 px-4">
                                        <?php if(!empty($cItem->cards)): ?>
                                            <div class="flex items-center gap-1 flex-wrap">
                                                <?php $__currentLoopData = $cItem->cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="px-1.5 py-0.5 rounded text-[8px] bg-rose-950/60 border border-rose-500/40 text-rose-300">
                                                        <?php echo e($card['name']); ?>

                                                    </span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-slate-500 italic">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if($cItem->status === 'in_custody'): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">In Custody</span>
                                        <?php elseif($cItem->status === 'listed'): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Listed</span>
                                        <?php elseif($cItem->status === 'sold'): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Sold</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-700 text-slate-300">Withdrawn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">
                                        <?php echo e($cItem->created_at->format('M d, Y H:i')); ?>

                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <?php if(in_array($cItem->status, ['in_custody', 'listed'])): ?>
                                            <form action="<?php echo e(route('admin.marketplace.custody.refund', $cItem->id)); ?>" method="POST" onsubmit="return confirm('Emergency Return: This will deposit the item directly back into the character inventory in-game. Proceed?');" class="inline-block">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 hover:bg-cyan-500/20 transition-colors cursor-pointer" title="Direct In-Game Refund">
                                                    Refund to Char
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-slate-500 text-[11px] italic">Archived</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-slate-400">
                                        <i data-lucide="archive" class="w-8 h-8 text-slate-600 mx-auto mb-2"></i>
                                        No escrow custody items match the query.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($custodyItems->hasPages()): ?>
                    <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                        <?php echo e($custodyItems->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: TRANSACTION LEDGER                  -->
        <!-- ========================================== -->
        <div x-show="adminTab === 'transactions'" class="space-y-4">
            <!-- Search & Filters -->
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 backdrop-blur shadow-lg">
                <form method="GET" action="<?php echo e(route('admin.marketplace.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="hidden" name="tab" value="transactions">
                    
                    <div class="md:col-span-2 relative" style="position: relative;">
                        <div style="position: absolute; left: 14px; top: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10;">
                            <i data-lucide="search" class="w-4 h-4 text-cyan-400" style="width: 16px; height: 16px;"></i>
                        </div>
                        <input type="text" name="tx_search" value="<?php echo e(request('tx_search')); ?>" placeholder="Search TX reference code, buyer character, or seller..." 
                               style="padding-left: 44px !important; padding-right: 16px !important; height: 42px;"
                               class="w-full rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>

                    <div>
                        <select name="tx_currency" style="height: 42px;" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            <option value="" <?php echo e(!request('tx_currency') ? 'selected' : ''); ?>>All Currencies</option>
                            <option value="zeny" <?php echo e(request('tx_currency') === 'zeny' ? 'selected' : ''); ?>>Zeny Trades Only</option>
                            <option value="cashpoints" <?php echo e(request('tx_currency') === 'cashpoints' ? 'selected' : ''); ?>>Cash Points Trades Only</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" style="height: 42px;" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 transition-all cursor-pointer w-full">
                            Filter Ledger
                        </button>
                        <?php if(request('tx_search') || request('tx_currency')): ?>
                            <a href="<?php echo e(route('admin.marketplace.index', ['tab' => 'transactions'])); ?>" style="height: 42px; width: 42px;" class="rounded-xl text-xs font-bold text-slate-400 hover:text-white bg-slate-800 border border-slate-700 transition-all shrink-0 flex items-center justify-center" title="Reset Filters">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        <?php endif; ?>
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
                            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-cyan-400"><?php echo e($tx->transaction_code); ?></td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <img src="<?php echo e($tx->item->icon_url); ?>" alt="Item" class="w-7 h-7 object-contain rounded bg-slate-950 p-0.5 border border-slate-800" onerror="this.src='/images/item-placeholder.png'">
                                            <div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-white"><?php echo e($tx->quantity); ?>x <?php echo e($tx->item->formatted_name); ?></span>
                                                    <?php if($tx->item->slots > 0): ?>
                                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-bold font-mono bg-slate-800 text-cyan-300 border border-cyan-500/30">
                                                            <?php echo e($tx->item->slots); ?> <?php echo e(Str::plural('SLOT', $tx->item->slots)); ?>

                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 font-medium text-white"><?php echo e($tx->buyer_char_name); ?></td>
                                    <td class="py-3 px-4 font-medium text-white"><?php echo e($tx->seller_char_name); ?></td>
                                    <td class="py-3 px-4 font-bold font-mono <?php echo e($tx->currency_type === 'zeny' ? 'text-amber-300' : 'text-purple-300'); ?>">
                                        <?php echo e(number_format($tx->gross_amount)); ?> <?php echo e($tx->currency_type === 'zeny' ? 'Z' : 'CP'); ?>

                                    </td>
                                    <td class="py-3 px-4 text-rose-300 font-mono">
                                        -<?php echo e(number_format($tx->tax_amount)); ?>

                                    </td>
                                    <td class="py-3 px-4 text-emerald-300 font-bold font-mono">
                                        <?php echo e(number_format($tx->net_seller_proceeds)); ?>

                                    </td>
                                    <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">
                                        <?php echo e($tx->created_at->format('M d, Y H:i')); ?>

                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            <i data-lucide="check" class="w-3 h-3"></i> Delivered
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-10 text-slate-400">
                                        <i data-lucide="history" class="w-8 h-8 text-slate-600 mx-auto mb-2"></i>
                                        No marketplace sales recorded yet.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($transactions->hasPages()): ?>
                    <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                        <?php echo e($transactions->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 4: CONFIGURATION & ECONOMY RULES       -->
        <!-- ========================================== -->
        <div x-show="adminTab === 'settings'" class="space-y-6">
            <form action="<?php echo e(route('admin.marketplace.settings')); ?>" method="POST">
                <?php echo csrf_field(); ?>

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
                            <input type="checkbox" name="module_enabled_marketplace" value="1" <?php echo e(!empty($settings['enabled']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                        </div>

                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-950 border border-slate-800">
                            <div>
                                <label class="text-xs font-bold text-white block">Marketplace Maintenance Mode</label>
                                <span class="text-[11px] text-slate-400">Temporarily suspend listing creation and purchases while preserving all escrowed items.</span>
                            </div>
                            <input type="checkbox" name="marketplace_maintenance_mode" value="1" <?php echo e(!empty($settings['maintenance_mode']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
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
                            <input type="text" name="marketplace_announcement" value="<?php echo e($settings['announcement']); ?>" placeholder="e.g. Welcome to the Trade House! 5% tax is currently active." class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Displayed at the top of the Trade House interface</span>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-300 block mb-1">Trade House Rules &amp; Notice</label>
                            <textarea name="marketplace_rules" rows="3" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500"><?php echo e($settings['rules']); ?></textarea>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Official rules guidelines displayed in the rules modal for players</span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Max / Player</label>
                                <input type="number" name="marketplace_max_listings_player" value="<?php echo e($settings['max_listings_player']); ?>" min="1" max="100" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Listings/Account</span>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Max / Character</label>
                                <input type="number" name="marketplace_max_listings_char" value="<?php echo e($settings['max_listings_char']); ?>" min="1" max="50" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Listings/Char</span>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Max Items / Listing</label>
                                <input type="number" name="marketplace_max_items_per_listing" value="<?php echo e($settings['max_items_per_listing'] ?? 1000); ?>" min="1" max="30000" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Max Stack</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Minimum Selling Price</label>
                                <input type="number" name="marketplace_min_price" value="<?php echo e($settings['min_price']); ?>" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Maximum Selling Price</label>
                                <input type="number" name="marketplace_max_price" value="<?php echo e($settings['max_price']); ?>" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 pt-2 border-t border-slate-800">
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Min Duration</label>
                                <input type="number" name="marketplace_min_duration_hours" value="<?php echo e($settings['min_duration_hours'] ?? 24); ?>" min="1" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Hours</span>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Default Duration</label>
                                <select name="marketplace_default_duration_hours" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                    <option value="24" <?php echo e($settings['default_duration_hours'] == 24 ? 'selected' : ''); ?>>24 Hours (1 Day)</option>
                                    <option value="72" <?php echo e($settings['default_duration_hours'] == 72 ? 'selected' : ''); ?>>72 Hours (3 Days)</option>
                                    <option value="168" <?php echo e($settings['default_duration_hours'] == 168 ? 'selected' : ''); ?>>168 Hours (7 Days)</option>
                                    <option value="336" <?php echo e($settings['default_duration_hours'] == 336 ? 'selected' : ''); ?>>336 Hours (14 Days)</option>
                                    <option value="720" <?php echo e($settings['default_duration_hours'] == 720 ? 'selected' : ''); ?>>720 Hours (30 Days)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Max Duration</label>
                                <input type="number" name="marketplace_max_duration_hours" value="<?php echo e($settings['max_duration_hours'] ?? 720); ?>" min="24" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
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
                                <input type="checkbox" name="marketplace_allow_zeny" value="1" <?php echo e(!empty($settings['allow_zeny']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800">
                                <div>
                                    <label class="text-xs font-bold text-white block">Allow Cash Points Payments</label>
                                    <span class="text-[10px] text-slate-400">Trade using account CP</span>
                                </div>
                                <input type="checkbox" name="marketplace_allow_cashpoints" value="1" <?php echo e(!empty($settings['allow_cashpoints']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500 focus:ring-cyan-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Marketplace Sales Tax (%)</label>
                                <input type="number" step="0.1" name="marketplace_tax_percent" value="<?php echo e($settings['tax_percent']); ?>" min="0" max="50" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Deducted automatically from seller proceeds</span>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-300 block mb-1">Listing Insertion Fee (Zeny)</label>
                                <input type="number" name="marketplace_listing_fee" value="<?php echo e($settings['listing_fee']); ?>" min="0" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500">
                                <span class="text-[10px] text-slate-400">Flat fee charged from character Zeny</span>
                            </div>
                        </div>

                        <!-- Item Safety Rules -->
                        <div class="space-y-2 pt-2 border-t border-slate-800">
                            <p class="text-[11px] font-semibold text-slate-300 uppercase tracking-wider">Item Safeguards &amp; Restriction Rules</p>

                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_withdrawals" value="1" <?php echo e(!empty($settings['allow_withdrawals']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Item Withdrawals
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_stackables" value="1" <?php echo e(!empty($settings['allow_stackables']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Stackable Items
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_equipment" value="1" <?php echo e(!empty($settings['allow_equipment']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Equipment
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_refined" value="1" <?php echo e(!empty($settings['allow_refined']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Refined Equipment
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_cards" value="1" <?php echo e(!empty($settings['allow_cards']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Cards &amp; Random Options
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_history_enabled" value="1" <?php echo e(!empty($settings['history_enabled']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Enable Marketplace History
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_bound" value="1" <?php echo e(!empty($settings['allow_bound']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500">
                                    Allow Bound Items (Caution)
                                </label>
                                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="marketplace_allow_rental" value="1" <?php echo e(!empty($settings['allow_rental']) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-slate-700 text-cyan-500">
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/marketplace/index.blade.php ENDPATH**/ ?>