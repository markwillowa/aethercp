@extends('layouts.app')

@section('title', 'Maps & Dungeons Directory')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 py-2">

    <!-- Top Header with Database Switcher -->
    <div class="p-6 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="map-pin" class="w-6 h-6 text-cyan-400"></i>
                <h1 class="text-2xl font-bold text-white tracking-wide uppercase">
                    Maps &amp; Dungeons Atlas
                </h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Explore the geography of Rune-Midgard, ancient dungeon ruins, and trading hubs
            </p>
        </div>

        <!-- Database Switcher Tabs -->
        <div class="flex items-center gap-2">
            <a href="{{ route('database.items') }}" class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider bg-slate-950 border border-slate-800 text-slate-300 hover:text-cyan-400 hover:border-slate-700 transition-all flex items-center gap-1.5">
                <i data-lucide="package" class="w-3.5 h-3.5"></i>
                <span>Items</span>
            </a>
            <a href="{{ route('database.monsters') }}" class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider bg-slate-950 border border-slate-800 text-slate-300 hover:text-cyan-400 hover:border-slate-700 transition-all flex items-center gap-1.5">
                <i data-lucide="skull" class="w-3.5 h-3.5"></i>
                <span>Monsters</span>
            </a>
            <a href="{{ route('database.maps') }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 shadow-lg flex items-center gap-1.5">
                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                <span>Maps &amp; Dungeons</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <form method="GET" action="{{ route('database.maps') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative" style="position: relative; display: flex; align-items: center;">
                <div class="search-icon-centered" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); -webkit-transform: translateY(-50%); display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10; width: 16px; height: 16px; line-height: 0;">
                    <i data-lucide="search" class="w-4 h-4 text-cyan-400" style="width: 16px; height: 16px; display: block;"></i>
                </div>
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Search by map name or GAT code (e.g. Prontera, mag_dun01)..."
                       style="width: 100%; padding-left: 44px !important; display: block;"
                       class="px-4 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs sm:text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 transition-all">
            </div>

            <div>
                <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs sm:text-sm text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500 cursor-pointer">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Map Types</option>
                    <option value="town" {{ $type === 'town' ? 'selected' : '' }}>Cities &amp; Towns</option>
                    <option value="dungeon" {{ $type === 'dungeon' ? 'selected' : '' }}>Dungeons &amp; Catacombs</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="compass" class="w-4 h-4"></i>
                    <span>Search Atlas</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Maps Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse($mapList as $m)
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-lg flex flex-col justify-between space-y-4 hover:border-cyan-500/40 transition-all">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold text-cyan-400">{{ $m['code'] }}.gat</span>
                        @if($m['type'] === 'Town')
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-blue-500/10 text-blue-400 border border-blue-500/30">Town</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-rose-500/10 text-rose-400 border border-rose-500/30">Dungeon</span>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-bold text-base text-white">
                            {{ $m['name'] }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            {{ $m['desc'] }}
                        </p>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-[10px] text-slate-400">Status: <strong class="text-emerald-400">Accessible</strong></span>
                    <a href="{{ route('database.monsters', ['q' => $m['code']]) }}" class="text-xs font-semibold text-cyan-400 hover:text-cyan-300 inline-flex items-center gap-1">
                        <span>Spawns</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-slate-900/60 border border-slate-800 rounded-2xl p-12 text-center text-slate-400 space-y-2">
                <i data-lucide="map" class="w-10 h-10 text-slate-600 mx-auto"></i>
                <p class="text-sm font-bold text-white">No maps found matching your query</p>
                <p class="text-xs text-slate-400">Try searching with a different map name or keyword.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
