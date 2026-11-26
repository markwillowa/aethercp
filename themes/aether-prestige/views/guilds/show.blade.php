@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <a href="{{ route('guilds.index') }}" class="inline-flex items-center gap-2 text-xs text-[#AAB3C1] hover:text-[#C6A76A] font-semibold transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to Guild Directory</span>
    </a>

    <!-- Guild Banner -->
    <div class="prestige-card-elevated p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-2xl">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-[#101725] border border-white/10 flex items-center justify-center shadow-xl p-2.5">
                <img src="{{ route('guilds.emblem', $guild->guild_id) }}" alt="{{ $guild->name }}" class="w-10 h-10 object-contain image-rendering-pixelated">
            </div>
            <div class="space-y-1.5">
                <h1 class="font-cinzel text-3xl font-extrabold text-white tracking-wider uppercase">{{ $guild->name }}</h1>
                <p class="text-xs text-[#788394] flex items-center gap-3 flex-wrap">
                    <span>Guild Master: <a href="{{ route('characters.show', $guild->master) }}" class="text-[#C6A76A] font-bold hover:underline font-cinzel">{{ $guild->master }}</a></span>
                    <span>â€¢</span>
                    <span>Level <strong class="text-white font-mono">{{ $guild->guild_lv }}</strong></span>
                    <span>â€¢</span>
                    <span>Average Level <strong class="text-[#D8BE8A] font-mono">{{ $guild->average_lv }}</strong></span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-6 text-xs bg-[#101725] p-5 rounded-2xl border border-white/[0.08]">
            <div>
                <span class="text-[#788394] uppercase tracking-wider text-[10px] font-semibold block">Members</span>
                <strong class="text-sm font-bold text-white font-mono">{{ $guild->connect_member }} / {{ $guild->max_member }}</strong>
            </div>
            <div class="border-l border-white/[0.08] pl-6">
                <span class="text-[#788394] uppercase tracking-wider text-[10px] font-semibold block">Total Guild EXP</span>
                <strong class="text-sm font-bold text-[#C6A76A] font-mono">{{ number_format($guild->exp) }}</strong>
            </div>
        </div>
    </div>

    <!-- Conquered Castles (if any) -->
    @if(!empty($castles) && count($castles) > 0)
        <div class="prestige-card p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-white/[0.08] pb-3">
                <i data-lucide="castle" class="w-4 h-4 text-[#C6A76A]"></i>
                <h3 class="font-cinzel text-xs font-bold uppercase tracking-wider text-white">Fortress Conquests</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($castles as $c)
                    <div class="bg-[#101725] border border-white/[0.08] p-4 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="font-cinzel text-xs font-bold text-white block">Castle #{{ $c->castle_id }}</span>
                            <span class="text-[10px] text-[#AAB3C1]">Defense: {{ $c->defense ?? 0 }} â€¢ Economy: {{ $c->economy ?? 0 }}</span>
                        </div>
                        <i data-lucide="shield" class="w-5 h-5 text-[#C6A76A]"></i>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Guild Members Table -->
    <div class="prestige-card overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-white/[0.08] bg-[#101725]/60 flex items-center justify-between">
            <h2 class="font-cinzel text-xs font-bold uppercase tracking-wider text-white">Guild Roster ({{ count($members) }} Members)</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="prestige-table">
                <thead>
                    <tr>
                        <th>Character Name</th>
                        <th class="text-center">Class</th>
                        <th class="text-center">Base Level</th>
                        <th class="text-center">Job Level</th>
                        <th class="text-right">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $m)
                        <tr>
                            <td>
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('characters.show', $m->name) }}" class="font-cinzel text-sm font-bold text-white hover:text-[#C6A76A]">
                                        {{ $m->name }}
                                    </a>
                                    @if((int)($guild->char_id ?? 0) === (int)($m->char_id ?? 0))
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/40">Guild Master</span>
                                    @endif
                                    @foreach(array_slice($memberBadges[(int) ($m->char_id ?? 0)] ?? [], 0, 2) as $mb)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border" style="color: {{ $mb['color'] }}; border-color: {{ $mb['color'] }}66; background-color: {{ $mb['color'] }}1f;">{{ $mb['label'] }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] bg-[#101725] text-[#D8BE8A] border border-white/10">
                                    {{ \App\Services\Emulator\JobHelper::getName((int) $m->class) }}
                                </span>
                            </td>
                            <td class="text-center font-mono font-bold text-[#C6A76A]">Lv {{ $m->base_level ?? $m->lv }}</td>
                            <td class="text-center font-mono font-bold text-[#D8BE8A]">Lv {{ $m->job_level ?? '-' }}</td>
                            <td class="text-right text-xs">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ ($m->online ?? 0) > 0 ? 'bg-emerald-950/80 text-emerald-400 border border-emerald-500/30' : 'text-[#788394]' }}">
                                    {{ ($m->online ?? 0) > 0 ? 'Online' : 'Offline' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-xs text-[#788394]">No member records discovered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
