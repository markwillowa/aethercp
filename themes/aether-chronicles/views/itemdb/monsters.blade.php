@extends('layouts.app')

@section('title', 'Monster Database & Bestiary')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <!-- Top Wood Board Header with Database Navigation Switcher -->
    <div class="chronicles-wood-board flex flex-col md:flex-row md:items-center justify-between gap-6 p-5 sm:p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="skull" class="w-6 h-6 text-[#FFD700]"></i>
                <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                    Monster Database &amp; Bestiary
                </h1>
            </div>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Explore monster stats, HP, elemental affinities, MVP bosses, and drop percentages
            </p>
        </div>

        <!-- Database Switcher Tabs -->
        <div class="flex items-center gap-2">
            <a href="{{ route('database.items') }}" class="px-3.5 py-2 rounded-lg text-xs font-cinzel font-bold uppercase tracking-wider bg-[#251F2D] border border-[#5A4A68] text-[#FFE4A0] hover:bg-[#352B42] hover:text-white transition-all flex items-center gap-1.5">
                <i data-lucide="package" class="w-3.5 h-3.5"></i>
                <span>Items</span>
            </a>
            <a href="{{ route('database.monsters') }}" class="px-3.5 py-2 rounded-lg text-xs font-cinzel font-black uppercase tracking-wider bg-gradient-to-b from-[#FFE894] to-[#8C5E09] text-[#1A1202] border border-[#FFEAA5] shadow-lg flex items-center gap-1.5">
                <i data-lucide="skull" class="w-3.5 h-3.5"></i>
                <span>Monsters</span>
            </a>
            <a href="{{ route('database.maps') }}" class="px-3.5 py-2 rounded-lg text-xs font-cinzel font-bold uppercase tracking-wider bg-[#251F2D] border border-[#5A4A68] text-[#FFE4A0] hover:bg-[#352B42] hover:text-white transition-all flex items-center gap-1.5">
                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                <span>Maps &amp; Dungeons</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="chronicles-panel p-5 shadow-lg">
        <form method="GET" action="{{ route('database.monsters') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative" style="position: relative; display: flex; align-items: center;">
                <div class="search-icon-centered" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); -webkit-transform: translateY(-50%); display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10; width: 16px; height: 16px; line-height: 0;">
                    <i data-lucide="search" class="w-4 h-4 text-[#A898B5]" style="width: 16px; height: 16px; display: block;"></i>
                </div>
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Search monster by name, ID, or element (e.g. Baphomet, Holy, 1002)..."
                       style="width: 100%; padding-left: 44px !important; height: 42px; display: block;"
                       class="bg-[#140E1B] border border-[#5A4A68] rounded-xl text-xs text-[#E8DCC9] placeholder:text-[#7A6B85] focus:outline-none focus:border-[#D4AF37]">
            </div>

            <div>
                <select name="category" onchange="this.form.submit()" style="height: 42px;" class="w-full px-3 bg-[#140E1B] border border-[#5A4A68] rounded-xl text-xs text-[#E8DCC9] focus:outline-none focus:border-[#D4AF37] cursor-pointer">
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
                <button type="submit" class="chronicles-btn-gold w-full justify-center py-2.5 h-[42px] cursor-pointer">
                    <i data-lucide="search" class="w-3.5 h-3.5 mr-1"></i>
                    <span>Search Bestiary</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Monster Grid Display -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse($monsters as $m)
            <div class="chronicles-panel p-5 flex flex-col justify-between space-y-4 hover:border-[#D4AF37]/60 transition-all shadow-lg">
                <div>
                    <!-- Top Ribbon & Sprite -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono text-[#D4AF37] font-bold">#{{ $m['id'] }}</span>
                        @if(!empty($m['is_mvp']))
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-[#E53935] text-white tracking-widest shadow">MVP BOSS</span>
                        @elseif(!empty($m['is_mini']))
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-[#D4AF37] text-black">MINI BOSS</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[9px] font-semibold bg-[#2B2135] text-[#D2C7BC] border border-[#5A4A68]">{{ $m['race'] }}</span>
                        @endif
                    </div>

                    <!-- Monster Animated Sprite Frame -->
                    <div class="w-full h-32 rounded-xl bg-[#140E1B] border border-[#3E324A] flex items-center justify-center p-3 relative overflow-hidden mb-3">
                        <div class="absolute inset-0 bg-radial from-[#3E2E4E]/30 to-transparent"></div>
                        @if(!empty($m['gif']))
                            <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/monsters/' . $m['gif']]) }}" 
                                 alt="{{ $m['name'] }}" class="max-h-24 max-w-full object-contain relative z-10 transition-transform hover:scale-110 duration-200"
                                 onerror="this.src='https://static.divine-pride.net/images/mobs/png/{{ $m['id'] }}.png'">
                        @else
                            <img src="https://static.divine-pride.net/images/mobs/png/{{ $m['id'] }}.png" alt="{{ $m['name'] }}" class="max-h-24 max-w-full object-contain relative z-10" onerror="this.src='/images/item-placeholder.png'">
                        @endif
                    </div>

                    <h3 class="font-cinzel text-base font-bold text-[#F3E5AB] truncate">
                        <a href="{{ route('database.monsters.show', $m['id']) }}" class="hover:text-[#FFD700] transition-colors">
                            {{ $m['name'] }}
                        </a>
                    </h3>

                    <!-- Stats Quick Table -->
                    <div class="mt-3 bg-[#181120] border border-[#3E324A] rounded-lg p-2.5 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-[9px] text-[#A396AD] uppercase block font-cinzel">Level</span>
                            <span class="font-mono font-bold text-[#FFD700]">Lv. {{ $m['level'] }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-[#A396AD] uppercase block font-cinzel">HP</span>
                            <span class="font-mono font-bold text-emerald-400">{{ number_format($m['hp']) }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-[#A396AD] uppercase block font-cinzel">Element</span>
                            <span class="text-[11px] text-[#E2D9EB] truncate block">{{ $m['element'] }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-[#A396AD] uppercase block font-cinzel">Size / Race</span>
                            <span class="text-[11px] text-[#E5A823] truncate block">{{ $m['size'] }} • {{ $m['race'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-[#3E324A] flex items-center justify-between">
                    <span class="text-[10px] text-[#A396AD]">Drops: <strong class="text-white">{{ count($m['drops']) }} items</strong></span>
                    <a href="{{ route('database.monsters.show', $m['id']) }}" class="text-xs font-bold font-cinzel text-[#E5A823] hover:text-[#FFD700] inline-flex items-center gap-1 transition-colors">
                        <span>Inspect Drops</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full chronicles-panel text-center py-16 p-6">
                <i data-lucide="skull" class="w-12 h-12 text-[#A88B57] mx-auto mb-3"></i>
                <h3 class="font-cinzel text-lg font-bold text-[#F3E5AB]">No Monsters Found</h3>
                <p class="text-xs text-[#A396AD] mt-1 max-w-sm mx-auto">
                    Try searching with another keyword or selecting a different classification.
                </p>
            </div>
        @endforelse
    </div>

</div>
@endsection
