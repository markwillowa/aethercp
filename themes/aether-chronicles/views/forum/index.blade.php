@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">
    <!-- Wood Board Header (Matches Donate Layout) -->
    <div class="chronicles-wood-board flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Adventurer Tavern &amp; Community
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Discuss builds, WoE strategies, trade offers, and server chronicles with fellow Midgardians
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <form method="GET" action="{{ route('forum.index') }}" class="flex items-center gap-1.5">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Search discussions..."
                       class="px-3 py-1.5 rounded-lg bg-[#24160A] border border-[#8B5A2B] text-xs text-[#FFE4A0] placeholder:text-[#A396AD]">
                <button type="submit" class="chronicles-btn-wood text-xs py-1.5 px-3 w-auto cursor-pointer">Search</button>
            </form>
            <a href="{{ route('forum.leaderboard') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
                <i data-lucide="award" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                <span>Reputation</span>
            </a>
        </div>
    </div>

    <!-- 2-Column Donate-Style Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Main Column (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            @forelse($categories as $category)
                <div class="chronicles-panel-ivy space-y-4">
                    <div class="border-b border-[#DECBB1] pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="font-cinzel text-base font-black text-[#4A2200] uppercase">{{ $category->name }}</h2>
                            @if($category->description)
                                <p class="text-xs text-[#795E43] mt-0.5">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($category->activeBoards as $board)
                            <a href="{{ route('forum.board', $board->slug) }}" class="block p-3.5 rounded-xl bg-[#FAF4EA] hover:bg-[#EFE4D0] border border-[#DECBB1] transition-colors">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="font-cinzel text-sm font-bold text-[#4A2200]">{{ $board->name }}</h3>
                                        @if($board->description)
                                            <p class="text-xs text-[#795E43] mt-0.5">{{ $board->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right shrink-0 text-[11px] font-mono text-[#9E5600]">
                                        <span class="block font-bold">{{ $board->topics_count ?? 0 }} Topics</span>
                                        <span class="text-[#8C7A68]">{{ $board->posts_count ?? 0 }} Posts</span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-xs text-[#8C7A68] text-center py-3">No boards available in this category.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="chronicles-panel-ivy text-center py-10 text-xs text-[#8C7A68]">No forum categories configured yet.</div>
            @endforelse

            <!-- Recent Discussions -->
            <div class="chronicles-panel-ivy space-y-4">
                <h2 class="font-cinzel text-base font-black text-[#4A2200] uppercase border-b border-[#DECBB1] pb-2">
                    {{ !empty($search) ? 'Search Results' : 'Recent Discussions' }}
                </h2>
                <div class="divide-y divide-[#DECBB1]">
                    @forelse($recentDiscussions as $topic)
                        <a href="{{ route('forum.topic.show', $topic->slug) }}" class="py-3 flex items-center justify-between gap-3 hover:bg-[#FAF4EA]/60 px-2 rounded transition-colors">
                            <div class="min-w-0">
                                <p class="font-cinzel text-sm font-bold text-[#4A2200] truncate">{{ $topic->title }}</p>
                                <p class="text-[11px] text-[#795E43]">
                                    {{ $topic->board?->name }} • {{ $topic->created_at?->diffForHumans() }}
                                </p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded bg-[#EFE4D0] border border-[#DECBB1] text-[11px] font-mono font-bold text-[#9E5600] shrink-0">
                                {{ $topic->replies_count ?? 0 }} replies
                            </span>
                        </a>
                    @empty
                        <p class="py-6 text-center text-xs text-[#8C7A68]">No discussions posted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Sidebar (4 cols) -->
        <aside class="lg:col-span-4 space-y-6">
            @auth
                @if($userStats)
                    <div class="chronicles-panel-ivy pt-8 space-y-3">
                        <div class="chronicles-ribbon-header"><span>Your Standing</span></div>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="p-2.5 rounded-xl bg-[#FAF4EA] border border-[#DECBB1]">
                                <strong class="block font-mono text-base text-[#4A2200]">{{ $userStats['topics_count'] }}</strong>
                                <span class="text-[10px] text-[#8C7A68] uppercase">Topics</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-[#FAF4EA] border border-[#DECBB1]">
                                <strong class="block font-mono text-base text-[#4A2200]">{{ $userStats['replies_count'] }}</strong>
                                <span class="text-[10px] text-[#8C7A68] uppercase">Replies</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-[#FAF4EA] border border-[#DECBB1]">
                                <strong class="block font-mono text-base text-[#D4441D]">{{ $userStats['reputation_points'] }}</strong>
                                <span class="text-[10px] text-[#8C7A68] uppercase">Rep</span>
                            </div>
                        </div>
                        <a href="{{ route('forum.identity.settings') }}" class="chronicles-btn-wood text-xs py-2 w-full">Identity Settings</a>
                    </div>
                @endif
            @endauth

            <div class="chronicles-panel-ivy space-y-3">
                <h3 class="font-cinzel text-sm font-bold text-[#4A2200] uppercase border-b border-[#DECBB1] pb-2">Latest Announcements</h3>
                @forelse($announcements as $ann)
                    <a href="{{ route('forum.topic.show', $ann->slug) }}" class="block p-2.5 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] hover:border-[#9E5600]">
                        <p class="text-xs font-bold text-[#4A2200] truncate">{{ $ann->title }}</p>
                        <span class="text-[10px] text-[#8C7A68]">{{ $ann->created_at?->diffForHumans() }}</span>
                    </a>
                @empty
                    <p class="text-xs text-[#8C7A68]">No announcements yet.</p>
                @endforelse
            </div>

            <div class="chronicles-panel-ivy space-y-3">
                <h3 class="font-cinzel text-sm font-bold text-[#4A2200] uppercase border-b border-[#DECBB1] pb-2">Trending Discussions</h3>
                @forelse($trendingDiscussions as $trend)
                    <a href="{{ route('forum.topic.show', $trend->slug) }}" class="block p-2.5 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] hover:border-[#9E5600]">
                        <p class="text-xs font-bold text-[#4A2200] truncate">{{ $trend->title }}</p>
                        <span class="text-[10px] text-[#9E5600] font-mono">{{ $trend->replies_count }} replies</span>
                    </a>
                @empty
                    <p class="text-xs text-[#8C7A68]">No trending topics yet.</p>
                @endforelse
            </div>
        </aside>
    </div>
</div>
@endsection
