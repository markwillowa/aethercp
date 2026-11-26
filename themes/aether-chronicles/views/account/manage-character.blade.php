@extends('layouts.app')

@section('content')
@php
    $charSex = strtoupper((string) ($account->sex ?? $character->sex ?? 'M')) === 'F' ? 'F' : 'M';
    $liveSpriteUrl = route('account.characters.sprite-preview', [
        'gender' => $charSex,
        'job' => (int) $character->class,
        'hair' => max(1, (int) $character->hair),
        'color' => (int) $character->hair_color,
        'dye' => (int) $character->clothes_color,
        'dir' => 0,
    ]);

    $findEquip = function (int $mask) use ($equipped) {
        foreach ($equipped as $eq) {
            if (((int) ($eq->equip ?? 0) & $mask) !== 0) {
                return $eq;
            }
        }
        return null;
    };

    $generalLeft = [
        ['label' => 'Upper Head', 'item' => $findEquip(256)],
        ['label' => 'Lower Head', 'item' => $findEquip(1)],
        ['label' => 'R-Hand', 'item' => $findEquip(2)],
        ['label' => 'Garment', 'item' => $findEquip(4)],
        ['label' => 'Accessory L', 'item' => $findEquip(8)],
    ];
    $generalRight = [
        ['label' => 'Mid Head', 'item' => $findEquip(512)],
        ['label' => 'Armor', 'item' => $findEquip(16)],
        ['label' => 'L-Hand', 'item' => $findEquip(32)],
        ['label' => 'Shoes', 'item' => $findEquip(64)],
        ['label' => 'Accessory R', 'item' => $findEquip(128)],
    ];
    $specialLeft = [
        ['label' => 'Costume Top', 'item' => $findEquip(1024)],
        ['label' => 'Costume Low', 'item' => $findEquip(4096)],
        ['label' => 'Shadow Weapon', 'item' => $findEquip(131072)],
        ['label' => 'Shadow Garment', 'item' => $findEquip(8192)],
        ['label' => 'Shadow Earring', 'item' => $findEquip(1048576)],
    ];
    $specialRight = [
        ['label' => 'Costume Mid', 'item' => $findEquip(2048)],
        ['label' => 'Shadow Armor', 'item' => $findEquip(65536)],
        ['label' => 'Shadow Shield', 'item' => $findEquip(262144)],
        ['label' => 'Shadow Shoes', 'item' => $findEquip(524288)],
        ['label' => 'Shadow Pendant', 'item' => $findEquip(2097152)],
    ];

    $invJs = collect($inventory)->map(fn ($i) => [
        'id' => (int) $i->id,
        'nameid' => (int) $i->nameid,
        'name' => (string) $i->item_name,
        'amount' => (int) $i->amount,
        'refine' => (int) ($i->refine ?? 0),
        'cards' => $i->cards ?? [],
        'type' => (string) ($i->item_type ?? 'etc'),
        'icon' => (string) $i->icon_url,
    ])->values()->all();

    $storageJs = collect($storage)->map(fn ($s) => [
        'id' => (int) $s->id,
        'nameid' => (int) $s->nameid,
        'name' => (string) $s->item_name,
        'amount' => (int) $s->amount,
        'refine' => (int) ($s->refine ?? 0),
        'cards' => $s->cards ?? [],
        'type' => (string) ($s->item_type ?? 'etc'),
        'icon' => (string) $s->icon_url,
    ])->values()->all();
@endphp

<div class="chronicles-container py-10 space-y-6" x-data="{ tab: 'profile', equipTab: 'general' }">
    <!-- Chronicles Wood Board Header -->
    <div class="chronicles-wood-board flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-[#FAF4EA] border-2 border-[#D4AF37] flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                <img src="{{ $liveSpriteUrl }}" alt="{{ $character->name }}" class="w-14 h-14 object-contain scale-125" onerror="this.style.display='none'">
            </div>
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">{{ $character->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase {{ (int)$character->online > 0 ? 'bg-[#1E4620] text-[#A5D6A7] border border-[#4CAF50]' : 'bg-[#2A1F18] text-[#EAD2B0] border border-[#8B5A2B]' }}">
                        {{ (int)$character->online > 0 ? 'Online' : 'Offline' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-[#D4AF37]/20 text-[#FFE4A0] border border-[#D4AF37]/50">
                        {{ $character->job_name }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-[#EAD2B0] mt-1">
                    <span>Lv. <strong class="text-white">{{ $character->base_level }}</strong> / <strong>{{ $character->job_level }}</strong></span>
                    <span>Zeny: <strong class="text-[#FFD700]">{{ number_format($character->zeny) }} Z</strong></span>
                    <span>Cash Points: <strong class="text-[#FFE4A0]">{{ number_format($cash_points) }} CP</strong></span>
                    <span>Guild: <strong>{{ $character->guild_name ?: 'None' }}</strong></span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 self-start lg:self-center">
            <a href="{{ route('characters.show', $character->name) }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
                <i data-lucide="external-link" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                <span>Public Profile</span>
            </a>
            <a href="{{ route('account.characters') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                <span>Roster</span>
            </a>
        </div>
    </div>

    <!-- Status Banners -->
    @if((int)$character->online > 0)
        <div class="bg-[#FFF4E5] border border-[#E6A23C] rounded-2xl p-4 text-xs text-[#663C00] flex items-center gap-2.5">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-[#D4441D] shrink-0"></i>
            <span>This character is currently <strong>Online</strong>. Please log out of the game before using character services.</span>
        </div>
    @endif

    @if($active_lock)
        <div class="bg-[#FCE8E6] border border-[#E6B0AA] rounded-2xl p-4 text-xs text-[#7B1F12] flex items-center justify-between gap-3">
            <div><strong>Character Locked ({{ $active_lock->lock_scope }}):</strong> {{ $active_lock->reason }}</div>
            @if((int)$active_lock->locked_by === (int)auth()->id() && $active_lock->lock_scope === 'services')
                <form method="POST" action="{{ route('account.characters.services.lock', $character->char_id) }}">
                    @csrf
                    <input type="hidden" name="action" value="unlock">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#1E4620] text-white font-bold cursor-pointer">Unlock</button>
                </form>
            @endif
        </div>
    @endif

    @if($deletion_request && $deletion_request->status === 'pending')
        <div class="bg-[#FFF4E5] border border-[#E6A23C] rounded-2xl p-4 text-xs text-[#663C00] flex items-center justify-between gap-3">
            <div><strong>Scheduled for Deletion:</strong> {{ $deletion_request->scheduled_delete_at->format('M d, Y H:i') }}</div>
            <form method="POST" action="{{ route('account.characters.services.deletion.cancel', $character->char_id) }}">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#1E4620] text-white font-bold cursor-pointer">Cancel Deletion</button>
            </form>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap gap-2 text-xs font-bold">
        @foreach([
            'profile' => 'Equip & Status',
            'bags' => 'Inventory & Storage',
            'services' => 'Self-Services',
            'privacy' => 'Profile Customization & Privacy',
            'activity' => 'Service History',
        ] as $tKey => $tLabel)
            <button type="button" @click="tab = '{{ $tKey }}'"
                    :class="tab === '{{ $tKey }}' ? 'bg-[#4A2200] text-[#FFE4A0] border-[#D4AF37]' : 'bg-[#FAF4EA] text-[#5A3D28] border-[#DECBB1] hover:bg-[#EFE4D0]'"
                    class="px-4 py-2 rounded-xl border transition-colors cursor-pointer font-cinzel">
                {{ $tLabel }}
            </button>
        @endforeach
    </div>

    <!-- TAB 1: RO-Style Equip & Status Window -->
    <div x-show="tab === 'profile'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 text-xs">
        <div class="lg:col-span-8 chronicles-panel-ivy p-0 overflow-hidden">
            <div class="px-5 py-3 bg-[#4A2200] border-b border-[#8B5A2B] flex items-center justify-between">
                <span class="font-cinzel font-bold text-[#FFE4A0]">Equip &amp; Status</span>
                <div class="inline-flex rounded-lg bg-[#2A1400] border border-[#8B5A2B] p-0.5 text-[11px] font-bold">
                    <button type="button" @click="equipTab = 'general'"
                            :class="equipTab === 'general' ? 'bg-[#D4AF37] text-[#2A1400]' : 'text-[#EAD2B0]'"
                            class="px-3 py-1 rounded-md cursor-pointer">General</button>
                    <button type="button" @click="equipTab = 'special'"
                            :class="equipTab === 'special' ? 'bg-[#D4AF37] text-[#2A1400]' : 'text-[#EAD2B0]'"
                            class="px-3 py-1 rounded-md cursor-pointer">Special Equipment</button>
                </div>
            </div>

            <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4 items-center border-b border-[#DECBB1]">
                <!-- Left Equipment Column -->
                <div class="space-y-2.5">
                    <template x-if="equipTab === 'general'">
                        <div class="space-y-2.5">
                            @foreach($generalLeft as $eqSlot)
                                <div class="h-12 px-3 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-[#DECBB1] flex items-center justify-center shrink-0">
                                        @if($eqSlot['item'])
                                            <img src="{{ $eqSlot['item']->icon_url }}" alt="" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                        @else
                                            <span class="w-4 h-2 rounded-full bg-[#DECBB1]"></span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        @if($eqSlot['item'])
                                            <p class="font-bold text-[#4A2200] truncate">
                                                @if((int)$eqSlot['item']->refine > 0)<span class="text-[#D4441D]">+{{ $eqSlot['item']->refine }}</span> @endif
                                                {{ $eqSlot['item']->item_name }}
                                            </p>
                                            <p class="text-[10px] text-[#9E5600] truncate">{{ $eqSlot['label'] }}@if(!empty($eqSlot['item']->cards)) • <span class="text-[#1E4620]">{{ implode(', ', $eqSlot['item']->cards) }}</span>@endif</p>
                                        @else
                                            <p class="text-[11px] text-[#8C7A68] italic">{{ $eqSlot['label'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </template>
                    <template x-if="equipTab === 'special'">
                        <div class="space-y-2.5">
                            @foreach($specialLeft as $eqSlot)
                                <div class="h-12 px-3 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-[#DECBB1] flex items-center justify-center shrink-0">
                                        @if($eqSlot['item'])
                                            <img src="{{ $eqSlot['item']->icon_url }}" alt="" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                        @else
                                            <span class="w-4 h-2 rounded-full bg-[#DECBB1]"></span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        @if($eqSlot['item'])
                                            <p class="font-bold text-[#4A2200] truncate">@if((int)$eqSlot['item']->refine > 0)<span class="text-[#D4441D]">+{{ $eqSlot['item']->refine }}</span> @endif{{ $eqSlot['item']->item_name }}</p>
                                            <p class="text-[10px] text-[#9E5600] truncate">{{ $eqSlot['label'] }}</p>
                                        @else
                                            <p class="text-[11px] text-[#8C7A68] italic">{{ $eqSlot['label'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </template>
                </div>

                <!-- Center Sprite Stage -->
                <div class="flex flex-col items-center justify-center py-6 px-4 rounded-2xl bg-gradient-to-b from-[#FAF4EA] to-[#EFE4D0] border-2 border-[#DECBB1] min-h-[260px] shadow-inner">
                    <img src="{{ $liveSpriteUrl }}" alt="{{ $character->name }}" class="w-28 h-36 object-contain scale-125 drop-shadow" onerror="this.style.display='none'">
                    <span class="mt-3 px-3 py-0.5 rounded-full bg-[#4A2200] text-[11px] font-bold text-[#FFE4A0]">{{ $character->job_name }}</span>
                </div>

                <!-- Right Equipment Column -->
                <div class="space-y-2.5">
                    <template x-if="equipTab === 'general'">
                        <div class="space-y-2.5">
                            @foreach($generalRight as $eqSlot)
                                <div class="h-12 px-3 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] flex items-center justify-between gap-2.5 text-right">
                                    <div class="min-w-0 flex-1">
                                        @if($eqSlot['item'])
                                            <p class="font-bold text-[#4A2200] truncate">@if((int)$eqSlot['item']->refine > 0)<span class="text-[#D4441D]">+{{ $eqSlot['item']->refine }}</span> @endif{{ $eqSlot['item']->item_name }}</p>
                                            <p class="text-[10px] text-[#9E5600] truncate">{{ $eqSlot['label'] }}@if(!empty($eqSlot['item']->cards)) • <span class="text-[#1E4620]">{{ implode(', ', $eqSlot['item']->cards) }}</span>@endif</p>
                                        @else
                                            <p class="text-[11px] text-[#8C7A68] italic">{{ $eqSlot['label'] }}</p>
                                        @endif
                                    </div>
                                    <div class="w-8 h-8 rounded-lg bg-white border border-[#DECBB1] flex items-center justify-center shrink-0">
                                        @if($eqSlot['item'])
                                            <img src="{{ $eqSlot['item']->icon_url }}" alt="" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                        @else
                                            <span class="w-4 h-2 rounded-full bg-[#DECBB1]"></span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </template>
                    <template x-if="equipTab === 'special'">
                        <div class="space-y-2.5">
                            @foreach($specialRight as $eqSlot)
                                <div class="h-12 px-3 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] flex items-center justify-between gap-2.5 text-right">
                                    <div class="min-w-0 flex-1">
                                        @if($eqSlot['item'])
                                            <p class="font-bold text-[#4A2200] truncate">@if((int)$eqSlot['item']->refine > 0)<span class="text-[#D4441D]">+{{ $eqSlot['item']->refine }}</span> @endif{{ $eqSlot['item']->item_name }}</p>
                                            <p class="text-[10px] text-[#9E5600] truncate">{{ $eqSlot['label'] }}</p>
                                        @else
                                            <p class="text-[11px] text-[#8C7A68] italic">{{ $eqSlot['label'] }}</p>
                                        @endif
                                    </div>
                                    <div class="w-8 h-8 rounded-lg bg-white border border-[#DECBB1] flex items-center justify-center shrink-0">
                                        @if($eqSlot['item'])
                                            <img src="{{ $eqSlot['item']->icon_url }}" alt="" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                        @else
                                            <span class="w-4 h-2 rounded-full bg-[#DECBB1]"></span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </template>
                </div>
            </div>

            <!-- RO Status Panel -->
            @php
                $statCost = fn (int $val) => $val < 100 ? (intdiv(max(1, $val) - 1, 10) + 2) : (4 * intdiv($val - 100, 5) + 16);
                $roAtk = (int) $character->str + intdiv((int) $character->dex, 5) + intdiv((int) $character->luk, 3);
                $roMatk = (int) $character->int + intdiv((int) $character->int, 2);
                $roHit = (int) $character->base_level + (int) $character->dex;
                $roCrit = 1 + intdiv((int) $character->luk, 3);
                $roFlee = (int) $character->base_level + (int) $character->agi;
                $roAspd = min(193, 130 + intdiv((int) $character->agi + intdiv((int) $character->dex, 4), 2));
            @endphp
            <div class="p-5 space-y-3">
                <h3 class="text-[11px] font-cinzel font-bold uppercase tracking-wider text-[#4A3319] border-b border-[#DECBB1] pb-2">Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                    <div class="md:col-span-5 space-y-1.5">
                        @foreach(['str' => 'Str', 'agi' => 'Agi', 'vit' => 'Vit', 'int' => 'Int', 'dex' => 'Dex', 'luk' => 'Luk'] as $k => $lbl)
                            @php $sVal = (int) $character->{$k}; @endphp
                            <div class="grid grid-cols-12 items-center gap-1.5 text-xs">
                                <span class="col-span-3 font-bold text-[#9E5600]">{{ $lbl }}</span>
                                <span class="col-span-4 px-2 py-1 rounded bg-[#FAF4EA] border border-[#DECBB1] font-mono font-bold text-[#4A2200] text-right">{{ $sVal }}</span>
                                <span class="col-span-2 text-center text-[#8C7A68] text-[10px]">▶</span>
                                <span class="col-span-3 px-2 py-1 rounded bg-[#EFE4D0] border border-[#DECBB1] font-mono text-[#795E43] text-right">{{ $statCost($sVal) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="md:col-span-4 space-y-1.5">
                        <div class="flex justify-between px-3 py-1 rounded bg-[#FAF4EA] border border-[#DECBB1]"><span class="font-bold text-[#9E5600]">Atk</span><span class="font-mono text-[#4A2200]">{{ $roAtk }}</span></div>
                        <div class="flex justify-between px-3 py-1 rounded bg-[#FAF4EA] border border-[#DECBB1]"><span class="font-bold text-[#9E5600]">Matk</span><span class="font-mono text-[#4A2200]">{{ $roMatk }}</span></div>
                        <div class="flex justify-between px-3 py-1 rounded bg-[#FAF4EA] border border-[#DECBB1]"><span class="font-bold text-[#9E5600]">Hit</span><span class="font-mono text-[#4A2200]">{{ $roHit }}</span></div>
                        <div class="flex justify-between px-3 py-1 rounded bg-[#FAF4EA] border border-[#DECBB1]"><span class="font-bold text-[#9E5600]">Critical</span><span class="font-mono text-[#4A2200]">{{ $roCrit }}</span></div>
                        <div class="flex justify-between px-3 py-1 rounded bg-[#EFE4D0] border border-[#DECBB1]"><span class="font-bold text-[#D4441D]">Status Point</span><span class="font-mono font-bold text-[#D4441D]">{{ number_format($character->status_point) }}</span></div>
                        <div class="flex justify-between px-3 py-1 rounded bg-[#FAF4EA] border border-[#DECBB1]"><span class="font-bold text-[#9E5600]">Guild</span><span class="font-semibold text-[#4A2200]">{{ $character->guild_name ?: 'None' }}</span></div>
                    </div>
                    <div class="md:col-span-3 space-y-1.5">
                        <div class="flex justify-between px-3 py-1 rounded bg-[#FAF4EA] border border-[#DECBB1]"><span class="font-bold text-[#9E5600]">Def</span><span class="font-mono text-[#4A2200]">{{ (int) $character->vit }}</span></div>
                        <div class="flex justify-between px-3 py-1 rounded bg-[#FAF4EA] border border-[#DECBB1]"><span class="font-bold text-[#9E5600]">Mdef</span><span class="font-mono text-[#4A2200]">{{ (int) $character->int }}</span></div>
                        <div class="flex justify-between px-3 py-1 rounded bg-[#FAF4EA] border border-[#DECBB1]"><span class="font-bold text-[#9E5600]">Flee</span><span class="font-mono text-[#4A2200]">{{ $roFlee }}</span></div>
                        <div class="flex justify-between px-3 py-1 rounded bg-[#FAF4EA] border border-[#DECBB1]"><span class="font-bold text-[#9E5600]">Aspd</span><span class="font-mono text-[#4A2200]">{{ $roAspd }}</span></div>
                        <div class="flex justify-between px-3 py-1 rounded bg-[#EFE4D0] border border-[#DECBB1]"><span class="font-bold text-[#D4441D]">Skill Pt</span><span class="font-mono font-bold text-[#D4441D]">{{ number_format($character->skill_point) }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 chronicles-panel-ivy space-y-4 h-fit">
            <h2 class="text-sm font-cinzel font-bold text-[#4A2200] border-b border-[#DECBB1] pb-2">Character Details</h2>
            <div class="space-y-2 text-[#5A3D28]">
                <div class="flex justify-between"><span class="text-[#8C7A68]">Character ID</span><span class="font-mono">#{{ $character->char_id }}</span></div>
                <div class="flex justify-between"><span class="text-[#8C7A68]">Zeny</span><span class="font-bold text-[#9E5600]">{{ number_format($character->zeny) }} Z</span></div>
                <div class="flex justify-between"><span class="text-[#8C7A68]">Location</span><span class="font-mono">{{ $character->last_map }} ({{ $character->last_x }}, {{ $character->last_y }})</span></div>
                <div class="flex justify-between"><span class="text-[#8C7A68]">Hair Style / Color</span><span>#{{ $character->hair }} / #{{ $character->hair_color }}</span></div>
                <div class="flex justify-between"><span class="text-[#8C7A68]">Clothes Dye</span><span>#{{ $character->clothes_color }}</span></div>
                <div class="flex justify-between"><span class="text-[#8C7A68]">Account Gender</span><span>{{ $charSex === 'F' ? 'Female (♀)' : 'Male (♂)' }}</span></div>
                <div class="flex justify-between"><span class="text-[#8C7A68]">Last Login</span><span>{{ $character->last_login ?: 'Never' }}</span></div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Inventory & Kafra Storage -->
    <div x-show="tab === 'bags'" style="display: none;" class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs">
        <div class="chronicles-panel-ivy p-0 overflow-hidden flex flex-col" x-data="{ items: @js($invJs), selectedItem: null }">
            <div class="px-5 py-3 bg-[#4A2200] border-b border-[#8B5A2B] flex items-center justify-between">
                <span class="font-cinzel font-bold text-[#FFE4A0]">Inventory ({{ count($invJs) }} items)</span>
                <span class="text-[11px] text-[#EAD2B0]">Click an item to inspect</span>
            </div>
            <div class="p-4 flex-1 flex flex-col justify-between">
                <div class="grid grid-cols-5 sm:grid-cols-7 gap-2 max-h-72 overflow-y-auto pr-1">
                    <template x-for="item in items" :key="item.id">
                        <button type="button" @click="selectedItem = item"
                                :class="selectedItem && selectedItem.id === item.id ? 'border-[#D4441D] bg-[#EFE4D0]' : 'border-[#DECBB1] bg-[#FAF4EA] hover:border-[#9E5600]'"
                                class="relative aspect-square rounded-xl border flex items-center justify-center p-1.5 cursor-pointer" :title="item.name">
                            <img :src="item.icon" :alt="item.name" class="w-7 h-7 object-contain" onerror="this.src='/images/item-placeholder.png'">
                            <span x-show="item.refine > 0" class="absolute top-0.5 left-1 text-[9px] font-bold text-[#D4441D]" x-text="'+' + item.refine"></span>
                            <span x-show="item.amount > 1" class="absolute bottom-0.5 right-1 text-[9px] font-mono font-bold text-[#4A2200] bg-white/90 px-1 rounded" x-text="item.amount"></span>
                        </button>
                    </template>
                    <template x-if="items.length === 0"><div class="col-span-7 py-16 text-center text-[#8C7A68]">Inventory is empty.</div></template>
                </div>
                <div class="mt-3 pt-2.5 border-t border-[#DECBB1] min-h-[32px]">
                    <template x-if="selectedItem">
                        <div class="w-full space-y-0.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-[#4A2200] truncate"><span x-show="selectedItem.refine > 0" class="text-[#D4441D]" x-text="'+' + selectedItem.refine + ' '"></span><span x-text="selectedItem.name"></span></span>
                                <span class="font-mono text-[#9E5600] shrink-0" x-text="'#' + selectedItem.nameid + ' • Qty: ' + selectedItem.amount"></span>
                            </div>
                            <div x-show="selectedItem.cards && selectedItem.cards.length > 0" class="text-[10px] text-[#1E4620]" x-text="'Cards: ' + (selectedItem.cards || []).join(', ')"></div>
                        </div>
                    </template>
                    <template x-if="!selectedItem"><span class="text-[#8C7A68]">Select an item slot above to view details.</span></template>
                </div>
            </div>
        </div>

        <div class="chronicles-panel-ivy p-0 overflow-hidden flex flex-col"
             x-data="{ search: '', sortAsc: true, items: @js($storageJs), get filtered() { const q = (this.search || '').trim().toLowerCase(); return this.items.filter(i => !q || i.name.toLowerCase().includes(q) || String(i.nameid).includes(q)).sort((a, b) => this.sortAsc ? a.name.localeCompare(b.name) : b.name.localeCompare(a.name)); } }">
            <div class="px-5 py-3 bg-[#4A2200] border-b border-[#8B5A2B] flex items-center justify-between">
                <span class="font-cinzel font-bold text-[#FFE4A0]">Kafra Storage</span>
                <span class="text-[11px] font-mono text-[#FFE4A0]">{{ count($storageJs) }} / 600</span>
            </div>
            <div class="p-4 flex-1 flex flex-col justify-between">
                <div class="space-y-1.5 max-h-64 overflow-y-auto pr-1">
                    <template x-for="item in filtered" :key="item.id">
                        <div class="px-3 py-2 rounded-xl bg-[#FAF4EA] border border-[#DECBB1] flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img :src="item.icon" :alt="item.name" class="w-6 h-6 object-contain shrink-0" onerror="this.src='/images/item-placeholder.png'">
                                <div class="min-w-0">
                                    <p class="font-bold text-[#4A2200] truncate"><span x-show="item.refine > 0" class="text-[#D4441D]" x-text="'+' + item.refine + ' '"></span><span x-text="item.name"></span></p>
                                    <p class="text-[10px] font-mono text-[#8C7A68] truncate"><span x-text="'#' + item.nameid"></span><span x-show="item.cards && item.cards.length > 0" class="text-[#1E4620] font-sans ml-1.5" x-text="'• ' + (item.cards || []).join(', ')"></span></p>
                                </div>
                            </div>
                            <span class="font-mono font-bold text-[#9E5600] shrink-0" x-text="item.amount + 'x'"></span>
                        </div>
                    </template>
                </div>
                <div class="mt-3 pt-2.5 border-t border-[#DECBB1] flex items-center gap-2">
                    <input type="text" x-model="search" placeholder="Search storage..." class="flex-1 bg-[#FAF4EA] border border-[#DECBB1] rounded-lg px-3 py-1.5 text-[#4A2200] text-xs">
                    <button type="button" @click="sortAsc = !sortAsc" class="px-3 py-1.5 rounded-lg bg-[#4A2200] text-[#FFE4A0] font-bold cursor-pointer" x-text="sortAsc ? 'Sort A-Z' : 'Sort Z-A'"></button>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: Self-Services -->
    <div x-show="tab === 'services'" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        @if($settings['rename_enabled'])
            <form method="POST" action="{{ route('account.characters.services.rename', $character->char_id) }}" class="chronicles-panel-ivy space-y-3">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-rename">
                <div class="flex justify-between items-center"><h3 class="font-cinzel text-sm font-bold text-[#4A2200]">Character Rename</h3><span class="font-bold text-[#9E5600]">{{ number_format($settings['rename_cost']) }} CP</span></div>
                <input type="text" name="new_name" required minlength="4" maxlength="23" placeholder="New character name" class="w-full bg-[#FAF4EA] border border-[#DECBB1] rounded-xl px-3 py-2 text-[#4A2200]">
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#4A2200] text-[#FFE4A0] font-bold cursor-pointer">Rename Character</button>
            </form>
        @endif

        @if($settings['appearance_enabled'])
            @php
                $maxHair = max(1, (int) $settings['appearance_max_hair']);
                $maxHairColor = max(0, (int) $settings['appearance_max_hair_color']);
                $maxClothesColor = max(0, (int) $settings['appearance_max_clothes_color']);
            @endphp
            <form method="POST" action="{{ route('account.characters.services.appearance', $character->char_id) }}"
                  class="md:col-span-2 chronicles-panel-ivy space-y-5"
                  x-data="{
                      gender: @js($charSex), jobId: {{ (int) $character->class }}, jobName: @js($character->job_name),
                      hairStyle: {{ max(1, (int) $character->hair) }}, hairColor: {{ (int) $character->hair_color }}, clothesColor: {{ (int) $character->clothes_color }},
                      direction: 0, hairColors: @js($hairColors),
                      proxyBaseUrl: @js(route('account.characters.sprite-preview')), hairProxyBaseUrl: @js(route('account.characters.hair-preview')),
                      previewSpriteSrc: '', previewFailed: false,
                      init() { this.refreshPreview(); this.$watch('hairStyle', () => this.refreshPreview()); this.$watch('hairColor', () => this.refreshPreview()); this.$watch('clothesColor', () => this.refreshPreview()); this.$watch('direction', () => this.refreshPreview()); },
                      refreshPreview() { this.previewFailed = false; this.previewSpriteSrc = `${this.proxyBaseUrl}?gender=${this.gender}&job=${this.jobId}&hair=${this.hairStyle}&color=${this.hairColor}&dye=${this.clothesColor}&dir=${this.direction}`; },
                      hairIconUrl(style) { return `${this.hairProxyBaseUrl}?gender=${this.gender}&hair=${style}`; },
                      get selectedHairColor() { return this.hairColors[this.hairColor] || { name: 'Color #' + this.hairColor, hex: '#94a3b8' }; }
                  }">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-appearance">
                <div class="flex justify-between items-center border-b border-[#DECBB1] pb-3">
                    <h3 class="font-cinzel text-base font-bold text-[#4A2200]">Appearance Change</h3>
                    <span class="font-bold text-[#9E5600]">{{ number_format($settings['appearance_cost']) }} CP</span>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-2xl p-5 flex flex-col items-center text-center space-y-2">
                        <div class="flex gap-2">
                            <button type="button" @click="direction = (direction + 7) % 8" class="px-2 py-0.5 rounded bg-[#EFE4D0] border border-[#DECBB1] cursor-pointer">↺</button>
                            <button type="button" @click="direction = (direction + 1) % 8" class="px-2 py-0.5 rounded bg-[#EFE4D0] border border-[#DECBB1] cursor-pointer">↻</button>
                        </div>
                        <img x-show="!previewFailed" :src="previewSpriteSrc" class="w-28 h-32 object-contain scale-125" x-on:error="previewFailed = true">
                        <span class="font-bold text-[#4A2200]" x-text="jobName"></span>
                    </div>
                    <div class="lg:col-span-2 space-y-4">
                        <div class="grid grid-cols-5 sm:grid-cols-7 gap-2 max-h-48 overflow-y-auto pr-1">
                            @for($hStyle = 1; $hStyle <= $maxHair; $hStyle++)
                                <label @click="hairStyle = {{ $hStyle }}" :class="Number(hairStyle) === {{ $hStyle }} ? 'bg-[#4A2200] text-[#FFE4A0] border-[#D4AF37]' : 'bg-[#FAF4EA] border-[#DECBB1] text-[#4A2200]'" class="p-2 rounded-xl border flex flex-col items-center gap-1 cursor-pointer">
                                    <input type="radio" name="hair" value="{{ $hStyle }}" x-model="hairStyle" class="hidden">
                                    <img :src="hairIconUrl({{ $hStyle }})" loading="lazy" class="w-9 h-9 object-contain" onerror="this.style.display='none'">
                                    <span class="text-[10px] font-mono">#{{ $hStyle }}</span>
                                </label>
                            @endfor
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @for($hColor = 0; $hColor <= $maxHairColor; $hColor++)
                                @php $cInfo = $hairColors[$hColor] ?? ['name' => "Color #{$hColor}", 'hex' => '#94a3b8']; @endphp
                                <label @click.prevent="hairColor = {{ $hColor }}" :class="Number(hairColor) === {{ $hColor }} ? 'border-[#D4441D] bg-[#EFE4D0]' : 'border-[#DECBB1] bg-[#FAF4EA]'" class="px-2.5 py-1.5 rounded-xl border flex items-center gap-1.5 cursor-pointer text-[#4A2200]">
                                    <input type="radio" name="hair_color" value="{{ $hColor }}" :checked="Number(hairColor) === {{ $hColor }}" class="hidden">
                                    <span class="w-4 h-4 rounded border border-black/20" style="background-color: {{ $cInfo['hex'] }};"></span>
                                    <span>{{ $cInfo['name'] }}</span>
                                </label>
                            @endfor
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @for($cDye = 0; $cDye <= $maxClothesColor; $cDye++)
                                <label @click.prevent="clothesColor = {{ $cDye }}" :class="Number(clothesColor) === {{ $cDye }} ? 'bg-[#4A2200] text-[#FFE4A0]' : 'bg-[#FAF4EA] text-[#4A2200]'" class="px-3 py-1 rounded-xl border border-[#DECBB1] font-mono font-bold cursor-pointer">
                                    <input type="radio" name="clothes_color" value="{{ $cDye }}" :checked="Number(clothesColor) === {{ $cDye }}" class="hidden">#{{ $cDye }}
                                </label>
                            @endfor
                        </div>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#4A2200] text-[#FFE4A0] font-bold cursor-pointer">Apply Appearance Changes</button>
                    </div>
                </div>
            </form>
        @endif

        @if($settings['job_change_enabled'])
            <form method="POST" action="{{ route('account.characters.services.job', $character->char_id) }}"
                  class="md:col-span-2 chronicles-panel-ivy space-y-4"
                  x-data="{ gender: @js($charSex), hairStyle: {{ max(1, (int) $character->hair) }}, hairColor: {{ (int) $character->hair_color }}, clothesColor: {{ (int) $character->clothes_color }}, selectedClass: {{ (int) $character->class }}, jobSearch: '', jobs: @js($availableJobs), proxyBaseUrl: @js(route('account.characters.sprite-preview')), get filteredJobs() { const q = (this.jobSearch || '').trim().toLowerCase(); return !q ? this.jobs : this.jobs.filter(j => j.name.toLowerCase().includes(q) || String(j.id).includes(q)); }, get previewUrl() { return `${this.proxyBaseUrl}?gender=${this.gender}&job=${this.selectedClass}&hair=${this.hairStyle}&color=${this.hairColor}&dye=${this.clothesColor}&dir=0`; } }">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-job">
                <input type="hidden" name="target_class" :value="selectedClass">
                <div class="flex justify-between items-center border-b border-[#DECBB1] pb-3">
                    <h3 class="font-cinzel text-base font-bold text-[#4A2200]">Job / Class Change</h3>
                    <span class="font-bold text-[#9E5600]">{{ number_format($settings['job_change_cost']) }} CP</span>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-[#FAF4EA] border border-[#DECBB1] rounded-2xl p-5 flex flex-col items-center">
                        <img :src="previewUrl" class="w-28 h-32 object-contain scale-125" onerror="this.style.display='none'">
                        <span class="font-mono text-[#9E5600] mt-2" x-text="'Class ID #' + selectedClass"></span>
                    </div>
                    <div class="lg:col-span-2 space-y-3">
                        <input type="text" x-model="jobSearch" placeholder="Search job name or ID..." class="w-full bg-[#FAF4EA] border border-[#DECBB1] rounded-xl px-3 py-2 text-[#4A2200]">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-48 overflow-y-auto pr-1">
                            <template x-for="job in filteredJobs" :key="job.id">
                                <button type="button" @click="selectedClass = job.id" :class="Number(selectedClass) === Number(job.id) ? 'bg-[#4A2200] text-[#FFE4A0]' : 'bg-[#FAF4EA] text-[#4A2200] border-[#DECBB1]'" class="p-2 rounded-xl border text-left font-semibold cursor-pointer flex justify-between"><span x-text="job.name" class="truncate"></span><span class="font-mono text-[10px]" x-text="'#'+job.id"></span></button>
                            </template>
                        </div>
                        <button type="submit" :disabled="Number(selectedClass) === {{ (int) $character->class }}" class="px-6 py-2.5 rounded-xl bg-[#4A2200] text-[#FFE4A0] font-bold disabled:opacity-50 cursor-pointer">Change Job Class</button>
                    </div>
                </div>
            </form>
        @endif

        @if($settings['stat_reset_enabled'])
            <form method="POST" action="{{ route('account.characters.services.stat-reset', $character->char_id) }}" class="chronicles-panel-ivy space-y-3" onsubmit="return confirm('Reset all character attributes?');">
                @csrf<input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-stat">
                <div class="flex justify-between"><h3 class="font-cinzel text-sm font-bold text-[#4A2200]">Stat Reset</h3><span class="font-bold text-[#9E5600]">{{ number_format($settings['stat_reset_cost']) }} CP</span></div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#9E5600] text-white font-bold cursor-pointer">Reset Stats</button>
            </form>
        @endif

        @if($settings['skill_reset_enabled'])
            <form method="POST" action="{{ route('account.characters.services.skill-reset', $character->char_id) }}" class="chronicles-panel-ivy space-y-3" onsubmit="return confirm('Reset all learned skills?');">
                @csrf<input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-skill">
                <div class="flex justify-between"><h3 class="font-cinzel text-sm font-bold text-[#4A2200]">Skill Reset</h3><span class="font-bold text-[#9E5600]">{{ number_format($settings['skill_reset_cost']) }} CP</span></div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#9E5600] text-white font-bold cursor-pointer">Reset Skills</button>
            </form>
        @endif

        @if($settings['gender_enabled'])
            <form method="POST" action="{{ route('account.characters.services.gender', $character->char_id) }}" class="chronicles-panel-ivy space-y-3">
                @csrf<input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-gender">
                <div class="flex justify-between"><h3 class="font-cinzel text-sm font-bold text-[#4A2200]">Account Gender Change</h3><span class="font-bold text-[#9E5600]">{{ number_format($settings['gender_cost']) }} CP</span></div>
                <div class="flex gap-2">
                    <select name="gender" class="bg-[#FAF4EA] border border-[#DECBB1] rounded-xl px-3 py-2 text-[#4A2200]"><option value="M" {{ $charSex === 'M' ? 'selected' : '' }}>Male (M)</option><option value="F" {{ $charSex === 'F' ? 'selected' : '' }}>Female (F)</option></select>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#4A2200] text-[#FFE4A0] font-bold cursor-pointer">Change Gender</button>
                </div>
            </form>
        @endif

        @if($settings['lock_enabled'] || $settings['deletion_enabled'])
            <div class="chronicles-panel-ivy space-y-3">
                <h3 class="font-cinzel text-sm font-bold text-[#4A2200]">Security &amp; Deletion</h3>
                @if($settings['lock_enabled'] && !$active_lock)
                    <form method="POST" action="{{ route('account.characters.services.lock', $character->char_id) }}" class="flex gap-2">
                        @csrf<input type="hidden" name="action" value="lock">
                        <input type="text" name="reason" placeholder="Lock reason..." class="flex-1 bg-[#FAF4EA] border border-[#DECBB1] rounded-xl px-3 py-2 text-[#4A2200]">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#4A2200] text-[#FFE4A0] font-bold cursor-pointer">Self-Lock</button>
                    </form>
                @endif
                @if($settings['deletion_enabled'] && (!$deletion_request || $deletion_request->status !== 'pending'))
                    <form method="POST" action="{{ route('account.characters.services.deletion.request', $character->char_id) }}" onsubmit="return confirm('Schedule deletion?');">
                        @csrf<button type="submit" class="px-4 py-2 rounded-xl bg-[#A82508] text-white font-bold cursor-pointer">Request Deletion ({{ $settings['deletion_wait_hours'] }}h)</button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <!-- TAB 4: Privacy & Customization -->
    <div x-show="tab === 'privacy'" style="display: none;" class="max-w-3xl text-xs">
        <form method="POST" action="{{ route('account.characters.privacy', $character->char_id) }}" enctype="multipart/form-data" class="chronicles-panel-ivy space-y-4">
            @csrf
            @php
                $privacyFields = ['is_public' => 'Public Profile', 'show_char_info' => 'Basic Info', 'show_equipments' => 'Equipments', 'show_stats' => 'Stats & Attributes', 'show_zeny' => 'Zeny Balance', 'show_guild' => 'Guild Standing', 'show_last_login' => 'Last Login Time'];
            @endphp
            <h2 class="font-cinzel text-sm font-bold text-[#4A2200] border-b border-[#DECBB1] pb-2">Profile Customization &amp; Privacy</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach($privacyFields as $pField => $pLabel)
                    <label class="flex items-center gap-2 bg-[#FAF4EA] border border-[#DECBB1] rounded-xl px-3 py-2 text-[#4A2200] cursor-pointer">
                        <input type="checkbox" name="{{ $pField }}" value="1" {{ ($privacy->$pField ?? true) ? 'checked' : '' }}>
                        <span>{{ $pLabel }}</span>
                    </label>
                @endforeach
            </div>
            <input type="url" name="avatar_url" value="{{ !empty($privacy->avatar_path) && filter_var($privacy->avatar_path, FILTER_VALIDATE_URL) ? $privacy->avatar_path : '' }}" placeholder="Image URL..." class="w-full bg-[#FAF4EA] border border-[#DECBB1] rounded-xl px-3 py-2 text-[#4A2200]">
            <input type="file" name="avatar_file" accept="image/*" class="w-full text-xs text-[#5A3D28]">
            <input type="text" name="bio" value="{{ $privacy->bio ?? '' }}" placeholder="Adventurer bio / motto..." class="w-full bg-[#FAF4EA] border border-[#DECBB1] rounded-xl px-3 py-2 text-[#4A2200]">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#4A2200] text-[#FFE4A0] font-bold cursor-pointer">Save Profile &amp; Privacy</button>
        </form>
    </div>

    <!-- TAB 5: Activity -->
    <div x-show="tab === 'activity'" style="display: none;" class="chronicles-panel-ivy space-y-2 text-xs">
        <h3 class="font-cinzel text-sm font-bold text-[#4A2200] border-b border-[#DECBB1] pb-2">Recent Service History</h3>
        @forelse($logs as $log)
            <div class="py-2 flex justify-between border-b border-[#DECBB1]/60">
                <span class="font-bold text-[#4A2200]">{{ $log->action_type }} ({{ $log->result }})</span>
                <span class="text-[#8C7A68]">{{ $log->created_at->format('M d, Y H:i') }}</span>
            </div>
        @empty
            <p class="py-4 text-center text-[#8C7A68]">No service activity recorded yet.</p>
        @endforelse
    </div>
</div>
@endsection
