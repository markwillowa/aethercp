@extends('layouts.app')

@section('title', 'My Support Tickets')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header Card -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-500 flex items-center justify-center font-bold text-slate-950 shrink-0 shadow-lg">
                <i data-lucide="inbox" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-wide">My Support Tickets</h1>
                <p class="text-xs text-slate-400">View status, check staff responses, and continue conversations.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('support.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4 text-cyan-400"></i>
                <span>Support Home</span>
            </a>
            @if($settings['enabled'])
                <a href="{{ route('support.create') }}" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 shadow-md shadow-cyan-500/20 flex items-center gap-1.5 transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>New Ticket</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0 text-emerald-400"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter & Search Console -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-lg">
        <form action="{{ route('support.my-tickets') }}" method="GET" class="space-y-3">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative flex-1" style="position: relative;">
                    <div style="position: absolute; left: 16px; top: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10;">
                        <i data-lucide="search" class="w-4 h-4 text-cyan-400" style="width: 16px; height: 16px;"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets by reference code or subject..." 
                           style="padding-left: 48px !important; padding-right: 16px !important; height: 44px;"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl text-xs font-medium text-slate-100 placeholder:text-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all shadow-inner">
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 cursor-pointer flex items-center gap-1.5 shadow-md shadow-cyan-500/20">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Filter</span>
                    </button>
                    @if(request('search') || request('status') || request('category'))
                        <a href="{{ route('support.my-tickets') }}" class="px-3.5 py-2.5 rounded-xl text-xs font-medium bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 cursor-pointer flex items-center gap-1" title="Reset Filters">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Status Tabs & Category Selector -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-800">
                <!-- Status Switcher -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    @php
                        $statuses = [
                            'all' => 'All Tickets',
                            'open' => 'Open',
                            'in_progress' => 'In Progress',
                            'awaiting_player' => 'Awaiting Response',
                            'resolved' => 'Resolved',
                            'closed' => 'Closed',
                        ];
                        $curStatus = request('status', 'all');
                    @endphp

                    @foreach($statuses as $sKey => $sLabel)
                        <a href="{{ route('support.my-tickets', array_merge(request()->except('page'), ['status' => $sKey])) }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $curStatus === $sKey ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/30' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                            {{ $sLabel }}
                        </a>
                    @endforeach
                </div>

                <!-- Category Filter -->
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-slate-500 whitespace-nowrap">Category:</span>
                    <select name="category" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg text-xs bg-slate-950 border border-slate-800 text-slate-300 focus:border-cyan-500 cursor-pointer">
                        <option value="">All Categories</option>
                        <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Account</option>
                        <option value="character" {{ request('category') === 'character' ? 'selected' : '' }}>Character</option>
                        <option value="in_game" {{ request('category') === 'in_game' ? 'selected' : '' }}>In-Game</option>
                        <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technical</option>
                        <option value="donation" {{ request('category') === 'donation' ? 'selected' : '' }}>Payment / Donation</option>
                        <option value="bug_report" {{ request('category') === 'bug_report' ? 'selected' : '' }}>Bug Reports</option>
                        <option value="report_player" {{ request('category') === 'report_player' ? 'selected' : '' }}>Player Report</option>
                        <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/80 text-slate-400 text-[11px] font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Reference</th>
                        <th class="py-3.5 px-4">Subject</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4 text-center">Priority</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Created</th>
                        <th class="py-3.5 px-4 text-right">Last Updated</th>
                        <th class="py-3.5 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-cyan-400 whitespace-nowrap">
                                <a href="{{ route('support.show', $ticket->reference) }}" class="hover:underline">
                                    {{ $ticket->reference }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <a href="{{ route('support.show', $ticket->reference) }}" class="font-bold text-white hover:text-cyan-400 transition-colors block truncate max-w-sm">
                                    {{ $ticket->subject }}
                                </a>
                                @if($ticket->character_name)
                                    <span class="text-[10px] text-slate-500 font-mono block">Char: {{ $ticket->character_name }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-300 whitespace-nowrap">
                                {{ $ticket->category_label }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider {{ $ticket->priority_badge_class }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider {{ $ticket->status_badge_class }}">
                                    {{ $ticket->status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-slate-400 whitespace-nowrap">
                                {{ $ticket->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-slate-300 whitespace-nowrap">
                                {{ $ticket->last_activity_at ? $ticket->last_activity_at->diffForHumans() : $ticket->created_at->diffForHumans() }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('support.show', $ticket->reference) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors inline-flex items-center gap-1">
                                    <span>View</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-cyan-400"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-10 h-10 text-slate-600 mx-auto mb-2"></i>
                                <p class="text-sm font-bold text-white">No support tickets found</p>
                                <p class="text-xs text-slate-500 mt-1">Submit your first inquiry if you require game master assistance.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
