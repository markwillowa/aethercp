@extends('layouts.app')

@section('title', 'Server Status & Realm Specifications')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <!-- Top Wood Board Header -->
    <div class="chronicles-wood-board flex flex-col md:flex-row md:items-center justify-between gap-6 p-5 sm:p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2.5">
                <i data-lucide="activity" class="w-6 h-6 text-[#FFD700]"></i>
                <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                    Realm Status &amp; Information
                </h1>
            </div>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Real-time emulator cluster health, server rates, game mechanics, and castle schedules
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if($overallOnline)
                <div class="px-3.5 py-1.5 rounded-full text-xs font-bold font-cinzel bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-2 shadow-lg">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>ALL REALMS OPERATIONAL</span>
                </div>
            @else
                <div class="px-3.5 py-1.5 rounded-full text-xs font-bold font-cinzel bg-rose-500/20 text-rose-300 border border-rose-500/40 flex items-center gap-2 shadow-lg">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <span>PARTIAL REALM MAINTENANCE</span>
                </div>
            @endif
        </div>
    </div>

    <!-- 1. Real-time Cluster Gateway Nodes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($servers as $key => $node)
            <div class="chronicles-panel p-5 space-y-4 shadow-lg hover:border-[#D4AF37]/50 transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-[#1B1522] border border-[#5A4A68] flex items-center justify-center shrink-0">
                            @if($key === 'login')
                                <i data-lucide="key-round" class="w-5 h-5 text-[#D4AF37]"></i>
                            @elseif($key === 'char')
                                <i data-lucide="users" class="w-5 h-5 text-[#D4AF37]"></i>
                            @else
                                <i data-lucide="map" class="w-5 h-5 text-[#D4AF37]"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-cinzel text-sm font-bold text-[#F3E5AB]">{{ $node['name'] }}</h3>
                            <span class="text-[10px] text-[#A396AD] font-mono">Port: {{ $node['port'] }}</span>
                        </div>
                    </div>

                    @if($node['online'])
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Online
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Offline
                        </span>
                    @endif
                </div>

                <p class="text-xs text-[#C8BDAA] leading-relaxed">
                    {{ $node['desc'] }}
                </p>

                <div class="pt-3 border-t border-[#3E324A] flex items-center justify-between text-[11px]">
                    <span class="text-[#A396AD]">Connection Latency:</span>
                    <span class="font-mono font-bold text-emerald-400">{{ $node['latency'] }} ms</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- 2. Realm Population & Registered Adventurers -->
    <div class="chronicles-panel p-6 shadow-2xl">
        <div class="chronicles-panel-header px-5 py-3 -m-6 mb-6">
            <span class="chronicles-panel-title">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                <span>Realm Population &amp; Demographics</span>
            </span>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-4 text-center">
                <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Adventurers In-Game</span>
                <span class="text-2xl font-black font-mono text-[#FFD700] mt-1 block">{{ number_format($onlinePlayers) }}</span>
                <span class="text-[10px] text-emerald-400 mt-1 block">Live in Midgard</span>
            </div>

            <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-4 text-center">
                <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Master Accounts</span>
                <span class="text-2xl font-black font-mono text-white mt-1 block">{{ number_format($totalAccounts) }}</span>
                <span class="text-[10px] text-[#A396AD] mt-1 block">Registered Players</span>
            </div>

            <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-4 text-center">
                <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Characters Created</span>
                <span class="text-2xl font-black font-mono text-[#E5A823] mt-1 block">{{ number_format($totalCharacters) }}</span>
                <span class="text-[10px] text-[#A396AD] mt-1 block">Active Heroes</span>
            </div>

            <div class="bg-[#181120] border border-[#3E324A] rounded-xl p-4 text-center">
                <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Formed Guilds</span>
                <span class="text-2xl font-black font-mono text-[#A855F7] mt-1 block">{{ number_format($totalGuilds) }}</span>
                <span class="text-[10px] text-[#A396AD] mt-1 block">Battle Syndicates</span>
            </div>
        </div>
    </div>

    <!-- 3. Server Specifications & Game Mechanics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Rates & Multipliers -->
        <div class="chronicles-panel p-6 shadow-2xl space-y-4">
            <h3 class="font-cinzel text-base font-bold text-[#F3E5AB] flex items-center gap-2 border-b border-[#3E324A] pb-3">
                <i data-lucide="zap" class="w-4 h-4 text-[#D4AF37]"></i>
                Server Rates &amp; Progression
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                <div class="bg-[#181120] border border-[#3E324A] rounded-lg p-3 text-center">
                    <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Base Experience</span>
                    <span class="text-lg font-bold font-mono text-[#FFD700]">{{ $serverInfo['base_rate'] }}</span>
                </div>
                <div class="bg-[#181120] border border-[#3E324A] rounded-lg p-3 text-center">
                    <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Job Experience</span>
                    <span class="text-lg font-bold font-mono text-[#FFD700]">{{ $serverInfo['job_rate'] }}</span>
                </div>
                <div class="bg-[#181120] border border-[#3E324A] rounded-lg p-3 text-center">
                    <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Common Drops</span>
                    <span class="text-lg font-bold font-mono text-emerald-400">{{ $serverInfo['drop_rate'] }}</span>
                </div>
                <div class="bg-[#181120] border border-[#3E324A] rounded-lg p-3 text-center">
                    <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Monster Cards</span>
                    <span class="text-lg font-bold font-mono text-[#E5A823]">{{ $serverInfo['card_rate'] }}</span>
                </div>
                <div class="bg-[#181120] border border-[#3E324A] rounded-lg p-3 text-center">
                    <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">MVP Card Drop</span>
                    <span class="text-lg font-bold font-mono text-rose-400">{{ $serverInfo['mvp_card_rate'] }}</span>
                </div>
                <div class="bg-[#181120] border border-[#3E324A] rounded-lg p-3 text-center">
                    <span class="text-[10px] uppercase text-[#A396AD] block font-bold font-cinzel">Quest EXP</span>
                    <span class="text-lg font-bold font-mono text-[#A855F7]">{{ $serverInfo['quest_rate'] }}</span>
                </div>
            </div>
        </div>

        <!-- Mechanics & Rules -->
        <div class="chronicles-panel p-6 shadow-2xl space-y-4">
            <h3 class="font-cinzel text-base font-bold text-[#F3E5AB] flex items-center gap-2 border-b border-[#3E324A] pb-3">
                <i data-lucide="sliders" class="w-4 h-4 text-[#D4AF37]"></i>
                Server Mechanics &amp; Information
            </h3>

            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between p-2.5 rounded bg-[#181120] border border-[#3E324A]">
                    <span class="text-[#A396AD]">Server Episode &amp; Type:</span>
                    <strong class="text-white">{{ $serverInfo['episode'] }} ({{ $serverInfo['type'] }})</strong>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded bg-[#181120] border border-[#3E324A]">
                    <span class="text-[#A396AD]">Max Level Progression:</span>
                    <strong class="text-[#FFD700] font-mono">Base {{ $serverInfo['max_base_level'] }} / Job {{ $serverInfo['max_job_level'] }}</strong>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded bg-[#181120] border border-[#3E324A]">
                    <span class="text-[#A396AD]">Max Stats &amp; Max ASPD:</span>
                    <strong class="text-white font-mono">Stat {{ $serverInfo['max_stats'] }} • ASPD {{ $serverInfo['max_aspd'] }}</strong>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded bg-[#181120] border border-[#3E324A]">
                    <span class="text-[#A396AD]">War of Emperium:</span>
                    <strong class="text-[#E5A823] font-cinzel">{{ $serverInfo['woe_schedule'] }}</strong>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded bg-[#181120] border border-[#3E324A]">
                    <span class="text-[#A396AD]">Anti-Cheat Engine:</span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-300 font-bold">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        {{ $serverInfo['anti_cheat'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Bar -->
    <div class="p-6 rounded-2xl bg-[#181120] border border-[#3E324A] flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h4 class="font-cinzel text-sm font-bold text-white uppercase">Ready to join the adventure?</h4>
            <p class="text-xs text-[#A396AD] mt-0.5">Download our full client installer and patcher to start playing in minutes.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('downloads.index') }}" class="chronicles-btn-gold text-xs py-2 px-4">
                <span>Download Client</span>
            </a>
            <a href="{{ route('register') }}" class="chronicles-btn-dark text-xs py-2 px-4">
                <span>Create Account</span>
            </a>
        </div>
    </div>

</div>
@endsection
