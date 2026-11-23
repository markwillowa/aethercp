@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Player & Guild Leaderboards</h1>
            <p class="text-xs text-slate-400 mt-1">Real-time standings across all adventurers. Cached every 5 minutes.</p>
        </div>

        <!-- Ranking Type Tabs -->
        <div class="flex flex-wrap items-center gap-2">
            @php
                $tabs = [
                    'base' => ['label' => 'Base Level', 'icon' => 'trending-up'],
                    'job' => ['label' => 'Job Level', 'icon' => 'award'],
                    'zeny' => ['label' => 'Zeny Wealth', 'icon' => 'coins'],
                    'mvp' => ['label' => 'MVP Kills', 'icon' => 'skull'],
                    'guild' => ['label' => 'Guilds', 'icon' => 'shield'],
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

    <!-- Leaderboard Table -->
    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-950/80 border-b border-slate-800 text-slate-400 text-[11px] font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-16 text-center">Rank</th>
                        @if($type === 'guild')
                            <th class="py-3.5 px-4">Guild Name</th>
                            <th class="py-3.5 px-4">Guild Master</th>
                            <th class="py-3.5 px-4 text-center">Guild Level</th>
                            <th class="py-3.5 px-4 text-center">Members</th>
                            <th class="py-3.5 px-4 text-right">Guild EXP</th>
                        @else
                            <th class="py-3.5 px-4">Character Name</th>
                            <th class="py-3.5 px-4 text-center">Class</th>
                            <th class="py-3.5 px-4 text-center">Base Level</th>
                            @if($type === 'job')
                                <th class="py-3.5 px-4 text-center">Job Level</th>
                            @endif
                            @if($type === 'zeny')
                                <th class="py-3.5 px-4 text-right">Zeny</th>
                            @elseif($type === 'mvp')
                                <th class="py-3.5 px-4 text-right">Fame Points</th>
                            @else
                                <th class="py-3.5 px-4 text-right">Total Base EXP</th>
                            @endif
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($ladder as $idx => $row)
                        <tr class="hover:bg-slate-800/30 transition-colors {{ $idx < 3 ? 'bg-cyan-950/10' : '' }}">
                            <td class="py-3.5 px-4 text-center font-bold">
                                @if($idx === 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-slate-950 font-black text-xs shadow-md shadow-amber-400/30">1</span>
                                @elseif($idx === 1)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-300 text-slate-950 font-black text-xs shadow-md shadow-slate-300/30">2</span>
                                @elseif($idx === 2)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-700 text-white font-black text-xs shadow-md shadow-amber-700/30">3</span>
                                @else
                                    <span class="text-slate-500 font-semibold text-xs">#{{ $idx + 1 }}</span>
                                @endif
                            </td>

                            @if($type === 'guild')
                                <td class="py-3.5 px-4 font-bold text-white">
                                    <a href="{{ route('guilds.show', $row->guild_id) }}" class="hover:text-cyan-400 transition-colors">
                                        {{ $row->name }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-slate-300">{{ $row->master }}</td>
                                <td class="py-3.5 px-4 text-center font-bold text-cyan-400">Lv. {{ $row->guild_lv }}</td>
                                <td class="py-3.5 px-4 text-center text-slate-300">{{ $row->connect_member }} / {{ $row->max_member }}</td>
                                <td class="py-3.5 px-4 text-right font-mono text-slate-300">{{ number_format($row->exp) }}</td>
                            @else
                                <td class="py-3.5 px-4 font-bold text-white">
                                    <a href="{{ route('characters.show', $row->name) }}" class="hover:text-cyan-400 transition-colors">
                                        {{ $row->name }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ \App\Services\Emulator\JobHelper::getBadgeColor($row->class) }}">
                                        {{ \App\Services\Emulator\JobHelper::getName($row->class) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-cyan-400">{{ $row->base_level }}</td>
                                @if($type === 'job')
                                    <td class="py-3.5 px-4 text-center font-bold text-blue-400">{{ $row->job_level }}</td>
                                @endif
                                @if($type === 'zeny')
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-amber-400">{{ number_format($row->zeny) }} Z</td>
                                @elseif($type === 'mvp')
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-purple-400">{{ number_format($row->fame) }}</td>
                                @else
                                    <td class="py-3.5 px-4 text-right font-mono text-slate-400">{{ number_format($row->base_exp) }}</td>
                                @endif
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                No records currently found on this leaderboard.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

