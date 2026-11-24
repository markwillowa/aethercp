@extends('layouts.app')

@section('title', 'Monster Database & Bestiary')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 py-2">

    <!-- Top Header with Database Switcher -->
    <div class="p-6 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="skull" class="w-6 h-6 text-cyan-400"></i>
                <h1 class="text-2xl font-bold text-white tracking-wide uppercase">
                    Monster Database &amp; Bestiary
                </h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Explore monster stats, HP, elemental affinities, MVP bosses, and drop percentages
            </p>
        </div>

        <!-- Database Switcher Tabs -->
        <div class="flex items-center gap-2">
            <a href="{{ route('database.items') }}" class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider bg-slate-950 border border-slate-800 text-slate-300 hover:text-cyan-400 hover:border-slate-700 transition-all flex items-center gap-1.5">
                <i data-lucide="package" class="w-3.5 h-3.5"></i>
                <span>Items</span>
            </a>
            <a href="{{ route('database.monsters') }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 shadow-lg flex items-center gap-1.5">
                <i data-lucide="skull" class="w-3.5 h-3.5"></i>
                <span>Monsters</span>
            </a>
            <a href="{{ route('database.maps') }}" class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider bg-slate-950 border border-slate-800 text-slate-300 hover:text-cyan-400 hover:border-slate-700 transition-all flex items-center gap-1.5">
                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                <span>Maps &amp; Dungeons</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <form method="GET" action="{{ route('database.monsters') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative" style="position: relative; display: flex; align-items: center;">
                <div class="search-icon-centered" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); -webkit-transform: translateY(-50%); display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10; width: 16px; height: 16px; line-height: 0;">
                    <i data-lucide="search" class="w-4 h-4 text-cyan-400" style="width: 16px; height: 16px; display: block;"></i>
                </div>
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Search monster by name, ID, or element (e.g. Baphomet, Holy, 1002)..."
                       style="width: 100%; padding-left: 44px !important; display: block;"
                       class="px-4 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs sm:text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 transition-all">
            </div>

            <div>
                <select name="category" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs sm:text-sm text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500 cursor-pointer">
                    <option value="all" {{ $category === 'all' ? 'selected' : '' }}>All Classifications</option>
                    <option value="mvp" {{ $category === 'mvp' ? 'selected' : '' }}>MVP / World Bosses Only</option>
                    <option value="mini" {{ $category === 'mini' ? 'selected' : '' }}>Mini-Bosses Only</option>
                    <option value="Demon" {{ $category === 'Demon' ? 'selected' : '' }}>Demon Race</option>
                    <option value="Angel" {{ $category === 'Angel' ? 'selected' : '' }}>Angel Race</option>
                    <option value="Plant" {{ $category === 'Plant' ? 'selected' : '' }}>Plant Race</option>
                    <option value="Insect" {{ $category === 'Insect' ? 'selected' : '' }}>Insect Race</option>
                    <option value="Formless" {{ $category === 'Formless' ? 'selected' : '' }}>Formless Race</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Search Bestiary</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Monster Grid Display -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse($monsters as $m)
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-lg flex flex-col justify-between space-y-4 hover:border-cyan-500/40 transition-all">
                <div>
                    <!-- Top Ribbon & Sprite -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono text-cyan-400 font-bold">#{{ $m['id'] }}</span>
                        @if(!empty($m['is_mvp']))
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30 tracking-wider">MVP BOSS</span>
                        @elseif(!empty($m['is_mini']))
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">MINI BOSS</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[9px] font-medium bg-slate-800 text-slate-300">{{ $m['race'] }}</span>
                        @endif
                    </div>

                    <!-- Monster Sprite Frame -->
                    <div class="w-full h-32 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center p-3 relative overflow-hidden mb-3">
                        <img src="https://static.divine-pride.net/images/mobs/png/{{ $m['id'] }}.png" 
                             alt="{{ $m['name'] }}" class="max-h-24 max-w-full object-contain relative z-10 transition-transform hover:scale-110 duration-200"
                             onerror="this.src='/images/item-placeholder.png'">
                    </div>

                    <div>
                        <h3 class="font-bold text-white text-sm hover:text-cyan-400 transition-colors">
                            <a href="{{ route('database.monsters.show', $m['id']) }}">{{ $m['name'] }}</a>
                        </h3>
                        <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-400">
                            <span>Lv. {{ $m['level'] }}</span>
                            <span>•</span>
                            <span class="text-emerald-400">{{ $m['element'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">HP: <strong class="text-white font-mono">{{ number_format($m['hp']) }}</strong></span>
                    <a href="{{ route('database.monsters.show', $m['id']) }}" class="text-xs font-semibold text-cyan-400 hover:text-cyan-300 flex items-center gap-1">
                        <span>Details</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-slate-900/60 border border-slate-800 rounded-2xl p-12 text-center text-slate-400 space-y-2">
                <i data-lucide="skull" class="w-10 h-10 text-slate-600 mx-auto"></i>
                <p class="text-sm font-bold text-white">No monsters matched your query</p>
                <p class="text-xs text-slate-400">Try searching with a different monster name, element, or ID.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
