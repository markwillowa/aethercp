@extends('layouts.app')

@section('title', 'Daily Rewards & Attendance - ' . config('aether.name'))

@section('content')
<div class="prestige-container py-10 space-y-8" x-data="{
    activeSlug: '{{ $activeSlug }}',
    selectedCharId: '{{ $characters[0]->char_id ?? '' }}',
    submitting: false
}">
    <!-- News & Marketplace Style Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Adventurer Daily Rewards
            </h1>
            <p class="text-xs text-[#788394] mt-1">
                Check in every day to claim exclusive Zeny, consumable crates, rare card albums, and milestone celebration gifts.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @auth
                <a href="{{ route('escrow.index') }}" class="h-9 px-4 rounded-xl bg-[#151B26] border border-[#C6A76A]/30 text-[#D8BE8A] hover:text-white inline-flex items-center gap-2 text-xs font-bold transition-colors">
                    <i data-lucide="inbox" class="w-4 h-4 text-[#C6A76A]"></i>
                    <span>Escrow Inbox</span>
                    @if($inboxCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-mono">{{ $inboxCount }}</span>
                    @endif
                </a>
            @endauth
        </div>
    </div>

    @if(empty($campaignsData))
        <div class="prestige-card p-12 text-center text-[#788394] space-y-3">
            <i data-lucide="calendar-x" class="w-12 h-12 mx-auto text-[#788394]/60"></i>
            <h3 class="font-cinzel text-lg font-bold text-white uppercase">No Active Reward Campaigns</h3>
            <p class="text-xs max-w-md mx-auto">There are currently no active login attendance campaigns scheduled. Please check back soon.</p>
        </div>
    @else
        @if(count($campaignsData) > 1)
            <div class="flex items-center gap-2 overflow-x-auto pb-1">
                @foreach($campaignsData as $cData)
                    @php $camp = $cData['campaign']; @endphp
                    <button type="button"
                            @click="activeSlug = '{{ $camp->slug }}'"
                            :class="activeSlug === '{{ $camp->slug }}' ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border-[#C6A76A]/50' : 'bg-[#151B26] text-[#AAB3C1] border-white/[0.08] hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold border whitespace-nowrap transition-colors flex items-center gap-2 cursor-pointer shrink-0">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span>{{ $camp->name }}</span>
                        <span class="px-1.5 py-0.2 rounded text-[10px] font-mono bg-[#101725] border border-white/10 text-[#D8BE8A]">{{ $camp->duration_days }}d</span>
                    </button>
                @endforeach
            </div>
        @endif

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
                <div class="prestige-card p-6 sm:p-8 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-6">
                    <div class="space-y-3 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-[#101725] border border-[#C6A76A]/30 text-[#D8BE8A]">
                                {{ $camp->duration_days }} Days Total
                            </span>
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-[#C6A76A]/15 border border-[#C6A76A]/30 text-[#D8BE8A] capitalize">
                                {{ $camp->progression_mode }} Mode
                            </span>
                            @if($camp->repeatable)
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-[#101725] border border-white/10 text-[#AAB3C1]">
                                    Repeatable
                                </span>
                            @endif
                        </div>

                        <h2 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-white uppercase tracking-wide">{{ $camp->name }}</h2>
                        <p class="text-xs sm:text-sm text-[#AAB3C1] max-w-2xl leading-relaxed">
                            {{ $camp->description ?: 'Log in every day to claim progressive rewards. Unclaimed items are kept safely in your web inbox.' }}
                        </p>

                        <div class="pt-2 max-w-xl space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-[#788394] font-medium">
                                    Attendance Progress: <strong class="text-white font-mono">{{ $claimedCount }} / {{ $totalDays }} Days</strong>
                                </span>
                                <span class="text-[#D8BE8A] font-mono font-bold">{{ $progressPct }}%</span>
                            </div>
                            <div class="w-full h-2.5 bg-[#101725] rounded-full border border-white/10 overflow-hidden p-0.5">
                                <div class="h-full bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] rounded-full transition-all duration-500" style="width: {{ $progressPct }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row lg:flex-col items-stretch gap-4 shrink-0 min-w-[280px]">
                        <div class="bg-[#101725] border border-white/10 rounded-2xl p-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#C6A76A]/15 border border-[#C6A76A]/30 flex items-center justify-center">
                                    <i data-lucide="flame" class="w-5 h-5 text-[#C6A76A]"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#788394] block">Current Streak</span>
                                    <strong class="text-lg font-black text-[#D8BE8A] font-mono">{{ $part ? $part->current_streak : 0 }} Days</strong>
                                </div>
                            </div>
                            <div class="text-right border-l border-white/10 pl-3">
                                <span class="text-[10px] font-semibold text-[#788394] block">Best Streak</span>
                                <span class="text-xs font-mono font-bold text-[#D9DEE7]">{{ $part ? $part->highest_streak : 0 }}d</span>
                            </div>
                        </div>

                        @auth
                            <div class="space-y-2.5">
                                @if(count($characters) > 0 && $camp->allow_character_choice)
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[#788394] mb-1">
                                            Reward Receiving Character:
                                        </label>
                                        <select x-model="selectedCharId" class="w-full bg-[#101725] border border-white/10 rounded-xl px-3 py-2 text-xs font-semibold text-[#D9DEE7] focus:border-[#C6A76A] focus:outline-none cursor-pointer">
                                            @foreach($characters as $c)
                                                <option value="{{ $c->char_id }}">{{ $c->name }} (Lv. {{ $c->base_level }}/{{ $c->job_level }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                @if($canClaim && $nextDay)
                                    <form method="POST" action="{{ route('attendance.claim', $camp->id) }}" @submit="submitting = true">
                                        @csrf
                                        <input type="hidden" name="day_number" value="{{ $nextDay['day_number'] }}">
                                        <input type="hidden" name="character_id" :value="selectedCharId">
                                        <button type="submit" :disabled="submitting" class="w-full py-3 px-5 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                                            <i data-lucide="gift" class="w-4 h-4"></i>
                                            <span x-text="submitting ? 'Claiming Reward...' : 'Claim Day {{ $nextDay['day_number'] }} Reward'"></span>
                                        </button>
                                    </form>
                                @elseif($part && $part->last_attendance_date && $part->last_attendance_date->toDateString() === app(\App\Services\Attendance\AttendanceService::class)->getCampaignTodayDate($camp))
                                    <div class="p-3 rounded-xl bg-emerald-950/50 border border-emerald-500/30 text-center">
                                        <span class="text-xs font-bold text-emerald-400 flex items-center justify-center gap-1.5">
                                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                                            <span>Claimed for Today!</span>
                                        </span>
                                        <p class="text-[10px] text-[#788394] mt-0.5">Next day unlocks at midnight.</p>
                                    </div>
                                @elseif(!$eligibility['eligible'])
                                    <div class="p-3 rounded-xl bg-rose-950/40 border border-rose-500/30 text-center">
                                        <span class="text-xs font-bold text-rose-300 block">Not Eligible</span>
                                        <p class="text-[10px] text-[#788394] mt-0.5">{{ $eligibility['reason'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider text-center block">
                                Log In to Claim Rewards
                            </a>
                        @endauth
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-[#C6A76A]"></i>
                            <h3 class="font-cinzel text-sm font-bold uppercase tracking-wider text-white">Attendance Calendar</h3>
                        </div>
                        <div class="flex items-center gap-3 text-[11px] text-[#788394] font-medium">
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Claimed</span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#D8BE8A] animate-pulse"></span> Ready</span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#C6A76A]"></span> Milestone</span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-white/20"></span> Locked</span>
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
                            <div class="rounded-2xl p-3.5 flex flex-col justify-between border transition-all duration-200
                                 {{ $isAvailable ? 'bg-[#151B26] border-[#C6A76A] ring-1 ring-[#C6A76A]/40' : ($isClaimed ? 'bg-[#101725] border-emerald-500/30 opacity-80' : ($isMilestone ? 'bg-[#151B26] border-[#C6A76A]/35' : 'bg-[#151B26] border-white/[0.08]')) }}">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-mono font-black {{ $isAvailable ? 'text-[#D8BE8A]' : ($isClaimed ? 'text-emerald-400' : ($isMilestone ? 'text-[#C6A76A]' : 'text-[#AAB3C1]')) }}">
                                            Day {{ $d['day_number'] }}
                                        </span>
                                        @if($isClaimed)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-950 text-emerald-400 border border-emerald-500/30">Claimed</span>
                                        @elseif($isAvailable)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-[#C6A76A] text-[#101725]">Available</span>
                                        @elseif($isMilestone)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/30">Special</span>
                                        @else
                                            <i data-lucide="lock" class="w-3 h-3 text-[#788394]"></i>
                                        @endif
                                    </div>

                                    <div class="space-y-1.5 my-2">
                                        @forelse($d['items'] as $item)
                                            <div class="flex items-center gap-2 p-1.5 rounded-xl bg-[#101725] border border-white/[0.06]">
                                                @if($item->reward_type === 'zeny')
                                                    <div class="w-7 h-7 rounded-lg bg-[#C6A76A]/15 border border-[#C6A76A]/30 flex items-center justify-center shrink-0">
                                                        <i data-lucide="coins" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                                                    </div>
                                                    <span class="font-bold text-[#D8BE8A] text-[11px] font-mono block truncate">{{ number_format($item->zeny_amount) }} Z</span>
                                                @elseif($item->reward_type === 'cashpoints')
                                                    <div class="w-7 h-7 rounded-lg bg-[#C6A76A]/15 border border-[#C6A76A]/30 flex items-center justify-center shrink-0">
                                                        <i data-lucide="gem" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                                                    </div>
                                                    <span class="font-bold text-[#D8BE8A] text-[11px] font-mono block truncate">{{ number_format($item->cashpoints_amount) }} CP</span>
                                                @else
                                                    <div class="w-7 h-7 rounded-lg bg-[#151B26] border border-white/10 flex items-center justify-center shrink-0 overflow-hidden p-0.5">
                                                        <img src="{{ $item->icon_url ?? ('https://static.divine-pride.net/images/items/item/' . $item->item_id . '.png') }}" alt="" class="w-5 h-5 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                                    </div>
                                                    <span class="font-bold text-white text-[11px] block truncate" title="{{ $item->formatted_title }}">{{ $item->formatted_title }}</span>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="py-2 text-center text-[10px] text-[#788394] italic">Mystery Gift</div>
                                        @endforelse
                                    </div>
                                </div>

                                @if($isAvailable && auth()->check())
                                    <form method="POST" action="{{ route('attendance.claim', $camp->id) }}" class="mt-2" @submit="submitting = true">
                                        @csrf
                                        <input type="hidden" name="day_number" value="{{ $d['day_number'] }}">
                                        <input type="hidden" name="character_id" :value="selectedCharId">
                                        <button type="submit" :disabled="submitting" class="w-full py-1.5 px-2 rounded-lg bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-[10px] uppercase tracking-wider flex items-center justify-center gap-1 cursor-pointer">
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
