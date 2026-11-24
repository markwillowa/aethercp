<?php $__env->startSection('admin_content'); ?>
<div x-data="{
    showCreateModal: false,
    showEditModal: false,
    editData: {
        id: null,
        title: '',
        category: 'Full Client',
        version: '',
        file_size: '',
        url_mirror1: '',
        url_mirror2: '',
        url_mirror3: '',
        description: '',
        sort_order: 0,
        is_active: true
    },
    openEdit(item) {
        this.editData = { ...item };
        this.showEditModal = true;
    }
}" class="space-y-6">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Downloads &amp; Client Manager</h1>
            <p class="text-xs text-slate-400 mt-1">Configure client packages, mirrors, patches, and tools distributed to players on the public downloads portal.</p>
        </div>
        <button @click="showCreateModal = true" class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs flex items-center gap-2 shadow-lg shadow-cyan-500/20 transition-all shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Add Download Package</span>
        </button>
    </div>

    <!-- Flash notifications -->
    <?php if(session('success')): ?>
        <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                <span>Total Packages</span>
                <i data-lucide="package" class="w-4 h-4 text-cyan-400"></i>
            </div>
            <p class="text-xl font-bold text-white"><?php echo e($downloads->count()); ?></p>
        </div>

        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                <span>Active on Public Portal</span>
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <p class="text-xl font-bold text-emerald-400"><?php echo e($downloads->where('is_active', true)->count()); ?></p>
        </div>

        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                <span>Distribution Categories</span>
                <i data-lucide="layers" class="w-4 h-4 text-amber-400"></i>
            </div>
            <p class="text-xl font-bold text-amber-400"><?php echo e($downloads->pluck('category')->unique()->count()); ?></p>
        </div>
    </div>

    <!-- Downloads Table -->
    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-semibold">
                    <tr>
                        <th class="py-3 px-4 text-center">Sort</th>
                        <th class="py-3 px-4">Title &amp; Category</th>
                        <th class="py-3 px-4">Version &amp; Size</th>
                        <th class="py-3 px-4">Download Mirrors</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $downloads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-4 text-center font-mono text-slate-500 font-bold">
                                <?php echo e($item->sort_order); ?>

                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-white text-sm flex items-center gap-2">
                                    <span><?php echo e($item->title); ?></span>
                                    <?php
                                        $catColors = [
                                            'Full Client' => 'bg-cyan-950 text-cyan-300 border-cyan-500/30',
                                            'Lite Client' => 'bg-blue-950 text-blue-300 border-blue-500/30',
                                            'Patch' => 'bg-emerald-950 text-emerald-300 border-emerald-500/30',
                                            'Tool' => 'bg-amber-950 text-amber-300 border-amber-500/30',
                                        ];
                                        $catColor = $catColors[$item->category] ?? 'bg-slate-800 text-slate-300 border-slate-700';
                                    ?>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border <?php echo e($catColor); ?>">
                                        <?php echo e($item->category); ?>

                                    </span>
                                </div>
                                <?php if($item->description): ?>
                                    <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1"><?php echo e($item->description); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-300">
                                <div><span class="text-slate-500">Ver:</span> <?php echo e($item->version ?: 'Latest'); ?></div>
                                <div><span class="text-slate-500">Size:</span> <?php echo e($item->file_size ?: '—'); ?></div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <?php if($item->url_mirror1): ?>
                                        <a href="<?php echo e($item->url_mirror1); ?>" target="_blank" rel="noopener" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-cyan-400 font-mono text-[10px] flex items-center gap-1">
                                            <i data-lucide="external-link" class="w-3 h-3"></i>
                                            <span>Mirror 1</span>
                                        </a>
                                    <?php endif; ?>
                                    <?php if($item->url_mirror2): ?>
                                        <a href="<?php echo e($item->url_mirror2); ?>" target="_blank" rel="noopener" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-blue-400 font-mono text-[10px] flex items-center gap-1">
                                            <i data-lucide="external-link" class="w-3 h-3"></i>
                                            <span>Mirror 2</span>
                                        </a>
                                    <?php endif; ?>
                                    <?php if($item->url_mirror3): ?>
                                        <a href="<?php echo e($item->url_mirror3); ?>" target="_blank" rel="noopener" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-purple-400 font-mono text-[10px] flex items-center gap-1">
                                            <i data-lucide="external-link" class="w-3 h-3"></i>
                                            <span>Mirror 3</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form method="POST" action="<?php echo e(route('admin.downloads.toggle', $item->id)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase transition-colors <?php echo e($item->is_active ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-900/50' : 'bg-slate-800 text-slate-500 border border-slate-700 hover:bg-slate-700'); ?>">
                                        <?php echo e($item->is_active ? 'Active' : 'Hidden'); ?>

                                    </button>
                                </form>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEdit(<?php echo e(json_encode($item)); ?>)" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-cyan-400 transition-colors" title="Edit Download">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    <form method="POST" action="<?php echo e(route('admin.downloads.delete', $item->id)); ?>" onsubmit="return confirm('Are you sure you want to delete download package <?php echo e($item->title); ?>?');" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-red-950 text-slate-400 hover:text-red-400 transition-colors" title="Delete Download">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <i data-lucide="download-cloud" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                                <p>No client download packages configured yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Download Modal -->
    <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showCreateModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-cyan-400"></i>
                    <span>Create Download Package</span>
                </h3>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="<?php echo e(route('admin.downloads.store')); ?>" class="space-y-4 text-xs">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-300 mb-1">Package Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Official Full Game Client (v1.5)" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Category *</label>
                        <select name="category" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                            <option value="Full Client">Full Client</option>
                            <option value="Lite Client">Lite Client</option>
                            <option value="Patch">Patch</option>
                            <option value="Tool">Tool</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Version</label>
                        <input type="text" name="version" placeholder="e.g. 1.5.0" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">File Size</label>
                        <input type="text" name="file_size" placeholder="e.g. 2.4 GB" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="0" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Primary Download URL (Mirror 1) *</label>
                    <input type="url" name="url_mirror1" required placeholder="https://..." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Secondary Mirror URL (Mirror 2)</label>
                        <input type="url" name="url_mirror2" placeholder="https://..." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Tertiary Mirror URL (Mirror 3)</label>
                        <input type="url" name="url_mirror3" placeholder="https://..." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Description &amp; Installation Notes</label>
                    <textarea name="description" rows="3" placeholder="Includes full BGM, updated data.grf, and auto-patcher executable." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="create_active" value="1" checked class="rounded border-slate-800 bg-slate-950 text-cyan-500">
                    <label for="create_active" class="text-slate-300 font-medium">Publish package to public downloads page immediately</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold">Save Download Package</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Download Modal -->
    <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4 text-cyan-400"></i>
                    <span>Edit Download Package</span>
                </h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'<?php echo e(url('admin/downloads')); ?>/' + editData.id" method="POST" class="space-y-4 text-xs">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-300 mb-1">Package Title *</label>
                        <input type="text" name="title" x-model="editData.title" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Category *</label>
                        <select name="category" x-model="editData.category" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                            <option value="Full Client">Full Client</option>
                            <option value="Lite Client">Lite Client</option>
                            <option value="Patch">Patch</option>
                            <option value="Tool">Tool</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Version</label>
                        <input type="text" name="version" x-model="editData.version" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">File Size</label>
                        <input type="text" name="file_size" x-model="editData.file_size" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" x-model="editData.sort_order" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Primary Download URL (Mirror 1) *</label>
                    <input type="url" name="url_mirror1" x-model="editData.url_mirror1" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Secondary Mirror URL (Mirror 2)</label>
                        <input type="url" name="url_mirror2" x-model="editData.url_mirror2" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Tertiary Mirror URL (Mirror 3)</label>
                        <input type="url" name="url_mirror3" x-model="editData.url_mirror3" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Description &amp; Installation Notes</label>
                    <textarea name="description" x-model="editData.description" rows="3" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="edit_active" value="1" :checked="editData.is_active" class="rounded border-slate-800 bg-slate-950 text-cyan-500">
                    <label for="edit_active" class="text-slate-300 font-medium">Publish package to public downloads page</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold">Update Package</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/downloads.blade.php ENDPATH**/ ?>