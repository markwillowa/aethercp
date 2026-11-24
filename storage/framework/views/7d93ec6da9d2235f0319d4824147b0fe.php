<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark" data-theme="aether-chronicles">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e($title ?? 'Aether Chronicles'); ?> - <?php echo e(\App\Models\SiteSetting::get('server_name', 'Aether Chronicles')); ?></title>
    <meta name="description" content="<?php echo e(\App\Models\SiteSetting::get('chronicles_tagline', 'Your Legend Begins in Rune-Midgard.')); ?>">

    <!-- Google Fonts & Tailwind Styles via Vite -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <!-- Aether Chronicles Classic MMORPG Theme Stylesheet -->
    <?php
        $cssAssetPath = base_path('themes/aether-chronicles/assets/theme.css');
        $cssVer = file_exists($cssAssetPath) ? filemtime($cssAssetPath) : '5.0';
        $headerBgVal = \App\Models\SiteSetting::get('chronicles_header_bg');
        if (!empty($headerBgVal)) {
            if (\Illuminate\Support\Str::startsWith($headerBgVal, ['http://', 'https://', '//', '/'])) {
                $headerBg = $headerBgVal;
            } else {
                $headerBg = route('theme.asset', ['theme' => 'aether-chronicles', 'path' => $headerBgVal]);
            }
        } else {
            $headerBg = route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/header_chronicles_hero_clean.jpg']);
        }
    ?>
    <link rel="stylesheet" href="<?php echo e(route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'theme.css'])); ?>?v=<?php echo e($cssVer); ?>">

    <!-- Lucide Icons Global Fallback Script -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="chronicles-theme flex flex-col justify-between font-sans antialiased selection:bg-[#D4AF37] selection:text-[#1A1522]">

    <div>
        <!-- ======================================================================
             1. MAIN WEBSITE HEADER BANNER (Castle, Knight, Priestess & Crest)
             ====================================================================== -->
        <header class="chronicles-header-banner" style="background-image: url('<?php echo e($headerBg); ?>');">
            <div class="chronicles-header-overlay"></div>
            
            <div class="chronicles-container w-full relative z-10 py-6 flex items-center justify-between">
                <!-- Left: 3D Embossed Logo & Tagline -->
                <?php
                    $customLogo = \App\Models\SiteSetting::get('chronicles_logo');
                    $serverName = \App\Models\SiteSetting::get('server_name', 'AETHER CHRONICLES');
                    $tagline = \App\Models\SiteSetting::get('chronicles_tagline', 'Your Legend Begins in Rune-Midgard.');
                ?>

                <a href="<?php echo e(route('home')); ?>" class="no-underline inline-block group">
                    <?php if(!empty($customLogo)): ?>
                        <img src="<?php echo e($customLogo); ?>" alt="<?php echo e($serverName); ?>" class="h-16 object-contain">
                    <?php else: ?>
                        <div class="chronicles-logo-badge">
                            <span>✦</span>
                            <span>Classic MMORPG</span>
                            <span>✦</span>
                        </div>
                        <h1 class="chronicles-logo-title group-hover:brightness-110 transition-all">
                            <?php echo e($serverName); ?>

                        </h1>
                        <p class="chronicles-logo-subtitle">
                            <?php echo e($tagline); ?>

                        </p>
                    <?php endif; ?>
                </a>

                <!-- Right: Online Adventurers Pill Badge (Live Server Status) -->
                <?php
                    $emulator = app(\App\Contracts\EmulatorInterface::class);
                    $onlinePlayers = 0;
                    $isClusterOnline = false;
                    try {
                        $onlinePlayers = $emulator->getOnlinePlayerCount();
                        $statusLogin = $emulator->checkServerStatus('login');
                        $isClusterOnline = !empty($statusLogin['online']) || $onlinePlayers > 0;
                    } catch (\Throwable $e) {
                        $onlinePlayers = 0;
                        $isClusterOnline = false;
                    }
                ?>
                <div class="hidden md:flex items-center gap-2.5 chronicles-online-pill self-end mb-2">
                    <?php if($isClusterOnline): ?>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#22C55E] animate-ping"></span>
                    <?php else: ?>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#EF4444]"></span>
                    <?php endif; ?>
                    <span class="text-xs font-cinzel text-[#E8DCC9]">Players In Midgard:</span>
                    <strong class="font-mono <?php echo e($onlinePlayers > 0 ? 'text-[#FFD700]' : 'text-[#8C7F99]'); ?> text-sm"><?php echo e(number_format($onlinePlayers)); ?></strong>
                </div>
            </div>
        </header>

        <!-- ======================================================================
             2. DECORATIVE NAVIGATION BAR (Full Width Medieval Stone)
             ====================================================================== -->
        <div class="chronicles-container">
            <?php echo $__env->make('components.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Flash Notifications -->
        <div class="chronicles-container my-2">
            <?php if(session('success')): ?>
                <div class="p-3 rounded bg-[#1E3A18] border border-[#4CAF50] text-[#D8FFB8] text-xs flex items-center gap-2.5 shadow-lg">
                    <i data-lucide="check-circle" class="w-4 h-4 text-[#4CAF50] shrink-0"></i>
                    <span><?php echo e(session('success')); ?></span>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="p-3 rounded bg-[#3A1818] border border-[#F44336] text-[#FFCDD2] text-xs flex items-center gap-2.5 shadow-lg">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-[#F44336] shrink-0"></i>
                    <span><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Content Area -->
        <main>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <!-- Footer -->
    <?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Lucide Icons Initializer -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\aether\themes\aether-chronicles\views/layouts/app.blade.php ENDPATH**/ ?>