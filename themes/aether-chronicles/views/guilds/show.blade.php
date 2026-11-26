@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="shield" class="w-6 h-6 text-[#FFD700]"></i>
                <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                    Guild Dossier: {{ $guild->name }}
                </h1>
            </div>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Alliance hierarchy, occupied castles, and roster composition
            </p>
        </div>
        <a href="{{ route('guilds.index') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#FFD700]"></i>
            <span>All Guilds</span>
        </a>
    </div>

    <!-- Guild Profile Sheet -->
    <div class="chronicles-panel p-6 shadow-2xl">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <div class="w-28 h-28 bg-[#181120] border-2 border-[#5A4A68] rounded-2xl flex items-center justify-center p-4 shrink-0 shadow-lg">
                @if($guild->guild_id > 0)
                    <img src="{{ route('guilds.emblem', $guild->guild_id) }}" alt="Guild Emblem" class="w-16 h-16 rounded" loading="lazy">
                @else
                    <i data-lucide="shield" class="w-12 h-12 text-[#D4AF37]"></i>
                @endif
            </div>

            <div class="flex-1 space-y-5 w-full">
                <div class="border-b border-[#3E324A] pb-4">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h2 class="font-cinzel text-2xl font-black text-[#F3E5AB] uppercase">{{ $guild->name }}</h2>
                        @foreach($guildBadges ?? [] as $badge)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border inline-flex items-center gap-1"
                                  style="color: {{ $badge['color'] }}; border-color: {{ $badge['color'] }}66; background-color: {{ $badge['color'] }}1f;">
                                {{ $badge['label'] }}
                            </span>
                        @endforeach
                    </div>
                    <div class="text-xs text-[#A396AD] mt-1 flex flex-wrap items-center gap-4">
                        <span>Guild Master:
                            @if(!empty($guild->master_char_name))
                                <a href="{{ route('characters.show', $guild->master_char_name) }}" class="font-bold text-[#FFD700] hover:underline">{{ $guild->master }}</a>
                            @else
                                <strong class="text-[#FFD700]">{{ $guild->master }}</strong>
                            @endif
                        </span>
                        <span>Level: <strong class="text-[#E5A823] font-mono">Lv. {{ $guild->guild_lv }}</strong></span>
                        <span>Guild ID: <strong class="text-white font-mono">#{{ $guild->guild_id }}</strong></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Members</span>
                        <span class="text-base font-bold font-mono text-white">{{ count($members) }} / {{ $guild->max_member }}</span>
                    </div>
                    <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Average Level</span>
                        <span class="text-base font-bold font-mono text-[#FFD700]">{{ $guild->average_lv ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Castles Held</span>
                        <span class="text-base font-bold font-mono text-[#E5A823]">{{ count($castles) }}</span>
                    </div>
                    <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Total Guild EXP</span>
                        <span class="text-base font-bold font-mono text-[#4ADE80]">{{ number_format($guild->exp) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Table Panel -->
    <div class="chronicles-panel shadow-2xl">
        <div class="chronicles-panel-header px-5 py-3">
            <span class="chronicles-panel-title">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>Guild Roster &amp; Members ({{ count($members) }})</span>
            </span>
        </div>

        <div class="p-4 sm:p-6 overflow-x-auto">
            <table class="chronicles-table">
                <thead>
                    <tr>
                        <th>Member Name</th>
                        <th class="text-center">Job Class</th>
                        <th class="text-center">Base Level</th>
                        <th class="text-center">Job Level</th>
                        <th class="text-right">Guild Position</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $m)
                        <tr class="hover:bg-white/[0.04] transition-colors">
                            <td>
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('characters.show', $m->name) }}" class="font-cinzel font-bold text-[#F3E5AB] hover:text-[#FFD700] transition-colors">
                                        {{ $m->name }}
                                    </a>
                                    @if((int)($guild->char_id ?? 0) === (int)($m->char_id ?? 0))
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#D4AF37]/20 text-[#FFD700] border border-[#D4AF37]/40">Guild Master</span>
                                    @endif
                                    @foreach(array_slice($memberBadges[(int) ($m->char_id ?? 0)] ?? [], 0, 2) as $mb)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border"
                                              style="color: {{ $mb['color'] }}; border-color: {{ $mb['color'] }}66; background-color: {{ $mb['color'] }}1f;">
                                            {{ $mb['label'] }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-[#181120] text-[#CBB488] border border-[#5A4A68]">
                                    {{ \App\Services\Emulator\JobHelper::getName($m->class) }}
                                </span>
                            </td>
                            <td class="text-center font-mono font-bold text-[#FFD700]">Lv. {{ $m->base_level ?? $m->lv }}</td>
                            <td class="text-center font-mono font-bold text-[#E5A823]">Lv. {{ $m->job_level }}</td>
                            <td class="text-right text-xs font-semibold">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ ($m->online ?? 0) > 0 ? 'bg-emerald-950 text-emerald-300 border border-emerald-500/40' : 'text-[#A396AD]' }}">
                                    {{ ($m->online ?? 0) > 0 ? 'Online' : ($m->position_name ?? 'Offline') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-xs text-[#8C7A68]">No member records found for this guild.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
