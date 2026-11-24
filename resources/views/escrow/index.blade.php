@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                    Central Web Clearinghouse
                </span>
                <span class="text-xs text-slate-500">•</span>
                <span class="text-xs text-amber-400 font-medium">Safe In-Game Delivery</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white mt-1">Escrow Inbox</h1>
            <p class="text-xs text-slate-400 mt-1 max-w-2xl">
                All items and rewards from <strong class="text-amber-400 font-semibold">Daily Attendance</strong>, <strong class="text-cyan-400 font-semibold">Marketplace purchases</strong>, <strong class="text-purple-400 font-semibold">Ranking rewards</strong>, and <strong class="text-emerald-400 font-semibold">Donations</strong> are held securely here. Transfer items to Kafra Storage or directly to your Character whenever you are ready.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-700/60 transition-colors flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-4 h-4 text-amber-400"></i>
                <span>Daily Rewards</span>
            </a>
            <a href="{{ route('marketplace.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-700/60 transition-colors flex items-center gap-1.5">
                <i data-lucide="shopping-bag" class="w-4 h-4 text-cyan-400"></i>
                <span>Marketplace</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-400 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Source Filter Tabs & Status Tabs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Source Filters -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('escrow.index', ['source' => 'all', 'status' => $status]) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all {{ $source === 'all' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                All Sources ({{ $counts['total'] }})
            </a>
            <a href="{{ route('escrow.index', ['source' => 'attendance', 'status' => $status]) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all {{ $source === 'attendance' ? 'bg-amber-500/20 text-amber-300 border-amber-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                Daily Rewards ({{ $counts['attendance'] }})
            </a>
            <a href="{{ route('escrow.index', ['source' => 'marketplace', 'status' => $status]) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all {{ $source === 'marketplace' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                Marketplace ({{ $counts['marketplace'] }})
            </a>
            <a href="{{ route('escrow.index', ['source' => 'ranking', 'status' => $status]) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all {{ $source === 'ranking' ? 'bg-purple-500/20 text-purple-300 border-purple-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                Rankings ({{ $counts['ranking'] }})
            </a>
            <a href="{{ route('escrow.index', ['source' => 'donation', 'status' => $status]) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all {{ $source === 'donation' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                Donations ({{ $counts['donation'] }})
            </a>
        </div>

        <!-- Status Filter (Unclaimed vs Claimed) -->
        <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-slate-800 shrink-0">
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'unclaimed']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'unclaimed' ? 'bg-cyan-500 text-slate-950' : 'text-slate-400 hover:text-white' }}">
                Unclaimed ({{ $counts['unclaimed'] }})
            </a>
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'claimed']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'claimed' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                Claimed History ({{ $counts['claimed'] }})
            </a>
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'all']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $status === 'all' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                All
            </a>
        </div>
    </div>

    <!-- Escrow Items List -->
    <div class="bg-slate-900/70 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl backdrop-blur-xl">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="inbox" class="w-4 h-4 text-cyan-400"></i>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">
                    {{ $status === 'claimed' ? 'Claimed Transfer History' : 'Items In Escrow Ready For In-Game Delivery' }}
                </h2>
            </div>
            <span class="text-xs text-slate-400 font-mono">{{ $items->total() }} Item{{ $items->total() === 1 ? '' : 's' }}</span>
        </div>

        @if($items->count() > 0)
            <div class="divide-y divide-slate-800/80">
                @foreach($items as $item)
                    <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-5 hover:bg-slate-950/40 transition-colors">
                        <!-- Item Identity & Info -->
                        <div class="flex items-start sm:items-center gap-4 min-w-0">
                            <!-- Icon -->
                            <div class="w-14 h-14 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0 overflow-hidden shadow-inner">
                                @if(!empty($item->icon_url))
                                    <img src="{{ $item->icon_url }}" alt="{{ $item->item_name }}" class="w-9 h-9 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                @elseif($item->reward_type === 'zeny')
                                    <span class="text-amber-400 font-black text-base">Z</span>
                                @elseif($item->reward_type === 'cashpoints')
                                    <span class="text-cyan-400 font-black text-base">CP</span>
                                @else
                                    <i data-lucide="package" class="w-6 h-6 text-slate-500"></i>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm sm:text-base font-extrabold text-white truncate">
                                        {{ $item->formatted_title }}
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider border {{ $item->source_badge_color }}">
                                        {{ $item->source_label }}
                                    </span>
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
                                <p class="text-xs text-slate-300 mt-1">
                                    {{ $item->title }}
                                    @if(!empty($item->message))
                                        • <span class="text-slate-400 italic">{{ $item->message }}</span>
                                    @endif
                                </p>
                                <p class="text-[11px] text-slate-500 font-mono mt-1">
                                    Deposited: {{ $item->created_at->format('M d, Y H:i') }}
                                    @if($item->claimed_at)
                                        • <span class="text-emerald-400">Transferred: {{ $item->claimed_at->format('M d, Y H:i') }} to {{ $item->claimed_destination_label }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Actions / Transfer Controls -->
                        <div class="shrink-0 flex items-center gap-3 flex-wrap">
                            @if($item->status === 'unclaimed')
                                <!-- Option 1: Transfer to Kafra Storage -->
                                <form method="POST" action="{{ route('escrow.claim-storage', $item->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-md shadow-cyan-500/20 flex items-center gap-1.5 transition-all active:scale-95">
                                        <i data-lucide="archive" class="w-3.5 h-3.5"></i>
                                        <span>Transfer to Storage</span>
                                    </button>
                                </form>

                                <!-- Option 2: Transfer directly to Character Inventory -->
                                @if(count($characters) > 0)
                                    <div x-data="{ openCharSelect: false }" class="relative">
                                        <button type="button" @click="openCharSelect = !openCharSelect"
                                                class="px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 flex items-center gap-1.5 transition-all">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                                            <span>Transfer to Character</span>
                                            <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                                        </button>

                                        <!-- Character Selection Dropdown -->
                                        <div x-show="openCharSelect" @click.away="openCharSelect = false"
                                             x-transition.opacity
                                             style="display: none;"
                                             class="absolute right-0 mt-2 w-72 bg-slate-950 border border-slate-800 rounded-2xl shadow-2xl p-3 z-50 space-y-2">
                                            <div class="border-b border-slate-800 pb-2">
                                                <p class="text-xs font-bold text-white">Select Character</p>
                                                <p class="text-[10px] text-slate-400">Character must be <strong class="text-amber-400">Offline</strong> to receive inventory items.</p>
                                            </div>
                                            <div class="max-h-56 overflow-y-auto space-y-1.5">
                                                @foreach($characters as $c)
                                                    <form method="POST" action="{{ route('escrow.claim-character', $item->id) }}">
                                                        @csrf
                                                        <input type="hidden" name="character_id" value="{{ $c->char_id }}">
                                                        <button type="submit"
                                                                class="w-full text-left p-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800/80 hover:border-emerald-500/40 flex items-center justify-between gap-2 transition-all group">
                                                            <div class="min-w-0">
                                                                <span class="text-xs font-bold text-white group-hover:text-emerald-300 block truncate">{{ $c->name }}</span>
                                                                <span class="text-[10px] text-slate-400 block">{{ \App\Services\Emulator\JobHelper::getName($c->class) }} • Lv. {{ $c->base_level }}</span>
                                                            </div>
                                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase shrink-0 {{ (int) $c->online > 0 ? 'bg-amber-500/20 text-amber-300' : 'bg-emerald-500/20 text-emerald-400' }}">
                                                                {{ (int) $c->online > 0 ? 'Online' : 'Ready' }}
                                                            </span>
                                                        </button>
                                                    </form>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @elseif($item->status === 'claimed')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-500/30">
                                    <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                                    <span>Delivered in-game ({{ $item->claimed_destination_label }})</span>
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
                <h3 class="text-base font-bold text-slate-300">
                    {{ $status === 'claimed' ? 'No Claimed History Found' : 'Your Escrow Inbox is Empty' }}
                </h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                    When you claim Daily Rewards, purchase items in the Marketplace, earn Ranking recognition, or contribute via Donations, all items arrive here safely until you transfer them to your game.
                </p>
                <div class="mt-5 flex items-center justify-center gap-3">
                    <a href="{{ route('attendance.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition-colors inline-flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span>Daily Rewards</span>
                    </a>
                    <a href="{{ route('marketplace.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 transition-colors inline-flex items-center gap-1.5">
                        <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                        <span>Marketplace</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
