@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">War of Emperium & Castles</h1>
            <p class="text-xs text-slate-400 mt-1">Conquered realm castles, defending alliances, and fortress defense metrics.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-950 text-amber-300 border border-amber-500/30">
                Siege Active: Wed & Sat
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($castles as $c)
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 backdrop-blur-md shadow-xl flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                            Castle #{{ $c->castle_id }}
                        </span>
                        @if($c->guild_id > 0)
                            <img src="{{ route('guilds.emblem', $c->guild_id) }}" alt="Guild Emblem" class="w-6 h-6 rounded" loading="lazy">
                        @endif
                    </div>

                    <h3 class="text-lg font-bold text-white">
                        {{ $castleNames[$c->castle_id] ?? ("Agit #{$c->castle_id}") }}
                    </h3>

                    <div class="text-xs text-slate-400 space-y-1">
                        <p>Defending Guild:
                            @if($c->guild_id > 0 && !empty($c->guild_name))
                                <a href="{{ route('guilds.show', $c->guild_id) }}" class="font-bold text-cyan-300 hover:underline">{{ $c->guild_name }}</a>
                            @else
                                <span class="text-slate-500 italic">Unoccupied</span>
                            @endif
                        </p>
                        @if(!empty($c->guild_master))
                            <p>Leader: <span class="text-slate-300">{{ $c->guild_master }}</span></p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs pt-3 border-t border-slate-800 bg-slate-950/60 p-2.5 rounded-xl">
                    <div>
                        <span class="text-slate-500 block text-[10px]">Defense</span>
                        <strong class="text-emerald-400 font-bold">{{ $c->defense ?? 0 }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[10px]">Economy</span>
                        <strong class="text-amber-400 font-bold">{{ $c->economy ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-slate-900/40 border border-slate-800 rounded-2xl p-12 text-center text-slate-500">
                No active castle records found in game database.
            </div>
        @endforelse
    </div>
</div>
@endsection

