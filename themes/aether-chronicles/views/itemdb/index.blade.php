@extends('layouts.app')

@section('content')
<div class="chronicles-container py-6 space-y-6">

    <!-- Header Panel -->
    <div class="chronicles-panel p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-xl sm:text-2xl font-bold text-[#F3E5AB] tracking-wide uppercase flex items-center gap-2">
                <i data-lucide="database" class="w-5 h-5 text-[#D4AF37]"></i>
                <span>Item Database &amp; Codex</span>
            </h1>
            <p class="text-xs text-[#A396AD] mt-1">
                Lookup equipment, weapons, cards, and items directly from server database tables.
            </p>
        </div>
        <div class="chronicles-online-pill text-xs">
            <span class="w-2 h-2 rounded-full bg-[#22C55E]"></span>
            <span>Official Emulator Database</span>
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
