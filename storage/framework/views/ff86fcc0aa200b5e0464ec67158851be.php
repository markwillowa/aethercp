<nav x-data="{ mobileOpen: false, userDropdown: false }" class="relative z-20 border-b border-slate-800 bg-[#0b0f19]/90 backdrop-blur-md sticky top-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 p-0.5 shadow-lg shadow-cyan-500/20 group-hover:shadow-cyan-500/40 transition-all shrink-0">
                        <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                            <i data-lucide="shield" class="w-5 h-5 text-cyan-400 group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                    <div class="shrink-0">
                        <span class="text-xl font-bold tracking-tight aether-gradient-text block"><?php echo e(config('aether.name', 'AetherCP')); ?></span>
                        <span class="block text-[10px] text-slate-400 uppercase tracking-widest"><?php echo e(config('aether.server_name', 'Ragnarok')); ?></span>
                    </div>
                </a>
            </div>

            <!-- Desktop Menu Links -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="<?php echo e(route('home')); ?>" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('home') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'); ?>">
                    Home
                </a>
                <a href="<?php echo e(route('news.index')); ?>" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('news.*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'); ?>">
                    News
                </a>
                <a href="<?php echo e(route('guilds.index')); ?>" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('guilds.*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'); ?>">
                    Guilds
                </a>
                <a href="<?php echo e(route('server.status')); ?>" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('server.status') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'); ?>">
                    Status
                </a>
                <a href="<?php echo e(route('characters.online')); ?>" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('characters.online') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'); ?>">
                    Online
                </a>
                <a href="<?php echo e(route('woe.index')); ?>" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('woe.*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'); ?>">
                    WoE
                </a>
                <a href="<?php echo e(route('itemdb.index')); ?>" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('itemdb.*') || request()->routeIs('database.*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'); ?>">
                    Database
                </a>
                <?php if(app(\App\Services\Module\ModuleManager::class)->isEnabled('marketplace')): ?>
                    <a href="<?php echo e(route('marketplace.index')); ?>" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('marketplace.*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'); ?>">
                        Marketplace
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('donations.index')); ?>" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('donations.*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'); ?>">
                    Donations
                </a>
            </div>

            <!-- Right Side Auth / User Controls -->
            <div class="hidden md:flex items-center space-x-3">
                <?php if(auth()->guard()->check()): ?>
                    <div class="relative" @click.away="userDropdown = false">
                        <button @click="userDropdown = !userDropdown" class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition-colors">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-cyan-600 to-blue-500 flex items-center justify-center text-xs font-bold text-white shadow-sm">
                                <?php echo e(strtoupper(substr(auth()->user()->username ?? auth()->user()->name, 0, 1))); ?>

                            </div>
                            <span class="text-sm font-medium text-slate-200"><?php echo e(auth()->user()->username ?? auth()->user()->name); ?></span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                        </button>

                        <div x-show="userDropdown" x-transition.opacity.duration.150ms class="absolute right-0 mt-2 w-52 bg-slate-900/95 border border-slate-800 rounded-xl shadow-2xl py-1.5 backdrop-blur-xl z-50">
                            <div class="px-4 py-2 border-b border-slate-800/80">
                                <p class="text-xs text-slate-400">Signed in as</p>
                                <p class="text-sm font-semibold text-cyan-400 truncate"><?php echo e(auth()->user()->username); ?></p>
                                <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded bg-slate-800 text-slate-300">
                                    <?php echo e(auth()->user()->role); ?>

                                </span>
                            </div>

                            <a href="<?php echo e(route('account.profile')); ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-800/80 transition-colors">
                                <i data-lucide="user" class="w-4 h-4 text-cyan-400"></i>
                                <span>My Account</span>
                            </a>
                            <a href="<?php echo e(route('account.characters')); ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-800/80 transition-colors">
                                <i data-lucide="users" class="w-4 h-4 text-cyan-400"></i>
                                <span>My Characters</span>
                            </a>

                            <?php if(app(\App\Services\Module\ModuleManager::class)->isEnabled('support')): ?>
                                <a href="<?php echo e(route('support.index')); ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-800/80 transition-colors">
                                    <i data-lucide="life-buoy" class="w-4 h-4 text-cyan-400"></i>
                                    <span>Support Center</span>
                                </a>
                            <?php endif; ?>

                            <?php if(auth()->user()->isAdmin()): ?>
                                <div class="border-t border-slate-800 my-1"></div>
                                <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-amber-300 hover:text-amber-200 hover:bg-amber-950/30 transition-colors">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-amber-400"></i>
                                    <span>Admin Panel</span>
                                </a>
                            <?php endif; ?>

                            <div class="border-t border-slate-800 my-1"></div>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-950/20 transition-colors text-left">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Log Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">
                        Log In
                    </a>
                    <a href="<?php echo e(route('register')); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 shadow-md shadow-cyan-500/20 hover:shadow-cyan-500/40 transition-all flex items-center gap-1.5">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Register</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex md:hidden">
                <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                    <i data-lucide="menu" class="w-6 h-6" x-show="!mobileOpen"></i>
                    <i data-lucide="x" class="w-6 h-6" x-show="mobileOpen"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer -->
    <div x-show="mobileOpen" x-transition class="md:hidden border-b border-slate-800 bg-slate-950/95 px-4 pt-2 pb-4 space-y-1">
        <a href="<?php echo e(route('home')); ?>" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">Home</a>
        <a href="<?php echo e(route('news.index')); ?>" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">News</a>
        <a href="<?php echo e(route('guilds.index')); ?>" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">Guilds</a>
        <a href="<?php echo e(route('characters.online')); ?>" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">Online</a>
        <a href="<?php echo e(route('woe.index')); ?>" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">WoE</a>
        <a href="<?php echo e(route('itemdb.index')); ?>" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">Database</a>
        <?php if(app(\App\Services\Module\ModuleManager::class)->isEnabled('marketplace')): ?>
            <a href="<?php echo e(route('marketplace.index')); ?>" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">Marketplace</a>
        <?php endif; ?>
        <a href="<?php echo e(route('donations.index')); ?>" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">Donations</a>
        <?php if(app(\App\Services\Module\ModuleManager::class)->isEnabled('faq')): ?>
            <a href="<?php echo e(route('faq.index')); ?>" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">FAQ</a>
        <?php endif; ?>
        <?php if(app(\App\Services\Module\ModuleManager::class)->isEnabled('support')): ?>
            <a href="<?php echo e(route('support.index')); ?>" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-200 hover:bg-slate-800">Support</a>
        <?php endif; ?>

        <div class="border-t border-slate-800 pt-3 mt-2">
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('account.profile')); ?>" class="block px-3 py-2 rounded-lg text-base font-medium text-cyan-400 hover:bg-slate-800">My Account (<?php echo e(auth()->user()->username); ?>)</a>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-base font-medium text-red-400 hover:bg-slate-800">Log Out</button>
                </form>
            <?php else: ?>
                <div class="flex gap-2 pt-1">
                    <a href="<?php echo e(route('login')); ?>" class="flex-1 text-center py-2 rounded-lg bg-slate-800 text-slate-200 text-sm font-semibold">Log In</a>
                    <a href="<?php echo e(route('register')); ?>" class="flex-1 text-center py-2 rounded-lg bg-cyan-500 text-slate-950 text-sm font-semibold">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php /**PATH C:\xampp\htdocs\aether\resources\views/components/navbar.blade.php ENDPATH**/ ?>