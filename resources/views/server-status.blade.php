@extends('layouts.app')

@section('title', 'Server Status & Realm Specifications')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 py-2">

    <!-- Top Header -->
    <div class="p-6 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="activity" class="w-6 h-6 text-cyan-400"></i>
                <h1 class="text-2xl font-bold text-white tracking-wide uppercase">
                    Realm Status &amp; Information
                </h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Real-time emulator cluster health, server rates, game mechanics, and castle schedules
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if($overallOnline)
                <div class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-2 shadow-lg">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>ALL REALMS OPERATIONAL</span>
                </div>
            @else
                <div class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30 flex items-center gap-2 shadow-lg">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <span>PARTIAL REALM MAINTENANCE</span>
                </div>
            @endif
        </div>
    </div>

    <!-- 1. Real-time Cluster Gateway Nodes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($servers as $key => $node)
            <div class="p-5 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-lg hover:border-cyan-500/40 transition-all space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-950/40 border border-cyan-500/30 flex items-center justify-center shrink-0">
                            @if($key === 'login')
                                <i data-lucide="key-round" class="w-5 h-5 text-cyan-400"></i>
                            @elseif($key === 'char')
                                <i data-lucide="users" class="w-5 h-5 text-cyan-400"></i>
                            @else
                                <i data-lucide="map" class="w-5 h-5 text-cyan-400"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">{{ $node['name'] }}</h3>
                            <span class="text-[10px] text-slate-400 font-mono">Port: {{ $node['port'] }}</span>
                        </div>
                    </div>

                    @if($node['online'])
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Online
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Offline
                        </span>
                    @endif
                </div>

                <p class="text-xs text-slate-400 leading-relaxed">
                    {{ $node['desc'] }}
                </p>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-[11px]">
                    <span class="text-slate-400">Connection Latency:</span>
                    <span class="font-mono font-bold text-emerald-400">{{ $node['latency'] }} ms</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- 2. Realm Population & Registered Adventurers -->
    <div class="p-6 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-800">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-cyan-400"></i>
            <h2 class="text-sm font-bold text-white uppercase tracking-wide">Realm Population &amp; Demographics</h2>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 text-center">
                <span class="text-[10px] uppercase text-slate-400 block font-semibold">Adventurers In-Game</span>
                <span class="text-2xl font-black font-mono text-cyan-400 mt-1 block">{{ number_format($onlinePlayers) }}</span>
                <span class="text-[10px] text-emerald-400 mt-1 block">Live in Midgard</span>
            </div>

            <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 text-center">
                <span class="text-[10px] uppercase text-slate-400 block font-semibold">Master Accounts</span>
                <span class="text-2xl font-black font-mono text-white mt-1 block">{{ number_format($totalAccounts) }}</span>
                <span class="text-[10px] text-slate-400 mt-1 block">Registered Players</span>
            </div>

            <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 text-center">
                <span class="text-[10px] uppercase text-slate-400 block font-semibold">Characters Created</span>
                <span class="text-2xl font-black font-mono text-blue-400 mt-1 block">{{ number_format($totalCharacters) }}</span>
                <span class="text-[10px] text-slate-400 mt-1 block">Active Heroes</span>
            </div>

            <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 text-center">
                <span class="text-[10px] uppercase text-slate-400 block font-semibold">Formed Guilds</span>
                <span class="text-2xl font-black font-mono text-purple-400 mt-1 block">{{ number_format($totalGuilds) }}</span>
                <span class="text-[10px] text-slate-400 mt-1 block">Battle Syndicates</span>
            </div>
        </div>
    </div>

    <!-- 3. Server Specifications & Game Mechanics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Rates & Multipliers -->
        <div class="p-6 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3 uppercase tracking-wide">
                <i data-lucide="zap" class="w-4 h-4 text-cyan-400"></i>
                <span>Server Rates &amp; Progression</span>
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                    <span class="text-[10px] uppercase text-slate-400 block font-semibold">Base Experience</span>
                    <span class="text-lg font-bold font-mono text-cyan-400">{{ $serverInfo['base_rate'] }}</span>
                </div>
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                    <span class="text-[10px] uppercase text-slate-400 block font-semibold">Job Experience</span>
                    <span class="text-lg font-bold font-mono text-cyan-400">{{ $serverInfo['job_rate'] }}</span>
                </div>
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                    <span class="text-[10px] uppercase text-slate-400 block font-semibold">Common Drops</span>
                    <span class="text-lg font-bold font-mono text-emerald-400">{{ $serverInfo['drop_rate'] }}</span>
                </div>
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                    <span class="text-[10px] uppercase text-slate-400 block font-semibold">Monster Cards</span>
                    <span class="text-lg font-bold font-mono text-amber-400">{{ $serverInfo['card_rate'] }}</span>
                </div>
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                    <span class="text-[10px] uppercase text-slate-400 block font-semibold">MVP Card Drop</span>
                    <span class="text-lg font-bold font-mono text-rose-400">{{ $serverInfo['mvp_card_rate'] }}</span>
                </div>
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 text-center">
                    <span class="text-[10px] uppercase text-slate-400 block font-semibold">Quest EXP</span>
                    <span class="text-lg font-bold font-mono text-purple-400">{{ $serverInfo['quest_rate'] }}</span>
                </div>
            </div>
        </div>

        <!-- Mechanics & Rules -->
        <div class="p-6 rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3 uppercase tracking-wide">
                <i data-lucide="sliders" class="w-4 h-4 text-cyan-400"></i>
                <span>Server Mechanics &amp; Information</span>
            </h3>

            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-slate-400">Server Episode &amp; Type:</span>
                    <strong class="text-white">{{ $serverInfo['episode'] }} ({{ $serverInfo['type'] }})</strong>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-slate-400">Max Level Progression:</span>
                    <strong class="text-cyan-400 font-mono">Base {{ $serverInfo['max_base_level'] }} / Job {{ $serverInfo['max_job_level'] }}</strong>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-slate-400">Max Stats &amp; Max ASPD:</span>
                    <strong class="text-white font-mono">Stat {{ $serverInfo['max_stats'] }} • ASPD {{ $serverInfo['max_aspd'] }}</strong>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-slate-400">War of Emperium:</span>
                    <strong class="text-amber-400 font-semibold">{{ $serverInfo['woe_schedule'] }}</strong>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-slate-400">Anti-Cheat Engine:</span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-400 font-bold">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        {{ $serverInfo['anti_cheat'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Bar -->
    <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl">
        <div>
            <h4 class="text-sm font-bold text-white uppercase">Ready to join the adventure?</h4>
            <p class="text-xs text-slate-400 mt-0.5">Download our full client installer and patcher to start playing in minutes.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('downloads.index') }}" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs py-2.5 px-5 rounded-xl transition-all shadow-md">
                <span>Download Client</span>
            </a>
            <a href="{{ route('register') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs py-2.5 px-5 rounded-xl transition-colors">
                <span>Create Account</span>
            </a>
        </div>
    </div>

</div>
@endsection
