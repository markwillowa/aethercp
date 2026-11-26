@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <div class="chronicles-wood-board flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                Adventurer Roster
            </h1>
            <p class="text-xs text-[#EAD2B0] mt-1">
                Inspect your characters, statistics, and rescue stuck adventurers
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('character_creation'))
                <a href="{{ route('account.characters.create') }}" class="chronicles-btn-gold text-xs py-2 px-4 w-auto flex items-center gap-1.5">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                    <span>Create Character</span>
                </a>
            @endif
            <a href="{{ route('account.profile') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                <span>Account Profile</span>
            </a>
        </div>
    </div>

    <!-- Characters Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($characters as $char)
            <div class="chronicles-panel-ivy flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] flex items-center justify-center shrink-0 overflow-hidden shadow-inner">
                                <img src="{{ route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/characters/lord_knight.png']) }}" 
                                     alt="Sprite" class="h-12 object-contain filter drop-shadow">
                            </div>
                            <div>
                                <h3 class="font-cinzel text-base font-bold text-[#4A2200]">
                                    <a href="{{ route('characters.show', $char->name) }}" class="hover:text-[#D4441D] transition-colors">
                                        {{ $char->name }}
                                    </a>
                                </h3>
                                <span class="text-xs text-[#795E43] font-semibold">
                                    {{ \App\Services\Emulator\JobHelper::getName($char->class) }}
                                </span>
                            </div>
                        </div>

                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $char->online > 0 ? 'bg-[#1E4620] text-[#A5D6A7] border border-[#4CAF50]' : 'bg-[#FAF4EA] text-[#8C7A68] border border-[#DECBB1]' }}">
                            {{ $char->online > 0 ? 'Online' : 'Offline' }}
                        </span>
                    </div>

                    <!-- Levels & Vitals -->
                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-xl p-3 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-[10px] uppercase text-[#8C7A68] font-semibold block">Base Level</span>
                            <span class="text-sm font-bold font-mono text-[#D4441D]">Lv. {{ $char->base_level }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase text-[#8C7A68] font-semibold block">Job Level</span>
                            <span class="text-sm font-bold font-mono text-[#9E5600]">Lv. {{ $char->job_level }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase text-[#8C7A68] font-semibold block">Current Map</span>
                            <span class="text-xs font-mono text-[#5A3D28] truncate block">{{ $char->last_map }}.gat</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase text-[#8C7A68] font-semibold block">Carried Zeny</span>
                            <span class="text-xs font-mono font-bold text-[#4A2200]">{{ number_format($char->zeny) }} Z</span>
                        </div>
                    </div>
                </div>

                @php $charModuleEnabled = app(\App\Services\Module\ModuleManager::class)->isEnabled('character'); @endphp
                <div class="pt-3 border-t border-[#DECBB1] grid {{ $charModuleEnabled ? 'grid-cols-3' : 'grid-cols-2' }} gap-2">
                    @if($charModuleEnabled)
                        <a href="{{ route('account.characters.manage', $char->char_id) }}"
                           class="px-2.5 py-2 rounded-lg text-[11px] font-bold bg-[#4A2200] hover:bg-[#5E2C00] text-[#FFE4A0] border border-[#8B5A2B] flex items-center justify-center gap-1 whitespace-nowrap transition-colors">
                            <i data-lucide="settings-2" class="w-3.5 h-3.5 shrink-0"></i>
                            <span>Manage</span>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('account.unstuck', $char->char_id) }}" class="w-full">
                        @csrf
                        <button type="submit" {{ $char->online > 0 ? 'disabled' : '' }}
                                onclick="return confirm('Reset {{ $char->name }} position back to Prontera?')"
                                class="w-full px-2.5 py-2 rounded-lg text-[11px] font-bold bg-[#FAF4EA] hover:bg-[#EFE4D0] text-[#5A3D28] border border-[#DECBB1] disabled:opacity-50 flex items-center justify-center gap-1 whitespace-nowrap cursor-pointer">
                            <i data-lucide="compass" class="w-3.5 h-3.5 text-[#9E5600] shrink-0"></i>
                            <span>Unstuck</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('account.characters.delete', $char->char_id) }}" class="w-full"
                          onsubmit="return confirm('Are you sure you want to permanently delete character {{ $char->name }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" {{ $char->online > 0 ? 'disabled' : '' }}
                                class="w-full px-2.5 py-2 rounded-lg text-[11px] font-bold bg-[#FCE8E6] hover:bg-[#F9D2CE] text-[#A82508] border border-[#E6B0AA] disabled:opacity-50 flex items-center justify-center gap-1 whitespace-nowrap cursor-pointer">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5 shrink-0"></i>
                            <span>Delete</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full chronicles-panel-ivy text-center py-12">
                <i data-lucide="shield-question" class="w-12 h-12 text-[#A88B57] mx-auto mb-3"></i>
                <h3 class="font-cinzel text-lg font-bold text-[#4A2200]">No Characters Created Yet</h3>
                <p class="text-xs text-[#795E43] mt-1 max-w-sm mx-auto">
                    Launch the game client, log into your account, and create your first adventurer!
                </p>
            </div>
        @endforelse
    </div>

</div>
@endsection
