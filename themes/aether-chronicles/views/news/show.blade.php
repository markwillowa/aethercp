@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-xl sm:text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Chronicle Record
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Published on {{ $article->published_at?->format('F d, Y') ?? $article->created_at->format('F d, Y') }}
            </p>
        </div>
        <a href="{{ route('news.index') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#FFD700]"></i>
            <span>All Chronicles</span>
        </a>
    </div>

    <!-- Article Reader Sheet -->
    <article class="chronicles-panel-ivy pt-8 max-w-4xl mx-auto">
        <div class="chronicles-ribbon-header">
            <span>{{ strtoupper($article->category ?? 'Announcement') }}</span>
        </div>

        <div class="space-y-6 mt-2">
            <div class="border-b border-[#DECBB1] pb-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#FFD700] text-[#3B1F0A] border border-[#B8860B] font-cinzel">
                        {{ $article->category }}
                    </span>
                    <span class="text-xs text-[#8C7A68]">
                        {{ $article->published_at?->format('M d, Y - h:i A') ?? $article->created_at->format('M d, Y') }}
                    </span>
                    <span class="text-xs text-[#8C7A68]">• Author: <strong class="text-[#4A2200]">{{ $article->author->username ?? 'Game Master' }}</strong></span>
                </div>

                <h1 class="font-cinzel text-2xl sm:text-3xl font-black text-[#4A2200]">
                    {{ $article->title }}
                </h1>
            </div>

            <!-- Body Content -->
            <div class="prose prose-sm max-w-none text-[#3B2315] leading-relaxed">
                {!! $article->content !!}
            </div>

            <div class="pt-6 border-t border-[#DECBB1] flex items-center justify-between">
                <a href="{{ route('news.index') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                    <span>Back to Chronicles</span>
                </a>

                <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="text-xs font-bold text-[#8B5A2B] hover:text-[#D4441D]">
                    Back to Top ↑
                </button>
            </div>
        </div>
    </article>

</div>
@endsection
