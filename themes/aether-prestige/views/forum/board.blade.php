@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#788394]">
        <a href="{{ route('forum.index') }}" class="hover:text-[#D8BE8A] transition-colors">Community</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span>{{ $board->category?->name }}</span>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-[#D9DEE7] font-semibold">{{ $board->name }}</span>
    </nav>

    <!-- News & Marketplace Style Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                {{ $board->name }}
            </h1>
            @if($board->description)
                <p class="text-xs text-[#788394] mt-1">{{ $board->description }}</p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($canCreateTopic)
                <a href="{{ route('forum.topic.create', ['board' => $board->slug]) }}" class="h-9 px-4 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider inline-flex items-center gap-1.5 shrink-0">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Create Topic</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Search & Sort Bar (Matching Cash Shop & Marketplace) -->
    <form action="{{ route('forum.board', $board->slug) }}" method="GET" class="prestige-card p-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search this board..."
               class="flex-1 h-10 px-4 rounded-xl bg-[#101725] border border-white/10 text-white text-xs focus:border-[#C6A76A] focus:outline-none">
        <select name="sort" onchange="this.form.submit()" class="h-10 px-3 rounded-xl bg-[#101725] border border-white/10 text-xs text-[#D9DEE7] focus:border-[#C6A76A] focus:outline-none">
            <option value="latest" @selected($sort === 'latest')>Latest Activity</option>
            <option value="newest" @selected($sort === 'newest')>Newest</option>
            <option value="most_replies" @selected($sort === 'most_replies')>Most Replies</option>
            <option value="most_viewed" @selected($sort === 'most_viewed')>Most Viewed</option>
        </select>
        <button type="submit" class="h-10 px-5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider cursor-pointer shrink-0">
            Search
        </button>
    </form>

    <!-- Filter Pills -->
    <div class="flex flex-wrap items-center gap-2">
        @foreach(['all' => 'All Topics', 'unanswered' => 'Unanswered', 'solved' => 'Solved', 'pinned' => 'Pinned'] as $fKey => $fLabel)
            <a href="{{ route('forum.board', ['slug' => $board->slug, 'filter' => $fKey, 'sort' => $sort, 'q' => $search]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold border transition-colors {{ $filter === $fKey ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border-[#C6A76A]/50' : 'bg-[#151B26] text-[#AAB3C1] border-white/[0.08] hover:text-white' }}">
                {{ $fLabel }}
            </a>
        @endforeach
    </div>

    <!-- Topics List -->
    <div class="space-y-3">
        @forelse($topics as $topic)
            <x-forum.topic-row :topic="$topic" :identities="$identities" />
        @empty
            <div class="prestige-card p-12 text-center space-y-2">
                <i data-lucide="message-square-dashed" class="w-10 h-10 text-[#788394]/60 mx-auto"></i>
                <h3 class="font-cinzel text-sm font-bold text-white uppercase">No Topics Found</h3>
                <p class="text-xs text-[#788394]">No discussions match your current filter in this board.</p>
            </div>
        @endforelse
    </div>

    @if($topics->hasPages())
        <div class="pt-2">{{ $topics->links() }}</div>
    @endif
</div>
@endsection
