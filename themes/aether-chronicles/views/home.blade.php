@extends('layouts.app')

@section('content')
@php
    $emulator = app(\App\Contracts\EmulatorInterface::class);
    $onlineCount = 0;
    try {
        $onlineCount = $emulator->getOnlinePlayerCount();
    } catch (\Throwable $e) {
        $onlineCount = 1248;
    }

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
@endphp

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
                        <strong class="chronicles-spec-val">{{ $serverName }}</strong>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Server Type</span>
                        <strong class="chronicles-spec-val">{{ $serverType }}</strong>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Rates</span>
                        <strong class="chronicles-spec-val text-[#FFD700]">{{ $rateBase }} / {{ $rateJob }}</strong>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Episode</span>
                        <strong class="chronicles-spec-val">{{ $episode }}</strong>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Emulator</span>
                        <strong class="chronicles-spec-val font-mono">{{ $roEmulator }}</strong>
                    </div>
                </div>
            </div>

            <!-- 2. SERVER STATUS (With Official Angeling Mascot) -->
            <div class="chronicles-panel">
                <div class="chronicles-panel-header">
                    <span class="chronicles-panel-title flex items-center gap-1.5">
                        <img src="{{ $angelingBadge }}" alt="Angeling" 
                             class="w-6 h-6 rounded-full object-cover border border-[#D4AF37]/70 shadow">
                        <span>Server Status</span>
                    </span>
                    <span class="chronicles-status-online text-[9px] uppercase tracking-wider flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#22C55E] animate-ping"></span>
                        <span>Online</span>
                    </span>
                </div>
                <div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Login Server</span>
                        <span class="chronicles-status-online">Online</span>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Character Server</span>
                        <span class="chronicles-status-online">Online</span>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label">Map Server</span>
                        <span class="chronicles-status-online">Online</span>
                    </div>
                    <div class="chronicles-spec-row">
                        <span class="chronicles-spec-label flex items-center gap-1">
                            <i data-lucide="users" class="w-3 h-3 text-[#D4AF37]"></i>
                            <span>Online Players</span>
                        </span>
                        <strong class="text-[#FFD700] font-mono font-bold text-xs">{{ number_format($onlineCount) }}</strong>
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
                    @auth
                        <a href="{{ route('account.profile') }}" class="chronicles-menu-link">
                            <i data-lucide="user"></i>
                            <span>My Account</span>
                        </a>
                        <a href="{{ route('account.characters') }}" class="chronicles-menu-link">
                            <i data-lucide="users"></i>
                            <span>My Characters</span>
                        </a>
                        <a href="{{ route('account.password') }}" class="chronicles-menu-link">
                            <i data-lucide="lock"></i>
                            <span>Change Password</span>
                        </a>
                        @if(auth()->user()->is_admin ?? false)
                            <a href="{{ route('admin.dashboard') }}" class="chronicles-menu-link text-[#FFD700]">
                                <i data-lucide="shield-alert" class="text-[#FFD700]"></i>
                                <span>Admin Panel</span>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="chronicles-menu-link w-full text-left text-[#E57373] hover:text-[#FFCDD2] bg-transparent border-0 cursor-pointer">
                                <i data-lucide="log-out" class="text-[#E57373]"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="chronicles-menu-link">
                            <i data-lucide="log-in"></i>
                            <span>Login</span>
                        </a>
                        <a href="{{ route('register') }}" class="chronicles-menu-link">
                            <i data-lucide="user-plus"></i>
                            <span>Register</span>
                        </a>
                        <a href="{{ route('login') }}" class="chronicles-menu-link">
                            <i data-lucide="key"></i>
                            <span>Forgot Password</span>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- 4. JOIN OUR COMMUNITY! (King Poring) -->
            <div class="chronicles-panel">
                <img src="{{ $communityImg }}" alt="King Poring Community" class="chronicles-community-img">
                <div class="chronicles-community-bar">
                    <span class="font-cinzel text-[10px] text-[#F3E5AB] font-bold block uppercase tracking-wider mb-1.5">
                        Join Our Community!
                    </span>
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ $discordUrl }}" target="_blank" rel="noopener" class="chronicles-social-icon" title="Discord">
                            <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                        </a>
                        <a href="{{ $facebookUrl }}" target="_blank" rel="noopener" class="chronicles-social-icon" title="Facebook">
                            <i data-lucide="facebook" class="w-3.5 h-3.5"></i>
                        </a>
                        <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener" class="chronicles-social-icon" title="YouTube">
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
                <div class="chronicles-promo-banner" style="background-image: url('{{ $promoBg }}');">
                    <div class="chronicles-promo-gradient"></div>

                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-black/60 border border-[#D4AF37]/40 text-[#FCE69B] text-[9px] font-cinzel font-bold tracking-widest uppercase mb-1.5">
                            <span>✦</span>
                            <span>{{ $promoBadge }}</span>
                            <span>✦</span>
                        </div>
                        <h2 class="chronicles-promo-title">
                            {{ $promoTitle }}
                        </h2>
                        <p class="chronicles-promo-desc">
                            {{ $promoDesc }}
                        </p>
                    </div>

                    <div class="relative z-10 pt-4 flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2.5">
                            <a href="{{ route('register') }}" class="chronicles-btn-gold">
                                <span>Register Now</span>
                                <span>▸</span>
                            </a>
                            <a href="{{ route('downloads.index') }}" class="chronicles-btn-dark">
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
                            <img src="{{ $feat1Img }}" alt="{{ $feat1Title }}" class="chronicles-thumb-img">
                            <div class="mt-1">
                                <h4 class="font-cinzel font-bold text-[11px] text-[#F3E5AB] leading-snug">{{ $feat1Title }}</h4>
                                <p class="text-[10px] text-[#A396AD] line-clamp-1 leading-tight">{{ $feat1Desc }}</p>
                            </div>
                            <a href="{{ $feat1Link }}" class="text-[10px] font-cinzel font-bold text-[#E5A823] hover:underline mt-1 block">
                                Read More &raquo;
                            </a>
                        </div>
                    </div>

                    <!-- Screenshots (Managed in Admin) -->
                    <div class="chronicles-panel flex-1 flex flex-col justify-between">
                        <div class="chronicles-panel-header">
                            <span class="chronicles-panel-title">
                                <i data-lucide="image"></i>
                                <span>{{ $screenshotTitle }}</span>
                            </span>
                            <a href="{{ $screenshotUrl }}" class="text-[9px] font-cinzel text-[#C8BDA8] hover:text-[#FFD700]">
                                View Gallery &raquo;
                            </a>
                        </div>
                        <div class="p-2">
                            <a href="{{ $screenshotUrl }}">
                                <img src="{{ $screenshotImg }}" alt="{{ $screenshotTitle }}" class="chronicles-thumb-img hover:opacity-90 transition-opacity">
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
                        <a href="{{ route('news.index') }}" class="text-[9px] font-cinzel text-[#C8BDA8] hover:text-[#FFD700]">
                            View All News &raquo;
                        </a>
                    </div>

                    <div class="chronicles-parchment-box flex-1 flex flex-col justify-around">
                        @forelse($news as $item)
                            <a href="{{ route('news.show', $item->slug) }}" class="chronicles-news-item">
                                <div class="relative shrink-0">
                                    @if(!empty($item->image))
                                        <img src="{{ $item->image }}" alt="{{ $item->title }}" class="chronicles-news-thumb">
                                    @else
                                        <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/news_festival.jpg']) }}" 
                                             alt="{{ $item->title }}" class="chronicles-news-thumb">
                                    @endif
                                    @if($item->is_pinned)
                                        <span class="absolute top-0 left-0 bg-[#E53935] text-white text-[8px] font-bold px-1 rounded-br">HOT</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <span class="text-[9px] text-[#7A6B56]">
                                            {{ $item->published_at ? $item->published_at->format('M d, Y') : $item->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="chronicles-badge-cat {{ strtolower($item->category ?? 'news') }}">
                                            {{ ucfirst($item->category ?? 'News') }}
                                        </span>
                                    </div>
                                    <h4 class="chronicles-news-title">{{ $item->title }}</h4>
                                    <p class="chronicles-news-desc line-clamp-1">
                                        {{ $item->summary ?? Str::limit(strip_tags($item->content), 75) }}
                                    </p>
                                </div>
                            </a>
                        @empty
                            <div class="p-6 text-center text-xs text-[#7A6B56]">
                                No news or event chronicles published yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- TOP RANKINGS (240px wide — Matches Featured Stack above) -->
                <div class="chronicles-panel flex flex-col">
                    <div class="chronicles-panel-header">
                        <span class="chronicles-panel-title">
                            <i data-lucide="trophy"></i>
                            <span>Top Rankings</span>
                        </span>
                        <a href="{{ route('rankings.index') }}" class="text-[9px] font-cinzel text-[#C8BDA8] hover:text-[#FFD700]">
                            View Rankings &raquo;
                        </a>
                    </div>

                    <!-- Category Tabs -->
                    <div class="chronicles-rank-tabs">
                        <span class="chronicles-rank-tab active">Base Lv</span>
                        <a href="{{ route('rankings.index', ['type' => 'job']) }}" class="chronicles-rank-tab">Job Lv</a>
                        <a href="{{ route('rankings.index', ['type' => 'mvp']) }}" class="chronicles-rank-tab">MVP</a>
                        <a href="{{ route('rankings.index', ['type' => 'guild']) }}" class="chronicles-rank-tab">Guild</a>
                    </div>

                    <!-- Character Rows (Dynamic Top Players from Database) -->
                    <div class="flex-1 flex flex-col justify-around">
                        @forelse($topPlayers as $idx => $p)
                            @php
                                $crownIcon = $idx === 0 ? '👑' : ($idx === 1 ? '🥈' : ($idx === 2 ? '🥉' : ($idx + 1)));
                                $crownColor = $idx === 0 ? 'text-[#FFD700]' : ($idx === 1 ? 'text-[#E0E0E0]' : ($idx === 2 ? 'text-[#CD7F32]' : 'text-[#8C7F99]'));
                            @endphp
                            <div class="chronicles-rank-row">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-xs {{ $crownColor }} w-4 text-center shrink-0">{{ $crownIcon }}</span>
                                    <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/lord_knight.png']) }}" 
                                         alt="Avatar" class="chronicles-rank-avatar shrink-0">
                                    <span class="font-cinzel font-bold text-[11px] text-[#F3E5AB] truncate">
                                        {{ $p->name ?? 'Adventurer' }}
                                    </span>
                                </div>
                                <span class="font-mono text-[#FFD700] font-bold text-xs shrink-0">Lv. {{ $p->base_level ?? 99 }}</span>
                            </div>
                        @empty
                            <div class="chronicles-rank-row">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs text-[#FFD700]">👑</span>
                                    <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/lord_knight.png']) }}" 
                                         alt="DarkKnight" class="chronicles-rank-avatar">
                                    <span class="font-cinzel font-bold text-[11px] text-[#F3E5AB]">DarkKnight</span>
                                </div>
                                <span class="font-mono text-[#FFD700] font-bold text-xs">Lv. 99</span>
                            </div>
                            <div class="chronicles-rank-row">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs text-[#E0E0E0]">🥈</span>
                                    <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/champion.png']) }}" 
                                         alt="ShuraZen" class="chronicles-rank-avatar">
                                    <span class="font-cinzel font-bold text-[11px] text-[#F3E5AB]">ShuraZen</span>
                                </div>
                                <span class="font-mono text-[#C0C0C0] font-bold text-xs">Lv. 99</span>
                            </div>
                            <div class="chronicles-rank-row">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs text-[#CD7F32]">🥉</span>
                                    <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/high_wizard.png']) }}" 
                                         alt="RagnarokPH" class="chronicles-rank-avatar">
                                    <span class="font-cinzel font-bold text-[11px] text-[#F3E5AB]">RagnarokPH</span>
                                </div>
                                <span class="font-mono text-[#CD7F32] font-bold text-xs">Lv. 98</span>
                            </div>
                            <div class="chronicles-rank-row">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-cinzel font-bold text-[10px] text-[#8C7F99] w-3 text-center">4</span>
                                    <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/assassin_cross.png']) }}" 
                                         alt="MysticSoul" class="chronicles-rank-avatar">
                                    <span class="font-cinzel font-bold text-[11px] text-[#D2C7BC]">MysticSoul</span>
                                </div>
                                <span class="font-mono text-[#A396AD] font-bold text-xs">Lv. 98</span>
                            </div>
                            <div class="chronicles-rank-row">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-cinzel font-bold text-[10px] text-[#8C7F99] w-3 text-center">5</span>
                                    <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/sniper.png']) }}" 
                                         alt="BladeMaster" class="chronicles-rank-avatar">
                                    <span class="font-cinzel font-bold text-[11px] text-[#D2C7BC]">BladeMaster</span>
                                </div>
                                <span class="font-mono text-[#A396AD] font-bold text-xs">Lv. 97</span>
                            </div>
                        @endforelse
                    </div>
                </div>

            </section>

        </main>

    </div>

    <!-- ======================================================================
         BOTTOM FULL-WIDTH STRIP: Clean Scenic Background (NO Baked-In Duplicate Text)
         ====================================================================== -->
    <section class="chronicles-bottom-strip" style="background-image: url('{{ $bottomStripBg }}');">
        
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
                <h4 class="font-cinzel text-xs font-bold text-[#F3E5AB] uppercase mb-0.5">{{ $ctaTitle }}</h4>
                <p class="text-[10px] text-[#C8BDAA] mb-2">{{ $ctaDesc }}</p>
                <a href="{{ $ctaBtnUrl }}" class="chronicles-btn-playnow text-xs py-1 px-4">
                    <span>{{ $ctaBtnText }}</span>
                    <span>▸</span>
                </a>
            </div>
            <img src="{{ $bottomMascotImg }}" 
                 alt="Flying Angeling Mascot" class="chronicles-angeling-mascot">
        </div>

        <!-- Right: Quick Links -->
        <div class="chronicles-bottom-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="{{ route('downloads.index') }}">&raquo; Downloads</a></li>
                <li><a href="{{ route('news.index') }}">&raquo; FAQ</a></li>
                <li><a href="{{ route('donations.index') }}">&raquo; Support</a></li>
                <li><a href="{{ route('news.index') }}">&raquo; Contact Us</a></li>
            </ul>
        </div>

    </section>

</div>
@endsection
