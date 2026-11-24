@extends('layouts.app')

@section('title', 'Maps & Dungeons Directory')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <!-- Top Wood Board Header with Database Navigation Switcher -->
    <div class="chronicles-wood-board flex flex-col md:flex-row md:items-center justify-between gap-6 p-5 sm:p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="map-pin" class="w-6 h-6 text-[#FFD700]"></i>
                <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                    Maps &amp; Dungeons Atlas
                </h1>
            </div>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Explore the geography of Rune-Midgard, ancient dungeon ruins, and trading hubs
            </p>
        </div>

        <!-- Database Switcher Tabs -->
        <div class="flex items-center gap-2">
            <a href="{{ route('database.items') }}" class="px-3.5 py-2 rounded-lg text-xs font-cinzel font-bold uppercase tracking-wider bg-[#251F2D] border border-[#5A4A68] text-[#FFE4A0] hover:bg-[#352B42] hover:text-white transition-all flex items-center gap-1.5">
                <i data-lucide="package" class="w-3.5 h-3.5"></i>
                <span>Items</span>
            </a>
            <a href="{{ route('database.monsters') }}" class="px-3.5 py-2 rounded-lg text-xs font-cinzel font-bold uppercase tracking-wider bg-[#251F2D] border border-[#5A4A68] text-[#FFE4A0] hover:bg-[#352B42] hover:text-white transition-all flex items-center gap-1.5">
                <i data-lucide="skull" class="w-3.5 h-3.5"></i>
                <span>Monsters</span>
            </a>
            <a href="{{ route('database.maps') }}" class="px-3.5 py-2 rounded-lg text-xs font-cinzel font-black uppercase tracking-wider bg-gradient-to-b from-[#FFE894] to-[#8C5E09] text-[#1A1202] border border-[#FFEAA5] shadow-lg flex items-center gap-1.5">
                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                <span>Maps &amp; Dungeons</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="chronicles-panel p-5 shadow-lg">
        <form method="GET" action="{{ route('database.maps') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative" style="position: relative; display: flex; align-items: center;">
                <div class="search-icon-centered" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); -webkit-transform: translateY(-50%); display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10; width: 16px; height: 16px; line-height: 0;">
                    <i data-lucide="search" class="w-4 h-4 text-[#A898B5]" style="width: 16px; height: 16px; display: block;"></i>
                </div>
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Search by map name or GAT code (e.g. Prontera, mag_dun01)..."
                       style="width: 100%; padding-left: 44px !important; height: 42px; display: block;"
                       class="bg-[#140E1B] border border-[#5A4A68] rounded-xl text-xs text-[#E8DCC9] placeholder:text-[#7A6B85] focus:outline-none focus:border-[#D4AF37]">
            </div>

            <div>
                <select name="type" onchange="this.form.submit()" style="height: 42px;" class="w-full px-3 bg-[#140E1B] border border-[#5A4A68] rounded-xl text-xs text-[#E8DCC9] focus:outline-none focus:border-[#D4AF37] cursor-pointer">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Map Types</option>
                    <option value="town" {{ $type === 'town' ? 'selected' : '' }}>Cities &amp; Towns</option>
                    <option value="dungeon" {{ $type === 'dungeon' ? 'selected' : '' }}>Dungeons &amp; Catacombs</option>
                </select>
            </div>

            <div>
                <button type="submit" class="chronicles-btn-gold w-full justify-center py-2.5 h-[42px] cursor-pointer">
                    <i data-lucide="compass" class="w-3.5 h-3.5 mr-1"></i>
                    <span>Search Atlas</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Maps Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse($mapList as $m)
            <div class="chronicles-panel p-5 flex flex-col justify-between space-y-4 hover:border-[#D4AF37]/60 transition-all shadow-lg">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold text-[#D4AF37]">{{ $m['code'] }}.gat</span>
                        @if($m['type'] === 'Town')
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-blue-500/20 text-blue-300 border border-blue-500/30">Town</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30">Dungeon</span>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-cinzel text-base font-bold text-[#F3E5AB]">
                            {{ $m['name'] }}
                        </h3>
                        <p class="text-xs text-[#A396AD] mt-1 leading-relaxed">
                            {{ $m['desc'] }}
                        </p>
                    </div>
                </div>

                <div class="pt-3 border-t border-[#3E324A] flex items-center justify-between">
                    <span class="text-[10px] text-[#A396AD]">Status: <strong class="text-emerald-400">Accessible</strong></span>
                    <a href="{{ route('database.monsters', ['q' => $m['code']]) }}" class="text-xs font-bold font-cinzel text-[#E5A823] hover:text-[#FFD700] inline-flex items-center gap-1">
                        <span>Spawns</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full chronicles-panel text-center py-16 p-6">
                <i data-lucide="map-pin" class="w-12 h-12 text-[#A88B57] mx-auto mb-3"></i>
                <h3 class="font-cinzel text-lg font-bold text-[#F3E5AB]">No Maps Found</h3>
                <p class="text-xs text-[#A396AD] mt-1 max-w-sm mx-auto">
                    Try searching with another keyword or selecting a different map type.
                </p>
            </div>
        @endforelse
    </div>

</div>
@endsection
