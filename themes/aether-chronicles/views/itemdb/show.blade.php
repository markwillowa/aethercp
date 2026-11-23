@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Item Inspection
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Detailed stats, equip locations, and card capabilities
            </p>
        </div>
        <a href="{{ route('itemdb.index') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#FFD700]"></i>
            <span>Return to Codex</span>
        </a>
    </div>

    <!-- Item Card Sheet -->
    <div class="chronicles-panel-ivy pt-8 max-w-2xl mx-auto">
        <div class="chronicles-ribbon-header">
            <span>{{ $item->name_japanese ?? $item->name_english }}</span>
        </div>

        <div class="space-y-6 mt-2">
            <div class="flex items-center gap-4 border-b border-[#DECBB1] pb-4">
                <div class="w-16 h-16 rounded-xl bg-[#FAF4EA] border-2 border-[#DECBB1] flex items-center justify-center shrink-0 shadow-inner">
                    <i data-lucide="shield" class="w-8 h-8 text-[#A88B57]"></i>
                </div>
                <div>
                    <h2 class="font-cinzel text-xl font-black text-[#4A2200]">
                        {{ $item->name_japanese ?? $item->name_english }}
                    </h2>
                    <div class="text-xs text-[#795E43] mt-0.5">
                        Item ID: <strong class="font-mono text-[#D4441D]">#{{ $item->id }}</strong> | 
                        Type: <strong class="text-[#4A2200]">{{ $item->type }}</strong>
                        @if(!empty($item->slots))
                            | Slots: <strong class="font-mono text-[#D4441D]">[{{ $item->slots }}]</strong>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2.5 text-center">
                    <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Attack Power</span>
                    <span class="text-sm font-bold font-mono text-[#D4441D]">{{ $item->atk ?? '0' }}</span>
                </div>
                <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2.5 text-center">
                    <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Defense</span>
                    <span class="text-sm font-bold font-mono text-[#5A3D28]">{{ $item->def ?? '0' }}</span>
                </div>
                <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2.5 text-center">
                    <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Weight</span>
                    <span class="text-sm font-bold font-mono text-[#9E5600]">{{ ($item->weight ?? 0) / 10 }}</span>
                </div>
                <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-lg p-2.5 text-center">
                    <span class="text-[10px] uppercase text-[#8C7A68] block font-bold">Required Level</span>
                    <span class="text-sm font-bold font-mono text-[#4A2200]">{{ $item->equip_level_min ?? 0 }}</span>
                </div>
            </div>

            <!-- Script / Description Box -->
            @if(!empty($item->description) || !empty($item->script))
                <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-xl p-4 space-y-2 text-xs">
                    <h4 class="font-cinzel font-bold text-[#4A2200] uppercase text-[11px]">Item Lore & Effect</h4>
                    <div class="text-[#5A3D28] leading-relaxed whitespace-pre-wrap">
                        {{ $item->description ?? $item->script }}
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
