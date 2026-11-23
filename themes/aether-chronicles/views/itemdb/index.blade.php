@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Item Database & Codex
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Lookup weapons, armors, cards, and legendary artifacts from server tables
            </p>
        </div>
        <div class="chronicles-status-pill woe text-xs py-1.5 px-3">
            <span>Official Emulator Database</span>
        </div>
    </div>

    <!-- Search Form Panel -->
    <div class="chronicles-panel-ivy pt-6">
        <form method="GET" action="{{ route('itemdb.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Search by item name or ID..."
                       class="chronicles-input">
            </div>

            <div>
                <select name="type" class="chronicles-input">
                    <option value="">All Categories</option>
                    @foreach($itemTypes as $t)
                        <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="chronicles-btn-red w-full">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Search Codex</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table Panel -->
    <div class="chronicles-panel-ivy pt-8">
        <div class="chronicles-ribbon-header">
            <span>Search Results ({{ $items->total() }})</span>
        </div>

        <div class="overflow-x-auto mt-2">
            <table class="chronicles-table">
                <thead>
                    <tr>
                        <th class="w-20">ID</th>
                        <th>Item Name</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Slots</th>
                        <th class="text-right">Weight</th>
                        <th class="text-center w-28">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="font-mono text-xs font-bold text-[#8C7A68]">#{{ $item->id }}</td>
                            <td>
                                <a href="{{ route('itemdb.show', $item->id) }}" class="font-cinzel font-bold text-[#4A2200] hover:text-[#D4441D]">
                                    {{ $item->name_japanese ?? $item->name_english }}
                                </a>
                                @if(!empty($item->name_japanese) && !empty($item->name_english) && $item->name_japanese !== $item->name_english)
                                    <span class="text-[10px] text-[#8C7A68] block">({{ $item->name_english }})</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#FAF4EA] text-[#5A3D28] border border-[#DECBB1]">
                                    {{ $item->type }}
                                </span>
                            </td>
                            <td class="text-center font-mono font-bold text-[#D4441D]">
                                {{ $item->slots ?? 0 }}
                            </td>
                            <td class="text-right font-mono text-xs text-[#5A3D28]">
                                {{ ($item->weight ?? 0) / 10 }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('itemdb.show', $item->id) }}" class="text-xs font-bold text-[#8B5A2B] hover:text-[#D4441D]">
                                    Inspect
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-[#8C7A68]">
                                No items found matching your query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-3 border-t border-[#DECBB1]">
            {{ $items->links() }}
        </div>
    </div>

</div>
@endsection
