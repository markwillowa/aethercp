@extends('layouts.app')

@php
    $rcModMgr = app(\App\Services\Module\ModuleManager::class);
@endphp

@section('content')
<div class="ro-layout-3col">
    <!-- ======================================================================
         LEFT SIDEBAR (Col 1 on desktop)
         - Account Box / Panel
         - Server Status Panel (Login, Char, Map, Players)
         - Server Information & Rates Panel
         ====================================================================== -->
    <aside class="ro-col-left">
        <!-- 1. Account Box -->
        <div class="ro-panel">
            <div class="ro-panel-header">
                <i data-lucide="key" class="w-4 h-4"></i>
                <span>Account Portal</span>
            </div>
            <div class="p-4 space-y-3">
                @auth
                    <div class="ro-inner-box space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded bg-[#315B88] border border-[#6392C2] text-[#F8EBD2] flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(auth()->user()->username ?? auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold text-[#3A2A1C] truncate">{{ auth()->user()->username ?? auth()->user()->name }}</p>
                                <span class="text-[10px] text-[#77644E] uppercase tracking-wider font-semibold">Account #{{ auth()->user()->account_id }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-1 text-xs">
                        <a href="{{ route('account.profile') }}" class="ro-btn-primary w-full text-center">
                            <i data-lucide="user" class="w-3.5 h-3.5"></i>
                            <span>My Account Profile</span>
                        </a>
                        <a href="{{ route('account.characters') }}" class="ro-btn-wood w-full text-center">
                            <i data-lucide="users" class="w-3.5 h-3.5 text-[#FFDA89]"></i>
                            <span>Character Management</span>
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="ro-btn-gold w-full text-center">
                                <i data-lucide="shield-alert" class="w-3.5 h-3.5 text-[#3A2A1C]"></i>
                                <span>Admin Control Panel</span>
                            </a>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-[#DECBB1]">
                        @csrf
                        <button type="submit" class="w-full text-center text-xs text-[#B71C1C] hover:underline flex items-center justify-center gap-1 font-bold">
                            <i data-lucide="log-out" class="w-3 h-3"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('login') }}" class="space-y-2.5">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-[#5C4632] uppercase tracking-wider mb-1">Username</label>
                            <input type="text" name="username" required class="w-full text-xs" placeholder="Login ID">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#5C4632] uppercase tracking-wider mb-1">Password</label>
                            <input type="password" name="password" required class="w-full text-xs" placeholder="••••••••">
                        </div>
                        <div class="flex items-center justify-between text-[11px] pt-1">
                            <label class="flex items-center gap-1.5 cursor-pointer text-[#5C4632]">
                                <input type="checkbox" name="remember" class="rounded border-[#8C6A2D]">
                                <span>Remember</span>
                            </label>
                            <a href="{{ route('register') }}" class="text-[#315B88] font-bold hover:underline">New Player?</a>
                        </div>
                        <div class="pt-1">
                            <button type="submit" class="ro-btn-primary w-full">
                                <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                                <span>Enter Realm</span>
                            </button>
                        </div>
                        <a href="{{ route('register') }}" class="ro-btn-gold w-full text-center">
                            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                            <span>Create Free Account</span>
                        </a>
                    </form>
                @endauth
            </div>
        </div>

        <!-- 2. Server Status Box -->
        <div class="ro-panel">
            <div class="ro-panel-header">
                <i data-lucide="activity" class="w-4 h-4"></i>
                <span>Server Realms</span>
            </div>
            <div class="p-3 space-y-2 text-xs">
                @php
                    $emulator = app(\App\Contracts\EmulatorInterface::class);
                    $loginStatus = $emulator->checkServerStatus('login');
                    $charStatus = $emulator->checkServerStatus('char');
                    $mapStatus = $emulator->checkServerStatus('map');
                    $onlineCount = $emulator->getOnlinePlayerCount();
                @endphp

                <div class="flex items-center justify-between py-1 border-b border-[#DECBB1]">
                    <span class="font-bold text-[#5C4632] flex items-center gap-1.5">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-[#8C6A2D]"></i>
                        <span>Login Server:</span>
                    </span>
                    @if($loginStatus['online'])
                        <span class="ro-status-online">ONLINE</span>
                    @else
                        <span class="ro-status-offline">OFFLINE</span>
                    @endif
                </div>

                <div class="flex items-center justify-between py-1 border-b border-[#DECBB1]">
                    <span class="font-bold text-[#5C4632] flex items-center gap-1.5">
                        <i data-lucide="users" class="w-3.5 h-3.5 text-[#8C6A2D]"></i>
                        <span>Char Server:</span>
                    </span>
                    @if($charStatus['online'])
                        <span class="ro-status-online">ONLINE</span>
                    @else
                        <span class="ro-status-offline">OFFLINE</span>
                    @endif
                </div>

                <div class="flex items-center justify-between py-1 border-b border-[#DECBB1]">
                    <span class="font-bold text-[#5C4632] flex items-center gap-1.5">
                        <i data-lucide="map" class="w-3.5 h-3.5 text-[#8C6A2D]"></i>
                        <span>Map Server:</span>
                    </span>
                    @if($mapStatus['online'])
                        <span class="ro-status-online">ONLINE</span>
                    @else
                        <span class="ro-status-offline">OFFLINE</span>
                    @endif
                </div>

                <div class="ro-inner-box mt-2 flex items-center justify-between">
                    <span class="font-bold text-[#3A2A1C] flex items-center gap-1.5">
                        <i data-lucide="compass" class="w-4 h-4 text-[#315B88]"></i>
                        <span>Adventurers:</span>
                    </span>
                    <strong class="text-[#315B88] font-mono text-sm">{{ $onlineCount }} Online</strong>
                </div>
            </div>
        </div>

        <!-- 3. Server Info Box -->
        <div class="ro-panel">
            <div class="ro-panel-header">
                <i data-lucide="info" class="w-4 h-4"></i>
                <span>Server Information</span>
            </div>
            <div class="p-3 text-xs space-y-1.5">
                <div class="flex justify-between py-0.5 border-b border-[#DECBB1]">
                    <span class="text-[#5C4632]">Base Rates:</span>
                    <strong class="text-[#3A2A1C]">{{ config('aether.rates.base') }}</strong>
                </div>
                <div class="flex justify-between py-0.5 border-b border-[#DECBB1]">
                    <span class="text-[#5C4632]">Job Rates:</span>
                    <strong class="text-[#3A2A1C]">{{ config('aether.rates.job') }}</strong>
                </div>
                <div class="flex justify-between py-0.5 border-b border-[#DECBB1]">
                    <span class="text-[#5C4632]">Item Drop:</span>
                    <strong class="text-[#3A2A1C]">{{ config('aether.rates.drop') }}</strong>
                </div>
                <div class="flex justify-between py-0.5 border-b border-[#DECBB1]">
                    <span class="text-[#5C4632]">Card Multiplier:</span>
                    <strong class="text-[#3A2A1C]">{{ config('aether.rates.card') }}</strong>
                </div>
                <div class="flex justify-between py-0.5 border-b border-[#DECBB1]">
                    <span class="text-[#5C4632]">Max Level:</span>
                    <strong class="text-[#3A2A1C]">{{ config('aether.rates.max_base_level') }} / {{ config('aether.rates.max_job_level') }}</strong>
                </div>
                <div class="flex justify-between py-0.5 border-b border-[#DECBB1]">
                    <span class="text-[#5C4632]">Max ASPD:</span>
                    <strong class="text-[#3A2A1C]">{{ config('aether.rates.max_aspd') }}</strong>
                </div>
                <div class="flex justify-between py-0.5">
                    <span class="text-[#5C4632]">Emulator Core:</span>
                    <strong class="text-[#315B88] uppercase">{{ config('emulator.driver') }} ({{ config('emulator.renewal') ? 'Renewal' : 'Pre-Re' }})</strong>
                </div>
            </div>
        </div>
    </aside>

    <!-- ======================================================================
         CENTER CONTENT (Col 2 on desktop)
         - Medieval Welcome Banner
         - Latest Chronicle / News & Announcements
         - Featured Portals & Game Guides
         ====================================================================== -->
    <section class="ro-col-center">
        <!-- 1. Medieval Welcome Banner -->
        <div class="ro-panel">
            <div class="p-6 sm:p-8 bg-gradient-to-b from-[#FCF7ED] to-[#F5EBD5] border-b-2 border-[#8C6A2D]">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#5B402B] text-[#FFDA89] border border-[#8C6A2D]">
                        Classic Rune-Midgard Chronicles
                    </span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black font-serif tracking-tight text-[#2D1D13] leading-tight mb-3">
                    Welcome to {{ config('aether.server_name', 'FreynixRO') }}
                </h1>
                <p class="text-xs sm:text-sm text-[#5C4632] leading-relaxed mb-5">
                    Step foot into a faithful recreation of classic Ragnarok Online. Experience the thrill of early MVP encounters, balanced guild sieges during War of Emperium, and a bustling Prontera marketplace.
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('register') }}" class="ro-btn-gold text-xs sm:text-sm py-2 px-5">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        <span>Begin Your Adventure</span>
                    </a>
                    @if($rcModMgr->isEnabled('downloads'))
                    <a href="{{ route('downloads.index') }}" class="ro-btn-primary text-xs sm:text-sm py-2 px-5">
                        <i data-lucide="download" class="w-4 h-4 text-[#FFDA89]"></i>
                        <span>Download Game Client</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Quick Rates Strip inside banner -->
            <div class="ro-rates-strip grid grid-cols-2 sm:grid-cols-4 w-full text-center bg-[#EBDDBF] border-t border-[#DECBB1] py-2.5 text-xs">
                <div class="border-b sm:border-b-0 border-r border-[#DECBB1] py-1 px-2">
                    <span class="block text-[10px] text-[#77644E] font-bold uppercase">Base</span>
                    <strong class="text-sm font-bold text-[#3A2A1C]">{{ config('aether.rates.base') }}</strong>
                </div>
                <div class="border-b sm:border-b-0 sm:border-r border-[#DECBB1] py-1 px-2">
                    <span class="block text-[10px] text-[#77644E] font-bold uppercase">Job</span>
                    <strong class="text-sm font-bold text-[#3A2A1C]">{{ config('aether.rates.job') }}</strong>
                </div>
                <div class="border-r border-[#DECBB1] py-1 px-2">
                    <span class="block text-[10px] text-[#77644E] font-bold uppercase">Drop</span>
                    <strong class="text-sm font-bold text-[#3A2A1C]">{{ config('aether.rates.drop') }}</strong>
                </div>
                <div class="py-1 px-2">
                    <span class="block text-[10px] text-[#77644E] font-bold uppercase">Episode</span>
                    <strong class="text-sm font-bold text-[#315B88]">19.0</strong>
                </div>
            </div>
        </div>

        <!-- 2. News and Announcements -->
        @if($rcModMgr->isEnabled('news'))
        <div class="ro-panel">
            <div class="ro-panel-header justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="scroll" class="w-4 h-4"></i>
                    <span>Town Notice Board &bull; Latest News</span>
                </div>
                <a href="{{ route('news.index') }}" class="text-[11px] text-[#FFDA89] hover:underline flex items-center gap-1 font-serif">
                    <span>View All</span>
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>

            <div class="divide-y divide-[#DECBB1] p-2">
                @forelse($news as $article)
                    <article class="p-3 hover:bg-[#EBD6B3] rounded transition-colors space-y-1.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                @if($article->is_pinned)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#D97706] text-[#FFF] shadow-sm">
                                        Pinned
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#315B88] text-[#F8EBD2]">
                                    {{ $article->category }}
                                </span>
                                <h3 class="text-sm font-bold font-serif text-[#2D1D13]">
                                    <a href="{{ route('news.show', $article->slug) }}" class="hover:text-[#315B88] transition-colors">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                            </div>
                            <time class="text-[11px] text-[#77644E] font-mono">
                                {{ $article->published_at ? $article->published_at->format('M d, Y') : $article->created_at->format('M d, Y') }}
                            </time>
                        </div>
                        <p class="text-xs text-[#5C4632] leading-relaxed line-clamp-2">
                            {{ $article->summary ?: Str::limit(strip_tags($article->content), 140) }}
                        </p>
                    </article>
                @empty
                    <div class="p-6 text-center text-xs text-[#77644E]">
                        <i data-lucide="feather" class="w-8 h-8 mx-auto mb-2 text-[#8C6A2D]"></i>
                        <p class="font-bold">No announcements posted on the notice board yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
        @endif

        <!-- 3. Featured MMORPG Portals -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if($rcModMgr->isEnabled('database'))
            <a href="{{ route('itemdb.index') }}" class="ro-panel p-4 hover:shadow-lg transition-all group block">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded bg-[#493322] border border-[#8C6A2D] flex items-center justify-center text-[#FFDA89] group-hover:scale-105 transition-transform">
                        <i data-lucide="database" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-serif font-bold text-[#2D1D13] group-hover:text-[#315B88] transition-colors">Item Compendium</h4>
                        <p class="text-[11px] text-[#77644E]">Search weapons, equipment, cards, and drops.</p>
                    </div>
                </div>
            </a>
            @endif

            <a href="{{ route('woe.index') }}" class="ro-panel p-4 hover:shadow-lg transition-all group block">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded bg-[#493322] border border-[#8C6A2D] flex items-center justify-center text-[#FFDA89] group-hover:scale-105 transition-transform">
                        <i data-lucide="swords" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-serif font-bold text-[#2D1D13] group-hover:text-[#315B88] transition-colors">War of Emperium</h4>
                        <p class="text-[11px] text-[#77644E]">Inspect castle ownership and siege schedules.</p>
                    </div>
                </div>
            </a>
        </div>
    </section>

    <!-- ======================================================================
         RIGHT SIDEBAR (Col 3 on desktop)
         - Hall of Fame (Top 5 Adventurers)
         - Community & Discord
         - Downloads & Support
         ====================================================================== -->
    <aside class="ro-col-right">
        <!-- 1. Top Adventurers (Hall of Fame) -->
        @if($rcModMgr->isEnabled('rankings'))
        <div class="ro-panel">
            <div class="ro-panel-header justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="crown" class="w-4 h-4"></i>
                    <span>Hall of Fame</span>
                </div>
                <a href="{{ route('rankings.index') }}" class="text-[11px] text-[#FFDA89] hover:underline font-serif">
                    Top 50
                </a>
            </div>

            <div class="p-2 space-y-1.5">
                @forelse($topPlayers as $idx => $player)
                    @php
                        $pCharId = data_get($player, 'char_id');
                        $pName = data_get($player, 'name', 'Adventurer');
                        $pBaseLv = data_get($player, 'base_level', 1);
                        $pJobLv = data_get($player, 'job_level', 1);
                        $pClassName = data_get($player, 'class_name') ?? (\App\Services\Emulator\JobHelper::getName(data_get($player, 'class', 0)) ?: 'Novice');
                    @endphp
                    <div class="flex items-center justify-between p-2 rounded bg-[#FCF7ED] border border-[#DECBB1] text-xs">
                        <div class="flex items-center gap-2.5">
                            @if($idx === 0)
                                <span class="ro-rank-badge ro-rank-1">1</span>
                            @elseif($idx === 1)
                                <span class="ro-rank-badge ro-rank-2">2</span>
                            @elseif($idx === 2)
                                <span class="ro-rank-badge ro-rank-3">3</span>
                            @else
                                <span class="ro-rank-badge ro-rank-other">{{ $idx + 1 }}</span>
                            @endif
                            <div class="overflow-hidden">
                                @if($pCharId)
                                    <a href="{{ route('characters.show', $pCharId) }}" class="font-bold text-[#2D1D13] hover:text-[#315B88] truncate block">
                                        {{ $pName }}
                                    </a>
                                @else
                                    <span class="font-bold text-[#2D1D13] truncate block">{{ $pName }}</span>
                                @endif
                                <span class="text-[10px] text-[#77644E] block">
                                    Lv. {{ $pBaseLv }} / {{ $pJobLv }}
                                </span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-[#315B88] uppercase tracking-wider">
                            {{ $pClassName }}
                        </span>
                    </div>
                @empty
                    <div class="p-4 text-center text-xs text-[#77644E]">
                        <span>No ranking data recorded yet.</span>
                    </div>
                @endforelse
            </div>

            <div class="p-2 bg-[#EBDDBF] border-t border-[#DECBB1] text-center">
                <a href="{{ route('rankings.index') }}" class="ro-btn-primary w-full text-xs">
                    <i data-lucide="trophy" class="w-3.5 h-3.5"></i>
                    <span>Full Leaderboards</span>
                </a>
            </div>
        </div>
        @endif

        <!-- 2. Community & Discord Box -->
        <div class="ro-panel">
            <div class="ro-panel-header">
                <i data-lucide="messages-square" class="w-4 h-4"></i>
                <span>Tavern & Community</span>
            </div>
            <div class="p-3 space-y-2.5 text-xs">
                <p class="text-[#5C4632] leading-relaxed">
                    Join our active adventurer community for guilds, patch previews, market trades, and live technical support.
                </p>
                @if(config('aether.community.discord'))
                    <a href="{{ config('aether.community.discord') }}" target="_blank" rel="noopener" class="ro-btn-primary w-full text-center py-2">
                        <i data-lucide="message-square" class="w-4 h-4 text-[#9CBBE9]"></i>
                        <span>Join Community Discord</span>
                    </a>
                @endif
                <div class="grid grid-cols-2 gap-2 pt-1">
                    @if($rcModMgr->isEnabled('donations'))
                    <a href="{{ route('donations.index') }}" class="ro-btn-gold text-center py-1.5 text-xs">
                        <i data-lucide="heart" class="w-3 h-3 text-[#B71C1C]"></i>
                        <span>Support Server</span>
                    </a>
                    @endif
                    @if($rcModMgr->isEnabled('downloads'))
                    <a href="{{ route('downloads.index') }}" class="ro-btn-wood text-center py-1.5 text-xs">
                        <i data-lucide="download" class="w-3 h-3 text-[#FFDA89]"></i>
                        <span>Full Client</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Guild Roster Shortcut -->
        @if($rcModMgr->isEnabled('guild'))
        <div class="ro-panel">
            <div class="ro-panel-header justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                    <span>Guild Rankings</span>
                </div>
                <a href="{{ route('guilds.index') }}" class="text-[11px] text-[#FFDA89] hover:underline font-serif">
                    Roster
                </a>
            </div>
            <div class="p-3 text-xs space-y-2 text-[#5C4632]">
                <p class="leading-relaxed">
                    Inspect active guilds competing for territorial supremacy across Midgard castles.
                </p>
                <a href="{{ route('guilds.index') }}" class="ro-btn-wood w-full text-center py-1.5">
                    <i data-lucide="users-round" class="w-3.5 h-3.5 text-[#FFDA89]"></i>
                    <span>Inspect Guild Standings</span>
                </a>
            </div>
        </div>
        @endif
    </aside>
</div>
@endsection

