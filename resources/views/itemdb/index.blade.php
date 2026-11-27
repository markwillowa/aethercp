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
                    <option value="">All Item Categories</option>
                    <optgroup label="Main Categories (SQL)">
                        @foreach(['Equip', 'Usable', 'Etc'] as $mainCat)
                            <option value="{{ $mainCat }}" {{ $type === $mainCat ? 'selected' : '' }}>{{ $mainCat }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Specific Item Types">
                        @foreach(array_diff($itemTypes, ['Equip', 'Usable', 'Etc']) as $t)
                            <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Search Item</span>
                </button>
            </div>
        </form>

        <!-- Quick Category Pills -->
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-slate-800/80 text-xs">
            <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px] mr-1">Browse Category:</span>
            @foreach(['' => 'All', 'Equip' => 'Equip', 'Usable' => 'Usable', 'Etc' => 'Etc'] as $catVal => $catLabel)
                @php $isCatActive = (string)$type === (string)$catVal && ($catVal !== '' || $keyword !== ''); @endphp
                <a href="{{ route('itemdb.index', array_filter(['q' => $keyword, 'type' => $catVal])) }}"
                   class="px-3 py-1 rounded-lg font-semibold border transition-colors {{ $type === $catVal && $catVal !== '' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-slate-950 text-slate-300 border-slate-800 hover:border-slate-700' }}">
                    {{ $catLabel }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Search Results -->
    @if($results !== null)
        <div class="space-y-4">
            <div class="flex items-center justify-between text-xs text-slate-400 px-1">
                <span>Found <strong class="text-white">{{ $results['total'] }}</strong> matching items</span>
                <span>Page {{ $results['page'] }} of {{ $results['last_page'] }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @forelse($results['items'] as $item)
                    <div class="bg-slate-900/80 border border-slate-800 hover:border-cyan-500/40 rounded-2xl p-5 shadow-lg transition-all flex flex-col justify-between space-y-3 group">
                        <div>
                            <!-- Top Ribbon -->
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-mono text-cyan-400 font-bold">#{{ $item->id }}</span>
                                <div class="flex items-center gap-1">
                                    @if(!empty($item->slots) && $item->slots > 0)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-cyan-950/60 text-cyan-400 border border-cyan-500/30">
                                            [{{ $item->slots }} {{ Str::plural('Slot', $item->slots) }}]
                                        </span>
                                    @endif
                                    @if(!empty($item->category))
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-950/70 text-indigo-300 border border-indigo-500/30">
                                            {{ $item->category }}
                                        </span>
                                    @endif
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-slate-800 text-slate-300">
                                        {{ $item->type ?? 'Item' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Item Image Frame (similar to mob database) -->
                            <div class="w-full h-32 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center p-3 relative overflow-hidden mb-3 group-hover:border-cyan-500/30 transition-colors">
                                <img src="https://static.divine-pride.net/images/items/collection/{{ $item->id }}.png"
                                     alt="{{ $item->name_english }}"
                                     class="max-h-24 max-w-full object-contain relative z-10 transition-transform group-hover:scale-110 duration-200"
                                     onerror="this.onerror=null; this.src='https://static.divine-pride.net/images/items/item/{{ $item->id }}.png';">
                                <div class="absolute bottom-2 right-2 z-10 w-6 h-6 rounded bg-slate-900/90 border border-slate-800 flex items-center justify-center p-0.5 shadow">
                                    <img src="https://static.divine-pride.net/images/items/item/{{ $item->id }}.png" alt="" class="w-4 h-4 object-contain" onerror="this.style.display='none';">
                                </div>
                            </div>

                            <div>
                                <h3 class="font-bold text-white text-sm group-hover:text-cyan-400 transition-colors truncate">
                                    <a href="{{ route('itemdb.show', $item->id) }}">{{ $item->name_english }}</a>
                                </h3>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-1 font-mono">
                                    <span>Wt: {{ number_format(($item->weight ?? 0) / 10, 1) }}</span>
                                    @if(!empty($item->price_buy) && $item->price_buy > 0)
                                        <span>•</span>
                                        <span class="text-amber-400">{{ number_format($item->price_buy) }} Z</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-800/80">
                            <a href="{{ route('itemdb.show', $item->id) }}" class="w-full py-1.5 px-3 rounded-lg bg-slate-950 hover:bg-cyan-500 hover:text-slate-950 text-cyan-400 text-xs font-semibold border border-slate-800 hover:border-cyan-400 transition-all flex items-center justify-center gap-1">
                                <span>Inspect Details</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-slate-900/60 border border-slate-800 rounded-2xl p-10 text-center text-slate-400 space-y-2">
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
