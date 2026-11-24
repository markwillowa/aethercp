<?php $__env->startSection('admin_content'); ?>
<div class="space-y-6">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Module & Extension Manager</h1>
        <p class="text-xs text-slate-400 mt-1">Enable, disable, and configure control panel modules and extensions.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider <?php echo e($mod['enabled'] ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400'); ?>">
                            <?php echo e($mod['enabled'] ? 'Active' : 'Disabled'); ?>

                        </span>
                        <span class="text-xs text-slate-500 font-mono">v<?php echo e($mod['version'] ?? '1.0.0'); ?></span>
                    </div>

                    <h3 class="text-base font-bold text-white"><?php echo e($mod['name']); ?></h3>
                    <p class="text-xs text-slate-400 leading-relaxed"><?php echo e($mod['description']); ?></p>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-[11px] text-slate-500">
                        <?php echo e(!empty($mod['protected']) ? 'Core Protected' : ($mod['is_core'] ? 'Core Module' : 'Custom Extension')); ?>

                    </span>

                    <?php if(empty($mod['protected'])): ?>
                        <div class="flex items-center gap-2">
                            <?php if($key === 'marketplace'): ?>
                                <a href="<?php echo e(route('admin.marketplace.index')); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#D4AF37]/20 hover:bg-[#D4AF37]/30 text-[#FCE69B] border border-[#D4AF37]/40 flex items-center gap-1 transition-colors">
                                    <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                                    Configure
                                </a>
                            <?php endif; ?>
                            <form method="POST" action="<?php echo e(route('admin.modules.toggle', $key)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?php echo e($mod['enabled'] ? 'bg-red-950/80 hover:bg-red-900 text-red-300 border border-red-500/30' : 'bg-emerald-950/80 hover:bg-emerald-900 text-emerald-300 border border-emerald-500/30'); ?>">
                                    <?php echo e($mod['enabled'] ? 'Disable' : 'Enable'); ?>

                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <span class="text-[11px] font-semibold text-slate-500">Locked</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/modules.blade.php ENDPATH**/ ?>