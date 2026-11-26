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
        'type' => (string) ($i->item_type ?? 'etc'),
        'favorite' => (int) ($i->favorite ?? 0) > 0,
        'icon' => (string) $i->icon_url,
    ])->values()->all();

    $storageJs = collect($storage)->map(fn ($s) => [
        'id' => (int) $s->id,
        'nameid' => (int) $s->nameid,
        'name' => (string) $s->item_name,
        'amount' => (int) $s->amount,
        'refine' => (int) ($s->refine ?? 0),
        'type' => (string) ($s->item_type ?? 'etc'),
        'icon' => (string) $s->icon_url,
    ])->values()->all();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="{ tab: 'profile', equipTab: 'general' }">
    <!-- Hero Character Header -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 sm:p-6 backdrop-blur-xl shadow-2xl flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-950 border border-slate-700/80 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                <img src="{{ $liveSpriteUrl }}" alt="{{ $character->name }}" class="w-14 h-14 object-contain scale-125" onerror="this.style.display='none'">
            </div>
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-2xl font-black text-white tracking-tight">{{ $character->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ (int)$character->online > 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-300 border border-slate-700' }}">
                        {{ (int)$character->online > 0 ? 'Online' : 'Offline' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                        {{ $character->job_name }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400 mt-1.5">
                    <span>Lv. <strong class="text-white">{{ $character->base_level }}</strong> / <strong class="text-slate-200">{{ $character->job_level }}</strong></span>
                    <span>Zeny: <strong class="text-amber-400">{{ number_format($character->zeny) }} Z</strong></span>
                    <span>Cash Points: <strong class="text-cyan-400">{{ number_format($cash_points) }} CP</strong></span>
                    <span>Guild: <strong class="text-slate-200">{{ $character->guild_name ?: 'None' }}</strong></span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5 self-start lg:self-center">
            <a href="{{ route('characters.show', $character->name) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800/80 hover:bg-slate-700 text-cyan-300 border border-slate-700 inline-flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>Public Profile</span>
            </a>
            <a href="{{ route('account.characters') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 inline-flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>My Characters</span>
            </a>
        </div>
    </div>

    <!-- Status Banners -->
    @if((int)$character->online > 0)
        <div class="bg-amber-950/40 border border-amber-500/30 rounded-2xl p-4 text-xs text-amber-200 flex items-center gap-2.5">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-400 shrink-0"></i>
            <span>This character is currently <strong>Online</strong>. Please log out of the game before using character services.</span>
        </div>
    @endif

    @if($active_lock)
        <div class="bg-rose-950/40 border border-rose-500/30 rounded-2xl p-4 text-xs text-rose-200 flex items-center justify-between gap-3">
            <div><strong>Character Locked ({{ $active_lock->lock_scope }}):</strong> {{ $active_lock->reason }}</div>
            @if((int)$active_lock->locked_by === (int)auth()->id() && $active_lock->lock_scope === 'services')
                <form method="POST" action="{{ route('account.characters.services.lock', $character->char_id) }}">
                    @csrf
                    <input type="hidden" name="action" value="unlock">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500 text-slate-950 font-bold cursor-pointer">Unlock</button>
                </form>
            @endif
        </div>
    @endif

    @if($deletion_request && $deletion_request->status === 'pending')
        <div class="bg-amber-950/40 border border-amber-500/30 rounded-2xl p-4 text-xs text-amber-200 flex items-center justify-between gap-3">
            <div><strong>Scheduled for Deletion:</strong> {{ $deletion_request->scheduled_delete_at->format('M d, Y H:i') }}</div>
            <form method="POST" action="{{ route('account.characters.services.deletion.cancel', $character->char_id) }}">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500 text-slate-950 font-bold cursor-pointer">Cancel Deletion</button>
            </form>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-slate-800 pb-3 text-xs font-semibold">
        <button type="button" @click="tab = 'profile'" :class="tab === 'profile' ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/20' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'" class="px-4 py-2 rounded-xl transition-colors cursor-pointer">Equip &amp; Status</button>
        <button type="button" @click="tab = 'bags'" :class="tab === 'bags' ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/20' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'" class="px-4 py-2 rounded-xl transition-colors cursor-pointer">Inventory &amp; Storage</button>
        <button type="button" @click="tab = 'services'" :class="tab === 'services' ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/20' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'" class="px-4 py-2 rounded-xl transition-colors cursor-pointer">Self-Services</button>
        <button type="button" @click="tab = 'privacy'" :class="tab === 'privacy' ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/20' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'" class="px-4 py-2 rounded-xl transition-colors cursor-pointer">Profile Customization &amp; Privacy</button>
        <button type="button" @click="tab = 'activity'" :class="tab === 'activity' ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/20' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'" class="px-4 py-2 rounded-xl transition-colors cursor-pointer">Service History</button>
    </div>

    <!-- TAB 1: RO-Style Equip & Status Window -->
    <div x-show="tab === 'profile'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 text-xs">
        <!-- Left/Main: RO Equip Doll + Status Window -->
        <div class="lg:col-span-8 bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl">
            <!-- Window Titlebar -->
            <div class="px-5 py-3 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border-b border-slate-800 flex items-center justify-between">
                <span class="font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                    <span>Equip &amp; Status</span>
                </span>
                <div class="inline-flex rounded-lg bg-slate-950 border border-slate-800 p-0.5 text-[11px] font-bold">
                    <button type="button" @click="equipTab = 'general'"
                            :class="equipTab === 'general' ? 'bg-cyan-500/20 text-cyan-300' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1 rounded-md transition-colors cursor-pointer">General</button>
                    <button type="button" @click="equipTab = 'special'"
                            :class="equipTab === 'special' ? 'bg-cyan-500/20 text-cyan-300' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1 rounded-md transition-colors cursor-pointer">Special Equipment</button>
                </div>
            </div>

            <!-- 3-Column RO Equip Doll -->
            <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4 items-center bg-slate-950/40 border-b border-slate-800">
                <!-- Left Equipment Column -->
                <div class="space-y-2.5">
                    <template x-if="equipTab === 'general'">
                        <div class="space-y-2.5">
                            @foreach($generalLeft as $slot)
                                <div class="h-12 px-3 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0">
                                        @if($slot['item'])
                                            <img src="{{ $slot['item']->icon_url }}" alt="" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                        @else
                                            <span class="w-4 h-2 rounded-full bg-slate-800"></span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        @if($slot['item'])
                                            <p class="font-bold text-white truncate">{{ $slot['item']->refine > 0 ? "+{$slot['item']->refine} " : '' }}{{ $slot['item']->item_name }}</p>
                                            <p class="text-[10px] text-cyan-400">{{ $slot['label'] }}</p>
                                        @else
                                            <p class="text-[11px] text-slate-600 italic">{{ $slot['label'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </template>
                    <template x-if="equipTab === 'special'">
                        <div class="space-y-2.5">
                            @foreach($specialLeft as $slot)
                                <div class="h-12 px-3 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0">
                                        @if($slot['item'])
                                            <img src="{{ $slot['item']->icon_url }}" alt="" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                        @else
                                            <span class="w-4 h-2 rounded-full bg-slate-800"></span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        @if($slot['item'])
                                            <p class="font-bold text-white truncate">{{ $slot['item']->refine > 0 ? "+{$slot['item']->refine} " : '' }}{{ $slot['item']->item_name }}</p>
                                            <p class="text-[10px] text-cyan-400">{{ $slot['label'] }}</p>
                                        @else
                                            <p class="text-[11px] text-slate-600 italic">{{ $slot['label'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </template>
                </div>

                <!-- Center Sprite Stage -->
                <div class="flex flex-col items-center justify-center py-6 px-4 rounded-2xl bg-gradient-to-b from-slate-900/80 via-slate-950 to-slate-900/80 border border-slate-800/80 min-h-[260px]">
                    <img src="{{ $liveSpriteUrl }}" alt="{{ $character->name }}" class="w-28 h-36 object-contain scale-125 drop-shadow-[0_10px_18px_rgba(0,0,0,0.8)]" onerror="this.style.display='none'">
                    <span class="mt-3 px-3 py-0.5 rounded-full bg-slate-900 border border-slate-700 text-[11px] font-bold text-cyan-300">{{ $character->job_name }}</span>
                </div>

                <!-- Right Equipment Column -->
                <div class="space-y-2.5">
                    <template x-if="equipTab === 'general'">
                        <div class="space-y-2.5">
                            @foreach($generalRight as $slot)
                                <div class="h-12 px-3 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center justify-between gap-2.5 text-right">
                                    <div class="min-w-0 flex-1">
                                        @if($slot['item'])
                                            <p class="font-bold text-white truncate">{{ $slot['item']->refine > 0 ? "+{$slot['item']->refine} " : '' }}{{ $slot['item']->item_name }}</p>
                                            <p class="text-[10px] text-cyan-400">{{ $slot['label'] }}</p>
                                        @else
                                            <p class="text-[11px] text-slate-600 italic">{{ $slot['label'] }}</p>
                                        @endif
                                    </div>
                                    <div class="w-8 h-8 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0">
                                        @if($slot['item'])
                                            <img src="{{ $slot['item']->icon_url }}" alt="" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                        @else
                                            <span class="w-4 h-2 rounded-full bg-slate-800"></span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </template>
                    <template x-if="equipTab === 'special'">
                        <div class="space-y-2.5">
                            @foreach($specialRight as $slot)
                                <div class="h-12 px-3 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center justify-between gap-2.5 text-right">
                                    <div class="min-w-0 flex-1">
                                        @if($slot['item'])
                                            <p class="font-bold text-white truncate">{{ $slot['item']->refine > 0 ? "+{$slot['item']->refine} " : '' }}{{ $slot['item']->item_name }}</p>
                                            <p class="text-[10px] text-cyan-400">{{ $slot['label'] }}</p>
                                        @else
                                            <p class="text-[11px] text-slate-600 italic">{{ $slot['label'] }}</p>
                                        @endif
                                    </div>
                                    <div class="w-8 h-8 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0">
                                        @if($slot['item'])
                                            <img src="{{ $slot['item']->icon_url }}" alt="" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                        @else
                                            <span class="w-4 h-2 rounded-full bg-slate-800"></span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </template>
                </div>
            </div>

            <!-- RO Status Panel Below Equip -->
            @php
                $statCost = fn (int $val) => $val < 100 ? (intdiv(max(1, $val) - 1, 10) + 2) : (4 * intdiv($val - 100, 5) + 16);
                $roAtk = (int) $character->str + intdiv((int) $character->dex, 5) + intdiv((int) $character->luk, 3);
                $roMatk = (int) $character->int + intdiv((int) $character->int, 2);
                $roHit = (int) $character->base_level + (int) $character->dex;
                $roCrit = 1 + intdiv((int) $character->luk, 3);
                $roFlee = (int) $character->base_level + (int) $character->agi;
                $roAspd = min(193, 130 + intdiv((int) $character->agi + intdiv((int) $character->dex, 4), 2));
            @endphp
            <div class="p-5 bg-slate-900/60 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                        <i data-lucide="chevron-up" class="w-3.5 h-3.5 text-cyan-400"></i>
                        <span>Status</span>
                    </h3>
                    <span class="text-[11px] text-slate-400">Show Equip: <strong class="{{ ($privacy->show_equipments ?? true) ? 'text-emerald-400' : 'text-slate-500' }}">{{ ($privacy->show_equipments ?? true) ? 'Yes' : 'No' }}</strong></span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                    <!-- Left: Str..Luk with ► cost -->
                    <div class="md:col-span-5 space-y-1.5">
                        @foreach(['str' => 'Str', 'agi' => 'Agi', 'vit' => 'Vit', 'int' => 'Int', 'dex' => 'Dex', 'luk' => 'Luk'] as $k => $lbl)
                            @php $sVal = (int) $character->{$k}; @endphp
                            <div class="grid grid-cols-12 items-center gap-1.5 text-xs">
                                <span class="col-span-3 font-bold text-cyan-300">{{ $lbl }}</span>
                                <span class="col-span-4 px-2 py-1 rounded bg-slate-950 border border-slate-800 font-mono font-bold text-white text-right">{{ $sVal }}</span>
                                <span class="col-span-2 text-center text-slate-500 text-[10px]">▶</span>
                                <span class="col-span-3 px-2 py-1 rounded bg-slate-950/70 border border-slate-800/80 font-mono text-slate-400 text-right">{{ $statCost($sVal) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Middle: Atk, Matk, Hit, Critical, Status Point, Guild -->
                    <div class="md:col-span-4 space-y-1.5">
                        <div class="flex items-center justify-between px-3 py-1 rounded bg-slate-950/80 border border-slate-800">
                            <span class="font-bold text-cyan-300">Atk</span>
                            <span class="font-mono text-white">{{ $roAtk }}</span>
                        </div>
                        <div class="flex items-center justify-between px-3 py-1 rounded bg-slate-950/80 border border-slate-800">
                            <span class="font-bold text-cyan-300">Matk</span>
                            <span class="font-mono text-white">{{ $roMatk }}</span>
                        </div>
                        <div class="flex items-center justify-between px-3 py-1 rounded bg-slate-950/80 border border-slate-800">
                            <span class="font-bold text-cyan-300">Hit</span>
                            <span class="font-mono text-white">{{ $roHit }}</span>
                        </div>
                        <div class="flex items-center justify-between px-3 py-1 rounded bg-slate-950/80 border border-slate-800">
                            <span class="font-bold text-cyan-300">Critical</span>
                            <span class="font-mono text-white">{{ $roCrit }}</span>
                        </div>
                        <div class="flex items-center justify-between px-3 py-1 rounded bg-slate-950/80 border border-slate-800">
                            <span class="font-bold text-amber-300">Status Point</span>
                            <span class="font-mono font-bold text-amber-300">{{ number_format($character->status_point) }}</span>
                        </div>
                        <div class="flex items-center justify-between px-3 py-1 rounded bg-slate-950/80 border border-slate-800">
                            <span class="font-bold text-cyan-300">Guild</span>
                            <span class="font-semibold text-white truncate max-w-[120px]">{{ $character->guild_name ?: 'None' }}</span>
                        </div>
                    </div>

                    <!-- Right: Def, Mdef, Flee, Aspd, Skill Pt -->
                    <div class="md:col-span-3 space-y-1.5">
                        <div class="flex items-center justify-between px-3 py-1 rounded bg-slate-950/80 border border-slate-800">
                            <span class="font-bold text-cyan-300">Def</span>
                            <span class="font-mono text-white">{{ (int) $character->vit }}</span>
                        </div>
                        <div class="flex items-center justify-between px-3 py-1 rounded bg-slate-950/80 border border-slate-800">
                            <span class="font-bold text-cyan-300">Mdef</span>
                            <span class="font-mono text-white">{{ (int) $character->int }}</span>
                        </div>
                        <div class="flex items-center justify-between px-3 py-1 rounded bg-slate-950/80 border border-slate-800">
                            <span class="font-bold text-cyan-300">Flee</span>
                            <span class="font-mono text-white">{{ $roFlee }}</span>
                        </div>
                        <div class="flex items-center justify-between px-3 py-1 rounded bg-slate-950/80 border border-slate-800">
                            <span class="font-bold text-cyan-300">Aspd</span>
                            <span class="font-mono text-white">{{ $roAspd }}</span>
                        </div>
                        <div class="flex items-center justify-between px-3 py-1 rounded bg-slate-950/80 border border-slate-800">
                            <span class="font-bold text-amber-300">Skill Pt</span>
                            <span class="font-mono font-bold text-amber-300">{{ number_format($character->skill_point) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar: Overview & Appearance Summary -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 space-y-4">
                <h2 class="text-sm font-bold text-white border-b border-slate-800 pb-2.5">Character Details</h2>
                <div class="space-y-2.5 text-slate-300">
                    <div class="flex justify-between"><span class="text-slate-400">Character ID</span><span class="font-mono">#{{ $character->char_id }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Zeny</span><span class="font-bold text-amber-400">{{ number_format($character->zeny) }} Z</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Location</span><span class="font-mono">{{ $character->last_map }} ({{ $character->last_x }}, {{ $character->last_y }})</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Save Point</span><span class="font-mono">{{ $character->save_map }} ({{ $character->save_x }}, {{ $character->save_y }})</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Hair Style / Color</span><span>#{{ $character->hair }} / #{{ $character->hair_color }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Clothes Dye</span><span>#{{ $character->clothes_color }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Account Gender</span><span>{{ $charSex === 'F' ? 'Female (♀)' : 'Male (♂)' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Last Login</span><span>{{ $character->last_login ?: 'Never' }}</span></div>
                </div>
                <div class="pt-3 border-t border-slate-800 flex gap-2">
                    <button type="button" @click="tab = 'bags'" class="flex-1 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-300 font-bold cursor-pointer">Open Bags &amp; Storage</button>
                    <button type="button" @click="tab = 'services'" class="flex-1 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold cursor-pointer">Services</button>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: RO-Style Inventory & Kafra Storage Windows -->
    <div x-show="tab === 'bags'" style="display: none;" class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs">
        <!-- Character Inventory Window -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl flex flex-col"
             x-data="{
                 invTab: 'all',
                 items: @js($invJs),
                 selectedItem: null,
                 get filtered() {
                     return this.items.filter(i => {
                         if (this.invTab === 'all') return true;
                         if (this.invTab === 'fav') return i.favorite;
                         if (this.invTab === 'item') return ['healing','usable','delayconsume','cash'].includes(i.type);
                         if (this.invTab === 'gear') return ['weapon','armor','shadowgear','petegg','petarmor','ammo'].includes(i.type);
                         return !['healing','usable','delayconsume','cash','weapon','armor','shadowgear','petegg','petarmor','ammo'].includes(i.type);
                     });
                 }
             }">
            <div class="px-5 py-3 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border-b border-slate-800 flex items-center justify-between">
                <span class="font-bold text-white">Inventory ({{ count($invJs) }} items)</span>
                <span class="text-[11px] text-slate-400">Click an item to inspect</span>
            </div>

            <div class="flex flex-1 min-h-[340px]">
                <!-- Vertical RO Tabs -->
                <div class="w-12 bg-slate-950/90 border-r border-slate-800 flex flex-col py-2 gap-1 shrink-0">
                    @foreach(['all' => 'All', 'item' => 'Item', 'gear' => 'Gear', 'etc' => 'Etc.', 'fav' => 'Fav'] as $tKey => $tLabel)
                        <button type="button" @click="invTab = '{{ $tKey }}'"
                                :class="invTab === '{{ $tKey }}' ? 'bg-cyan-500/20 text-cyan-300 border-r-2 border-cyan-400' : 'text-slate-500 hover:text-slate-300'"
                                class="py-3 text-[10px] font-bold uppercase tracking-wider text-center cursor-pointer transition-colors">
                            {{ $tLabel }}
                        </button>
                    @endforeach
                </div>

                <!-- 7-Column RO Slot Grid -->
                <div class="flex-1 p-4 flex flex-col justify-between bg-slate-950/40">
                    <div class="grid grid-cols-5 sm:grid-cols-7 gap-2 max-h-72 overflow-y-auto pr-1">
                        <template x-for="item in filtered" :key="item.id">
                            <button type="button" @click="selectedItem = item"
                                    :class="selectedItem && selectedItem.id === item.id ? 'border-cyan-400 ring-1 ring-cyan-500/40 bg-slate-800' : 'border-slate-800 bg-slate-900/90 hover:border-slate-600'"
                                    class="relative aspect-square rounded-xl border flex items-center justify-center p-1.5 cursor-pointer transition-all"
                                    :title="item.name">
                                <img :src="item.icon" :alt="item.name" class="w-7 h-7 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                <span x-show="item.refine > 0" class="absolute top-0.5 left-1 text-[9px] font-bold text-cyan-300" x-text="'+' + item.refine"></span>
                                <span x-show="item.amount > 1" class="absolute bottom-0.5 right-1 text-[9px] font-mono font-bold text-white bg-slate-950/80 px-1 rounded" x-text="item.amount"></span>
                            </button>
                        </template>
                        <template x-if="filtered.length === 0">
                            <div class="col-span-7 py-16 text-center text-slate-500">No items in this category.</div>
                        </template>
                    </div>

                    <!-- Selected Item Inspector Bar -->
                    <div class="mt-3 pt-2.5 border-t border-slate-800 flex items-center justify-between text-[11px] min-h-[28px]">
                        <template x-if="selectedItem">
                            <div class="flex items-center justify-between w-full gap-2">
                                <span class="font-bold text-white truncate" x-text="(selectedItem.refine > 0 ? '+' + selectedItem.refine + ' ' : '') + selectedItem.name"></span>
                                <span class="font-mono text-cyan-300 shrink-0" x-text="'#' + selectedItem.nameid + ' • Qty: ' + selectedItem.amount"></span>
                            </div>
                        </template>
                        <template x-if="!selectedItem">
                            <span class="text-slate-500">Select an item slot above to view details.</span>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Kafra Storage Window -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl flex flex-col"
             x-data="{
                 stTab: 'all',
                 search: '',
                 sortAsc: true,
                 items: @js($storageJs),
                 get filtered() {
                     const q = (this.search || '').trim().toLowerCase();
                     let list = this.items.filter(i => {
                         if (q && !i.name.toLowerCase().includes(q) && !String(i.nameid).includes(q)) return false;
                         if (this.stTab === 'all') return true;
                         if (this.stTab === 'item') return ['healing','usable','delayconsume','cash'].includes(i.type);
                         if (this.stTab === 'armor') return ['armor','shadowgear'].includes(i.type);
                         if (this.stTab === 'arms') return ['weapon','ammo'].includes(i.type);
                         if (this.stTab === 'card') return i.type === 'card';
                         return !['healing','usable','delayconsume','cash','armor','shadowgear','weapon','ammo','card'].includes(i.type);
                     });
                     return list.sort((a, b) => this.sortAsc ? a.name.localeCompare(b.name) : b.name.localeCompare(a.name));
                 }
             }">
            <div class="px-5 py-3 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border-b border-slate-800 flex items-center justify-between">
                <span class="font-bold text-white">Kafra Storage</span>
                <span class="text-[11px] font-mono text-cyan-300">{{ count($storageJs) }} / 600</span>
            </div>

            <div class="flex flex-1 min-h-[340px]">
                <!-- Vertical Storage Tabs -->
                <div class="w-14 bg-slate-950/90 border-r border-slate-800 flex flex-col py-2 gap-1 shrink-0">
                    @foreach(['all' => 'All', 'item' => 'Item', 'armor' => 'Armor', 'arms' => 'Arms', 'card' => 'Card', 'etc' => 'Etc.'] as $sKey => $sLabel)
                        <button type="button" @click="stTab = '{{ $sKey }}'"
                                :class="stTab === '{{ $sKey }}' ? 'bg-cyan-500/20 text-cyan-300 border-r-2 border-cyan-400' : 'text-slate-500 hover:text-slate-300'"
                                class="py-2.5 text-[10px] font-bold uppercase tracking-wider text-center cursor-pointer transition-colors">
                            {{ $sLabel }}
                        </button>
                    @endforeach
                </div>

                <!-- Storage Item List + Search/Sort Footer -->
                <div class="flex-1 p-4 flex flex-col justify-between bg-slate-950/40">
                    <div class="space-y-1.5 max-h-64 overflow-y-auto pr-1">
                        <template x-for="item in filtered" :key="item.id">
                            <div class="px-3 py-2 rounded-xl bg-slate-900/90 border border-slate-800/80 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="relative w-8 h-8 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0">
                                        <img :src="item.icon" :alt="item.name" class="w-6 h-6 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-white truncate" x-text="(item.refine > 0 ? '+' + item.refine + ' ' : '') + item.name"></p>
                                        <p class="text-[10px] font-mono text-slate-500" x-text="'#' + item.nameid"></p>
                                    </div>
                                </div>
                                <span class="font-mono font-bold text-cyan-300 shrink-0" x-text="item.amount + 'x'"></span>
                            </div>
                        </template>
                        <template x-if="filtered.length === 0">
                            <div class="py-16 text-center text-slate-500">No storage items found.</div>
                        </template>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-slate-800 flex items-center gap-2">
                        <input type="text" x-model="search" placeholder="Search storage..." class="flex-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-1.5 text-white text-xs focus:border-cyan-500 focus:outline-none">
                        <button type="button" @click="sortAsc = !sortAsc" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold cursor-pointer" x-text="sortAsc ? 'Sort A-Z' : 'Sort Z-A'"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: Self-Services -->
    <div x-show="tab === 'services'" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        @if($settings['rename_enabled'])
            <form method="POST" action="{{ route('account.characters.services.rename', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-rename">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Character Rename</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['rename_cost']) }} CP</span>
                </div>
                <input type="text" name="new_name" required minlength="4" maxlength="23" placeholder="New character name (4-23 chars)" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold cursor-pointer">Rename Character</button>
            </form>
        @endif

        @if($settings['appearance_enabled'])
            @php
                $maxHair = max(1, (int) $settings['appearance_max_hair']);
                $maxHairColor = max(0, (int) $settings['appearance_max_hair_color']);
                $maxClothesColor = max(0, (int) $settings['appearance_max_clothes_color']);
            @endphp
            <form method="POST" action="{{ route('account.characters.services.appearance', $character->char_id) }}"
                  class="md:col-span-2 bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-5"
                  x-data="{
                      gender: @js($charSex),
                      jobId: {{ (int) $character->class }},
                      jobName: @js($character->job_name),
                      hairStyle: {{ max(1, (int) $character->hair) }},
                      hairColor: {{ (int) $character->hair_color }},
                      clothesColor: {{ (int) $character->clothes_color }},
                      direction: 0,
                      hairColors: @js($hairColors),
                      proxyBaseUrl: @js(route('account.characters.sprite-preview')),
                      hairProxyBaseUrl: @js(route('account.characters.hair-preview')),
                      previewSpriteSrc: '',
                      previewFailed: false,
                      _debounceTimer: null,
                      init() {
                          this.refreshPreview();
                          this.$watch('hairStyle', () => this.schedulePreview());
                          this.$watch('hairColor', () => this.schedulePreview());
                          this.$watch('clothesColor', () => this.schedulePreview());
                          this.$watch('direction', () => this.schedulePreview());
                      },
                      schedulePreview() {
                          clearTimeout(this._debounceTimer);
                          this._debounceTimer = setTimeout(() => this.refreshPreview(), 80);
                      },
                      refreshPreview() {
                          this.previewFailed = false;
                          this.previewSpriteSrc = `${this.proxyBaseUrl}?gender=${this.gender}&job=${this.jobId}&hair=${this.hairStyle}&color=${this.hairColor}&dye=${this.clothesColor}&dir=${this.direction}`;
                      },
                      hairIconUrl(style) {
                          return `${this.hairProxyBaseUrl}?gender=${this.gender}&hair=${style}`;
                      },
                      get selectedHairColor() {
                          return this.hairColors[this.hairColor] || { name: 'Color #' + this.hairColor, hex: '#94a3b8' };
                      },
                      rotateLeft() { this.direction = (this.direction + 7) % 8; },
                      rotateRight() { this.direction = (this.direction + 1) % 8; }
                  }">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-appearance">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-white">Appearance Change</h3>
                        <p class="text-[11px] text-slate-400">Preview your character's actual job sprite, hairstyle, hair color, and clothes dye live.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-cyan-500/15 text-cyan-300 font-bold self-start sm:self-auto">{{ number_format($settings['appearance_cost']) }} CP</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-5 space-y-4 h-fit">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Live Preview</span>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="rotateLeft()" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-300 hover:text-white cursor-pointer" title="Rotate Left">↺</button>
                                <button type="button" @click="rotateRight()" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-300 hover:text-white cursor-pointer" title="Rotate Right">↻</button>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-gradient-to-b from-slate-950 via-slate-900/90 to-slate-950 border border-slate-800 p-5 flex flex-col items-center justify-center text-center space-y-2">
                            <div class="relative w-28 h-32 flex items-center justify-center">
                                <img x-show="previewSpriteSrc && !previewFailed"
                                     :src="previewSpriteSrc"
                                     :alt="jobName"
                                     class="max-w-full max-h-full object-contain drop-shadow-[0_6px_12px_rgba(0,0,0,0.65)] scale-125"
                                     x-on:error="previewFailed = true">
                                <div x-show="!previewSpriteSrc || previewFailed" style="display: none;" class="flex flex-col items-center justify-center gap-1">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center border border-white/20 text-lg font-black text-white"
                                         :style="`background: radial-gradient(circle at 35% 30%, ${selectedHairColor.hex}, #0f172a 95%);`"
                                         x-text="gender === 'F' ? '♀' : '♂'"></div>
                                    <span class="text-[10px] font-bold text-cyan-300" x-text="jobName"></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-950 border border-slate-700/80 text-[11px] font-bold text-cyan-300" x-text="jobName"></span>
                            <p class="text-sm font-black text-white">{{ $character->name }}</p>
                        </div>

                        <div class="space-y-1.5 text-[11px] bg-slate-900/60 p-3.5 rounded-xl border border-slate-800/80">
                            <div class="flex justify-between"><span class="text-slate-400">Hair Style</span><span class="font-mono font-bold text-cyan-300" x-text="'#' + hairStyle"></span></div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Hair Color</span>
                                <span class="inline-flex items-center gap-1.5 font-semibold text-white">
                                    <span class="w-3 h-3 rounded-full border border-white/40" :style="`background-color: ${selectedHairColor.hex};`"></span>
                                    <span x-text="selectedHairColor.name"></span>
                                </span>
                            </div>
                            <div class="flex justify-between"><span class="text-slate-400">Clothes Dye</span><span class="font-mono font-bold text-amber-300" x-text="'Palette #' + clothesColor"></span></div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-5">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-slate-300">Hair Style ({{ $charSex === 'F' ? 'Female' : 'Male' }})</label>
                                <span class="text-[11px] font-mono text-cyan-400" x-text="'Selected: #' + hairStyle"></span>
                            </div>
                            <div class="grid grid-cols-5 sm:grid-cols-7 gap-2 max-h-52 overflow-y-auto pr-1">
                                @for($hStyle = 1; $hStyle <= $maxHair; $hStyle++)
                                    <label @click="hairStyle = {{ $hStyle }}"
                                           :class="Number(hairStyle) === {{ $hStyle }} ? 'bg-cyan-500/20 border-cyan-400 ring-2 ring-cyan-500/40 text-white' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:border-slate-700'"
                                           class="p-2 rounded-xl border flex flex-col items-center gap-1 cursor-pointer transition-all">
                                        <input type="radio" name="hair" value="{{ $hStyle }}" x-model="hairStyle" class="hidden">
                                        <div class="w-10 h-10 rounded-lg bg-slate-900/90 border border-slate-800 flex items-center justify-center overflow-hidden">
                                            <img :src="hairIconUrl({{ $hStyle }})" loading="lazy" alt="Style #{{ $hStyle }}" class="w-9 h-9 object-contain"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <span style="display: none;" class="text-xs font-bold text-slate-300">{{ $charSex === 'F' ? '♀' : '♂' }}</span>
                                        </div>
                                        <span class="text-[10px] font-mono font-bold">#{{ $hStyle }}</span>
                                    </label>
                                @endfor
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-slate-300">Hair Color</label>
                                <span class="text-[11px] text-slate-400" x-text="selectedHairColor.name"></span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @for($hColor = 0; $hColor <= $maxHairColor; $hColor++)
                                    @php $cInfo = $hairColors[$hColor] ?? ['name' => "Color #{$hColor}", 'hex' => '#94a3b8']; @endphp
                                    <label @click.prevent="hairColor = {{ $hColor }}"
                                           :class="Number(hairColor) === {{ $hColor }} ? 'border-cyan-400 bg-slate-800 ring-2 ring-cyan-500/40' : 'border-slate-800 bg-slate-950/60'"
                                           class="px-3 py-1.5 rounded-xl border flex items-center gap-2 text-xs text-slate-200 cursor-pointer transition-all">
                                        <input type="radio" name="hair_color" value="{{ $hColor }}" :checked="Number(hairColor) === {{ $hColor }}" class="hidden">
                                        <span class="w-4 h-4 rounded-md border border-white/40 shrink-0" style="background-color: {{ $cInfo['hex'] }};"></span>
                                        <span>{{ $cInfo['name'] }}</span>
                                    </label>
                                @endfor
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-slate-300">Clothes Color (Palette)</label>
                                <span class="text-[11px] font-mono text-amber-300" x-text="'Palette #' + clothesColor"></span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @for($cDye = 0; $cDye <= $maxClothesColor; $cDye++)
                                    <label @click.prevent="clothesColor = {{ $cDye }}"
                                           :class="Number(clothesColor) === {{ $cDye }} ? 'border-amber-400 bg-amber-500/15 text-amber-200 ring-2 ring-amber-500/30' : 'border-slate-800 bg-slate-950/60 text-slate-300'"
                                           class="px-3 py-1.5 rounded-xl border text-xs font-mono font-bold cursor-pointer transition-all">
                                        <input type="radio" name="clothes_color" value="{{ $cDye }}" :checked="Number(clothesColor) === {{ $cDye }}" class="hidden">
                                        <span>#{{ $cDye }}</span>
                                    </label>
                                @endfor
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold transition-colors cursor-pointer">
                                Apply Appearance Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        @if($settings['job_change_enabled'])
            <form method="POST" action="{{ route('account.characters.services.job', $character->char_id) }}"
                  class="md:col-span-2 bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4"
                  x-data="{
                      gender: @js($charSex),
                      hairStyle: {{ max(1, (int) $character->hair) }},
                      hairColor: {{ (int) $character->hair_color }},
                      clothesColor: {{ (int) $character->clothes_color }},
                      selectedClass: {{ (int) $character->class }},
                      jobSearch: '',
                      jobs: @js($availableJobs),
                      proxyBaseUrl: @js(route('account.characters.sprite-preview')),
                      previewFailed: false,
                      get filteredJobs() {
                          const q = (this.jobSearch || '').trim().toLowerCase();
                          if (!q) return this.jobs;
                          return this.jobs.filter(j => j.name.toLowerCase().includes(q) || String(j.id).includes(q));
                      },
                      get selectedJobObj() {
                          return this.jobs.find(j => Number(j.id) === Number(this.selectedClass)) || { id: this.selectedClass, name: @js($character->job_name) };
                      },
                      get previewUrl() {
                          return `${this.proxyBaseUrl}?gender=${this.gender}&job=${this.selectedClass}&hair=${this.hairStyle}&color=${this.hairColor}&dye=${this.clothesColor}&dir=0`;
                      }
                  }"
                  x-effect="selectedClass; previewFailed = false;"
                  onsubmit="return confirm('Change character job class?{{ $settings['job_change_reset_skills'] ? ' Learned skills will be reset and skill points refunded.' : '' }}');">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-job">
                <input type="hidden" name="target_class" :value="selectedClass">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-white">Job / Class Change</h3>
                        <p class="text-[11px] text-slate-400">Preview and switch your character's job class. @if($settings['job_change_reset_skills']) Learned skills are automatically reset and skill points refunded. @endif</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-cyan-500/15 text-cyan-300 font-bold self-start sm:self-auto">{{ number_format($settings['job_change_cost']) }} CP</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-5 flex flex-col items-center text-center space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Job Preview</span>
                        <div class="w-28 h-32 flex items-center justify-center">
                            <img x-show="!previewFailed" :src="previewUrl" :alt="selectedJobObj.name"
                                 class="max-w-full max-h-full object-contain drop-shadow-[0_6px_12px_rgba(0,0,0,0.65)] scale-125"
                                 x-on:error="previewFailed = true">
                            <span x-show="previewFailed" style="display: none;" class="text-xs font-bold text-cyan-300" x-text="selectedJobObj.name"></span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-900 border border-slate-700 text-[11px] font-bold text-cyan-300" x-text="selectedJobObj.name"></span>
                        <span class="text-[11px] text-slate-400 font-mono" x-text="'Class ID #' + selectedClass"></span>
                    </div>

                    <div class="lg:col-span-2 space-y-3">
                        <input type="text" x-model="jobSearch" placeholder="Search job name or ID (e.g. Rune Knight, 4054)..."
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-56 overflow-y-auto pr-1">
                            <template x-for="job in filteredJobs" :key="job.id">
                                <button type="button" @click="selectedClass = job.id"
                                        :class="Number(selectedClass) === Number(job.id) ? 'bg-cyan-500/20 border-cyan-400 text-white ring-1 ring-cyan-500/40' : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:border-slate-700'"
                                        class="p-2.5 rounded-xl border text-left transition-all cursor-pointer flex items-center justify-between gap-2">
                                    <span class="font-semibold truncate" x-text="job.name"></span>
                                    <span class="text-[10px] font-mono text-slate-500 shrink-0" x-text="'#' + job.id"></span>
                                </button>
                            </template>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" :disabled="Number(selectedClass) === {{ (int) $character->class }}"
                                    class="px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 disabled:opacity-50 text-slate-950 font-bold transition-colors cursor-pointer">
                                Change Job Class
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        @if($settings['stat_reset_enabled'])
            <form method="POST" action="{{ route('account.characters.services.stat-reset', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3" onsubmit="return confirm('Reset all character attributes?');">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-stat">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Stat Reset</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['stat_reset_cost']) }} CP</span>
                </div>
                <p class="text-slate-400">Resets STR/AGI/VIT/INT/DEX/LUK to 1 and refunds all spent status points. Min Level: {{ $settings['stat_reset_min_level'] }}.</p>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold cursor-pointer">Reset Stats</button>
            </form>
        @endif

        @if($settings['skill_reset_enabled'])
            <form method="POST" action="{{ route('account.characters.services.skill-reset', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3" onsubmit="return confirm('Reset all learned skills?');">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-skill">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Skill Reset</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['skill_reset_cost']) }} CP</span>
                </div>
                <p class="text-slate-400">Resets learned skills and returns all spent skill points. Min Level: {{ $settings['skill_reset_min_level'] }}.</p>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold cursor-pointer">Reset Skills</button>
            </form>
        @endif

        @if($settings['gender_enabled'])
            <form method="POST" action="{{ route('account.characters.services.gender', $character->char_id) }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3" onsubmit="return confirm('Change account gender? This affects all characters on your game account.');">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}-gender">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-white">Account Gender Change</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 font-bold">{{ number_format($settings['gender_cost']) }} CP</span>
                </div>
                <p class="text-slate-400">Note: rAthena stores gender at the account level. All characters on your account must be offline.</p>
                <div class="flex items-center gap-3">
                    <select name="gender" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        <option value="M" {{ $charSex === 'M' ? 'selected' : '' }}>Male (M)</option>
                        <option value="F" {{ $charSex === 'F' ? 'selected' : '' }}>Female (F)</option>
                    </select>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold cursor-pointer">Change Gender</button>
                </div>
            </form>
        @endif

        @if($settings['lock_enabled'] || $settings['deletion_enabled'])
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white">Security &amp; Deletion</h3>
                @if($settings['lock_enabled'] && !$active_lock)
                    <form method="POST" action="{{ route('account.characters.services.lock', $character->char_id) }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="action" value="lock">
                        <input type="text" name="reason" placeholder="Optional lock note..." class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold cursor-pointer">Self-Lock</button>
                    </form>
                @endif
                @if($settings['deletion_enabled'] && (!$deletion_request || $deletion_request->status !== 'pending'))
                    <form method="POST" action="{{ route('account.characters.services.deletion.request', $character->char_id) }}" onsubmit="return confirm('Schedule this character for deletion?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-950/60 hover:bg-rose-900 text-rose-300 border border-rose-500/30 font-bold cursor-pointer">
                            Request Deletion ({{ $settings['deletion_wait_hours'] }}h Wait)
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <!-- TAB 4: Privacy & Customization -->
    <div x-show="tab === 'privacy'" style="display: none;" class="max-w-3xl text-xs">
        <form method="POST" action="{{ route('account.characters.privacy', $character->char_id) }}" enctype="multipart/form-data" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-5">
            @csrf
            @php
                $acctRankingsEnabled = app(\App\Services\Module\ModuleManager::class)->isEnabled('rankings');
                $privacyFields = [
                    'is_public' => 'Public Profile',
                    'show_char_info' => 'Basic Info',
                    'show_equipments' => 'Equipments',
                    'show_stats' => 'Stats & Attributes',
                    'show_zeny' => 'Zeny Balance',
                    'show_guild' => 'Guild Standing',
                    'show_last_login' => 'Last Login Time',
                ];
                if ($acctRankingsEnabled) {
                    $privacyFields['show_badges'] = 'Earned Badges';
                    $privacyFields['show_rankings'] = 'Leaderboard Standings';
                    $privacyFields['show_achievements'] = 'Achievements';
                }
            @endphp

            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-white">Profile Customization &amp; Privacy</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Control what appears on your public character profile.</p>
                </div>
                <span class="text-[10px] px-2.5 py-1 rounded-full font-bold uppercase {{ ($privacy->is_public ?? true) ? 'text-emerald-400 bg-emerald-950/60 border border-emerald-500/30' : 'text-amber-400 bg-amber-950/60 border border-amber-500/30' }}">
                    {{ ($privacy->is_public ?? true) ? 'Public' : 'Private' }}
                </span>
            </div>

            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2.5">Display &amp; Visibility</span>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-slate-300">
                    @foreach($privacyFields as $pField => $pLabel)
                        <label class="flex items-center gap-2 cursor-pointer hover:text-white bg-slate-950/60 border border-slate-800/80 rounded-xl px-3 py-2">
                            <input type="checkbox" name="{{ $pField }}" value="1" {{ ($privacy->$pField ?? true) ? 'checked' : '' }} class="rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-0">
                            <span>{{ $pLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-800 pt-4 space-y-2.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Character Avatar / Portrait</span>
                <input type="url" name="avatar_url" value="{{ !empty($privacy->avatar_path) && filter_var($privacy->avatar_path, FILTER_VALIDATE_URL) ? $privacy->avatar_path : '' }}" placeholder="Image URL (e.g. from Google or RateMyServer)..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs placeholder:text-slate-600 focus:border-cyan-500">
                <div class="flex items-center gap-3">
                    <label class="text-xs text-slate-400 shrink-0">Or Upload:</label>
                    <input type="file" name="avatar_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700">
                </div>
                @if(!empty($privacy->avatar_path))
                    <label class="flex items-center gap-2 text-xs text-amber-400 cursor-pointer pt-1">
                        <input type="checkbox" name="remove_avatar" value="1" class="rounded bg-slate-900 border-slate-700 text-amber-500">
                        <span>Reset to default job portrait</span>
                    </label>
                @endif
            </div>

            <div class="border-t border-slate-800 pt-4 space-y-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Adventurer Bio / Motto</span>
                <input type="text" name="bio" value="{{ $privacy->bio ?? '' }}" placeholder="Short adventurer motto or story..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white text-xs placeholder:text-slate-600 focus:border-cyan-500">
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold transition-colors cursor-pointer">
                Save Profile &amp; Privacy
            </button>
        </form>
    </div>

    <!-- TAB 5: Activity -->
    <div x-show="tab === 'activity'" style="display: none;" class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden text-xs">
        <div class="px-5 py-3.5 bg-slate-950/60 border-b border-slate-800">
            <h3 class="text-sm font-bold text-white">Recent Service History</h3>
        </div>
        <div class="divide-y divide-slate-800/50">
            @forelse($logs as $log)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-cyan-300">{{ $log->action_type }}</span>
                        <span class="ml-2 px-1.5 py-0.5 rounded text-[10px] {{ $log->result === 'success' ? 'bg-emerald-950 text-emerald-400' : 'bg-rose-950 text-rose-400' }}">{{ $log->result }}</span>
                        @if($log->service_cost > 0)
                            <span class="text-slate-400 ml-2">-{{ number_format($log->service_cost) }} CP</span>
                        @endif
                    </div>
                    <span class="text-slate-500">{{ $log->created_at->format('M d, Y H:i') }}</span>
                </div>
            @empty
                <p class="px-5 py-6 text-center text-slate-500">No service activity recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
