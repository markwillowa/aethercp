@extends('layouts.app')

@php
    $modMgr = app(\App\Services\Module\ModuleManager::class);
    $galleryScreenshots = [];
    $rawGallery = \App\Models\SiteSetting::get('chronicles_gallery_data');
    if (!empty($rawGallery)) {
        $decoded = json_decode($rawGallery, true);
        if (is_array($decoded) && count($decoded) > 0) {
            $galleryScreenshots = $decoded;
        }
    }
    if (empty($galleryScreenshots)) {
        $galleryScreenshots = [
            [
                'title' => 'Capital City of Prontera',
                'image' => asset('images/screenshot_prontera.jpg'),
                'category' => 'Towns'
            ],
            [
                'title' => 'War of Emperium Castle Siege',
                'image' => asset('images/feat_woe_boss.jpg'),
                'category' => 'WoE'
            ],
            [
                'title' => 'Grand Midgard Festival',
                'image' => asset('images/news_festival.jpg'),
                'category' => 'Events'
            ],
            [
                'title' => 'Treasure Vaults & Ancient Ruins',
                'image' => asset('images/news_update.jpg'),
                'category' => 'Raids'
            ],
        ];
    }
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
    <!-- Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900/90 to-cyan-950/40 border border-slate-800 p-8 sm:p-12 shadow-2xl">
        <div class="relative z-10 max-w-2xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-semibold">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                <span>Next-Gen Ragnarok Online Experience</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                Welcome to <span class="aether-gradient-text">{{ config('aether.server_name', 'Freynix RO') }}</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-300 leading-relaxed">
                Step into a high-rate renewal world crafted for balanced competition, intense War of Emperium, and deep end-game instance progression.
            </p>
            <div class="pt-2 flex flex-wrap items-center gap-3">
                <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Create Free Account</span>
                </a>
                @if($modMgr->isEnabled('downloads'))
                <a href="{{ route('downloads.index') }}" class="px-6 py-3 rounded-xl font-semibold bg-slate-800/80 hover:bg-slate-700/80 text-white border border-slate-700 transition-colors flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4 text-cyan-400"></i>
                    <span>Download Game Client</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Decorative fantasy badge graphic -->
        <div class="absolute -right-12 -bottom-12 opacity-15 pointer-events-none hidden lg:block">
            <i data-lucide="shield" class="w-96 h-96 text-cyan-400"></i>
        </div>
    </div>

    <!-- Quick Meta Server Rates Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-2">
                <span>Base Experience</span>
                <i data-lucide="trending-up" class="w-4 h-4 text-cyan-400"></i>
            </div>
            <p class="text-2xl font-bold text-white">{{ config('aether.rates.base') }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Accelerated Leveling</p>
        </div>

        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-2">
                <span>Job Experience</span>
                <i data-lucide="award" class="w-4 h-4 text-blue-400"></i>
            </div>
            <p class="text-2xl font-bold text-white">{{ config('aether.rates.job') }}</p>
            <p class="text-[11px] text-slate-500 mt-1">4th Job Progression</p>
        </div>

        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-2">
                <span>Item Drop Rate</span>
                <i data-lucide="package" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <p class="text-2xl font-bold text-white">{{ config('aether.rates.drop') }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Fair Equipment Drops</p>
        </div>

        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 backdrop-blur-sm">
            <div class="flex items-center justify-between text-slate-400 text-xs mb-2">
                <span>Card Drop Rate</span>
                <i data-lucide="layers" class="w-4 h-4 text-purple-400"></i>
            </div>
            <p class="text-2xl font-bold text-white">{{ config('aether.rates.card') }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Normal & MVP Cards</p>
        </div>
    </div>

    <!-- Main Content Grid: Latest News (Left) + Server Widgets (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @if($modMgr->isEnabled('news'))
        <!-- News Section (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="newspaper" class="w-5 h-5 text-cyan-400"></i>
                    <h2 class="text-xl font-bold text-white">Latest Announcements & Updates</h2>
                </div>
                <a href="{{ route('news.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 flex items-center gap-1 font-medium">
                    <span>View All</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="space-y-4">
                @forelse($news as $article)
                    <article class="bg-slate-900/50 border border-slate-800 hover:border-slate-700/80 rounded-2xl p-6 transition-all duration-200">
                        <div class="flex items-center gap-2.5 mb-2.5">
                            @if($article->is_pinned)
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                                    Pinned
                                </span>
                            @endif
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-slate-800 text-slate-300">
                                {{ $article->category }}
                            </span>
                            <span class="text-xs text-slate-500">{{ $article->published_at?->diffForHumans() }}</span>
                        </div>

                        <h3 class="text-lg font-bold text-white mb-2 hover:text-cyan-400 transition-colors">
                            <a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a>
                        </h3>

                        <p class="text-sm text-slate-300 leading-relaxed mb-4">
                            {{ $article->summary }}
                        </p>

                        <div class="flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-800/60">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                                Posted by {{ $article->author_name }}
                            </span>
                            <a href="{{ route('news.show', $article->slug) }}" class="text-cyan-400 hover:underline flex items-center gap-1 font-medium">
                                <span>Read Full Article</span>
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="bg-slate-900/40 border border-slate-800 rounded-2xl p-8 text-center text-slate-400">
                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                        <p>No news articles published yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
        @endif

        <!-- Sidebar Widgets (1 Col) -->
        <div class="{{ $modMgr->isEnabled('news') ? 'space-y-6' : 'lg:col-span-3 space-y-6' }}">
            <!-- Account Portal Widget -->
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i data-lucide="key" class="w-4 h-4 text-cyan-400"></i>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white">Account Portal</h3>
                </div>

                @auth
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-950/60 border border-slate-800">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-500 flex items-center justify-center font-bold text-sm text-white shadow-md">
                                {{ strtoupper(substr(auth()->user()->username ?? auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-bold text-white truncate">{{ auth()->user()->username ?? auth()->user()->name }}</p>
                                <p class="text-[11px] text-cyan-400 font-mono">ACCOUNT #{{ auth()->user()->account_id }}</p>
                            </div>
                        </div>

                        <div class="space-y-1.5 text-xs font-semibold">
                            <a href="{{ route('account.profile') }}" class="flex items-center justify-between p-2 rounded-xl bg-slate-950/50 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 transition-colors">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="user" class="w-3.5 h-3.5 text-cyan-400"></i>
                                    <span>My Account Profile</span>
                                </span>
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-500"></i>
                            </a>
                            @if($modMgr->isEnabled('character'))
                            <a href="{{ route('account.characters') }}" class="flex items-center justify-between p-2 rounded-xl bg-slate-950/50 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 transition-colors">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-blue-400"></i>
                                    <span>Character Management</span>
                                </span>
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-500"></i>
                            </a>
                            @endif
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
                            Sign in to inspect your character roster, reallocate stat points, view guild affiliations, and manage donation items.
                        </p>
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('login') }}" class="py-2 text-center rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold border border-slate-700 transition-colors">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" class="py-2 text-center rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 text-xs font-bold shadow-md transition-colors">
                                Register
                            </a>
                        </div>
                    </div>
                @endauth
            </div>

            @if($modMgr->isEnabled('server_info'))
            <!-- Server Status Card -->
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="activity" class="w-4 h-4 text-cyan-400"></i>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-white">Live Cluster Status</h3>
                    </div>
                    <a href="{{ route('server.status') }}" class="text-xs text-cyan-400 hover:text-cyan-300 flex items-center gap-1 font-medium">
                        <span>Details</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="space-y-2.5 text-xs">
                    @foreach(['login' => 'Login Server', 'char' => 'Character Server', 'map' => 'Map Cluster'] as $type => $label)
                        @php
                            $status = app(\App\Contracts\EmulatorInterface::class)->checkServerStatus($type);
                        @endphp
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80">
                            <span class="font-medium text-slate-300">{{ $label }}</span>
                            <div class="flex items-center gap-2">
                                @if($status['online'])
                                    <span class="text-[10px] text-slate-400">{{ $status['latency_ms'] }}ms</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-950/80 text-emerald-400 border border-emerald-500/30">ONLINE</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-950/80 text-red-400 border border-red-500/30">OFFLINE</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($modMgr->isEnabled('gallery'))
            <!-- Media & Screenshots Gallery Widget (Randomly changes every 15 seconds) -->
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 space-y-3"
                 x-data="{
                     gallery: {{ json_encode($galleryScreenshots) }},
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
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="image" class="w-4 h-4 text-cyan-400"></i>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-white">Media Gallery</h3>
                    </div>
                    <a href="{{ route('gallery.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 flex items-center gap-1 font-medium">
                        <span>View All</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="relative overflow-hidden rounded-xl border border-slate-800/80 bg-slate-950 group">
                    <a href="{{ route('gallery.index') }}" class="block relative aspect-video overflow-hidden" title="Click to view Media Gallery">
                        <img :src="gallery[activeIdx]?.image" 
                             :alt="gallery[activeIdx]?.title"
                             class="w-full h-full object-cover transition-all duration-700 hover:scale-105"
                             onerror="this.src='/images/news_update.jpg'">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent pointer-events-none"></div>

                        <div class="absolute bottom-0 inset-x-0 p-3 flex items-center justify-between">
                            <div>
                                <span x-text="gallery[activeIdx]?.category || 'Screenshot'" class="text-[9px] font-mono font-bold uppercase tracking-wider text-cyan-400 bg-slate-900/90 px-1.5 py-0.5 rounded border border-cyan-500/30"></span>
                                <h4 x-text="gallery[activeIdx]?.title" class="text-xs font-bold text-white mt-1 drop-shadow-sm truncate max-w-[180px]"></h4>
                            </div>
                            <span class="w-7 h-7 rounded-lg bg-cyan-500/20 text-cyan-400 border border-cyan-500/40 flex items-center justify-center shrink-0 shadow">
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                            </span>
                        </div>
                    </a>
                </div>
            </div>
            @endif

            @if($modMgr->isEnabled('rankings'))
            <!-- Live 5-Tab Rankings Widget -->
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 space-y-4"
                 x-data="{
                     activeTab: 'lvl',
                     tabs: [
                         { key: 'lvl', label: 'Lvl' },
                         { key: 'job', label: 'Job' },
                         { key: 'pvp', label: 'PvP' },
                         { key: 'mvp', label: 'MVP' },
                         { key: 'guild', label: 'Guild' }
                     ],
                     datasets: {{ json_encode($homeRankings ?? []) }},
                     loading: false,
                     error: null,
                     feedBaseUrl: '{{ url('/rankings/feed') }}',
                     fullRankingsUrl: '{{ route('rankings.index') }}',
                     init() {
                         setInterval(() => {
                             this.refreshTab(this.activeTab, true);
                         }, 30000);
                     },
                     selectTab(key) {
                         this.activeTab = key;
                         this.error = null;
                         this.refreshTab(key, false);
                     },
                     async refreshTab(key, silent = false) {
                         if (!silent && (!this.datasets[key] || !this.datasets[key].items)) {
                             this.loading = true;
                         }
                         try {
                             const res = await fetch(this.feedBaseUrl + '/' + key, {
                                 headers: { 'Accept': 'application/json' }
                             });
                             if (!res.ok) throw new Error('Failed to load rankings');
                             const json = await res.json();
                             if (json.ok && json.data) {
                                 this.datasets[key] = json.data;
                                 this.error = null;
                             }
                         } catch (e) {
                             if (!silent) {
                                 this.error = 'Unable to refresh live rankings.';
                             }
                         } finally {
                             this.loading = false;
                         }
                     },
                     get currentItems() {
                         return (this.datasets[this.activeTab] && this.datasets[this.activeTab].items) ? this.datasets[this.activeTab].items : [];
                     },
                     get lastUpdated() {
                         return (this.datasets[this.activeTab] && this.datasets[this.activeTab].updated_human) ? this.datasets[this.activeTab].updated_human : '';
                     }
                 }">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="trophy" class="w-4 h-4 text-amber-400"></i>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-white">Live Rankings</h3>
                    </div>
                    <a :href="fullRankingsUrl + '?type=' + activeTab" class="text-xs text-cyan-400 hover:underline flex items-center gap-1">
                        <span>View Full Rankings</span>
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </a>
                </div>

                <!-- 5 Ranking Category Tabs -->
                <div class="grid grid-cols-5 gap-1 p-1 rounded-xl bg-slate-950/80 border border-slate-800/80">
                    <template x-for="tab in tabs" :key="tab.key">
                        <button type="button"
                                @click="selectTab(tab.key)"
                                :class="activeTab === tab.key ? 'bg-cyan-500 text-slate-950 font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 font-medium'"
                                class="py-1.5 px-1 rounded-lg text-[11px] transition-all text-center cursor-pointer"
                                x-text="tab.label"></button>
                    </template>
                </div>

                <!-- Error State -->
                <template x-if="error">
                    <div class="p-3 rounded-xl bg-red-950/40 border border-red-500/30 text-red-300 text-xs text-center">
                        <span x-text="error"></span>
                    </div>
                </template>

                <!-- Loading State -->
                <template x-if="loading">
                    <div class="space-y-2 py-2">
                        <div class="h-9 rounded-xl bg-slate-800/50 animate-pulse"></div>
                        <div class="h-9 rounded-xl bg-slate-800/50 animate-pulse"></div>
                        <div class="h-9 rounded-xl bg-slate-800/50 animate-pulse"></div>
                    </div>
                </template>

                <!-- Leaderboard Rows -->
                <div x-show="!loading" class="space-y-2">
                    <template x-for="(item, idx) in currentItems" :key="activeTab + '_' + (item.char_id || item.guild_id || idx)">
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/60 hover:bg-slate-800/50 border border-slate-800/60 transition-colors text-xs gap-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center font-bold text-[10px] shrink-0"
                                      :class="item.rank === 1 ? 'bg-amber-400 text-slate-950' : (item.rank === 2 ? 'bg-slate-300 text-slate-950' : (item.rank === 3 ? 'bg-amber-700 text-white' : 'bg-slate-800 text-slate-400'))"
                                      x-text="item.rank"></span>

                                <!-- Avatar / Guild Emblem -->
                                <template x-if="activeTab === 'guild'">
                                    <div class="w-6 h-6 rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center shrink-0 overflow-hidden">
                                        <template x-if="item.emblem_url">
                                            <img :src="item.emblem_url" alt="" class="w-5 h-5 object-contain">
                                        </template>
                                        <template x-if="!item.emblem_url">
                                            <span class="text-[10px] font-bold text-cyan-400" x-text="item.name ? item.name.charAt(0).toUpperCase() : 'G'"></span>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="activeTab !== 'guild'">
                                    <div class="w-6 h-6 rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center shrink-0 overflow-hidden">
                                        <template x-if="item.guild_emblem_url">
                                            <img :src="item.guild_emblem_url" alt="" class="w-5 h-5 object-contain">
                                        </template>
                                        <template x-if="!item.guild_emblem_url">
                                            <span class="text-[10px] font-bold text-cyan-400" x-text="item.name ? item.name.charAt(0).toUpperCase() : 'A'"></span>
                                        </template>
                                    </div>
                                </template>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <a :href="item.profile_url" class="font-semibold text-slate-200 hover:text-cyan-400 truncate transition-colors" x-text="item.name"></a>
                                        <template x-if="item.achievement_label || item.badge_label">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 shrink-0"
                                                  x-text="item.achievement_label || item.badge_label"></span>
                                        </template>
                                    </div>
                                    <p class="text-[10px] text-slate-500 truncate"
                                       x-text="activeTab === 'guild' ? ('Master: ' + item.master + ' • ' + item.members_count + ' members') : (item.job_name + (item.guild_name ? ' • ' + item.guild_name : ''))"></p>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <template x-if="activeTab === 'guild'">
                                    <div>
                                        <span class="text-cyan-400 font-bold block" x-text="'Lv. ' + item.guild_lv"></span>
                                        <span class="text-[10px] text-amber-400 font-mono" x-text="item.castles_count + ' Castle' + (item.castles_count === 1 ? '' : 's')"></span>
                                    </div>
                                </template>
                                <template x-if="activeTab !== 'guild'">
                                    <div>
                                        <span class="text-cyan-400 font-bold block" x-text="(item.primary_stat_label ? item.primary_stat_label + ' ' : '') + item.primary_stat_value"></span>
                                        <span class="text-[10px] text-slate-500 font-mono" x-text="item.secondary_stat || ''"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="currentItems.length === 0">
                        <div class="p-4 rounded-xl bg-slate-950/50 border border-slate-800/60 text-center space-y-1">
                            <p class="text-xs text-slate-400 font-medium">No rankings recorded yet.</p>
                            <p class="text-[10px] text-slate-500" x-show="activeTab === 'pvp' || activeTab === 'mvp'">Verified gameplay events will appear here automatically.</p>
                        </div>
                    </template>
                </div>

                <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1 border-t border-slate-800/60">
                    <span x-show="lastUpdated">Updated: <span x-text="lastUpdated" class="font-mono text-slate-400"></span></span>
                    <button type="button" @click="refreshTab(activeTab, false)" class="text-cyan-400 hover:text-cyan-300 font-medium cursor-pointer ml-auto">Refresh</button>
                </div>
            </div>
            @endif

            <!-- Join Community Banner -->
            <div class="rounded-2xl p-5 bg-gradient-to-br from-indigo-950/80 to-slate-900 border border-indigo-500/30 space-y-3">
                <div class="flex items-center gap-2 text-indigo-300 font-bold text-sm">
                    <i data-lucide="message-square" class="w-4 h-4"></i>
                    <span>Official Discord</span>
                </div>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Join our vibrant Ragnarok community for guild recruitment, live market trading, and patch announcements.
                </p>
                <a href="{{ config('aether.community.discord') }}" target="_blank" rel="noopener" class="block text-center py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md transition-colors">
                    Join Discord Server
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

