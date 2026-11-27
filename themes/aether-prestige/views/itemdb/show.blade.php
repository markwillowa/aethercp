@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
    <a href="{{ route('itemdb.index') }}" class="inline-flex items-center gap-2 text-xs text-[#AAB3C1] hover:text-[#C6A76A] font-semibold transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to Database Search</span>
    </a>

    <div class="prestige-card-elevated p-8 shadow-2xl space-y-6">
        <div class="flex items-start gap-5 border-b border-white/[0.08] pb-6">
            <div class="w-16 h-16 rounded-2xl bg-[#101725] border border-[#C6A76A]/40 flex items-center justify-center shrink-0">
                <i data-lucide="package" class="w-8 h-8 text-[#C6A76A]"></i>
            </div>
            <div class="flex-grow">
                <div class="flex items-center gap-3">
                    <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-white">{{ $item->name_english }}</h1>
                    <span class="px-2.5 py-0.5 rounded text-xs font-mono font-bold bg-[#101725] text-[#C6A76A] border border-[#C6A76A]/30">
                        #{{ $item->id }}
                    </span>
                </div>
                <p class="text-xs text-[#788394] mt-1">{{ $item->name_aegis ?? $item->name_japanese ?? '' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="bg-[#101725] p-3.5 rounded-xl border border-white/[0.06]">
                <span class="text-[#788394] block mb-0.5 uppercase tracking-wider text-[10px]">Type @if(!empty($item->category))({{ $item->category }})@endif</span>
                <strong class="text-white font-semibold">{{ $item->type ?? 'Normal' }}</strong>
            </div>
            <div class="bg-[#101725] p-3.5 rounded-xl border border-white/[0.06]">
                <span class="text-[#788394] block mb-0.5 uppercase tracking-wider text-[10px]">Slots</span>
                <strong class="text-[#D8BE8A] font-mono">{{ $item->slots ?? 0 }}</strong>
            </div>
            <div class="bg-[#101725] p-3.5 rounded-xl border border-white/[0.06]">
                <span class="text-[#788394] block mb-0.5 uppercase tracking-wider text-[10px]">Weight</span>
                <strong class="text-white font-mono">{{ $item->weight ?? 0 }}</strong>
            </div>
            <div class="bg-[#101725] p-3.5 rounded-xl border border-white/[0.06]">
                <span class="text-[#788394] block mb-0.5 uppercase tracking-wider text-[10px]">Buy Price</span>
                <strong class="text-[#C6A76A] font-mono">{{ number_format($item->price_buy ?? 0) }} Z</strong>
            </div>
        </div>

        @if(!empty($item->script))
            <div class="space-y-2">
                <h3 class="font-cinzel text-xs font-bold uppercase tracking-wider text-[#AAB3C1]">Item Script & Modifiers</h3>
                <pre class="bg-[#101725] border border-white/[0.08] p-4 rounded-xl text-xs text-[#2DD4BF] font-mono overflow-x-auto">{{ $item->script }}</pre>
            </div>
        @endif
    </div>
</div>
@endsection

