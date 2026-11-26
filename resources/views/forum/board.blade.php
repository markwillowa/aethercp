@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('forum.index') }}" class="hover:text-cyan-400 transition-colors">Forum</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
        <span>{{ $board->category?->name }}</span>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
        <span class="text-slate-200 font-semibold">{{ $board->name }}</span>
    </nav>

    <!-- Board Header -->
    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/75 backdrop-blur-md p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center text-cyan-400 shrink-0">
                <i data-lucide="{{ $board->icon ?: 'hash' }}" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $board->name }}</h1>
                @if($board->description)
                    <p class="text-sm text-slate-400 mt-1">{{ $board->description }}</p>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('forum.board', $board->slug) }}" method="GET" class="relative">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <span class="search-icon-centered text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text"
                       name="q"
                       value="{{ $search }}"
                       placeholder="Search this board..."
                       class="pl-10 pr-4 py-2 rounded-xl bg-slate-950/80 border border-slate-700/80 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
            </form>

            @if($canCreateTopic)
                <a href="{{ route('forum.topic.create', ['board' => $board->slug]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-sm font-semibold text-white whitespace-nowrap shrink-0 shadow-lg shadow-cyan-500/20">
                    <i data-lucide="plus" class="w-4 h-4 shrink-0"></i>
                    <span>Create Topic</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Filters & Sorting -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            @foreach(['all' => 'All Topics', 'unanswered' => 'Unanswered', 'solved' => 'Solved', 'pinned' => 'Pinned'] as $fKey => $fLabel)
                <a href="{{ route('forum.board', ['slug' => $board->slug, 'filter' => $fKey, 'sort' => $sort, 'q' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors {{ $filter === $fKey ? 'bg-cyan-500/15 border-cyan-500/40 text-cyan-300' : 'bg-slate-900/60 border-slate-800 text-slate-400 hover:text-white' }}">
                    {{ $fLabel }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('forum.board', $board->slug) }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="hidden" name="q" value="{{ $search }}">
            <label for="board-sort" class="text-xs text-slate-400">Sort:</label>
            <select id="board-sort" name="sort" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                <option value="latest" @selected($sort === 'latest')>Latest Activity</option>
                <option value="newest" @selected($sort === 'newest')>Newest</option>
                <option value="most_replies" @selected($sort === 'most_replies')>Most Replies</option>
                <option value="most_viewed" @selected($sort === 'most_viewed')>Most Viewed</option>
            </select>
        </form>
    </div>

    <!-- Topics List -->
    <div class="space-y-3">
        @forelse($topics as $topic)
            <x-forum.topic-row :topic="$topic" :identities="$identities" />
        @empty
            <x-forum.empty-state icon="message-square-dashed" title="No Topics Found" description="No discussions match your current filter in this board." />
        @endforelse
    </div>

    @if($topics->hasPages())
        <div class="pt-2">
            {{ $topics->links() }}
        </div>
    @endif
</div>
@endsection
