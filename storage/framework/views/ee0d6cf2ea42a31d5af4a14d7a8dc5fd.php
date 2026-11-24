<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Server News & Announcements</h1>
            <p class="text-xs text-slate-400 mt-1">Stay informed with official game updates, maintenance alerts, and events.</p>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap items-center gap-2">
            <?php $cats = ['all' => 'All Articles', 'news' => 'News', 'announcement' => 'Announcements', 'event' => 'Events', 'update' => 'Updates', 'maintenance' => 'Maintenance']; ?>
            <?php $__currentLoopData = $cats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($key === 'all' ? route('news.index') : route('news.index', ['category' => $key])); ?>"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors <?php echo e(($category === $key || (!$category && $key === 'all')) ? 'bg-cyan-500 text-slate-950 shadow-sm' : 'bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800'); ?>">
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="bg-slate-900/60 border border-slate-800 hover:border-slate-700/80 rounded-2xl p-6 transition-all duration-200 backdrop-blur-sm">
                <div class="flex items-center gap-2.5 mb-2.5">
                    <?php if($article->is_pinned): ?>
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                            Pinned
                        </span>
                    <?php endif; ?>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-slate-800 text-slate-300">
                        <?php echo e($article->category); ?>

                    </span>
                    <span class="text-xs text-slate-500"><?php echo e($article->published_at?->format('M d, Y')); ?> (<?php echo e($article->published_at?->diffForHumans()); ?>)</span>
                </div>

                <h2 class="text-xl font-bold text-white mb-2 hover:text-cyan-400 transition-colors">
                    <a href="<?php echo e(route('news.show', $article->slug)); ?>"><?php echo e($article->title); ?></a>
                </h2>

                <p class="text-sm text-slate-300 leading-relaxed mb-4">
                    <?php echo e($article->summary); ?>

                </p>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-800/60">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                        Posted by <?php echo e($article->author_name); ?>

                    </span>
                    <a href="<?php echo e(route('news.show', $article->slug)); ?>" class="text-cyan-400 hover:underline flex items-center gap-1 font-medium">
                        <span>Read Full Story</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="bg-slate-900/40 border border-slate-800 rounded-2xl p-12 text-center text-slate-400">
                <p>No articles found in this category.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="pt-4">
        <?php echo e($news->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/news/index.blade.php ENDPATH**/ ?>