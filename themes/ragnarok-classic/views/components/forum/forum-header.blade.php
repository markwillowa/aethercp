@props([
    'stats' => ['total_topics' => 0, 'total_replies' => 0, 'member_count' => 0],
    'search' => '',
])
<div class="ro-panel overflow-hidden">
    <div class="ro-panel-header justify-between">
        <div class="flex items-center gap-2">
            <i data-lucide="messages-square" class="w-4 h-4"></i>
            <span>{{ config('aether.server_name', 'AetherCP') }} Adventurer Forums</span>
        </div>
        <div class="flex items-center gap-3 text-[11px] font-mono text-[#FFDA89]">
            <span>{{ number_format($stats['total_topics'] ?? 0) }} Topics</span>
            <span>&bull;</span>
            <span>{{ number_format($stats['total_replies'] ?? 0) }} Replies</span>
            <span>&bull;</span>
            <span>{{ number_format($stats['member_count'] ?? 0) }} Members</span>
        </div>
    </div>

    <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-[#FCF7ED]">
        <div class="space-y-1 max-w-2xl">
            <h1 class="text-2xl font-serif font-bold text-[#2D1D13] tracking-tight">
                {{ config('aether.server_name', 'AetherCP') }} Community Tavern
            </h1>
            <p class="text-xs text-[#5C4632]">
                Discuss class builds, War of Emperium strategies, server events, and trade with fellow Rune-Midgard adventurers.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
            <form action="{{ route('forum.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text"
                       name="q"
                       value="{{ $search }}"
                       placeholder="Search discussions..."
                       class="w-full sm:w-56">
                <button type="submit" class="ro-btn-primary text-xs shrink-0">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    <span>Search</span>
                </button>
            </form>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('forum.leaderboard') }}" class="ro-btn-gold text-xs whitespace-nowrap shrink-0">
                    <i data-lucide="trophy" class="w-3.5 h-3.5"></i>
                    <span>Leaderboard</span>
                </a>
                @auth
                    <a href="{{ route('forum.topic.create') }}" class="ro-btn-primary text-xs whitespace-nowrap shrink-0">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Create Topic</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="ro-btn-primary text-xs whitespace-nowrap shrink-0">
                        <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                        <span>Sign In to Post</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
