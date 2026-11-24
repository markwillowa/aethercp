<?php $__env->startSection('admin_content'); ?>
<div x-data="{
    showGrantModal: false,
    itemQuery: '',
    items: [],
    selectedItem: null,
    searching: false,
    searchItems() {
        if (this.itemQuery.length < 2) {
            this.items = [];
            return;
        }
        this.searching = true;
        fetch('<?php echo e(route('admin.items.search')); ?>?q=' + encodeURIComponent(this.itemQuery))
            .then(res => res.json())
            .then(data => {
                this.items = data;
                this.searching = false;
            })
            .catch(() => { this.searching = false; });
    },
    selectItem(item) {
        this.selectedItem = item;
        this.itemQuery = item.name + ' (#' + item.id + ')';
        this.items = [];
    },
    clearItem() {
        this.selectedItem = null;
        this.itemQuery = '';
        this.items = [];
    }
}" class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Donation Management</h1>
            <p class="text-xs text-slate-400 mt-1">Review player donation submissions, PayPal IPN payments, and in-game reward fulfillment.</p>
        </div>
        <button @click="showGrantModal = true" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs flex items-center gap-2 shadow-lg shadow-amber-500/20 transition-all shrink-0">
            <i data-lucide="award" class="w-4 h-4"></i>
            <span>+ Direct GM Credit / Item Grant</span>
        </button>
    </div>

    <!-- Top Sub-Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 pb-2 text-xs font-semibold">
        <a href="<?php echo e(route('admin.donations')); ?>" class="px-3 py-1.5 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center gap-1.5">
            <i data-lucide="list-checks" class="w-3.5 h-3.5"></i>
            <span>Approvals &amp; Transactions</span>
        </a>
        <a href="<?php echo e(route('admin.donations.packages')); ?>" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900 transition-colors flex items-center gap-1.5">
            <i data-lucide="gift" class="w-3.5 h-3.5"></i>
            <span>Packages, In-Game Rewards &amp; Instructions</span>
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Account</th>
                        <th class="py-3 px-4">Package</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Points</th>
                        <th class="py-3 px-4">Storage Items</th>
                        <th class="py-3 px-4">Method / Ref</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">In-Game Delivery</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $donations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3 px-4 font-mono font-bold text-cyan-400">#<?php echo e($d->id); ?></td>
                            <td class="py-3 px-4 font-mono text-slate-300">#<?php echo e($d->account_id); ?></td>
                            <td class="py-3 px-4 font-bold text-white"><?php echo e($d->package?->name ?? 'Custom Tier'); ?></td>
                            <td class="py-3 px-4 font-mono">$<?php echo e(number_format($d->amount, 2)); ?></td>
                            <td class="py-3 px-4 font-bold text-cyan-400"><?php echo e(number_format($d->points_awarded)); ?></td>
                            <td class="py-3 px-4">
                                <?php if($d->package && $d->package->item_amount > 0): ?>
                                    <span class="text-amber-400 font-medium"><?php echo e($d->package->item_amount); ?>x <?php echo e($d->package->item_name ?: 'Item #'.$d->package->item_id); ?></span>
                                <?php else: ?>
                                    <span class="text-slate-500">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-mono text-slate-300 text-[11px]"><?php echo e($d->transaction_reference); ?></div>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider <?php echo e($d->payment_method === 'paypal' ? 'bg-blue-950 text-blue-300 border border-blue-500/30' : 'bg-slate-800 text-slate-400'); ?>">
                                    <?php echo e($d->payment_method); ?>

                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo e($d->status === 'approved' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : ($d->status === 'pending' ? 'bg-amber-950 text-amber-400 border border-amber-500/30' : 'bg-red-950 text-red-400 border border-red-500/30')); ?>">
                                    <?php echo e($d->status); ?>

                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <?php if($d->status === 'approved'): ?>
                                    <div class="flex flex-col items-center gap-0.5 text-[10px]">
                                        <span class="<?php echo e($d->points_delivered ? 'text-emerald-400' : 'text-amber-400'); ?>">
                                            Points: <?php echo e($d->points_delivered ? 'Credited' : 'Pending'); ?>

                                        </span>
                                        <?php if($d->package && $d->package->item_amount > 0): ?>
                                            <span class="<?php echo e($d->items_delivered ? 'text-emerald-400' : 'text-amber-400'); ?>">
                                                Items: <?php echo e($d->items_delivered ? 'Delivered' : 'Pending'); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-slate-500 text-[10px]">Awaiting</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <?php if($d->status === 'pending'): ?>
                                    <div class="flex items-center justify-end gap-1">
                                        <form method="POST" action="<?php echo e(route('admin.donations.status', $d->id)); ?>" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-2 py-1 rounded bg-emerald-950 hover:bg-emerald-900 text-emerald-300 font-semibold text-[10px] border border-emerald-500/30 cursor-pointer">
                                                Approve & Deliver
                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('admin.donations.status', $d->id)); ?>" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-2 py-1 rounded bg-red-950 hover:bg-red-900 text-red-300 font-semibold text-[10px] border border-red-500/30 cursor-pointer">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="text-slate-500 text-[10px]">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="py-6 text-center text-slate-500">No donation transactions recorded.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-2">
        <?php echo e($donations->links()); ?>

    </div>

    <!-- Direct GM Grant Modal -->
    <div x-show="showGrantModal" x-transition.opacity class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showGrantModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="award" class="w-4 h-4 text-amber-400"></i>
                    <span>Direct GM Reward &amp; Points Grant</span>
                </h3>
                <button @click="showGrantModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <p class="text-xs text-slate-400">Directly grant in-game Cash Points (#CASHPOINTS) and/or official item rewards delivered straight to the player's Kafra Storage without requiring external payment.</p>

            <form method="POST" action="<?php echo e(route('admin.donations.grant')); ?>" class="space-y-4 text-xs">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Target Account *</label>
                    <input type="text" name="identifier" required placeholder="Game Account ID (e.g. 2000001) or Username" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    <p class="text-[11px] text-slate-500 mt-1">Accepts numeric Account ID or exact login username from the rAthena game database.</p>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Cash Points to Award</label>
                    <input type="number" name="points" min="0" placeholder="0" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white font-mono">
                    <p class="text-[11px] text-slate-500 mt-1">Automatically credited to player's in-game <code>#CASHPOINTS</code> account registry.</p>
                </div>

                <!-- Item Reward Live Search -->
                <div class="space-y-1 relative">
                    <label class="block font-semibold text-slate-300">In-Game Item Reward (from Database)</label>
                    <input type="hidden" name="item_id" :value="selectedItem ? selectedItem.id : ''">

                    <div class="relative">
                        <input type="text" x-model="itemQuery" @input.debounce.300ms="searchItems()" placeholder="Search item name or ID from item_db..." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                        <div x-show="searching" class="absolute right-3 top-2.5 text-slate-500">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                        </div>
                        <button type="button" x-show="selectedItem" @click="clearItem()" class="absolute right-3 top-2.5 text-slate-400 hover:text-red-400">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Autocomplete dropdown -->
                    <div x-show="items.length > 0" class="absolute left-0 right-0 z-50 mt-1 bg-slate-950 border border-slate-800 rounded-xl shadow-xl max-h-48 overflow-y-auto divide-y divide-slate-800/60" style="display: none;">
                        <template x-for="item in items" :key="item.id">
                            <div @click="selectItem(item)" class="p-2 hover:bg-slate-900 cursor-pointer flex items-center justify-between">
                                <div>
                                    <span class="text-white font-bold" x-text="item.name"></span>
                                    <span class="text-[10px] text-slate-500 ml-1.5" x-text="'[' + item.type + ']'"></span>
                                </div>
                                <span class="font-mono text-cyan-400 text-[10px]" x-text="'#' + item.id"></span>
                            </div>
                        </template>
                    </div>

                    <div x-show="selectedItem" class="p-2 bg-emerald-950/40 border border-emerald-500/30 rounded-xl text-emerald-300 text-[11px] flex items-center justify-between mt-1">
                        <span>Selected: <strong x-text="selectedItem.name"></strong> (ID: #<span x-text="selectedItem.id"></span>)</span>
                        <span class="text-[10px] uppercase font-bold text-emerald-400">Validated</span>
                    </div>
                </div>

                <div x-show="selectedItem">
                    <label class="block font-semibold text-slate-300 mb-1">Item Quantity</label>
                    <input type="number" name="item_amount" min="1" value="1" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white font-mono">
                    <p class="text-[11px] text-slate-500 mt-1">Directly delivered to player's in-game storage (Kafra).</p>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Grant Reason / Admin Audit Notes</label>
                    <input type="text" name="notes" placeholder="e.g. PvP Tournament 1st Place reward" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showGrantModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold">Confirm &amp; Deliver In-Game</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/donations.blade.php ENDPATH**/ ?>