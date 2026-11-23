@extends('layouts.app')

@section('content')
<div class="chronicles-container py-4 space-y-4">

    <!-- ======================================================================
         MAIN 3-COLUMN COMPOSITION (Faithful to Reference Image)
         ====================================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 items-start">

        <!-- ==================================================================
             LEFT COLUMN (lg:col-span-3 - approx 22% width)
             ================================================================== -->
        <div class="lg:col-span-3 space-y-3.5">

            <!-- 1. Server Information Panel -->
            <div class="chronicles-panel">
                <div class="chronicles-panel-header">
                    <span class="chronicles-panel-title">
                        <i data-lucide="home" class="w-3.5 h-3.5"></i>
                        <span>Server Information</span>
                    </span>
                </div>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between border-b border-white/[0.04] pb-1">
                        <span class="text-[#A396AD]">Server Name</span>
                        <strong class="text-[#F3E5AB] font-cinzel">{{ \App\Models\SiteSetting::get('server_name', 'Aether Chronicles') }}</strong>
                    </div>
                    <div class="flex justify-between border-b border-white/[0.04] pb-1">
                        <span class="text-[#A396AD]">Server Type</span>
                        <span class="text-[#E2D9EB] font-medium">Pre-Renewal</span>
                    </div>
                    <div class="flex justify-between border-b border-white/[0.04] pb-1">
                        <span class="text-[#A396AD]">Rates</span>
                        <strong class="text-[#FFD700] font-mono">{{ \App\Models\SiteSetting::get('rate_base', '100x') }} / {{ \App\Models\SiteSetting::get('rate_job', '100x') }}</strong>
                    </div>
                    <div class="flex justify-between border-b border-white/[0.04] pb-1">
                        <span class="text-[#A396AD]">Episode</span>
                        <span class="text-[#E2D9EB] font-medium">Classic</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#A396AD]">Emulator</span>
                        <span class="text-[#E2D9EB] font-mono">rAthena</span>
                    </div>
                </div>
            </div>

            <!-- 2. Server Status Panel -->
            <div class="chronicles-panel">
                <div class="chronicles-panel-header">
                    <span class="chronicles-panel-title">
                        <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                        <span>Server Status</span>
                    </span>
                </div>
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center justify-between border-b border-white/[0.04] pb-1">
                        <span class="flex items-center gap-1.5 text-[#E2D9EB]">
                            <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
                            <span>Login Server</span>
                        </span>
                        <span class="text-[#22C55E] font-bold">Online</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-white/[0.04] pb-1">
                        <span class="flex items-center gap-1.5 text-[#E2D9EB]">
                            <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
                            <span>Character Server</span>
                        </span>
                        <span class="text-[#22C55E] font-bold">Online</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-white/[0.04] pb-1">
                        <span class="flex items-center gap-1.5 text-[#E2D9EB]">
                            <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
                            <span>Map Server</span>
                        </span>
                        <span class="text-[#22C55E] font-bold">Online</span>
                    </div>
                    <div class="flex items-center justify-between pt-0.5">
                        <span class="flex items-center gap-1.5 text-[#A396AD]">
                            <i data-lucide="users" class="w-3 h-3 text-[#E5A823]"></i>
                            <span>Online Players</span>
                        </span>
                        @php
                            $emulator = app(\App\Contracts\EmulatorInterface::class);
                            $onlinePlayers = 0;
                            try { $onlinePlayers = $emulator->getOnlinePlayerCount(); } catch (\Throwable $e) { $onlinePlayers = 1248; }
                        @endphp
                        <strong class="text-[#FFD700] font-mono font-bold">{{ number_format($onlinePlayers) }}</strong>
                    </div>
                </div>
            </div>

            <!-- 3. Account Menu Panel -->
            <div class="chronicles-panel">
                <div class="chronicles-panel-header">
                    <span class="chronicles-panel-title">
                        <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                        <span>Account Menu</span>
                    </span>
                </div>
                <div class="space-y-1 text-xs font-cinzel">
                    @guest
                        <a href="{{ route('login') }}" class="flex items-center gap-2 p-1.5 rounded hover:bg-[#352B42] text-[#E2D9EB] hover:text-[#FFD700] transition-colors">
                            <i data-lucide="key" class="w-3.5 h-3.5 text-[#E5A823]"></i>
                            <span>Login</span>
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center gap-2 p-1.5 rounded hover:bg-[#352B42] text-[#E2D9EB] hover:text-[#FFD700] transition-colors">
                            <i data-lucide="user-plus" class="w-3.5 h-3.5 text-[#E5A823]"></i>
                            <span>Register</span>
                        </a>
                    @else
                        <a href="{{ route('account.profile') }}" class="flex items-center gap-2 p-1.5 rounded hover:bg-[#352B42] text-[#E2D9EB] hover:text-[#FFD700] transition-colors">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-[#E5A823]"></i>
                            <span>My Account ({{ auth()->user()->username }})</span>
                        </a>
                        <a href="{{ route('account.characters') }}" class="flex items-center gap-2 p-1.5 rounded hover:bg-[#352B42] text-[#E2D9EB] hover:text-[#FFD700] transition-colors">
                            <i data-lucide="shield" class="w-3.5 h-3.5 text-[#E5A823]"></i>
                            <span>My Characters</span>
                        </a>
                        <a href="{{ route('account.profile') }}" class="flex items-center gap-2 p-1.5 rounded hover:bg-[#352B42] text-[#E2D9EB] hover:text-[#FFD700] transition-colors">
                            <i data-lucide="lock" class="w-3.5 h-3.5 text-[#E5A823]"></i>
                            <span>Change Password</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="pt-1">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full p-1.5 rounded hover:bg-[#352B42] text-[#E57373] text-left transition-colors">
                                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    @endguest
                </div>
            </div>

            <!-- 4. Join Our Community! Panel (Cute King Poring) -->
            <div class="chronicles-panel text-center p-3">
                <div class="mb-2 rounded overflow-hidden">
                    <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/community_king_poring.jpg']) }}" 
                         alt="Join Community" class="w-full h-24 object-cover rounded border border-[#564C63]">
                </div>
                <div class="font-cinzel font-bold text-xs text-[#F3E5AB] tracking-wide mb-2 uppercase">
                    Join Our Community!
                </div>
                <div class="flex items-center justify-center gap-2.5">
                    <a href="{{ \App\Models\SiteSetting::get('social_discord', '#') }}" target="_blank" rel="noopener" 
                       class="w-7 h-7 rounded bg-[#5865F2] text-white flex items-center justify-center hover:scale-110 transition-transform shadow" title="Discord">
                        <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                    </a>
                    <a href="{{ \App\Models\SiteSetting::get('social_facebook', '#') }}" target="_blank" rel="noopener" 
                       class="w-7 h-7 rounded bg-[#1877F2] text-white flex items-center justify-center hover:scale-110 transition-transform shadow" title="Facebook">
                        <i data-lucide="facebook" class="w-3.5 h-3.5"></i>
                    </a>
                    <a href="{{ \App\Models\SiteSetting::get('social_youtube', '#') }}" target="_blank" rel="noopener" 
                       class="w-7 h-7 rounded bg-[#CD201F] text-white flex items-center justify-center hover:scale-110 transition-transform shadow" title="YouTube">
                        <i data-lucide="youtube" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- ==================================================================
             CENTER COLUMN (lg:col-span-6 - approx 56% width)
             ================================================================== -->
        <div class="lg:col-span-6 space-y-3.5">

            <!-- 1. Main Promotional Hero Box (Rogue Girl Artwork) -->
            <div class="chronicles-promo-box">
                <div class="chronicles-promo-overlay"></div>

                <div class="relative z-10">
                    <h2 class="chronicles-promo-title">
                        {{ \App\Models\SiteSetting::get('home_hero_subtitle', 'Experience the True Ragnarok!') }}
                    </h2>
                    <p class="chronicles-promo-desc">
                        {{ \App\Models\SiteSetting::get('home_hero_desc', 'Join thousands of players in an epic adventure across Rune-Midgard!') }}
                    </p>
                </div>

                <div class="relative z-10 pt-6 flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('register') }}" class="chronicles-btn-gold">
                            <span>Register Now</span>
                            <span>▸</span>
                        </a>
                        <a href="{{ route('downloads.index') }}" class="chronicles-btn-dark">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>Download Client</span>
                        </a>
                    </div>

                    <!-- Carousel Dots -->
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#E5A823] border border-white shadow"></span>
                        <span class="w-2 h-2 rounded-full bg-white/40"></span>
                        <span class="w-2 h-2 rounded-full bg-white/40"></span>
                    </div>
                </div>
            </div>

            <!-- 2. Latest News & Announcements (Aged Parchment Container) -->
            <div class="chronicles-panel p-2">
                <div class="chronicles-panel-header px-1">
                    <span class="chronicles-panel-title">
                        <i data-lucide="megaphone" class="w-3.5 h-3.5"></i>
                        <span>Latest News & Announcements</span>
                    </span>
                    <a href="{{ route('news.index') }}" class="chronicles-panel-link">
                        View All News &raquo;
                    </a>
                </div>

                <div class="chronicles-parchment-container space-y-1">
                    <!-- News Item 1 (Festival Event) -->
                    <a href="{{ route('news.index') }}" class="chronicles-news-row group relative overflow-hidden">
                        <div class="relative shrink-0">
                            <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/news_festival.jpg']) }}" 
                                 alt="Festival" class="chronicles-news-thumb">
                            <span class="absolute top-0 left-0 bg-[#E53935] text-white text-[9px] font-bold px-1 rounded-br">HOT</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-[10px] text-[#7A6B56] flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                    <span>Nov 26, 2026</span>
                                </span>
                                <span class="chronicles-badge-events">Events</span>
                            </div>
                            <h4 class="font-cinzel text-xs font-bold text-[#382C1B] group-hover:text-[#8C6D23] leading-snug">
                                Grand Festival Event is Live!
                            </h4>
                            <p class="text-[11px] text-[#695842] leading-tight line-clamp-1 mt-0.5">
                                Join our biggest event of the year and get exclusive rewards!
                            </p>
                        </div>
                    </a>

                    <!-- News Item 2 (Server Update) -->
                    <a href="{{ route('news.index') }}" class="chronicles-news-row group">
                        <div class="shrink-0">
                            <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/news_chests.jpg']) }}" 
                                 alt="Update" class="chronicles-news-thumb">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-[10px] text-[#7A6B56] flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                    <span>Nov 24, 2026</span>
                                </span>
                                <span class="chronicles-badge-updates">Updates</span>
                            </div>
                            <h4 class="font-cinzel text-xs font-bold text-[#382C1B] group-hover:text-[#8C6D23] leading-snug">
                                Server Update v2.0.0
                            </h4>
                            <p class="text-[11px] text-[#695842] leading-tight line-clamp-1 mt-0.5">
                                New maps, new quests and more exciting content!
                            </p>
                        </div>
                    </a>

                    <!-- News Item 3 (Welcome) -->
                    <a href="{{ route('news.index') }}" class="chronicles-news-row group">
                        <div class="shrink-0">
                            <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/community_king_poring.jpg']) }}" 
                                 alt="Welcome" class="chronicles-news-thumb">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-[10px] text-[#7A6B56] flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                    <span>Nov 20, 2026</span>
                                </span>
                                <span class="chronicles-badge-news">News</span>
                            </div>
                            <h4 class="font-cinzel text-xs font-bold text-[#382C1B] group-hover:text-[#8C6D23] leading-snug">
                                Welcome to Aether Chronicles!
                            </h4>
                            <p class="text-[11px] text-[#695842] leading-tight line-clamp-1 mt-0.5">
                                Thank you for choosing our server!
                            </p>
                        </div>
                    </a>
                </div>
            </div>

        </div>

        <!-- ==================================================================
             RIGHT COLUMN (lg:col-span-3 - approx 22% width)
             ================================================================== -->
        <div class="lg:col-span-3 space-y-3.5">

            <!-- 1. Featured WoE Panel -->
            <div class="chronicles-panel">
                <div class="chronicles-panel-header">
                    <span class="chronicles-panel-title">
                        <i data-lucide="star" class="w-3.5 h-3.5"></i>
                        <span>Featured</span>
                    </span>
                </div>
                <div>
                    <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/feat_woe_boss.jpg']) }}" 
                         alt="WoE Boss" class="w-full h-24 object-cover rounded border border-[#564C63] mb-2">
                    <h4 class="font-cinzel font-bold text-xs text-[#F3E5AB]">Epic WoE Battles</h4>
                    <p class="text-[11px] text-[#A396AD] mt-0.5">Fight for your guild glory!</p>
                    <a href="{{ route('woe.index') }}" class="text-[11px] font-cinzel font-bold text-[#E5A823] hover:underline inline-block mt-1">
                        Read More &raquo;
                    </a>
                </div>
            </div>

            <!-- 2. Screenshots Panel -->
            <div class="chronicles-panel">
                <div class="chronicles-panel-header">
                    <span class="chronicles-panel-title">
                        <i data-lucide="image" class="w-3.5 h-3.5"></i>
                        <span>Screenshots</span>
                    </span>
                    <a href="{{ route('news.index') }}" class="chronicles-panel-link">
                        View Gallery &raquo;
                    </a>
                </div>
                <div>
                    <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/screenshot_prontera.jpg']) }}" 
                         alt="Prontera Screenshot" class="w-full h-24 object-cover rounded border border-[#564C63]">
                </div>
            </div>

            <!-- 3. Top Rankings Panel -->
            <div class="chronicles-panel">
                <div class="chronicles-panel-header">
                    <span class="chronicles-panel-title">
                        <i data-lucide="trophy" class="w-3.5 h-3.5"></i>
                        <span>Top Rankings</span>
                    </span>
                    <a href="{{ route('rankings.index') }}" class="chronicles-panel-link">
                        View Rankings &raquo;
                    </a>
                </div>

                <!-- Tabs: Base Lv, Job Lv, MVP, Guild -->
                <div class="chronicles-rank-tabs">
                    <span class="chronicles-rank-tab active">Base Lv</span>
                    <a href="{{ route('rankings.index', ['type' => 'job']) }}" class="chronicles-rank-tab">Job Lv</a>
                    <a href="{{ route('rankings.index', ['type' => 'mvp']) }}" class="chronicles-rank-tab">MVP</a>
                    <a href="{{ route('rankings.index', ['type' => 'guild']) }}" class="chronicles-rank-tab">Guild</a>
                </div>

                <!-- 5 Rows matching Reference: Crowns & Avatars -->
                <div class="space-y-0.5">
                    <!-- Rank 1: Gold Crown -->
                    <div class="chronicles-rank-row">
                        <div class="flex items-center gap-2">
                            <span class="text-base text-[#FFD700]" title="Rank 1">👑</span>
                            <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/lord_knight.png']) }}" 
                                 alt="Avatar" class="chronicles-rank-avatar">
                            <span class="font-cinzel font-bold text-[#F3E5AB]">DarkKnight</span>
                        </div>
                        <span class="font-mono text-[#FFD700] font-bold">Lv. 99</span>
                    </div>

                    <!-- Rank 2: Silver Crown -->
                    <div class="chronicles-rank-row">
                        <div class="flex items-center gap-2">
                            <span class="text-base text-[#C0C0C0]" title="Rank 2">👑</span>
                            <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/champion.png']) }}" 
                                 alt="Avatar" class="chronicles-rank-avatar">
                            <span class="font-cinzel font-bold text-[#E2D9EB]">ShuraZen</span>
                        </div>
                        <span class="font-mono text-[#C0C0C0] font-bold">Lv. 99</span>
                    </div>

                    <!-- Rank 3: Bronze Crown -->
                    <div class="chronicles-rank-row">
                        <div class="flex items-center gap-2">
                            <span class="text-base text-[#CD7F32]" title="Rank 3">👑</span>
                            <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/high_wizard.png']) }}" 
                                 alt="Avatar" class="chronicles-rank-avatar">
                            <span class="font-cinzel font-bold text-[#E2D9EB]">RagnarokPH</span>
                        </div>
                        <span class="font-mono text-[#CD7F32] font-bold">Lv. 98</span>
                    </div>

                    <!-- Rank 4 -->
                    <div class="chronicles-rank-row">
                        <div class="flex items-center gap-2">
                            <span class="font-cinzel font-bold text-xs text-[#A396AD] w-4 text-center">4</span>
                            <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/sniper.png']) }}" 
                                 alt="Avatar" class="chronicles-rank-avatar">
                            <span class="font-cinzel text-xs text-[#E2D9EB]">MysticSoul</span>
                        </div>
                        <span class="font-mono text-xs text-[#A396AD]">Lv. 98</span>
                    </div>

                    <!-- Rank 5 -->
                    <div class="chronicles-rank-row">
                        <div class="flex items-center gap-2">
                            <span class="font-cinzel font-bold text-xs text-[#A396AD] w-4 text-center">5</span>
                            <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/assassin_cross.png']) }}" 
                                 alt="Avatar" class="chronicles-rank-avatar">
                            <span class="font-cinzel text-xs text-[#E2D9EB]">BladeMaster</span>
                        </div>
                        <span class="font-mono text-xs text-[#A396AD]">Lv. 97</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ======================================================================
         BOTTOM BANNER STRIP (Server Features, CTA, Mascot, Quick Links)
         ====================================================================== -->
    <div class="chronicles-bottom-strip">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
            
            <!-- Left: Server Features (4 cols) -->
            <div class="md:col-span-4 flex items-center gap-4 border-b md:border-b-0 md:border-r border-[#564766]/50 pb-4 md:pb-0 pr-2">
                <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/monsters/baphomet.gif']) }}" 
                     alt="Monster" class="h-16 object-contain shrink-0">
                <div class="space-y-1 text-xs">
                    <div class="font-cinzel font-bold text-[#F3E5AB] uppercase mb-1">Server Features</div>
                    <div class="flex items-center gap-1.5 text-[#E2D9EB]">
                        <span class="text-[#FFD700]">✔</span>
                        <span>Balanced Gameplay</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-[#E2D9EB]">
                        <span class="text-[#FFD700]">✔</span>
                        <span>Active Community</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-[#E2D9EB]">
                        <span class="text-[#FFD700]">✔</span>
                        <span>Regular Events</span>
                    </div>
                </div>
            </div>

            <!-- Center: CTA & Flying Angeling Mascot (5 cols) -->
            <div class="md:col-span-5 flex items-center justify-between gap-4 border-b md:border-b-0 md:border-r border-[#564766]/50 pb-4 md:pb-0 px-2">
                <div class="text-center sm:text-left">
                    <h3 class="font-cinzel font-bold text-sm text-[#FFF3D6] uppercase tracking-wide">
                        Join the Adventure Today!
                    </h3>
                    <p class="text-xs text-[#A396AD] mt-0.5 mb-2.5">
                        Be part of our growing Ragnarok community.
                    </p>
                    <a href="{{ route('downloads.index') }}" class="chronicles-btn-playnow text-xs py-1.5 px-5">
                        <span>Play Now</span>
                        <span>▸</span>
                    </a>
                </div>
                <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/monsters/angeling.gif']) }}" 
                     alt="Angeling Mascot" class="h-14 object-contain shrink-0 drop-shadow">
            </div>

            <!-- Right: Quick Links (3 cols) -->
            <div class="md:col-span-3 text-xs space-y-1 pl-2">
                <div class="font-cinzel font-bold text-[#F3E5AB] uppercase mb-1">Quick Links</div>
                <div class="grid grid-cols-2 gap-1 font-cinzel">
                    <a href="{{ route('downloads.index') }}" class="text-[#E2D9EB] hover:text-[#FFD700] flex items-center gap-1">
                        <span>▸</span> Downloads
                    </a>
                    <a href="{{ route('news.index') }}" class="text-[#E2D9EB] hover:text-[#FFD700] flex items-center gap-1">
                        <span>▸</span> FAQ
                    </a>
                    <a href="{{ route('donations.index') }}" class="text-[#E2D9EB] hover:text-[#FFD700] flex items-center gap-1">
                        <span>▸</span> Support
                    </a>
                    <a href="{{ route('news.index') }}" class="text-[#E2D9EB] hover:text-[#FFD700] flex items-center gap-1">
                        <span>▸</span> Contact Us
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
