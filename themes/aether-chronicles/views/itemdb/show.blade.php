@extends('layouts.app')

@section('content')
<div class="chronicles-container py-8 space-y-6">

    <div class="chronicles-panel p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-xl sm:text-2xl font-bold text-[#F3E5AB] tracking-wide uppercase flex items-center gap-2">
                <i data-lucide="shield" class="w-5 h-5 text-[#D4AF37]"></i>
                <span>Item Inspection</span>
            </h1>
            <p class="text-xs text-[#A396AD] mt-1">
                Detailed stats, equip locations, and card capabilities
            </p>
        </div>
        <a href="{{ route('itemdb.index') }}" class="chronicles-btn-dark py-1.5 px-4 text-xs">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 mr-1"></i>
            <span>Return to Codex</span>
        </a>
    </div>

    <!-- Item Card Sheet -->
    <div class="chronicles-panel max-w-2xl mx-auto overflow-hidden">
        <div class="chronicles-panel-header">
            <span class="chronicles-panel-title">
                <i data-lucide="package"></i>
                <span>{{ $item->name_english ?? $item->name_japanese }}</span>
            </span>
            <span class="font-mono text-xs font-bold text-[#D4AF37]">#{{ $item->id }}</span>
        </div>

        <div class="p-6 space-y-6">
            <div class="flex items-center gap-4 border-b border-[#4A3E56] pb-4">
                <div class="w-16 h-16 rounded-lg bg-[#251D2E] border border-[#5A4A68] flex items-center justify-center shrink-0 shadow-inner">
                    <i data-lucide="shield" class="w-8 h-8 text-[#D4AF37]"></i>
                </div>
                <div>
                    <h2 class="font-cinzel text-lg font-bold text-[#F3E5AB]">
                        {{ $item->name_english ?? $item->name_japanese }}
                    </h2>
                    <div class="text-xs text-[#A396AD] mt-0.5">
                        Item ID: <strong class="font-mono text-[#FFD700]">#{{ $item->id }}</strong> | 
                        Type: <strong class="text-[#E8DCC9]">{{ $item->type ?? 'Item' }}</strong>
                        @if(!empty($item->slots))
                            | Slots: <strong class="font-mono text-[#FFD700]">[{{ $item->slots }}]</strong>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="bg-[#1C1523] border border-[#4A3E56] rounded p-2.5 text-center">
                    <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Attack Power</span>
                    <span class="text-sm font-bold font-mono text-[#E57373]">{{ $item->atk ?? '0' }}</span>
                </div>
                <div class="bg-[#1C1523] border border-[#4A3E56] rounded p-2.5 text-center">
                    <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Defense</span>
                    <span class="text-sm font-bold font-mono text-[#81C784]">{{ $item->def ?? '0' }}</span>
                </div>
                <div class="bg-[#1C1523] border border-[#4A3E56] rounded p-2.5 text-center">
                    <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Weight</span>
                    <span class="text-sm font-bold font-mono text-[#FFD700]">{{ number_format(($item->weight ?? 0) / 10, 1) }}</span>
                </div>
                <div class="bg-[#1C1523] border border-[#4A3E56] rounded p-2.5 text-center">
                    <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Required Lv</span>
                    <span class="text-sm font-bold font-mono text-[#E8DCC9]">{{ $item->equip_level_min ?? 0 }}</span>
                </div>
            </div>

            <!-- Description / Lore Box -->
            @if(!empty($item->description) || !empty($item->script))
                <div class="bg-[#1C1523] border border-[#4A3E56] rounded p-4 space-y-2 text-xs">
                    <h4 class="font-cinzel font-bold text-[#F3E5AB] uppercase text-[11px] flex items-center gap-1.5">
                        <i data-lucide="scroll" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Item Lore &amp; In-Game Script</span>
                    </h4>
                    <div class="text-[#C8BDAA] leading-relaxed whitespace-pre-wrap font-mono text-[11px]">
                        {{ $item->description ?? $item->script }}
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
