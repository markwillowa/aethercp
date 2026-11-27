@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Item Database & Codex
            </h1>
            <p class="text-xs text-[#788394] mt-1">Search weapons, armor, cards, and mythical consumables integrated with emulator tables.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#101725] text-[#C6A76A] border border-[#C6A76A]/30">
                Official Emulator DB
            </span>
        </div>
    </div>

    <!-- Search Bar Card -->
    <div class="prestige-card-elevated p-6 shadow-2xl">
        <form method="GET" action="{{ route('itemdb.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
                <div class="relative">
                    <input type="text" name="q" value="{{ $keyword }}" placeholder="Search by item name or ID..."
                           class="prestige-input pl-10">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#788394]">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <div>
                <select name="type" class="prestige-input">
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
                <button type="submit" class="prestige-btn-primary w-full py-2.5">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Search Item Codex</span>
                </button>
            </div>
        </form>

        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-white/[0.06] text-xs">
            <span class="text-[#788394] font-semibold uppercase tracking-wider text-[10px] mr-1">SQL Categories:</span>
            @foreach(['Equip', 'Usable', 'Etc'] as $mainCat)
                <a href="{{ route('itemdb.index', array_filter(['q' => $keyword, 'type' => $mainCat])) }}"
                   class="px-3 py-1 rounded-lg font-semibold border transition-colors {{ $type === $mainCat ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border-[#C6A76A]/50' : 'bg-[#101725] text-[#AAB3C1] border-white/10 hover:border-[#C6A76A]/40' }}">
                    {{ $mainCat }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Search Results -->
    @if($results !== null)
        <div class="space-y-4">
            <div class="flex items-center justify-between text-xs text-[#788394] px-1">
                <span>Discovered <strong>{{ $results['total'] }}</strong> matching artifacts</span>
                <span>Page {{ $results['page'] }} of {{ $results['last_page'] }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($results['items'] as $item)
                    <div class="prestige-card p-4 flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#101725] border border-white/10 flex items-center justify-center shrink-0">
                            <i data-lucide="package" class="w-6 h-6 text-[#C6A76A]"></i>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="font-cinzel text-sm font-bold text-white truncate hover:text-[#C6A76A] transition-colors">
                                    <a href="{{ route('itemdb.show', $item->id) }}">{{ $item->name_english }}</a>
                                </h3>
                                <span class="font-mono text-xs font-bold text-[#C6A76A]">#{{ $item->id }}</span>
                            </div>

                            <p class="text-xs text-[#788394] mt-0.5 truncate">{{ $item->name_aegis ?? $item->type ?? 'Item' }}</p>

                            <div class="flex items-center gap-4 text-[11px] text-[#AAB3C1] mt-2 pt-2 border-t border-white/[0.04]">
                                @if(!empty($item->category))
                                    <span>Category: <strong class="text-[#C6A76A]">{{ $item->category }}</strong></span>
                                @endif
                                <span>Type: <strong class="text-white">{{ $item->type ?? 'Normal' }}</strong></span>
                                <span>Slots: <strong class="text-[#D8BE8A]">{{ $item->slots ?? 0 }}</strong></span>
                                <span>Weight: <strong class="text-white">{{ $item->weight ?? 0 }}</strong></span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full prestige-card p-12 text-center text-xs text-[#788394]">
                        No items matched your query criteria.
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
@endsection

