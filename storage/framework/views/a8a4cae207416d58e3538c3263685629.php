<?php $__env->startSection('content'); ?>
<div class="chronicles-container py-10 space-y-8">

    <!-- Top Wood Board Header -->
    <div class="chronicles-wood-board flex flex-col md:flex-row md:items-center justify-between gap-6 p-5 sm:p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="trophy" class="w-6 h-6 text-[#FFD700]"></i>
                <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                    Hall of Fame Leaderboards
                </h1>
            </div>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Honoring the mightiest champions and prestigious guilds across <?php echo e(\App\Models\SiteSetting::get('server_name', config('aether.server_name', 'Aether Chronicles'))); ?>

            </p>
        </div>

        <!-- Ranking Type Tabs -->
        <div class="flex flex-wrap items-center gap-2">
            <?php
                $tabs = [
                    'base' => ['label' => 'Base Level', 'icon' => 'trending-up'],
                    'job' => ['label' => 'Job Level', 'icon' => 'award'],
                    'zeny' => ['label' => 'Zeny Wealth', 'icon' => 'coins'],
                    'mvp' => ['label' => 'MVP Kills', 'icon' => 'skull'],
                    'guild' => ['label' => 'Guild Rankings', 'icon' => 'shield'],
                ];
                $rankingItems = $ladder ?? $rankings ?? [];
                $colCount = ($type === 'guild') ? 6 : (($type === 'job') ? 6 : 5);
            ?>
            <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('rankings.index', ['type' => $key])); ?>"
                   class="px-3.5 py-2 rounded-lg text-xs font-cinzel font-bold uppercase tracking-wider flex items-center gap-1.5 transition-all <?php echo e($type === $key ? 'bg-gradient-to-b from-[#FFE894] to-[#8C5E09] text-[#1A1202] border border-[#FFEAA5] shadow-lg font-black' : 'bg-[#251F2D] border border-[#5A4A68] text-[#FFE4A0] hover:bg-[#352B42] hover:text-[#FFF]'); ?>">
                    <i data-lucide="<?php echo e($tab['icon']); ?>" class="w-3.5 h-3.5"></i>
                    <span><?php echo e($tab['label']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Leaderboard Table Panel -->
    <div class="chronicles-panel shadow-2xl">
        <div class="chronicles-panel-header px-5 py-3">
            <span class="chronicles-panel-title">
                <i data-lucide="award" class="w-4 h-4"></i>
                <span><?php echo e($tabs[$type]['label'] ?? 'Standings'); ?> Rankings</span>
            </span>
            <span class="text-[11px] text-[#A396AD] font-mono">Top <?php echo e(count($rankingItems)); ?> Ranked</span>
        </div>

        <div class="p-4 sm:p-6 overflow-x-auto">
            <table class="chronicles-table">
                <thead>
                    <tr>
                        <th class="w-16 text-center">Rank</th>
                        <?php if($type === 'guild'): ?>
                            <th class="text-left">Guild Crest &amp; Name</th>
                            <th class="text-left">Guild Master</th>
                            <th class="text-center">Guild Level</th>
                            <th class="text-center">Members</th>
                            <th class="text-right">Total EXP</th>
                        <?php else: ?>
                            <th class="text-left">Adventurer Name</th>
                            <th class="text-center">Job Class</th>
                            <th class="text-center">Base Level</th>
                            <?php if($type === 'job'): ?>
                                <th class="text-center">Job Level</th>
                            <?php endif; ?>
                            <?php if($type === 'zeny'): ?>
                                <th class="text-right">Carried Zeny</th>
                            <?php elseif($type === 'mvp'): ?>
                                <th class="text-right">MVP Kills</th>
                            <?php else: ?>
                                <th class="text-right">EXP Points</th>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rankingItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $rank = $index + 1; ?>
                        <tr class="hover:bg-white/[0.04] transition-colors">
                            <td class="text-center font-bold">
                                <?php if($rank === 1): ?>
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gradient-to-b from-[#FFE885] to-[#D4AF37] text-[#3B1F0A] border border-[#FFF3D6] font-cinzel font-black shadow-md text-xs">
                                        1
                                    </span>
                                <?php elseif($rank === 2): ?>
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gradient-to-b from-[#E0E0E0] to-[#9E9E9E] text-[#212121] border border-white font-cinzel font-black shadow-md text-xs">
                                        2
                                    </span>
                                <?php elseif($rank === 3): ?>
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gradient-to-b from-[#D79A63] to-[#8C532B] text-white border border-[#F5CBA7] font-cinzel font-black shadow-md text-xs">
                                        3
                                    </span>
                                <?php else: ?>
                                    <span class="font-mono text-xs text-[#A396AD]">#<?php echo e($rank); ?></span>
                                <?php endif; ?>
                            </td>

                            <?php if($type === 'guild'): ?>
                                <td class="text-left">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded bg-[#1B1522] border border-[#5A4A68] flex items-center justify-center shrink-0">
                                            <?php if($row->guild_id > 0): ?>
                                                <img src="<?php echo e(route('guilds.emblem', $row->guild_id)); ?>" alt="Emblem" class="w-6 h-6 rounded" loading="lazy">
                                            <?php else: ?>
                                                <i data-lucide="shield" class="w-4 h-4 text-[#D4AF37]"></i>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?php echo e(route('guilds.show', $row->guild_id)); ?>" class="font-cinzel font-bold text-[#F3E5AB] hover:text-[#FFD700] transition-colors">
                                            <?php echo e($row->name); ?>

                                        </a>
                                    </div>
                                </td>
                                <td class="text-left text-[#E2D9EB]"><?php echo e($row->master); ?></td>
                                <td class="text-center font-mono font-bold text-[#FFD700]">Lv. <?php echo e($row->guild_lv); ?></td>
                                <td class="text-center font-mono text-[#E2D9EB]"><?php echo e($row->connect_member ?? 0); ?> / <?php echo e($row->max_member); ?></td>
                                <td class="text-right font-mono text-xs text-[#FFD700] font-bold"><?php echo e(number_format($row->exp)); ?></td>
                            <?php else: ?>
                                <td class="text-left">
                                    <a href="<?php echo e(route('characters.show', $row->name)); ?>" class="font-cinzel font-bold text-[#F3E5AB] hover:text-[#FFD700] transition-colors">
                                        <?php echo e($row->name); ?>

                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="px-2.5 py-1 rounded text-[11px] font-semibold bg-[#1F1926] text-[#CBB488] border border-[#5A4A68]">
                                        <?php echo e(\App\Services\Emulator\JobHelper::getName($row->class)); ?>

                                    </span>
                                </td>
                                <td class="text-center font-mono font-bold text-[#FFD700]">Lv. <?php echo e($row->base_level); ?></td>
                                <?php if($type === 'job'): ?>
                                    <td class="text-center font-mono font-bold text-[#E5A823]">Lv. <?php echo e($row->job_level); ?></td>
                                <?php endif; ?>
                                <?php if($type === 'zeny'): ?>
                                    <td class="text-right font-mono text-xs font-bold text-[#FFD700]"><?php echo e(number_format($row->zeny)); ?> Z</td>
                                <?php elseif($type === 'mvp'): ?>
                                    <td class="text-right font-mono text-xs font-bold text-[#E53935]"><?php echo e(number_format($row->mvp_kills ?? $row->score ?? 0)); ?></td>
                                <?php else: ?>
                                    <td class="text-right font-mono text-xs text-[#A396AD]"><?php echo e(number_format($row->base_exp)); ?></td>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($colCount); ?>" class="py-12 text-center text-xs text-[#8C7A68]">
                                <i data-lucide="trophy" class="w-8 h-8 text-[#5A4A68] mx-auto mb-2"></i>
                                Leaderboard awaiting new records.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\themes\aether-chronicles\views/rankings/index.blade.php ENDPATH**/ ?>