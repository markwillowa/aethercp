@extends('layouts.app')

@section('content')
<div class="payon-container py-10 space-y-6">
    <!-- Payon Ranking Hall Header -->
    <div class="payon-wood-frame overflow-hidden">
        <div class="payon-wood-header">
            <div class="flex items-center gap-2">
                <i data-lucide="trophy" class="w-4 h-4 text-[#A88950]"></i>
                <span class="font-cinzel uppercase tracking-wider text-sm">Payon Ranking Hall</span>
            </div>
            @if(!empty($leaderboard['updated_human']))
                <span class="text-[11px] font-mono text-[#E7D8B5]">Updated: {{ $leaderboard['updated_human'] }}</span>
            @endif
        </div>

        <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="font-cinzel text-2xl font-bold text-[#35291F]">Adventurer &amp; Clan Standings</h1>
                <p class="text-xs text-[#80633B] mt-1">Live kingdom leaderboards synchronized with rAthena.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @php
                    $tabs = [
                        'lvl' => ['label' => 'Base', 'icon' => 'trending-up'],
                        'job' => ['label' => 'Job', 'icon' => 'award'],
                        'pvp' => ['label' => 'PvP', 'icon' => 'swords'],
                        'mvp' => ['label' => 'MVP', 'icon' => 'skull'],
                        'guild' => ['label' => 'Guild', 'icon' => 'shield'],
                        'zeny' => ['label' => 'Zeny', 'icon' => 'coins'],
                    ];
                @endphp
                @foreach($tabs as $key => $tab)
                    <a href="{{ route('rankings.index', ['type' => $key]) }}"
                       class="px-3.5 py-1.5 rounded text-xs font-bold border flex items-center gap-1.5 transition-colors {{ $type === $key ? 'bg-[#263D2C] text-[#FFF8E8] border-[#A88950]' : 'bg-[#F2E8CF] text-[#35291F] border-[#A88950] hover:bg-[#E7D8B5]' }}">
                        <i data-lucide="{{ $tab['icon'] }}" class="w-3.5 h-3.5"></i>
                        <span>{{ $tab['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filter Scroll Bar -->
    <form method="GET" action="{{ route('rankings.index') }}" class="payon-parchment-panel p-4 flex flex-wrap items-center gap-3">
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="{{ $type === 'guild' ? 'Search clan or master name...' : 'Search adventurer name...' }}"
               class="flex-1 min-w-[200px] px-3.5 py-2 rounded text-xs">

        @if($type !== 'guild')
            <select name="class" class="px-3 py-2 rounded text-xs">
                <option value="">All Job Classes</option>
                @foreach(config('jobs', []) as $jobId => $jobName)
                    <option value="{{ $jobId }}" {{ (string)($classFilter ?? '') === (string)$jobId ? 'selected' : '' }}>{{ $jobName }}</option>
                @endforeach
            </select>
        @endif

        <button type="submit" class="payon-btn-forest py-2 px-4 text-xs">Filter</button>
        @if(!empty($search) || ($classFilter ?? '') !== '')
            <a href="{{ route('rankings.index', ['type' => $type]) }}" class="payon-btn-gold py-2 px-3.5 text-xs">Reset</a>
        @endif
    </form>

    <!-- Wooden Leaderboard Table -->
    <div class="payon-ranking-board">
        @php $ladder = $leaderboard['items'] ?? []; @endphp
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="font-cinzel uppercase text-[11px] tracking-wider">
                        <th class="py-3.5 px-4 text-center w-16">Rank</th>
                        @if($type === 'guild')
                            <th class="py-3.5 px-4">Clan Name</th>
                            <th class="py-3.5 px-4">Master</th>
                            <th class="py-3.5 px-4 text-center">Level</th>
                            <th class="py-3.5 px-4 text-center">Members</th>
                            <th class="py-3.5 px-4 text-center">Castles</th>
                            <th class="py-3.5 px-4 text-right">Score</th>
                        @else
                            <th class="py-3.5 px-4">Adventurer</th>
                            <th class="py-3.5 px-4 text-center">Class</th>
                            <th class="py-3.5 px-4 text-center">Base Lv</th>
                            <th class="py-3.5 px-4 text-center">Job Lv</th>
                            <th class="py-3.5 px-4 text-center">Guild</th>
                            <th class="py-3.5 px-4 text-right">Standing</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#A88950]/30">
                    @forelse($ladder as $row)
                        @php $rank = $row['rank'] ?? 1; @endphp
                        <tr class="hover:bg-[#E7D8B5]/60 transition-colors {{ $rank <= 3 ? 'bg-[#FFF8E8]' : '' }}">
                            <td class="py-3.5 px-4 text-center font-bold">
                                @if($rank === 1)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full payon-rank-badge-1 font-cinzel text-xs">1</span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full payon-rank-badge-2 font-cinzel text-xs">2</span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full payon-rank-badge-3 font-cinzel text-xs">3</span>
                                @else
                                    <span class="text-[#80633B] font-mono">#{{ $rank }}</span>
                                @endif
                            </td>

                            @if($type === 'guild')
                                <td class="py-3.5 px-4 font-bold">
                                    <a href="{{ $row['profile_url'] }}" class="flex items-center gap-2 text-[#35291F] hover:text-[#526D3A]">
                                        @if(!empty($row['emblem_url']))
                                            <img src="{{ $row['emblem_url'] }}" alt="{{ $row['name'] }}" class="w-6 h-6 object-contain rounded border border-[#A88950] bg-[#FFF8E8] p-0.5">
                                        @endif
                                        <span>{{ $row['name'] }}</span>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-[#493322]">{{ $row['master'] }}</td>
                                <td class="py-3.5 px-4 text-center font-bold text-[#263D2C]">Lv. {{ $row['guild_lv'] }}</td>
                                <td class="py-3.5 px-4 text-center font-mono">{{ $row['members_count'] }} / {{ $row['max_member'] }}</td>
                                <td class="py-3.5 px-4 text-center font-bold text-[#A88950]">{{ $row['castles_count'] }}</td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-[#35291F]">{{ number_format($row['score']) }} pts</td>
                            @else
                                <td class="py-3.5 px-4 font-bold">
                                    <a href="{{ $row['profile_url'] }}" class="flex items-center gap-2.5 text-[#35291F] hover:text-[#526D3A]">
                                        <span>{{ $row['name'] }}</span>
                                        @if(!empty($row['online']))
                                            <span class="w-2 h-2 rounded-full bg-[#526D3A]" title="Online"></span>
                                        @endif
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-[#E7D8B5] border border-[#A88950] text-[#35291F]">
                                        {{ $row['job_name'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-[#263D2C]">{{ $row['base_level'] }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-[#526D3A]">{{ $row['job_level'] }}</td>
                                <td class="py-3.5 px-4 text-center text-[#493322]">
                                    @if(!empty($row['guild_name']))
                                        <a href="{{ route('guilds.show', $row['guild_id']) }}" class="hover:underline font-semibold">{{ $row['guild_name'] }}</a>
                                    @else
                                        <span class="text-[#80633B]">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-[#80633B]">
                                    {{ $type === 'zeny' ? ($row['primary_stat_value'] ?? '') : ($row['secondary_stat'] ?? 'Active') }}
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-[#80633B]">No records currently found on this leaderboard.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(($leaderboard['last_page'] ?? 1) > 1)
            <div class="p-4 border-t border-[#A88950]/40 flex items-center justify-between text-xs text-[#80633B]">
                <span>Page {{ $leaderboard['current_page'] }} of {{ $leaderboard['last_page'] }} ({{ number_format($leaderboard['total']) }} total)</span>
                <div class="flex items-center gap-2">
                    @if($leaderboard['current_page'] > 1)
                        <a href="{{ route('rankings.index', array_merge(request()->query(), ['page' => $leaderboard['current_page'] - 1])) }}" class="payon-btn-gold py-1.5 px-3 text-xs">Previous</a>
                    @endif
                    @if($leaderboard['current_page'] < $leaderboard['last_page'])
                        <a href="{{ route('rankings.index', array_merge(request()->query(), ['page' => $leaderboard['current_page'] + 1])) }}" class="payon-btn-forest py-1.5 px-3 text-xs">Next</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
