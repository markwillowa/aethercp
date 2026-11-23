@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
    <!-- Elysian Mythic Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0d1424] via-[#09101f] to-[#1a150a] border border-amber-500/30 p-8 sm:p-14 shadow-2xl shadow-amber-950/20">
        <div class="relative z-10 max-w-2xl space-y-5">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full elysian-gold-badge text-xs font-bold tracking-wide">
                <i data-lucide="crown" class="w-4 h-4 text-amber-400"></i>
                <span>THE VALKYRIE REALM &bull; ELYSIAN SAGA</span>
            </div>
            
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight leading-tight">
                Ascend to <span class="elysian-gold-text">Valhalla</span>
            </h1>
            
            <p class="text-base sm:text-lg text-amber-100/90 leading-relaxed font-normal">
                Welcome to <strong class="text-amber-300 font-bold">{{ config('aether.server_name', 'Freynix RO') }}</strong>. Unsheathe your blade in an exalted world of heroic conquest, legendary Valkyrie relics, and high-intensity War of Emperium castle sieges.
            </p>

            <div class="pt-3 flex flex-wrap items-center gap-4">
                <a href="{{ route('register') }}" class="px-7 py-3.5 rounded-xl font-bold bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 text-slate-950 hover:from-amber-300 hover:to-amber-500 shadow-xl shadow-amber-500/25 transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    <span>Begin Your Odyssey</span>
                </a>
                <a href="{{ route('downloads.index') }}" class="px-7 py-3.5 rounded-xl font-semibold bg-slate-900/90 hover:bg-slate-800 text-amber-200 border border-amber-500/40 hover:border-amber-400 transition-all flex items-center gap-2 shadow-lg">
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
        <div class="bg-slate-900/60 border border-amber-500/20 hover:border-amber-500/40 rounded-2xl p-5 backdrop-blur-sm transition-all group">
            <div class="flex items-center justify-between text-amber-400/80 text-xs mb-2">
                <span class="font-bold uppercase tracking-wider">Base EXP</span>
                <i data-lucide="flame" class="w-4 h-4 text-amber-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-black text-white group-hover:text-amber-300 transition-colors">{{ config('aether.rates.base') }}</p>
            <p class="text-[11px] text-amber-300/60 mt-1">Accelerated Progression</p>
        </div>

        <div class="bg-slate-900/60 border border-amber-500/20 hover:border-amber-500/40 rounded-2xl p-5 backdrop-blur-sm transition-all group">
            <div class="flex items-center justify-between text-amber-400/80 text-xs mb-2">
                <span class="font-bold uppercase tracking-wider">Job EXP</span>
                <i data-lucide="award" class="w-4 h-4 text-sky-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-black text-white group-hover:text-sky-300 transition-colors">{{ config('aether.rates.job') }}</p>
            <p class="text-[11px] text-sky-300/60 mt-1">4th Job Mastery</p>
        </div>

        <div class="bg-slate-900/60 border border-amber-500/20 hover:border-amber-500/40 rounded-2xl p-5 backdrop-blur-sm transition-all group">
            <div class="flex items-center justify-between text-amber-400/80 text-xs mb-2">
                <span class="font-bold uppercase tracking-wider">Drop Rate</span>
                <i data-lucide="gem" class="w-4 h-4 text-amber-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-black text-white group-hover:text-amber-300 transition-colors">{{ config('aether.rates.drop') }}</p>
            <p class="text-[11px] text-amber-300/60 mt-1">Loot & Card Multiplier</p>
        </div>

        <div class="bg-slate-900/60 border border-amber-500/20 hover:border-amber-500/40 rounded-2xl p-5 backdrop-blur-sm transition-all group">
            <div class="flex items-center justify-between text-amber-400/80 text-xs mb-2">
                <span class="font-bold uppercase tracking-wider">Episode</span>
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition-transform"></i>
            </div>
            <p class="text-3xl font-black text-white group-hover:text-emerald-300 transition-colors">19.0</p>
            <p class="text-[11px] text-emerald-300/60 mt-1">Land of Issgard Renewal</p>
        </div>
    </div>

    <!-- Quick Portals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('rankings.index') }}" class="group bg-slate-900/60 border border-amber-500/20 hover:border-amber-500/50 rounded-2xl p-6 backdrop-blur-sm transition-all shadow-xl hover:-translate-y-1">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center mb-4 text-amber-400 group-hover:bg-amber-500 group-hover:text-slate-950 transition-all">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold text-white group-hover:text-amber-300 transition-colors">Hall of Fame</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">View the most formidable adventurers, top guilds, and MVP vanquishers in the realm.</p>
        </a>

        <a href="{{ route('woe.index') }}" class="group bg-slate-900/60 border border-amber-500/20 hover:border-amber-500/50 rounded-2xl p-6 backdrop-blur-sm transition-all shadow-xl hover:-translate-y-1">
            <div class="w-12 h-12 rounded-xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center mb-4 text-sky-400 group-hover:bg-sky-500 group-hover:text-slate-950 transition-all">
                <i data-lucide="swords" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold text-white group-hover:text-sky-300 transition-colors">War of Emperium</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">Inspect active castle ownership, siege timetables, and territorial battle honors.</p>
        </a>

        <a href="{{ route('itemdb.index') }}" class="group bg-slate-900/60 border border-amber-500/20 hover:border-amber-500/50 rounded-2xl p-6 backdrop-blur-sm transition-all shadow-xl hover:-translate-y-1">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center mb-4 text-amber-400 group-hover:bg-amber-500 group-hover:text-slate-950 transition-all">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold text-white group-hover:text-amber-300 transition-colors">Compendium Database</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">Search official equipment parameters, bonus scripts, cards, and craftable recipes.</p>
        </a>
    </div>

    <!-- Latest Chronicles & Announcements -->
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-amber-500/20 pb-4">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i data-lucide="scroll" class="w-6 h-6 text-amber-400"></i>
                    <span>Chronicles of the Realm</span>
                </h2>
                <p class="text-xs text-amber-300/60 mt-1">Official patch notes, server dispatches, and upcoming community events.</p>
            </div>
            <a href="{{ route('news.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-bold flex items-center gap-1 transition-colors">
                <span>View All Dispatches</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $recentArticles = \App\Models\News::where('is_published', true)->latest('published_at')->limit(3)->get();
            @endphp
            @forelse($recentArticles as $article)
                <div class="bg-slate-900/60 border border-amber-500/20 hover:border-amber-500/50 rounded-2xl p-6 backdrop-blur-sm transition-all flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider elysian-gold-badge">
                                {{ ucfirst($article->category) }}
                            </span>
                            <span class="text-[11px] text-slate-500">{{ $article->published_at?->format('M d, Y') ?? 'Recent' }}</span>
                        </div>
                        <h3 class="text-base font-bold text-white group-hover:text-amber-300 transition-colors line-clamp-1">
                            <a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a>
                        </h3>
                        <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed">
                            {{ $article->summary ?: strip_tags($article->content) }}
                        </p>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-800/80 flex items-center justify-between text-xs">
                        <span class="text-slate-500">By {{ $article->author_name }}</span>
                        <a href="{{ route('news.show', $article->slug) }}" class="text-amber-400 group-hover:text-amber-300 font-semibold flex items-center gap-1">
                            <span>Read Dispatch</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-slate-900/50 border border-amber-500/20 rounded-2xl p-10 text-center text-slate-400">
                    <p class="text-sm">No chronicles have been inscribed yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

