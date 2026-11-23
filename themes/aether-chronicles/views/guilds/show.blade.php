@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Guild Dossier
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Alliance hierarchy, occupied castles, and roster composition
            </p>
        </div>
        <a href="{{ route('guilds.index') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#FFD700]"></i>
            <span>All Guilds</span>
        </a>
    </div>

    <!-- Guild Profile Sheet -->
    <div class="chronicles-panel-ivy pt-8">
        <div class="chronicles-ribbon-header">
            <span>{{ $guild->name }}</span>
        </div>

        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mt-2">
            <div class="w-32 h-32 bg-[#FAF4EA] border-2 border-[#DECBB1] rounded-2xl flex items-center justify-center p-4 shrink-0 shadow-inner">
                <img src="{{ route('guilds.emblem', $guild->guild_id) }}" alt="Guild Emblem" class="w-16 h-16 rounded border border-[#DECBB1]" loading="lazy">
            </div>

            <div class="flex-1 space-y-4 w-full">
                <div class="border-b border-[#DECBB1] pb-3">
                    <h2 class="font-cinzel text-2xl font-black text-[#4A2200] uppercase">{{ $guild->name }}</h2>
                    <div class="text-xs text-[#795E43] mt-1">
                        Guild Master: <strong class="text-[#D4441D]">{{ $guild->master }}</strong> | 
                        Level: <strong class="text-[#9E5600] font-mono">{{ $guild->guild_lv }}</strong>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2.5 text-center">
                        <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Members</span>
                        <span class="text-sm font-bold font-mono text-[#4A2200]">{{ count($members) }} / {{ $guild->max_member }}</span>
                    </div>
                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2.5 text-center">
                        <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Average Level</span>
                        <span class="text-sm font-bold font-mono text-[#D4441D]">{{ $guild->average_lv ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2.5 text-center">
                        <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Castles Held</span>
                        <span class="text-sm font-bold font-mono text-[#9E5600]">{{ count($castles) }}</span>
                    </div>
                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2.5 text-center">
                        <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Total EXP</span>
                        <span class="text-sm font-bold font-mono text-[#4A2200]">{{ number_format($guild->exp) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Table Panel -->
    <div class="chronicles-panel-ivy pt-8">
        <div class="chronicles-ribbon-header">
            <span>Roster & Members ({{ count($members) }})</span>
        </div>

        <div class="overflow-x-auto mt-2">
            <table class="chronicles-table">
                <thead>
                    <tr>
                        <th>Member Name</th>
                        <th>Job Class</th>
                        <th class="text-center">Base Level</th>
                        <th class="text-center">Job Level</th>
                        <th class="text-right">Guild Position</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $m)
                        <tr>
                            <td>
                                <a href="{{ route('characters.show', $m->name) }}" class="font-cinzel font-bold text-[#4A2200] hover:text-[#D4441D]">
                                    {{ $m->name }}
                                </a>
                            </td>
                            <td>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#FAF4EA] text-[#5A3D28] border border-[#DECBB1]">
                                    {{ \App\Services\Emulator\JobHelper::getName($m->class) }}
                                </span>
                            </td>
                            <td class="text-center font-mono font-bold text-[#D4441D]">Lv. {{ $m->base_level }}</td>
                            <td class="text-center font-mono font-bold text-[#9E5600]">Lv. {{ $m->job_level }}</td>
                            <td class="text-right text-xs font-semibold text-[#5A3D28]">{{ $m->position_name ?? 'Member' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-xs text-[#8C7A68]">No member records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
