@extends('layouts.app')

@section('title', 'Daily Rewards & Attendance - ' . config('aether.name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{
    activeSlug: '{{ $activeSlug }}',
    selectedCharId: '{{ $characters[0]->char_id ?? '' }}',
    submitting: false
}">
    <!-- Header Ribbon -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center gap-1">
                    <i data-lucide="sparkles" class="w-3 h-3 text-amber-400"></i>
                    <span>Daily Attendance</span>
                </span>
                <span class="text-xs text-slate-500">•</span>
                <span class="text-xs text-cyan-400 font-medium">Free Adventurer Rewards</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mt-1">
                Adventurer Daily Rewards
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-2xl">
                Check in every day to claim exclusive Zeny, consumable crates, rare card albums, and milestone celebration gifts!
            </p>
        </div>

        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('escrow.index') }}" class="px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-slate-200 hover:text-white border border-slate-800 flex items-center gap-2 text-xs font-semibold shadow-inner transition-colors">
                    <div class="relative">
                        <i data-lucide="inbox" class="w-4 h-4 text-cyan-400"></i>
                        @if($inboxCount > 0)
                            <span class="absolute -top-1.5 -right-2 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-amber-400 text-slate-950 font-mono">
                                {{ $inboxCount }}
                            </span>
                        @endif
                    </div>
                    <span>Escrow Inbox</span>
                </a>
            @endauth
        </div>
    </div>

    @if(empty($campaignsData))
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-12 text-center text-slate-400 space-y-3">
            <i data-lucide="calendar-x" class="w-12 h-12 mx-auto text-slate-600"></i>
            <h3 class="text-lg font-bold text-white">No Active Reward Campaigns</h3>
            <p class="text-xs text-slate-400 max-w-md mx-auto">There are currently no active login attendance campaigns scheduled. Please check back soon or keep an eye on server announcements!</p>
        </div>
    @else
        <!-- Multi-Campaign Switcher Tabs (If multiple active campaigns) -->
        @if(count($campaignsData) > 1)
            <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-800/80">
                @foreach($campaignsData as $cData)
                    @php $camp = $cData['campaign']; @endphp
                    <button type="button"
                            @click="activeSlug = '{{ $camp->slug }}'"
                            :class="activeSlug === '{{ $camp->slug }}' ? 'bg-cyan-500 text-slate-950 font-bold shadow-md shadow-cyan-500/20' : 'bg-slate-900/80 text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-800 font-medium'"
                            class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all flex items-center gap-2 cursor-pointer">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span>{{ $camp->name }}</span>
                        <span class="px-1.5 py-0.2 rounded text-[10px] font-mono {{ $camp->slug === $activeSlug ? 'bg-slate-950 text-cyan-300' : 'bg-slate-800 text-slate-400' }}">
                            {{ $camp->duration_days }}d
                        </span>
                    </button>
                @endforeach
            </div>
        @endif

        <!-- Campaign View Panels -->
        @foreach($campaignsData as $cData)
            @php
                $camp = $cData['campaign'];
                $part = $cData['participation'];
                $eligibility = $cData['eligibility'];
                $days = $cData['days'];
                $nextDay = $cData['next_claimable_day'];
                $canClaim = $cData['can_claim_today'];
                $totalDays = $camp->duration_days;
                $claimedCount = $part ? $part->total_claimed_days : 0;
                $progressPct = $totalDays > 0 ? min(100, round(($claimedCount / $totalDays) * 100)) : 0;
            @endphp
            <div x-show="activeSlug === '{{ $camp->slug }}'" style="display: none;" class="space-y-6">
                <!-- Overview Hero Card -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 backdrop-blur-xl shadow-2xl relative overflow-hidden flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-6">
                    <div class="space-y-3 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-slate-950 border border-cyan-500/30 text-cyan-300">
                                {{ $camp->duration_days }} Days Total
                            </span>
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-purple-950/60 border border-purple-500/30 text-purple-300 capitalize">
                                {{ $camp->progression_mode }} Mode
                            </span>
                            @if($camp->repeatable)
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-800 text-slate-300">
                                    Repeatable
                                </span>
                            @endif
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">{{ $camp->name }}</h2>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                            {{ $camp->description ?: 'Log in every day to claim progressive rewards. Unclaimed items are kept safely in your web inbox.' }}
                        </p>

                        <!-- Progress Bar & Streak -->
                        <div class="pt-2 max-w-xl space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-400 flex items-center gap-1.5 font-medium">
                                    <span>Attendance Progress:</span>
                                    <strong class="text-white font-mono">{{ $claimedCount }} / {{ $totalDays }} Days</strong>
                                </span>
                                <span class="text-cyan-400 font-mono font-bold">{{ $progressPct }}%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-950 rounded-full border border-slate-800 overflow-hidden p-0.5">
                                <div class="h-full bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full transition-all duration-500" style="width: {{ $progressPct }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Stats & Claim Console -->
                    <div class="flex flex-col sm:flex-row lg:flex-col items-stretch gap-4 shrink-0 min-w-[280px]">
                        <!-- Streak Widget -->
                        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center">
                                    <i data-lucide="flame" class="w-5 h-5 text-amber-400"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Current Streak</span>
                                    <strong class="text-lg font-black text-amber-300 font-mono">{{ $part ? $part->current_streak : 0 }} Days</strong>
                                </div>
                            </div>
                            <div class="text-right border-l border-slate-800/80 pl-3">
                                <span class="text-[10px] font-semibold text-slate-500 block">Best Streak</span>
                                <span class="text-xs font-mono font-bold text-slate-300">{{ $part ? $part->highest_streak : 0 }}d</span>
                            </div>
                        </div>

                        <!-- Character Selector & Claim CTA -->
                        @auth
                            <div class="space-y-2.5">
                                @if(count($characters) > 0 && $camp->allow_character_choice)
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                                            Reward Receiving Character:
                                        </label>
                                        <select x-model="selectedCharId" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:border-cyan-500 focus:outline-none cursor-pointer">
                                            @foreach($characters as $c)
                                                <option value="{{ $c->char_id }}">
                                                    {{ $c->name }} (Lv. {{ $c->base_level }}/{{ $c->job_level }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                @if($canClaim && $nextDay)
                                    <form method="POST" action="{{ route('attendance.claim', $camp->id) }}" @submit="submitting = true">
                                        @csrf
                                        <input type="hidden" name="day_number" value="{{ $nextDay['day_number'] }}">
                                        <input type="hidden" name="character_id" :value="selectedCharId">

                                        <button type="submit" :disabled="submitting" class="w-full py-3 px-5 rounded-2xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 hover:from-amber-300 hover:to-amber-500 text-slate-950 font-black text-sm shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                                            <i data-lucide="gift" class="w-4 h-4"></i>
                                            <span x-text="submitting ? 'Claiming Reward...' : 'Claim Day {{ $nextDay['day_number'] }} Reward'"></span>
                                        </button>
                                    </form>
                                @elseif($part && $part->last_attendance_date && $part->last_attendance_date->toDateString() === app(\App\Services\Attendance\AttendanceService::class)->getCampaignTodayDate($camp))
                                    <div class="p-3 rounded-2xl bg-emerald-950/50 border border-emerald-500/30 text-center">
                                        <span class="text-xs font-bold text-emerald-400 flex items-center justify-center gap-1.5">
                                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                                            <span>Claimed for Today!</span>
                                        </span>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Next day unlocks at midnight.</p>
                                    </div>
                                @elseif(!$eligibility['eligible'])
                                    <div class="p-3 rounded-2xl bg-rose-950/40 border border-rose-500/30 text-center">
                                        <span class="text-xs font-bold text-rose-300 block">Not Eligible</span>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $eligibility['reason'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="w-full py-3 px-4 rounded-2xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs text-center shadow-lg shadow-cyan-500/20 transition-all block">
                                Log In to Claim Rewards
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Rewards Calendar Grid -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-cyan-400"></i>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Attendance Calendar</h3>
                        </div>
                        <div class="flex items-center gap-3 text-[11px] text-slate-400 font-medium">
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Claimed</span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span> Ready</span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Milestone</span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-700"></span> Locked</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
                        @foreach($days as $d)
                            @php
                                $status = $d['status'];
                                $isMilestone = $d['is_milestone'];
                                $isClaimed = $status === 'claimed';
                                $isAvailable = $status === 'available';
                            @endphp
                            <div class="rounded-2xl p-3.5 flex flex-col justify-between border transition-all duration-200 relative group
                                 {{ $isAvailable ? 'bg-cyan-950/30 border-cyan-500 shadow-lg shadow-cyan-500/10 ring-1 ring-cyan-500/50' : ($isClaimed ? 'bg-slate-950/70 border-emerald-500/30 opacity-75' : ($isMilestone ? 'bg-slate-900/90 border-amber-500/30' : 'bg-slate-900/60 border-slate-800/80')) }}">

                                <div>
                                    <!-- Card Header Ribbon -->
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-mono font-black {{ $isAvailable ? 'text-cyan-400' : ($isClaimed ? 'text-emerald-400' : ($isMilestone ? 'text-amber-400' : 'text-slate-400')) }}">
                                            Day {{ $d['day_number'] }}
                                        </span>

                                        @if($isClaimed)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-950 text-emerald-400 border border-emerald-500/30 flex items-center gap-0.5">
                                                <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                                <span>Claimed</span>
                                            </span>
                                        @elseif($isAvailable)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-cyan-400 text-slate-950 animate-pulse">
                                                Available
                                            </span>
                                        @elseif($isMilestone)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center gap-0.5">
                                                <i data-lucide="star" class="w-2.5 h-2.5 fill-amber-400"></i>
                                                <span>Special</span>
                                            </span>
                                        @else
                                            <i data-lucide="lock" class="w-3 h-3 text-slate-600"></i>
                                        @endif
                                    </div>

                                    <!-- Rewards Item Icons & Quantities -->
                                    <div class="space-y-1.5 my-2">
                                        @forelse($d['items'] as $item)
                                            <div class="flex items-center gap-2 p-1.5 rounded-xl bg-slate-950/80 border border-slate-800/60">
                                                @if($item->reward_type === 'zeny')
                                                    <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/30 flex items-center justify-center shrink-0">
                                                        <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-400"></i>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <span class="font-bold text-amber-300 text-[11px] font-mono block truncate">{{ number_format($item->zeny_amount) }} Z</span>
                                                    </div>
                                                @elseif($item->reward_type === 'cashpoints')
                                                    <div class="w-7 h-7 rounded-lg bg-purple-500/10 border border-purple-500/30 flex items-center justify-center shrink-0">
                                                        <i data-lucide="gem" class="w-3.5 h-3.5 text-purple-400"></i>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <span class="font-bold text-purple-300 text-[11px] font-mono block truncate">{{ number_format($item->cashpoints_amount) }} CP</span>
                                                    </div>
                                                @else
                                                    <div class="w-7 h-7 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center shrink-0 overflow-hidden p-0.5">
                                                        <img src="{{ $item->icon_url ?? ('https://static.divine-pride.net/images/items/item/' . $item->item_id . '.png') }}" alt="" class="w-5 h-5 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <span class="font-bold text-white text-[11px] block truncate" title="{{ $item->formatted_title }}">
                                                            {{ $item->formatted_title }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="py-2 text-center text-[10px] text-slate-600 italic">
                                                Mystery Gift
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Action on Card for Available Day -->
                                @if($isAvailable && auth()->check())
                                    <form method="POST" action="{{ route('attendance.claim', $camp->id) }}" class="mt-2" @submit="submitting = true">
                                        @csrf
                                        <input type="hidden" name="day_number" value="{{ $d['day_number'] }}">
                                        <input type="hidden" name="character_id" :value="selectedCharId">
                                        <button type="submit" :disabled="submitting" class="w-full py-1.5 px-2 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition-colors flex items-center justify-center gap-1 cursor-pointer shadow">
                                            <span>Claim Day {{ $d['day_number'] }}</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
