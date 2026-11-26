@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-white/[0.08] pb-6">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="trophy" class="w-6 h-6 text-[#C6A76A]"></i>
                <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                    Hall of Fame Leaderboards
                </h1>
            </div>
            <p class="text-xs text-[#788394] mt-1">Sovereign standings, legendary feats, and premier guild rankings across {{ config('aether.server_name', 'MYTHERA') }}.</p>
        </div>

        <!-- Ranking Type Tabs -->
        <div class="flex flex-wrap items-center gap-2">
            @php
                $tabs = [
                    'base' => ['label' => 'Base Level', 'icon' => 'trending-up'],
                    'job' => ['label' => 'Job Level', 'icon' => 'award'],
                    'zeny' => ['label' => 'Zeny Wealth', 'icon' => 'coins'],
                    'pvp' => ['label' => 'PvP Kills', 'icon' => 'swords'],
                    'mvp' => ['label' => 'MVP Kills', 'icon' => 'skull'],
                    'guild' => ['label' => 'Guild Rankings', 'icon' => 'shield'],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <a href="{{ route('rankings.index', ['type' => $key]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2 transition-all {{ $type === $key ? 'bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] shadow-lg shadow-[#C6A76A]/20 font-cinzel' : 'bg-[#151B26] border border-white/[0.08] text-[#AAB3C1] hover:text-white hover:border-[#C6A76A]/40' }}">
                    <i data-lucide="{{ $tab['icon'] }}" class="w-3.5 h-3.5"></i>
                    <span>{{ $tab['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Leaderboard Table Card -->
    <div class="prestige-card overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="prestige-table">
                <thead>
                    <tr>
                        <th class="w-20 text-center">Rank</th>
                        @if($type === 'guild')
                            <th>Guild Crest & Name</th>
                            <th>Guild Master</th>
                            <th class="text-center">Guild Level</th>
                            <th class="text-center">Members</th>
                            <th class="text-right">Total EXP</th>
                        @else
                            <th>Adventurer Name</th>
                            <th class="text-center">Job Class</th>
                            <th class="text-center">Base Level</th>
                            @if($type === 'job')
                                <th class="text-center">Job Level</th>
                            @endif
                            @if($type === 'zeny')
                                <th class="text-right">Carried Zeny</th>
                            @elseif($type === 'pvp')
                                <th class="text-right">PvP Kills</th>
                            @elseif($type === 'mvp')
                                <th class="text-right">MVP Kills</th>
                            @else
                                <th class="text-right">Base EXP</th>
                            @endif
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($ladder as $idx => $r)
                        <tr>
                            <!-- Rank Column with Luxury Podiums -->
                            <td class="text-center">
                                <div class="w-7 h-7 mx-auto rounded-lg flex items-center justify-center font-bold text-xs {{ $idx === 0 ? 'rank-badge-1' : ($idx === 1 ? 'rank-badge-2' : ($idx === 2 ? 'rank-badge-3' : 'bg-[#101725] text-[#AAB3C1] border border-white/10')) }}">
                                    {{ $idx + 1 }}
                                </div>
                            </td>

                            @if($type === 'guild')
                                @php
                                    $gId = (int) data_get($r, 'guild_id', 0);
                                    $gName = data_get($r, 'name', 'Unknown');
                                    $gMaster = data_get($r, 'master', '-');
                                    $gBadges = data_get($r, 'badges', []);
                                @endphp
                                <td>
                                    <div class="flex items-center gap-3">
                                        <img src="{{ route('guilds.emblem', $gId) }}" alt="Guild Emblem" class="w-6 h-6 rounded border border-white/10 shadow-sm" loading="lazy">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <a href="{{ route('guilds.show', $gId) }}" class="font-cinzel text-sm font-bold text-[#F4F5F7] hover:text-[#C6A76A] transition-colors">
                                                {{ $gName }}
                                            </a>
                                            @foreach(array_slice($gBadges, 0, 2) as $gb)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border" style="color: {{ $gb['color'] }}; border-color: {{ $gb['color'] }}66; background-color: {{ $gb['color'] }}1f;">{{ $gb['label'] }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('characters.show', $gMaster) }}" class="text-xs text-[#AAB3C1] hover:text-white hover:underline">
                                        {{ $gMaster }}
                                    </a>
                                </td>
                                <td class="text-center font-mono font-bold text-[#C6A76A]">Lv {{ data_get($r, 'guild_lv', 1) }}</td>
                                <td class="text-center text-xs text-[#AAB3C1]">{{ data_get($r, 'connect_member', 0) }} / {{ data_get($r, 'max_member', 0) }}</td>
                                <td class="text-right font-mono font-bold text-white text-xs">{{ number_format((int) data_get($r, 'exp', 0)) }}</td>
                            @else
                                @php
                                    $cName = data_get($r, 'name', 'Unknown');
                                    $cClass = (int) data_get($r, 'class', 0);
                                    $cBadges = data_get($r, 'badges', []);
                                @endphp
                                <td>
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <a href="{{ route('characters.show', $cName) }}" class="font-cinzel text-sm font-bold text-[#F4F5F7] hover:text-[#C6A76A] transition-colors">
                                            {{ $cName }}
                                        </a>
                                        @foreach(array_slice($cBadges, 0, 2) as $cb)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border" style="color: {{ $cb['color'] }}; border-color: {{ $cb['color'] }}66; background-color: {{ $cb['color'] }}1f;">{{ $cb['label'] }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-[#101725] text-[#D9DEE7] border border-white/10">
                                        {{ \App\Services\Emulator\JobHelper::getName($cClass) }}
                                    </span>
                                </td>
                                <td class="text-center font-mono font-bold text-[#C6A76A]">Lv {{ data_get($r, 'base_level', 1) }}</td>
                                @if($type === 'job')
                                    <td class="text-center font-mono font-bold text-[#D8BE8A]">Lv {{ data_get($r, 'job_level', 1) }}</td>
                                @endif
                                @if($type === 'zeny')
                                    <td class="text-right font-mono font-bold text-[#D8BE8A]">{{ number_format((int) data_get($r, 'zeny', 0)) }} Z</td>
                                @elseif($type === 'pvp')
                                    <td class="text-right font-mono font-bold text-[#C6A76A]">{{ number_format((int) data_get($r, 'pvp_kills', data_get($r, 'score', 0))) }}</td>
                                @elseif($type === 'mvp')
                                    <td class="text-right font-mono font-bold text-[#2DD4BF]">{{ number_format((int) data_get($r, 'mvp_kills', data_get($r, 'score', 0))) }}</td>
                                @else
                                    <td class="text-right font-mono text-xs text-[#AAB3C1]">{{ number_format((int) data_get($r, 'base_exp', 0)) }}</td>
                                @endif
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-[#788394]">
                                No ranked records found for this category.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
