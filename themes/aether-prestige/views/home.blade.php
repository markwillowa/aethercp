@extends('layouts.app')

@section('content')
@php
    $pModMgr = app(\App\Services\Module\ModuleManager::class);
@endphp
<div>
    <!-- ======================================================================
         SECTION A: CINEMATIC HERO (Pixel-Accurate to Reference Image)
         ====================================================================== -->
    <section class="prestige-hero-screen">
        <!-- Background Artwork -->
        <img src="{{ route('theme.asset', ['theme' => 'aether-prestige', 'path' => 'images/hero-bg.jpg']) }}" 
             alt="{{ \App\Models\SiteSetting::get('server_name', config('aether.server_name', 'MYTHERA')) }}" 
             class="prestige-hero-bg">
        
        <!-- Dark Moody Atmospheric Vignette Overlay -->
        <div class="prestige-hero-overlay"></div>

        <!-- Atmospheric Floating Golden & Cyan Light Particles -->
        <div class="prestige-hero-particles" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- Hero Main Body (Left: Title & CTAs, Right: 3 Trailers) -->
        <div class="prestige-hero-body">
            <!-- Left Column: Title, Subtitle, Description & Buttons -->
            <div class="prestige-hero-left">
                <div>
                    <h1 class="prestige-hero-title">
                        {{ \App\Models\SiteSetting::get('server_name', config('aether.server_name', 'MYTHERA')) }}
                    </h1>
                    <h2 class="prestige-hero-subtitle">
                        {{ \App\Models\SiteSetting::get('home_hero_subtitle', 'The Lost Realms') }}
                    </h2>
                </div>

                <p class="prestige-hero-desc">
                    {{ \App\Models\SiteSetting::get('home_hero_desc', 'An epic open-world fantasy RPG where ancient powers clash, kingdoms fall, and heroes rise.') }}
                </p>

                <div class="prestige-hero-btn-row">
                    <a href="{{ route('register') }}" class="prestige-btn-cyan">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>{{ \App\Models\SiteSetting::get('home_hero_cta_text', 'Register Free') }}</span>
                    </a>

                    <button type="button" onclick="openPrestigeTrailer('{{ \App\Models\SiteSetting::get('home_hero_trailer_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ') }}', '{{ addslashes(\App\Models\SiteSetting::get('home_hero_trailer_title', 'Cinematic Reveal Trailer')) }}')" class="prestige-btn-outline">
                        <i data-lucide="play" class="w-4 h-4 text-white fill-current"></i>
                        <span>{{ \App\Models\SiteSetting::get('home_hero_trailer_title', 'Watch Trailer') }}</span>
                    </button>
                </div>
            </div>

            <!-- Right Column: Vertically Stacked Trailers (01, 02, 03) -->
            <div class="prestige-hero-right">
                <span class="prestige-trailers-header">{{ \App\Models\SiteSetting::get('home_trailers_header', 'Trailers') }}</span>

                <!-- Trailer 01 -->
                <div onclick="openPrestigeTrailer('{{ \App\Models\SiteSetting::get('home_trailer_1_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ') }}', '{{ addslashes(\App\Models\SiteSetting::get('home_trailer_1_title', 'Chapter I: The Awakening')) }}')" class="prestige-trailer-card group">
                    <div class="prestige-trailer-thumb">
                        <img src="{{ route('theme.asset', ['theme' => 'aether-prestige', 'path' => 'images/trailer-1.jpg']) }}" alt="Trailer 01">
                        <div class="prestige-play-icon">
                            <div class="prestige-play-icon-badge">
                                <i data-lucide="play" class="w-3 h-3 text-white fill-current ml-0.5"></i>
                            </div>
                        </div>
                    </div>
                    <span class="prestige-trailer-num">01</span>
                </div>

                <!-- Trailer 02 -->
                <div onclick="openPrestigeTrailer('{{ \App\Models\SiteSetting::get('home_trailer_2_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ') }}', '{{ addslashes(\App\Models\SiteSetting::get('home_trailer_2_title', 'Chapter II: Shadows & Blades')) }}')" class="prestige-trailer-card group">
                    <div class="prestige-trailer-thumb">
                        <img src="{{ route('theme.asset', ['theme' => 'aether-prestige', 'path' => 'images/trailer-2.jpg']) }}" alt="Trailer 02">
                        <div class="prestige-play-icon">
                            <div class="prestige-play-icon-badge">
                                <i data-lucide="play" class="w-3 h-3 text-white fill-current ml-0.5"></i>
                            </div>
                        </div>
                    </div>
                    <span class="prestige-trailer-num">02</span>
                </div>

                <!-- Trailer 03 -->
                <div onclick="openPrestigeTrailer('{{ \App\Models\SiteSetting::get('home_trailer_3_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ') }}', '{{ addslashes(\App\Models\SiteSetting::get('home_trailer_3_title', 'Chapter III: War of Emperium')) }}')" class="prestige-trailer-card group">
                    <div class="prestige-trailer-thumb">
                        <img src="{{ route('theme.asset', ['theme' => 'aether-prestige', 'path' => 'images/trailer-3.jpg']) }}" alt="Trailer 03">
                        <div class="prestige-play-icon">
                            <div class="prestige-play-icon-badge">
                                <i data-lucide="play" class="w-3 h-3 text-white fill-current ml-0.5"></i>
                            </div>
                        </div>
                    </div>
                    <span class="prestige-trailer-num">03</span>
                </div>
            </div>
        </div>

        <!-- Hero Bottom Row: Server Rates (Left) & Social Icons (Right) -->
        <div class="prestige-hero-bottom">
            <div class="prestige-rates-strip">
                <span class="flex items-center gap-1.5 text-white">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                    <span>Rates:</span>
                    <strong class="text-[#D8BE8A]">{{ \App\Models\SiteSetting::get('base_rate', config('aether.rates.base', '1000x')) }} / {{ \App\Models\SiteSetting::get('job_rate', config('aether.rates.job', '1000x')) }} / {{ \App\Models\SiteSetting::get('drop_rate', config('aether.rates.drop', '100x')) }}</strong>
                </span>
                <span class="text-white/20">•</span>
                <span>{{ \App\Models\SiteSetting::get('server_mode', config('emulator.renewal') ? 'Renewal' : 'Pre-Renewal') }}</span>
                <span class="text-white/20">•</span>
                <span class="uppercase text-[#2DD4BF] font-mono">{{ \App\Models\SiteSetting::get('server_emulator', config('emulator.driver', 'rAthena')) }}</span>
            </div>

            <!-- Social Media Icons (Matching Reference: FB, Discord, Twitter/X, YT) -->
            <div class="prestige-social-icons">
                @php
                    $fbUrl = \App\Models\SiteSetting::get('facebook_url', config('aether.community.facebook'));
                    $dcUrl = \App\Models\SiteSetting::get('discord_url', config('aether.community.discord'));
                    $twUrl = \App\Models\SiteSetting::get('twitter_url');
                    $ytUrl = \App\Models\SiteSetting::get('youtube_url', config('aether.community.youtube'));
                @endphp

                @if($fbUrl)
                    <a href="{{ $fbUrl }}" target="_blank" rel="noopener" title="Facebook">
                        <i data-lucide="facebook" class="w-4 h-4"></i>
                    </a>
                @else
                    <span><i data-lucide="facebook" class="w-4 h-4"></i></span>
                @endif

                @if($dcUrl)
                    <a href="{{ $dcUrl }}" target="_blank" rel="noopener" title="Discord">
                        <i data-lucide="message-square" class="w-4 h-4"></i>
                    </a>
                @endif

                @if($twUrl)
                    <a href="{{ $twUrl }}" target="_blank" rel="noopener" title="Twitter / X">
                        <i data-lucide="twitter" class="w-4 h-4"></i>
                    </a>
                @else
                    <span><i data-lucide="twitter" class="w-4 h-4"></i></span>
                @endif

                @if($ytUrl)
                    <a href="{{ $ytUrl }}" target="_blank" rel="noopener" title="YouTube">
                        <i data-lucide="youtube" class="w-4 h-4"></i>
                    </a>
                @else
                    <span><i data-lucide="youtube" class="w-4 h-4"></i></span>
                @endif
            </div>
        </div>
    </section>

    <!-- ======================================================================
         CONTENT SECTIONS (Below Hero)
         ====================================================================== -->
    <div class="prestige-container prestige-sections-wrap">
        @if($pModMgr->isEnabled('server_info'))
        <!-- SECTION 1: LIVE SERVER STATUS TELEMETRY -->
        @php
            $emulator = app(\App\Contracts\EmulatorInterface::class);
            $loginStatus = $emulator->checkServerStatus('login');
            $charStatus = $emulator->checkServerStatus('char');
            $mapStatus = $emulator->checkServerStatus('map');
            $onlinePlayers = $emulator->getOnlinePlayerCount();
        @endphp
        <section class="prestige-card-elevated p-6 sm:p-8 shadow-2xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-white/[0.08] pb-6 mb-6">
                <div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="activity" class="w-5 h-5 text-[#C6A76A]"></i>
                        <h3 class="font-cinzel text-xl font-bold uppercase tracking-wider text-white">
                            {{ \App\Models\SiteSetting::get('home_telemetry_title', 'Live Realm Telemetry') }}
                        </h3>
                    </div>
                    <p class="text-xs text-[#788394] mt-1">
                        {{ \App\Models\SiteSetting::get('home_telemetry_desc', 'Real-time status for server daemons, world instances, and active adventurers.') }}
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="block text-[10px] uppercase tracking-wider text-[#788394] font-semibold">Active Players</span>
                        <span class="text-2xl font-black text-white font-mono">{{ number_format($onlinePlayers) }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-[#101725] border border-white/10 flex items-center justify-center text-[#2DD4BF]">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <!-- Daemons Grid -->
            <div class="prestige-grid-4">
                <div class="bg-[#101725]/80 border border-white/[0.06] rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-[#AAB3C1] block">Login Server</span>
                        <span class="text-xs font-bold {{ $loginStatus['online'] ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $loginStatus['online'] ? 'OPERATIONAL' : 'OFFLINE' }}
                        </span>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full {{ $loginStatus['online'] ? 'bg-emerald-400 shadow-[0_0_8px_#34d399]' : 'bg-red-400 shadow-[0_0_8px_#f87171]' }}"></span>
                </div>

                <div class="bg-[#101725]/80 border border-white/[0.06] rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-[#AAB3C1] block">Char Server</span>
                        <span class="text-xs font-bold {{ $charStatus['online'] ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $charStatus['online'] ? 'OPERATIONAL' : 'OFFLINE' }}
                        </span>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full {{ $charStatus['online'] ? 'bg-emerald-400 shadow-[0_0_8px_#34d399]' : 'bg-red-400 shadow-[0_0_8px_#f87171]' }}"></span>
                </div>

                <div class="bg-[#101725]/80 border border-white/[0.06] rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-[#AAB3C1] block">Map Server</span>
                        <span class="text-xs font-bold {{ $mapStatus['online'] ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $mapStatus['online'] ? 'OPERATIONAL' : 'OFFLINE' }}
                        </span>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full {{ $mapStatus['online'] ? 'bg-emerald-400 shadow-[0_0_8px_#34d399]' : 'bg-red-400 shadow-[0_0_8px_#f87171]' }}"></span>
                </div>

                <div class="bg-[#101725]/80 border border-white/[0.06] rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-[#AAB3C1] block">Gepard Shield</span>
                        <span class="text-xs font-bold text-[#C6A76A]">
                            {{ \App\Models\SiteSetting::get('home_gepard_status', 'ACTIVE (v3.0)') }}
                        </span>
                    </div>
                    <i data-lucide="shield-check" class="w-5 h-5 text-[#C6A76A]"></i>
                </div>
            </div>
        </section>
        @endif

        <!-- SECTION 2: SERVER FEATURES -->
        <section id="features">
            <div class="text-center max-w-2xl mx-auto mb-12 space-y-3">
                <span class="text-xs uppercase tracking-luxury text-[#C6A76A] font-bold font-cinzel">
                    {{ \App\Models\SiteSetting::get('home_features_tag', 'Uncompromising Craftsmanship') }}
                </span>
                <h2 class="font-cinzel text-3xl sm:text-4xl font-extrabold text-white tracking-wider">
                    {{ \App\Models\SiteSetting::get('home_features_title', 'Engineered For Champions') }}
                </h2>
                <p class="text-xs sm:text-sm text-[#AAB3C1]">
                    {{ \App\Models\SiteSetting::get('home_features_desc', 'Discover the hallmark features that establish our realm as the pinnacle of Ragnarok Online private servers.') }}
                </p>
            </div>

            <div class="prestige-grid-3">
                <!-- Feature 1 -->
                <div class="prestige-card p-6 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-[#101725] border border-[#C6A76A]/30 flex items-center justify-center text-[#C6A76A]">
                        <i data-lucide="{{ \App\Models\SiteSetting::get('home_feature_1_icon', 'swords') }}" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-cinzel text-lg font-bold text-white">
                        {{ \App\Models\SiteSetting::get('home_feature_1_title', 'Balanced Competitive PvP') }}
                    </h3>
                    <p class="text-xs text-[#AAB3C1] leading-relaxed">
                        {{ \App\Models\SiteSetting::get('home_feature_1_desc', 'Meticulously tested skill formulas, item restrictions, and custom battlegrounds designed to ensure true mechanical skill triumphs over pay-to-win mechanics.') }}
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="prestige-card p-6 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-[#101725] border border-[#2DD4BF]/30 flex items-center justify-center text-[#2DD4BF]">
                        <i data-lucide="{{ \App\Models\SiteSetting::get('home_feature_2_icon', 'castle') }}" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-cinzel text-lg font-bold text-white">
                        {{ \App\Models\SiteSetting::get('home_feature_2_title', 'Prestigious War of Emperium') }}
                    </h3>
                    <p class="text-xs text-[#AAB3C1] leading-relaxed">
                        {{ \App\Models\SiteSetting::get('home_feature_2_desc', 'Weekly castle sieges with automated guild economy payouts, custom castle treasure chests, and live guild rankings honoring sovereign alliances.') }}
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="prestige-card p-6 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-[#101725] border border-[#D9DEE7]/30 flex items-center justify-center text-[#D9DEE7]">
                        <i data-lucide="{{ \App\Models\SiteSetting::get('home_feature_3_icon', 'crown') }}" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-cinzel text-lg font-bold text-white">
                        {{ \App\Models\SiteSetting::get('home_feature_3_title', 'Custom End-Game Dungeons') }}
                    </h3>
                    <p class="text-xs text-[#AAB3C1] leading-relaxed">
                        {{ \App\Models\SiteSetting::get('home_feature_3_desc', 'Face terrifying world bosses, mythic tier instance challenges, and exclusive costume craftables that test even the most organized parties.') }}
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="prestige-card p-6 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-[#101725] border border-[#C6A76A]/30 flex items-center justify-center text-[#C6A76A]">
                        <i data-lucide="{{ \App\Models\SiteSetting::get('home_feature_4_icon', 'database') }}" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-cinzel text-lg font-bold text-white">
                        {{ \App\Models\SiteSetting::get('home_feature_4_title', 'Integrated Item Database') }}
                    </h3>
                    <p class="text-xs text-[#AAB3C1] leading-relaxed">
                        {{ \App\Models\SiteSetting::get('home_feature_4_desc', 'Search item stats, drop rates, card effects, and equipment bonuses in real-time straight from our blazing-fast control panel.') }}
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="prestige-card p-6 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-[#101725] border border-[#2DD4BF]/30 flex items-center justify-center text-[#2DD4BF]">
                        <i data-lucide="{{ \App\Models\SiteSetting::get('home_feature_5_icon', 'shield') }}" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-cinzel text-lg font-bold text-white">
                        {{ \App\Models\SiteSetting::get('home_feature_5_title', 'Flawless Anti-Cheat Defense') }}
                    </h3>
                    <p class="text-xs text-[#AAB3C1] leading-relaxed">
                        {{ \App\Models\SiteSetting::get('home_feature_5_desc', 'State-of-the-art packet verification, macro blockers, and dedicated game master surveillance to guarantee a level playing field for all.') }}
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="prestige-card p-6 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-[#101725] border border-[#D9DEE7]/30 flex items-center justify-center text-[#D9DEE7]">
                        <i data-lucide="{{ \App\Models\SiteSetting::get('home_feature_6_icon', 'heart-handshake') }}" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-cinzel text-lg font-bold text-white">
                        {{ \App\Models\SiteSetting::get('home_feature_6_title', 'Thriving Global Community') }}
                    </h3>
                    <p class="text-xs text-[#AAB3C1] leading-relaxed">
                        {{ \App\Models\SiteSetting::get('home_feature_6_desc', 'Active Discord community, scheduled community mini-games, and responsive 24/7 staff support ready to assist your journey at any time.') }}
                    </p>
                </div>
            </div>
        </section>

        @if($pModMgr->isEnabled('news') || $pModMgr->isEnabled('rankings'))
        <!-- SECTION 3: SPLIT GRID (News Left, Hall of Fame Right) -->
        <section class="prestige-grid-split" style="{{ !$pModMgr->isEnabled('rankings') ? 'grid-template-columns: 1fr;' : (!$pModMgr->isEnabled('news') ? 'grid-template-columns: 1fr;' : '') }}">
            @if($pModMgr->isEnabled('news'))
            <!-- News (Left) -->
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-white/[0.08] pb-4">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="newspaper" class="w-5 h-5 text-[#C6A76A]"></i>
                        <h2 class="font-cinzel text-xl font-bold uppercase tracking-wider text-white">
                            {{ \App\Models\SiteSetting::get('home_news_title', 'Latest Realm Chronicles') }}
                        </h2>
                    </div>
                    <a href="{{ route('news.index') }}" class="text-xs font-bold uppercase tracking-wider text-[#C6A76A] hover:text-[#D8BE8A] flex items-center gap-1">
                        <span>View All</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($news as $article)
                        <article class="prestige-card p-5 hover:border-[#C6A76A]/40 transition-all flex flex-col sm:flex-row gap-5 items-start">
                            <div class="w-full sm:w-24 sm:h-20 rounded-lg bg-[#101725] border border-white/[0.08] flex sm:flex-col items-center justify-center p-2 text-center shrink-0">
                                <span class="font-cinzel text-lg font-extrabold text-[#C6A76A]">{{ $article->published_at?->format('d') ?? $article->created_at->format('d') }}</span>
                                <span class="text-[10px] uppercase tracking-wider text-[#788394]">{{ $article->published_at?->format('M Y') ?? $article->created_at->format('M Y') }}</span>
                            </div>

                            <div class="flex-grow space-y-1.5">
                                <div class="flex items-center gap-2">
                                    @if($article->is_pinned)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/40">
                                            Pinned
                                        </span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider bg-[#101725] text-[#AAB3C1] border border-white/[0.06]">
                                        {{ $article->category }}
                                    </span>
                                </div>

                                <h3 class="font-cinzel text-base font-bold text-white hover:text-[#C6A76A] transition-colors">
                                    <a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a>
                                </h3>

                                <p class="text-xs text-[#788394] line-clamp-2">
                                    {{ Str::limit(strip_tags($article->content), 120) }}
                                </p>
                            </div>
                        </article>
                    @empty
                        <div class="prestige-card p-8 text-center text-xs text-[#788394]">
                            No published chronicles currently available.
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

            @if($pModMgr->isEnabled('rankings'))
            <!-- Hall of Fame (Right) -->
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-white/[0.08] pb-4">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="trophy" class="w-5 h-5 text-[#C6A76A]"></i>
                        <h2 class="font-cinzel text-xl font-bold uppercase tracking-wider text-white">
                            {{ \App\Models\SiteSetting::get('home_halloffame_title', 'Hall of Fame') }}
                        </h2>
                    </div>
                    <a href="{{ route('rankings.index') }}" class="text-xs font-bold uppercase tracking-wider text-[#C6A76A] hover:text-[#D8BE8A] flex items-center gap-1">
                        <span>Rankings</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="prestige-card overflow-hidden">
                    <div class="p-4 border-b border-white/[0.06] bg-[#101725]/60 flex items-center justify-between text-xs">
                        <span class="font-cinzel font-bold text-white uppercase tracking-wider">Top Adventurers (Base Lv)</span>
                        <span class="text-[10px] text-[#788394]">Top 5</span>
                    </div>

                    <div class="divide-y divide-white/[0.04]">
                        @forelse($topPlayers as $idx => $player)
                            @php
                                $pName = data_get($player, 'name', 'Unknown');
                                $pClass = (int) data_get($player, 'class', 0);
                            @endphp
                            <div class="p-3.5 flex items-center justify-between hover:bg-[#1B2432]/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs {{ $idx === 0 ? 'rank-badge-1' : ($idx === 1 ? 'rank-badge-2' : ($idx === 2 ? 'rank-badge-3' : 'bg-[#101725] text-[#AAB3C1] border border-white/10')) }}">
                                        {{ $idx + 1 }}
                                    </div>

                                    <div>
                                        <a href="{{ route('characters.show', $pName) }}" class="font-cinzel text-xs font-bold text-white hover:text-[#C6A76A] transition-colors block">
                                            {{ $pName }}
                                        </a>
                                        <span class="text-[10px] text-[#788394]">
                                            {{ \App\Services\Emulator\JobHelper::getName($pClass) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <span class="text-xs font-mono font-bold text-[#C6A76A]">Lv {{ data_get($player, 'base_level', 1) }}</span>
                                    <span class="block text-[10px] text-[#788394]">Job {{ data_get($player, 'job_level', 1) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-[#788394]">
                                Character rankings data synchronizing.
                            </div>
                        @endforelse
                    </div>

                    <div class="p-3 bg-[#101725]/60 text-center border-t border-white/[0.06]">
                        <a href="{{ route('rankings.index') }}" class="text-xs text-[#C6A76A] hover:underline font-semibold font-cinzel">
                            Explore Full Hall of Fame (PvP, MVP, Zeny) →
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </section>
        @endif

        <!-- SECTION 4: CALL TO ACTION BANNER -->
        <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#101725] via-[#1B2432] to-[#101725] border border-[#C6A76A]/30 p-8 sm:p-12 shadow-2xl">
            <div class="relative z-10 max-w-2xl space-y-4">
                <span class="text-xs font-bold uppercase tracking-luxury text-[#C6A76A] font-cinzel">
                    {{ \App\Models\SiteSetting::get('home_cta_badge', 'Join The Sovereign Order') }}
                </span>
                <h2 class="font-cinzel text-3xl sm:text-4xl font-extrabold text-white leading-tight">
                    {{ \App\Models\SiteSetting::get('home_cta_title', 'Your Legacy Awaits In The Lost Realms') }}
                </h2>
                <p class="text-xs sm:text-sm text-[#AAB3C1] leading-relaxed">
                    {{ \App\Models\SiteSetting::get('home_cta_desc', 'Download the complete high-resolution game client, forge your guild, and step into the most refined Ragnarok Online private server experience today.') }}
                </p>

                <div class="pt-2 flex flex-wrap items-center gap-4">
                    <a href="{{ route('register') }}" class="prestige-btn-primary">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>{{ \App\Models\SiteSetting::get('home_cta_btn1_text', 'Create Account') }}</span>
                    </a>
                    @if($pModMgr->isEnabled('downloads'))
                    <a href="{{ route('downloads.index') }}" class="prestige-btn-outline">
                        <i data-lucide="download" class="w-4 h-4 text-[#C6A76A]"></i>
                        <span>{{ \App\Models\SiteSetting::get('home_cta_btn2_text', 'Download Client') }}</span>
                    </a>
                    @endif
                    @if($dcUrl)
                        <a href="{{ $dcUrl }}" target="_blank" rel="noopener" class="prestige-btn-outline">
                            <i data-lucide="message-square" class="w-4 h-4 text-[#2DD4BF]"></i>
                            <span>Join Discord</span>
                        </a>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
