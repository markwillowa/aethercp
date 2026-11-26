@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Adventurer Record
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Verified character attributes, combat capabilities, and guild standing
            </p>
        </div>
        <a href="{{ route('rankings.index') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#FFD700]"></i>
            <span>Hall of Fame</span>
        </a>
    </div>

    <!-- Character Profile Card -->
    <div class="chronicles-panel-ivy pt-8">
        <div class="chronicles-ribbon-header">
            <span>{{ $char->name }}</span>
        </div>

        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mt-2">
            <!-- Left Character Illustration Box -->
            <div class="w-44 h-56 bg-gradient-to-b from-[#FAF4EA] to-[#EFE4D0] border-2 border-[#DECBB1] rounded-2xl flex flex-col items-center justify-center p-4 shrink-0 shadow-inner">
                <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/high_wizard.png']) }}" 
                     alt="Character Avatar" 
                     class="h-40 object-contain filter drop-shadow">
                <span class="mt-2 text-xs font-cinzel font-bold text-[#4A2200]">
                    {{ \App\Services\Emulator\JobHelper::getName($char->class) }}
                </span>
            </div>

            <!-- Right Specs & Vitals -->
            <div class="flex-1 space-y-6 w-full">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#DECBB1] pb-3">
                    <div>
                        <h2 class="font-cinzel text-2xl font-black text-[#4A2200] uppercase">{{ $char->name }}</h2>
                        <div class="text-xs text-[#795E43] mt-1">
                            Base Level: <strong class="text-[#D4441D] font-mono">Lv. {{ $char->base_level }}</strong> | 
                            Job Level: <strong class="text-[#9E5600] font-mono">Lv. {{ $char->job_level }}</strong>
                        </div>
                    </div>
                    <div>
                        <span class="px-2.5 py-1 rounded text-xs font-bold uppercase {{ $char->online > 0 ? 'bg-[#1E4620] text-[#A5D6A7] border border-[#4CAF50]' : 'bg-[#FAF4EA] text-[#8C7A68] border border-[#DECBB1]' }}">
                            {{ $char->online > 0 ? 'Active Online' : 'Offline' }}
                        </span>
                    </div>
                </div>

                <!-- Stats Grid (STR, AGI, VIT, INT, DEX, LUK) -->
                <div>
                    <h3 class="text-xs font-cinzel font-bold text-[#4A3319] uppercase mb-2">Combat Attributes</h3>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 text-center text-xs">
                        <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2">
                            <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">STR</span>
                            <span class="text-sm font-bold font-mono text-[#4A2200]">{{ $char->str }}</span>
                        </div>
                        <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2">
                            <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">AGI</span>
                            <span class="text-sm font-bold font-mono text-[#4A2200]">{{ $char->agi }}</span>
                        </div>
                        <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2">
                            <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">VIT</span>
                            <span class="text-sm font-bold font-mono text-[#4A2200]">{{ $char->vit }}</span>
                        </div>
                        <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2">
                            <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">INT</span>
                            <span class="text-sm font-bold font-mono text-[#4A2200]">{{ $char->int }}</span>
                        </div>
                        <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2">
                            <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">DEX</span>
                            <span class="text-sm font-bold font-mono text-[#4A2200]">{{ $char->dex }}</span>
                        </div>
                        <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2">
                            <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">LUK</span>
                            <span class="text-sm font-bold font-mono text-[#4A2200]">{{ $char->luk }}</span>
                        </div>
                    </div>
                </div>

                <!-- Vitals & World Details -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-3">
                        <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Max HP / SP</span>
                        <span class="text-sm font-bold font-mono text-[#D4441D]">{{ number_format($char->max_hp) }} / {{ number_format($char->max_sp) }}</span>
                    </div>

                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-3">
                        <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Carried Zeny</span>
                        <span class="text-sm font-bold font-mono text-[#9E5600]">{{ number_format($char->zeny) }} Z</span>
                    </div>

                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-3">
                        <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Guild Affiliation</span>
                        <span class="text-sm font-bold text-[#4A2200]">
                            @if($guild)
                                <a href="{{ route('guilds.show', $guild->guild_id) }}" class="text-[#D4441D] hover:underline font-cinzel">{{ $guild->name }}</a>
                            @else
                                <span class="text-[#8C7A68] font-normal italic">None</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipped Gear (Chronicles Parchment Style) -->
    @if(($privacy->show_equipments ?? true) || ($isOwnerOrAdmin ?? false))
        <div class="chronicles-panel-ivy space-y-4">
            <h3 class="text-xs font-cinzel font-bold text-[#4A3319] uppercase border-b border-[#DECBB1] pb-2">
                Equipped Gear ({{ count($equippedItems ?? []) }})
            </h3>
            @if(!empty($equippedItems))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($equippedItems as $eq)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-[#FAF4EA] border border-[#DECBB1]">
                            <div class="w-10 h-10 rounded-xl bg-white border border-[#DECBB1] flex items-center justify-center shrink-0">
                                <img src="{{ $eq['icon_url'] }}" alt="{{ $eq['name'] }}" class="w-7 h-7 object-contain" onerror="this.src='/images/item-placeholder.png'">
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#9E5600] block">{{ $eq['slot_label'] }}</span>
                                <a href="{{ route('itemdb.show', $eq['nameid']) }}" class="text-xs font-bold text-[#4A2200] hover:text-[#D4441D] truncate block">
                                    @if($eq['refine'] > 0)
                                        <span class="text-[#D4441D]">+{{ $eq['refine'] }}</span>
                                    @endif
                                    {{ $eq['name'] }}{{ $eq['slots'] > 0 ? ' [' . $eq['slots'] . ']' : '' }}
                                </a>
                                @if(!empty($eq['cards']))
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($eq['cards'] as $card)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-[#EFE4D0] text-[#5A3D28] border border-[#DECBB1]">{{ $card['name'] }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-[#8C7A68] text-center py-4">No equipment currently equipped on this character.</p>
            @endif
        </div>
    @endif

    <!-- Verified Ranking Event History -->
    @if(!empty($rankingSummary['history']))
        <div class="chronicles-panel-ivy space-y-3">
            <h3 class="text-xs font-cinzel font-bold text-[#4A3319] uppercase border-b border-[#DECBB1] pb-2">
                Verified Ranking Event History
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-[#8C7A68] uppercase text-[10px] border-b border-[#DECBB1]">
                        <tr>
                            <th class="py-2 px-3">Event Type</th>
                            <th class="py-2 px-3">Value</th>
                            <th class="py-2 px-3">Recorded At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DECBB1]/60">
                        @foreach($rankingSummary['history'] as $evt)
                            @php
                                $eventLabel = match ($evt->event_type) {
                                    'pvp_kill' => 'PvP Kills',
                                    'pvp_death' => 'PvP Deaths',
                                    'mvp_kill' => 'MVP Kills',
                                    'base_level' => 'Base Level',
                                    'job_level' => 'Job Level',
                                    default => \Illuminate\Support\Str::headline((string) $evt->event_type),
                                };
                            @endphp
                            <tr>
                                <td class="py-2 px-3 font-bold text-[#9E5600]">{{ $eventLabel }}</td>
                                <td class="py-2 px-3 font-mono text-[#4A2200]">{{ number_format((int) $evt->value) }}</td>
                                <td class="py-2 px-3 text-[#795E43]">{{ \Carbon\Carbon::parse($evt->occurred_at)->format('M d, Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
