<?php $__env->startSection('admin_content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">News & Content Management</h1>
            <p class="text-xs text-slate-400 mt-1">Create and manage server announcements and patch updates.</p>
        </div>
        <a href="<?php echo e(route('admin.news.create')); ?>" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-semibold rounded-xl text-xs shadow-md transition-all flex items-center gap-1.5">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Create Article</span>
        </a>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                <tr>
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Category</th>
                    <th class="py-3 px-4">Author</th>
                    <th class="py-3 px-4 text-center">Pinned</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                <?php $__empty_1 = true; $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-800/30">
                        <td class="py-3 px-4 font-bold text-white"><?php echo e($a->title); ?></td>
                        <td class="py-3 px-4 uppercase text-[10px] text-cyan-400 font-semibold"><?php echo e($a->category); ?></td>
                        <td class="py-3 px-4 text-slate-300"><?php echo e($a->author_name); ?></td>
                        <td class="py-3 px-4 text-center font-bold"><?php echo e($a->is_pinned ? 'YES' : 'NO'); ?></td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo e($a->is_published ? 'bg-emerald-950 text-emerald-400' : 'bg-slate-800 text-slate-400'); ?>">
                                <?php echo e($a->is_published ? 'Published' : 'Draft'); ?>

                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <form method="POST" action="<?php echo e(route('admin.news.delete', $a->id)); ?>" onsubmit="return confirm('Delete this article?')" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500">No news articles found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="pt-2">
        <?php echo e($articles->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/news/index.blade.php ENDPATH**/ ?>