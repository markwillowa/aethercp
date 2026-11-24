@extends('layouts.admin')

@section('title', 'Support Center & Ticket Management')

@section('admin_content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-[#1C1425] border border-[#4A3A5A] shadow-xl">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-[#D4AF37]/20 border border-[#D4AF37]/50 text-[#FCE69B] flex items-center justify-center font-bold shrink-0 shadow-inner">
                <i data-lucide="life-buoy" class="w-6 h-6 text-[#FFD700]"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-white font-cinzel tracking-wide uppercase">Support Center Management</h1>
                <p class="text-xs text-[#C8BDAA]">Review player inquiries, assign staff, reply to tickets, and track resolution audit logs.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.support.settings') }}" class="px-4 py-2 rounded-xl text-xs font-cinzel font-bold bg-[#140E1B] border border-[#5A456B] text-[#FFE894] hover:bg-[#251A33] hover:text-white transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="sliders" class="w-4 h-4 text-[#D4AF37]"></i>
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
        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-[#3E2F4D]">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Cases</span>
            <span class="text-xl font-mono font-bold text-white">{{ number_format($stats['total']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-emerald-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block mb-1">Open</span>
            <span class="text-xl font-mono font-bold text-emerald-300">{{ number_format($stats['open']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-cyan-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 block mb-1">In Progress</span>
            <span class="text-xl font-mono font-bold text-cyan-300">{{ number_format($stats['in_progress']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-amber-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 block mb-1">Awaiting Player</span>
            <span class="text-xl font-mono font-bold text-amber-300">{{ number_format($stats['awaiting_player']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-purple-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-400 block mb-1">Awaiting Staff</span>
            <span class="text-xl font-mono font-bold text-purple-300">{{ number_format($stats['awaiting_staff']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-blue-500/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-400 block mb-1">Resolved</span>
            <span class="text-xl font-mono font-bold text-blue-300">{{ number_format($stats['resolved']) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-[#1C1425] border border-slate-700">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Closed</span>
            <span class="text-xl font-mono font-bold text-slate-300">{{ number_format($stats['closed']) }}</span>
        </div>
    </div>

    <!-- Search & Filter Console -->
    <div class="p-4 rounded-2xl bg-[#1C1425] border border-[#3E2F4D] shadow-lg">
        <form method="GET" action="{{ route('admin.support.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- Search Query -->
            <div class="sm:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4 text-[#C9A96E]"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search reference, subject, player or character..." 
                       style="padding-left: 3rem !important; padding-right: 1rem !important; height: 40px;"
                       class="w-full rounded-xl text-xs bg-[#140E1B] border border-[#5A456B] text-white placeholder:text-[#8D7D9A] focus:outline-none focus:border-[#D4AF37] transition-all">
            </div>

            <!-- Status -->
            <div>
                <select name="status" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#5A456B] text-white focus:outline-none focus:border-[#D4AF37] cursor-pointer" style="height: 40px;">
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
                <select name="category" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#5A456B] text-white focus:outline-none focus:border-[#D4AF37] cursor-pointer" style="height: 40px;">
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
                <select name="assigned_to" class="w-full px-3 py-2 rounded-xl text-xs bg-[#140E1B] border border-[#5A456B] text-white focus:outline-none focus:border-[#D4AF37] cursor-pointer" style="height: 40px;">
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
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl text-xs font-cinzel font-bold bg-[#D4AF37] hover:bg-[#FFE4A0] text-black transition-colors flex items-center justify-center gap-1.5 shadow-md" style="height: 40px;">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Apply</span>
                </button>
                @if(request('search') || request('status') || request('category') || request('assigned_to'))
                    <a href="{{ route('admin.support.index') }}" class="p-2.5 rounded-xl text-xs bg-[#140E1B] border border-[#5A456B] text-slate-300 hover:text-white transition-colors" title="Reset Filters" style="height: 40px;">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Ticket Management Table -->
    <div class="rounded-2xl bg-[#1C1425] border border-[#3E2F4D] overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-[#3E2F4D] bg-[#22172D] text-[#F3E5AB] font-cinzel text-[11px] tracking-wide uppercase">
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
                <tbody class="divide-y divide-[#2F213C]">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-[#251A33] transition-colors group">
                            <td class="py-3.5 px-4 font-mono font-bold text-[#FFE894]">
                                {{ $ticket->ticket_reference }}
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-medium text-white">{{ $ticket->user->username ?? 'User #'.$ticket->user_id }}</p>
                                <p class="text-[10px] text-slate-400">
                                    {{ $ticket->char_name ? 'Char: '.$ticket->char_name : 'Acct #'.$ticket->account_id }}
                                </p>
                            </td>
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.support.show', $ticket->id) }}" class="font-bold text-[#FCE69B] group-hover:text-white transition-colors block">
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
                                        <i data-lucide="shield" class="w-3 h-3"></i>
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
                                {{ $ticket->last_message_at->diffForHumans() }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.support.show', $ticket->id) }}" class="px-3 py-1.5 rounded-lg text-xs font-cinzel font-bold bg-[#D4AF37] hover:bg-[#FFE4A0] text-black transition-colors inline-flex items-center gap-1 shadow-sm">
                                    <span>Manage</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="life-buoy" class="w-8 h-8 text-[#5A456B]"></i>
                                    <p class="text-sm font-semibold text-white">No support tickets match the selected filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="p-4 border-t border-[#3E2F4D] bg-[#140E1B]">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
