@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ tab: 'overview' }">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-700 flex items-center justify-center text-xl font-black text-cyan-400">
                {{ strtoupper(substr($character->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-2xl font-bold text-white">{{ $character->name }}</h1>
                    <span class="font-mono text-xs text-slate-500">#{{ $character->char_id }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ (int) $character->online > 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                        {{ (int) $character->online > 0 ? 'Online' : 'Offline' }}
                    </span>
                    @if($active_lock)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-950 text-rose-300 border border-rose-500/40">
                            Locked ({{ $active_lock->lock_scope }})
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    {{ $character->job_name }} • Lv. {{ $character->base_level }} / {{ $character->job_level }} •
                    Account: <span class="text-slate-200">{{ $character->account_userid ?? 'N/A' }}</span> (#{{ $character->account_id }}, Sex: {{ $character->account_sex ?? 'M' }}) •
                    Cash Points: <span class="text-cyan-400 font-bold">{{ number_format($cash_points) }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('admin.characters') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 inline-flex items-center gap-1.5 self-start">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Characters</span>
        </a>
    </div>

    @if((int) $character->online > 0)
        <div class="bg-amber-950/40 border border-amber-500/30 rounded-2xl p-4 text-xs text-amber-200 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-400 shrink-0"></i>
                <span>This character is marked <strong>Online</strong>. Direct database edits (attributes, inventory, storage, reset) are blocked to prevent live map-server overwrites. Escrow item delivery remains safe.</span>
            </div>
            @if(auth()->user()?->hasCharMgmtPermission('moderate_characters'))
                <form method="POST" action="{{ route('admin.characters.moderate', $character->char_id) }}">
                    @csrf
                    <input type="hidden" name="action" value="force_logout">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-200 border border-amber-500/40 font-bold whitespace-nowrap cursor-pointer">
                        Clear Online Flag
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-slate-800 pb-3 text-xs font-semibold">
        @foreach([
            'overview' => 'Overview & Edit',
            'items' => 'Inventory, Equipment & Storage',
            'moderation' => 'Moderation & Notes',
            'audit' => 'Transactions & Audit Logs',
        ] as $key => $label)
            <button type="button" @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'"
                    class="px-4 py-2 rounded-xl transition-colors cursor-pointer">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Tab 1: Overview & Edit -->
    <div x-show="tab === 'overview'" class="space-y-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
                <span class="text-slate-400 block">Zeny</span>
                <span class="text-lg font-bold text-amber-400">{{ number_format($character->zeny) }} Z</span>
            </div>
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
                <span class="text-slate-400 block">Guild / Party</span>
                <span class="text-sm font-bold text-white block truncate">{{ $character->guild_name ?: 'No Guild' }}</span>
                <span class="text-[11px] text-slate-400">{{ $character->party_name ?: 'No Party' }}</span>
            </div>
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
                <span class="text-slate-400 block">Current Location</span>
                <span class="text-sm font-bold text-cyan-300 font-mono">{{ $character->last_map }} ({{ $character->last_x }}, {{ $character->last_y }})</span>
                <span class="text-[11px] text-slate-400">Save: {{ $character->save_map }} ({{ $character->save_x }}, {{ $character->save_y }})</span>
            </div>
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
                <span class="text-slate-400 block">Last Login</span>
                <span class="text-sm font-bold text-slate-200">{{ $character->last_login ?: 'Never' }}</span>
            </div>
        </div>

        @if(auth()->user()?->hasCharMgmtPermission('edit_characters'))
            <form method="POST" action="{{ route('admin.characters.update', $character->char_id) }}" class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-5 text-xs">
                @csrf
                <h2 class="text-sm font-bold text-white border-b border-slate-800 pb-3">Edit Character Data (Offline Guarded)</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-slate-400 mb-1">Character Name</label>
                        <input type="text" name="name" value="{{ $character->name }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Job Class</label>
                        <select name="class" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            @foreach($jobs as $id => $jName)
                                <option value="{{ $id }}" {{ (int)$character->class === (int)$id ? 'selected' : '' }}>{{ $jName }} (#{{ $id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Base Level</label>
                        <input type="number" name="base_level" value="{{ $character->base_level }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Job Level</label>
                        <input type="number" name="job_level" value="{{ $character->job_level }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Zeny</label>
                        <input type="number" name="zeny" value="{{ $character->zeny }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-300">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Status Points</label>
                        <input type="number" name="status_point" value="{{ $character->status_point }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Skill Points</label>
                        <input type="number" name="skill_point" value="{{ $character->skill_point }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    @if(in_array('trait_point', $supported_columns, true))
                        <div>
                            <label class="block text-slate-400 mb-1">Trait Points</label>
                            <input type="number" name="trait_point" value="{{ $character->trait_point ?? 0 }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                    @foreach(['str', 'agi', 'vit', 'int', 'dex', 'luk'] as $stat)
                        <div>
                            <label class="block text-slate-400 uppercase mb-1">{{ $stat }}</label>
                            <input type="number" name="{{ $stat }}" value="{{ $character->{$stat} }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Hair Style</label>
                        <input type="number" name="hair" value="{{ $character->hair }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Hair Color</label>
                        <input type="number" name="hair_color" value="{{ $character->hair_color }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Clothes Color</label>
                        <input type="number" name="clothes_color" value="{{ $character->clothes_color }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Last Map</label>
                        <input type="text" name="last_map" value="{{ $character->last_map }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Last X</label>
                        <input type="number" name="last_x" value="{{ $character->last_x }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Last Y</label>
                        <input type="number" name="last_y" value="{{ $character->last_y }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" {{ (int) $character->online > 0 ? 'disabled' : '' }}
                            class="px-5 py-2.5 rounded-xl font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 disabled:opacity-50 cursor-pointer">
                        Save Character Changes
                    </button>
                </div>
            </form>
        @endif
    </div>

    <!-- Tab 2: Inventory, Equipment & Storage -->
    <div x-show="tab === 'items'" style="display: none;" class="space-y-6 text-xs">
        @if(auth()->user()?->hasCharMgmtPermission('manage_items'))
            <form method="POST" action="{{ route('admin.characters.give-item', $character->char_id) }}" class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
                @csrf
                <h2 class="text-sm font-bold text-white">Give Item to Character</h2>
                <div class="grid grid-cols-1 sm:grid-cols-6 gap-3 items-end">
                    <div>
                        <label class="block text-slate-400 mb-1">Item ID</label>
                        <input type="number" name="item_id" required min="1" placeholder="e.g. 501" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Quantity</label>
                        <input type="number" name="amount" value="1" min="1" max="30000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Refine</label>
                        <input type="number" name="refine" value="0" min="0" max="20" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Delivery</label>
                        <select name="delivery" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            <option value="escrow">Escrow Inbox (Safe Online/Offline)</option>
                            <option value="direct">Direct Inventory (Offline Only)</option>
                        </select>
                    </div>
                    <label class="inline-flex items-center gap-2 text-slate-300 py-2 cursor-pointer">
                        <input type="checkbox" name="is_bound" value="1" class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                        <span>Account Bound</span>
                    </label>
                    <button type="submit" class="px-4 py-2 rounded-xl font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 cursor-pointer">
                        Deliver Item
                    </button>
                </div>
            </form>
        @endif

        @foreach([
            ['title' => 'Equipped Items', 'rows' => $equipped, 'container' => 'inventory'],
            ['title' => 'Character Inventory', 'rows' => $inventory, 'container' => 'inventory'],
            ['title' => 'Character Cart', 'rows' => $cart, 'container' => 'cart'],
            ['title' => 'Account Storage', 'rows' => $storage, 'container' => 'storage'],
        ] as $section)
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-950/60 border-b border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white">{{ $section['title'] }} ({{ count($section['rows']) }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-[10px] uppercase text-slate-400">
                                <th class="px-4 py-2.5">Item</th>
                                <th class="px-4 py-2.5">Qty</th>
                                <th class="px-4 py-2.5">Refine</th>
                                <th class="px-4 py-2.5">Slot / Bound</th>
                                <th class="px-4 py-2.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            @forelse($section['rows'] as $row)
                                <tr>
                                    <td class="px-4 py-2.5">
                                        <span class="font-bold text-white">{{ $row->item_name }}</span>
                                        <span class="font-mono text-slate-500 ml-1">#{{ $row->nameid }}</span>
                                    </td>
                                    <td class="px-4 py-2.5 font-mono text-slate-200">{{ $row->amount }}</td>
                                    <td class="px-4 py-2.5 font-mono text-cyan-300">+{{ $row->refine }}</td>
                                    <td class="px-4 py-2.5">
                                        @if($row->equip_slot_name)
                                            <span class="px-2 py-0.5 rounded bg-cyan-500/15 text-cyan-300 text-[10px]">{{ $row->equip_slot_name }}</span>
                                        @endif
                                        @if((int)($row->bound ?? 0) > 0)
                                            <span class="px-2 py-0.5 rounded bg-amber-500/15 text-amber-300 text-[10px]">Bound</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        @if(auth()->user()?->hasCharMgmtPermission('manage_items'))
                                            <div class="inline-flex items-center gap-1.5">
                                                @if($section['container'] === 'inventory' && (int)($row->equip ?? 0) > 0)
                                                    <form method="POST" action="{{ route('admin.characters.container-item', [$character->char_id, 'inventory', $row->id]) }}">
                                                        @csrf
                                                        <input type="hidden" name="action" value="unequip">
                                                        <button type="submit" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-amber-300 cursor-pointer">Unequip</button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.characters.container-item', [$character->char_id, $section['container'], $row->id]) }}" onsubmit="return confirm('Remove this item?');">
                                                    @csrf
                                                    <input type="hidden" name="action" value="remove">
                                                    <button type="submit" class="px-2 py-1 rounded bg-rose-950/60 hover:bg-rose-900 text-rose-300 border border-rose-500/30 cursor-pointer">Remove</button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Empty</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Tab 3: Moderation & Notes -->
    <div x-show="tab === 'moderation'" style="display: none;" class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs">
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold text-white border-b border-slate-800 pb-3">Moderation Actions</h2>
            @if(auth()->user()?->hasCharMgmtPermission('moderate_characters'))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <form method="POST" action="{{ route('admin.characters.moderate', $character->char_id) }}">
                        @csrf
                        <input type="hidden" name="action" value="move_save_point">
                        <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-300 font-semibold cursor-pointer">Move to Save Point</button>
                    </form>
                    <form method="POST" action="{{ route('admin.characters.moderate', $character->char_id) }}" onsubmit="return confirm('Reset all character stats?');">
                        @csrf
                        <input type="hidden" name="action" value="reset_stats">
                        <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 font-semibold cursor-pointer">Reset Stats</button>
                    </form>
                    <form method="POST" action="{{ route('admin.characters.moderate', $character->char_id) }}" onsubmit="return confirm('Reset all character skills?');">
                        @csrf
                        <input type="hidden" name="action" value="reset_skills">
                        <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 font-semibold cursor-pointer">Reset Skills</button>
                    </form>
                    @if(($character->delete_date ?? 0) > 0 || $deletion_requests->where('status', 'pending')->count() > 0)
                        <form method="POST" action="{{ route('admin.characters.moderate', $character->char_id) }}">
                            @csrf
                            <input type="hidden" name="action" value="approve_recovery">
                            <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 font-semibold cursor-pointer">Approve Recovery / Cancel Deletion</button>
                        </form>
                    @endif
                </div>

                @if($active_lock)
                    <form method="POST" action="{{ route('admin.characters.moderate', $character->char_id) }}" class="pt-3 border-t border-slate-800">
                        @csrf
                        <input type="hidden" name="action" value="unlock">
                        <p class="text-rose-300 mb-2">Locked reason: {{ $active_lock->reason }}</p>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold cursor-pointer">Unlock Character</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.characters.moderate', $character->char_id) }}" class="pt-3 border-t border-slate-800 space-y-3">
                        @csrf
                        <input type="hidden" name="action" value="lock">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <select name="lock_scope" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                <option value="full">Full Lock</option>
                                <option value="services">Services Lock</option>
                            </select>
                            <input type="text" name="reason" required placeholder="Lock reason..." class="sm:col-span-2 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 font-bold cursor-pointer">Lock Character</button>
                    </form>
                @endif
            @endif
        </div>

        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-bold text-white border-b border-slate-800 pb-3">Internal Admin Notes</h2>
            <form method="POST" action="{{ route('admin.characters.notes.store', $character->char_id) }}" class="space-y-2">
                @csrf
                <textarea name="note" rows="3" required placeholder="Add internal staff note..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white"></textarea>
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold cursor-pointer">Add Note</button>
            </form>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($notes as $note)
                    <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-3">
                        <div class="flex justify-between text-[11px] text-slate-400 mb-1">
                            <span class="font-bold text-cyan-300">{{ $note->admin->username ?? 'Admin' }}</span>
                            <span>{{ $note->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <p class="text-slate-200">{{ $note->note }}</p>
                    </div>
                @empty
                    <p class="text-slate-500">No internal notes recorded.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tab 4: Transactions & Audit Logs -->
    <div x-show="tab === 'audit'" style="display: none;" class="space-y-6 text-xs">
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-3.5 bg-slate-950/60 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white">Paid Service Transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-[10px] uppercase text-slate-400">
                            <th class="px-4 py-2.5">Service</th>
                            <th class="px-4 py-2.5">Cost</th>
                            <th class="px-4 py-2.5">Status</th>
                            <th class="px-4 py-2.5">Date</th>
                            <th class="px-4 py-2.5 text-right">Refund</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($transactions as $tx)
                            <tr>
                                <td class="px-4 py-2.5 font-bold text-white">{{ $tx->service_type }}</td>
                                <td class="px-4 py-2.5 text-cyan-300">{{ number_format($tx->cash_points_cost) }} CP</td>
                                <td class="px-4 py-2.5 uppercase font-bold">{{ $tx->status }}</td>
                                <td class="px-4 py-2.5 text-slate-400">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-2.5 text-right">
                                    @if($tx->status === 'completed' && $tx->cash_points_cost > 0 && auth()->user()?->hasCharMgmtPermission('refund_services'))
                                        <form method="POST" action="{{ route('admin.characters.refund', [$character->char_id, $tx->id]) }}" class="inline-flex items-center gap-1.5">
                                            @csrf
                                            <input type="text" name="reason" required placeholder="Refund reason" class="bg-slate-950 border border-slate-800 rounded-lg px-2 py-1 text-white w-36">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 font-bold cursor-pointer">Refund</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">No service transactions.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-3.5 bg-slate-950/60 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white">Character Service &amp; Moderation Audit Log</h3>
            </div>
            <div class="divide-y divide-slate-800/50">
                @forelse($logs as $log)
                    <div class="px-5 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="font-bold text-cyan-300">{{ $log->action_type }}</span>
                            <span class="text-slate-400 ml-2">by {{ $log->actor_type }} #{{ $log->actor_user_id }}</span>
                            <span class="ml-2 px-1.5 py-0.5 rounded text-[10px] {{ $log->result === 'success' ? 'bg-emerald-950 text-emerald-400' : 'bg-rose-950 text-rose-400' }}">{{ $log->result }}</span>
                            @if($log->failure_reason)
                                <p class="text-rose-400 mt-0.5">{{ $log->failure_reason }}</p>
                            @endif
                        </div>
                        <span class="text-slate-500">{{ $log->created_at->format('M d, Y H:i:s') }}</span>
                    </div>
                @empty
                    <p class="px-5 py-6 text-center text-slate-500">No audit logs recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
