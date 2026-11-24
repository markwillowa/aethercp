@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <!-- Top Wood Board Header -->
    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="shield" class="w-6 h-6 text-[#FFD700]"></i>
                <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                    Guild Directory &amp; Alliances
                </h1>
            </div>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Active battle syndicates, castle conquerors, and member rosters across Midgard
            </p>
        </div>
        <a href="{{ route('woe.index') }}" class="chronicles-btn-dark text-xs py-2 px-4 w-auto shrink-0 flex items-center gap-2">
            <i data-lucide="swords" class="w-4 h-4 text-[#FFD700]"></i>
            <span>War of Emperium</span>
        </a>
    </div>

    <!-- Guilds Grid -->
    @php
        $guilds = $guildList ?? $guilds ?? [];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($guilds as $g)
            <div class="chronicles-panel p-5 flex flex-col justify-between space-y-4 hover:border-[#D4AF37]/60 transition-all shadow-lg">
                <div class="space-y-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded bg-[#1F1926] border border-[#5A4A68] flex items-center justify-center shrink-0 shadow-inner">
                            @if($g->guild_id > 0)
                                <img src="{{ route('guilds.emblem', $g->guild_id) }}" alt="Guild Emblem" class="w-8 h-8 rounded" loading="lazy">
                            @else
                                <i data-lucide="shield" class="w-6 h-6 text-[#D4AF37]"></i>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-cinzel text-base font-bold text-[#F3E5AB] truncate">
                                <a href="{{ route('guilds.show', $g->guild_id) }}" class="hover:text-[#FFD700] transition-colors">
                                    {{ $g->name }}
                                </a>
                            </h3>
                            <span class="text-xs text-[#A396AD]">
                                Master: <strong class="text-[#E2D9EB]">{{ $g->master }}</strong>
                            </span>
                        </div>
                    </div>

                    <!-- 3-Column Metrics Box -->
                    <div class="bg-[#181120] border border-[#3E324A] rounded-lg p-3 grid grid-cols-3 gap-2 text-center text-xs">
                        <div>
                            <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Level</span>
                            <span class="text-sm font-bold font-mono text-[#FFD700]">Lv. {{ $g->guild_lv }}</span>
                        </div>
                        <div class="border-x border-[#3E324A]">
                            <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Members</span>
                            <span class="text-sm font-bold font-mono text-[#E2D9EB]">{{ $g->connect_member ?? 0 }} / {{ $g->max_member }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Castles</span>
                            <span class="text-sm font-bold font-mono text-[#E5A823]">{{ $g->castles_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-[#3E324A] flex items-center justify-between">
                    <span class="text-[10px] text-[#A396AD] font-mono">ID #{{ $g->guild_id }}</span>
                    <a href="{{ route('guilds.show', $g->guild_id) }}" class="text-xs font-bold font-cinzel text-[#E5A823] hover:text-[#FFD700] inline-flex items-center gap-1 transition-colors">
                        <span>Inspect Guild</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full chronicles-panel text-center py-16 p-6">
                <i data-lucide="shield-alert" class="w-12 h-12 text-[#A88B57] mx-auto mb-3"></i>
                <h3 class="font-cinzel text-lg font-bold text-[#F3E5AB]">No Guilds Registered</h3>
                <p class="text-xs text-[#A396AD] mt-1 max-w-sm mx-auto">
                    Forge an Emperium in-game to establish your guild dynasty and conquer Castles.
                </p>
            </div>
        @endforelse
    </div>

</div>
@endsection
