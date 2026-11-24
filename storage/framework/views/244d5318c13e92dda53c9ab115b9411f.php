<?php $__env->startSection('content'); ?>
<div class="chronicles-container py-10 space-y-8" x-data="{ selectedPackage: <?php echo e($packages->first()->id ?? 'null'); ?>, selectedCost: '<?php echo e($packages->first()->price_formatted ?? '10.00'); ?>', selectedPts: '<?php echo e(number_format($packages->first()->points ?? 1000)); ?>' }">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Patronage & Treasury Vault
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Support server infrastructure, earn Cash Points, and unlock cosmetic headgears
            </p>
        </div>
        <div class="chronicles-status-pill online text-xs py-1.5 px-3">
            <span>Instant PayPal & Claim Engine</span>
        </div>
    </div>

    <!-- 2-Column Patronage Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left: Tier Packages (7 cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="text-center sm:text-left mb-2">
                <h3 class="font-cinzel text-lg font-bold text-[#FFE4A0] uppercase tracking-wide">
                    Select Contribution Package
                </h3>
                <p class="text-xs text-[#DECBB1]">All points are credited directly to your connected game account</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php $__empty_1 = true; $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div @click="selectedPackage = <?php echo e($pkg->id); ?>; selectedCost = '<?php echo e($pkg->price_formatted ?? number_format($pkg->price, 2)); ?>'; selectedPts = '<?php echo e(number_format($pkg->points)); ?>'"
                         :class="selectedPackage === <?php echo e($pkg->id); ?> ? 'ring-4 ring-[#D4441D] scale-[1.02] bg-[#FFF8EC]' : 'bg-[#FCF7ED]'"
                         class="chronicles-panel-ivy cursor-pointer transition-all duration-200 relative p-4 flex flex-col justify-between">
                        
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-cinzel text-xs font-black uppercase text-[#9E5600] tracking-wider">
                                    <?php echo e($pkg->name); ?>

                                </span>
                                <?php if($pkg->is_featured): ?>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-[#D4441D] text-white">
                                        Popular
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="text-2xl font-black font-cinzel text-[#4A2200] mb-1">
                                <?php echo e(number_format($pkg->points)); ?> <span class="text-xs font-bold text-[#8B5A2B]">PTS</span>
                            </div>

                            <?php if($pkg->bonus_points > 0): ?>
                                <div class="text-[11px] font-bold text-[#2E7D32]">
                                    + <?php echo e(number_format($pkg->bonus_points)); ?> Bonus Points!
                                </div>
                            <?php endif; ?>

                            <p class="text-xs text-[#6B5541] mt-2 leading-relaxed">
                                <?php echo e($pkg->description ?? 'Cash points usable in all Kafra In-Game Specialty Boutiques.'); ?>

                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-[#DECBB1] flex items-center justify-between">
                            <span class="font-mono font-bold text-base text-[#D4441D]">
                                $<?php echo e(number_format($pkg->price, 2)); ?> USD
                            </span>
                            <span class="text-xs font-bold text-[#8B5A2B] uppercase tracking-wider font-cinzel">
                                Select Tier →
                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full chronicles-panel-ivy text-center py-8">
                        <p class="text-xs text-[#8C7A68]">No packages published at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Checkout & Claim Terminal (5 cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Checkout Terminal -->
            <div class="chronicles-panel-ivy pt-8">
                <div class="chronicles-ribbon-header">
                    <span>Instant Checkout</span>
                </div>

                <div class="space-y-4 mt-2">
                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-xl p-3.5 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-[#8C7A68]">Selected Points:</span>
                            <strong class="font-mono text-[#4A2200]" x-text="selectedPts + ' Points'">1,000 Points</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#8C7A68]">Amount Due:</span>
                            <strong class="font-mono text-[#D4441D] text-sm" x-text="'$' + selectedCost + ' USD'">$10.00 USD</strong>
                        </div>
                        <div class="flex justify-between border-t border-[#DECBB1] pt-1.5">
                            <span class="text-[#8C7A68]">Credited To:</span>
                            <strong class="text-[#2D5A1E] font-bold"><?php echo e(auth()->user()->username ?? 'Please log in'); ?></strong>
                        </div>
                    </div>

                    <?php if(auth()->guard()->check()): ?>
                        <?php if(!empty($paypalEnabled) || !empty($paypalConfig['enabled'])): ?>
                            <form method="POST" :action="'<?php echo e(url('/donations/paypal/checkout')); ?>/' + selectedPackage">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="chronicles-btn-red w-full">
                                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                                    <span>Checkout with PayPal</span>
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="p-3 rounded-lg bg-[#FAF4EA] border border-[#DECBB1] text-xs text-[#8C7A68] text-center">
                                Direct PayPal gateway currently undergoing maintenance. Please use manual reference claim below.
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="chronicles-btn-red w-full">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                            <span>Log In to Support Server</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Manual Transaction Claim Panel -->
            <div class="chronicles-panel-ivy pt-8">
                <div class="chronicles-ribbon-header">
                    <span>Claim Reference</span>
                </div>

                <div class="space-y-3 mt-2 text-xs">
                    <p class="text-[#6B5541]">
                        Completed a transfer via GCash, Maya, Bank, or Crypto? Enter your transaction code below:
                    </p>

                    <?php if(auth()->guard()->check()): ?>
                        <form method="POST" action="<?php echo e(route('donations.submit')); ?>" class="space-y-3">
                            <?php echo csrf_field(); ?>
                            <div>
                                <label class="block font-cinzel font-bold text-[#4A3319] uppercase mb-1">
                                    Payment Method
                                </label>
                                <select name="payment_method" required class="chronicles-input">
                                    <option value="gcash">GCash / Maya</option>
                                    <option value="bank_transfer">Bank Deposit (BDO/BPI/UnionBank)</option>
                                    <option value="crypto">Cryptocurrency (USDT/BTC)</option>
                                    <option value="paypal_manual">PayPal Manual Invoice</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-cinzel font-bold text-[#4A3319] uppercase mb-1">
                                    Amount Sent (USD or PHP)
                                </label>
                                <input type="number" step="0.01" name="amount" required placeholder="e.g. 20.00" class="chronicles-input">
                            </div>

                            <div>
                                <label class="block font-cinzel font-bold text-[#4A3319] uppercase mb-1">
                                    Transaction / Reference ID
                                </label>
                                <input type="text" name="transaction_id" required placeholder="Enter reference number" class="chronicles-input">
                            </div>

                            <div class="pt-1">
                                <button type="submit" class="chronicles-btn-wood text-xs">
                                    <i data-lucide="check" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                                    <span>Submit Claim Request</span>
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <a href="<?php echo e(route('login')); ?>" class="text-xs font-bold text-[#D4441D] hover:underline">
                                Log in to submit reference claims
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\themes\aether-chronicles\views/donations/index.blade.php ENDPATH**/ ?>