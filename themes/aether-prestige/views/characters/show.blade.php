@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <div class="flex items-center justify-between border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Adventurer Profile
            </h1>
            <p class="text-xs text-[#788394] mt-1">Verified character statistics, guild affiliation, and world standing.</p>
        </div>
        <a href="{{ route('rankings.index') }}" class="prestige-btn-outline text-xs">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Leaderboards</span>
        </a>
    </div>

    <!-- Main Character Card -->
    <div class="prestige-card-elevated p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-2xl">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-[#1B2432] to-[#222D3C] border border-[#C6A76A]/50 flex items-center justify-center text-3xl font-black text-[#C6A76A] shadow-xl font-cinzel">
                {{ strtoupper(substr($char->name, 0, 1)) }}
            </div>
            <div class="space-y-2">
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-white uppercase">{{ $char->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-[#101725] text-[#D8BE8A] border border-[#C6A76A]/40">
                        {{ \App\Services\Emulator\JobHelper::getName($char->class) }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $char->online > 0 ? 'bg-emerald-950/80 text-emerald-400 border border-emerald-500/30' : 'bg-[#101725] text-[#788394] border border-white/10' }}">
                        {{ $char->online > 0 ? 'Online' : 'Offline' }}
                    </span>
                </div>
                <p class="text-xs text-[#788394] flex items-center gap-3 flex-wrap">
                    <span>Base Level: <strong class="text-[#C6A76A] font-mono">{{ $char->base_level }}</strong></span>
                    <span>â€¢</span>
                    <span>Job Level: <strong class="text-[#D8BE8A] font-mono">{{ $char->job_level }}</strong></span>
                    @if($guild)
                        <span>â€¢</span>
                        <span>Guild: <a href="{{ route('guilds.show', $guild->guild_id) }}" class="text-[#2DD4BF] font-semibold hover:underline font-cinzel">{{ $guild->name }}</a></span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-6 text-xs bg-[#101725] p-5 rounded-2xl border border-white/[0.08]">
            <div>
                <span class="text-[#788394] uppercase tracking-wider text-[10px] font-semibold block">Max HP / SP</span>
                <strong class="text-sm font-bold text-white font-mono">{{ number_format($char->max_hp) }} / {{ number_format($char->max_sp) }}</strong>
            </div>
            <div class="border-l border-white/[0.08] pl-6">
                <span class="text-[#788394] uppercase tracking-wider text-[10px] font-semibold block">Carried Zeny</span>
                <strong class="text-sm font-bold text-[#D8BE8A] font-mono">{{ number_format($char->zeny) }} Z</strong>
            </div>
        </div>
    </div>

    <!-- Attributes / Stats Matrix -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="prestige-card p-4 text-center">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#788394]">Strength</span>
            <p class="font-cinzel text-xl font-extrabold text-[#C6A76A] mt-1">{{ $char->str }}</p>
        </div>
        <div class="prestige-card p-4 text-center">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#788394]">Agility</span>
            <p class="font-cinzel text-xl font-extrabold text-[#2DD4BF] mt-1">{{ $char->agi }}</p>
        </div>
        <div class="prestige-card p-4 text-center">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#788394]">Vitality</span>
            <p class="font-cinzel text-xl font-extrabold text-[#D9DEE7] mt-1">{{ $char->vit }}</p>
        </div>
        <div class="prestige-card p-4 text-center">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#788394]">Intelligence</span>
            <p class="font-cinzel text-xl font-extrabold text-[#93C5FD] mt-1">{{ $char->int }}</p>
        </div>
        <div class="prestige-card p-4 text-center">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#788394]">Dexterity</span>
            <p class="font-cinzel text-xl font-extrabold text-[#FCD34D] mt-1">{{ $char->dex }}</p>
        </div>
        <div class="prestige-card p-4 text-center">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#788394]">Luck</span>
            <p class="font-cinzel text-xl font-extrabold text-[#F472B6] mt-1">{{ $char->luk }}</p>
        </div>
    </div>

    <!-- Equipped Items Grid -->
    <div class="prestige-card p-6 space-y-4">
        <div class="flex items-center gap-2 border-b border-white/[0.08] pb-3">
            <i data-lucide="shield" class="w-4 h-4 text-[#C6A76A]"></i>
            <h3 class="font-cinzel text-xs font-bold uppercase tracking-wider text-white">Equipped Gear & Armaments</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($equippedItems as $item)
                <div class="bg-[#101725] border border-white/[0.06] rounded-xl p-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-[#151B26] border border-white/10 flex items-center justify-center shrink-0">
                        <i data-lucide="package" class="w-5 h-5 text-[#C6A76A]"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-white truncate font-cinzel">{{ $item->name ?? "Item #{$item->nameid}" }}</h4>
                        <span class="text-[10px] font-mono text-[#D8BE8A]">Slot: {{ $item->equip ?? 'Gear' }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-6 text-center text-xs text-[#788394]">
                    No equipment currently registered on this adventurer.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
