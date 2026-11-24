<?php $__env->startSection('admin_content'); ?>
<div class="space-y-6">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">System Activity & Audit Logs</h1>
        <p class="text-xs text-slate-400 mt-1">Security logs, logins, administrative actions, and unstuck history.</p>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                <tr>
                    <th class="py-3 px-4">Timestamp</th>
                    <th class="py-3 px-4">Action</th>
                    <th class="py-3 px-4">Account ID</th>
                    <th class="py-3 px-4">Description</th>
                    <th class="py-3 px-4">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-800/30">
                        <td class="py-3 px-4 text-slate-500 font-mono text-[11px]"><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></td>
                        <td class="py-3 px-4 font-bold text-cyan-400"><?php echo e($log->action); ?></td>
                        <td class="py-3 px-4 font-mono text-slate-300">#<?php echo e($log->account_id ?? '-'); ?></td>
                        <td class="py-3 px-4 text-slate-200"><?php echo e($log->description); ?></td>
                        <td class="py-3 px-4 text-slate-500 font-mono"><?php echo e($log->ip_address); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500">No activity logs recorded.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="pt-2">
        <?php echo e($logs->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/logs.blade.php ENDPATH**/ ?>