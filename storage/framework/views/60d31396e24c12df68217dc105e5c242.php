<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark" data-theme="<?php echo e($currentTheme['slug'] ?? 'aether-dark'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e($title ?? config('aether.name', 'AetherCP')); ?> - <?php echo e(config('aether.server_name', 'Ragnarok Online')); ?></title>
    <meta name="description" content="<?php echo e(config('aether.description')); ?>">

    <!-- Google Fonts / Instrument Sans via Vite -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <!-- Dynamic Theme Styling & CSS Injection -->
    <style>
        :root {
            --color-theme-primary: <?php echo e($currentTheme['colors']['primary'] ?? '#06b6d4'); ?>;
            --color-theme-accent: <?php echo e($currentTheme['colors']['accent'] ?? '#3b82f6'); ?>;
            --color-theme-bg: <?php echo e($currentTheme['colors']['background'] ?? '#070a13'); ?>;
            --color-theme-surface: <?php echo e($currentTheme['colors']['surface'] ?? '#0f172a'); ?>;
        }
        html, body {
            background-color: var(--color-theme-bg) !important;
            color: #e2e8f0 !important;
        }
    </style>

    <?php if(!empty($currentTheme['has_css']) && !empty($currentTheme['slug'])): ?>
        <?php
            $cssAssetPath = base_path('themes/' . $currentTheme['slug'] . '/assets/theme.css');
            $cssVer = file_exists($cssAssetPath) ? filemtime($cssAssetPath) : '1.0';
        ?>
        <link rel="stylesheet" href="<?php echo e(route('theme.asset', ['theme' => $currentTheme['slug'], 'path' => 'theme.css'])); ?>?v=<?php echo e($cssVer); ?>">
    <?php endif; ?>
</head>
<body class="text-slate-200 min-h-screen flex flex-col font-sans antialiased selection:bg-amber-400 selection:text-black">
    <!-- Ethereal Background Glow Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-cyan-600/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Top Server Status Bar -->
    <header class="relative z-20 border-b border-slate-800/80 bg-slate-950/70 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-wrap items-center justify-between text-xs text-slate-400 gap-3">
            <!-- Left: Rates & Server Info -->
            <div class="flex items-center space-x-4">
                <span class="flex items-center gap-1.5 text-slate-300">
                    <i data-lucide="zap" class="w-3.5 h-3.5 text-cyan-400"></i>
                    Rates: <strong class="text-cyan-300"><?php echo e(config('aether.rates.base')); ?> / <?php echo e(config('aether.rates.job')); ?> / <?php echo e(config('aether.rates.drop')); ?></strong>
                </span>
                <span class="hidden md:inline-block text-slate-700">|</span>
                <span class="hidden md:flex items-center gap-1 text-slate-300">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                    Episode: <span class="text-slate-200">19 Renewal</span>
                </span>
            </div>

            <!-- Right: Live Server Status Pill -->
            <div class="flex items-center space-x-3">
                <?php
                    $onlineCount = app(\App\Contracts\EmulatorInterface::class)->getOnlinePlayerCount();
                    $loginStatus = app(\App\Contracts\EmulatorInterface::class)->checkServerStatus('login');
                ?>
                <div class="flex items-center gap-2 bg-slate-900/90 border border-slate-800 px-3 py-1 rounded-full">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full <?php echo e($loginStatus['online'] ? 'bg-emerald-400 opacity-75' : 'bg-red-400 opacity-75'); ?>"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 <?php echo e($loginStatus['online'] ? 'bg-emerald-500' : 'bg-red-500'); ?>"></span>
                    </span>
                    <span class="text-slate-300 font-medium">Server:</span>
                    <span class="<?php echo e($loginStatus['online'] ? 'text-emerald-400 font-semibold' : 'text-red-400'); ?>">
                        <?php echo e($loginStatus['online'] ? 'ONLINE' : 'OFFLINE'); ?>

                    </span>
                    <span class="text-slate-600">|</span>
                    <span class="text-slate-300"><strong class="text-cyan-400"><?php echo e($onlineCount); ?></strong> Online</span>
                </div>

                <?php if(config('aether.community.discord')): ?>
                    <a href="<?php echo e(config('aether.community.discord')); ?>" target="_blank" rel="noopener" class="flex items-center gap-1 bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-300 border border-indigo-500/30 px-2.5 py-1 rounded-full transition-colors">
                        <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                        <span>Discord</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Navigation Bar -->
    <?php echo $__env->make('components.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Flash Notifications -->
    <?php if(session('success')): ?>
        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 px-4 py-3 rounded-xl flex items-center gap-3 backdrop-blur-md">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                <p class="text-sm font-medium"><?php echo e(session('success')); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-950/80 border border-red-500/40 text-red-200 px-4 py-3 rounded-xl flex items-center gap-3 backdrop-blur-md">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-400 shrink-0"></i>
                <p class="text-sm font-medium"><?php echo e(session('error')); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Content Slot -->
    <main class="relative z-10 flex-grow py-8">
        <?php echo e($slot ?? ''); ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Master Footer -->
    <?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.renderAetherIcons) {
                window.renderAetherIcons();
            }
        });
    </script>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\aether\resources\views/layouts/app.blade.php ENDPATH**/ ?>