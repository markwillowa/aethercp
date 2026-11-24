<?php $__env->startSection('content'); ?>
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Active In Midgard
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Real-time roster of adventurers currently roaming Rune-Midgard
            </p>
        </div>
        <div class="chronicles-status-pill online text-xs py-2 px-4">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span>Total Online: <strong class="text-white"><?php echo e($totalOnline); ?></strong></span>
        </div>
    </div>

    <!-- Online Table Panel -->
    <div class="chronicles-panel-ivy pt-6">
        <div class="chronicles-ribbon-header">
            <span>Online Adventurers</span>
        </div>

        <div class="overflow-x-auto mt-2">
            <table class="chronicles-table">
                <thead>
                    <tr>
                        <th>Adventurer</th>
                        <th>Job Class</th>
                        <th class="text-center">Base Level</th>
                        <th class="text-center">Job Level</th>
                        <th>Current Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $onlinePlayers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('characters.show', $p->name)); ?>" class="font-cinzel font-bold text-[#4A2200] hover:text-[#D4441D]">
                                    <?php echo e($p->name); ?>

                                </a>
                            </td>
                            <td>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#FAF4EA] text-[#5A3D28] border border-[#DECBB1]">
                                    <?php echo e(\App\Services\Emulator\JobHelper::getName($p->class)); ?>

                                </span>
                            </td>
                            <td class="text-center font-mono font-bold text-[#D4441D]">Lv. <?php echo e($p->base_level); ?></td>
                            <td class="text-center font-mono font-bold text-[#9E5600]">Lv. <?php echo e($p->job_level); ?></td>
                            <td class="font-mono text-xs text-[#5A3D28]"><?php echo e($p->last_map); ?>.gat</td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center text-xs text-[#8C7A68]">
                                Midgard rests peacefully. No adventurers roaming right now.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\themes\aether-chronicles\views/characters/online.blade.php ENDPATH**/ ?>