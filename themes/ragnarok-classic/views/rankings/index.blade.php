@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Banner & Category Tabs -->
    <div class="ro-panel">
        <div class="ro-panel-header justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2">
                <i data-lucide="trophy" class="w-4 h-4"></i>
                <h1 class="text-base font-bold font-serif text-[#F8EBD2]">Midgard Hall of Fame &bull; Leaderboards</h1>
            </div>
            <span class="text-[11px] text-[#FFDA89] font-normal">Updated periodically from server database</span>
        </div>

        <div class="p-4 bg-[#FCF7ED] flex flex-wrap items-center justify-between gap-3 border-b border-[#DECBB1]">
            <p class="text-xs text-[#5C4632]">
                Inspect the most revered adventurers and dominant guilds across Rune-Midgard.
            </p>

            <!-- Classic Tab Buttons -->
            <div class="flex flex-wrap items-center gap-1.5">
                @php
                    $tabs = [
                        'base' => ['label' => 'Base Level', 'icon' => 'trending-up'],
                        'job' => ['label' => 'Job Level', 'icon' => 'award'],
                        'zeny' => ['label' => 'Zeny Wealth', 'icon' => 'coins'],
                        'mvp' => ['label' => 'MVP Kills', 'icon' => 'skull'],
                        'guild' => ['label' => 'Top Guilds', 'icon' => 'shield'],
                    ];
                @endphp
                @foreach($tabs as $key => $tab)
                    <a href="{{ route('rankings.index', ['type' => $key]) }}"
                       class="{{ $type === $key ? 'ro-btn-primary' : 'ro-btn-wood' }} text-xs py-1.5 px-3">
                        <i data-lucide="{{ $tab['icon'] }}" class="w-3.5 h-3.5"></i>
                        <span>{{ $tab['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Leaderboard Table -->
        <div class="overflow-x-auto p-4 bg-[#F5EBD5]">
            <table class="ro-table">
                <thead>
                    <tr>
                        <th class="w-14 text-center">Rank</th>
                        @if($type === 'guild')
                            <th>Guild Name</th>
                            <th>Guild Master</th>
                            <th class="text-center">Level</th>
                            <th class="text-center">Members</th>
                            <th class="text-right">Total Guild EXP</th>
                        @else
                            <th>Character Name</th>
                            <th class="text-center">Job Class</th>
                            <th class="text-center">Base Lv</th>
                            @if($type === 'job')
                                <th class="text-center">Job Lv</th>
                            @endif
                            @if($type === 'zeny')
                                <th class="text-right">Accumulated Zeny</th>
                            @elseif($type === 'mvp')
                                <th class="text-right">Fame Points</th>
                            @else
                                <th class="text-right">Base Experience</th>
                            @endif
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($ladder as $idx => $row)
                        <tr>
                            <td class="text-center">
                                @if($idx === 0)
                                    <span class="ro-rank-badge ro-rank-1" title="1st Place - Champion">1</span>
                                @elseif($idx === 1)
                                    <span class="ro-rank-badge ro-rank-2" title="2nd Place">2</span>
                                @elseif($idx === 2)
                                    <span class="ro-rank-badge ro-rank-3" title="3rd Place">3</span>
                                @else
                                    <span class="ro-rank-badge ro-rank-other">{{ $idx + 1 }}</span>
                                @endif
                            </td>

                            @if($type === 'guild')
                                <td class="font-bold">
                                    <a href="{{ route('guilds.show', data_get($row, 'guild_id')) }}" class="text-[#315B88] hover:underline flex items-center gap-1.5">
                                        <i data-lucide="shield" class="w-3.5 h-3.5 text-[#8C6A2D]"></i>
                                        <span>{{ data_get($row, 'name') }}</span>
                                    </a>
                                </td>
                                <td class="text-[#5C4632]">{{ data_get($row, 'master') }}</td>
                                <td class="text-center font-bold text-[#8C5E1E]">Lv. {{ data_get($row, 'guild_lv') }}</td>
                                <td class="text-center font-mono">{{ data_get($row, 'connect_member') }} / {{ data_get($row, 'max_member') }}</td>
                                <td class="text-right font-mono font-bold text-[#3A2A1C]">{{ number_format((int) data_get($row, 'exp')) }}</td>
                            @else
                                <td class="font-bold">
                                    <a href="{{ route('characters.show', data_get($row, 'name')) }}" class="text-[#315B88] hover:underline flex items-center gap-1.5">
                                        <i data-lucide="user" class="w-3.5 h-3.5 text-[#8C6A2D]"></i>
                                        <span>{{ data_get($row, 'name') }}</span>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase bg-[#EBDDBF] border border-[#DECBB1] text-[#3A2A1C]">
                                        {{ \App\Services\Emulator\JobHelper::getName(data_get($row, 'class', 0)) }}
                                    </span>
                                </td>
                                <td class="text-center font-bold text-[#315B88]">{{ data_get($row, 'base_level') }}</td>
                                @if($type === 'job')
                                    <td class="text-center font-bold text-[#8C5E1E]">{{ data_get($row, 'job_level') }}</td>
                                @endif
                                @if($type === 'zeny')
                                    <td class="text-right font-mono font-bold text-[#B88926]">{{ number_format((int) data_get($row, 'zeny')) }} Z</td>
                                @elseif($type === 'mvp')
                                    <td class="text-right font-mono font-bold text-[#7E22CE]">{{ number_format((int) data_get($row, 'fame')) }} pts</td>
                                @else
                                    <td class="text-right font-mono text-[#5C4632]">{{ number_format((int) data_get($row, 'base_exp')) }}</td>
                                @endif
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-xs text-[#77644E]">
                                <i data-lucide="scroll" class="w-6 h-6 mx-auto mb-1 text-[#8C6A2D]"></i>
                                <span>No adventurer entries recorded yet on this ladder.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

