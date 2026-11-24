@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Player &amp; Guild Leaderboards</h1>
            <p class="text-xs text-slate-400 mt-1">
                Live server standings synchronized with rAthena.
                @if(!empty($leaderboard['updated_human']))
                    <span class="text-slate-500 font-mono ml-1">Last updated: {{ $leaderboard['updated_human'] }}</span>
                @endif
            </p>
        </div>

        <!-- Ranking Type Tabs -->
        <div class="flex flex-wrap items-center gap-2">
            @php
                $tabs = [
                    'lvl' => ['label' => 'Lvl', 'icon' => 'trending-up'],
                    'job' => ['label' => 'Job', 'icon' => 'award'],
                    'pvp' => ['label' => 'PvP', 'icon' => 'swords'],
                    'mvp' => ['label' => 'MVP', 'icon' => 'skull'],
                    'guild' => ['label' => 'Guild', 'icon' => 'shield'],
                    'zeny' => ['label' => 'Zeny', 'icon' => 'coins'],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <a href="{{ route('rankings.index', ['type' => $key]) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-colors {{ $type === $key ? 'bg-cyan-500 text-slate-950 shadow-md' : 'bg-slate-900 border border-slate-800 text-slate-300 hover:bg-slate-800' }}">
                    <i data-lucide="{{ $tab['icon'] }}" class="w-3.5 h-3.5"></i>
                    <span>{{ $tab['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" action="{{ route('rankings.index') }}" class="flex flex-wrap items-center gap-3 bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
        <input type="hidden" name="type" value="{{ $type }}">
        <div class="relative flex-1 min-w-[200px]">
            <input type="text"
                   name="search"
                   value="{{ $search ?? '' }}"
                   placeholder="{{ $type === 'guild' ? 'Search guild or master name...' : 'Search character name...' }}"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
        </div>

        @if($type !== 'guild')
            <select name="class" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                <option value="">All Job Classes</option>
                @foreach(config('jobs', []) as $jobId => $jobName)
                    <option value="{{ $jobId }}" {{ (string)($classFilter ?? '') === (string)$jobId ? 'selected' : '' }}>{{ $jobName }}</option>
                @endforeach
            </select>
        @endif

        <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold transition-colors">
            Filter
        </button>

        @if(!empty($search) || ($classFilter ?? '') !== '')
            <a href="{{ route('rankings.index', ['type' => $type]) }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                Reset
            </a>
        @endif
    </form>

    <!-- Leaderboard Table -->
    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-950/80 border-b border-slate-800 text-slate-400 text-[11px] font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-16 text-center">Rank</th>
                        @if($type === 'guild')
                            <th class="py-3.5 px-4">Guild</th>
                            <th class="py-3.5 px-4">Guild Master</th>
                            <th class="py-3.5 px-4 text-center">Level</th>
                            <th class="py-3.5 px-4 text-center">Members</th>
                            <th class="py-3.5 px-4 text-center">Castles</th>
                            <th class="py-3.5 px-4 text-right">Badge / Score</th>
                        @else
                            <th class="py-3.5 px-4">Character</th>
                            <th class="py-3.5 px-4 text-center">Class</th>
                            <th class="py-3.5 px-4 text-center">Base Lv</th>
                            <th class="py-3.5 px-4 text-center">Job Lv</th>
                            <th class="py-3.5 px-4 text-center">Guild</th>
                            <th class="py-3.5 px-4 text-right">
                                @if($type === 'pvp')
                                    PvP Kills
                                @elseif($type === 'mvp')
                                    MVP Kills
                                @elseif($type === 'zeny')
                                    Zeny Balance
                                @else
                                    Milestone / Badge
                                @endif
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($ladder as $row)
                        @php
                            $rank = $row['rank'] ?? 1;
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors {{ $rank <= 3 ? 'bg-cyan-950/10' : '' }}">
                            <td class="py-3.5 px-4 text-center font-bold">
                                @if($rank === 1)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-slate-950 font-black text-xs shadow-md shadow-amber-400/30">1</span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-300 text-slate-950 font-black text-xs shadow-md shadow-slate-300/30">2</span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-700 text-white font-black text-xs shadow-md shadow-amber-700/30">3</span>
                                @else
                                    <span class="text-slate-500 font-semibold text-xs">#{{ $rank }}</span>
                                @endif
                            </td>

                            @if($type === 'guild')
                                <td class="py-3.5 px-4 font-bold text-white">
                                    <a href="{{ $row['profile_url'] }}" class="flex items-center gap-2.5 hover:text-cyan-400 transition-colors">
                                        @if(!empty($row['emblem_url']))
                                            <img src="{{ $row['emblem_url'] }}" alt="{{ $row['name'] }}" class="w-6 h-6 object-contain rounded bg-slate-950 border border-slate-800 p-0.5">
                                        @else
                                            <span class="w-6 h-6 rounded bg-slate-950 border border-slate-800 flex items-center justify-center text-[10px] text-cyan-400">{{ strtoupper(substr($row['name'], 0, 1)) }}</span>
                                        @endif
                                        <span>{{ $row['name'] }}</span>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-slate-300">{{ $row['master'] }}</td>
                                <td class="py-3.5 px-4 text-center font-bold text-cyan-400">Lv. {{ $row['guild_lv'] }}</td>
                                <td class="py-3.5 px-4 text-center text-slate-300">{{ $row['members_count'] }} / {{ $row['max_member'] }}</td>
                                <td class="py-3.5 px-4 text-center font-bold text-amber-400">{{ $row['castles_count'] }}</td>
                                <td class="py-3.5 px-4 text-right">
                                    @if(!empty($row['badge_label']))
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 mr-2">{{ $row['badge_label'] }}</span>
                                    @endif
                                    <span class="font-mono text-slate-400 text-xs">{{ number_format($row['score']) }} pts</span>
                                </td>
                            @else
                                <td class="py-3.5 px-4 font-bold text-white">
                                    <a href="{{ $row['profile_url'] }}" class="flex items-center gap-2.5 hover:text-cyan-400 transition-colors">
                                        <span class="w-6 h-6 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-center text-[10px] text-cyan-400 shrink-0">
                                            {{ strtoupper(substr($row['name'], 0, 1)) }}
                                        </span>
                                        <span>{{ $row['name'] }}</span>
                                        @if(!empty($row['online']))
                                            <span class="w-2 h-2 rounded-full bg-emerald-400" title="Online in-game"></span>
                                        @endif
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $row['job_badge_color'] }}">
                                        {{ $row['job_name'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-cyan-400">{{ $row['base_level'] }}</td>
                                <td class="py-3.5 px-4 text-center font-bold text-blue-400">{{ $row['job_level'] }}</td>
                                <td class="py-3.5 px-4 text-center text-slate-300 text-xs">
                                    @if(!empty($row['guild_name']))
                                        <a href="{{ route('guilds.show', $row['guild_id']) }}" class="inline-flex items-center gap-1.5 hover:text-cyan-400">
                                            @if(!empty($row['guild_emblem_url']))
                                                <img src="{{ $row['guild_emblem_url'] }}" alt="" class="w-4 h-4 object-contain">
                                            @endif
                                            <span>{{ $row['guild_name'] }}</span>
                                        </a>
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono">
                                    @if(!empty($row['achievement_label']))
                                        <span class="px-2 py-0.5 rounded text-[10px] font-sans font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                            {{ $row['achievement_label'] }}
                                        </span>
                                    @elseif($type === 'zeny')
                                        <span class="font-bold text-amber-400">{{ $row['primary_stat_value'] }}</span>
                                    @else
                                        <span class="text-slate-400 text-xs">{{ $row['secondary_stat'] ?? 'Active' }}</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-500">
                                @if($type === 'pvp' || $type === 'mvp')
                                    No verified {{ strtoupper($type) }} kill events recorded yet. Live gameplay events will appear here automatically.
                                @else
                                    No records currently found on this leaderboard.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(($leaderboard['last_page'] ?? 1) > 1)
            <div class="p-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span>Page {{ $leaderboard['current_page'] }} of {{ $leaderboard['last_page'] }} ({{ number_format($leaderboard['total']) }} total)</span>
                <div class="flex items-center gap-2">
                    @if($leaderboard['current_page'] > 1)
                        <a href="{{ route('rankings.index', array_merge(request()->query(), ['page' => $leaderboard['current_page'] - 1])) }}"
                           class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-semibold">Previous</a>
                    @endif
                    @if($leaderboard['current_page'] < $leaderboard['last_page'])
                        <a href="{{ route('rankings.index', array_merge(request()->query(), ['page' => $leaderboard['current_page'] + 1])) }}"
                           class="px-3 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white font-semibold">Next</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
