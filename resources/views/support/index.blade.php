@extends('layouts.app')

@section('title', 'Player Support Center')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <!-- Top Wood Board Header -->
    <div class="chronicles-wood-board flex flex-col md:flex-row md:items-center justify-between gap-6 p-6 shadow-2xl">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center shrink-0 shadow-lg" style="background: radial-gradient(circle, #E5B358 0%, #806442 100%); border: 2px solid #FCE69B; color: #17111F;">
                <i data-lucide="life-buoy" class="w-8 h-8"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-[#B69A65] font-cinzel">Rune-Midgard Support</span>
                    <span class="px-2 py-0.5 rounded text-[9px] font-bold font-mono uppercase bg-[#140E1B] text-[#D4AF37] border border-[#806442]">Direct Help</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-cinzel text-[#FCE69B] tracking-wider uppercase drop-shadow">
                    Support Center
                </h1>
                <p class="text-xs text-[#C8BDAA] mt-0.5">We're here to help. Submit inquiries, report issues, and track your active tickets.</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('support.my-tickets') }}" class="chronicles-btn-dark py-2.5 px-4 cursor-pointer flex items-center gap-1.5 shadow-md">
                <i data-lucide="inbox" class="w-4 h-4 text-[#D4AF37]"></i>
                <span class="font-cinzel text-xs font-bold">My Tickets</span>
            </a>
            @if($settings['enabled'])
                <a href="{{ route('support.create') }}" class="chronicles-btn-gold py-2.5 px-5 cursor-pointer flex items-center gap-1.5 shadow-md">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span class="font-cinzel text-xs font-bold tracking-wide">SUBMIT A TICKET</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Announcement Banner -->
    @if(!empty($settings['announcement']))
        <div class="p-4 rounded-xl bg-[#1C1425] border border-[#5A456B] text-xs flex items-center gap-3 text-[#EDE4D2] shadow-md">
            <i data-lucide="megaphone" class="w-5 h-5 text-[#FFD700] shrink-0"></i>
            <span class="text-[#E2D9EB]">{{ $settings['announcement'] }}</span>
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
        <div class="chronicles-panel p-4" style="background: #181120; border: 1.5px solid #4A3A5A;">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A898B5] font-cinzel">Active Tickets</span>
                <i data-lucide="activity" class="w-4 h-4 text-[#FFD700]"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-[#FFE4A0]">{{ $stats['active'] }}</div>
            <p class="text-[10px] text-[#8C7B99] mt-0.5">Under active review</p>
        </div>

        <div class="chronicles-panel p-4" style="background: #181120; border: 1.5px solid #4A3A5A;">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A898B5] font-cinzel">Awaiting Response</span>
                <i data-lucide="clock" class="w-4 h-4 text-amber-400"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-amber-300">{{ $stats['awaiting_player'] }}</div>
            <p class="text-[10px] text-[#8C7B99] mt-0.5">Staff replied to you</p>
        </div>

        <div class="chronicles-panel p-4" style="background: #181120; border: 1.5px solid #4A3A5A;">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A898B5] font-cinzel">Resolved</span>
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-emerald-300">{{ $stats['resolved'] }}</div>
            <p class="text-[10px] text-[#8C7B99] mt-0.5">Issues addressed</p>
        </div>

        <div class="chronicles-panel p-4" style="background: #181120; border: 1.5px solid #4A3A5A;">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A898B5] font-cinzel">Total Inquiries</span>
                <i data-lucide="folder" class="w-4 h-4 text-cyan-400"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-cyan-300">{{ $stats['total'] }}</div>
            <p class="text-[10px] text-[#8C7B99] mt-0.5">All time submissions</p>
        </div>
    </div>

    <!-- Quick Help Categories -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-[#181120] border border-[#3E2E4E] hover:border-[#D4AF37]/50 transition-colors">
            <div class="w-9 h-9 rounded-lg bg-[#251A33] border border-[#5A456B] flex items-center justify-center text-[#FFD700] mb-3">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <h4 class="font-cinzel font-bold text-sm text-[#F3E5AB]">Account &amp; Security</h4>
            <p class="text-xs text-[#A898B5] mt-1">Password resets, pin code changes, 2FA authorization, and security inquiries.</p>
        </div>

        <div class="p-4 rounded-xl bg-[#181120] border border-[#3E2E4E] hover:border-[#D4AF37]/50 transition-colors">
            <div class="w-9 h-9 rounded-lg bg-[#251A33] border border-[#5A456B] flex items-center justify-center text-cyan-400 mb-3">
                <i data-lucide="sword" class="w-5 h-5"></i>
            </div>
            <h4 class="font-cinzel font-bold text-sm text-[#F3E5AB]">Character &amp; Gameplay</h4>
            <p class="text-xs text-[#A898B5] mt-1">Stuck characters, lost items, quest issues, or guild management assistance.</p>
        </div>

        <div class="p-4 rounded-xl bg-[#181120] border border-[#3E2E4E] hover:border-[#D4AF37]/50 transition-colors">
            <div class="w-9 h-9 rounded-lg bg-[#251A33] border border-[#5A456B] flex items-center justify-center text-amber-400 mb-3">
                <i data-lucide="gem" class="w-5 h-5"></i>
            </div>
            <h4 class="font-cinzel font-bold text-sm text-[#F3E5AB]">Donations &amp; Billing</h4>
            <p class="text-xs text-[#A898B5] mt-1">PayPal verification, donation package rewards, and Cash Point credits.</p>
        </div>

        <div class="p-4 rounded-xl bg-[#181120] border border-[#3E2E4E] hover:border-[#D4AF37]/50 transition-colors">
            <div class="w-9 h-9 rounded-lg bg-[#251A33] border border-[#5A456B] flex items-center justify-center text-rose-400 mb-3">
                <i data-lucide="bug" class="w-5 h-5"></i>
            </div>
            <h4 class="font-cinzel font-bold text-sm text-[#F3E5AB]">Bug Reports &amp; Abuse</h4>
            <p class="text-xs text-[#A898B5] mt-1">Client crashes, map glitches, rule violations, or suspicious bot behavior.</p>
        </div>
    </div>

    <!-- Recently Updated Tickets Table -->
    <div class="chronicles-panel overflow-hidden shadow-2xl" style="background: #181120; border: 2px solid #5A456B;">
        <div class="p-4 border-b border-[#3E2E4E] bg-[#22172D] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-[#FFD700]"></i>
                <h3 class="font-cinzel text-sm font-bold text-[#F3E5AB] uppercase tracking-wide">Recent Support Tickets</h3>
            </div>
            <a href="{{ route('support.my-tickets') }}" class="text-xs text-[#D4AF37] hover:text-[#FFE4A0] font-cinzel font-bold flex items-center gap-1">
                <span>View All Tickets</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-[#3E2E4E] bg-[#1E1428] text-[#D8CDBC] font-cinzel text-[11px] tracking-wide uppercase">
                        <th class="py-3 px-4">Reference</th>
                        <th class="py-3 px-4">Subject</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Priority</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Last Activity</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2F213C]">
                    @forelse($recentTickets as $ticket)
                        <tr class="hover:bg-[#251A33] transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-[#FFE894]">
                                {{ $ticket->ticket_reference }}
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('support.show', $ticket->ticket_reference) }}" class="font-cinzel font-bold text-[#FCE69B] hover:text-white transition-colors">
                                    {{ $ticket->subject }}
                                </a>
                                @if($ticket->char_name)
                                    <p class="text-[10px] text-[#A898B5] mt-0.5">Char: {{ $ticket->char_name }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-[#D8CDBC]">
                                {{ $ticket->category_label }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono border {{ $ticket->priority_badge_class }}">
                                    {{ $ticket->priority_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono border {{ $ticket->status_badge_class }}">
                                    {{ $ticket->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-[#A898B5]">
                                {{ $ticket->last_message_at->diffForHumans() }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('support.show', $ticket->ticket_reference) }}" class="chronicles-btn-gold py-1 px-3 text-[11px] font-cinzel font-bold inline-flex items-center gap-1">
                                    <span>View</span>
                                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-[#C8BDAA] font-cinzel">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="inbox" class="w-8 h-8 text-[#5A456B]"></i>
                                    <p class="text-sm font-semibold text-[#E2D9EB]">You have not submitted any support tickets yet.</p>
                                    @if($settings['enabled'])
                                        <a href="{{ route('support.create') }}" class="chronicles-btn-gold py-1.5 px-4 text-xs mt-2 inline-flex items-center gap-1.5">
                                            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                            <span>Submit Your First Ticket</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
