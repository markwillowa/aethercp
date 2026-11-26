@extends('layouts.app')

@section('content')
<div class="payon-container py-10 space-y-6">
    <!-- Kingdom Notice Board Header -->
    <div class="payon-wood-frame overflow-hidden">
        <div class="payon-wood-header">
            <div class="flex items-center gap-2">
                <i data-lucide="scroll-text" class="w-4 h-4 text-[#A88950]"></i>
                <span class="font-cinzel uppercase tracking-wider text-sm">Kingdom Notice Board</span>
            </div>
            <span class="text-xs text-[#E7D8B5]">Official Village Dispatches</span>
        </div>
        <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="font-cinzel text-2xl font-bold text-[#35291F]">Server News &amp; Announcements</h1>
                <p class="text-xs text-[#80633B] mt-1">Stay informed with official game updates, maintenance alerts, and kingdom events.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @php $cats = ['all' => 'All Notices', 'news' => 'News', 'announcement' => 'Announcements', 'event' => 'Events', 'update' => 'Updates', 'maintenance' => 'Maintenance']; @endphp
                @foreach($cats as $key => $label)
                    <a href="{{ $key === 'all' ? route('news.index') : route('news.index', ['category' => $key]) }}"
                       class="px-3 py-1.5 rounded text-xs font-bold border transition-colors {{ ($category === $key || (!$category && $key === 'all')) ? 'bg-[#263D2C] text-[#FFF8E8] border-[#A88950]' : 'bg-[#F2E8CF] text-[#35291F] border-[#A88950] hover:bg-[#E7D8B5]' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($news as $article)
            <article class="payon-notice-card">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <div class="flex items-center gap-2">
                        @if($article->is_pinned)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#493322] text-[#FFF8E8]">Pinned</span>
                        @endif
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#E7D8B5] text-[#263D2C] border border-[#A88950]">
                            {{ $article->category }}
                        </span>
                    </div>
                    <span class="text-xs font-mono text-[#80633B]">{{ $article->published_at?->format('M d, Y') }}</span>
                </div>

                <h2 class="font-cinzel text-xl font-bold text-[#35291F] mb-2">
                    <a href="{{ route('news.show', $article->slug) }}" class="hover:text-[#526D3A] transition-colors">{{ $article->title }}</a>
                </h2>

                <p class="text-sm text-[#493322] leading-relaxed mb-4">{{ $article->summary }}</p>

                <div class="flex items-center justify-between text-xs text-[#80633B] pt-3 border-t border-[#A88950]/40">
                    <span class="flex items-center gap-1.5"><i data-lucide="user" class="w-3.5 h-3.5"></i> Posted by {{ $article->author_name }}</span>
                    <a href="{{ route('news.show', $article->slug) }}" class="text-[#263D2C] font-bold hover:underline flex items-center gap-1">
                        <span>Read Full Notice</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </article>
        @empty
            <div class="payon-parchment-panel p-12 text-center text-xs text-[#80633B]">
                No notices found in this category.
            </div>
        @endforelse
    </div>

    @if($news->hasPages())
        <div>{{ $news->links() }}</div>
    @endif
</div>
@endsection
