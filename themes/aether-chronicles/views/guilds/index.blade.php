@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Guild Directory & Alliances
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Active battle syndicates, castle conquerors, and member rosters
            </p>
        </div>
        <a href="{{ route('woe.index') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
            <i data-lucide="swords" class="w-3.5 h-3.5 text-[#FFD700]"></i>
            <span>War of Emperium</span>
        </a>
    </div>

    <!-- Guilds Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($guilds as $g)
            <div class="chronicles-panel-ivy flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] flex items-center justify-center shrink-0">
                            <img src="{{ route('guilds.emblem', $g->guild_id) }}" alt="Guild Emblem" class="w-7 h-7 rounded border border-[#DECBB1]" loading="lazy">
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-cinzel text-base font-bold text-[#4A2200] truncate">
                                <a href="{{ route('guilds.show', $g->guild_id) }}" class="hover:text-[#D4441D]">
                                    {{ $g->name }}
                                </a>
                            </h3>
                            <span class="text-xs text-[#795E43]">
                                Master: <strong class="text-[#4A2200]">{{ $g->master }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-xl p-3 grid grid-cols-3 gap-2 text-center text-xs">
                        <div>
                            <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Level</span>
                            <span class="text-sm font-bold font-mono text-[#D4441D]">{{ $g->guild_lv }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Members</span>
                            <span class="text-sm font-bold font-mono text-[#5A3D28]">{{ $g->connect_member ?? 0 }} / {{ $g->max_member }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Castles</span>
                            <span class="text-sm font-bold font-mono text-[#9E5600]">{{ $g->castles_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-[#DECBB1] text-right">
                    <a href="{{ route('guilds.show', $g->guild_id) }}" class="text-xs font-bold text-[#8B5A2B] hover:text-[#D4441D] inline-flex items-center gap-1">
                        <span>Inspect Guild</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full chronicles-panel-ivy text-center py-12">
                <i data-lucide="shield-alert" class="w-12 h-12 text-[#A88B57] mx-auto mb-3"></i>
                <h3 class="font-cinzel text-lg font-bold text-[#4A2200]">No Guilds Registered</h3>
                <p class="text-xs text-[#795E43] mt-1">
                    Forge an Emperium in game to establish your guild dynasty.
                </p>
            </div>
        @endforelse
    </div>

</div>
@endsection
