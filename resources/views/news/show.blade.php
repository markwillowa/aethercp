@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <a href="{{ route('news.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-cyan-400 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to All News</span>
    </a>

    <article class="bg-slate-900/80 border border-slate-800 rounded-3xl p-8 backdrop-blur-xl shadow-2xl space-y-6">
        <div class="space-y-3 border-b border-slate-800 pb-5">
            <div class="flex items-center gap-2.5">
                <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-slate-800 text-cyan-400 border border-cyan-500/20">
                    {{ $article->category }}
                </span>
                <span class="text-xs text-slate-400">{{ $article->published_at?->format('F d, Y - h:i A') }}</span>
                <span class="text-slate-600">•</span>
                <span class="text-xs text-slate-400">By {{ $article->author_name }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                {{ $article->title }}
            </h1>
        </div>

        <div class="prose prose-invert max-w-none text-slate-200 text-sm sm:text-base leading-relaxed space-y-4">
            {!! nl2br(e($article->content)) !!}
        </div>
    </article>

    @if(count($recentNews) > 0)
        <div class="pt-6 border-t border-slate-800/80">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 mb-4">Other Recent Articles</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($recentNews as $recent)
                    <a href="{{ route('news.show', $recent->slug) }}" class="p-4 rounded-xl bg-slate-900/50 border border-slate-800 hover:border-slate-700 text-xs transition-colors block">
                        <span class="text-slate-500 block mb-1">{{ $recent->published_at?->format('M d, Y') }}</span>
                        <strong class="text-slate-200 hover:text-cyan-400 line-clamp-1">{{ $recent->title }}</strong>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

