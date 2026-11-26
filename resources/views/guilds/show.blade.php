@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <a href="{{ route('guilds.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-cyan-400 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to Guild Directory</span>
    </a>

    <!-- Guild Header Banner -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 backdrop-blur-xl shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center shadow-lg p-2">
                <img src="{{ route('guilds.emblem', $guild->guild_id) }}" alt="{{ $guild->name }}" class="w-10 h-10 object-contain image-rendering-pixelated">
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">{{ $guild->name }}</h1>
                    @foreach($guildBadges as $badge)
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold border inline-flex items-center gap-1"
                              style="color: {{ $badge['color'] }}; border-color: {{ $badge['color'] }}66; background-color: {{ $badge['color'] }}1f;">
                            @if(!empty($badge['icon_url']))
                                <img src="{{ $badge['icon_url'] }}" alt="" class="w-3.5 h-3.5 object-contain">
                            @elseif(!empty($badge['icon']))
                                <i data-lucide="{{ $badge['icon'] }}" class="w-3.5 h-3.5"></i>
                            @endif
                            <span>{{ $badge['label'] }}</span>
                        </span>
                    @endforeach
                </div>
                <p class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                    <span>Guild Master: <strong class="text-cyan-400">{{ $guild->master }}</strong></span>
                    <span>•</span>
                    <span>Level <strong class="text-slate-200">{{ $guild->guild_lv }}</strong></span>
                    <span>•</span>
                    <span>Average Level <strong class="text-slate-200">{{ $guild->average_lv }}</strong></span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs bg-slate-950/70 p-4 rounded-2xl border border-slate-800">
            <div>
                <span class="text-slate-500 block">Members</span>
                <strong class="text-sm font-bold text-white">{{ $guild->connect_member }} / {{ $guild->max_member }}</strong>
            </div>
            <div class="border-l border-slate-800 pl-4">
                <span class="text-slate-500 block">Total Experience</span>
                <strong class="text-sm font-bold text-amber-400 font-mono">{{ number_format($guild->exp) }}</strong>
            </div>
        </div>
    </div>

    <!-- Guild Member Roster -->
    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl backdrop-blur-sm">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-white">Guild Members ({{ count($members) }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-950/80 text-slate-400 uppercase text-[10px] font-semibold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Character Name</th>
                        <th class="py-3 px-4 text-center">Class ID</th>
                        <th class="py-3 px-4 text-center">Base / Job</th>
                        <th class="py-3 px-4 text-right">Contributed EXP</th>
                        <th class="py-3 px-4 text-center">Online</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($members as $m)
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3 px-4 font-bold text-white">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('characters.show', $m->name) }}" class="hover:text-cyan-400">{{ $m->name }}</a>
                                    @foreach(array_slice($memberBadges[(int) ($m->char_id ?? 0)] ?? [], 0, 2) as $mb)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border"
                                              style="color: {{ $mb['color'] }}; border-color: {{ $mb['color'] }}66; background-color: {{ $mb['color'] }}1f;">
                                            {{ $mb['label'] }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center font-mono text-slate-400">{{ $m->class }}</td>
                            <td class="py-3 px-4 text-center font-bold text-slate-200">{{ $m->lv }}</td>
                            <td class="py-3 px-4 text-right font-mono text-slate-300">{{ number_format($m->exp) }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $m->online > 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                                    {{ $m->online > 0 ? 'Online' : 'Offline' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-500">No member records recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

