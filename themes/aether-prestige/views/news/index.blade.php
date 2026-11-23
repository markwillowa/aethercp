@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Chronicles & Announcements
            </h1>
            <p class="text-xs text-[#788394] mt-1">Official patch notes, scheduled maintenance, and community updates.</p>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap items-center gap-2">
            @php $cats = ['all' => 'All Chronicles', 'news' => 'News', 'announcement' => 'Announcements', 'event' => 'Events', 'update' => 'Patch Notes', 'maintenance' => 'Maintenance']; @endphp
            @foreach($cats as $key => $label)
                <a href="{{ $key === 'all' ? route('news.index') : route('news.index', ['category' => $key]) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ ($category === $key || (!$category && $key === 'all')) ? 'bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] shadow-md font-cinzel' : 'bg-[#151B26] border border-white/[0.08] text-[#AAB3C1] hover:text-white hover:border-[#C6A76A]/40' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="space-y-4">
        @forelse($news as $article)
            <article class="prestige-card p-6 flex flex-col sm:flex-row gap-6 items-start">
                <div class="w-full sm:w-28 sm:h-24 rounded-xl bg-[#101725] border border-white/[0.08] flex sm:flex-col items-center justify-center p-3 text-center shrink-0">
                    <span class="font-cinzel text-2xl font-extrabold text-[#C6A76A]">{{ $article->published_at?->format('d') ?? $article->created_at->format('d') }}</span>
                    <span class="text-[10px] uppercase tracking-wider text-[#788394]">{{ $article->published_at?->format('M Y') ?? $article->created_at->format('M Y') }}</span>
                </div>

                <div class="flex-grow space-y-2">
                    <div class="flex items-center gap-2.5">
                        @if($article->is_pinned)
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/40 font-cinzel">
                                Pinned
                            </span>
                        @endif
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-[#101725] text-[#AAB3C1] border border-white/[0.06]">
                            {{ $article->category }}
                        </span>
                        <span class="text-xs text-[#788394]">{{ $article->published_at?->diffForHumans() }}</span>
                    </div>

                    <h2 class="font-cinzel text-xl font-bold text-white hover:text-[#C6A76A] transition-colors">
                        <a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a>
                    </h2>

                    <p class="text-xs text-[#AAB3C1] leading-relaxed line-clamp-2">
                        {{ $article->summary }}
                    </p>

                    <div class="flex items-center justify-between text-xs text-[#788394] pt-3 border-t border-white/[0.04]">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                            <span>Author: {{ $article->author_name }}</span>
                        </span>
                        <a href="{{ route('news.show', $article->slug) }}" class="text-xs font-bold text-[#C6A76A] hover:underline flex items-center gap-1">
                            <span>Read Article</span>
                            <i data-lucide="chevron-right" class="w-3 h-3"></i>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="prestige-card p-12 text-center text-xs text-[#788394]">
                No chronicles currently published in this category.
            </div>
        @endforelse
    </div>
</div>
@endsection

