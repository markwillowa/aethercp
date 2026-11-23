@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                War of Emperium & Castles
            </h1>
            <p class="text-xs text-[#788394] mt-1">Conquered realm fortresses, defending guilds, and fortress defense metrics.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/40 font-cinzel">
                Siege Schedule: Wed & Sat 20:00 - 22:00
            </span>
        </div>
    </div>

    <!-- Castles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($castles as $c)
            <div class="prestige-card p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-[#101725] text-[#C6A76A] border border-[#C6A76A]/30">
                            Castle #{{ $c->castle_id }}
                        </span>
                        @if($c->guild_id > 0)
                            <img src="{{ route('guilds.emblem', $c->guild_id) }}" alt="Guild Crest" class="w-7 h-7 rounded border border-white/10" loading="lazy">
                        @endif
                    </div>

                    <h3 class="font-cinzel text-lg font-bold text-white">
                        {{ $castleNames[$c->castle_id] ?? ("Agit #{$c->castle_id}") }}
                    </h3>

                    <div class="text-xs text-[#788394] space-y-1.5">
                        <p>Defending Guild:
                            @if($c->guild_id > 0 && !empty($c->guild_name))
                                <a href="{{ route('guilds.show', $c->guild_id) }}" class="font-cinzel font-bold text-[#D8BE8A] hover:underline">{{ $c->guild_name }}</a>
                            @else
                                <span class="text-[#788394] italic">Unoccupied</span>
                            @endif
                        </p>
                        @if(!empty($c->guild_master))
                            <p>Guild Master: <span class="text-white font-medium">{{ $c->guild_master }}</span></p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs pt-4 border-t border-white/[0.06] bg-[#101725] p-3 rounded-xl">
                    <div>
                        <span class="text-[#788394] block text-[10px] uppercase font-semibold">Fortress Defense</span>
                        <strong class="text-emerald-400 font-mono font-bold text-sm">{{ $c->defense ?? 0 }}</strong>
                    </div>
                    <div>
                        <span class="text-[#788394] block text-[10px] uppercase font-semibold">Castle Economy</span>
                        <strong class="text-[#C6A76A] font-mono font-bold text-sm">{{ $c->economy ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full prestige-card p-12 text-center text-xs text-[#788394]">
                No castle records currently active.
            </div>
        @endforelse
    </div>
</div>
@endsection

