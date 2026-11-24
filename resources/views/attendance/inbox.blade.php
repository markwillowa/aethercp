@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                    Web Inventory Box
                </span>
            </div>
            <h1 class="text-2xl font-black text-white mt-1">Player Reward Inbox</h1>
            <p class="text-xs text-slate-400 mt-1">
                Safely store and claim rewards earned from Daily Attendance and server campaigns. Deposit items into your Kafra Storage whenever you are ready.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-700/60 transition-colors flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-4 h-4 text-cyan-400"></i>
                <span>Daily Rewards Calendar</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-400 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Inbox List -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl backdrop-blur-xl">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="package" class="w-4 h-4 text-cyan-400"></i>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Received Items &amp; Packages</h2>
            </div>
            <span class="text-xs text-slate-400 font-mono">{{ $items->total() }} Item{{ $items->total() === 1 ? '' : 's' }} Total</span>
        </div>

        @if($items->count() > 0)
            <div class="divide-y divide-slate-800/80">
                @foreach($items as $item)
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-950/40 transition-colors">
                        <div class="flex items-start sm:items-center gap-4">
                            <!-- Icon -->
                            <div class="w-12 h-12 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0 overflow-hidden shadow-inner">
                                @if(!empty($item->icon_url))
                                    <img src="{{ $item->icon_url }}" alt="{{ $item->item_name }}" class="w-8 h-8 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                @elseif($item->reward_type === 'zeny')
                                    <span class="text-amber-400 font-black text-sm">Z</span>
                                @elseif($item->reward_type === 'cashpoints')
                                    <span class="text-cyan-400 font-black text-sm">CP</span>
                                @else
                                    <i data-lucide="gift" class="w-5 h-5 text-slate-500"></i>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-bold text-white truncate">
                                        {{ $item->formatted_title }}
                                    </h3>
                                    @if($item->refine > 0)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            +{{ $item->refine }} Refined
                                        </span>
                                    @endif
                                    @if($item->bound > 0)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                            Account Bound
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $item->title ?? 'Daily Attendance Reward' }}
                                    @if(!empty($item->message))
                                        • <span class="italic text-slate-500">{{ $item->message }}</span>
                                    @endif
                                </p>
                                <p class="text-[11px] text-slate-500 font-mono mt-1">
                                    Received: {{ $item->created_at->format('M d, Y H:i') }}
                                    @if($item->claimed_at)
                                        • Deposited to Kafra: {{ $item->claimed_at->format('M d, Y H:i') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Action / Status -->
                        <div class="flex items-center gap-3 shrink-0 sm:self-center">
                            @if($item->status === 'unclaimed' || $item->status === 'pending')
                                <form method="POST" action="{{ route('inbox.claim-storage', $item->id) }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-lg shadow-cyan-500/20 flex items-center gap-1.5 transition-all active:scale-95">
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
                                <span class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-400">
                                    {{ ucfirst($item->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($items->hasPages())
                <div class="p-5 border-t border-slate-800">
                    {{ $items->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-3xl bg-slate-950 border border-slate-800 flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <i data-lucide="inbox" class="w-8 h-8 text-slate-600"></i>
                </div>
                <h3 class="text-base font-bold text-slate-300">Your Reward Inbox is Empty</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                    Log in every day and claim your rewards on the Daily Rewards Calendar to receive items here.
                </p>
                <div class="mt-5">
                    <a href="{{ route('attendance.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-colors inline-flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span>Go to Daily Rewards</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
