@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
    <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 text-xs text-[#AAB3C1] hover:text-[#C6A76A] font-semibold transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to All Chronicles</span>
    </a>

    <article class="prestige-card-elevated p-8 sm:p-10 shadow-2xl space-y-6">
        <div class="space-y-3 border-b border-white/[0.08] pb-6">
            <div class="flex items-center gap-2.5">
                <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-[#101725] text-[#C6A76A] border border-[#C6A76A]/30 font-cinzel">
                    {{ $article->category }}
                </span>
                <span class="text-xs text-[#788394]">{{ $article->published_at?->format('F d, Y - h:i A') }}</span>
                <span class="text-white/20">•</span>
                <span class="text-xs text-[#AAB3C1]">Author: {{ $article->author_name }}</span>
            </div>
            <h1 class="font-cinzel text-3xl sm:text-4xl font-extrabold text-white tracking-wider leading-tight">
                {{ $article->title }}
            </h1>
        </div>

        <div class="text-[#D9DEE7] text-sm sm:text-base leading-relaxed space-y-4">
            {!! nl2br(e($article->content)) !!}
        </div>
    </article>

    @if(count($recentNews) > 0)
        <div class="pt-6 border-t border-white/[0.08]">
            <h3 class="font-cinzel text-xs font-bold uppercase tracking-wider text-white mb-4">Other Recent Chronicles</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($recentNews as $recent)
                    <a href="{{ route('news.show', $recent->slug) }}" class="p-4 rounded-xl prestige-card text-xs block hover:border-[#C6A76A]/40 transition-colors">
                        <span class="text-[#788394] block mb-1 text-[10px]">{{ $recent->published_at?->format('M d, Y') }}</span>
                        <strong class="text-white font-cinzel font-bold hover:text-[#C6A76A] line-clamp-1">{{ $recent->title }}</strong>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

