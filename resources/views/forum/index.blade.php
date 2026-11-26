@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <x-forum.forum-header :stats="$stats" :search="$search ?? ''" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Main Column -->
        <div class="lg:col-span-8 min-w-0 space-y-8">
            @if(!empty($search))
                <div class="rounded-xl border border-cyan-500/30 bg-cyan-500/10 px-4 py-3 flex items-center justify-between text-sm text-cyan-200">
                    <span>Showing discussions matching <strong class="text-white">"{{ $search }}"</strong></span>
                    <a href="{{ route('forum.index') }}" class="text-xs font-semibold text-cyan-400 hover:underline">Clear search</a>
                </div>
            @endif

            <!-- Forum Categories & Boards -->
            <div class="space-y-6">
                @forelse($categories as $category)
                    <section class="rounded-2xl border border-slate-800/80 bg-slate-900/45 backdrop-blur-md overflow-hidden">
                        <div class="px-5 py-4 bg-slate-950/60 border-b border-slate-800/80 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-600/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
                                    <i data-lucide="{{ $category->icon ?: 'message-square' }}" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-white">{{ $category->name }}</h2>
                                    @if($category->description)
                                        <p class="text-xs text-slate-400">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-4 space-y-3">
                            @forelse($category->activeBoards as $board)
                                <x-forum.board-card :board="$board" :identities="$identities" />
                            @empty
                                <p class="text-sm text-slate-500 text-center py-4">No boards available in this category.</p>
                            @endforelse
                        </div>
                    </section>
                @empty
                    <x-forum.empty-state icon="folder-open" title="No Forum Categories Yet" description="Administrators haven't configured any forum boards yet." />
                @endforelse
            </div>

            <!-- Recent Discussions -->
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <i data-lucide="clock" class="w-5 h-5 text-cyan-400"></i>
                        <span>{{ !empty($search) ? 'Search Results' : 'Recent Discussions' }}</span>
                    </h2>
                </div>

                @forelse($recentDiscussions as $topic)
                    <x-forum.topic-row :topic="$topic" :identities="$identities" :show-board="true" />
                @empty
                    <x-forum.empty-state icon="message-square-dashed" title="No Discussions Yet" description="Be the first adventurer to start a conversation in the community!" />
                @endforelse
            </section>
        </div>

        <!-- Sidebar -->
        <aside class="lg:col-span-4 min-w-0 space-y-6">
            @auth
                @if($userStats)
                    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/65 backdrop-blur-md p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300">Your Forum Standing</h3>
                            <a href="{{ route('forum.identity.settings') }}" class="text-xs text-cyan-400 hover:underline">Identity Settings</a>
                        </div>
                        @if(isset($identities[auth()->id()]))
                            <x-forum.user-identity :identity="$identities[auth()->id()]" :show-points="true" />
                        @endif
                        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-800/80 text-center">
                            <div class="rounded-xl bg-slate-950/60 p-2.5">
                                <div class="text-base font-bold text-white font-mono">{{ $userStats['topics_count'] }}</div>
                                <div class="text-[10px] text-slate-500 uppercase">Topics</div>
                            </div>
                            <div class="rounded-xl bg-slate-950/60 p-2.5">
                                <div class="text-base font-bold text-white font-mono">{{ $userStats['replies_count'] }}</div>
                                <div class="text-[10px] text-slate-500 uppercase">Replies</div>
                            </div>
                            <div class="rounded-xl bg-slate-950/60 p-2.5">
                                <div class="text-base font-bold text-amber-400 font-mono">{{ $userStats['reputation_points'] }}</div>
                                <div class="text-[10px] text-slate-500 uppercase">Rep</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- Latest Announcements -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/65 backdrop-blur-md p-5 space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                    <i data-lucide="megaphone" class="w-4 h-4 text-purple-400"></i>
                    <span>Latest Announcements</span>
                </h3>
                @forelse($announcements as $ann)
                    <a href="{{ route('forum.topic.show', $ann->slug) }}" class="block p-3 rounded-xl bg-slate-950/50 border border-slate-800/80 hover:border-purple-500/40 transition-colors">
                        <div class="text-sm font-semibold text-slate-200 truncate">{{ $ann->title }}</div>
                        <div class="text-[11px] text-slate-500 mt-1">{{ $ann->created_at?->diffForHumans() }}</div>
                    </a>
                @empty
                    <p class="text-xs text-slate-500">No announcements posted yet.</p>
                @endforelse
            </div>

            <!-- Trending Discussions -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/65 backdrop-blur-md p-5 space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                    <i data-lucide="flame" class="w-4 h-4 text-amber-400"></i>
                    <span>Trending Discussions</span>
                </h3>
                @forelse($trendingDiscussions as $trend)
                    <a href="{{ route('forum.topic.show', $trend->slug) }}" class="block p-3 rounded-xl bg-slate-950/50 border border-slate-800/80 hover:border-cyan-500/40 transition-colors">
                        <div class="text-sm font-semibold text-slate-200 truncate">{{ $trend->title }}</div>
                        <div class="text-[11px] text-slate-500 mt-1 flex items-center justify-between">
                            <span>{{ $trend->board?->name }}</span>
                            <span class="font-mono text-cyan-400">{{ $trend->replies_count }} replies</span>
                        </div>
                    </a>
                @empty
                    <p class="text-xs text-slate-500">No trending topics in the last 14 days.</p>
                @endforelse
            </div>

            <!-- Most Active Members -->
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/65 backdrop-blur-md p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                        <i data-lucide="award" class="w-4 h-4 text-emerald-400"></i>
                        <span>Top Reputation</span>
                    </h3>
                    <a href="{{ route('forum.leaderboard') }}" class="text-xs text-cyan-400 hover:underline">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($activeMembers as $memberProfile)
                        @if(isset($identities[$memberProfile->user_id]))
                            <div class="flex items-center justify-between gap-2">
                                <x-forum.user-identity :identity="$identities[$memberProfile->user_id]" :compact="true" :show-reputation="false" />
                                <span class="text-xs font-mono font-bold text-amber-400 shrink-0">{{ number_format($memberProfile->reputation_points) }} pts</span>
                            </div>
                        @endif
                    @empty
                        <p class="text-xs text-slate-500">No member reputation activity yet.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
