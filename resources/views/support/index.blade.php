@extends('layouts.app')

@section('title', 'Player Support Center')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header Card -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-500 flex items-center justify-center shrink-0 shadow-lg text-slate-950 font-bold">
                <i data-lucide="life-buoy" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-cyan-400 uppercase tracking-wider">Help &amp; Inquiries</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-slate-800 text-slate-300 border border-slate-700">Staff Support</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold aether-gradient-text tracking-tight mt-0.5">
                    Player Support Center
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Submit inquiries, track investigations, and communicate directly with game masters.</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('support.my-tickets') }}" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 cursor-pointer flex items-center gap-1.5 transition-colors">
                <i data-lucide="inbox" class="w-4 h-4 text-cyan-400"></i>
                <span>My Tickets</span>
            </a>
            @if($settings['enabled'])
                <a href="{{ route('support.create') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 hover:from-cyan-400 hover:to-blue-500 cursor-pointer flex items-center gap-1.5 shadow-md shadow-cyan-500/20 transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Submit Ticket</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Announcement Banner -->
    @if(!empty($settings['announcement']))
        <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 text-xs flex items-center gap-3 text-slate-300 shadow-md">
            <i data-lucide="megaphone" class="w-5 h-5 text-cyan-400 shrink-0"></i>
            <span>{{ $settings['announcement'] }}</span>
        </div>
    @endif

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

    <!-- Ticket Summary Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Active Tickets</span>
                <i data-lucide="activity" class="w-4 h-4 text-cyan-400"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-cyan-300">{{ $stats['active'] }}</div>
            <p class="text-[10px] text-slate-500 mt-0.5">Under active review</p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Awaiting Response</span>
                <i data-lucide="clock" class="w-4 h-4 text-amber-400"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-amber-300">{{ $stats['awaiting_player'] }}</div>
            <p class="text-[10px] text-slate-500 mt-0.5">Staff replied to you</p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Resolved</span>
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-emerald-300">{{ $stats['resolved'] }}</div>
            <p class="text-[10px] text-slate-500 mt-0.5">Issues addressed</p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total Inquiries</span>
                <i data-lucide="folder" class="w-4 h-4 text-blue-400"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-blue-300">{{ $stats['total'] }}</div>
            <p class="text-[10px] text-slate-500 mt-0.5">All time submissions</p>
        </div>
    </div>

    <!-- Quick Help Topics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 transition-colors">
            <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-cyan-400 mb-3 shadow-inner">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <h4 class="font-bold text-sm text-white">Account &amp; Security</h4>
            <p class="text-xs text-slate-400 mt-1">Password resets, pin code changes, 2FA authorization, and security inquiries.</p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 transition-colors">
            <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-blue-400 mb-3 shadow-inner">
                <i data-lucide="sword" class="w-5 h-5"></i>
            </div>
            <h4 class="font-bold text-sm text-white">Character &amp; Gameplay</h4>
            <p class="text-xs text-slate-400 mt-1">Stuck characters, lost items, quest issues, or guild management assistance.</p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 transition-colors">
            <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-amber-400 mb-3 shadow-inner">
                <i data-lucide="gem" class="w-5 h-5"></i>
            </div>
            <h4 class="font-bold text-sm text-white">Donations &amp; Billing</h4>
            <p class="text-xs text-slate-400 mt-1">PayPal verification, donation package rewards, and Cash Point credits.</p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 transition-colors">
            <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-rose-400 mb-3 shadow-inner">
                <i data-lucide="bug" class="w-5 h-5"></i>
            </div>
            <h4 class="font-bold text-sm text-white">Bug Reports &amp; Abuse</h4>
            <p class="text-xs text-slate-400 mt-1">Client crashes, map glitches, rule violations, or suspicious bot behavior.</p>
        </div>
    </div>

    <!-- Recently Updated Tickets Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 bg-slate-950/80 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-cyan-400"></i>
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Recently Updated Tickets</h3>
            </div>
            <a href="{{ route('support.my-tickets') }}" class="text-xs text-cyan-400 hover:text-cyan-300 flex items-center gap-1 font-semibold">
                <span>View All Tickets</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/60 text-slate-400 text-[11px] font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4">Reference</th>
                        <th class="py-3 px-4">Subject</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4 text-center">Priority</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Last Activity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($recentTickets as $ticket)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-cyan-400 whitespace-nowrap">
                                <a href="{{ route('support.show', $ticket->reference) }}" class="hover:underline">
                                    {{ $ticket->reference }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('support.show', $ticket->reference) }}" class="font-bold text-white hover:text-cyan-400 transition-colors block truncate max-w-xs">
                                    {{ $ticket->subject }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-300 whitespace-nowrap">
                                {{ $ticket->category_label }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider {{ $ticket->priority_badge_class }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider {{ $ticket->status_badge_class }}">
                                    {{ $ticket->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-400 whitespace-nowrap">
                                {{ $ticket->last_activity_at ? $ticket->last_activity_at->diffForHumans() : $ticket->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                You do not have any submitted tickets yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
