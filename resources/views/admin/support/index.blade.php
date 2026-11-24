@extends('layouts.admin')

@section('title', 'Support Center & Ticket Management')

@section('admin_content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl backdrop-blur">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center font-bold shrink-0 shadow-inner">
                <i data-lucide="life-buoy" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-wide uppercase">Support Center Management</h1>
                <p class="text-xs text-slate-400">Review player inquiries, assign staff, reply to tickets, and track resolution audit logs.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.support.settings') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="sliders" class="w-4 h-4 text-cyan-400"></i>
                <span>Support Settings</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0 text-emerald-400"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 text-rose-400"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Statistics Metrics Row -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-800 shadow-sm backdrop-blur">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Total Cases</span>
            <span class="text-xl font-mono font-bold text-white">{{ number_format($stats['total']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-emerald-500/30 shadow-sm backdrop-blur">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-emerald-400 block mb-1">Open</span>
            <span class="text-xl font-mono font-bold text-emerald-300">{{ number_format($stats['open']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-cyan-500/30 shadow-sm backdrop-blur">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-cyan-400 block mb-1">In Progress</span>
            <span class="text-xl font-mono font-bold text-cyan-300">{{ number_format($stats['in_progress']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-amber-500/30 shadow-sm backdrop-blur">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-amber-400 block mb-1">Awaiting Player</span>
            <span class="text-xl font-mono font-bold text-amber-300">{{ number_format($stats['awaiting_player']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-purple-500/30 shadow-sm backdrop-blur">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-purple-400 block mb-1">Awaiting Staff</span>
            <span class="text-xl font-mono font-bold text-purple-300">{{ number_format($stats['awaiting_staff']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-blue-500/30 shadow-sm backdrop-blur">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-400 block mb-1">Resolved</span>
            <span class="text-xl font-mono font-bold text-blue-300">{{ number_format($stats['resolved']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-700 shadow-sm backdrop-blur">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Closed</span>
            <span class="text-xl font-mono font-bold text-slate-300">{{ number_format($stats['closed']) }}</span>
        </div>
    </div>

    <!-- Search & Filter Console -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-lg backdrop-blur">
        <form method="GET" action="{{ route('admin.support.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- Search Query -->
            <div class="sm:col-span-2 relative" style="position: relative; display: flex; align-items: center;">
                <div class="search-icon-centered" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); -webkit-transform: translateY(-50%); display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10; width: 16px; height: 16px; line-height: 0;">
                    <i data-lucide="search" class="w-4 h-4 text-cyan-400" style="width: 16px; height: 16px; display: block;"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search reference, subject, player or character..." 
                       style="width: 100%; padding-left: 44px !important; padding-right: 16px !important; height: 42px; display: block;"
                       class="rounded-xl text-xs bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
            </div>

            <!-- Status -->
            <div>
                <select name="status" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer" style="height: 42px;">
                    <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="awaiting_player" {{ request('status') === 'awaiting_player' ? 'selected' : '' }}>Awaiting Player</option>
                    <option value="awaiting_staff" {{ request('status') === 'awaiting_staff' ? 'selected' : '' }}>Awaiting Staff</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <!-- Category -->
            <div>
                <select name="category" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer" style="height: 42px;">
                    <option value="all" {{ request('category') === 'all' || !request('category') ? 'selected' : '' }}>All Categories</option>
                    <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Account Issues</option>
                    <option value="character" {{ request('category') === 'character' ? 'selected' : '' }}>Character Issues</option>
                    <option value="in_game" {{ request('category') === 'in_game' ? 'selected' : '' }}>In-Game Issues</option>
                    <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technical Support</option>
                    <option value="donation" {{ request('category') === 'donation' ? 'selected' : '' }}>Donations / Billing</option>
                    <option value="bug_report" {{ request('category') === 'bug_report' ? 'selected' : '' }}>Bug Reports</option>
                    <option value="player_report" {{ request('category') === 'player_report' ? 'selected' : '' }}>Report a Player</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Assigned Staff -->
            <div>
                <select name="assigned_to" class="w-full px-3 py-2 rounded-xl text-xs bg-slate-950 border border-slate-800 text-white focus:outline-none focus:border-cyan-500 cursor-pointer" style="height: 42px;">
                    <option value="" {{ !request('assigned_to') ? 'selected' : '' }}>All Staff</option>
                    <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>Unassigned Only</option>
                    @foreach($staffMembers as $sMember)
                        <option value="{{ $sMember->id }}" {{ request('assigned_to') == $sMember->id ? 'selected' : '' }}>
                            {{ $sMember->username }} ({{ ucfirst($sMember->role) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 transition-all flex items-center justify-center gap-1.5 shadow-md cursor-pointer" style="height: 42px;">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Apply</span>
                </button>
                @if(request('search') || request('status') || request('category') || request('assigned_to'))
                    <a href="{{ route('admin.support.index') }}" class="p-2.5 rounded-xl text-xs bg-slate-800 border border-slate-700 text-slate-300 hover:text-white transition-colors flex items-center justify-center shrink-0" title="Reset Filters" style="height: 42px; width: 42px;">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Ticket Management Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-2xl backdrop-blur">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/80 text-slate-300 font-semibold text-[11px] tracking-wider uppercase">
                        <th class="py-3.5 px-4">Reference</th>
                        <th class="py-3.5 px-4">Player &amp; Character</th>
                        <th class="py-3.5 px-4">Subject &amp; Category</th>
                        <th class="py-3.5 px-4 text-center">Priority</th>
                        <th class="py-3.5 px-4">Assigned Staff</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4">Last Activity</th>
                        <th class="py-3.5 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-800/40 transition-colors group">
                            <td class="py-3.5 px-4 font-mono font-bold text-cyan-400">
                                {{ $ticket->ticket_reference }}
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-medium text-white">{{ $ticket->user->username ?? 'User #'.$ticket->user_id }}</p>
                                <p class="text-[10px] text-slate-400">
                                    {{ $ticket->char_name ? 'Char: '.$ticket->char_name : 'Acct #'.$ticket->account_id }}
                                </p>
                            </td>
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.support.show', $ticket->id) }}" class="font-bold text-slate-100 group-hover:text-cyan-400 transition-colors block">
                                    {{ $ticket->subject }}
                                </a>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $ticket->category_label }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono border {{ $ticket->priority_badge_class }}">
                                    {{ $ticket->priority_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($ticket->assignedStaff)
                                    <span class="inline-flex items-center gap-1 font-medium text-cyan-300">
                                        <i data-lucide="shield" class="w-3 h-3 text-cyan-400"></i>
                                        <span>{{ $ticket->assignedStaff->username }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-500 italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono border {{ $ticket->status_badge_class }}">
                                    {{ $ticket->status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px]">
                                {{ $ticket->last_message_at?->diffForHumans() ?? 'N/A' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.support.show', $ticket->id) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 transition-all inline-flex items-center gap-1 shadow-sm">
                                    <span>Manage</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="life-buoy" class="w-8 h-8 text-slate-600"></i>
                                    <p class="text-sm font-semibold text-white">No support tickets match the selected filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
