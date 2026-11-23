@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Server News & Announcements</h1>
            <p class="text-xs text-slate-400 mt-1">Stay informed with official game updates, maintenance alerts, and events.</p>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap items-center gap-2">
            @php $cats = ['all' => 'All Articles', 'news' => 'News', 'announcement' => 'Announcements', 'event' => 'Events', 'update' => 'Updates', 'maintenance' => 'Maintenance']; @endphp
            @foreach($cats as $key => $label)
                <a href="{{ $key === 'all' ? route('news.index') : route('news.index', ['category' => $key]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ ($category === $key || (!$category && $key === 'all')) ? 'bg-cyan-500 text-slate-950 shadow-sm' : 'bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="space-y-4">
        @forelse($news as $article)
            <article class="bg-slate-900/60 border border-slate-800 hover:border-slate-700/80 rounded-2xl p-6 transition-all duration-200 backdrop-blur-sm">
                <div class="flex items-center gap-2.5 mb-2.5">
                    @if($article->is_pinned)
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                            Pinned
                        </span>
                    @endif
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-slate-800 text-slate-300">
                        {{ $article->category }}
                    </span>
                    <span class="text-xs text-slate-500">{{ $article->published_at?->format('M d, Y') }} ({{ $article->published_at?->diffForHumans() }})</span>
                </div>

                <h2 class="text-xl font-bold text-white mb-2 hover:text-cyan-400 transition-colors">
                    <a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a>
                </h2>

                <p class="text-sm text-slate-300 leading-relaxed mb-4">
                    {{ $article->summary }}
                </p>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-800/60">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                        Posted by {{ $article->author_name }}
                    </span>
                    <a href="{{ route('news.show', $article->slug) }}" class="text-cyan-400 hover:underline flex items-center gap-1 font-medium">
                        <span>Read Full Story</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </article>
        @empty
            <div class="bg-slate-900/40 border border-slate-800 rounded-2xl p-12 text-center text-slate-400">
                <p>No articles found in this category.</p>
            </div>
        @endforelse
    </div>

    <div class="pt-4">
        {{ $news->links() }}
    </div>
</div>
@endsection

