@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="trophy" class="w-6 h-6 text-[#FFD700]"></i>
                <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                    Hall of Fame Leaderboards
                </h1>
            </div>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Honoring the mightiest champions and prestigious guilds across {{ \App\Models\SiteSetting::get('server_name', config('aether.server_name', 'MYTHERA')) }}
            </p>
        </div>

        <!-- Ranking Type Tabs -->
        <div class="flex flex-wrap items-center gap-2">
            @php
                $tabs = [
                    'base' => ['label' => 'Base Level', 'icon' => 'trending-up'],
                    'job' => ['label' => 'Job Level', 'icon' => 'award'],
                    'zeny' => ['label' => 'Zeny Wealth', 'icon' => 'coins'],
                    'mvp' => ['label' => 'MVP Kills', 'icon' => 'skull'],
                    'guild' => ['label' => 'Guild Rankings', 'icon' => 'shield'],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <a href="{{ route('rankings.index', ['type' => $key]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-cinzel font-bold uppercase tracking-wider flex items-center gap-1.5 transition-all {{ $type === $key ? 'bg-gradient-to-b from-[#F3B33D] to-[#9E5600] text-[#3B1F0A] border-2 border-[#FFF3D6] shadow-md' : 'bg-[#3B1F0A]/80 border border-[#C99A6B]/50 text-[#FFE4A0] hover:bg-[#563013]' }}">
                    <i data-lucide="{{ $tab['icon'] }}" class="w-3.5 h-3.5"></i>
                    <span>{{ $tab['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Leaderboard Table Panel -->
    <div class="chronicles-panel-ivy pt-8">
        <div class="chronicles-ribbon-header">
            <span>{{ $tabs[$type]['label'] ?? 'Standings' }}</span>
        </div>

        <div class="overflow-x-auto mt-2">
            <table class="chronicles-table">
                <thead>
                    <tr>
                        <th class="w-16 text-center">Rank</th>
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
                            @elseif($type === 'mvp')
                                <th class="text-right">MVP Kills</th>
                            @else
                                <th class="text-right">EXP Points</th>
                            @endif
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($rankings as $index => $row)
                        @php $rank = $index + 1; @endphp
                        <tr>
                            <td class="text-center font-bold">
                                @if($rank === 1)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gradient-to-b from-[#FFE885] to-[#D4AF37] text-[#3B1F0A] border border-[#FFF3D6] font-cinzel font-black shadow text-xs">
                                        1
                                    </span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gradient-to-b from-[#E0E0E0] to-[#9E9E9E] text-[#212121] border border-white font-cinzel font-black shadow text-xs">
                                        2
                                    </span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gradient-to-b from-[#D79A63] to-[#8C532B] text-white border border-[#F5CBA7] font-cinzel font-black shadow text-xs">
                                        3
                                    </span>
                                @else
                                    <span class="font-mono text-xs text-[#8C7A68]">#{{ $rank }}</span>
                                @endif
                            </td>

                            @if($type === 'guild')
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if($row->guild_id > 0)
                                            <img src="{{ route('guilds.emblem', $row->guild_id) }}" alt="Guild Emblem" class="w-6 h-6 rounded border border-[#DECBB1]" loading="lazy">
                                        @endif
                                        <a href="{{ route('guilds.show', $row->guild_id) }}" class="font-cinzel font-bold text-[#4A2200] hover:text-[#D4441D]">
                                            {{ $row->name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="font-medium text-[#5A3D28]">{{ $row->master }}</td>
                                <td class="text-center font-mono font-bold text-[#D4441D]">Lv. {{ $row->guild_lv }}</td>
                                <td class="text-center font-mono text-[#5A3D28]">{{ $row->connect_member }} / {{ $row->max_member }}</td>
                                <td class="text-right font-mono text-xs text-[#9E5600] font-bold">{{ number_format($row->exp) }}</td>
                            @else
                                <td>
                                    <a href="{{ route('characters.show', $row->name) }}" class="font-cinzel font-bold text-[#4A2200] hover:text-[#D4441D]">
                                        {{ $row->name }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#FAF4EA] text-[#5A3D28] border border-[#DECBB1]">
                                        {{ \App\Services\Emulator\JobHelper::getName($row->class) }}
                                    </span>
                                </td>
                                <td class="text-center font-mono font-bold text-[#D4441D]">Lv. {{ $row->base_level }}</td>
                                @if($type === 'job')
                                    <td class="text-center font-mono font-bold text-[#9E5600]">Lv. {{ $row->job_level }}</td>
                                @endif
                                @if($type === 'zeny')
                                    <td class="text-right font-mono text-xs font-bold text-[#9E5600]">{{ number_format($row->zeny) }} Z</td>
                                @elseif($type === 'mvp')
                                    <td class="text-right font-mono text-xs font-bold text-[#D4441D]">{{ number_format($row->mvp_kills ?? $row->score ?? 0) }}</td>
                                @else
                                    <td class="text-right font-mono text-xs text-[#8C7A68]">{{ number_format($row->base_exp) }}</td>
                                @endif
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-[#8C7A68]">
                                Leaderboard awaiting new records.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
