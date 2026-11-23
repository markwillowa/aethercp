@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <a href="{{ route('itemdb.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-cyan-400 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to Database Search</span>
    </a>

    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-8 backdrop-blur-xl shadow-2xl space-y-6">
        <div class="flex items-start gap-5 border-b border-slate-800 pb-6">
            <div class="w-16 h-16 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0">
                <i data-lucide="package" class="w-8 h-8 text-cyan-400"></i>
            </div>
            <div class="flex-grow">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-extrabold text-white">{{ $item->name_english }}</h1>
                    <span class="px-2.5 py-0.5 rounded text-xs font-mono font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                        #{{ $item->id }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1">{{ $item->name_aegis ?? $item->name_japanese ?? '' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="bg-slate-950/70 p-3 rounded-xl border border-slate-800/80">
                <span class="text-slate-500 block mb-0.5">Type</span>
                <strong class="text-slate-200">{{ $item->type ?? 'Normal' }}</strong>
            </div>
            <div class="bg-slate-950/70 p-3 rounded-xl border border-slate-800/80">
                <span class="text-slate-500 block mb-0.5">Slots</span>
                <strong class="text-slate-200">{{ $item->slots ?? 0 }}</strong>
            </div>
            <div class="bg-slate-950/70 p-3 rounded-xl border border-slate-800/80">
                <span class="text-slate-500 block mb-0.5">Weight</span>
                <strong class="text-slate-200">{{ $item->weight ?? 0 }}</strong>
            </div>
            <div class="bg-slate-950/70 p-3 rounded-xl border border-slate-800/80">
                <span class="text-slate-500 block mb-0.5">Buy Price</span>
                <strong class="text-amber-400">{{ number_format($item->price_buy ?? 0) }} Z</strong>
            </div>
        </div>

        @if(!empty($item->script))
            <div class="space-y-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Item Script</h3>
                <pre class="bg-slate-950 border border-slate-800 p-4 rounded-xl text-xs text-cyan-300 font-mono overflow-x-auto">{{ $item->script }}</pre>
            </div>
        @endif
    </div>
</div>
@endsection

