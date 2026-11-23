@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Guild Directory</h1>
            <p class="text-xs text-slate-400 mt-1">Active player guilds, leadership rosters, and castle conquests.</p>
        </div>
        <a href="{{ route('woe.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-cyan-400 hover:text-cyan-300 text-xs font-semibold flex items-center gap-1.5 transition-colors">
            <i data-lucide="shield" class="w-4 h-4"></i>
            <span>Castle Sieges (WoE)</span>
        </a>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden shadow-xl backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-950/80 text-slate-400 uppercase text-[11px] font-semibold border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-14 text-center">Emblem</th>
                        <th class="py-3.5 px-4">Guild Name</th>
                        <th class="py-3.5 px-4">Guild Master</th>
                        <th class="py-3.5 px-4 text-center">Level</th>
                        <th class="py-3.5 px-4 text-center">Members</th>
                        <th class="py-3.5 px-4 text-right">Guild EXP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($guildList as $g)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-4 text-center">
                                <img src="{{ route('guilds.emblem', $g->guild_id) }}" alt="{{ $g->name }}" class="w-6 h-6 inline-block rounded shadow-sm border border-slate-800" loading="lazy">
                            </td>
                            <td class="py-3 px-4 font-bold text-white hover:text-cyan-400">
                                <a href="{{ route('guilds.show', $g->guild_id) }}">{{ $g->name }}</a>
                            </td>
                            <td class="py-3 px-4 text-slate-300">
                                <a href="{{ route('characters.show', $g->master) }}" class="hover:underline">{{ $g->master }}</a>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-cyan-400">Lv. {{ $g->guild_lv }}</td>
                            <td class="py-3 px-4 text-center text-slate-300">{{ $g->connect_member }} / {{ $g->max_member }}</td>
                            <td class="py-3 px-4 text-right font-mono text-slate-300">{{ number_format($g->exp) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">No guilds found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

