@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <nav class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('forum.index') }}" class="hover:text-cyan-400">Forum</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
        <span class="text-slate-200 font-semibold">Community Reputation Leaderboard</span>
    </nav>

    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/75 backdrop-blur-md p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
                <i data-lucide="trophy" class="w-6 h-6 text-amber-400"></i>
                <span>Community Reputation Leaderboard</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Recognizing our most helpful, active, and respected Rune-Midgard community members.</p>
        </div>

        <div class="flex items-center gap-2">
            @foreach(['all' => 'All Time', 'month' => 'This Month', 'week' => 'This Week'] as $pKey => $pLabel)
                <a href="{{ route('forum.leaderboard', ['period' => $pKey]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-semibold border transition-colors {{ $period === $pKey ? 'bg-cyan-500/15 border-cyan-500/40 text-cyan-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:text-white' }}">
                    {{ $pLabel }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/65 backdrop-blur-md overflow-hidden">
        @if($entries->isEmpty())
            <div class="p-8">
                <x-forum.empty-state icon="trophy" title="No Rankings for This Period" description="Reputation points earned during this timeframe will appear here." />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-950/70 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5 w-20">Rank</th>
                            <th class="px-5 py-3.5">Member</th>
                            <th class="px-5 py-3.5">Reputation Level</th>
                            <th class="px-5 py-3.5 text-right">Contributions</th>
                            <th class="px-5 py-3.5 text-right">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/70">
                        @foreach($entries as $idx => $entry)
                            @php
                                $rank = ($entries->currentPage() - 1) * $entries->perPage() + $idx + 1;
                                $identity = $entry->user ? ($identities[$entry->user->id] ?? null) : null;
                            @endphp
                            <tr class="hover:bg-slate-900/80 transition-colors">
                                <td class="px-5 py-4 font-mono font-bold">
                                    @if($rank === 1)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/40 text-amber-300">#1</span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-300/15 border border-slate-400/30 text-slate-200">#2</span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-orange-500/15 border border-orange-500/30 text-orange-300">#3</span>
                                    @else
                                        <span class="text-slate-400">#{{ $rank }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if($identity)
                                        <x-forum.user-identity :identity="$identity" :show-reputation="false" />
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <x-forum.reputation-badge :level="$entry->level" />
                                </td>
                                <td class="px-5 py-4 text-right font-mono text-slate-300">
                                    {{ number_format($entry->contribution_count) }}
                                </td>
                                <td class="px-5 py-4 text-right font-mono font-bold text-amber-400">
                                    {{ number_format($entry->points) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($entries->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $entries->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
