@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Character Management</h1>
            <p class="text-xs text-slate-400 mt-1">Inspect, moderate, edit, and manage player characters across the connected rAthena database.</p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()?->hasCharMgmtPermission('configure_services'))
                <a href="{{ route('admin.characters.settings') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-cyan-300 border border-slate-700 inline-flex items-center gap-1.5 transition-colors">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    <span>Service Settings</span>
                </a>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('admin.characters') }}" class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 text-xs">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, Char ID, Account ID, Guild..."
               class="lg:col-span-2 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white focus:border-cyan-500/50 focus:outline-none">

        <select name="class" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            <option value="">All Classes</option>
            @foreach($jobs as $id => $name)
                <option value="{{ $id }}" {{ (string)($filters['class'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }} (#{{ $id }})</option>
            @endforeach
        </select>

        <select name="online" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            <option value="">Any Status</option>
            <option value="1" {{ ($filters['online'] ?? '') === '1' ? 'selected' : '' }}>Online Only</option>
            <option value="0" {{ ($filters['online'] ?? '') === '0' ? 'selected' : '' }}>Offline Only</option>
        </select>

        <div class="flex items-center gap-2">
            <input type="number" name="min_level" value="{{ $filters['min_level'] ?? '' }}" placeholder="Min Lv" class="w-1/2 bg-slate-950 border border-slate-800 rounded-xl px-2.5 py-2 text-white">
            <input type="number" name="max_level" value="{{ $filters['max_level'] ?? '' }}" placeholder="Max Lv" class="w-1/2 bg-slate-950 border border-slate-800 rounded-xl px-2.5 py-2 text-white">
        </div>

        <div class="flex items-center gap-2">
            <label class="inline-flex items-center gap-1.5 text-slate-300 cursor-pointer">
                <input type="checkbox" name="locked" value="1" {{ ($filters['locked'] ?? '') === '1' ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                <span>Locked</span>
            </label>
            <button type="submit" class="ml-auto px-4 py-2 rounded-xl font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-colors cursor-pointer">Filter</button>
            <a href="{{ route('admin.characters') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300">Reset</a>
        </div>
    </form>

    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-950/60 border-b border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="px-5 py-3.5">Char ID</th>
                        <th class="px-5 py-3.5">Character</th>
                        <th class="px-5 py-3.5">Account</th>
                        <th class="px-5 py-3.5">Class &amp; Level</th>
                        <th class="px-5 py-3.5">Zeny</th>
                        <th class="px-5 py-3.5">Map &amp; Coords</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-xs text-slate-300">
                    @forelse($characters as $char)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-slate-500">#{{ $char->char_id }}</td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-white flex items-center gap-2">
                                    <span>{{ $char->name }}</span>
                                    @if($char->active_lock)
                                        <span class="px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px]">LOCKED</span>
                                    @endif
                                    @if(($char->delete_date ?? 0) > 0)
                                        <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px]">DELETING</span>
                                    @endif
                                </div>
                                @if($char->guild_name)
                                    <span class="text-[11px] text-slate-400">Guild: {{ $char->guild_name }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="text-slate-200 font-medium">{{ $char->account_userid ?? 'Unknown' }}</div>
                                <span class="font-mono text-[11px] text-slate-500">AID #{{ $char->account_id }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-cyan-400 font-semibold block">{{ $char->job_name }}</span>
                                <span class="text-slate-400">Lv. {{ $char->base_level }} / {{ $char->job_level }}</span>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-amber-400">{{ number_format($char->zeny) }} Z</td>
                            <td class="px-5 py-3.5 font-mono text-slate-400">{{ $char->last_map }} ({{ $char->last_x }}, {{ $char->last_y }})</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ (int) $char->online > 0 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                                    {{ (int) $char->online > 0 ? 'Online' : 'Offline' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.characters.show', $char->char_id) }}" class="px-3 py-1.5 rounded-lg bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-500/30 font-semibold inline-flex items-center gap-1 transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    <span>Inspect</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">No characters matched your filter criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($characters->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $characters->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
