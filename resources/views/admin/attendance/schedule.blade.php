@extends('layouts.admin')

@section('title', 'Reward Schedule Builder: ' . $campaign->name . ' - Admin Panel')

@section('content')
<div class="space-y-6" x-data="{
    addItemModal: false,
    selectedDay: 1,
    selectedDayTitle: 'Day 1',
    rewardType: 'item',
    generatorModal: false,
    editDayModal: false,
    editDayTitle: '',
    editDayMilestone: false
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.attendance.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 font-medium">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Campaigns</span>
                </a>
                <span class="text-xs text-slate-600">/</span>
                <span class="text-xs text-cyan-400 font-medium">Reward Schedule Builder</span>
            </div>
            <div class="flex items-center gap-3 mt-1">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">{{ $campaign->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-slate-950 border border-slate-800 text-cyan-300">
                    {{ $campaign->duration_days }} {{ Str::plural('Day', $campaign->duration_days) }}
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $campaign->status === 'active' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                    {{ $campaign->status }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">
                Configure rewards for every individual day. You can add multiple rewards (Zeny + Items) per day, customize milestone rewards, or regenerate with the schedule builder.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" @click="generatorModal = true" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-950 hover:bg-cyan-900 text-cyan-300 border border-cyan-500/40 flex items-center gap-1.5 shadow transition-colors cursor-pointer">
                <i data-lucide="sparkles" class="w-4 h-4 text-cyan-400"></i>
                <span>Auto-Generate Baseline Schedule</span>
            </button>
            <a href="{{ route('admin.attendance.edit', $campaign->id) }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors flex items-center gap-1.5">
                <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                <span>Settings</span>
            </a>
        </div>
    </div>

    <!-- Alert / Notice if days are missing -->
    @if($missingDaysCount > 0)
        <div class="p-4 rounded-2xl bg-amber-950/40 border border-amber-500/30 flex items-center justify-between gap-3 text-xs text-amber-200">
            <div class="flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-amber-400 shrink-0"></i>
                <div>
                    <strong>{{ $missingDaysCount }} unconfigured days detected.</strong>
                    <span>Your campaign duration is {{ $campaign->duration_days }} days, but only {{ $days->count() }} days have configured reward schedules.</span>
                </div>
            </div>
            <button type="button" @click="generatorModal = true" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold shrink-0 transition-colors cursor-pointer">
                Generate Missing Days
            </button>
        </div>
    @endif

    <!-- Days Grid / Timeline -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @for($d = 1; $d <= $campaign->duration_days; $d++)
            @php
                $dayModel = $days->firstWhere('day_number', $d);
                $isMilestone = $dayModel ? $dayModel->is_milestone : ($d % 7 === 0 || $d === $campaign->duration_days);
                $items = $dayModel ? $dayModel->items : collect();
            @endphp
            <div class="bg-slate-900/80 border {{ $isMilestone ? 'border-amber-500/40 shadow-lg shadow-amber-500/5' : 'border-slate-800' }} rounded-2xl p-4 flex flex-col justify-between space-y-3 hover:border-cyan-500/30 transition-all group">
                <div>
                    <!-- Top Ribbon -->
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs font-mono {{ $isMilestone ? 'bg-amber-400 text-slate-950' : 'bg-slate-950 text-cyan-400 border border-slate-800' }}">
                                {{ $d }}
                            </span>
                            <div>
                                <h3 class="font-bold text-white text-xs truncate max-w-[140px]" title="{{ $dayModel?->title ?: "Day {$d}" }}">
                                    {{ $dayModel?->title ?: "Day {$d}" }}
                                </h3>
                                @if($isMilestone)
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400 flex items-center gap-0.5">
                                        <i data-lucide="star" class="w-2.5 h-2.5 fill-amber-400"></i>
                                        <span>Milestone Reward</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <button type="button" @click="
                            editDayModal = true;
                            selectedDay = {{ $d }};
                            editDayTitle = '{{ addslashes($dayModel?->title ?: "Day {$d}") }}';
                            editDayMilestone = {{ $isMilestone ? 'true' : 'false' }};
                        " class="text-slate-500 hover:text-white transition-colors" title="Edit Day Details">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                    <!-- Configured Rewards -->
                    <div class="space-y-1.5 pt-2">
                        @forelse($items as $item)
                            <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-slate-950/70 border border-slate-800/60 text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    @if($item->reward_type === 'zeny')
                                        <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/30 flex items-center justify-center shrink-0">
                                            <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-400"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-amber-300 font-mono text-[11px] truncate block">{{ number_format($item->zeny_amount) }} Z</span>
                                            <span class="text-[9px] text-slate-500 block">Zeny Credit</span>
                                        </div>
                                    @elseif($item->reward_type === 'cashpoints')
                                        <div class="w-7 h-7 rounded-lg bg-purple-500/10 border border-purple-500/30 flex items-center justify-center shrink-0">
                                            <i data-lucide="gem" class="w-3.5 h-3.5 text-purple-400"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-purple-300 font-mono text-[11px] truncate block">{{ number_format($item->cashpoints_amount) }} CP</span>
                                            <span class="text-[9px] text-slate-500 block">Cash Points</span>
                                        </div>
                                    @else
                                        <div class="w-7 h-7 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center shrink-0 overflow-hidden p-0.5">
                                            <img src="{{ $item->icon_url ?? ('https://static.divine-pride.net/images/items/item/' . $item->item_id . '.png') }}" alt="" class="w-5 h-5 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-slate-200 text-[11px] truncate block" title="{{ $item->formatted_title }}">
                                                {{ $item->formatted_title }}
                                            </span>
                                            <span class="text-[9px] text-cyan-400 font-mono block">ID #{{ $item->item_id }}</span>
                                        </div>
                                    @endif
                                </div>

                                <form method="POST" action="{{ route('admin.attendance.schedule.delete-item', ['campaign' => $campaign->id, 'itemId' => $item->id]) }}" onsubmit="return confirm('Remove this reward from Day {{ $d }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-600 hover:text-rose-400 transition-colors p-1" title="Remove reward">
                                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="py-3 text-center text-slate-600 text-[11px] italic bg-slate-950/30 rounded-xl border border-dashed border-slate-800">
                                No rewards assigned yet
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Add Reward Button -->
                <button type="button" @click="
                    addItemModal = true;
                    selectedDay = {{ $d }};
                    selectedDayTitle = 'Day {{ $d }}';
                " class="w-full py-1.5 px-3 rounded-xl bg-slate-950 hover:bg-cyan-500 hover:text-slate-950 text-cyan-400 text-xs font-semibold border border-slate-800 hover:border-cyan-400 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Add Reward</span>
                </button>
            </div>
        @endfor
    </div>

    <!-- MODAL 1: ADD REWARD TO DAY -->
    <div x-show="addItemModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" @click.self="addItemModal = false">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="gift" class="w-4 h-4 text-cyan-400"></i>
                        <span>Add Reward to <span x-text="selectedDayTitle" class="text-cyan-400"></span></span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Choose reward type and quantities for this day.</p>
                </div>
                <button type="button" @click="addItemModal = false" class="text-slate-500 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" :action="`{{ url('admin/attendance/' . $campaign->id . '/schedule/day') }}/${selectedDay}/item`" class="space-y-4">
                @csrf

                <!-- Reward Type Switcher -->
                <div class="grid grid-cols-3 gap-2 p-1 bg-slate-950 rounded-xl border border-slate-800">
                    <button type="button" @click="rewardType = 'item'" :class="rewardType === 'item' ? 'bg-cyan-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'" class="py-1.5 rounded-lg text-xs transition-colors">
                        Item
                    </button>
                    <button type="button" @click="rewardType = 'zeny'" :class="rewardType === 'zeny' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'" class="py-1.5 rounded-lg text-xs transition-colors">
                        Zeny
                    </button>
                    <button type="button" @click="rewardType = 'cashpoints'" :class="rewardType === 'cashpoints' ? 'bg-purple-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'" class="py-1.5 rounded-lg text-xs transition-colors">
                        Cash Points
                    </button>
                </div>
                <input type="hidden" name="reward_type" :value="rewardType">

                <!-- ITEM REWARD FIELDS -->
                <div x-show="rewardType === 'item'" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Item ID *</label>
                            <input type="number" name="item_id" placeholder="e.g. 501, 603, 1201" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Quantity *</label>
                            <input type="number" name="quantity" value="1" min="1" max="30000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Item Display Name (Optional)</label>
                        <input type="text" name="item_name" placeholder="Leave empty to auto-resolve from Item DB" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800/80">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Refine Level (0-20)</label>
                            <input type="number" name="refine" value="0" min="0" max="20" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Delivery Destination</label>
                            <select name="delivery_method" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                                <option value="">Inherit Campaign Setting</option>
                                <option value="inbox">AetherCP Web Inbox (Safest)</option>
                                <option value="storage">Kafra Storage</option>
                                <option value="character">Character Inventory</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ZENY REWARD FIELDS -->
                <div x-show="rewardType === 'zeny'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Zeny Amount *</label>
                        <input type="number" name="zeny_amount" value="50000" min="1" max="2147483647" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm font-bold text-amber-400 font-mono focus:border-cyan-500 focus:outline-none">
                        <p class="text-[10px] text-slate-500 mt-1">Directly credited to player's selected character.</p>
                    </div>
                </div>

                <!-- CASH POINTS REWARD FIELDS -->
                <div x-show="rewardType === 'cashpoints'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Cash Points Amount *</label>
                        <input type="number" name="cashpoints_amount" value="10" min="1" max="1000000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm font-bold text-purple-400 font-mono focus:border-cyan-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="addItemModal = false" class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-colors">Add Reward</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: GENERATE BASELINE SCHEDULE -->
    <div x-show="generatorModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" @click.self="generatorModal = false">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 w-full max-w-lg shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-cyan-500/20 border border-cyan-500/40 flex items-center justify-center text-cyan-400">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">Generate Baseline Schedule</h3>
                        <p class="text-[11px] text-slate-400">Quickly populate all {{ $campaign->duration_days }} days with smart scaling.</p>
                    </div>
                </div>
                <button type="button" @click="generatorModal = false" class="text-slate-500 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.attendance.schedule.generate', $campaign->id) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Starting Base Zeny</label>
                        <input type="number" name="base_zeny" value="10000" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Zeny Step / Day (+)</label>
                        <input type="number" name="zeny_step" value="2500" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs pt-2 border-t border-slate-800/80">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Default Daily Item ID</label>
                        <input type="number" name="default_item_id" value="501" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono" placeholder="501 = Red Potion">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Default Daily Item Qty</label>
                        <input type="number" name="default_item_qty" value="5" min="1" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                    </div>
                </div>

                <div class="text-xs pt-2 border-t border-slate-800/80">
                    <label class="block font-semibold text-slate-300 mb-1">Milestone Interval (Every N Days)</label>
                    <input type="number" name="milestone_every" value="7" min="1" max="365" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                    <p class="text-[10px] text-slate-500 mt-1">Every 7th day automatically gets enhanced milestone rewards (Old Blue Box, Card Album, etc.).</p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="generatorModal = false" class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-colors">Generate Schedule Now</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: EDIT DAY SETTINGS -->
    <div x-show="editDayModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" @click.self="editDayModal = false">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4 text-cyan-400"></i>
                    <span>Edit Day <span x-text="selectedDay"></span> Details</span>
                </h3>
                <button type="button" @click="editDayModal = false" class="text-slate-500 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" :action="`{{ url('admin/attendance/' . $campaign->id . '/schedule/day') }}/${selectedDay}`" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Day Title</label>
                    <input type="text" name="title" x-model="editDayTitle" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none">
                </div>

                <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer pt-2">
                    <input type="checkbox" name="is_milestone" value="1" :checked="editDayMilestone" class="rounded bg-slate-950 border-slate-700 text-amber-500">
                    <span>Mark as Key Milestone Day (Golden badge &amp; highlighted card)</span>
                </label>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="editDayModal = false" class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-colors">Update Day</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
