@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="{ tab: 'overview' }">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Adventurer Profile</h1>
            <p class="text-xs text-slate-400 mt-1">Character recognition, rankings, earned badges, equipment, and verified achievements.</p>
        </div>
        <a href="{{ route('rankings.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Rankings</span>
        </a>
    </div>

    <!-- Main Character Header Card -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 backdrop-blur-xl shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-24 h-24 rounded-2xl bg-slate-950 border border-cyan-500/30 flex items-center justify-center shadow-lg overflow-hidden shrink-0">
                @if($jobSpriteUrl)
                    <img src="{{ $jobSpriteUrl }}" alt="{{ \App\Services\Emulator\JobHelper::getName($char->class) }}" class="w-full h-full object-cover">
                @else
                    <span class="w-full h-full bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-2xl font-black text-slate-950">
                        {{ strtoupper(substr($char->name, 0, 1)) }}
                    </span>
                @endif
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-2xl font-extrabold text-white">{{ $char->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold border {{ \App\Services\Emulator\JobHelper::getBadgeColor($char->class) }}">
                        {{ \App\Services\Emulator\JobHelper::getName($char->class) }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $char->online > 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                        {{ $char->online > 0 ? 'Online' : 'Offline' }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1.5 flex flex-wrap items-center gap-2">
                    <span>Base Level: <strong class="text-cyan-400">{{ $char->base_level }}</strong></span>
                    <span>•</span>
                    <span>Job Level: <strong class="text-blue-400">{{ $char->job_level }}</strong></span>
                    @if($guild && ($privacy->show_guild || $isOwnerOrAdmin))
                        <span>•</span>
                        <a href="{{ route('guilds.show', $guild->guild_id) }}" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-slate-950 border border-slate-800 hover:border-cyan-500/40 text-cyan-300 font-semibold transition-colors">
                            @if(!empty($guild->emblem_len) && $guild->emblem_len > 0)
                                <img src="{{ route('guilds.emblem', $guild->guild_id) }}" alt="{{ $guild->name }}" class="w-4 h-4 object-contain">
                            @else
                                <i data-lucide="shield" class="w-3.5 h-3.5 text-cyan-400"></i>
                            @endif
                            <span>{{ $guild->name }}</span>
                        </a>
                    @endif
                </p>
                @if(!empty($privacy->bio))
                    <p class="text-xs text-slate-300 italic mt-2">"{{ $privacy->bio }}"</p>
                @endif
                @if(($privacy->show_badges || $isOwnerOrAdmin) && !empty($badges))
                    <div class="flex flex-wrap items-center gap-1.5 mt-3">
                        @foreach($badges as $b)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold border"
                                  style="color: {{ $b['color'] }}; border-color: {{ $b['color'] }}66; background-color: {{ $b['color'] }}1f;">
                                @if(!empty($b['icon_url']))
                                    <img src="{{ $b['icon_url'] }}" alt="" class="w-3 h-3 object-contain">
                                @else
                                    <i data-lucide="{{ $b['icon'] ?? 'award' }}" class="w-3 h-3"></i>
                                @endif
                                <span>{{ $b['label'] }}</span>
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if($privacy->show_char_info || $isOwnerOrAdmin)
            <div class="flex items-center gap-4 text-xs bg-slate-950/70 p-4 rounded-2xl border border-slate-800 shrink-0">
                <div>
                    <span class="text-slate-500 block">HP / SP</span>
                    <strong class="text-sm font-bold text-white">{{ number_format($char->max_hp) }} / {{ number_format($char->max_sp) }}</strong>
                </div>
                <div class="border-l border-slate-800 pl-4">
                    <span class="text-slate-500 block">Zeny</span>
                    <strong class="text-sm font-bold text-amber-400 font-mono">{{ number_format($char->zeny) }} Z</strong>
                </div>
            </div>
        @endif
    </div>

    <!-- Profile Section Tabs -->
    <div class="flex flex-wrap gap-2">
        <button type="button" @click="tab = 'overview'" :class="tab === 'overview' ? 'bg-cyan-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 border border-slate-800 hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs transition-colors">Overview</button>
        @if($privacy->show_char_info || $isOwnerOrAdmin)
            <button type="button" @click="tab = 'character'" :class="tab === 'character' ? 'bg-cyan-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 border border-slate-800 hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs transition-colors">Character &amp; Gear</button>
        @endif
        @if($privacy->show_rankings || $isOwnerOrAdmin)
            <button type="button" @click="tab = 'rankings'" :class="tab === 'rankings' ? 'bg-cyan-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 border border-slate-800 hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs transition-colors">Rankings &amp; History</button>
        @endif
        @if($privacy->show_badges || $isOwnerOrAdmin)
            <button type="button" @click="tab = 'badges'" :class="tab === 'badges' ? 'bg-cyan-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 border border-slate-800 hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs transition-colors">Badges ({{ count($badges) }})</button>
        @endif
        @if($privacy->show_achievements || $isOwnerOrAdmin)
            <button type="button" @click="tab = 'achievements'" :class="tab === 'achievements' ? 'bg-cyan-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 border border-slate-800 hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs transition-colors">Achievements ({{ $achievements->count() }})</button>
        @endif
        @if($privacy->show_stats || $isOwnerOrAdmin)
            <button type="button" @click="tab = 'stats'" :class="tab === 'stats' ? 'bg-cyan-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 border border-slate-800 hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs transition-colors">Statistics</button>
        @endif
    </div>

    <!-- TAB: OVERVIEW -->
    <div x-show="tab === 'overview'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($privacy->show_rankings || $isOwnerOrAdmin)
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300 border-b border-slate-800 pb-3">Current Leaderboard Standings</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs">
                    @foreach(['lvl' => 'Base', 'job' => 'Job', 'pvp' => 'PvP', 'mvp' => 'MVP'] as $catKey => $catLabel)
                        @php $pos = $rankingSummary['positions'][$catKey] ?? null; @endphp
                        <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-3">
                            <span class="text-slate-500 text-[10px] uppercase block">{{ $catLabel }}</span>
                            <strong class="text-lg font-black {{ $pos ? 'text-cyan-400' : 'text-slate-600' }}">{{ $pos ? "#{$pos}" : '—' }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($privacy->show_stats || $isOwnerOrAdmin)
            @php
                $st = $rankingSummary['stats'] ?? null;
                $pvpKills = (int) ($st->pvp_kills ?? 0);
                $pvpDeaths = (int) ($st->pvp_deaths ?? 0);
                $mvpKills = (int) ($st->mvp_kills ?? 0);
            @endphp
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300 border-b border-slate-800 pb-3">Combat &amp; Recognition Summary</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs">
                    <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-3">
                        <span class="text-slate-500 text-[10px] uppercase block">PvP Kills</span>
                        <strong class="text-lg font-black text-rose-400">{{ number_format($pvpKills) }}</strong>
                    </div>
                    <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-3">
                        <span class="text-slate-500 text-[10px] uppercase block">MVP Kills</span>
                        <strong class="text-lg font-black text-purple-400">{{ number_format($mvpKills) }}</strong>
                    </div>
                    <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-3">
                        <span class="text-slate-500 text-[10px] uppercase block">Badges</span>
                        <strong class="text-lg font-black text-amber-400">{{ count($badges) }}</strong>
                    </div>
                    <div class="bg-slate-950/70 border border-slate-800/80 rounded-xl p-3">
                        <span class="text-slate-500 text-[10px] uppercase block">Rewards</span>
                        <strong class="text-lg font-black text-emerald-400">{{ number_format($rewardsEarnedCount) }}</strong>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- TAB: CHARACTER & GEAR -->
    @if($privacy->show_char_info || $isOwnerOrAdmin)
        <div x-show="tab === 'character'" style="display: none;" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
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

                <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
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
                        @if(($privacy->show_last_login || $isOwnerOrAdmin) && !empty($char->last_login))
                            <li class="flex justify-between py-1.5 border-b border-slate-800/60">
                                <span class="text-slate-400">Last Login</span>
                                <strong class="text-slate-300 font-mono">{{ $char->last_login }}</strong>
                            </li>
                        @endif
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

            <!-- Equipped Gear -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300 border-b border-slate-800 pb-3 flex items-center gap-2">
                    <i data-lucide="shield" class="w-4 h-4 text-cyan-400"></i>
                    <span>Equipped Gear ({{ count($equippedItems) }})</span>
                </h3>
                @if(count($equippedItems) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($equippedItems as $eq)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-950/70 border border-slate-800/80 hover:border-cyan-500/30 transition-colors">
                                <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center shrink-0">
                                    <img src="{{ $eq['icon_url'] }}" alt="{{ $eq['name'] }}" class="w-7 h-7 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] font-mono uppercase tracking-wider text-cyan-400 block">{{ $eq['slot_label'] }}</span>
                                    <a href="{{ route('itemdb.show', $eq['nameid']) }}" class="text-xs font-bold text-white hover:text-cyan-300 truncate block">
                                        {{ $eq['refine'] > 0 ? '+' . $eq['refine'] . ' ' : '' }}{{ $eq['name'] }}{{ $eq['slots'] > 0 ? ' [' . $eq['slots'] . ']' : '' }}
                                    </a>
                                    @if(!empty($eq['cards']))
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($eq['cards'] as $card)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-purple-950/60 text-purple-300 border border-purple-500/30">{{ $card['name'] }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-500 text-center py-6">No equipment currently equipped on this character.</p>
                @endif
            </div>
        </div>
    @endif

    <!-- TAB: RANKINGS & HISTORY -->
    @if($privacy->show_rankings || $isOwnerOrAdmin)
        <div x-show="tab === 'rankings'" style="display: none;" class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 bg-slate-950/60">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">Verified Ranking Event History</h3>
            </div>
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/40 text-slate-400 uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Event Type</th>
                        <th class="py-3 px-4">Value</th>
                        <th class="py-3 px-4">Recorded At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($rankingSummary['history'] ?? [] as $evt)
                        <tr>
                            <td class="py-2.5 px-4 font-mono text-cyan-400">{{ $evt->event_type }}</td>
                            <td class="py-2.5 px-4 text-slate-200">{{ $evt->value }}</td>
                            <td class="py-2.5 px-4 text-slate-400 font-mono">{{ $evt->occurred_at }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-6 text-center text-slate-500">No historical ranking events recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- TAB: BADGES -->
    @if($privacy->show_badges || $isOwnerOrAdmin)
        <div x-show="tab === 'badges'" style="display: none;" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($badges as $b)
                <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 border"
                         style="color: {{ $b['color'] }}; border-color: {{ $b['color'] }}66; background-color: {{ $b['color'] }}1f;">
                        @if(!empty($b['icon_url']))
                            <img src="{{ $b['icon_url'] }}" alt="" class="w-7 h-7 object-contain">
                        @else
                            <i data-lucide="{{ $b['icon'] ?? 'award' }}" class="w-6 h-6"></i>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white">{{ $b['label'] }}</h4>
                        <p class="text-[11px] uppercase font-mono text-slate-400">{{ $b['category'] }} • Awarded {{ $b['awarded_at'] }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-slate-900/60 border border-slate-800 rounded-2xl p-8 text-center text-xs text-slate-500">
                    No active badges earned yet.
                </div>
            @endforelse
        </div>
    @endif

    <!-- TAB: ACHIEVEMENTS -->
    @if($privacy->show_achievements || $isOwnerOrAdmin)
        <div x-show="tab === 'achievements'" style="display: none;" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($achievements as $ach)
                <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 border"
                         style="color: {{ $ach->color }}; border-color: {{ $ach->color }}66; background-color: {{ $ach->color }}1f;">
                        <i data-lucide="{{ $ach->icon }}" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white">{{ $ach->title }}</h4>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $ach->description }}</p>
                        <span class="text-[10px] font-mono text-slate-500 block mt-1.5">Earned {{ $ach->earned_at?->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-slate-900/60 border border-slate-800 rounded-2xl p-8 text-center text-xs text-slate-500">
                    No milestones unlocked yet.
                </div>
            @endforelse
        </div>
    @endif

    <!-- TAB: STATISTICS -->
    @if($privacy->show_stats || $isOwnerOrAdmin)
        @php
            $st = $rankingSummary['stats'] ?? null;
            $pvpKills = (int) ($st->pvp_kills ?? 0);
            $pvpDeaths = (int) ($st->pvp_deaths ?? 0);
            $kd = $pvpDeaths > 0 ? round($pvpKills / $pvpDeaths, 2) : (float) $pvpKills;
        @endphp
        <div x-show="tab === 'stats'" style="display: none;" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 text-center">
                <span class="text-[10px] uppercase text-slate-500 block">PvP Kills</span>
                <strong class="text-2xl font-black text-rose-400">{{ number_format($pvpKills) }}</strong>
            </div>
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 text-center">
                <span class="text-[10px] uppercase text-slate-500 block">PvP Deaths</span>
                <strong class="text-2xl font-black text-slate-300">{{ number_format($pvpDeaths) }}</strong>
            </div>
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 text-center">
                <span class="text-[10px] uppercase text-slate-500 block">K/D Ratio</span>
                <strong class="text-2xl font-black text-cyan-400">{{ $kd }}</strong>
            </div>
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 text-center">
                <span class="text-[10px] uppercase text-slate-500 block">MVP Kills</span>
                <strong class="text-2xl font-black text-purple-400">{{ number_format((int) ($st->mvp_kills ?? 0)) }}</strong>
            </div>
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 text-center">
                <span class="text-[10px] uppercase text-slate-500 block">Rewards Earned</span>
                <strong class="text-2xl font-black text-emerald-400">{{ number_format($rewardsEarnedCount) }}</strong>
            </div>
        </div>
    @endif
</div>
@endsection
