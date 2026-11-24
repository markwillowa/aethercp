<?php $__env->startSection('admin_content'); ?>
<div class="space-y-6">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Theme & Layout Manager</h1>
        <p class="text-xs text-slate-400 mt-1">Switch control panel themes without modifying core codebase.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $themes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $thm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="relative bg-slate-900/80 border <?php echo e($thm['is_active'] ? 'border-cyan-500 shadow-2xl aether-glow-cyan' : 'border-slate-800'); ?> rounded-2xl p-6 backdrop-blur-md flex flex-col justify-between space-y-4">
                <?php if($thm['is_active']): ?>
                    <div class="absolute -top-3 right-4 bg-cyan-500 text-slate-950 text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full shadow-md">
                        Active Theme
                    </div>
                <?php endif; ?>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono text-cyan-400 font-bold"><?php echo e($slug); ?></span>
                        <span class="text-xs text-slate-500 font-mono">v<?php echo e($thm['version']); ?></span>
                    </div>

                    <h3 class="text-base font-bold text-white"><?php echo e($thm['name']); ?></h3>
                    <p class="text-xs text-slate-400 leading-relaxed"><?php echo e($thm['description']); ?></p>
                    <p class="text-[11px] text-slate-500">Author: <span class="text-slate-300"><?php echo e($thm['author']); ?></span></p>
                </div>

                <div class="pt-3 border-t border-slate-800">
                    <?php if($thm['is_active']): ?>
                        <button disabled class="w-full py-2 rounded-xl bg-slate-800 text-slate-400 text-xs font-semibold cursor-not-allowed">
                            Currently Active
                        </button>
                    <?php else: ?>
                        <form method="POST" action="<?php echo e(route('admin.themes.activate', $slug)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold transition-colors">
                                Activate Theme
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/themes.blade.php ENDPATH**/ ?>