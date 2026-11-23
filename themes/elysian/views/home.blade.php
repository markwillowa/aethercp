@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
    <!-- Elysian Mythic Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl elysian-hero-banner p-8 sm:p-14">
        <div class="relative z-10 max-w-2xl space-y-5">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full elysian-gold-badge text-xs font-bold tracking-wide">
                <i data-lucide="crown" class="w-4 h-4 text-amber-400"></i>
                <span>THE VALKYRIE REALM &bull; ELYSIAN SAGA</span>
            </div>
            
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight leading-tight text-white">
                Ascend to <span class="elysian-gold-text">Valhalla</span>
            </h1>
            
            <p class="text-base sm:text-lg text-slate-300 leading-relaxed font-normal">
                Welcome to <strong class="text-amber-300 font-bold">{{ config('aether.server_name', 'Freynix RO') }}</strong>. Unsheathe your blade in an exalted world of heroic conquest, legendary Valkyrie relics, and high-intensity War of Emperium castle sieges.
            </p>

            <div class="pt-3 flex flex-wrap items-center gap-4">
                <a href="{{ route('register') }}" class="elysian-btn-primary px-7 py-3.5 rounded-xl font-bold flex items-center gap-2 transition-all">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    <span>Begin Your Odyssey</span>
                </a>
                <a href="{{ route('downloads.index') }}" class="elysian-btn-secondary px-7 py-3.5 rounded-xl font-semibold flex items-center gap-2 transition-all">
                    <i data-lucide="download" class="w-4 h-4 text-amber-400"></i>
                    <span>Download Game Client</span>
                </a>
            </div>
        </div>

        <!-- Decorative Nordic/Valkyrie Halo -->
        <div class="absolute -right-10 -bottom-10 opacity-15 pointer-events-none hidden lg:block text-amber-400">
            <i data-lucide="crown" class="w-[450px] h-[450px]"></i>
        </div>
    </div>

    <!-- Mythic Server Rates Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 border border-amber-500/20 hover:border-amber-500/40 rounded-2xl p-5 backdrop-blur-sm transition-all group">
            <div class="flex items-center justify-between text-amber-400/80 text-xs mb-2">
                <span class="font-bold uppercase tracking-wider">Base EXP</span>
                <i data-lucide="flame" class="w-4 h-4 text-amber-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-black text-white group-hover:text-amber-300 transition-colors">{{ config('aether.rates.base') }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Accelerated Progression</p>
        </div>

        <div class="bg-slate-900/80 border border-amber-500/20 hover:border-amber-500/40 rounded-2xl p-5 backdrop-blur-sm transition-all group">
            <div class="flex items-center justify-between text-amber-400/80 text-xs mb-2">
                <span class="font-bold uppercase tracking-wider">Job EXP</span>
                <i data-lucide="award" class="w-4 h-4 text-sky-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-black text-white group-hover:text-sky-300 transition-colors">{{ config('aether.rates.job') }}</p>
            <p class="text-[11px] text-slate-400 mt-1">4th Job Mastery</p>
        </div>

        <div class="bg-slate-900/80 border border-amber-500/20 hover:border-amber-500/40 rounded-2xl p-5 backdrop-blur-sm transition-all group">
            <div class="flex items-center justify-between text-amber-400/80 text-xs mb-2">
                <span class="font-bold uppercase tracking-wider">Drop Rate</span>
                <i data-lucide="gem" class="w-4 h-4 text-amber-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-black text-white group-hover:text-amber-300 transition-colors">{{ config('aether.rates.drop') }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Loot & Card Multiplier</p>
        </div>

        <div class="bg-slate-900/80 border border-amber-500/20 hover:border-amber-500/40 rounded-2xl p-5 backdrop-blur-sm transition-all group">
            <div class="flex items-center justify-between text-amber-400/80 text-xs mb-2">
                <span class="font-bold uppercase tracking-wider">Episode</span>
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-black text-white group-hover:text-emerald-300 transition-colors">19.0</p>
            <p class="text-[11px] text-slate-400 mt-1">Land of Issgard Renewal</p>
        </div>
    </div>

    <!-- Main Content Stage: 2-Col Left (News & Portals) + 1-Col Right (Account, Cluster Status, Top Adventurers, Discord) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left 2 Columns -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Latest Chronicles & Announcements -->
            <div class="space-y-5">
                <div class="flex items-center justify-between border-b border-amber-500/20 pb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                            <i data-lucide="scroll" class="w-6 h-6 text-amber-400"></i>
                            <span>Chronicles of the Realm</span>
                        </h2>
                        <p class="text-xs text-amber-300/60 mt-1">Official patch notes, server dispatches, and community events.</p>
                    </div>
                    <a href="{{ route('news.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-bold flex items-center gap-1 transition-colors">
                        <span>View All Dispatches</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($news as $article)
                        <article class="bg-slate-900/80 border border-amber-500/20 hover:border-amber-500/50 rounded-2xl p-6 backdrop-blur-sm transition-all group">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    @if($article->is_pinned)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                            Pinned
                                        </span>
                                    @endif
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider elysian-gold-badge">
                                        {{ ucfirst($article->category) }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-slate-500 font-mono">{{ $article->published_at?->format('M d, Y') ?? 'Recent' }}</span>
                            </div>

                            <h3 class="text-lg font-bold text-white group-hover:text-amber-300 transition-colors mb-2">
                                <a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a>
                            </h3>

                            <p class="text-xs text-slate-300 line-clamp-2 leading-relaxed mb-4">
                                {{ $article->summary ?: strip_tags($article->content) }}
                            </p>

                            <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                                <span class="text-slate-500 flex items-center gap-1.5">
                                    <i data-lucide="user" class="w-3.5 h-3.5 text-amber-400/70"></i>
                                    <span>By {{ $article->author_name }}</span>
                                </span>
                                <a href="{{ route('news.show', $article->slug) }}" class="text-amber-400 group-hover:text-amber-300 font-semibold flex items-center gap-1">
                                    <span>Read Dispatch</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="bg-slate-900/80 border border-amber-500/20 rounded-2xl p-10 text-center text-slate-400">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                            <p class="text-sm">No chronicles have been inscribed yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Portals Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <a href="{{ route('rankings.index') }}" class="group bg-slate-900/80 border border-amber-500/20 hover:border-amber-500/50 rounded-2xl p-5 backdrop-blur-sm transition-all shadow-xl hover:-translate-y-1 block">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center mb-3 text-amber-400 group-hover:bg-amber-500 group-hover:text-slate-950 transition-all">
                        <i data-lucide="trending-up" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-bold text-white group-hover:text-amber-300 transition-colors">Hall of Fame</h3>
                    <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Formidable adventurers, guilds &amp; MVP ladder.</p>
                </a>

                <a href="{{ route('woe.index') }}" class="group bg-slate-900/80 border border-amber-500/20 hover:border-amber-500/50 rounded-2xl p-5 backdrop-blur-sm transition-all shadow-xl hover:-translate-y-1 block">
                    <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center mb-3 text-sky-400 group-hover:bg-sky-500 group-hover:text-slate-950 transition-all">
                        <i data-lucide="swords" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-bold text-white group-hover:text-sky-300 transition-colors">War of Emperium</h3>
                    <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Castle ownership, siege timetables &amp; battle honors.</p>
                </a>

                <a href="{{ route('itemdb.index') }}" class="group bg-slate-900/80 border border-amber-500/20 hover:border-amber-500/50 rounded-2xl p-5 backdrop-blur-sm transition-all shadow-xl hover:-translate-y-1 block">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center mb-3 text-amber-400 group-hover:bg-amber-500 group-hover:text-slate-950 transition-all">
                        <i data-lucide="database" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-bold text-white group-hover:text-amber-300 transition-colors">Compendium</h3>
                    <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Equipment stats, cards, drops &amp; craftable recipes.</p>
                </a>
            </div>
        </div>

        <!-- Right 1 Column (Side Widgets) -->
        <div class="space-y-6">
            <!-- Account Portal Widget -->
            <div class="bg-slate-900/80 border border-amber-500/20 rounded-2xl p-5 space-y-4 backdrop-blur-sm">
                <div class="flex items-center gap-2 border-b border-amber-500/20 pb-3">
                    <i data-lucide="key" class="w-4 h-4 text-amber-400"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white">Valkyrie Account Portal</h3>
                </div>

                @auth
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-950/60 border border-amber-500/20">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 to-amber-400 flex items-center justify-center font-bold text-sm text-slate-950 shadow-md">
                                {{ strtoupper(substr(auth()->user()->username ?? auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-bold text-white truncate">{{ auth()->user()->username ?? auth()->user()->name }}</p>
                                <p class="text-[11px] text-amber-300 font-mono">ACCOUNT #{{ auth()->user()->account_id }}</p>
                            </div>
                        </div>

                        <div class="space-y-1.5 text-xs font-semibold">
                            <a href="{{ route('account.profile') }}" class="flex items-center justify-between p-2 rounded-xl bg-slate-950/50 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 transition-colors">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="user" class="w-3.5 h-3.5 text-amber-400"></i>
                                    <span>My Account Profile</span>
                                </span>
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-500"></i>
                            </a>
                            <a href="{{ route('account.characters') }}" class="flex items-center justify-between p-2 rounded-xl bg-slate-950/50 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 transition-colors">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-sky-400"></i>
                                    <span>Character Management</span>
                                </span>
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-500"></i>
                            </a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 transition-colors">
                                    <span class="flex items-center gap-2">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-amber-400"></i>
                                        <span>Admin Control Panel</span>
                                    </span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-amber-400"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="space-y-3">
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Sign in to inspect character attributes, allocate stat points, claim VIP Kafra benefits, and manage donation items.
                        </p>
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('login') }}" class="py-2 text-center rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold border border-slate-700 transition-colors">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" class="py-2 text-center rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 text-xs font-bold shadow-md transition-colors">
                                Register
                            </a>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Live Cluster Status -->
            <div class="bg-slate-900/80 border border-amber-500/20 rounded-2xl p-5 space-y-4 backdrop-blur-sm">
                <div class="flex items-center justify-between border-b border-amber-500/20 pb-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="activity" class="w-4 h-4 text-amber-400"></i>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-white">Live Cluster Status</h3>
                    </div>
                    @php
                        $onlineCount = app(\App\Contracts\EmulatorInterface::class)->getOnlinePlayerCount();
                    @endphp
                    <span class="text-xs font-bold text-amber-400 font-mono">{{ $onlineCount }} Online</span>
                </div>

                <div class="space-y-2 text-xs">
                    @foreach(['login' => 'Login Realm', 'char' => 'Char Gateway', 'map' => 'Midgard Map Cluster'] as $type => $label)
                        @php
                            $status = app(\App\Contracts\EmulatorInterface::class)->checkServerStatus($type);
                        @endphp
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
                            <span class="font-medium text-slate-300">{{ $label }}</span>
                            <div class="flex items-center gap-2">
                                @if($status['online'])
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $status['latency_ms'] }}ms</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-950/80 text-emerald-400 border border-emerald-500/30">ONLINE</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-950/80 text-red-400 border border-red-500/30">OFFLINE</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Adventurers (Hall of Fame) -->
            <div class="bg-slate-900/80 border border-amber-500/20 rounded-2xl p-5 space-y-4 backdrop-blur-sm">
                <div class="flex items-center justify-between border-b border-amber-500/20 pb-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="crown" class="w-4 h-4 text-amber-400"></i>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-white">Hall of Fame</h3>
                    </div>
                    <a href="{{ route('rankings.index') }}" class="text-xs text-amber-400 hover:underline">Top 50</a>
                </div>

                <div class="space-y-2">
                    @forelse($topPlayers as $idx => $player)
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-950/50 hover:bg-slate-800/40 transition-colors text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center font-bold text-[10px] {{ $idx === 0 ? 'bg-amber-400 text-slate-950' : ($idx === 1 ? 'bg-slate-300 text-slate-950' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-slate-800 text-slate-400')) }}">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="overflow-hidden">
                                    <a href="{{ route('characters.show', $player->char_id) }}" class="font-bold text-white hover:text-amber-400 truncate block">
                                        {{ $player->name }}
                                    </a>
                                </div>
                            </div>
                            <span class="text-amber-400 font-bold font-mono">Lv. {{ $player->base_level }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 text-center py-2">No ranking data recorded.</p>
                    @endforelse
                </div>
            </div>

            <!-- Official Discord Banner -->
            @if(config('aether.community.discord'))
                <div class="rounded-2xl p-5 bg-gradient-to-br from-indigo-950/80 to-slate-900 border border-indigo-500/30 space-y-3">
                    <div class="flex items-center gap-2 text-indigo-300 font-bold text-sm">
                        <i data-lucide="message-square" class="w-4 h-4"></i>
                        <span>Valhalla Sanctuary Discord</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Join our heroic community for party recruitment, guild alliances, and War of Emperium siege dispatches.
                    </p>
                    <a href="{{ config('aether.community.discord') }}" target="_blank" rel="noopener" class="block text-center py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md transition-colors">
                        Join Discord Server
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
