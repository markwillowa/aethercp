@props(['board', 'identities' => []])
@php
    $lastTopic = $board->lastTopic;
    $lastAuthor = $lastTopic?->lastPostUser ?? $lastTopic?->author;
    $lastIdentity = $lastAuthor ? ($identities[$lastAuthor->id] ?? null) : null;
    $lastDisplayName = $lastIdentity['character']['name'] ?? $lastIdentity['username'] ?? $lastAuthor?->username ?? 'Member';
@endphp
<div class="group rounded-xl border border-slate-800/80 bg-slate-900/60 hover:bg-slate-900/90 hover:border-cyan-500/40 p-4 sm:p-5 transition-all">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5 min-w-0 flex-1">
            <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-500/25 flex items-center justify-center text-cyan-400 shrink-0 group-hover:scale-105 transition-transform">
                <i data-lucide="{{ $board->icon ?: 'hash' }}" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <a href="{{ route('forum.board', $board->slug) }}" class="text-base font-semibold text-white group-hover:text-cyan-400 transition-colors truncate">
                        {{ $board->name }}
                    </a>
                    @if($board->is_locked)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-800 text-slate-400 text-[10px] font-semibold">
                            <i data-lucide="lock" class="w-3 h-3"></i> Locked
                        </span>
                    @endif
                </div>
                @if($board->description)
                    <p class="text-xs sm:text-sm text-slate-400 mt-1 line-clamp-2">{{ $board->description }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-between md:justify-end gap-6 shrink-0 border-t md:border-t-0 border-slate-800/70 pt-3 md:pt-0">
            <div class="flex items-center gap-5 text-center">
                <div>
                    <div class="text-sm font-bold text-white font-mono">{{ number_format($board->topics_count) }}</div>
                    <div class="text-[11px] text-slate-500 uppercase tracking-wider">Topics</div>
                </div>
                <div>
                    <div class="text-sm font-bold text-white font-mono">{{ number_format($board->posts_count) }}</div>
                    <div class="text-[11px] text-slate-500 uppercase tracking-wider">Posts</div>
                </div>
            </div>

            <div class="w-48 lg:w-56 text-right md:text-left border-l border-slate-800/80 pl-4 min-w-0">
                @if($lastTopic)
                    <a href="{{ route('forum.topic.show', $lastTopic->slug) }}" class="block text-xs font-medium text-slate-200 hover:text-cyan-400 truncate transition-colors">
                        {{ $lastTopic->title }}
                    </a>
                    <div class="text-[11px] text-slate-500 mt-0.5 truncate">
                        by <span class="text-slate-300">{{ $lastDisplayName }}</span>
                        @if($board->last_post_at)
                            &bull; {{ $board->last_post_at->diffForHumans() }}
                        @endif
                    </div>
                @else
                    <span class="text-xs text-slate-500 italic">No discussions yet</span>
                @endif
            </div>
        </div>
    </div>
</div>
