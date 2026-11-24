<?php $__env->startSection('content'); ?>
<?php
    $loginOnline = !empty($serverStatus['login']['online']);
    $charOnline = !empty($serverStatus['char']['online']);
    $mapOnline = !empty($serverStatus['map']['online']);
    $clusterOnline = $loginOnline || $charOnline || $mapOnline;
    $onlineCount = $serverStatus['online_count'] ?? 0;

    // Flexible asset resolver for Web URLs, custom root paths, or theme asset defaults
    $resolveAsset = function ($key, $defaultPath) {
        $val = \App\Models\SiteSetting::get($key);
        if (!empty($val)) {
            // Automatically remap legacy white-background JPG mascots to clean transparent PNG
            if (str_contains($val, 'mascot_angeling.jpg') || str_contains($val, 'angeling_status_badge.jpg')) {
                $val = 'images/angeling_transparent.png';
            }
            if (\Illuminate\Support\Str::startsWith($val, ['http://', 'https://', '//', '/'])) {
                return $val;
            }
            return route('theme.asset', ['theme' => 'aether-chronicles', 'path' => $val]);
        }
        return route('theme.asset', ['theme' => 'aether-chronicles', 'path' => $defaultPath]);
    };

    $serverName = \App\Models\SiteSetting::get('server_name', 'Aether Chronicles');
    $serverType = \App\Models\SiteSetting::get('chronicles_server_type', 'Pre-Renewal');
    $rateBase = \App\Models\SiteSetting::get('rate_base', '100x');
    $rateJob = \App\Models\SiteSetting::get('rate_job', '100x');
    $episode = \App\Models\SiteSetting::get('chronicles_episode', 'Classic');
    $roEmulator = \App\Models\SiteSetting::get('chronicles_emulator', 'rAthena');

    $promoTitle = \App\Models\SiteSetting::get('chronicles_promo_title', 'Experience the True Ragnarok!');
    $promoDesc = \App\Models\SiteSetting::get('chronicles_promo_desc', 'Join thousands of players in an epic adventure across Rune-Midgard!');
    $promoBadge = \App\Models\SiteSetting::get('chronicles_badge', 'Episode XIII: Noghaltz');
    $promoBg = $resolveAsset('chronicles_promo_bg', 'images/promo_banner_clean.jpg');

    $feat1Title = \App\Models\SiteSetting::get('chronicles_feat1_title', 'Epic WoE Battles');
    $feat1Desc = \App\Models\SiteSetting::get('chronicles_feat1_desc', 'Fight for your guild glory!');
    $feat1Link = \App\Models\SiteSetting::get('chronicles_feat1_link', route('woe.index'));
    $feat1Img = $resolveAsset('chronicles_feat1_img', 'images/feat_woe_boss.jpg');

    $screenshotTitle = \App\Models\SiteSetting::get('chronicles_screenshot_title', 'Screenshots');
    $screenshotImg = $resolveAsset('chronicles_screenshot_img', 'images/screenshot_prontera.jpg');
    $screenshotUrl = \App\Models\SiteSetting::get('chronicles_screenshot_url');
    if (empty($screenshotUrl) || $screenshotUrl === '/news') {
        $screenshotUrl = route('gallery.index');
    }

    $customGalleryJson = \App\Models\SiteSetting::get('chronicles_gallery_data', null);
    $galleryScreenshots = [];
    if (!empty($customGalleryJson)) {
        $decoded = json_decode($customGalleryJson, true);
        if (is_array($decoded)) {
            $galleryScreenshots = $decoded;
        }
    }
    if (empty($galleryScreenshots)) {
        $galleryScreenshots = [
            [
                'title' => 'Capital City of Prontera',
                'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/screenshot_prontera.jpg']),
            ],
            [
                'title' => 'War of Emperium Castle Siege',
                'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/feat_woe_boss.jpg']),
            ],
            [
                'title' => 'Grand Midgard Festival',
                'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/news_festival.jpg']),
            ],
            [
                'title' => 'Treasure Vaults & Update',
                'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/news_update.jpg']),
            ],
            [
                'title' => 'Episode XIII: Noghaltz & Odin Shrine',
                'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/header_chronicles_hero_clean.jpg']),
            ],
            [
                'title' => 'Road to Midgard Highway',
                'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/bottom_strip_clean.jpg']),
            ],
        ];
    }

    $angelingBadge = $resolveAsset('chronicles_angeling_img', 'images/angeling_transparent.png');
    $communityImg = $resolveAsset('chronicles_community_img', 'images/community_king_poring.jpg');
    $bottomStripBg = $resolveAsset('chronicles_bottom_strip_bg', 'images/bottom_strip_clean.jpg');
    $bottomMascotImg = $resolveAsset('chronicles_bottom_mascot_img', 'images/angeling_transparent.png');

    $ctaTitle = \App\Models\SiteSetting::get('chronicles_cta_title', 'Join the Adventure Today!');
    $ctaDesc = \App\Models\SiteSetting::get('chronicles_cta_desc', 'Be part of our growing Ragnarok community.');
    $ctaBtnText = \App\Models\SiteSetting::get('chronicles_cta_btn_text', 'Play Now');
    $ctaBtnUrl = \App\Models\SiteSetting::get('chronicles_cta_btn_url', route('downloads.index'));

    $discordUrl = \App\Models\SiteSetting::get('discord_url', 'https://discord.gg/ragnarok');
    $facebookUrl = \App\Models\SiteSetting::get('facebook_url', '#');
    $youtubeUrl = \App\Models\SiteSetting::get('youtube_url', '#');
?>

<div class="chronicles-container py-3">

    <!-- ======================================================================
         MASTER HOMEPAGE LAYOUT: Left Sidebar (220px) + Right Main Content
         ====================================================================== -->
    <div class="chronicles-main-layout">

        <!-- ==================================================================
             LEFT COLUMN (Sidebar): Server Info, Status, Account, Community
             ================================================================== -->
        <aside class="chronicles-sidebar">

            <!-- 1. SERVER INFORMATION -->
            <div class="chronicles-panel">
                <div class="chronicles-panel-header">
                    <span class="chronicles-panel-title">
                        <i data-lucide="home"></i>
                        <span>Server Information</span>
                    </span>
                </div>
                <div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Server Name</span>
                        <strong class="chronicles-spec-val"><?php echo e($serverName); ?></strong>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Server Type</span>
                        <strong class="chronicles-spec-val"><?php echo e($serverType); ?></strong>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Rates</span>
                        <strong class="chronicles-spec-val text-[#FFD700]"><?php echo e($rateBase); ?> / <?php echo e($rateJob); ?></strong>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Episode</span>
                        <strong class="chronicles-spec-val"><?php echo e($episode); ?></strong>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Emulator</span>
                        <strong class="chronicles-spec-val font-mono"><?php echo e($roEmulator); ?></strong>
                    </div>
                </div>
            </div>

            <!-- 2. SERVER STATUS (Dynamic Live Server Status) -->
            <div class="chronicles-panel">
                <div class="chronicles-panel-header">
                    <span class="chronicles-panel-title flex items-center gap-1.5">
                        <img src="<?php echo e($angelingBadge); ?>" alt="Angeling" 
                             class="w-6 h-6 rounded-full object-cover border border-[#D4AF37]/70 shadow">
                        <span>Server Status</span>
                    </span>
                    <?php if($clusterOnline): ?>
                        <span class="chronicles-status-online text-[9px] uppercase tracking-wider flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#22C55E] animate-ping"></span>
                            <span>Online</span>
                        </span>
                    <?php else: ?>
                        <span class="text-[#EF4444] text-[9px] uppercase tracking-wider flex items-center gap-1 font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#EF4444]"></span>
                            <span>Offline</span>
                        </span>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Login Server</span>
                        <?php if($loginOnline): ?>
                            <span class="chronicles-status-online">Online</span>
                        <?php else: ?>
                            <span class="text-[#EF4444] text-xs font-bold font-mono">Offline</span>
                        <?php endif; ?>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Character Server</span>
                        <?php if($charOnline): ?>
                            <span class="chronicles-status-online">Online</span>
                        <?php else: ?>
                            <span class="text-[#EF4444] text-xs font-bold font-mono">Offline</span>
                        <?php endif; ?>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Map Server</span>
                        <?php if($mapOnline): ?>
                            <span class="chronicles-status-online">Online</span>
                        <?php else: ?>
                            <span class="text-[#EF4444] text-xs font-bold font-mono">Offline</span>
                        <?php endif; ?>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label flex items-center gap-1">
                            <i data-lucide="users" class="w-3 h-3 text-[#D4AF37]"></i>
                            <span>Online Players</span>
                        </span>
                        <strong class="<?php echo e($onlineCount > 0 ? 'text-[#FFD700]' : 'text-[#8C7F99]'); ?> font-mono font-bold text-xs"><?php echo e(number_format($onlineCount)); ?></strong>
                    </div>
                </div>
            </div>

            <!-- 3. ACCOUNT MENU -->
            <div class="chronicles-panel">
                <div class="chronicles-panel-header">
                    <span class="chronicles-panel-title">
                        <i data-lucide="user"></i>
                        <span>Account Menu</span>
                    </span>
                </div>
                <div>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('account.profile')); ?>" class="chronicles-menu-link">
                            <i data-lucide="user"></i>
                            <span>My Account</span>
                        </a>
                        <a href="<?php echo e(route('account.characters')); ?>" class="chronicles-menu-link">
                            <i data-lucide="users"></i>
                            <span>My Characters</span>
                        </a>
                        <?php if(app(\App\Services\Module\ModuleManager::class)->isEnabled('marketplace')): ?>
                            <a href="<?php echo e(route('marketplace.index')); ?>" class="chronicles-menu-link">
                                <i data-lucide="scale"></i>
                                <span>Trade House</span>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('account.password')); ?>" class="chronicles-menu-link">
                            <i data-lucide="lock"></i>
                            <span>Change Password</span>
                        </a>
                        <?php if(auth()->user()->is_admin ?? false): ?>
                            <a href="<?php echo e(route('admin.dashboard')); ?>" class="chronicles-menu-link text-[#FFD700]">
                                <i data-lucide="shield-alert" class="text-[#FFD700]"></i>
                                <span>Admin Panel</span>
                            </a>
                        <?php endif; ?>
                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="m-0 p-0">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="chronicles-menu-link w-full text-left text-[#E57373] hover:text-[#FFCDD2] bg-transparent border-0 cursor-pointer">
                                <i data-lucide="log-out" class="text-[#E57373]"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="chronicles-menu-link">
                            <i data-lucide="log-in"></i>
                            <span>Login</span>
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="chronicles-menu-link">
                            <i data-lucide="user-plus"></i>
                            <span>Register</span>
                        </a>
                        <a href="<?php echo e(route('login')); ?>" class="chronicles-menu-link">
                            <i data-lucide="key"></i>
                            <span>Forgot Password</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 4. JOIN OUR COMMUNITY! (King Poring) -->
            <div class="chronicles-panel">
                <img src="<?php echo e($communityImg); ?>" alt="King Poring Community" class="chronicles-community-img">
                <div class="chronicles-community-bar">
                    <span class="font-cinzel text-[10px] text-[#F3E5AB] font-bold block uppercase tracking-wider mb-1.5">
                        Join Our Community!
                    </span>
                    <div class="flex items-center justify-center gap-2">
                        <a href="<?php echo e($discordUrl); ?>" target="_blank" rel="noopener" class="chronicles-social-icon" title="Discord">
                            <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                        </a>
                        <a href="<?php echo e($facebookUrl); ?>" target="_blank" rel="noopener" class="chronicles-social-icon" title="Facebook">
                            <i data-lucide="facebook" class="w-3.5 h-3.5"></i>
                        </a>
                        <a href="<?php echo e($youtubeUrl); ?>" target="_blank" rel="noopener" class="chronicles-social-icon" title="YouTube">
                            <i data-lucide="youtube" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            </div>

        </aside>

        <!-- ==================================================================
             RIGHT COLUMN: Promo Banner, Featured, News Parchment, Rankings
             ================================================================== -->
        <main class="chronicles-right-main">

            <!-- ROW 1: Large Clean Promo Banner + Featured/Screenshots (Widths 1fr & 240px) -->
            <section class="chronicles-top-row">

                <!-- MAIN PROMOTIONAL BANNER (Clean background with Rogue girl on right, HTML text on left) -->
                <div class="chronicles-promo-banner" style="background-image: url('<?php echo e($promoBg); ?>');">
                    <div class="chronicles-promo-gradient"></div>

                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-black/60 border border-[#D4AF37]/40 text-[#FCE69B] text-[9px] font-cinzel font-bold tracking-widest uppercase mb-1.5">
                            <span>✦</span>
                            <span><?php echo e($promoBadge); ?></span>
                            <span>✦</span>
                        </div>
                        <h2 class="chronicles-promo-title">
                            <?php echo e($promoTitle); ?>

                        </h2>
                        <p class="chronicles-promo-desc">
                            <?php echo e($promoDesc); ?>

                        </p>
                    </div>

                    <div class="relative z-10 pt-4 flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2.5">
                            <a href="<?php echo e(route('register')); ?>" class="chronicles-btn-gold">
                                <span>Register Now</span>
                                <span>▸</span>
                            </a>
                            <a href="<?php echo e(route('downloads.index')); ?>" class="chronicles-btn-dark">
                                <i data-lucide="download" class="w-3 h-3"></i>
                                <span>Download Client</span>
                            </a>
                        </div>

                        <!-- Dots Indicator -->
                        <div class="flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-[#E5A823] border border-white"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-white/40"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-white/40"></span>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDEBAR STACK: Featured & Screenshots (240px wide) -->
                <div class="chronicles-sidebar-stack">

                    <!-- Featured (WoE) -->
                    <div class="chronicles-panel flex-1 flex flex-col justify-between">
                        <div class="chronicles-panel-header">
                            <span class="chronicles-panel-title">
                                <i data-lucide="star"></i>
                                <span>Featured</span>
                            </span>
                        </div>
                        <div class="p-2 flex-1 flex flex-col justify-between">
                            <img src="<?php echo e($feat1Img); ?>" alt="<?php echo e($feat1Title); ?>" class="chronicles-thumb-img">
                            <div class="mt-1">
                                <h4 class="font-cinzel font-bold text-[11px] text-[#F3E5AB] leading-snug"><?php echo e($feat1Title); ?></h4>
                                <p class="text-[10px] text-[#A396AD] line-clamp-1 leading-tight"><?php echo e($feat1Desc); ?></p>
                            </div>
                            <a href="<?php echo e($feat1Link); ?>" class="text-[10px] font-cinzel font-bold text-[#E5A823] hover:underline mt-1 block">
                                Read More &raquo;
                            </a>
                        </div>
                    </div>

                    <!-- Screenshots (Managed in Admin, Rotates randomly every 15s, links to /gallery) -->
                    <div class="chronicles-panel flex-1 flex flex-col justify-between"
                         x-data="{
                             gallery: <?php echo e(json_encode($galleryScreenshots)); ?>,
                             activeIdx: 0,
                             init() {
                                 if (this.gallery.length > 1) {
                                     setInterval(() => {
                                         let next;
                                         do {
                                             next = Math.floor(Math.random() * this.gallery.length);
                                         } while (next === this.activeIdx && this.gallery.length > 1);
                                         this.activeIdx = next;
                                     }, 15000);
                                 }
                             }
                         }">
                        <div class="chronicles-panel-header">
                            <span class="chronicles-panel-title">
                                <i data-lucide="image"></i>
                                <span x-text="gallery[activeIdx]?.title || '<?php echo e($screenshotTitle); ?>'"><?php echo e($screenshotTitle); ?></span>
                            </span>
                            <a href="<?php echo e(route('gallery.index')); ?>" class="text-[9px] font-cinzel text-[#C8BDA8] hover:text-[#FFD700]">
                                View Gallery &raquo;
                            </a>
                        </div>
                        <div class="p-2 flex-1 flex flex-col justify-center">
                            <a href="<?php echo e(route('gallery.index')); ?>" class="block relative overflow-hidden rounded group" title="Click to view full Gallery">
                                <img :src="gallery[activeIdx]?.image" 
                                     :alt="gallery[activeIdx]?.title" 
                                     class="chronicles-thumb-img hover:opacity-90 transition-all duration-700 w-full h-24 object-cover">
                                <div class="absolute bottom-0 inset-x-0 bg-black/75 px-2 py-0.5 text-[8.5px] font-cinzel text-[#F3E5AB] truncate flex justify-between items-center border-t border-[#D4AF37]/30">
                                    <span x-text="gallery[activeIdx]?.title" class="truncate"></span>
                                    <span class="text-[#D4AF37] font-bold shrink-0 ml-1">&raquo;</span>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>

            </section>

            <!-- ROW 2: Latest News & Announcements + Top Rankings (Widths 1fr & 240px — Exact Alignment) -->
            <section class="chronicles-mid-row">

                <!-- LATEST NEWS & ANNOUNCEMENTS (Aged Parchment Style with Actual Database Records) -->
                <div class="chronicles-panel flex flex-col">
                    <div class="chronicles-panel-header">
                        <span class="chronicles-panel-title">
                            <i data-lucide="megaphone"></i>
                            <span>Latest News &amp; Announcements</span>
                        </span>
                        <a href="<?php echo e(route('news.index')); ?>" class="text-[9px] font-cinzel text-[#C8BDA8] hover:text-[#FFD700]">
                            View All News &raquo;
                        </a>
                    </div>

                    <div class="chronicles-parchment-box flex-1 flex flex-col justify-around">
                        <?php $__empty_1 = true; $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e(route('news.show', $item->slug)); ?>" class="chronicles-news-item">
                                <div class="relative shrink-0">
                                    <?php if(!empty($item->image)): ?>
                                        <img src="<?php echo e($item->image); ?>" alt="<?php echo e($item->title); ?>" class="chronicles-news-thumb">
                                    <?php else: ?>
                                        <img src="<?php echo e(route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/news_festival.jpg'])); ?>" 
                                             alt="<?php echo e($item->title); ?>" class="chronicles-news-thumb">
                                    <?php endif; ?>
                                    <?php if($item->is_pinned): ?>
                                        <span class="absolute top-0 left-0 bg-[#E53935] text-white text-[8px] font-bold px-1 rounded-br">HOT</span>
                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <span class="text-[9px] text-[#7A6B56]">
                                            <?php echo e($item->published_at ? $item->published_at->format('M d, Y') : $item->created_at->format('M d, Y')); ?>

                                        </span>
                                        <span class="chronicles-badge-cat <?php echo e(strtolower($item->category ?? 'news')); ?>">
                                            <?php echo e(ucfirst($item->category ?? 'News')); ?>

                                        </span>
                                    </div>
                                    <h4 class="chronicles-news-title"><?php echo e($item->title); ?></h4>
                                    <p class="chronicles-news-desc line-clamp-1">
                                        <?php echo e($item->summary ?? Str::limit(strip_tags($item->content), 75)); ?>

                                    </p>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-6 text-center text-xs text-[#7A6B56]">
                                No news or event chronicles published yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TOP RANKINGS (Client-Side Switchable Tabs, Current Live Database Data Only) -->
                <div class="chronicles-panel flex flex-col" x-data="{ activeRankTab: 'base' }">
                    <div class="chronicles-panel-header">
                        <span class="chronicles-panel-title">
                            <i data-lucide="trophy"></i>
                            <span>Top Rankings</span>
                        </span>
                        <a href="<?php echo e(route('rankings.index')); ?>" class="text-[9px] font-cinzel text-[#C8BDA8] hover:text-[#FFD700]">
                            View All &raquo;
                        </a>
                    </div>

                    <!-- Category Tabs (Switches client-side on homepage) -->
                    <div class="chronicles-rank-tabs">
                        <button type="button" @click="activeRankTab = 'base'" :class="activeRankTab === 'base' ? 'active' : ''" class="chronicles-rank-tab">Base Lv</button>
                        <button type="button" @click="activeRankTab = 'job'" :class="activeRankTab === 'job' ? 'active' : ''" class="chronicles-rank-tab">Job Lv</button>
                        <button type="button" @click="activeRankTab = 'mvp'" :class="activeRankTab === 'mvp' ? 'active' : ''" class="chronicles-rank-tab">MVP</button>
                        <button type="button" @click="activeRankTab = 'guild'" :class="activeRankTab === 'guild' ? 'active' : ''" class="chronicles-rank-tab">Guild</button>
                    </div>

                    <!-- TAB 1: Base Level -->
                    <div x-show="activeRankTab === 'base'" class="flex-1 flex flex-col justify-around">
                        <?php $__empty_1 = true; $__currentLoopData = $topBase ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $crownIcon = $idx === 0 ? '👑' : ($idx === 1 ? '🥈' : ($idx === 2 ? '🥉' : ($idx + 1)));
                                $crownColor = $idx === 0 ? 'text-[#FFD700]' : ($idx === 1 ? 'text-[#E0E0E0]' : ($idx === 2 ? 'text-[#CD7F32]' : 'text-[#8C7F99]'));
                            ?>
                            <div class="chronicles-rank-row">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-xs <?php echo e($crownColor); ?> w-4 text-center shrink-0"><?php echo e($crownIcon); ?></span>
                                    <span class="font-cinzel font-bold text-[11px] text-[#F3E5AB] truncate">
                                        <?php echo e($p->name); ?>

                                    </span>
                                </div>
                                <span class="font-mono text-[#FFD700] font-bold text-xs shrink-0">Lv. <?php echo e($p->base_level); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-6 text-center text-xs text-[#8C7F99] font-cinzel">
                                No characters ranked yet.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 2: Job Level -->
                    <div x-show="activeRankTab === 'job'" class="flex-1 flex flex-col justify-around" style="display: none;">
                        <?php $__empty_1 = true; $__currentLoopData = $topJob ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $crownIcon = $idx === 0 ? '👑' : ($idx === 1 ? '🥈' : ($idx === 2 ? '🥉' : ($idx + 1)));
                                $crownColor = $idx === 0 ? 'text-[#FFD700]' : ($idx === 1 ? 'text-[#E0E0E0]' : ($idx === 2 ? 'text-[#CD7F32]' : 'text-[#8C7F99]'));
                            ?>
                            <div class="chronicles-rank-row">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-xs <?php echo e($crownColor); ?> w-4 text-center shrink-0"><?php echo e($crownIcon); ?></span>
                                    <span class="font-cinzel font-bold text-[11px] text-[#F3E5AB] truncate">
                                        <?php echo e($p->name); ?>

                                    </span>
                                </div>
                                <span class="font-mono text-[#CD7F32] font-bold text-xs shrink-0">Job <?php echo e($p->job_level); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-6 text-center text-xs text-[#8C7F99] font-cinzel">
                                No characters ranked yet.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 3: MVP -->
                    <div x-show="activeRankTab === 'mvp'" class="flex-1 flex flex-col justify-around" style="display: none;">
                        <?php $__empty_1 = true; $__currentLoopData = $topMvp ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $crownIcon = $idx === 0 ? '👑' : ($idx === 1 ? '🥈' : ($idx === 2 ? '🥉' : ($idx + 1)));
                                $crownColor = $idx === 0 ? 'text-[#FFD700]' : ($idx === 1 ? 'text-[#E0E0E0]' : ($idx === 2 ? 'text-[#CD7F32]' : 'text-[#8C7F99]'));
                            ?>
                            <div class="chronicles-rank-row">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-xs <?php echo e($crownColor); ?> w-4 text-center shrink-0"><?php echo e($crownIcon); ?></span>
                                    <span class="font-cinzel font-bold text-[11px] text-[#F3E5AB] truncate">
                                        <?php echo e($p->name); ?>

                                    </span>
                                </div>
                                <span class="font-mono text-[#E53935] font-bold text-xs shrink-0"><?php echo e($p->fame ?? 0); ?> Fame</span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-6 text-center text-xs text-[#8C7F99] font-cinzel">
                                No MVP champions recorded yet.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 4: Guild -->
                    <div x-show="activeRankTab === 'guild'" class="flex-1 flex flex-col justify-around" style="display: none;">
                        <?php $__empty_1 = true; $__currentLoopData = $topGuilds ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $crownIcon = $idx === 0 ? '👑' : ($idx === 1 ? '🥈' : ($idx === 2 ? '🥉' : ($idx + 1)));
                                $crownColor = $idx === 0 ? 'text-[#FFD700]' : ($idx === 1 ? 'text-[#E0E0E0]' : ($idx === 2 ? 'text-[#CD7F32]' : 'text-[#8C7F99]'));
                            ?>
                            <div class="chronicles-rank-row">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-xs <?php echo e($crownColor); ?> w-4 text-center shrink-0"><?php echo e($crownIcon); ?></span>
                                    <span class="font-cinzel font-bold text-[11px] text-[#F3E5AB] truncate">
                                        <?php echo e($g->name); ?>

                                    </span>
                                </div>
                                <span class="font-mono text-[#D4AF37] font-bold text-xs shrink-0">G.Lv <?php echo e($g->guild_lv); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-6 text-center text-xs text-[#8C7F99] font-cinzel">
                                No guilds formed yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </section>

        </main>

    </div>

    <!-- ======================================================================
         BOTTOM FULL-WIDTH STRIP: Clean Scenic Background (NO Baked-In Duplicate Text)
         ====================================================================== -->
    <section class="chronicles-bottom-strip" style="background-image: url('<?php echo e($bottomStripBg); ?>');">
        
        <!-- Left: Server Features -->
        <div class="chronicles-bottom-col">
            <h4>Server Features</h4>
            <ul>
                <li><span class="text-[#22C55E] mr-1">✔</span> Balanced Gameplay</li>
                <li><span class="text-[#22C55E] mr-1">✔</span> Active Community</li>
                <li><span class="text-[#22C55E] mr-1">✔</span> Regular Events</li>
            </ul>
        </div>

        <!-- Center: CTA & Angeling Mascot -->
        <div class="chronicles-bottom-center">
            <div>
                <h4 class="font-cinzel text-xs font-bold text-[#F3E5AB] uppercase mb-0.5"><?php echo e($ctaTitle); ?></h4>
                <p class="text-[10px] text-[#C8BDAA] mb-2"><?php echo e($ctaDesc); ?></p>
                <a href="<?php echo e($ctaBtnUrl); ?>" class="chronicles-btn-playnow text-xs py-1 px-4">
                    <span><?php echo e($ctaBtnText); ?></span>
                    <span>▸</span>
                </a>
            </div>
            <img src="<?php echo e($bottomMascotImg); ?>" 
                 alt="Flying Angeling Mascot" class="chronicles-angeling-mascot">
        </div>

        <!-- Right: Quick Links -->
        <div class="chronicles-bottom-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo e(route('downloads.index')); ?>">&raquo; Downloads</a></li>
                <li><a href="<?php echo e(route('news.index')); ?>">&raquo; FAQ</a></li>
                <li><a href="<?php echo e(route('donations.index')); ?>">&raquo; Support</a></li>
                <li><a href="<?php echo e(route('news.index')); ?>">&raquo; Contact Us</a></li>
            </ul>
        </div>

    </section>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\themes\aether-chronicles\views/home.blade.php ENDPATH**/ ?>