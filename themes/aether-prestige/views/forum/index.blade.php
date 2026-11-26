@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8 max-w-6xl mx-auto">
    <!-- FAQ-Style Centered Prestige Hero -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-[#151B26] to-[#101725] border border-[#C6A76A]/30 p-6 sm:p-10 shadow-2xl">
        <div class="relative z-10 text-center max-w-2xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#C6A76A]/10 border border-[#C6A76A]/30 text-[#D8BE8A] text-xs font-semibold tracking-wider uppercase font-cinzel">
                <i data-lucide="messages-square" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                <span>Adventurer Community &amp; Tavern</span>
            </div>

            <h1 class="font-cinzel text-2xl sm:text-4xl font-extrabold text-white tracking-wider uppercase">
                Community Discussions
            </h1>

            <p class="text-xs sm:text-sm text-[#AAB3C1] leading-relaxed">
                Share class guides, War of Emperium strategies, market offers, and server feedback with fellow Midgardians.
            </p>

            <form action="{{ route('forum.index') }}" method="GET" class="pt-2 max-w-xl mx-auto">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Search topics, guides, or discussions..."
                           class="flex-1 h-11 px-4 rounded-xl bg-[#101725] border border-white/10 text-white text-xs sm:text-sm focus:border-[#C6A76A] focus:outline-none">
                    <button type="submit" class="h-11 px-6 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider cursor-pointer shrink-0">
                        Search
                    </button>
                    <a href="{{ route('forum.leaderboard') }}" class="h-11 px-4 rounded-xl bg-[#1B2432] border border-white/10 text-[#D8BE8A] text-xs font-bold flex items-center justify-center shrink-0">
                        Leaderboard
                    </a>
                </div>
            </form>
        </div>
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-48 bg-[#C6A76A]/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Forum Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-8 space-y-6">
            @forelse($categories as $category)
                <div class="prestige-card overflow-hidden">
                    <div class="px-5 py-4 bg-[#101725]/80 border-b border-white/[0.08] flex items-center justify-between">
                        <div>
                            <h2 class="font-cinzel text-base font-bold text-white uppercase tracking-wider">{{ $category->name }}</h2>
                            @if($category->description)
                                <p class="text-xs text-[#788394] mt-0.5">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($category->activeBoards as $board)
                            <a href="{{ route('forum.board', $board->slug) }}" class="block p-4 rounded-xl bg-[#101725]/70 hover:bg-[#1B2432]/70 border border-white/[0.06] hover:border-[#C6A76A]/40 transition-colors">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="font-cinzel text-sm font-bold text-[#F4F5F7]">{{ $board->name }}</h3>
                                        @if($board->description)
                                            <p class="text-xs text-[#788394] mt-0.5">{{ $board->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right shrink-0 text-[11px] font-mono">
                                        <span class="block font-bold text-[#D8BE8A]">{{ $board->topics_count ?? 0 }} Topics</span>
                                        <span class="text-[#788394]">{{ $board->posts_count ?? 0 }} Posts</span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-xs text-[#788394] text-center py-4">No boards in this category.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="prestige-card p-10 text-center text-xs text-[#788394]">No forum categories configured yet.</div>
            @endforelse

            <div class="prestige-card p-5 space-y-3">
                <h2 class="font-cinzel text-sm font-bold uppercase tracking-wider text-white border-b border-white/[0.08] pb-3">
                    {{ !empty($search) ? 'Search Results' : 'Recent Discussions' }}
                </h2>
                <div class="divide-y divide-white/[0.05]">
                    @forelse($recentDiscussions as $topic)
                        <a href="{{ route('forum.topic.show', $topic->slug) }}" class="py-3 flex items-center justify-between gap-3 hover:bg-white/[0.02] px-2 rounded transition-colors">
                            <div class="min-w-0">
                                <p class="font-cinzel text-sm font-bold text-white truncate">{{ $topic->title }}</p>
                                <p class="text-[11px] text-[#788394]">{{ $topic->board?->name }} • {{ $topic->created_at?->diffForHumans() }}</p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded bg-[#101725] border border-[#C6A76A]/30 text-[11px] font-mono text-[#D8BE8A] shrink-0">{{ $topic->replies_count ?? 0 }} replies</span>
                        </a>
                    @empty
                        <p class="py-6 text-center text-xs text-[#788394]">No discussions posted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="lg:col-span-4 space-y-6">
            @auth
                @if($userStats)
                    <div class="prestige-card p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-white/[0.08] pb-2.5">
                            <h3 class="font-cinzel text-xs font-bold uppercase tracking-wider text-white">Your Standing</h3>
                            <a href="{{ route('forum.identity.settings') }}" class="text-xs text-[#C6A76A] hover:underline">Identity</a>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="p-2.5 rounded-xl bg-[#101725] border border-white/[0.06]"><strong class="block font-mono text-white">{{ $userStats['topics_count'] }}</strong><span class="text-[10px] text-[#788394]">Topics</span></div>
                            <div class="p-2.5 rounded-xl bg-[#101725] border border-white/[0.06]"><strong class="block font-mono text-white">{{ $userStats['replies_count'] }}</strong><span class="text-[10px] text-[#788394]">Replies</span></div>
                            <div class="p-2.5 rounded-xl bg-[#101725] border border-white/[0.06]"><strong class="block font-mono text-[#D8BE8A]">{{ $userStats['reputation_points'] }}</strong><span class="text-[10px] text-[#788394]">Rep</span></div>
                        </div>
                    </div>
                @endif
            @endauth

            <div class="prestige-card p-5 space-y-3">
                <h3 class="font-cinzel text-xs font-bold uppercase tracking-wider text-white border-b border-white/[0.08] pb-2.5">Latest Announcements</h3>
                @forelse($announcements as $ann)
                    <a href="{{ route('forum.topic.show', $ann->slug) }}" class="block p-3 rounded-xl bg-[#101725] border border-white/[0.06] hover:border-[#C6A76A]/40">
                        <p class="text-xs font-bold text-white truncate">{{ $ann->title }}</p>
                        <span class="text-[10px] text-[#788394]">{{ $ann->created_at?->diffForHumans() }}</span>
                    </a>
                @empty
                    <p class="text-xs text-[#788394]">No announcements yet.</p>
                @endforelse
            </div>

            <div class="prestige-card p-5 space-y-3">
                <h3 class="font-cinzel text-xs font-bold uppercase tracking-wider text-white border-b border-white/[0.08] pb-2.5">Trending Discussions</h3>
                @forelse($trendingDiscussions as $trend)
                    <a href="{{ route('forum.topic.show', $trend->slug) }}" class="block p-3 rounded-xl bg-[#101725] border border-white/[0.06] hover:border-[#C6A76A]/40">
                        <p class="text-xs font-bold text-white truncate">{{ $trend->title }}</p>
                        <span class="text-[10px] text-[#D8BE8A] font-mono">{{ $trend->replies_count }} replies</span>
                    </a>
                @empty
                    <p class="text-xs text-[#788394]">No trending topics yet.</p>
                @endforelse
            </div>
        </aside>
    </div>
</div>
@endsection
