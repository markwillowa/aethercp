<?php $__env->startSection('admin_content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Game Accounts Manager</h1>
            <p class="text-xs text-slate-400 mt-1">Manage emulator accounts from the live login database.</p>
        </div>

        <form method="GET" action="<?php echo e(route('admin.accounts')); ?>" class="flex items-center gap-2">
            <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="Search ID, username, or email..."
                class="px-3 py-1.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
            <button type="submit" class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl text-xs">Search</button>
        </form>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                    <tr>
                        <th class="py-3 px-4">Account ID</th>
                        <th class="py-3 px-4">Username</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4 text-center">Group ID</th>
                        <th class="py-3 px-4 text-center">Sex</th>
                        <th class="py-3 px-4 text-center">State</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $gameAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3 px-4 font-mono font-bold text-cyan-400">#<?php echo e($acc->account_id); ?></td>
                            <td class="py-3 px-4 font-bold text-white"><?php echo e($acc->userid); ?></td>
                            <td class="py-3 px-4 text-slate-300"><?php echo e($acc->email); ?></td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold <?php echo e($acc->group_id > 0 ? 'bg-amber-950 text-amber-300 border border-amber-500/30' : 'bg-slate-800 text-slate-400'); ?>">
                                    <?php echo e($acc->group_id); ?>

                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold"><?php echo e($acc->sex); ?></td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo e($acc->state === 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-red-950 text-red-400 border border-red-500/30'); ?>">
                                    <?php echo e($acc->state === 0 ? 'Active' : 'Banned'); ?>

                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <form method="POST" action="<?php echo e(route('admin.accounts.ban', $acc->account_id)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="px-2.5 py-1 rounded text-[11px] font-semibold <?php echo e($acc->state === 0 ? 'bg-red-950/80 hover:bg-red-900 text-red-300 border border-red-500/30' : 'bg-emerald-950/80 hover:bg-emerald-900 text-emerald-300 border border-emerald-500/30'); ?>">
                                        <?php echo e($acc->state === 0 ? 'Ban Account' : 'Unban'); ?>

                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-500">No game accounts found matching criteria.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-2">
        <?php echo e($gameAccounts->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/accounts.blade.php ENDPATH**/ ?>