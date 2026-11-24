<?php $__env->startSection('admin_content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-800 pb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h1 class="text-2xl font-bold text-white">Database & YAML Importer</h1>
                <p class="text-xs text-slate-400 mt-1">
                    Upload and synchronize raw rAthena &amp; Hercules database YAML files (<code>item_db.yml</code>, <code>item_db_equip.yml</code>, <code>item_db2.yml</code>) directly into your live game database.
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-950 text-cyan-300 border border-cyan-500/30">
                <i data-lucide="cpu" class="w-3.5 h-3.5"></i>
                Streaming Parser O(1) Memory
            </span>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs space-y-2">
            <div class="flex items-center gap-2 font-bold text-sm text-emerald-400">
                <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
            <?php if(session('import_result')): ?>
                <?php $res = session('import_result'); ?>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-[11px] border-t border-emerald-500/20 text-emerald-200/90 font-mono">
                    <div>Processed: <strong class="text-white"><?php echo e(number_format($res['processed'])); ?></strong></div>
                    <div>Ingested: <strong class="text-white"><?php echo e(number_format($res['imported'])); ?></strong></div>
                    <div>Table: <strong class="text-white"><?php echo e($res['table']); ?></strong></div>
                    <div>Duration: <strong class="text-white"><?php echo e($res['elapsed_seconds']); ?>s</strong></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Database Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 backdrop-blur-md flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Official Item DB (item_db)</span>
                <div class="text-2xl font-black text-white mt-1"><?php echo e(number_format($itemDbCount)); ?> <span class="text-xs font-normal text-slate-400">items</span></div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                <i data-lucide="database" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 backdrop-blur-md flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Custom Item DB (item_db2)</span>
                <div class="text-2xl font-black text-cyan-400 mt-1"><?php echo e(number_format($itemDb2Count)); ?> <span class="text-xs font-normal text-slate-400">items</span></div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400">
                <i data-lucide="sparkles" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 backdrop-blur-md flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Database Engine</span>
                <div class="text-sm font-bold text-emerald-400 mt-1.5 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Dual-DB Connected</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <i data-lucide="server" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Suggested Local Files (if found on host machine) -->
    <?php if(!empty($suggestedPaths)): ?>
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 backdrop-blur-md space-y-3">
            <div class="flex items-center gap-2">
                <i data-lucide="folder-search" class="w-4 h-4 text-cyan-400"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">Detected Emulator YAML Files on Server</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                <?php $__currentLoopData = $suggestedPaths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/80 border border-slate-800 hover:border-cyan-500/40 transition-colors">
                        <div class="overflow-hidden pr-2">
                            <span class="font-bold text-slate-200 block truncate"><?php echo e($s['filename']); ?></span>
                            <span class="text-[10px] text-slate-500 block truncate font-mono"><?php echo e($s['path']); ?> (<?php echo e($s['size']); ?>)</span>
                        </div>
                        <button type="button" onclick="selectServerPath('<?php echo e(addslashes($s['path'])); ?>')"
                            class="shrink-0 px-2.5 py-1 rounded bg-slate-800 hover:bg-cyan-600 text-slate-200 hover:text-white text-[11px] font-semibold transition-colors">
                            Use This File
                        </button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Import Form -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl space-y-6">
        <div>
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <i data-lucide="upload-cloud" class="w-5 h-5 text-cyan-400"></i>
                <span>Import Configuration &amp; Ingestion</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Choose your YAML input source and target table. Large files stream in chunks to prevent timeouts.</p>
        </div>

        <form method="POST" action="<?php echo e(route('admin.importer.run')); ?>" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>

            <!-- Source Type Tabs -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">1. Select Input Source</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer border border-slate-800 rounded-xl p-3 flex flex-col items-center gap-1.5 text-center transition-all bg-slate-950/60 hover:border-cyan-500 has-checked:border-cyan-500 has-checked:bg-cyan-500/10 has-checked:text-cyan-300">
                        <input type="radio" name="source_type" value="upload" checked class="sr-only" onchange="toggleSourceType('upload')">
                        <i data-lucide="upload" class="w-5 h-5 text-slate-400"></i>
                        <span class="text-xs font-bold text-white">Upload File</span>
                        <span class="text-[10px] text-slate-500">From your computer</span>
                    </label>

                    <label class="cursor-pointer border border-slate-800 rounded-xl p-3 flex flex-col items-center gap-1.5 text-center transition-all bg-slate-950/60 hover:border-cyan-500 has-checked:border-cyan-500 has-checked:bg-cyan-500/10 has-checked:text-cyan-300">
                        <input type="radio" name="source_type" value="path" class="sr-only" onchange="toggleSourceType('path')">
                        <i data-lucide="hard-drive" class="w-5 h-5 text-slate-400"></i>
                        <span class="text-xs font-bold text-white">Server File Path</span>
                        <span class="text-[10px] text-slate-500">Local VPS / PC disk</span>
                    </label>

                    <label class="cursor-pointer border border-slate-800 rounded-xl p-3 flex flex-col items-center gap-1.5 text-center transition-all bg-slate-950/60 hover:border-cyan-500 has-checked:border-cyan-500 has-checked:bg-cyan-500/10 has-checked:text-cyan-300">
                        <input type="radio" name="source_type" value="url" class="sr-only" onchange="toggleSourceType('url')">
                        <i data-lucide="link" class="w-5 h-5 text-slate-400"></i>
                        <span class="text-xs font-bold text-white">Remote URL</span>
                        <span class="text-[10px] text-slate-500">GitHub raw / HTTP</span>
                    </label>
                </div>
            </div>

            <!-- Dynamic Source Input Panels -->
            <div id="source_upload_panel" class="space-y-2">
                <label class="block text-xs font-semibold text-slate-300">Choose YAML File (.yml, .yaml)</label>
                <input type="file" name="yaml_file" accept=".yml,.yaml"
                    class="block w-full text-xs text-slate-300 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-600 file:text-white hover:file:bg-cyan-500 file:cursor-pointer border border-slate-800 rounded-xl bg-slate-950 p-1">
                <p class="text-[10px] text-slate-500">Supports standard rAthena &amp; Hercules <code>Header: Type: ITEM_DB</code> format.</p>
            </div>

            <div id="source_path_panel" class="space-y-2 hidden">
                <label class="block text-xs font-semibold text-slate-300">Server Absolute File Path</label>
                <input type="text" id="file_path_input" name="file_path" placeholder="C:/Users/villa/Desktop/FreinyxRO/rathena/db/pre-re/item_db_equip.yml"
                    class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 placeholder-slate-600 font-mono">
                <p class="text-[10px] text-slate-500">Directly streams from disk without HTTP upload overhead. Ideal for 40,000+ line databases.</p>
            </div>

            <div id="source_url_panel" class="space-y-2 hidden">
                <label class="block text-xs font-semibold text-slate-300">Remote YAML Raw URL</label>
                <input type="url" name="remote_url" placeholder="https://raw.githubusercontent.com/rathena/rathena/master/db/pre-re/item_db_equip.yml"
                    class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200 placeholder-slate-600 font-mono">
                <p class="text-[10px] text-slate-500">The server will fetch and stream the YAML file in the background.</p>
            </div>

            <!-- Target Table & Ingestion Mode -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-800/80">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">2. Target Table</label>
                    <select name="target_table" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        <option value="item_db" selected>Official Item Database (`item_db`)</option>
                        <option value="item_db2">Custom Server Items (`item_db2`)</option>
                    </select>
                    <span class="text-[10px] text-slate-500 mt-1 block">Choose `item_db2` for custom server items to keep official items untouched.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">3. Import Mode</label>
                    <select name="mode" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs text-slate-200">
                        <option value="upsert" selected>Upsert (Update Existing, Insert New) - Recommended</option>
                        <option value="insert_missing">Insert Missing Only (Preserve Existing Items)</option>
                        <option value="truncate">Truncate &amp; Fresh Ingest (Wipe Table First)</option>
                    </select>
                    <span class="text-[10px] text-slate-500 mt-1 block">Upsert ensures items are updated with latest scripts and sprites without data loss.</span>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="play" class="w-4 h-4"></i>
                    <span>Execute Streaming YAML Ingestion</span>
                </button>
            </div>
        </form>
    </div>

    <!-- CLI Instructions -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 backdrop-blur-md space-y-2">
        <div class="flex items-center gap-2">
            <i data-lucide="terminal" class="w-4 h-4 text-cyan-400"></i>
            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Artisan CLI Command</h3>
        </div>
        <p class="text-xs text-slate-400">For automated ingestion or cron synchronizations, run via terminal:</p>
        <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 font-mono text-xs text-cyan-300 overflow-x-auto select-all">
            php artisan aether:import-yaml "C:/path/to/item_db_equip.yml" --table=item_db --mode=upsert
        </div>
    </div>
</div>

<script>
    function toggleSourceType(type) {
        document.getElementById('source_upload_panel').classList.toggle('hidden', type !== 'upload');
        document.getElementById('source_path_panel').classList.toggle('hidden', type !== 'path');
        document.getElementById('source_url_panel').classList.toggle('hidden', type !== 'url');
    }

    function selectServerPath(path) {
        var radio = document.querySelector('input[name="source_type"][value="path"]');
        if (radio) {
            radio.checked = true;
            toggleSourceType('path');
        }
        var input = document.getElementById('file_path_input');
        if (input) {
            input.value = path;
            input.focus();
        }
    }
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\resources\views/admin/importer.blade.php ENDPATH**/ ?>