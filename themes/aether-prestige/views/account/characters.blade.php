@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Adventurer Roster
            </h1>
            <p class="text-xs text-[#788394] mt-1">Review your active game characters, inspect stats, and reset position if stuck.</p>
        </div>
        <a href="{{ route('account.profile') }}" class="prestige-btn-outline text-xs">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Account Profile</span>
        </a>
    </div>

    <!-- Characters Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($characters as $char)
            <div class="prestige-card p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-[#101725] border border-[#C6A76A]/40 flex items-center justify-center font-cinzel text-lg font-bold text-[#C6A76A]">
                                {{ strtoupper(substr($char->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-cinzel text-base font-bold text-[#F4F5F7]">
                                    <a href="{{ route('characters.show', $char->name) }}" class="hover:text-[#C6A76A] transition-colors">
                                        {{ $char->name }}
                                    </a>
                                </h3>
                                <span class="text-[11px] text-[#AAB3C1] font-semibold">
                                    {{ \App\Services\Emulator\JobHelper::getName($char->class) }}
                                </span>
                            </div>
                        </div>

                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $char->online > 0 ? 'bg-emerald-950/80 text-emerald-400 border border-emerald-500/30' : 'bg-[#101725] text-[#788394] border border-white/[0.06]' }}">
                            {{ $char->online > 0 ? 'Online' : 'Offline' }}
                        </span>
                    </div>

                    <!-- Levels & Vitals -->
                    <div class="bg-[#101725]/80 border border-white/[0.06] rounded-xl p-3.5 grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] uppercase tracking-wider text-[#788394] block font-semibold">Base Level</span>
                            <span class="text-sm font-bold font-mono text-[#C6A76A]">{{ $char->base_level }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase tracking-wider text-[#788394] block font-semibold">Job Level</span>
                            <span class="text-sm font-bold font-mono text-[#D8BE8A]">{{ $char->job_level }}</span>
                        </div>
                        <div class="border-t border-white/[0.04] pt-2">
                            <span class="text-[10px] uppercase tracking-wider text-[#788394] block font-semibold">Max HP</span>
                            <span class="font-mono text-white text-xs font-semibold">{{ number_format($char->max_hp) }}</span>
                        </div>
                        <div class="border-t border-white/[0.04] pt-2">
                            <span class="text-[10px] uppercase tracking-wider text-[#788394] block font-semibold">Max SP</span>
                            <span class="font-mono text-white text-xs font-semibold">{{ number_format($char->max_sp) }}</span>
                        </div>
                    </div>

                    <!-- Location & Zeny -->
                    <div class="text-[11px] text-[#788394] space-y-1">
                        <div class="flex items-center justify-between">
                            <span>Current Map:</span>
                            <strong class="text-[#AAB3C1] font-mono">{{ $char->last_map }}.gat</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Carried Zeny:</span>
                            <strong class="text-[#D8BE8A] font-mono">{{ number_format($char->zeny) }} Z</strong>
                        </div>
                    </div>
                </div>

                <!-- Unstuck Action -->
                <div class="pt-4 border-t border-white/[0.06] flex items-center justify-between gap-3">
                    <a href="{{ route('characters.show', $char->name) }}" class="text-xs text-[#C6A76A] hover:underline font-semibold">
                        View Public Profile â†’
                    </a>

                    <form method="POST" action="{{ route('account.unstuck', $char->char_id) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#101725] hover:bg-white/10 border border-white/10 text-[11px] text-[#AAB3C1] hover:text-white transition-colors" title="Resets position to savepoint if stuck">
                            Reset Position
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full prestige-card p-12 text-center text-xs text-[#788394] space-y-3">
                <i data-lucide="shield-alert" class="w-10 h-10 text-[#C6A76A] mx-auto opacity-40"></i>
                <p>No characters created on this account yet. Launch your game client to create an adventurer!</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

