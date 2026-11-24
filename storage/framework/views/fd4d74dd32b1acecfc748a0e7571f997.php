<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Who's Online</h1>
            <p class="text-xs text-slate-400 mt-1">Live adventurers currently exploring Midgard.</p>
        </div>
        <div class="flex items-center gap-2 bg-slate-900 border border-slate-800 px-3.5 py-1.5 rounded-full text-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-slate-300">Total Online:</span>
            <strong class="text-cyan-400"><?php echo e($totalOnline); ?></strong>
        </div>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-950/80 text-slate-400 uppercase text-[10px] font-semibold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Character Name</th>
                        <th class="py-3 px-4">Job Class</th>
                        <th class="py-3 px-4 text-center">Base Level</th>
                        <th class="py-3 px-4 text-center">Job Level</th>
                        <th class="py-3 px-4">Current Map</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $onlinePlayers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3 px-4 font-bold text-white">
                                <a href="<?php echo e(route('characters.show', $p->name)); ?>" class="hover:text-cyan-400"><?php echo e($p->name); ?></a>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold border <?php echo e(\App\Services\Emulator\JobHelper::getBadgeColor($p->class)); ?>">
                                    <?php echo e(\App\Services\Emulator\JobHelper::getName($p->class)); ?>

                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-cyan-400"><?php echo e($p->base_level); ?></td>
                            <td class="py-3 px-4 text-center font-bold text-blue-400"><?php echo e($p->job_level); ?></td>
                            <td class="py-3 px-4 font-mono text-slate-300"><?php echo e($p->last_map); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">
                                No adventurers are currently online.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/characters/online.blade.php ENDPATH**/ ?>