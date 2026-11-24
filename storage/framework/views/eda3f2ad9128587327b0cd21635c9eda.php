<?php $__env->startSection('title', $monster['name'] . ' - Monster Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="skull" class="w-6 h-6 text-[#FFD700]"></i>
                <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                    <?php echo e($monster['name']); ?>

                </h1>
            </div>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Monster Codex ID #<?php echo e($monster['id']); ?> • <?php echo e($monster['race']); ?> • <?php echo e($monster['element']); ?>

            </p>
        </div>
        <a href="<?php echo e(route('database.monsters')); ?>" class="chronicles-btn-wood text-xs py-2 px-4 w-auto flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#FFD700]"></i>
            <span>All Monsters</span>
        </a>
    </div>

    <!-- Monster Dossier Panel -->
    <div class="chronicles-panel p-6 shadow-2xl">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <!-- Animated Sprite -->
            <div class="w-44 h-44 bg-[#140E1B] border-2 border-[#5A4A68] rounded-2xl flex items-center justify-center p-4 shrink-0 shadow-lg relative overflow-hidden">
                <div class="absolute inset-0 bg-radial from-[#3E2E4E]/40 to-transparent"></div>
                <?php if(!empty($monster['gif'])): ?>
                    <img src="<?php echo e(route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/monsters/' . $monster['gif']])); ?>" 
                         alt="<?php echo e($monster['name']); ?>" class="max-h-36 max-w-full object-contain relative z-10"
                         onerror="this.src='https://static.divine-pride.net/images/mobs/png/<?php echo e($monster['id']); ?>.png'">
                <?php else: ?>
                    <img src="https://static.divine-pride.net/images/mobs/png/<?php echo e($monster['id']); ?>.png" alt="<?php echo e($monster['name']); ?>" class="max-h-36 max-w-full object-contain relative z-10" onerror="this.src='/images/item-placeholder.png'">
                <?php endif; ?>
            </div>

            <!-- Stats & Profile -->
            <div class="flex-1 space-y-5 w-full">
                <div class="border-b border-[#3E324A] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="font-cinzel text-2xl font-black text-[#F3E5AB] uppercase"><?php echo e($monster['name']); ?></h2>
                            <?php if(!empty($monster['is_mvp'])): ?>
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase bg-[#E53935] text-white tracking-widest shadow">MVP BOSS</span>
                            <?php elseif(!empty($monster['is_mini'])): ?>
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-[#D4AF37] text-black">MINI BOSS</span>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs text-[#A396AD] mt-1 block">Monster Class ID: <strong class="text-[#FFD700] font-mono">#<?php echo e($monster['id']); ?></strong></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Level</span>
                        <span class="text-lg font-bold font-mono text-[#FFD700]">Lv. <?php echo e($monster['level']); ?></span>
                    </div>
                    <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Hit Points (HP)</span>
                        <span class="text-lg font-bold font-mono text-emerald-400"><?php echo e(number_format($monster['hp'])); ?></span>
                    </div>
                    <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Base EXP</span>
                        <span class="text-lg font-bold font-mono text-[#E5A823]"><?php echo e(number_format($monster['base_exp'])); ?></span>
                    </div>
                    <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Job EXP</span>
                        <span class="text-lg font-bold font-mono text-[#A855F7]"><?php echo e(number_format($monster['job_exp'])); ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 text-xs">
                    <div class="p-2.5 rounded-lg bg-[#140E1B] border border-[#3E324A]">
                        <span class="text-[10px] text-[#A396AD] block">Size:</span>
                        <strong class="text-white"><?php echo e($monster['size']); ?></strong>
                    </div>
                    <div class="p-2.5 rounded-lg bg-[#140E1B] border border-[#3E324A]">
                        <span class="text-[10px] text-[#A396AD] block">Race:</span>
                        <strong class="text-[#FFD700]"><?php echo e($monster['race']); ?></strong>
                    </div>
                    <div class="p-2.5 rounded-lg bg-[#140E1B] border border-[#3E324A]">
                        <span class="text-[10px] text-[#A396AD] block">Element:</span>
                        <strong class="text-emerald-400"><?php echo e($monster['element']); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drops & Locations Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Item Drops Table -->
        <div class="chronicles-panel shadow-2xl">
            <div class="chronicles-panel-header px-5 py-3">
                <span class="chronicles-panel-title">
                    <i data-lucide="package" class="w-4 h-4"></i>
                    <span>Item Drops &amp; Loot Table (<?php echo e(count($monster['drops'])); ?>)</span>
                </span>
            </div>

            <div class="p-4 sm:p-5 overflow-x-auto">
                <table class="chronicles-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th class="text-right">Drop Chance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $monster['drops']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $drop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-white/[0.04] transition-colors">
                                <td class="font-bold text-[#F3E5AB]">
                                    <?php echo e($drop['name']); ?>

                                </td>
                                <td class="text-right font-mono font-bold text-emerald-400">
                                    <?php echo e($drop['rate']); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Spawn Maps -->
        <div class="chronicles-panel shadow-2xl">
            <div class="chronicles-panel-header px-5 py-3">
                <span class="chronicles-panel-title">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                    <span>Known Habitat &amp; Spawn Maps (<?php echo e(count($monster['maps'])); ?>)</span>
                </span>
            </div>

            <div class="p-4 sm:p-5 space-y-2.5">
                <?php $__currentLoopData = $monster['maps']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $map): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-3 rounded-xl bg-[#140E1B] border border-[#3E324A] flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="compass" class="w-4 h-4 text-[#D4AF37]"></i>
                            <span class="font-mono text-xs text-white font-bold"><?php echo e($map); ?>.gat</span>
                        </div>
                        <a href="<?php echo e(route('database.maps', ['q' => $map])); ?>" class="text-[11px] font-cinzel text-[#E5A823] hover:text-[#FFD700]">
                            Inspect Map &raquo;
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\themes\aether-chronicles\views/itemdb/monster-show.blade.php ENDPATH**/ ?>