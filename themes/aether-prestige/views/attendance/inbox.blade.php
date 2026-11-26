@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <!-- Cash Shop / News & Media Style Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Player Reward Inbox
            </h1>
            <p class="text-xs text-[#788394] mt-1">
                Safely store and claim rewards earned from Daily Attendance and server campaigns into your Kafra Storage.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('attendance.index') }}" class="h-9 px-4 rounded-xl bg-[#151B26] border border-[#C6A76A]/30 text-xs font-bold text-[#D8BE8A] hover:text-white transition-colors inline-flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-4 h-4 text-[#C6A76A]"></i>
                <span>Daily Rewards Calendar</span>
            </a>
        </div>
    </div>

    <!-- Inbox List -->
    <div class="prestige-card overflow-hidden">
        <div class="p-5 border-b border-white/[0.08] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="package" class="w-4 h-4 text-[#C6A76A]"></i>
                <h2 class="font-cinzel text-sm font-bold text-white uppercase tracking-wider">Received Items &amp; Packages</h2>
            </div>
            <span class="text-xs text-[#788394] font-mono">{{ $items->total() }} Item{{ $items->total() === 1 ? '' : 's' }} Total</span>
        </div>

        @if($items->count() > 0)
            <div class="divide-y divide-white/[0.06]">
                @foreach($items as $item)
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-white/[0.02] transition-colors">
                        <div class="flex items-start sm:items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-[#101725] border border-white/10 flex items-center justify-center shrink-0 overflow-hidden">
                                @if(!empty($item->icon_url))
                                    <img src="{{ $item->icon_url }}" alt="{{ $item->item_name }}" class="w-8 h-8 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                @elseif($item->reward_type === 'zeny')
                                    <span class="text-[#D8BE8A] font-black text-sm">Z</span>
                                @elseif($item->reward_type === 'cashpoints')
                                    <span class="text-[#D8BE8A] font-black text-sm">CP</span>
                                @else
                                    <i data-lucide="gift" class="w-5 h-5 text-[#C6A76A]"></i>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-cinzel text-sm font-bold text-white truncate">
                                        {{ $item->formatted_title }}
                                    </h3>
                                    @if($item->refine > 0)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/30">
                                            +{{ $item->refine }} Refined
                                        </span>
                                    @endif
                                    @if($item->bound > 0)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-white/5 text-[#AAB3C1] border border-white/10">
                                            Account Bound
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-[#AAB3C1] mt-0.5">
                                    {{ $item->title ?? 'Daily Attendance Reward' }}
                                    @if(!empty($item->message))
                                        • <span class="italic text-[#788394]">{{ $item->message }}</span>
                                    @endif
                                </p>
                                <p class="text-[11px] text-[#788394] font-mono mt-1">
                                    Received: {{ $item->created_at->format('M d, Y H:i') }}
                                    @if($item->claimed_at)
                                        • Deposited to Kafra: {{ $item->claimed_at->format('M d, Y H:i') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0 sm:self-center">
                            @if($item->status === 'unclaimed' || $item->status === 'pending')
                                <form method="POST" action="{{ route('inbox.claim-storage', $item->id) }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-cinzel font-bold uppercase tracking-wider bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] flex items-center gap-1.5 cursor-pointer">
                                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                        <span>Claim to Kafra Storage</span>
                                    </button>
                                </form>
                            @elseif($item->status === 'claimed')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-500/30">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span>In Kafra Storage</span>
                                </span>
                            @else
                                <span class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-[#101725] text-[#AAB3C1] border border-white/10">
                                    {{ ucfirst($item->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($items->hasPages())
                <div class="p-5 border-t border-white/[0.08]">
                    {{ $items->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-[#101725] border border-white/10 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="inbox" class="w-8 h-8 text-[#788394]"></i>
                </div>
                <h3 class="font-cinzel text-base font-bold text-white uppercase">Your Reward Inbox is Empty</h3>
                <p class="text-xs text-[#788394] max-w-sm mx-auto mt-1">
                    Log in every day and claim your rewards on the Daily Rewards Calendar to receive items here.
                </p>
                <div class="mt-5">
                    <a href="{{ route('attendance.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-cinzel font-bold uppercase tracking-wider bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] inline-flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span>Go to Daily Rewards</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
