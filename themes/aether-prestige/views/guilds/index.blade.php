@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Guild Directory
            </h1>
            <p class="text-xs text-[#788394] mt-1">Sovereign alliances, guild leadership rosters, and castle standings.</p>
        </div>
        <a href="{{ route('woe.index') }}" class="prestige-btn-outline text-xs">
            <i data-lucide="castle" class="w-4 h-4 text-[#C6A76A]"></i>
            <span>Castle Sieges (WoE)</span>
        </a>
    </div>

    <div class="prestige-card overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="prestige-table">
                <thead>
                    <tr>
                        <th class="w-16 text-center">Emblem</th>
                        <th>Guild Crest & Name</th>
                        <th>Guild Master</th>
                        <th class="text-center">Guild Level</th>
                        <th class="text-center">Members</th>
                        <th class="text-right">Accumulated EXP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guildList as $g)
                        <tr>
                            <td class="text-center">
                                <img src="{{ route('guilds.emblem', $g->guild_id) }}" alt="{{ $g->name }}" class="w-7 h-7 inline-block rounded shadow-md border border-white/10" loading="lazy">
                            </td>
                            <td>
                                <a href="{{ route('guilds.show', $g->guild_id) }}" class="font-cinzel text-sm font-bold text-[#F4F5F7] hover:text-[#C6A76A] transition-colors">
                                    {{ $g->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('characters.show', $g->master) }}" class="text-xs text-[#AAB3C1] hover:text-white hover:underline">
                                    {{ $g->master }}
                                </a>
                            </td>
                            <td class="text-center font-mono font-bold text-[#C6A76A]">Lv {{ $g->guild_lv }}</td>
                            <td class="text-center text-xs text-[#AAB3C1]">{{ $g->connect_member }} / {{ $g->max_member }}</td>
                            <td class="text-right font-mono font-bold text-white text-xs">{{ number_format($g->exp) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-[#788394]">
                                No guilds currently registered in the realm registry.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

