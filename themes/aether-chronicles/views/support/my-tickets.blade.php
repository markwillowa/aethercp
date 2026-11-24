@extends('layouts.app')

@section('title', 'My Support Tickets')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <!-- Top Wood Board Header -->
    <div class="chronicles-wood-board flex flex-col md:flex-row md:items-center justify-between gap-6 p-6 shadow-2xl">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('support.index') }}" class="text-xs text-[#D4AF37] hover:text-[#FFE4A0] font-cinzel font-bold flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Support Center</span>
                </a>
                <span class="text-slate-500">•</span>
                <span class="text-[10px] text-[#A898B5] font-mono uppercase">Ticket Ledger</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black font-cinzel text-[#FCE69B] tracking-wider uppercase mt-1">
                My Support Tickets
            </h1>
            <p class="text-xs text-[#C8BDAA] mt-0.5">Track your open cases, view administrative responses, and manage existing tickets.</p>
        </div>

        @if($settings['enabled'])
            <div>
                <a href="{{ route('support.create') }}" class="chronicles-btn-gold py-2.5 px-5 cursor-pointer flex items-center gap-1.5 shadow-md">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span class="font-cinzel text-xs font-bold tracking-wide">SUBMIT A TICKET</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Filter & Search Console -->
    <div class="chronicles-panel p-4" style="background: linear-gradient(180deg, #1F162A 0%, #160F1E 100%); border: 2px solid #5A456B; box-shadow: 0 4px 20px rgba(0,0,0,0.6);">
        <form action="{{ route('support.my-tickets') }}" method="GET" class="space-y-3">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative flex-1" style="position: relative;">
                    <div style="position: absolute; left: 16px; top: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 10;">
                        <i data-lucide="search" class="w-4 h-4 text-[#C9A96E]" style="width: 16px; height: 16px;"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets by reference code or subject..." 
                           style="padding-left: 48px !important; padding-right: 16px !important; height: 44px;"
                           class="w-full bg-[#110B17] border border-[#6B537F] rounded-xl text-xs font-medium text-[#F3E5AB] placeholder:text-[#8D7D9A] focus:outline-none focus:border-[#D4AF37] focus:ring-1 focus:ring-[#D4AF37] transition-all shadow-inner">
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="chronicles-btn-gold py-2.5 px-5 cursor-pointer flex items-center gap-1.5 shadow-md">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Filter</span>
                    </button>
                    @if(request('search') || request('status') || request('category'))
                        <a href="{{ route('support.my-tickets') }}" class="chronicles-btn-dark py-2.5 px-3.5 cursor-pointer flex items-center gap-1" title="Reset Filters">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Status Tabs & Category Selector -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-[#352643]">
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
                    @foreach($statuses as $k => $label)
                        <a href="{{ route('support.my-tickets', array_merge(request()->query(), ['status' => $k])) }}" 
                           class="px-2.5 py-1 rounded-lg text-xs font-cinzel font-bold whitespace-nowrap transition-colors {{ $curStatus === $k ? 'bg-[#D4AF37]/20 text-[#FFE894] border border-[#D4AF37]/60' : 'text-[#A898B5] hover:text-white bg-[#140E1B] border border-transparent' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <!-- Category Selector -->
                <div class="shrink-0 w-full sm:w-48">
                    <select name="category" onchange="this.form.submit()" class="w-full px-3 py-1.5 bg-[#110B17] border border-[#6B537F] rounded-lg text-xs text-[#EDE4D2] focus:outline-none focus:border-[#D4AF37] cursor-pointer">
                        <option value="all">All Categories</option>
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
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="chronicles-panel overflow-hidden shadow-2xl" style="background: #181120; border: 2px solid #5A456B;">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b-2 border-[#5A456B] bg-[#22172D] text-[#F3E5AB] font-cinzel text-[11px] tracking-wide uppercase">
                        <th class="py-3.5 px-4">Ticket Ref</th>
                        <th class="py-3.5 px-4">Subject</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4 text-center">Priority</th>
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
                                <a href="{{ route('support.show', $ticket->ticket_reference) }}" class="font-cinzel font-bold text-[#FCE69B] group-hover:text-white transition-colors block">
                                    {{ $ticket->subject }}
                                </a>
                                <div class="flex items-center gap-2 mt-0.5 text-[10px] text-[#A898B5]">
                                    @if($ticket->char_name)
                                        <span>Char: <strong class="text-[#E2D9EB]">{{ $ticket->char_name }}</strong></span>
                                        <span>•</span>
                                    @endif
                                    <span>Submitted {{ $ticket->created_at->format('M d, Y') }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-[#D8CDBC]">
                                {{ $ticket->category_label }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono border {{ $ticket->priority_badge_class }}">
                                    {{ $ticket->priority_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono border {{ $ticket->status_badge_class }}">
                                    {{ $ticket->status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-[#A898B5]">
                                {{ $ticket->last_message_at->diffForHumans() }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('support.show', $ticket->ticket_reference) }}" class="chronicles-btn-gold py-1.5 px-3.5 text-[11px] font-cinzel font-bold inline-flex items-center gap-1 shadow-md hover:scale-105 transition-transform">
                                    <span>View Case</span>
                                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-14 text-[#C8BDAA] font-cinzel">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="inbox" class="w-8 h-8 text-[#5A456B]"></i>
                                    <p class="text-sm font-semibold text-[#E2D9EB]">No tickets found matching your query.</p>
                                    <p class="text-xs text-[#A898B5]">Try resetting your search query or selecting a different status filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="p-4 border-t border-[#3E2E4E] bg-[#140E1B]">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
