@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Profile Header -->
    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/75 backdrop-blur-md p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <x-forum.user-identity :identity="$identity" :show-points="true" />
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-950/70 border border-slate-800">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-cyan-400"></i>
                        Role: <strong class="text-slate-200 uppercase">{{ $member->role }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-950/70 border border-slate-800">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-400"></i>
                        Joined {{ $member->created_at?->format('M Y') }}
                    </span>
                    @if(!empty($identity['character']))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-950/70 border border-slate-800">
                            <i data-lucide="sword" class="w-3.5 h-3.5 text-amber-400"></i>
                            Character: <strong class="text-slate-200">{{ $identity['character']['name'] }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0">
                <div class="rounded-xl bg-slate-950/70 border border-slate-800/80 p-3 text-center">
                    <div class="text-lg font-bold text-amber-400 font-mono">{{ number_format($stats['reputation_points']) }}</div>
                    <div class="text-[10px] text-slate-500 uppercase tracking-wider">Reputation</div>
                </div>
                <div class="rounded-xl bg-slate-950/70 border border-slate-800/80 p-3 text-center">
                    <div class="text-lg font-bold text-white font-mono">{{ number_format($stats['topics_count']) }}</div>
                    <div class="text-[10px] text-slate-500 uppercase tracking-wider">Topics</div>
                </div>
                <div class="rounded-xl bg-slate-950/70 border border-slate-800/80 p-3 text-center">
                    <div class="text-lg font-bold text-white font-mono">{{ number_format($stats['replies_count']) }}</div>
                    <div class="text-[10px] text-slate-500 uppercase tracking-wider">Replies</div>
                </div>
                <div class="rounded-xl bg-slate-950/70 border border-slate-800/80 p-3 text-center">
                    <div class="text-lg font-bold text-cyan-400 font-mono">{{ number_format($stats['reactions_received']) }}</div>
                    <div class="text-[10px] text-slate-500 uppercase tracking-wider">Reactions</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
        @foreach([
            'overview' => ['label' => 'Overview', 'icon' => 'layout-dashboard'],
            'discussions' => ['label' => 'Discussions', 'icon' => 'message-square'],
            'replies' => ['label' => 'Replies', 'icon' => 'reply'],
            'reputation' => ['label' => 'Reputation History', 'icon' => 'award'],
            'achievements' => ['label' => 'Achievements', 'icon' => 'trophy'],
        ] as $tKey => $tMeta)
            <a href="{{ route('forum.profile.show', ['username' => $member->username, 'tab' => $tKey]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold border transition-colors {{ $tab === $tKey ? 'bg-cyan-500/15 border-cyan-500/40 text-cyan-300' : 'bg-slate-900/60 border-slate-800 text-slate-400 hover:text-white' }}">
                <i data-lucide="{{ $tMeta['icon'] }}" class="w-3.5 h-3.5"></i>
                <span>{{ $tMeta['label'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Tab Panels -->
    @if($tab === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-300">Reputation Progression</h2>
                <div class="flex items-center justify-between">
                    <x-forum.reputation-badge :level="$identity['reputation_level']" :points="$stats['reputation_points']" :show-points="true" />
                    <span class="text-xs text-slate-400 font-mono">{{ number_format($stats['reputation_points']) }} pts</span>
                </div>
                <div class="space-y-2 pt-2">
                    @foreach($levels as $lvl)
                        <div class="flex items-center justify-between text-xs px-3 py-2 rounded-lg {{ $stats['reputation_points'] >= $lvl->min_points ? 'bg-slate-950/80 text-slate-200 border border-slate-800' : 'text-slate-500' }}">
                            <span class="font-medium">{{ $lvl->name }}</span>
                            <span class="font-mono">{{ number_format($lvl->min_points) }}+ pts</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-300">Recent Discussions</h2>
                @forelse($discussions->take(5) as $topic)
                    <a href="{{ route('forum.topic.show', $topic->slug) }}" class="block p-3 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-cyan-500/40 transition-colors">
                        <div class="text-sm font-semibold text-white truncate">{{ $topic->title }}</div>
                        <div class="text-[11px] text-slate-500 mt-1">{{ $topic->board?->name }} &bull; {{ $topic->created_at?->diffForHumans() }}</div>
                    </a>
                @empty
                    <p class="text-xs text-slate-500">No discussions started yet.</p>
                @endforelse
            </div>
        </div>
    @elseif($tab === 'discussions')
        <div class="space-y-3">
            @forelse($discussions as $topic)
                <a href="{{ route('forum.topic.show', $topic->slug) }}" class="block rounded-xl border border-slate-800/80 bg-slate-900/60 hover:border-cyan-500/40 p-4 transition-colors">
                    <div class="flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <div class="text-base font-semibold text-white truncate">{{ $topic->title }}</div>
                            <div class="text-xs text-slate-400 mt-1">{{ $topic->board?->name }} &bull; {{ $topic->created_at?->diffForHumans() }}</div>
                        </div>
                        <div class="text-xs font-mono text-cyan-400 shrink-0">{{ $topic->replies_count }} replies</div>
                    </div>
                </a>
            @empty
                <x-forum.empty-state icon="message-square" title="No Discussions" description="This member has not started any topics yet." />
            @endforelse
            {{ $discussions->links() }}
        </div>
    @elseif($tab === 'replies')
        <div class="space-y-3">
            @forelse($replies as $reply)
                <a href="{{ route('forum.topic.show', $reply->topic->slug) }}#post-{{ $reply->id }}" class="block rounded-xl border border-slate-800/80 bg-slate-900/60 hover:border-cyan-500/40 p-4 transition-colors">
                    <div class="text-xs text-cyan-400 font-semibold">Replied in: {{ $reply->topic?->title }}</div>
                    <p class="text-sm text-slate-300 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($reply->content, 180) }}</p>
                    <div class="text-[11px] text-slate-500 mt-2">{{ $reply->created_at?->diffForHumans() }} &bull; {{ $reply->likes_count }} likes</div>
                </a>
            @empty
                <x-forum.empty-state icon="reply" title="No Replies" description="This member has not posted any replies yet." />
            @endforelse
            {{ $replies->links() }}
        </div>
    @elseif($tab === 'reputation')
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 overflow-hidden">
            @if($reputationHistory->isEmpty())
                <div class="p-8">
                    <x-forum.empty-state icon="award" title="No Reputation History" description="No reputation transactions recorded yet." />
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/70 text-slate-400 uppercase tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Event</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Source</th>
                                <th class="px-4 py-3 text-right">Points</th>
                                <th class="px-4 py-3 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/70">
                            @foreach($reputationHistory as $tx)
                                <tr class="hover:bg-slate-900/80">
                                    <td class="px-4 py-3 font-medium text-slate-200">{{ str_replace('_', ' ', ucfirst($tx->event_type)) }}</td>
                                    <td class="px-4 py-3 text-slate-300">{{ $tx->description }}</td>
                                    <td class="px-4 py-3 text-slate-400">{{ $tx->source?->username ?? 'System' }}</td>
                                    <td class="px-4 py-3 text-right font-mono font-bold {{ $tx->points >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                        {{ $tx->points >= 0 ? '+' : '' }}{{ $tx->points }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-500">{{ $tx->created_at?->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-800">{{ $reputationHistory->links() }}</div>
            @endif
        </div>
    @elseif($tab === 'achievements')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($achievements as $ach)
                <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 shrink-0">
                        <i data-lucide="trophy" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-white truncate">{{ $ach->title ?? $ach->achievement_key }}</div>
                        <div class="text-xs text-slate-400 truncate">{{ $ach->description ?? 'Unlocked achievement' }}</div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <x-forum.empty-state icon="trophy" title="No Achievements Yet" description="In-game and community achievements will appear here once unlocked." />
                </div>
            @endforelse
        </div>
    @endif
</div>
@endsection
