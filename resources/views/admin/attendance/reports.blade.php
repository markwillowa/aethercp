@extends('layouts.admin')

@section('title', 'Attendance & Participation Reports - Admin Panel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.attendance.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 font-medium">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Campaigns</span>
                </a>
                <span class="text-xs text-slate-600">/</span>
                <span class="text-xs text-cyan-400 font-medium">Player Participation</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1">
                Attendance &amp; Claim Reports
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
                Track player attendance streaks, progress against total campaign days, and claim histories.
            </p>
        </div>
    </div>

    <!-- Filter Console -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.attendance.reports') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
            <select name="campaign_id" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 focus:border-cyan-500 focus:outline-none cursor-pointer">
                <option value="">All Campaigns</option>
                @foreach($campaigns as $c)
                    <option value="{{ $c->id }}" {{ request('campaign_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->duration_days }}d)</option>
                @endforeach
            </select>

            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by Account ID or Username..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder:text-slate-500 focus:border-cyan-500 focus:outline-none">
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white transition-colors">Search</button>
            @if(request('campaign_id') || request('q'))
                <a href="{{ route('admin.attendance.reports') }}" class="px-3 py-2 rounded-xl text-xs text-slate-400 hover:text-white transition-colors">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 border-b border-slate-800 text-[10px] uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="py-3.5 px-4">Player / Account</th>
                        <th class="py-3.5 px-4">Campaign</th>
                        <th class="py-3.5 px-4 text-center">Progress</th>
                        <th class="py-3.5 px-4 text-center">Current Streak</th>
                        <th class="py-3.5 px-4 text-center">Best Streak</th>
                        <th class="py-3.5 px-4 text-center">Last Claimed</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($participations as $part)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-white block">{{ $part->user?->username ?? 'Account #' . $part->account_id }}</span>
                                <span class="text-[10px] text-slate-500 font-mono">Account #{{ $part->account_id }}</span>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="font-bold text-cyan-400">{{ $part->campaign?->name }}</span>
                                <span class="text-[10px] text-slate-500 block">Total: {{ $part->campaign?->duration_days }} Days</span>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $pct = $part->campaign?->duration_days > 0 ? round(($part->total_claimed_days / $part->campaign->duration_days) * 100) : 0;
                                @endphp
                                <span class="font-mono font-bold text-white">{{ $part->total_claimed_days }} / {{ $part->campaign?->duration_days }}</span>
                                <div class="w-24 h-1.5 bg-slate-800 rounded-full mx-auto mt-1 overflow-hidden">
                                    <div class="h-full bg-cyan-400 rounded-full" style="width: {{ min(100, $pct) }}%;"></div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-center font-mono font-bold text-amber-300">
                                {{ $part->current_streak }} {{ Str::plural('Day', $part->current_streak) }}
                            </td>

                            <td class="py-3.5 px-4 text-center font-mono text-slate-400">
                                {{ $part->highest_streak }} {{ Str::plural('Day', $part->highest_streak) }}
                            </td>

                            <td class="py-3.5 px-4 text-center font-mono text-slate-400 text-[11px]">
                                {{ $part->last_claimed_at ? $part->last_claimed_at->format('M d, Y H:i') : 'Never' }}
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $part->status === 'completed' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-cyan-950 text-cyan-400 border border-cyan-500/30' }}">
                                    {{ $part->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                                <p class="text-xs font-semibold text-slate-300">No participation records found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($participations->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $participations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
