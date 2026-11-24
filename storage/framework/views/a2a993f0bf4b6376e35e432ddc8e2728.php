<?php $__env->startSection('admin_content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Guild Records</h1>
            <p class="text-xs text-slate-400 mt-1">Manage, search, and inspect guild alliances, masters, and castle holdings.</p>
        </div>

        <form method="GET" action="<?php echo e(route('admin.guilds')); ?>" class="flex items-center gap-2">
            <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="Search guild name, ID, or master..."
                class="px-3 py-1.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
            <button type="submit" class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl text-xs">Search</button>
        </form>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">Emblem</th>
                        <th class="py-3 px-4">Guild ID</th>
                        <th class="py-3 px-4">Guild Name</th>
                        <th class="py-3 px-4">Guild Master</th>
                        <th class="py-3 px-4 text-center">Level</th>
                        <th class="py-3 px-4 text-center">Members</th>
                        <th class="py-3 px-4 text-center">Avg Lv</th>
                        <th class="py-3 px-4 text-right">Guild EXP</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $guilds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3 px-4 text-center">
                                <img src="<?php echo e(route('guilds.emblem', $g->guild_id)); ?>" alt="Emblem" class="w-6 h-6 object-contain inline-block image-pixelated">
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-cyan-400">#<?php echo e($g->guild_id); ?></td>
                            <td class="py-3 px-4 font-bold text-white"><?php echo e($g->name); ?></td>
                            <td class="py-3 px-4 text-slate-300 font-medium"><?php echo e($g->master); ?></td>
                            <td class="py-3 px-4 text-center font-bold text-amber-400">Lv. <?php echo e($g->guild_lv); ?></td>
                            <td class="py-3 px-4 text-center text-slate-300"><?php echo e($g->connect_member); ?> / <?php echo e($g->max_member); ?></td>
                            <td class="py-3 px-4 text-center text-slate-300"><?php echo e($g->average_lv); ?></td>
                            <td class="py-3 px-4 text-right font-mono text-slate-400"><?php echo e(number_format($g->exp)); ?></td>
                            <td class="py-3 px-4 text-right">
                                <a href="<?php echo e(route('guilds.show', $g->guild_id)); ?>" target="_blank"
                                   class="px-2.5 py-1 rounded-lg text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="py-6 text-center text-slate-500">No guild records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-2">
        <?php echo e($guilds->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/guilds.blade.php ENDPATH**/ ?>