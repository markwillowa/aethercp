<?php $__env->startSection('admin_content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Character Records</h1>
            <p class="text-xs text-slate-400 mt-1">Search and view character data across all accounts.</p>
        </div>

        <form method="GET" action="<?php echo e(route('admin.characters')); ?>" class="flex items-center gap-2">
            <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="Search character name or ID..."
                class="px-3 py-1.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
            <button type="submit" class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl text-xs">Search</button>
        </form>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                    <tr>
                        <th class="py-3 px-4">Char ID</th>
                        <th class="py-3 px-4">Account ID</th>
                        <th class="py-3 px-4">Character Name</th>
                        <th class="py-3 px-4 text-center">Class ID</th>
                        <th class="py-3 px-4 text-center">Base / Job</th>
                        <th class="py-3 px-4 text-right">Zeny</th>
                        <th class="py-3 px-4">Map Location</th>
                        <th class="py-3 px-4 text-center">Online</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $characters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3 px-4 font-mono font-bold text-cyan-400">#<?php echo e($c->char_id); ?></td>
                            <td class="py-3 px-4 font-mono text-slate-400">#<?php echo e($c->account_id); ?></td>
                            <td class="py-3 px-4 font-bold text-white"><?php echo e($c->name); ?></td>
                            <td class="py-3 px-4 text-center font-mono"><?php echo e($c->class); ?></td>
                            <td class="py-3 px-4 text-center font-bold text-slate-200"><?php echo e($c->base_level); ?> / <?php echo e($c->job_level); ?></td>
                            <td class="py-3 px-4 text-right font-mono text-amber-400"><?php echo e(number_format($c->zeny)); ?> Z</td>
                            <td class="py-3 px-4 text-slate-300"><?php echo e($c->last_map); ?> (<?php echo e($c->last_x); ?>, <?php echo e($c->last_y); ?>)</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo e($c->online > 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500'); ?>">
                                    <?php echo e($c->online > 0 ? 'Online' : 'Offline'); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="py-6 text-center text-slate-500">No character records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-2">
        <?php echo e($characters->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/characters.blade.php ENDPATH**/ ?>