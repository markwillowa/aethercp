@extends('layouts.app')

@section('title', 'Media & Screenshot Gallery')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl font-bold text-white">Media &amp; Screenshot Gallery</h1>
        <p class="text-xs text-slate-400 mt-1">Explore screenshots, guild battles, and server events from across Midgard.</p>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex items-center gap-2 flex-wrap">
        @foreach($categories as $catKey => $catLabel)
            <a href="{{ route('gallery.index', ['category' => $catKey]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ ($category === $catKey || ($catKey === 'all' && $category === 'all')) ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-slate-900 border border-slate-800 text-slate-400 hover:text-white' }}">
                {{ $catLabel }}
            </a>
        @endforeach
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($galleryItems as $item)
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden flex flex-col group">
                <div class="relative overflow-hidden aspect-video bg-black">
                    <img src="{{ $item['image'] }}" alt="{{ $item['title'] ?? 'Screenshot' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <span class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-950/80 border border-slate-800 text-cyan-400">
                        {{ $item['category'] ?? 'Media' }}
                    </span>
                </div>
                <div class="p-4 flex-1 flex flex-col justify-between space-y-2">
                    <div>
                        <h3 class="text-sm font-bold text-white">{{ $item['title'] ?? 'Rune-Midgard Scene' }}</h3>
                        <p class="text-xs text-slate-400 mt-1 line-clamp-2">{{ $item['caption'] ?? '' }}</p>
                    </div>
                    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-500">
                        <span>{{ $item['date'] ?? 'Chronicle' }}</span>
                        <a href="{{ $item['image'] }}" target="_blank" class="text-cyan-400 hover:underline">View Fullsize &raquo;</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-slate-900/60 border border-slate-800 rounded-2xl p-8 text-center text-slate-400 text-xs">
                No gallery media found for this category.
            </div>
        @endforelse
    </div>
</div>
@endsection
