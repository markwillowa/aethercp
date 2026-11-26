@props([
    'stats' => ['total_topics' => 0, 'total_replies' => 0, 'member_count' => 0],
    'search' => '',
])
<div class="relative overflow-hidden rounded-2xl border border-slate-800/80 bg-slate-900/75 backdrop-blur-md p-6 sm:p-8 shadow-xl">
    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 via-blue-600/5 to-purple-600/10 pointer-events-none"></div>
    <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="space-y-2 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/25 text-cyan-300 text-xs font-semibold uppercase tracking-wider">
                <i data-lucide="messages-square" class="w-3.5 h-3.5"></i>
                Community Forum
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                {{ config('aether.server_name', 'AetherCP') }} Adventurer Forums
            </h1>
            <p class="text-sm text-slate-400">
                Discuss class builds, War of Emperium strategies, server events, and trade with fellow Rune-Midgard adventurers.
            </p>

            <div class="flex flex-wrap items-center gap-4 pt-2 text-xs text-slate-300">
                <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="message-circle" class="w-4 h-4 text-cyan-400"></i>
                    <strong class="text-white font-semibold">{{ number_format($stats['total_topics'] ?? 0) }}</strong> Topics
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="reply" class="w-4 h-4 text-blue-400"></i>
                    <strong class="text-white font-semibold">{{ number_format($stats['total_replies'] ?? 0) }}</strong> Replies
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <i data-lucide="users" class="w-4 h-4 text-emerald-400"></i>
                    <strong class="text-white font-semibold">{{ number_format($stats['member_count'] ?? 0) }}</strong> Members
                </span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <form action="{{ route('forum.index') }}" method="GET" class="relative">
                <span class="search-icon-centered text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text"
                       name="q"
                       value="{{ $search }}"
                       placeholder="Search discussions..."
                       class="w-full sm:w-64 pl-10 pr-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-700/80 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition-colors">
            </form>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('forum.leaderboard') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 border border-slate-700 text-sm font-semibold text-slate-200 whitespace-nowrap shrink-0 transition-colors">
                    <i data-lucide="trophy" class="w-4 h-4 text-amber-400 shrink-0"></i>
                    <span>Leaderboard</span>
                </a>
                @auth
                    <a href="{{ route('forum.topic.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-sm font-semibold text-white shadow-lg shadow-cyan-500/20 whitespace-nowrap shrink-0 transition-all">
                        <i data-lucide="plus" class="w-4 h-4 shrink-0"></i>
                        <span>Create Topic</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-sm font-semibold text-white whitespace-nowrap shrink-0">
                        <i data-lucide="log-in" class="w-4 h-4 shrink-0"></i>
                        <span>Sign In to Post</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
