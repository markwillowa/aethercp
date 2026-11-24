<footer class="relative z-10 border-t border-slate-800/80 bg-[#070a13] mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
            <!-- Col 1: Brand & Bio -->
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-slate-950 font-bold">
                        <i data-lucide="shield" class="w-4 h-4 text-white"></i>
                    </div>
                    <span class="text-lg font-bold aether-gradient-text"><?php echo e(config('aether.name', 'AetherCP')); ?></span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed max-w-md">
                    <?php echo e(config('aether.description')); ?>

                </p>
                <div class="mt-4 flex items-center space-x-3 text-xs text-slate-500">
                    <span class="flex items-center gap-1">
                        <i data-lucide="cpu" class="w-3.5 h-3.5 text-cyan-400"></i>
                        Emulator: <strong class="text-slate-300 uppercase"><?php echo e(config('emulator.driver')); ?></strong>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-blue-400"></i>
                        Mode: <strong class="text-slate-300"><?php echo e(config('emulator.renewal') ? 'Renewal' : 'Pre-Renewal'); ?></strong>
                    </span>
                </div>
            </div>

            <!-- Col 2: Navigation Links -->
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Quick Navigation</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li><a href="<?php echo e(route('home')); ?>" class="hover:text-cyan-400 transition-colors">Server Home</a></li>
                    <li><a href="<?php echo e(route('news.index')); ?>" class="hover:text-cyan-400 transition-colors">Latest News</a></li>
                    <li><a href="<?php echo e(route('rankings.index')); ?>" class="hover:text-cyan-400 transition-colors">Player Rankings</a></li>
                    <li><a href="<?php echo e(route('itemdb.index')); ?>" class="hover:text-cyan-400 transition-colors">Item Database</a></li>
                    <li><a href="<?php echo e(route('downloads.index')); ?>" class="hover:text-cyan-400 transition-colors">Game Downloads</a></li>
                    <?php if(app(\App\Services\Module\ModuleManager::class)->isEnabled('faq')): ?>
                        <li><a href="<?php echo e(route('faq.index')); ?>" class="hover:text-cyan-400 transition-colors">Knowledge Base &amp; FAQ</a></li>
                    <?php endif; ?>
                    <?php if(app(\App\Services\Module\ModuleManager::class)->isEnabled('support')): ?>
                        <li><a href="<?php echo e(route('support.index')); ?>" class="hover:text-cyan-400 transition-colors">Support Center</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Col 3: Community & Rates -->
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Server Info</h4>
                <ul class="space-y-1.5 text-xs text-slate-400">
                    <li class="flex justify-between py-1 border-b border-slate-800/60">
                        <span>Max Base Lv:</span>
                        <strong class="text-slate-200"><?php echo e(config('aether.rates.max_base_level')); ?></strong>
                    </li>
                    <li class="flex justify-between py-1 border-b border-slate-800/60">
                        <span>Max Job Lv:</span>
                        <strong class="text-slate-200"><?php echo e(config('aether.rates.max_job_level')); ?></strong>
                    </li>
                    <li class="flex justify-between py-1 border-b border-slate-800/60">
                        <span>Max ASPD:</span>
                        <strong class="text-slate-200"><?php echo e(config('aether.rates.max_aspd')); ?></strong>
                    </li>
                    <li class="flex justify-between py-1 border-b border-slate-800/60">
                        <span>Base / Job:</span>
                        <strong class="text-cyan-400"><?php echo e(config('aether.rates.base')); ?> / <?php echo e(config('aether.rates.job')); ?></strong>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
            <p>© <?php echo e(date('Y')); ?> <?php echo e(config('aether.server_name')); ?>. All rights reserved.</p>
            <p class="flex items-center gap-1.5">
                <span>Powered by</span>
                <span class="text-slate-300 font-semibold">AetherCP</span>
                <span>• Independent RO Control Panel</span>
            </p>
        </div>
    </div>
</footer>

<?php /**PATH C:\xampp\htdocs\aether\resources\views/components/footer.blade.php ENDPATH**/ ?>