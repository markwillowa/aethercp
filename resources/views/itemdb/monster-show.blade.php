@extends('layouts.app')

@section('title', $monster['name'] . ' - Monster Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 py-2">

    <div class="p-5 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="skull" class="w-6 h-6 text-cyan-400"></i>
                <h1 class="text-xl sm:text-2xl font-bold text-white tracking-wide uppercase">
                    {{ $monster['name'] }}
                </h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Monster Codex ID #{{ $monster['id'] }} • {{ $monster['race'] }} • {{ $monster['element'] }}
            </p>
        </div>
        <a href="{{ route('database.monsters') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 py-2 px-4 rounded-xl text-xs font-semibold flex items-center gap-2 transition-colors shrink-0">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>All Monsters</span>
        </a>
    </div>

    <!-- Monster Dossier Panel -->
    <div class="p-6 sm:p-8 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-2xl space-y-6">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <!-- Sprite Frame -->
            <div class="w-44 h-44 bg-slate-950 border border-slate-800 rounded-2xl flex items-center justify-center p-4 shrink-0 shadow-lg relative overflow-hidden">
                <img src="https://static.divine-pride.net/images/mobs/png/{{ $monster['id'] }}.png" 
                     alt="{{ $monster['name'] }}" class="max-h-36 max-w-full object-contain relative z-10"
                     onerror="this.src='/images/item-placeholder.png'">
            </div>

            <!-- Stats & Profile -->
            <div class="flex-1 space-y-5 w-full">
                <div class="border-b border-slate-800 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl font-bold text-white uppercase">{{ $monster['name'] }}</h2>
                            @if(!empty($monster['is_mvp']))
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30 tracking-widest shadow">MVP BOSS</span>
                            @elseif(!empty($monster['is_mini']))
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">MINI BOSS</span>
                            @endif
                        </div>
                        <span class="text-xs text-slate-400 mt-1 block">Monster Class ID: <strong class="text-cyan-400 font-mono">#{{ $monster['id'] }}</strong></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-slate-400 block font-semibold">Level</span>
                        <span class="text-lg font-bold font-mono text-cyan-400 mt-1 block">Lv. {{ $monster['level'] }}</span>
                    </div>
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-slate-400 block font-semibold">Hit Points (HP)</span>
                        <span class="text-lg font-bold font-mono text-emerald-400 mt-1 block">{{ number_format($monster['hp']) }}</span>
                    </div>
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-slate-400 block font-semibold">Base EXP</span>
                        <span class="text-lg font-bold font-mono text-amber-400 mt-1 block">{{ number_format($monster['base_exp']) }}</span>
                    </div>
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                        <span class="text-[10px] uppercase text-slate-400 block font-semibold">Job EXP</span>
                        <span class="text-lg font-bold font-mono text-purple-400 mt-1 block">{{ number_format($monster['job_exp']) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 text-xs">
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-[10px] text-slate-400 block">Size:</span>
                        <strong class="text-white">{{ $monster['size'] }}</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-[10px] text-slate-400 block">Race:</span>
                        <strong class="text-cyan-400">{{ $monster['race'] }}</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-[10px] text-slate-400 block">Element:</span>
                        <strong class="text-emerald-400">{{ $monster['element'] }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drops & Locations Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Item Drops Table -->
        <div class="p-6 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3 uppercase tracking-wide">
                <i data-lucide="package" class="w-4 h-4 text-cyan-400"></i>
                <span>Item Drop Capabilities</span>
            </h3>

            <div class="space-y-2">
                @foreach($monster['drops'] as $drop)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-cyan-950/40 border border-cyan-500/30 flex items-center justify-center shrink-0">
                                <i data-lucide="gift" class="w-4 h-4 text-cyan-400"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white block">{{ $drop['name'] }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">Item ID: #{{ $drop['id'] }}</span>
                            </div>
                        </div>
                        <span class="text-xs font-mono font-bold text-emerald-400 bg-emerald-950/40 px-2 py-0.5 rounded border border-emerald-500/30">
                            {{ $drop['rate'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Spawn Locations -->
        <div class="p-6 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3 uppercase tracking-wide">
                <i data-lucide="map-pin" class="w-4 h-4 text-cyan-400"></i>
                <span>Known Spawn Maps</span>
            </h3>

            <div class="space-y-2">
                @foreach($monster['maps'] as $map)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-950/40 border border-blue-500/30 flex items-center justify-center shrink-0">
                                <i data-lucide="compass" class="w-4 h-4 text-blue-400"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white block">{{ $map['name'] }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $map['code'] }}.gat</span>
                            </div>
                        </div>
                        <span class="text-xs text-slate-300 font-mono">
                            {{ $map['amount'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection
