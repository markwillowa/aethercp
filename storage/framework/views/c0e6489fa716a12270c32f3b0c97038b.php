<?php $__env->startSection('title', 'Media & Screenshot Gallery'); ?>

<?php $__env->startSection('content'); ?>
<div class="chronicles-container py-4">

    <!-- Page Header Banner -->
    <div class="chronicles-panel mb-4 p-5 text-center relative overflow-hidden" 
         style="background: linear-gradient(180deg, rgba(31, 23, 40, 0.95) 0%, rgba(20, 15, 27, 0.98) 100%);">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-black/60 border border-[#D4AF37]/50 text-[#FCE69B] text-[10px] font-cinzel font-bold tracking-widest uppercase mb-2">
            <span>✦</span>
            <span>CHRONICLES ARCHIVE</span>
            <span>✦</span>
        </div>
        <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F3E5AB] tracking-wide uppercase drop-shadow-md">
            Media &amp; Screenshot Gallery
        </h1>
        <p class="text-xs text-[#C8BDAA] max-w-2xl mx-auto mt-2 leading-relaxed">
            Witness the epic sagas of Rune-Midgard. Explore legendary towns, fierce War of Emperium sieges, and community gatherings captured by fellow adventurers.
        </p>

        <!-- Category Filter Tabs -->
        <div class="flex items-center justify-center flex-wrap gap-2 mt-4 pt-3 border-t border-[#4A3A5A]">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catKey => $catLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('gallery.index', ['category' => $catKey])); ?>" 
                   class="px-3 py-1 rounded text-xs font-cinzel font-bold transition-all <?php echo e(($category === $catKey || ($catKey === 'all' && $category === 'all')) ? 'bg-gradient-to-r from-[#D4AF37] to-[#B69A65] text-[#1A1224] shadow-md' : 'bg-[#291F34] text-[#D8CEE0] border border-[#5A456C] hover:border-[#D4AF37] hover:text-[#FFF]'); ?>">
                    <?php echo e($catLabel); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php $__empty_1 = true; $__currentLoopData = $galleryItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="chronicles-panel flex flex-col group overflow-hidden">
                <div class="chronicles-panel-header">
                    <span class="chronicles-panel-title">
                        <i data-lucide="image"></i>
                        <span><?php echo e($item['title'] ?? 'Rune-Midgard Scene'); ?></span>
                    </span>
                    <span class="text-[9px] font-cinzel text-[#D4AF37] bg-[#2E203B] px-2 py-0.5 rounded border border-[#5A456C]">
                        <?php echo e($item['category'] ?? 'Media'); ?>

                    </span>
                </div>
                
                <!-- Thumbnail with Zoom Trigger -->
                <div class="relative overflow-hidden cursor-pointer" onclick="openLightbox('<?php echo e($item['image']); ?>', '<?php echo e(addslashes($item['title'] ?? '')); ?>', '<?php echo e(addslashes($item['caption'] ?? '')); ?>')">
                    <img src="<?php echo e($item['image']); ?>" 
                         alt="<?php echo e($item['title'] ?? 'Screenshot'); ?>" 
                         class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <span class="px-3 py-1.5 rounded-full bg-black/75 border border-[#D4AF37] text-[#F3E5AB] text-xs font-cinzel font-bold flex items-center gap-1.5 shadow-xl">
                            <i data-lucide="maximize-2" class="w-3.5 h-3.5"></i>
                            <span>View Fullsize</span>
                        </span>
                    </div>
                </div>

                <!-- Caption / Info -->
                <div class="p-3 flex-1 flex flex-col justify-between bg-[#1B1525]">
                    <p class="text-xs text-[#C8BDAA] leading-relaxed line-clamp-2">
                        <?php echo e($item['caption'] ?? ''); ?>

                    </p>
                    <div class="mt-2.5 pt-2 border-t border-[#3B2C4C] flex items-center justify-between text-[10px] text-[#A396AD]">
                        <span><?php echo e($item['date'] ?? 'Chronicle'); ?></span>
                        <button type="button" 
                                onclick="openLightbox('<?php echo e($item['image']); ?>', '<?php echo e(addslashes($item['title'] ?? '')); ?>', '<?php echo e(addslashes($item['caption'] ?? '')); ?>')" 
                                class="text-[#E5A823] font-cinzel font-bold hover:underline">
                            Enlarge &raquo;
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full chronicles-panel p-10 text-center">
                <i data-lucide="image-off" class="w-10 h-10 text-[#7A6B56] mx-auto mb-2"></i>
                <h3 class="font-cinzel text-sm text-[#F3E5AB] font-bold">No Screenshots in this Category</h3>
                <p class="text-xs text-[#A396AD] mt-1">Check back soon as new chronicle memories are captured!</p>
                <a href="<?php echo e(route('gallery.index')); ?>" class="mt-3 inline-block chronicles-btn-gold text-xs py-1 px-4">
                    View All Media
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Back to Home Button -->
    <div class="mt-6 text-center">
        <a href="<?php echo e(route('home')); ?>" class="chronicles-btn-dark inline-flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Return to Main Chronicle</span>
        </a>
    </div>

</div>

<!-- Lightbox Modal -->
<div id="gallery-lightbox" class="fixed inset-0 z-50 bg-black/90 hidden items-center justify-center p-4 backdrop-blur-sm" onclick="closeLightbox(event)">
    <div class="relative max-w-4xl w-full bg-[#1A1224] border-2 border-[#D4AF37] rounded-lg overflow-hidden shadow-2xl p-2" onclick="event.stopPropagation()">
        <!-- Close Button -->
        <button type="button" onclick="closeLightboxDirect()" class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-black/80 border border-[#D4AF37] text-[#F3E5AB] hover:text-white flex items-center justify-center text-sm font-bold">
            &times;
        </button>

        <!-- Image Container -->
        <div class="overflow-hidden rounded border border-[#4A3A5A] max-h-[75vh] flex items-center justify-center bg-black">
            <img id="lightbox-img" src="" alt="Enlarged Screenshot" class="max-h-[75vh] w-auto max-w-full object-contain">
        </div>

        <!-- Lightbox Caption Bar -->
        <div class="p-3 bg-[#20172C] border-t border-[#4A3A5A] mt-2 flex items-center justify-between">
            <div>
                <h3 id="lightbox-title" class="font-cinzel font-bold text-sm text-[#F3E5AB]"></h3>
                <p id="lightbox-caption" class="text-xs text-[#C8BDAA] mt-0.5"></p>
            </div>
            <a id="lightbox-raw" href="" target="_blank" class="px-3 py-1 rounded bg-[#352648] border border-[#5A456C] text-[#E2D9EB] hover:text-[#FFD700] text-xs font-cinzel">
                Original URL
            </a>
        </div>
    </div>
</div>

<script>
    function openLightbox(url, title, caption) {
        document.getElementById('lightbox-img').src = url;
        document.getElementById('lightbox-title').innerText = title;
        document.getElementById('lightbox-caption').innerText = caption;
        document.getElementById('lightbox-raw').href = url;
        const modal = document.getElementById('gallery-lightbox');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeLightboxDirect() {
        const modal = document.getElementById('gallery-lightbox');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function closeLightbox(event) {
        if (event.target.id === 'gallery-lightbox') {
            closeLightboxDirect();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightboxDirect();
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\themes\aether-chronicles\views/gallery/index.blade.php ENDPATH**/ ?>