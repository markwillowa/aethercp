@extends('layouts.app')

@section('title', ($item->name_english ?? $item->name_japanese) . ' - Item Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 py-2">

    <!-- Header & Return -->
    <div class="p-5 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-wide uppercase flex items-center gap-2">
                <i data-lucide="shield" class="w-5 h-5 text-cyan-400"></i>
                <span>Item Inspection</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Detailed stats, equip locations, and card capabilities
            </p>
        </div>
        <a href="{{ route('itemdb.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 py-2 px-4 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-colors shrink-0">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Return to Codex</span>
        </a>
    </div>

    <!-- Item Card Sheet -->
    <div class="p-6 sm:p-8 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-2xl space-y-6">
        <div class="flex items-center gap-5 border-b border-slate-800 pb-5">
            <div class="w-24 h-24 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0 shadow-inner overflow-hidden p-2 relative group">
                <img src="https://static.divine-pride.net/images/items/collection/{{ $item->id }}.png"
                     alt="{{ $item->name_english ?? $item->name_japanese }}"
                     class="max-h-20 max-w-full object-contain relative z-10 transition-transform group-hover:scale-110 duration-200"
                     onerror="this.onerror=null; this.src='https://static.divine-pride.net/images/items/item/{{ $item->id }}.png';">
                <div class="absolute bottom-1.5 right-1.5 z-10 w-7 h-7 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center p-0.5 shadow">
                    <img src="https://static.divine-pride.net/images/items/item/{{ $item->id }}.png" alt="" class="w-5 h-5 object-contain" onerror="this.style.display='none';">
                </div>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">
                    {{ $item->name_english ?? $item->name_japanese }}
                </h2>
                <div class="text-xs text-slate-400 mt-1 flex items-center gap-2 flex-wrap">
                    <span>Item ID: <strong class="font-mono text-cyan-400">#{{ $item->id }}</strong></span>
                    <span>•</span>
                    <span>Type: <strong class="text-slate-200">{{ $item->type ?? 'Item' }}</strong></span>
                    @if(!empty($item->slots) && $item->slots > 0)
                        <span>•</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-cyan-950/60 text-cyan-400 border border-cyan-500/30">
                            Slots: [{{ $item->slots }}]
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                <span class="text-[10px] uppercase text-slate-400 block font-semibold">Attack Power</span>
                <span class="text-base font-bold font-mono text-rose-400 mt-1 block">{{ $item->atk ?? '0' }}</span>
            </div>
            <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                <span class="text-[10px] uppercase text-slate-400 block font-semibold">Defense</span>
                <span class="text-base font-bold font-mono text-emerald-400 mt-1 block">{{ $item->def ?? '0' }}</span>
            </div>
            <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                <span class="text-[10px] uppercase text-slate-400 block font-semibold">Weight</span>
                <span class="text-base font-bold font-mono text-cyan-400 mt-1 block">{{ number_format(($item->weight ?? 0) / 10, 1) }}</span>
            </div>
            <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                <span class="text-[10px] uppercase text-slate-400 block font-semibold">Required Lv</span>
                <span class="text-base font-bold font-mono text-slate-200 mt-1 block">{{ $item->equip_level_min ?? 0 }}</span>
            </div>
        </div>

        <!-- Description / Lore Box -->
        @if(!empty($item->description) || !empty($item->script))
            <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 space-y-2 text-xs">
                <h4 class="font-bold text-white uppercase text-[11px] flex items-center gap-1.5">
                    <i data-lucide="scroll" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>Item Lore &amp; In-Game Script</span>
                </h4>
                <div class="text-slate-300 leading-relaxed whitespace-pre-wrap font-mono text-[11px]">
                    {{ $item->description ?? $item->script }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
