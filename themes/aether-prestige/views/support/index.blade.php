@extends('layouts.app')

@section('title', 'Player Support Center')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <!-- News & Marketplace Style Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Player Support Center
            </h1>
            <p class="text-xs text-[#788394] mt-1">
                Submit inquiries, track investigations, and communicate directly with game masters.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('support.my-tickets') }}" class="h-9 px-4 rounded-xl bg-[#151B26] border border-white/[0.08] text-xs font-bold text-[#AAB3C1] hover:text-white inline-flex items-center gap-1.5">
                <i data-lucide="inbox" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                <span>My Tickets</span>
            </a>
            @if($settings['enabled'])
                <a href="{{ route('support.create') }}" class="h-9 px-4 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider inline-flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    <span>Submit Ticket</span>
                </a>
            @endif
        </div>
    </div>

    @if(!empty($settings['announcement']))
        <div class="prestige-card px-4 py-3 text-xs flex items-center gap-3 text-[#D9DEE7]">
            <i data-lucide="megaphone" class="w-4 h-4 text-[#C6A76A] shrink-0"></i>
            <span>{{ $settings['announcement'] }}</span>
        </div>
    @endif

    <!-- Ticket Summary Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="prestige-card p-5">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-[#788394]">Active Tickets</span>
                <i data-lucide="activity" class="w-4 h-4 text-[#C6A76A]"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-[#D8BE8A]">{{ $stats['active'] }}</div>
            <p class="text-[10px] text-[#788394] mt-0.5">Under active review</p>
        </div>

        <div class="prestige-card p-5">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-[#788394]">Awaiting Response</span>
                <i data-lucide="clock" class="w-4 h-4 text-[#C6A76A]"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-[#D8BE8A]">{{ $stats['awaiting_player'] }}</div>
            <p class="text-[10px] text-[#788394] mt-0.5">Staff replied to you</p>
        </div>

        <div class="prestige-card p-5">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-[#788394]">Resolved</span>
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-emerald-300">{{ $stats['resolved'] }}</div>
            <p class="text-[10px] text-[#788394] mt-0.5">Issues addressed</p>
        </div>

        <div class="prestige-card p-5">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-[#788394]">Total Inquiries</span>
                <i data-lucide="folder" class="w-4 h-4 text-[#C6A76A]"></i>
            </div>
            <div class="mt-2 text-2xl font-black font-mono text-white">{{ $stats['total'] }}</div>
            <p class="text-[10px] text-[#788394] mt-0.5">All time submissions</p>
        </div>
    </div>

    <!-- Quick Help Topics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="prestige-card p-5">
            <div class="w-9 h-9 rounded-xl bg-[#101725] border border-[#C6A76A]/30 flex items-center justify-center text-[#C6A76A] mb-3">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <h4 class="font-cinzel font-bold text-sm text-white">Account &amp; Security</h4>
            <p class="text-xs text-[#AAB3C1] mt-1">Password resets, pin code changes, 2FA authorization, and security inquiries.</p>
        </div>

        <div class="prestige-card p-5">
            <div class="w-9 h-9 rounded-xl bg-[#101725] border border-[#C6A76A]/30 flex items-center justify-center text-[#C6A76A] mb-3">
                <i data-lucide="sword" class="w-5 h-5"></i>
            </div>
            <h4 class="font-cinzel font-bold text-sm text-white">Character &amp; Gameplay</h4>
            <p class="text-xs text-[#AAB3C1] mt-1">Stuck characters, lost items, quest issues, or guild management assistance.</p>
        </div>

        <div class="prestige-card p-5">
            <div class="w-9 h-9 rounded-xl bg-[#101725] border border-[#C6A76A]/30 flex items-center justify-center text-[#C6A76A] mb-3">
                <i data-lucide="gem" class="w-5 h-5"></i>
            </div>
            <h4 class="font-cinzel font-bold text-sm text-white">Donations &amp; Billing</h4>
            <p class="text-xs text-[#AAB3C1] mt-1">PayPal verification, donation package rewards, and Cash Point credits.</p>
        </div>

        <div class="prestige-card p-5">
            <div class="w-9 h-9 rounded-xl bg-[#101725] border border-rose-500/30 flex items-center justify-center text-rose-400 mb-3">
                <i data-lucide="bug" class="w-5 h-5"></i>
            </div>
            <h4 class="font-cinzel font-bold text-sm text-white">Bug Reports &amp; Abuse</h4>
            <p class="text-xs text-[#AAB3C1] mt-1">Client crashes, map glitches, rule violations, or suspicious bot behavior.</p>
        </div>
    </div>

    <!-- Recently Updated Tickets Table -->
    <div class="prestige-card overflow-hidden">
        <div class="p-5 border-b border-white/[0.08] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-[#C6A76A]"></i>
                <h3 class="font-cinzel text-xs font-bold text-white uppercase tracking-wider">Recently Updated Tickets</h3>
            </div>
            <a href="{{ route('support.my-tickets') }}" class="text-xs text-[#D8BE8A] hover:underline flex items-center gap-1 font-semibold">
                <span>View All Tickets</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-white/[0.08] bg-[#101725] text-[#788394] text-[11px] font-cinzel font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">Reference</th>
                        <th class="py-3 px-4">Subject</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4 text-center">Priority</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Last Activity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.06]">
                    @forelse($recentTickets as $ticket)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-[#D8BE8A] whitespace-nowrap">
                                <a href="{{ route('support.show', $ticket->reference) }}" class="hover:underline">{{ $ticket->reference }}</a>
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('support.show', $ticket->reference) }}" class="font-bold text-white hover:text-[#D8BE8A] transition-colors block truncate max-w-xs">{{ $ticket->subject }}</a>
                            </td>
                            <td class="py-3 px-4 text-[#D9DEE7] whitespace-nowrap">{{ $ticket->category_label }}</td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider {{ $ticket->priority_badge_class }}">{{ ucfirst($ticket->priority) }}</span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider {{ $ticket->status_badge_class }}">{{ $ticket->status_label }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-[#788394] whitespace-nowrap">
                                {{ $ticket->last_activity_at ? $ticket->last_activity_at->diffForHumans() : $ticket->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-[#788394]">You do not have any submitted tickets yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
