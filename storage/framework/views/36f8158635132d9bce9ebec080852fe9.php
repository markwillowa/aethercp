<?php $__env->startSection('title', 'Gallery Manager'); ?>

<?php $__env->startSection('admin_content'); ?>
<div x-data="{
    showAddModal: false,
    showEditModal: false,
    selectedCategory: 'all',
    editItem: {
        id: null,
        title: '',
        category: 'Towns',
        image: '',
        caption: '',
        date: ''
    },
    openEdit(item) {
        this.editItem = { ...item };
        this.showEditModal = true;
    }
}" class="space-y-6">

    <!-- Top Action Bar -->
    <div class="border-b border-slate-800 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
                <i data-lucide="image" class="w-6 h-6 text-cyan-400"></i>
                <span>Media &amp; Screenshot Gallery Manager</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Manage all screenshot chronicles displayed on the public <a href="<?php echo e(route('gallery.index')); ?>" target="_blank" class="text-cyan-400 hover:underline">/gallery</a> page and the homepage 15-second rotating showcase.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <form method="POST" action="<?php echo e(route('admin.gallery.reset')); ?>" onsubmit="return confirm('Reset all gallery items to curated defaults? Custom items will be replaced.');">
                <?php echo csrf_field(); ?>
                <button type="submit" class="px-3 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors flex items-center gap-1.5 border border-slate-700">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Reset Defaults</span>
                </button>
            </form>

            <button type="button" @click="showAddModal = true" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-colors flex items-center gap-2 shadow-lg">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Add Screenshot</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                <span>Total Screenshots</span>
                <i data-lucide="images" class="w-4 h-4 text-cyan-400"></i>
            </div>
            <p class="text-2xl font-extrabold text-white"><?php echo e(count($galleryItems)); ?></p>
            <p class="text-[11px] text-slate-500 mt-0.5">Showcased in gallery</p>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                <span>Active Categories</span>
                <i data-lucide="tag" class="w-4 h-4 text-amber-400"></i>
            </div>
            <p class="text-2xl font-extrabold text-white"><?php echo e(count(array_unique(array_column($galleryItems, 'category')))); ?></p>
            <p class="text-[11px] text-amber-400/80 mt-0.5">Towns, WoE, Raids, Events</p>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                <span>Homepage Carousel</span>
                <i data-lucide="sparkles" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <p class="text-2xl font-extrabold text-emerald-400">15s Active</p>
            <p class="text-[11px] text-emerald-400/80 mt-0.5">Auto-rotates randomly</p>
        </div>
    </div>

    <!-- Category Filter Bar -->
    <div class="flex items-center gap-2 flex-wrap">
        <button type="button" @click="selectedCategory = 'all'" 
                :class="selectedCategory === 'all' ? 'bg-cyan-500/15 text-cyan-400 border-cyan-500/30' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white'"
                class="px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all">
            All Media (<?php echo e(count($galleryItems)); ?>)
        </button>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $catCount = count(array_filter($galleryItems, fn($i) => strtolower($i['category'] ?? '') === strtolower($cat))); ?>
            <button type="button" @click="selectedCategory = '<?php echo e(strtolower($cat)); ?>'" 
                    :class="selectedCategory === '<?php echo e(strtolower($cat)); ?>' ? 'bg-cyan-500/15 text-cyan-400 border-cyan-500/30' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white'"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all">
                <?php echo e($cat); ?> (<?php echo e($catCount); ?>)
            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Screenshots Grid / Table -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php $__empty_1 = true; $__currentLoopData = $galleryItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div x-show="selectedCategory === 'all' || selectedCategory === '<?php echo e(strtolower($item['category'] ?? '')); ?>'"
                 class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden flex flex-col justify-between group shadow-lg transition-all hover:border-slate-700">
                
                <!-- Image Header -->
                <div class="relative aspect-video bg-black overflow-hidden">
                    <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute top-2 left-2 flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-black/75 border border-slate-700 text-white">
                            #<?php echo e($item['id']); ?>

                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-950/80 border border-cyan-600/40 text-cyan-400">
                            <?php echo e($item['category']); ?>

                        </span>
                    </div>
                    <span class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-mono text-slate-300 bg-black/70">
                        <?php echo e($item['date'] ?? 'Chronicle'); ?>

                    </span>
                </div>

                <!-- Info Body -->
                <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <h3 class="text-sm font-bold text-white leading-snug"><?php echo e($item['title']); ?></h3>
                        <p class="text-xs text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                            <?php echo e($item['caption'] ?: 'No caption provided.'); ?>

                        </p>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-800/80">
                        <div class="text-[10px] text-slate-500 truncate font-mono bg-slate-950 px-2 py-1 rounded border border-slate-800/60" title="<?php echo e($item['image']); ?>">
                            <?php echo e($item['image']); ?>

                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-1">
                            <a href="<?php echo e($item['image']); ?>" target="_blank" class="text-xs text-cyan-400 hover:underline flex items-center gap-1">
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                                <span>Preview</span>
                            </a>

                            <div class="flex items-center gap-1.5">
                                <button type="button" 
                                        @click="openEdit(<?php echo e(json_encode($item)); ?>)"
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors flex items-center gap-1">
                                    <i data-lucide="edit" class="w-3 h-3 text-cyan-400"></i>
                                    <span>Edit</span>
                                </button>

                                <form method="POST" action="<?php echo e(route('admin.gallery.destroy', $item['id'])); ?>" onsubmit="return confirm('Delete this screenshot from the gallery?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-1 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-950/30 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full bg-slate-900/60 border border-slate-800 rounded-2xl p-12 text-center space-y-3">
                <i data-lucide="image-off" class="w-10 h-10 text-slate-600 mx-auto"></i>
                <h3 class="text-sm font-bold text-white">No Gallery Screenshots Found</h3>
                <p class="text-xs text-slate-400">Click below to add your first screenshot or restore the curated Ragnarok presets.</p>
                <div class="flex items-center justify-center gap-3 pt-2">
                    <button type="button" @click="showAddModal = true" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500 text-slate-950">
                        Add New Screenshot
                    </button>
                    <form method="POST" action="<?php echo e(route('admin.gallery.reset')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300">
                            Reset Presets
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- ======================================================================
         MODAL: ADD SCREENSHOT
         ====================================================================== -->
    <div x-show="showAddModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-700 rounded-2xl max-w-lg w-full p-6 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-cyan-400"></i>
                    <span>Add Screenshot to Gallery</span>
                </h3>
                <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-white text-lg">&times;</button>
            </div>

            <form method="POST" action="<?php echo e(route('admin.gallery.store')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Title</label>
                    <input type="text" name="title" required placeholder="e.g. Geffen Magic Citadel" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Category</label>
                        <select name="category" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                            <option value="Towns">Towns &amp; Realms</option>
                            <option value="WoE">War of Emperium</option>
                            <option value="Events">Events &amp; Festivals</option>
                            <option value="Raids">Dungeons &amp; Raids</option>
                            <option value="Community">Community</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Chronicle / Date Tag</label>
                        <input type="text" name="date" placeholder="e.g. Chronicle VII" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Image URL or Local Path</label>
                    <input type="text" name="image" required placeholder="e.g. https://... or /themes/aether-chronicles/assets/images/screenshot_prontera.jpg" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                    <p class="text-[10px] text-slate-500 mt-1">Accepts any direct image URL (https://) or local theme path.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Caption / Description</label>
                    <textarea name="caption" rows="3" placeholder="Brief lore, story or description of the screenshot..." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950">Save Screenshot</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================================
         MODAL: EDIT SCREENSHOT
         ====================================================================== -->
    <div x-show="showEditModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-700 rounded-2xl max-w-lg w-full p-6 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="edit" class="w-4 h-4 text-cyan-400"></i>
                    <span>Edit Screenshot #<span x-text="editItem.id"></span></span>
                </h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-white text-lg">&times;</button>
            </div>

            <form method="POST" :action="'<?php echo e(url('/admin/gallery')); ?>/' + editItem.id" class="space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Title</label>
                    <input type="text" name="title" x-model="editItem.title" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Category</label>
                        <select name="category" x-model="editItem.category" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                            <option value="Towns">Towns &amp; Realms</option>
                            <option value="WoE">War of Emperium</option>
                            <option value="Events">Events &amp; Festivals</option>
                            <option value="Raids">Dungeons &amp; Raids</option>
                            <option value="Community">Community</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Chronicle / Date Tag</label>
                        <input type="text" name="date" x-model="editItem.date" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Image URL or Local Path</label>
                    <input type="text" name="image" x-model="editItem.image" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Caption / Description</label>
                    <textarea name="caption" x-model="editItem.caption" rows="3" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-white"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950">Update Screenshot</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/gallery/index.blade.php ENDPATH**/ ?>