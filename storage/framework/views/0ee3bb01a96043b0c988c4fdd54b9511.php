<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">War of Emperium & Castles</h1>
            <p class="text-xs text-slate-400 mt-1">Conquered realm castles, defending alliances, and fortress defense metrics.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-950 text-amber-300 border border-amber-500/30">
                Siege Active: Wed & Sat
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $castles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                            Castle #<?php echo e($c->castle_id); ?>

                        </span>
                        <?php if($c->guild_id > 0): ?>
                            <img src="<?php echo e(route('guilds.emblem', $c->guild_id)); ?>" alt="Guild Emblem" class="w-6 h-6 rounded" loading="lazy">
                        <?php endif; ?>
                    </div>

                    <h3 class="text-lg font-bold text-white">
                        <?php echo e($castleNames[$c->castle_id] ?? ("Agit #{$c->castle_id}")); ?>

                    </h3>

                    <div class="text-xs text-slate-400 space-y-1">
                        <p>Defending Guild:
                            <?php if($c->guild_id > 0 && !empty($c->guild_name)): ?>
                                <a href="<?php echo e(route('guilds.show', $c->guild_id)); ?>" class="font-bold text-cyan-300 hover:underline"><?php echo e($c->guild_name); ?></a>
                            <?php else: ?>
                                <span class="text-slate-500 italic">Unoccupied</span>
                            <?php endif; ?>
                        </p>
                        <?php if(!empty($c->guild_master)): ?>
                            <p>Leader: <span class="text-slate-300"><?php echo e($c->guild_master); ?></span></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs pt-3 border-t border-slate-800 bg-slate-950/60 p-2.5 rounded-xl">
                    <div>
                        <span class="text-slate-500 block text-[10px]">Defense</span>
                        <strong class="text-emerald-400 font-bold"><?php echo e($c->defense ?? 0); ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[10px]">Economy</span>
                        <strong class="text-amber-400 font-bold"><?php echo e($c->economy ?? 0); ?></strong>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-3 bg-slate-900/40 border border-slate-800 rounded-2xl p-12 text-center text-slate-500">
                No active castle records found in game database.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/woe/index.blade.php ENDPATH**/ ?>