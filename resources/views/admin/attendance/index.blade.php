@extends('layouts.admin')

@section('title', 'Daily Rewards & Attendance Campaigns - Admin Panel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Engagement System</span>
                <span class="text-xs text-slate-500">•</span>
                <span class="text-xs text-cyan-400 font-medium">Multi-Campaign Engine</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1">
                Daily Rewards &amp; Attendance
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
                Manage customizable login reward campaigns of any duration (5, 7, 14, 30, 90, 365+ days) with automated schedule generators.
            </p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.attendance.deliveries') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 flex items-center gap-1.5 transition-colors">
                <i data-lucide="truck" class="w-3.5 h-3.5 text-cyan-400"></i>
                <span>Delivery Logs</span>
            </a>
            <a href="{{ route('admin.attendance.reports') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 flex items-center gap-1.5 transition-colors">
                <i data-lucide="bar-chart-3" class="w-3.5 h-3.5 text-purple-400"></i>
                <span>Reports</span>
            </a>
            <a href="{{ route('admin.attendance.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 flex items-center gap-1.5 shadow-lg shadow-cyan-500/20 transition-all">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Create Campaign</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 block">Total Campaigns</span>
            <span class="text-xl font-black text-white mt-1 block">{{ number_format($stats['total_campaigns']) }}</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-emerald-400 block">Active Campaigns</span>
            <span class="text-xl font-black text-emerald-300 mt-1 block">{{ number_format($stats['active_campaigns']) }}</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 block">Total Participants</span>
            <span class="text-xl font-black text-cyan-400 mt-1 block">{{ number_format($stats['total_participants']) }}</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 block">Total Claims</span>
            <span class="text-xl font-black text-purple-400 mt-1 block">{{ number_format($stats['total_claims']) }}</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 block">Delivered Rewards</span>
            <span class="text-xl font-black text-amber-400 mt-1 block">{{ number_format($stats['delivered_count']) }}</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 block">Failed Deliveries</span>
            <span class="text-xl font-black {{ $stats['failed_count'] > 0 ? 'text-rose-400' : 'text-slate-500' }} mt-1 block">
                {{ number_format($stats['failed_count']) }}
            </span>
        </div>
    </div>

    <!-- Filter & Search Console -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search campaign by name or description..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder:text-slate-500 focus:border-cyan-500 focus:outline-none">
            </div>
            <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 focus:border-cyan-500 focus:outline-none cursor-pointer">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white transition-colors">Filter</button>
            @if(request('q') || request('status'))
                <a href="{{ route('admin.attendance.index') }}" class="px-3 py-2 rounded-xl text-xs text-slate-400 hover:text-white transition-colors">Reset</a>
            @endif
        </form>
    </div>

    <!-- Campaigns List -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 border-b border-slate-800 text-[10px] uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="py-3.5 px-4">Campaign</th>
                        <th class="py-3.5 px-4 text-center">Duration</th>
                        <th class="py-3.5 px-4 text-center">Schedule Days</th>
                        <th class="py-3.5 px-4 text-center">Mode &amp; Scope</th>
                        <th class="py-3.5 px-4 text-center">Delivery</th>
                        <th class="py-3.5 px-4 text-center">Participants</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($campaigns as $camp)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-4">
                                <div class="min-w-[200px]">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-white text-sm hover:text-cyan-400 transition-colors">
                                            <a href="{{ route('admin.attendance.schedule', $camp->id) }}">{{ $camp->name }}</a>
                                        </h3>
                                        @if($camp->repeatable)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-purple-950/60 text-purple-300 border border-purple-500/30">Repeatable</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $camp->description ?: 'No description provided.' }}</p>
                                    <div class="flex items-center gap-2 text-[10px] font-mono text-slate-500 mt-1">
                                        <span>Slug: {{ $camp->slug }}</span>
                                        @if($camp->start_at || $camp->end_at)
                                            <span>•</span>
                                            <span>{{ $camp->start_at ? $camp->start_at->format('M d') : 'Open' }} – {{ $camp->end_at ? $camp->end_at->format('M d, Y') : 'Indefinite' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-950 border border-slate-800 text-cyan-300">
                                    {{ $camp->duration_days }} {{ Str::plural('Day', $camp->duration_days) }}
                                </span>
                            </td>

                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('admin.attendance.schedule', $camp->id) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-950 hover:bg-slate-800 border {{ $camp->days_count >= $camp->duration_days ? 'border-emerald-500/30 text-emerald-400' : 'border-amber-500/30 text-amber-400' }} transition-colors" title="Manage Schedule">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    <span>{{ $camp->days_count }} / {{ $camp->duration_days }} Configured</span>
                                </a>
                            </td>

                            <td class="py-4 px-4 text-center">
                                <span class="block font-semibold capitalize text-slate-300 text-[11px]">{{ $camp->progression_mode }}</span>
                                <span class="text-[10px] text-slate-500 uppercase font-mono">{{ $camp->participation_scope }}-scoped</span>
                            </td>

                            <td class="py-4 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-800 text-slate-300 capitalize">
                                    {{ $camp->delivery_destination }}
                                </span>
                            </td>

                            <td class="py-4 px-4 text-center font-mono">
                                <span class="font-bold text-white">{{ number_format($camp->participations_count) }}</span>
                                <span class="text-[10px] text-slate-500 block">({{ number_format($camp->claims_count) }} claims)</span>
                            </td>

                            <td class="py-4 px-4 text-center">
                                <form method="POST" action="{{ route('admin.attendance.toggle-status', $camp->id) }}">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider transition-colors cursor-pointer {{ $camp->status === 'active' ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-900' : ($camp->status === 'draft' ? 'bg-slate-800 text-slate-400 border border-slate-700' : 'bg-amber-950/80 text-amber-300 border border-amber-500/30 hover:bg-amber-900') }}" title="Click to toggle active/paused">
                                        {{ $camp->status }}
                                    </button>
                                </form>
                            </td>

                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.attendance.schedule', $camp->id) }}" class="p-1.5 rounded-lg bg-cyan-950/50 hover:bg-cyan-900/60 text-cyan-400 border border-cyan-500/30 transition-colors" title="Schedule Builder">
                                        <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.attendance.duplicate', $camp->id) }}">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors cursor-pointer" title="Duplicate Campaign">
                                            <i data-lucide="copy" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.attendance.edit', $camp->id) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors" title="Edit Settings">
                                        <i data-lucide="settings" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.attendance.destroy', $camp->id) }}" onsubmit="return confirm('Permanently delete campaign {{ $camp->name }}? All schedule and participant records will be purged.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-950/40 hover:bg-rose-900/60 text-rose-400 border border-rose-500/30 transition-colors cursor-pointer" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <i data-lucide="calendar-x" class="w-10 h-10 mx-auto mb-2 text-slate-600"></i>
                                <p class="text-sm font-semibold text-slate-300">No reward campaigns found.</p>
                                <p class="text-xs text-slate-500 mt-0.5">Click "Create Campaign" to configure your first login rewards schedule.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($campaigns->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
