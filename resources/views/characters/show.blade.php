@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Adventurer Profile</h1>
            <p class="text-xs text-slate-400 mt-1">Public character statistics, levels, and guild standing.</p>
        </div>
        <a href="{{ route('rankings.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Rankings</span>
        </a>
    </div>

    <!-- Main Character Card -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 backdrop-blur-xl shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-2xl font-black text-slate-950 shadow-lg">
                {{ strtoupper(substr($char->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-extrabold text-white">{{ $char->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold border {{ \App\Services\Emulator\JobHelper::getBadgeColor($char->class) }}">
                        {{ \App\Services\Emulator\JobHelper::getName($char->class) }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $char->online > 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                        {{ $char->online > 0 ? 'Online' : 'Offline' }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                    <span>Base Level: <strong class="text-cyan-400">{{ $char->base_level }}</strong></span>
                    <span>•</span>
                    <span>Job Level: <strong class="text-blue-400">{{ $char->job_level }}</strong></span>
                    @if($guild)
                        <span>•</span>
                        <span>Guild: <a href="{{ route('guilds.show', $guild->guild_id) }}" class="text-cyan-300 font-semibold hover:underline">{{ $guild->name }}</a></span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs bg-slate-950/70 p-4 rounded-2xl border border-slate-800">
            <div>
                <span class="text-slate-500 block">HP / SP</span>
                <strong class="text-sm font-bold text-white">{{ number_format($char->max_hp) }} / {{ number_format($char->max_sp) }}</strong>
            </div>
            <div class="border-l border-slate-800 pl-4">
                <span class="text-slate-500 block">Zeny</span>
                <strong class="text-sm font-bold text-amber-400 font-mono">{{ number_format($char->zeny) }} Z</strong>
            </div>
        </div>
    </div>

    <!-- Stats & Attributes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Primary Attributes -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 backdrop-blur-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300 border-b border-slate-800 pb-3">Primary Attributes</h3>
            <div class="grid grid-cols-3 gap-3 text-xs">
                @foreach(['str' => 'STR', 'agi' => 'AGI', 'vit' => 'VIT', 'int' => 'INT', 'dex' => 'DEX', 'luk' => 'LUK'] as $key => $lbl)
                    <div class="bg-slate-950/70 p-3 rounded-xl border border-slate-800/80 text-center">
                        <span class="text-slate-500 text-[10px] block">{{ $lbl }}</span>
                        <strong class="text-lg font-bold text-white">{{ $char->$key ?? 1 }}</strong>
                    </div>
                @endforeach
            </div>

            @if(config('emulator.renewal') && isset($char->pow))
                <h4 class="text-xs font-bold uppercase tracking-wider text-purple-400 pt-2 border-t border-slate-800">Renewal Trait Stats</h4>
                <div class="grid grid-cols-3 gap-3 text-xs">
                    @foreach(['pow' => 'POW', 'sta' => 'STA', 'wis' => 'WIS', 'spl' => 'SPL', 'con' => 'CON', 'crt' => 'CRT'] as $key => $lbl)
                        <div class="bg-purple-950/20 p-3 rounded-xl border border-purple-500/20 text-center">
                            <span class="text-purple-400 text-[10px] block">{{ $lbl }}</span>
                            <strong class="text-lg font-bold text-purple-200">{{ $char->$key ?? 0 }}</strong>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Character Meta & Location -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 backdrop-blur-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300 border-b border-slate-800 pb-3">Character Information</h3>
            <ul class="space-y-2 text-xs">
                <li class="flex justify-between py-1.5 border-b border-slate-800/60">
                    <span class="text-slate-400">Gender</span>
                    <strong class="text-slate-200">{{ $char->sex === 'M' ? 'Male' : 'Female' }}</strong>
                </li>
                <li class="flex justify-between py-1.5 border-b border-slate-800/60">
                    <span class="text-slate-400">Last Seen Map</span>
                    <strong class="text-cyan-400 font-mono">{{ $char->last_map }}</strong>
                </li>
                <li class="flex justify-between py-1.5 border-b border-slate-800/60">
                    <span class="text-slate-400">Fame / MVP Points</span>
                    <strong class="text-amber-400">{{ number_format($char->fame) }}</strong>
                </li>
                <li class="flex justify-between py-1.5 border-b border-slate-800/60">
                    <span class="text-slate-400">Total Base EXP</span>
                    <strong class="text-slate-300 font-mono">{{ number_format($char->base_exp) }}</strong>
                </li>
                <li class="flex justify-between py-1.5">
                    <span class="text-slate-400">Total Job EXP</span>
                    <strong class="text-slate-300 font-mono">{{ number_format($char->job_exp) }}</strong>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

