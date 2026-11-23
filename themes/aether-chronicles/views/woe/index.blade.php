@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                War of Emperium & Castles
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Conquered realm fortresses, defending syndicates, and siege schedules
            </p>
        </div>
        <div class="chronicles-status-pill woe text-xs py-1.5 px-3">
            <span>Siege: Wed & Sat 20:00 - 22:00</span>
        </div>
    </div>

    <!-- Castles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($castles as $c)
            <div class="chronicles-panel-ivy flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-[#FAF4EA] text-[#9E5600] border border-[#DECBB1]">
                            Castle #{{ $c->castle_id }}
                        </span>
                        @if($c->guild_id > 0)
                            <img src="{{ route('guilds.emblem', $c->guild_id) }}" alt="Guild Emblem" class="w-7 h-7 rounded border border-[#DECBB1]" loading="lazy">
                        @endif
                    </div>

                    <h3 class="font-cinzel text-base font-bold text-[#4A2200]">
                        {{ $castleNames[$c->castle_id] ?? ("Fortress Agit #{$c->castle_id}") }}
                    </h3>

                    <div class="text-xs text-[#795E43] space-y-1 bg-[#FAF4EA] p-3 rounded-lg border border-[#DECBB1]">
                        <p>Defending Guild:
                            @if($c->guild_id > 0 && !empty($c->guild_name))
                                <a href="{{ route('guilds.show', $c->guild_id) }}" class="font-cinzel font-bold text-[#D4441D] hover:underline">{{ $c->guild_name }}</a>
                            @else
                                <span class="text-[#8C7A68] italic">Unclaimed</span>
                            @endif
                        </p>
                        @if(!empty($c->guild_master))
                            <p>Guild Master: <strong class="text-[#4A2200]">{{ $c->guild_master }}</strong></p>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-[#DECBB1] flex items-center justify-between text-xs">
                    <span class="text-[#8C7A68]">Economy: <strong class="text-[#4A2200]">{{ $c->economy ?? 0 }}</strong></span>
                    <span class="text-[#8C7A68]">Defense: <strong class="text-[#4A2200]">{{ $c->defense ?? 0 }}</strong></span>
                </div>
            </div>
        @empty
            <div class="col-span-full chronicles-panel-ivy text-center py-12">
                <i data-lucide="castle" class="w-12 h-12 text-[#A88B57] mx-auto mb-3"></i>
                <h3 class="font-cinzel text-lg font-bold text-[#4A2200]">All Castles Peaceful</h3>
                <p class="text-xs text-[#795E43] mt-1">Fortress gates are sealed until the next scheduled War of Emperium.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
