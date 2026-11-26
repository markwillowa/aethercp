@props([
    'stats' => ['total_topics' => 0, 'total_replies' => 0, 'member_count' => 0],
    'search' => '',
])
<div class="payon-wood-frame overflow-hidden">
    <div class="payon-wood-header">
        <div class="flex items-center gap-2">
            <i data-lucide="messages-square" class="w-4 h-4 text-[#A88950]"></i>
            <span class="font-cinzel uppercase tracking-wider text-sm">{{ config('aether.server_name', 'AetherCP') }} Village Pavilion</span>
        </div>
        <div class="flex items-center gap-3 text-[11px] font-mono text-[#E7D8B5]">
            <span>{{ number_format($stats['total_topics'] ?? 0) }} Topics</span>
            <span>&bull;</span>
            <span>{{ number_format($stats['total_replies'] ?? 0) }} Replies</span>
            <span>&bull;</span>
            <span>{{ number_format($stats['member_count'] ?? 0) }} Members</span>
        </div>
    </div>

    <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1 max-w-2xl">
            <h1 class="font-cinzel text-2xl font-bold text-[#35291F]">{{ config('aether.server_name', 'AetherCP') }} Adventurer Forums</h1>
            <p class="text-xs text-[#80633B]">Discuss class builds, War of Emperium strategies, server events, and trade with fellow adventurers.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
            <form action="{{ route('forum.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $search }}" placeholder="Search discussions..." class="w-full sm:w-52 px-3 py-2 rounded text-xs">
                <button type="submit" class="payon-btn-wood text-xs py-2 px-3.5 shrink-0">Search</button>
            </form>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('forum.leaderboard') }}" class="payon-btn-gold text-xs py-2 px-3.5 shrink-0">
                    <i data-lucide="trophy" class="w-3.5 h-3.5"></i>
                    <span>Leaderboard</span>
                </a>
                @auth
                    <a href="{{ route('forum.topic.create') }}" class="payon-btn-forest text-xs py-2 px-3.5 shrink-0">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Create Topic</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="payon-btn-forest text-xs py-2 px-3.5 shrink-0">Sign In</a>
                @endauth
            </div>
        </div>
    </div>
</div>
