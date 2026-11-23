@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Active In Midgard
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Real-time roster of adventurers currently roaming Rune-Midgard
            </p>
        </div>
        <div class="chronicles-status-pill online text-xs py-2 px-4">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span>Total Online: <strong class="text-white">{{ $totalOnline }}</strong></span>
        </div>
    </div>

    <!-- Online Table Panel -->
    <div class="chronicles-panel-ivy pt-6">
        <div class="chronicles-ribbon-header">
            <span>Online Adventurers</span>
        </div>

        <div class="overflow-x-auto mt-2">
            <table class="chronicles-table">
                <thead>
                    <tr>
                        <th>Adventurer</th>
                        <th>Job Class</th>
                        <th class="text-center">Base Level</th>
                        <th class="text-center">Job Level</th>
                        <th>Current Location</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($onlinePlayers as $p)
                        <tr>
                            <td>
                                <a href="{{ route('characters.show', $p->name) }}" class="font-cinzel font-bold text-[#4A2200] hover:text-[#D4441D]">
                                    {{ $p->name }}
                                </a>
                            </td>
                            <td>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#FAF4EA] text-[#5A3D28] border border-[#DECBB1]">
                                    {{ \App\Services\Emulator\JobHelper::getName($p->class) }}
                                </span>
                            </td>
                            <td class="text-center font-mono font-bold text-[#D4441D]">Lv. {{ $p->base_level }}</td>
                            <td class="text-center font-mono font-bold text-[#9E5600]">Lv. {{ $p->job_level }}</td>
                            <td class="font-mono text-xs text-[#5A3D28]">{{ $p->last_map }}.gat</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-xs text-[#8C7A68]">
                                Midgard rests peacefully. No adventurers roaming right now.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
