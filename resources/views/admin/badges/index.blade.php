@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6" x-data="{ tab: 'definitions', previewName: 'First to Max Level', previewColor: '#f59e0b', previewStyle: 'glow', previewIcon: 'crown' }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Badge &amp; Tier Management</h1>
            <p class="text-xs text-slate-400 mt-1">Create custom badges, configure Bronze-to-Diamond 5-star thresholds, and manage player/guild assignments.</p>
        </div>
        <form method="POST" action="{{ route('admin.badges.recalculate') }}">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg flex items-center gap-2 transition-colors">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
                <span>Recalculate All Badges</span>
            </button>
        </form>
    </div>

    <!-- Section Tabs -->
    <div class="flex flex-wrap gap-2">
        <button type="button" @click="tab = 'definitions'" :class="tab === 'definitions' ? 'bg-cyan-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 border border-slate-800 hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs transition-colors">Badge Definitions</button>
        <button type="button" @click="tab = 'pvp_tiers'" :class="tab === 'pvp_tiers' ? 'bg-cyan-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 border border-slate-800 hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs transition-colors">PvP Tier Thresholds (25)</button>
        <button type="button" @click="tab = 'mvp_tiers'" :class="tab === 'mvp_tiers' ? 'bg-cyan-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 border border-slate-800 hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs transition-colors">MVP Tier Thresholds (25)</button>
        <button type="button" @click="tab = 'assignments'" :class="tab === 'assignments' ? 'bg-cyan-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 border border-slate-800 hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-xs transition-colors">Assign &amp; History</button>
    </div>

    <!-- TAB 1: Badge Definitions & Live Preview -->
    <div x-show="tab === 'definitions'" class="space-y-6">
        <form method="POST" action="{{ route('admin.badges.store') }}" enctype="multipart/form-data" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-5">
            @csrf
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
                <h2 class="text-sm font-bold uppercase tracking-wider text-cyan-400">Create New Badge</h2>
                <!-- Live Badge Preview -->
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-slate-400">Live Preview:</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-bold text-xs border"
                          :style="`color: ${previewColor}; border-color: ${previewColor}66; background-color: ${previewStyle === 'outline' ? 'transparent' : previewColor + '22'}; box-shadow: ${previewStyle === 'glow' ? '0 0 12px ' + previewColor + '55' : 'none'}`">
                        <span x-text="previewName || 'Badge Name'"></span>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Badge Name</label>
                    <input type="text" name="name" x-model="previewName" required placeholder="e.g. Legendary Leveler" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Category</label>
                    <select name="category" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        @foreach(['level', 'job', 'pvp', 'mvp', 'guild', 'special', 'event', 'achievement'] as $cat)
                            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Auto Assignment Condition</label>
                    <select name="auto_condition_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        <option value="manual">Manual Assignment Only</option>
                        <option value="first_max_base">First to Max Base Level</option>
                        <option value="max_base">Reached Max Base Level</option>
                        <option value="first_max_job">First to Max Job Level</option>
                        <option value="max_job">Reached Max Job Level</option>
                        <option value="guild_rank_1">Top 1 Guild</option>
                        <option value="guild_rank_2">Top 2 Guild</option>
                        <option value="guild_rank_3">Top 3 Guild</option>
                        <option value="guild_rank_top10">Top 4–10 Guild</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Priority</label>
                    <input type="number" name="priority" value="50" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                </div>

                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Color (Hex)</label>
                    <input type="color" name="color" x-model="previewColor" class="w-full h-9 bg-slate-950 border border-slate-800 rounded-xl px-1 py-1 cursor-pointer">
                </div>
                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Visual Style</label>
                    <select name="visual_style" x-model="previewStyle" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        <option value="solid">Solid</option>
                        <option value="glow">Glow</option>
                        <option value="gradient">Gradient</option>
                        <option value="outline">Outline</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Icon Source</label>
                    <select name="icon_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        <option value="lucide">Lucide Icon</option>
                        <option value="image">Upload Image</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Lucide Icon / Upload</label>
                    <input type="text" name="icon" x-model="previewIcon" placeholder="crown, trophy, star, shield" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white mb-1.5">
                    <input type="file" name="icon_file" accept=".png,.jpg,.jpeg,.webp,.svg" class="text-[11px] text-slate-400 w-full">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs items-center">
                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Description</label>
                    <input type="text" name="description" placeholder="Short description of this badge..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                </div>
                <div class="flex flex-wrap items-center gap-5 pt-4">
                    <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
                        <input type="checkbox" name="is_enabled" value="1" checked class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                        <span>Enabled</span>
                    </label>
                    <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
                        <input type="checkbox" name="is_permanent" value="1" checked class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                        <span>Permanent</span>
                    </label>
                    <button type="submit" class="ml-auto px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold transition-colors">
                        Create Badge
                    </button>
                </div>
            </div>
        </form>

        <!-- Existing Badges Table -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Badge Preview</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Auto Condition</th>
                        <th class="py-3 px-4 text-center">Priority</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($badges as $b)
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-bold text-xs border"
                                      style="color: {{ $b->color }}; border-color: {{ $b->color }}66; background-color: {{ $b->color }}1f;">
                                    @if($b->icon_url)
                                        <img src="{{ $b->icon_url }}" alt="" class="w-3.5 h-3.5 object-contain">
                                    @else
                                        <i data-lucide="{{ $b->icon }}" class="w-3.5 h-3.5"></i>
                                    @endif
                                    <span>{{ $b->name }}</span>
                                </span>
                                <p class="text-[11px] text-slate-500 mt-1">{{ $b->description }}</p>
                            </td>
                            <td class="py-3 px-4 uppercase font-mono text-slate-300">{{ $b->category }}</td>
                            <td class="py-3 px-4 font-mono text-cyan-400">{{ $b->auto_condition_type }}</td>
                            <td class="py-3 px-4 text-center font-mono text-slate-300">{{ $b->priority }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $b->is_enabled ? 'bg-emerald-950 text-emerald-400' : 'bg-slate-800 text-slate-500' }}">
                                    {{ $b->is_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <form method="POST" action="{{ route('admin.badges.toggle', $b->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded-lg text-[11px] font-semibold {{ $b->is_enabled ? 'bg-red-950/70 text-red-300 border border-red-500/30' : 'bg-emerald-950/70 text-emerald-300 border border-emerald-500/30' }}">
                                        {{ $b->is_enabled ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: PvP Tier Thresholds -->
    <div x-show="tab === 'pvp_tiers'" style="display: none;">
        <form method="POST" action="{{ route('admin.badges.tiers') }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden">
            @csrf
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wider text-cyan-400">Configurable PvP Tiers (Bronze ★ to Diamond ★★★★★)</h2>
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold">Save PvP Thresholds</button>
            </div>
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                    <tr>
                        <th class="py-2.5 px-4">Tier &amp; Stars</th>
                        <th class="py-2.5 px-4">Min PvP Kills</th>
                        <th class="py-2.5 px-4">Color</th>
                        <th class="py-2.5 px-4 text-center">Enabled</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($pvpTiers as $tier)
                        <tr>
                            <td class="py-2 px-4 font-bold" style="color: {{ $tier->color }}">{{ $tier->formatted_label }}</td>
                            <td class="py-2 px-4">
                                <input type="number" name="tiers[{{ $tier->id }}][min_kills]" value="{{ $tier->min_kills }}" class="w-32 bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1 text-white font-mono">
                            </td>
                            <td class="py-2 px-4">
                                <input type="color" name="tiers[{{ $tier->id }}][color]" value="{{ $tier->color }}" class="w-12 h-7 bg-slate-950 border border-slate-800 rounded cursor-pointer">
                            </td>
                            <td class="py-2 px-4 text-center">
                                <input type="checkbox" name="tiers[{{ $tier->id }}][is_enabled]" value="1" {{ $tier->is_enabled ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>

    <!-- TAB 3: MVP Tier Thresholds -->
    <div x-show="tab === 'mvp_tiers'" style="display: none;">
        <form method="POST" action="{{ route('admin.badges.tiers') }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden">
            @csrf
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wider text-purple-400">Configurable MVP Tiers (Bronze ★ to Diamond ★★★★★)</h2>
                <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold">Save MVP Thresholds</button>
            </div>
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                    <tr>
                        <th class="py-2.5 px-4">Tier &amp; Stars</th>
                        <th class="py-2.5 px-4">Min Verified MVP Kills</th>
                        <th class="py-2.5 px-4">Color</th>
                        <th class="py-2.5 px-4 text-center">Enabled</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($mvpTiers as $tier)
                        <tr>
                            <td class="py-2 px-4 font-bold" style="color: {{ $tier->color }}">{{ $tier->formatted_label }}</td>
                            <td class="py-2 px-4">
                                <input type="number" name="tiers[{{ $tier->id }}][min_kills]" value="{{ $tier->min_kills }}" class="w-32 bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1 text-white font-mono">
                            </td>
                            <td class="py-2 px-4">
                                <input type="color" name="tiers[{{ $tier->id }}][color]" value="{{ $tier->color }}" class="w-12 h-7 bg-slate-950 border border-slate-800 rounded cursor-pointer">
                            </td>
                            <td class="py-2 px-4 text-center">
                                <input type="checkbox" name="tiers[{{ $tier->id }}][is_enabled]" value="1" {{ $tier->is_enabled ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>

    <!-- TAB 4: Manual Assignment & History -->
    <div x-show="tab === 'assignments'" style="display: none;" class="space-y-6">
        <form method="POST" action="{{ route('admin.badges.assign') }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-4">
            @csrf
            <h2 class="text-sm font-bold uppercase tracking-wider text-cyan-400">Manually Award Badge</h2>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Badge</label>
                    <select name="badge_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        @foreach($badges as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ ucfirst($b->category) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Target Type</label>
                    <select name="target_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                        <option value="character">Character</option>
                        <option value="guild">Guild</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 font-semibold mb-1.5">Name or ID</label>
                    <input type="text" name="target_identifier" required placeholder="Character/Guild Name or ID" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold">Award Badge</button>
                </div>
            </div>
        </form>

        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Badge</th>
                        <th class="py-3 px-4">Target</th>
                        <th class="py-3 px-4">Source</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Awarded</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($assignments as $a)
                        <tr>
                            <td class="py-2.5 px-4 font-bold" style="color: {{ $a->color }}">{{ $a->label }}</td>
                            <td class="py-2.5 px-4 font-mono text-slate-300">{{ ucfirst($a->target_type) }} #{{ $a->target_id }}</td>
                            <td class="py-2.5 px-4 uppercase text-[10px] text-slate-400">{{ $a->assigned_by }}</td>
                            <td class="py-2.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $a->is_active ? 'bg-emerald-950 text-emerald-400' : 'bg-slate-800 text-slate-500' }}">
                                    {{ $a->is_active ? 'Active' : 'Historical' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-slate-500">{{ $a->awarded_at?->diffForHumans() }}</td>
                            <td class="py-2.5 px-4 text-right">
                                @if($a->is_active)
                                    <form method="POST" action="{{ route('admin.badges.revoke', $a->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-950/70 text-red-300 border border-red-500/30 text-[11px] font-semibold">Revoke</button>
                                    </form>
                                @else
                                    <span class="text-slate-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">No badge assignments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
