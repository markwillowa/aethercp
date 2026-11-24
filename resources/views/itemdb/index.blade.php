@extends('layouts.app')

@section('title', 'Item Database & Equipment Codex')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 py-2">

    <!-- Top Header with Database Switcher -->
    <div class="p-6 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="package" class="w-6 h-6 text-cyan-400"></i>
                <h1 class="text-2xl font-bold text-white tracking-wide uppercase">
                    Item Database Codex
                </h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Explore items, weapons, slotted equipment, cards, and craftable recipes
            </p>
        </div>

        <!-- Database Switcher Tabs -->
        <div class="flex items-center gap-2">
            <a href="{{ route('database.items') }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 shadow-lg flex items-center gap-1.5">
                <i data-lucide="package" class="w-3.5 h-3.5"></i>
                <span>Items</span>
            </a>
            <a href="{{ route('database.monsters') }}" class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider bg-slate-950 border border-slate-800 text-slate-300 hover:text-cyan-400 hover:border-slate-700 transition-all flex items-center gap-1.5">
                <i data-lucide="skull" class="w-3.5 h-3.5"></i>
                <span>Monsters</span>
            </a>
            <a href="{{ route('database.maps') }}" class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider bg-slate-950 border border-slate-800 text-slate-300 hover:text-cyan-400 hover:border-slate-700 transition-all flex items-center gap-1.5">
                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                <span>Maps &amp; Dungeons</span>
            </a>
        </div>
    </div>

    <!-- Search Bar Card -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <form method="GET" action="{{ route('itemdb.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative" style="position: relative; display: flex; align-items: center;">
                <div class="search-icon-centered" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); -webkit-transform: translateY(-50%); display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10; width: 16px; height: 16px; line-height: 0;">
                    <i data-lucide="search" class="w-4 h-4 text-cyan-400" style="width: 16px; height: 16px; display: block;"></i>
                </div>
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Search by item name or item ID..."
                       style="width: 100%; padding-left: 44px !important; display: block;"
                       class="px-4 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs sm:text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 transition-all">
            </div>

            <div>
                <select name="type" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-xs sm:text-sm text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500 cursor-pointer">
                    <option value="">All Item Types</option>
                    @foreach($itemTypes as $t)
                        <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Search Item</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Search Results -->
    @if($results !== null)
        <div class="space-y-4">
            <div class="flex items-center justify-between text-xs text-slate-400 px-1">
                <span>Found <strong class="text-white">{{ $results['total'] }}</strong> matching items</span>
                <span>Page {{ $results['page'] }} of {{ $results['last_page'] }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($results['items'] as $item)
                    <div class="bg-slate-900/80 border border-slate-800 hover:border-cyan-500/40 rounded-2xl p-5 shadow-lg transition-all flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0">
                            <i data-lucide="package" class="w-6 h-6 text-cyan-400"></i>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="font-bold text-white text-sm truncate hover:text-cyan-400 transition-colors">
                                    <a href="{{ route('itemdb.show', $item->id) }}">{{ $item->name_english }}</a>
                                </h3>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if(!empty($item->slots) && $item->slots > 0)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-cyan-950/60 text-cyan-400 border border-cyan-500/30">
                                            [{{ $item->slots }}]
                                        </span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-800 text-slate-300">
                                        #{{ $item->id }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-xs text-cyan-400/90 font-medium mt-0.5">{{ $item->type ?? 'Item' }}</p>
                            <p class="text-xs text-slate-400 mt-2 line-clamp-2">
                                Weight: {{ $item->weight ?? 0 }} | Slots: {{ $item->slots ?? 0 }} | Price: {{ number_format($item->price_buy ?? 0) }} Z
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 bg-slate-900/60 border border-slate-800 rounded-2xl p-10 text-center text-slate-400 space-y-2">
                        <i data-lucide="search-x" class="w-10 h-10 text-slate-600 mx-auto"></i>
                        <p class="text-sm font-bold text-white">No items matched your search query</p>
                        <p class="text-xs text-slate-400">Try searching with a broader keyword or different category filter.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
@endsection
