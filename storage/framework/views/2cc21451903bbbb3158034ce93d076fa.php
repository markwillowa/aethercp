<?php $__env->startSection('content'); ?>
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Chronicles & Announcements
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Official patch notes, scheduled maintenance, and community updates
            </p>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap items-center gap-2">
            <?php 
                $cats = ['all' => 'All Chronicles', 'news' => 'News', 'announcement' => 'Announcements', 'event' => 'Events', 'update' => 'Patch Notes', 'maintenance' => 'Maintenance']; 
            ?>
            <?php $__currentLoopData = $cats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($key === 'all' ? route('news.index') : route('news.index', ['category' => $key])); ?>"
                   class="px-3 py-1.5 rounded-full text-xs font-cinzel font-bold uppercase tracking-wider transition-all <?php echo e(($category === $key || (!$category && $key === 'all')) ? 'bg-gradient-to-b from-[#F3B33D] to-[#9E5600] text-[#3B1F0A] border-2 border-[#FFF3D6] shadow-md' : 'bg-[#3B1F0A]/80 border border-[#C99A6B]/50 text-[#FFE4A0] hover:bg-[#563013]'); ?>">
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- News List -->
    <div class="space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $cat = strtoupper($article->category ?? 'NEWS');
                $badgeColor = match($cat) {
                    'EVENT' => 'bg-[#2E7D32] text-white',
                    'MAINT', 'MAINTENANCE' => 'bg-[#D84315] text-white',
                    default => 'bg-[#C2185B] text-white',
                };
            ?>
            <article class="chronicles-panel-ivy flex flex-col sm:flex-row items-start gap-5 hover:-translate-y-0.5 transition-transform">
                <div class="w-full sm:w-24 sm:h-24 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] flex sm:flex-col items-center justify-center p-3 text-center shrink-0">
                    <span class="font-cinzel text-2xl font-black text-[#D4441D]"><?php echo e($article->published_at?->format('d') ?? $article->created_at->format('d')); ?></span>
                    <span class="text-[10px] uppercase tracking-wider font-bold text-[#8C7A68]"><?php echo e($article->published_at?->format('M Y') ?? $article->created_at->format('M Y')); ?></span>
                </div>

                <div class="flex-grow space-y-2 min-w-0">
                    <div class="flex items-center gap-2">
                        <?php if($article->is_pinned): ?>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#FFD700] text-[#3B1F0A] border border-[#B8860B] font-cinzel">
                                Pinned
                            </span>
                        <?php endif; ?>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider <?php echo e($badgeColor); ?>">
                            <?php echo e($article->category); ?>

                        </span>
                        <span class="text-xs text-[#8C7A68]"><?php echo e($article->published_at?->diffForHumans()); ?></span>
                    </div>

                    <h2 class="font-cinzel text-lg font-bold text-[#4A2200] hover:text-[#D4441D] transition-colors">
                        <a href="<?php echo e(route('news.show', $article->slug)); ?>"><?php echo e($article->title); ?></a>
                    </h2>

                    <p class="text-xs text-[#5A3D28] leading-relaxed line-clamp-2">
                        <?php echo e(Str::limit(strip_tags($article->content), 160)); ?>

                    </p>

                    <div class="pt-1">
                        <a href="<?php echo e(route('news.show', $article->slug)); ?>" class="text-xs font-bold text-[#8B5A2B] hover:text-[#D4441D] inline-flex items-center gap-1">
                            <span>Read Full Chronicle</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="chronicles-panel-ivy text-center py-12">
                <i data-lucide="book-open" class="w-12 h-12 text-[#A88B57] mx-auto mb-3"></i>
                <h3 class="font-cinzel text-lg font-bold text-[#4A2200]">No Chronicle Records Found</h3>
                <p class="text-xs text-[#795E43] mt-1">Check back soon for patch updates and realm notices.</p>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <?php echo e($news->links()); ?>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\themes\aether-chronicles\views/news/index.blade.php ENDPATH**/ ?>