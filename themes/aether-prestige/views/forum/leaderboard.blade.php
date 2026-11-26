@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <nav class="flex items-center gap-2 text-xs text-[#788394]">
        <a href="{{ route('forum.index') }}" class="hover:text-[#D8BE8A] transition-colors">Community</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-[#D9DEE7] font-semibold">Community Reputation Leaderboard</span>
    </nav>

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Community Reputation Leaderboard
            </h1>
            <p class="text-xs text-[#788394] mt-1">Recognizing our most helpful, active, and respected Rune-Midgard community members.</p>
        </div>

        <div class="flex items-center gap-2">
            @foreach(['all' => 'All Time', 'month' => 'This Month', 'week' => 'This Week'] as $pKey => $pLabel)
                <a href="{{ route('forum.leaderboard', ['period' => $pKey]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold border transition-colors {{ $period === $pKey ? 'bg-[#C6A76A]/20 border-[#C6A76A]/50 text-[#D8BE8A]' : 'bg-[#151B26] border-white/[0.08] text-[#AAB3C1] hover:text-white' }}">
                    {{ $pLabel }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="prestige-card overflow-hidden">
        @if($entries->isEmpty())
            <div class="p-12 text-center space-y-2">
                <i data-lucide="trophy" class="w-10 h-10 text-[#C6A76A]/60 mx-auto"></i>
                <h3 class="font-cinzel text-sm font-bold text-white uppercase">No Rankings for This Period</h3>
                <p class="text-xs text-[#788394]">Reputation points earned during this timeframe will appear here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#101725] text-[11px] font-cinzel uppercase tracking-wider text-[#788394] border-b border-white/[0.08]">
                        <tr>
                            <th class="px-5 py-3.5 w-20">Rank</th>
                            <th class="px-5 py-3.5">Member</th>
                            <th class="px-5 py-3.5">Reputation Level</th>
                            <th class="px-5 py-3.5 text-right">Contributions</th>
                            <th class="px-5 py-3.5 text-right">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.06]">
                        @foreach($entries as $idx => $entry)
                            @php
                                $rank = ($entries->currentPage() - 1) * $entries->perPage() + $idx + 1;
                                $identity = $entry->user ? ($identities[$entry->user->id] ?? null) : null;
                            @endphp
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-5 py-4 font-mono font-bold">
                                    @if($rank === 1)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#C6A76A]/25 border border-[#C6A76A]/50 text-[#D8BE8A]">#1</span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 border border-white/20 text-white">#2</span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/30 text-amber-300">#3</span>
                                    @else
                                        <span class="text-[#788394]">#{{ $rank }}</span>
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
                                <td class="px-5 py-4 text-right font-mono text-[#D9DEE7]">
                                    {{ number_format($entry->contribution_count) }}
                                </td>
                                <td class="px-5 py-4 text-right font-mono font-bold text-[#D8BE8A]">
                                    {{ number_format($entry->points) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($entries->hasPages())
                <div class="p-4 border-t border-white/[0.08]">{{ $entries->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
