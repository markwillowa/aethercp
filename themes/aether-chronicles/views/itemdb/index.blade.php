@extends('layouts.app')

@section('title', 'Item Database & Codex')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <!-- Top Wood Board Header with Database Navigation Switcher -->
    <div class="chronicles-wood-board flex flex-col md:flex-row md:items-center justify-between gap-6 p-5 sm:p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="database" class="w-6 h-6 text-[#FFD700]"></i>
                <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                    Item Database &amp; Codex
                </h1>
            </div>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Lookup equipment, weapons, cards, and items directly from server database tables
            </p>
        </div>

        <!-- Database Switcher Tabs -->
        <div class="flex items-center gap-2">
            <a href="{{ route('itemdb.index') }}" class="px-3.5 py-2 rounded-lg text-xs font-cinzel font-black uppercase tracking-wider bg-gradient-to-b from-[#FFE894] to-[#8C5E09] text-[#1A1202] border border-[#FFEAA5] shadow-lg flex items-center gap-1.5">
                <i data-lucide="package" class="w-3.5 h-3.5"></i>
                <span>Items</span>
            </a>
            <a href="{{ route('database.monsters') }}" class="px-3.5 py-2 rounded-lg text-xs font-cinzel font-bold uppercase tracking-wider bg-[#251F2D] border border-[#5A4A68] text-[#FFE4A0] hover:bg-[#352B42] hover:text-white transition-all flex items-center gap-1.5">
                <i data-lucide="skull" class="w-3.5 h-3.5"></i>
                <span>Monsters</span>
            </a>
            <a href="{{ route('database.maps') }}" class="px-3.5 py-2 rounded-lg text-xs font-cinzel font-bold uppercase tracking-wider bg-[#251F2D] border border-[#5A4A68] text-[#FFE4A0] hover:bg-[#352B42] hover:text-white transition-all flex items-center gap-1.5">
                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                <span>Maps &amp; Dungeons</span>
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="chronicles-panel p-4">
        <form method="GET" action="{{ route('itemdb.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Search by item name or ID (e.g. Poring Card, 501)..."
                       class="w-full px-3 py-2 bg-[#17121D] border border-[#5A4A68] rounded text-xs text-[#E8DCC9] focus:outline-none focus:border-[#D4AF37]">
            </div>

            <div>
                <select name="type" class="w-full px-3 py-2 bg-[#17121D] border border-[#5A4A68] rounded text-xs text-[#E8DCC9] focus:outline-none focus:border-[#D4AF37]">
                    <option value="">All Categories</option>
                    @foreach($itemTypes as $t)
                        <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="chronicles-btn-gold w-full justify-center py-2">
                    <i data-lucide="search" class="w-3.5 h-3.5 mr-1"></i>
                    <span>Search Codex</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Results Display -->
    @if($results !== null)
        <div class="chronicles-panel">
            <div class="chronicles-panel-header">
                <span class="chronicles-panel-title">
                    <i data-lucide="package"></i>
                    <span>Search Results ({{ number_format($results['total'] ?? 0) }})</span>
                </span>
                <span class="text-[10px] text-[#A396AD] font-mono">
                    Page {{ $results['page'] ?? 1 }} of {{ $results['last_page'] ?? 1 }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-[#4A3E56] bg-[#1F1728] text-[#C8BDAA] font-cinzel">
                            <th class="py-2.5 px-3 w-16">ID</th>
                            <th class="py-2.5 px-3">Item Name</th>
                            <th class="py-2.5 px-3 text-center">Category</th>
                            <th class="py-2.5 px-3 text-center">Slots</th>
                            <th class="py-2.5 px-3 text-right">Weight</th>
                            <th class="py-2.5 px-3 text-center w-24">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.04]">
                        @forelse($results['items'] as $item)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-2 px-3 font-mono text-[#D4AF37] font-bold">#{{ $item->id }}</td>
                                <td class="py-2 px-3">
                                    <a href="{{ route('itemdb.show', $item->id) }}" class="font-cinzel font-bold text-[#F3E5AB] hover:text-[#FFD700]">
                                        {{ $item->name_english ?? $item->name_japanese }}
                                    </a>
                                    @if(!empty($item->name_japanese) && !empty($item->name_english) && $item->name_japanese !== $item->name_english)
                                        <span class="text-[10px] text-[#A396AD] block">({{ $item->name_japanese }})</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-[#2B2135] text-[#D2C7BC] border border-[#5A4A68]">
                                        {{ $item->type ?? 'Item' }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-center font-mono font-bold text-[#E5A823]">
                                    {{ $item->slots ?? 0 }}
                                </td>
                                <td class="py-2 px-3 text-right font-mono text-[#A396AD]">
                                    {{ number_format(($item->weight ?? 0) / 10, 1) }}
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <a href="{{ route('itemdb.show', $item->id) }}" class="chronicles-btn-dark py-1 px-2.5 text-[10px]">
                                        Inspect
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-xs text-[#A396AD]">
                                    No items found matching your query criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(($results['last_page'] ?? 1) > 1)
                <div class="p-3 border-t border-[#4A3E56] flex items-center justify-between text-xs">
                    @if($results['page'] > 1)
                        <a href="{{ route('itemdb.index', array_merge(request()->query(), ['page' => $results['page'] - 1])) }}" class="chronicles-btn-dark py-1 px-3">
                            &laquo; Previous
                        </a>
                    @else
                        <span></span>
                    @endif

                    <span class="text-[#A396AD]">Page {{ $results['page'] }} of {{ $results['last_page'] }}</span>

                    @if($results['page'] < $results['last_page'])
                        <a href="{{ route('itemdb.index', array_merge(request()->query(), ['page' => $results['page'] + 1])) }}" class="chronicles-btn-dark py-1 px-3">
                            Next &raquo;
                        </a>
                    @else
                        <span></span>
                    @endif
                </div>
            @endif
        </div>
    @else
        <!-- Initial Search Prompt -->
        <div class="chronicles-panel p-10 text-center">
            <i data-lucide="search" class="w-10 h-10 text-[#5A4A68] mx-auto mb-3"></i>
            <h3 class="font-cinzel text-base font-bold text-[#F3E5AB]">Item Database Search</h3>
            <p class="text-xs text-[#A396AD] max-w-md mx-auto mt-1">
                Enter an item name or item ID in the box above to search the Ragnarok Online database.
            </p>
        </div>
    @endif

</div>
@endsection
