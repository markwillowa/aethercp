@props(['topic', 'identities' => [], 'showBoard' => false])
@php
    $authorIdentity = $topic->author ? ($identities[$topic->author->id] ?? null) : null;
    $lastUser = $topic->lastPostUser ?? $topic->author;
    $lastIdentity = $lastUser ? ($identities[$lastUser->id] ?? null) : null;
    $lastDisplayName = $lastIdentity['character']['name'] ?? $lastIdentity['username'] ?? $lastUser?->username ?? 'Member';
@endphp
<div class="group rounded-xl border border-slate-800/80 bg-slate-900/55 hover:bg-slate-900/85 hover:border-cyan-500/35 p-4 transition-all">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="min-w-0 flex-1 space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <x-forum.topic-status-badge :topic="$topic" />
                @if($showBoard && $topic->board)
                    <a href="{{ route('forum.board', $topic->board->slug) }}" class="px-2 py-0.5 rounded-md bg-slate-800/90 border border-slate-700/80 text-[11px] font-medium text-cyan-300 hover:border-cyan-500/40 transition-colors">
                        {{ $topic->board->name }}
                    </a>
                @endif
            </div>

            <a href="{{ route('forum.topic.show', $topic->slug) }}" class="block text-base font-semibold text-white group-hover:text-cyan-400 transition-colors leading-snug">
                {{ $topic->title }}
            </a>

            @if($authorIdentity)
                <x-forum.user-identity :identity="$authorIdentity" :compact="true" />
            @endif
        </div>

        <div class="flex items-center justify-between md:justify-end gap-6 shrink-0 border-t md:border-t-0 border-slate-800/70 pt-3 md:pt-0">
            <div class="flex items-center gap-5 text-center">
                <div>
                    <div class="text-sm font-bold text-white font-mono">{{ number_format($topic->replies_count) }}</div>
                    <div class="text-[11px] text-slate-500 uppercase tracking-wider">Replies</div>
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-300 font-mono">{{ number_format($topic->views_count) }}</div>
                    <div class="text-[11px] text-slate-500 uppercase tracking-wider">Views</div>
                </div>
            </div>

            <div class="w-40 text-right border-l border-slate-800/80 pl-4">
                <div class="text-xs font-medium text-slate-300 truncate">{{ $lastDisplayName }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">
                    {{ ($topic->last_activity_at ?? $topic->created_at)?->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>
</div>
