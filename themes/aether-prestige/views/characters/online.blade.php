@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <div class="flex items-center justify-between border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Active In Midgard
            </h1>
            <p class="text-xs text-[#788394] mt-1">Live adventurers currently traversing dungeons, fields, and capitals.</p>
        </div>
        <div class="flex items-center gap-2.5 bg-[#151B26] border border-white/[0.08] px-4 py-2 rounded-xl text-xs">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-[0_0_8px_#34d399]"></span>
            <span class="text-[#AAB3C1]">Total Adventurers Online:</span>
            <strong class="text-[#C6A76A] font-mono font-bold text-sm">{{ $totalOnline }}</strong>
        </div>
    </div>

    <div class="prestige-card overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="prestige-table">
                <thead>
                    <tr>
                        <th>Adventurer Name</th>
                        <th>Job Class</th>
                        <th class="text-center">Base Level</th>
                        <th class="text-center">Job Level</th>
                        <th>Current Map Location</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($onlinePlayers as $p)
                        <tr>
                            <td>
                                <a href="{{ route('characters.show', $p->name) }}" class="font-cinzel font-bold text-white hover:text-[#C6A76A] transition-colors">
                                    {{ $p->name }}
                                </a>
                            </td>
                            <td>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-[#101725] text-[#D8BE8A] border border-white/10">
                                    {{ \App\Services\Emulator\JobHelper::getName($p->class) }}
                                </span>
                            </td>
                            <td class="text-center font-mono font-bold text-[#C6A76A]">Lv {{ $p->base_level }}</td>
                            <td class="text-center font-mono font-bold text-[#D8BE8A]">Lv {{ $p->job_level }}</td>
                            <td class="font-mono text-xs text-[#AAB3C1]">{{ $p->last_map }}.gat</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-xs text-[#788394]">
                                No adventurers are currently connected to the game realm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

