@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-panel flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="trophy" class="w-6 h-6 text-[#FFD700]"></i>
                <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                    Hall of Fame Leaderboards
                </h1>
            </div>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Honoring the mightiest champions and prestigious guilds across {{ \App\Models\SiteSetting::get('server_name', config('aether.server_name', 'Aether Chronicles')) }}
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
                $rankingItems = $ladder ?? $rankings ?? [];
            @endphp
            @foreach($tabs as $key => $tab)
                <a href="{{ route('rankings.index', ['type' => $key]) }}"
                   class="px-3 py-1.5 rounded text-xs font-cinzel font-bold uppercase tracking-wider flex items-center gap-1.5 transition-all {{ $type === $key ? 'bg-gradient-to-b from-[#F3B33D] to-[#9E5600] text-[#3B1F0A] border-2 border-[#FFF3D6] shadow-md' : 'bg-[#251F2D] border border-[#5A4A68] text-[#FFE4A0] hover:bg-[#352B42]' }}">
                    <i data-lucide="{{ $tab['icon'] }}" class="w-3.5 h-3.5"></i>
                    <span>{{ $tab['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Leaderboard Table Panel -->
    <div class="chronicles-panel pt-6">
        <div class="chronicles-panel-header">
            <span class="chronicles-panel-title">
                <i data-lucide="award" class="w-4 h-4"></i>
                <span>{{ $tabs[$type]['label'] ?? 'Standings' }}</span>
            </span>
        </div>

        <div class="overflow-x-auto mt-2">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-[#5A4A68] text-[#CBB488] font-cinzel text-[11px] uppercase">
                        <th class="w-16 py-2 px-3 text-center">Rank</th>
                        @if($type === 'guild')
                            <th class="py-2 px-3">Guild Crest &amp; Name</th>
                            <th class="py-2 px-3">Guild Master</th>
                            <th class="py-2 px-3 text-center">Guild Level</th>
                            <th class="py-2 px-3 text-center">Members</th>
                            <th class="py-2 px-3 text-right">Total EXP</th>
                        @else
                            <th class="py-2 px-3">Adventurer Name</th>
                            <th class="py-2 px-3 text-center">Job Class</th>
                            <th class="py-2 px-3 text-center">Base Level</th>
                            @if($type === 'job')
                                <th class="py-2 px-3 text-center">Job Level</th>
                            @endif
                            @if($type === 'zeny')
                                <th class="py-2 px-3 text-right">Carried Zeny</th>
                            @elseif($type === 'mvp')
                                <th class="py-2 px-3 text-right">MVP Kills</th>
                            @else
                                <th class="py-2 px-3 text-right">EXP Points</th>
                            @endif
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    @forelse($rankingItems as $index => $row)
                        @php $rank = $index + 1; @endphp
                        <tr class="hover:bg-white/[0.03] transition-colors">
                            <td class="py-2.5 px-3 text-center font-bold">
                                @if($rank === 1)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gradient-to-b from-[#FFE885] to-[#D4AF37] text-[#3B1F0A] border border-[#FFF3D6] font-cinzel font-black shadow text-xs">
                                        1
                                    </span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gradient-to-b from-[#E0E0E0] to-[#9E9E9E] text-[#212121] border border-white font-cinzel font-black shadow text-xs">
                                        2
                                    </span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gradient-to-b from-[#D79A63] to-[#8C532B] text-white border border-[#F5CBA7] font-cinzel font-black shadow text-xs">
                                        3
                                    </span>
                                @else
                                    <span class="font-mono text-xs text-[#8C7A68]">#{{ $rank }}</span>
                                @endif
                            </td>

                            @if($type === 'guild')
                                <td class="py-2.5 px-3">
                                    <div class="flex items-center gap-2.5">
                                        @if($row->guild_id > 0)
                                            <img src="{{ route('guilds.emblem', $row->guild_id) }}" alt="Guild Emblem" class="w-6 h-6 rounded border border-[#5A4A68]" loading="lazy">
                                        @endif
                                        <a href="{{ route('guilds.show', $row->guild_id) }}" class="font-cinzel font-bold text-[#F3E5AB] hover:text-[#FFD700]">
                                            {{ $row->name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 text-[#E2D9EB]">{{ $row->master }}</td>
                                <td class="py-2.5 px-3 text-center font-mono font-bold text-[#FFD700]">Lv. {{ $row->guild_lv }}</td>
                                <td class="py-2.5 px-3 text-center font-mono text-[#E2D9EB]">{{ $row->connect_member }} / {{ $row->max_member }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-xs text-[#FFD700] font-bold">{{ number_format($row->exp) }}</td>
                            @else
                                <td class="py-2.5 px-3">
                                    <a href="{{ route('characters.show', $row->name) }}" class="font-cinzel font-bold text-[#F3E5AB] hover:text-[#FFD700]">
                                        {{ $row->name }}
                                    </a>
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#1F1926] text-[#CBB488] border border-[#5A4A68]">
                                        {{ \App\Services\Emulator\JobHelper::getName($row->class) }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono font-bold text-[#FFD700]">Lv. {{ $row->base_level }}</td>
                                @if($type === 'job')
                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-[#E5A823]">Lv. {{ $row->job_level }}</td>
                                @endif
                                @if($type === 'zeny')
                                    <td class="py-2.5 px-3 text-right font-mono text-xs font-bold text-[#FFD700]">{{ number_format($row->zeny) }} Z</td>
                                @elseif($type === 'mvp')
                                    <td class="py-2.5 px-3 text-right font-mono text-xs font-bold text-[#E53935]">{{ number_format($row->mvp_kills ?? $row->score ?? 0) }}</td>
                                @else
                                    <td class="py-2.5 px-3 text-right font-mono text-xs text-[#A396AD]">{{ number_format($row->base_exp) }}</td>
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
