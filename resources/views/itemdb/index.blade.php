@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Item Database Search</h1>
        <p class="text-xs text-slate-400 mt-1">Explore items, equipment, cards, and craftable recipes.</p>
    </div>

    <!-- Search Bar Card -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl">
        <form method="GET" action="{{ route('itemdb.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Search by item name or item ID..."
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200 placeholder-slate-500">
            </div>

            <div>
                <select name="type" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-200">
                    <option value="">All Item Types</option>
                    @foreach($itemTypes as $t)
                        <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-semibold text-sm shadow-md transition-all flex items-center justify-center gap-1.5">
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
                <span>Found <strong>{{ $results['total'] }}</strong> matching items</span>
                <span>Page {{ $results['page'] }} of {{ $results['last_page'] }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($results['items'] as $item)
                    <div class="bg-slate-900/60 border border-slate-800 hover:border-slate-700/80 rounded-2xl p-4 backdrop-blur-sm transition-all duration-150 flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0">
                            <i data-lucide="package" class="w-6 h-6 text-cyan-400"></i>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="font-bold text-white text-sm truncate hover:text-cyan-400">
                                    <a href="{{ route('itemdb.show', $item->id) }}">{{ $item->name_english }}</a>
                                </h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-800 text-slate-300">
                                    #{{ $item->id }}
                                </span>
                            </div>
                            <p class="text-xs text-cyan-400/80 font-medium mt-0.5">{{ $item->type ?? 'Item' }}</p>
                            <p class="text-xs text-slate-400 mt-2 line-clamp-2">
                                Weight: {{ $item->weight ?? 0 }} | Slots: {{ $item->slots ?? 0 }} | Price: {{ number_format($item->price_buy ?? 0) }} Z
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 bg-slate-900/40 border border-slate-800 rounded-2xl p-8 text-center text-slate-400">
                        No items matched your search query.
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
@endsection

