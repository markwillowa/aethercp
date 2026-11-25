@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- 1. Header Hero Card -->
    <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 backdrop-blur-md shadow-2xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <!-- Title & Bio -->
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-cyan-600 via-blue-600 to-indigo-600 flex items-center justify-center shrink-0 shadow-lg shadow-cyan-500/20 text-slate-950 font-bold border border-cyan-400/30">
                    <i data-lucide="archive" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                            Web-to-Game Clearinghouse
                        </span>
                        <span class="text-xs text-slate-600">•</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                            Safe In-Game Delivery
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-1">
                        Escrow Inbox
                    </h1>
                    <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">
                        Central holding vault for items and rewards. Items from <strong class="text-amber-300 font-semibold">Daily Attendance</strong>, <strong class="text-cyan-300 font-semibold">Marketplace purchases</strong>, <strong class="text-purple-300 font-semibold">Rankings</strong>, and <strong class="text-emerald-300 font-semibold">Donations</strong> are stored safely here until you transfer them to your game.
                    </p>
                </div>
            </div>

            <!-- Fast Action Navigation Buttons -->
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <a href="{{ route('marketplace.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-md shadow-cyan-500/20 transition-all flex items-center gap-2 active:scale-95">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                    <span>Browse Marketplace</span>
                </a>
                <a href="{{ route('attendance.index') }}" class="px-3.5 py-2.5 rounded-xl text-xs font-bold bg-slate-950/90 hover:bg-slate-800 text-amber-300 border border-amber-500/40 transition-all flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-amber-400"></i>
                    <span>Daily Rewards</span>
                </a>
                <a href="{{ route('rankings.index') }}" class="px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-slate-950/80 hover:bg-slate-800 text-slate-300 border border-slate-800 transition-colors flex items-center gap-1.5">
                    <i data-lucide="trophy" class="w-4 h-4 text-purple-400"></i>
                    <span>Rankings</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 shrink-0"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-400 shrink-0"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Source Filter Tabs & Status Tabs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Source Filters -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('escrow.index', ['source' => 'all', 'status' => $status]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2 {{ $source === 'all' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                <span>All Sources</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $source === 'all' ? 'bg-cyan-400/30 text-cyan-200' : 'bg-slate-800 text-slate-400' }}">{{ $counts['total'] }}</span>
            </a>
            <a href="{{ route('escrow.index', ['source' => 'attendance', 'status' => $status]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2 {{ $source === 'attendance' ? 'bg-amber-500/20 text-amber-300 border-amber-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-amber-400"></i>
                <span>Daily Rewards</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $source === 'attendance' ? 'bg-amber-400/30 text-amber-200' : 'bg-slate-800 text-slate-400' }}">{{ $counts['attendance'] }}</span>
            </a>
            <a href="{{ route('escrow.index', ['source' => 'marketplace', 'status' => $status]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2 {{ $source === 'marketplace' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                <i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-cyan-400"></i>
                <span>Marketplace</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $source === 'marketplace' ? 'bg-cyan-400/30 text-cyan-200' : 'bg-slate-800 text-slate-400' }}">{{ $counts['marketplace'] }}</span>
            </a>
            <a href="{{ route('escrow.index', ['source' => 'cash_shop', 'status' => $status]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2 {{ $source === 'cash_shop' ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                <i data-lucide="store" class="w-3.5 h-3.5 text-indigo-400"></i>
                <span>Cash Shop</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $source === 'cash_shop' ? 'bg-indigo-400/30 text-indigo-200' : 'bg-slate-800 text-slate-400' }}">{{ $counts['cash_shop'] ?? 0 }}</span>
            </a>
            <a href="{{ route('escrow.index', ['source' => 'ranking', 'status' => $status]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2 {{ $source === 'ranking' ? 'bg-purple-500/20 text-purple-300 border-purple-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                <i data-lucide="trophy" class="w-3.5 h-3.5 text-purple-400"></i>
                <span>Rankings</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $source === 'ranking' ? 'bg-purple-400/30 text-purple-200' : 'bg-slate-800 text-slate-400' }}">{{ $counts['ranking'] }}</span>
            </a>
            <a href="{{ route('escrow.index', ['source' => 'donation', 'status' => $status]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold border transition-all flex items-center gap-2 {{ $source === 'donation' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/50 shadow-sm' : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white' }}">
                <i data-lucide="heart" class="w-3.5 h-3.5 text-emerald-400"></i>
                <span>Donations</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $source === 'donation' ? 'bg-emerald-400/30 text-emerald-200' : 'bg-slate-800 text-slate-400' }}">{{ $counts['donation'] }}</span>
            </a>
        </div>

        <!-- Status Filter (Unclaimed vs Claimed) -->
        <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-slate-800 shrink-0">
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'unclaimed']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'unclaimed' ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 shadow-sm' : 'text-slate-400 hover:text-white' }}">
                Unclaimed ({{ $counts['unclaimed'] }})
            </a>
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'claimed']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'claimed' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white' }}">
                Claimed History ({{ $counts['claimed'] }})
            </a>
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'all']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'all' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                All
            </a>
        </div>
    </div>

    <!-- Escrow Items List -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl shadow-2xl backdrop-blur-xl">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between rounded-t-3xl bg-slate-950/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
                    <i data-lucide="inbox" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-white tracking-wide">
                        {{ $status === 'claimed' ? 'Claimed In-Game Transfer History' : 'Items In Escrow Ready For In-Game Delivery' }}
                    </h2>
                    <p class="text-[11px] text-slate-400">
                        {{ $status === 'claimed' ? 'Archived log of items successfully transferred to Storage or Character' : 'Select whether to deposit items to your Kafra Storage or directly to a Character inventory' }}
                    </p>
                </div>
            </div>
            <span class="text-xs font-mono px-3 py-1 rounded-full bg-slate-950 border border-slate-800 text-slate-300">
                <strong class="text-cyan-400">{{ $items->total() }}</strong> Item{{ $items->total() === 1 ? '' : 's' }}
            </span>
        </div>

        @if($items->count() > 0)
            <div class="divide-y divide-slate-800/80">
                @foreach($items as $item)
                    <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-5 hover:bg-slate-950/40 transition-colors">
                        <!-- Item Identity & Info -->
                        <div class="flex items-start sm:items-center gap-4 min-w-0">
                            <!-- Icon -->
                            <div class="w-14 h-14 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center shrink-0 overflow-hidden shadow-inner p-1">
                                @if(!empty($item->icon_url))
                                    <img src="{{ $item->icon_url }}" alt="{{ $item->item_name }}" class="w-9 h-9 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                @elseif($item->reward_type === 'zeny')
                                    <span class="text-amber-400 font-black text-base font-mono">Z</span>
                                @elseif($item->reward_type === 'cashpoints')
                                    <span class="text-cyan-400 font-black text-base font-mono">CP</span>
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
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 font-mono">
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
                                    <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-md shadow-cyan-500/20 flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                                        <i data-lucide="archive" class="w-3.5 h-3.5"></i>
                                        <span>Transfer to Storage</span>
                                    </button>
                                </form>

                                <!-- Option 2: Transfer directly to Character Inventory -->
                                @if(count($characters) > 0)
                                    <div x-data="{ openCharSelect: false }" class="relative" :class="{ 'z-50': openCharSelect, 'z-10': !openCharSelect }">
                                        <button type="button" @click="openCharSelect = !openCharSelect"
                                                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/40 flex items-center gap-1.5 transition-all cursor-pointer shadow-sm shadow-emerald-500/10 active:scale-95">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                                            <span>Transfer to Character</span>
                                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': openCharSelect }"></i>
                                        </button>

                                        <!-- Character Selection Dropdown -->
                                        <div x-show="openCharSelect" @click.away="openCharSelect = false"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                             style="display: none;"
                                             class="absolute right-0 top-full mt-2 w-80 bg-slate-950 border border-emerald-500/30 rounded-2xl shadow-2xl shadow-black/80 p-4 z-50 space-y-3 ring-1 ring-white/10">
                                            <div class="border-b border-slate-800/80 pb-2.5">
                                                <p class="text-xs font-bold text-white tracking-wide flex items-center gap-1.5">
                                                    <i data-lucide="user" class="w-3.5 h-3.5 text-emerald-400"></i>
                                                    <span>Select Receiving Character</span>
                                                </p>
                                                <p class="text-[10px] text-slate-400 mt-1">Character must be <strong class="text-amber-400 font-semibold">Offline</strong> to receive inventory items safely.</p>
                                            </div>
                                            <div class="max-h-56 overflow-y-auto space-y-2 pr-0.5 custom-scrollbar">
                                                @foreach($characters as $c)
                                                    <form method="POST" action="{{ route('escrow.claim-character', $item->id) }}">
                                                        @csrf
                                                        <input type="hidden" name="character_id" value="{{ $c->char_id }}">
                                                        <button type="submit"
                                                                class="w-full text-left p-2.5 rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-slate-800 hover:border-emerald-500/50 flex items-center justify-between gap-2 transition-all group cursor-pointer">
                                                            <div class="min-w-0">
                                                                <span class="text-xs font-bold text-white group-hover:text-emerald-300 block truncate">{{ $c->name }}</span>
                                                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ \App\Services\Emulator\JobHelper::getName($c->class) }} • Lv. {{ $c->base_level }}</span>
                                                            </div>
                                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase shrink-0 {{ (int) $c->online > 0 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' }}">
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
            <!-- Refined & Informative Empty State -->
            <div class="py-12 px-6 text-center relative overflow-hidden">
                <div class="relative z-10 max-w-xl mx-auto space-y-6">
                    <!-- Icon with Ambient Ring -->
                    <div class="relative flex items-center justify-center mx-auto w-20 h-20">
                        <div class="absolute inset-0 bg-cyan-500/15 rounded-3xl blur-xl animate-pulse"></div>
                        <div class="relative w-18 h-18 rounded-3xl bg-gradient-to-b from-slate-900 to-slate-950 border border-slate-700/80 shadow-2xl flex items-center justify-center">
                            <i data-lucide="inbox" class="w-9 h-9 text-cyan-400"></i>
                        </div>
                    </div>

                    <!-- Texts -->
                    <div class="space-y-2">
                        <h3 class="text-xl font-black text-white tracking-tight">
                            {{ $status === 'claimed' ? 'No Claimed History Found' : 'Your Escrow Inbox is Empty' }}
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed max-w-md mx-auto">
                            When you claim daily rewards, purchase items in the marketplace, climb PvP leaderboards, or contribute donations, all items arrive here safely until you transfer them to your game.
                        </p>
                    </div>

                    <!-- 4-Feature Informational Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left pt-2">
                        <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 shrink-0 mt-0.5">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">Daily Attendance</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Log in daily to claim consumable packs, refinement mats &amp; Zeny.</p>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 shrink-0 mt-0.5">
                                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">Player Marketplace</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Browse player consignments for weapons, cards &amp; gear with verified escrow.</p>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-400 shrink-0 mt-0.5">
                                <i data-lucide="trophy" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">PvP &amp; MvP Leaderboards</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Climb the seasonal rankings to earn monthly prize distributions.</p>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0 mt-0.5">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">Patronage &amp; Donations</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Receive bonus item packages and cash points straight to escrow.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-2 flex items-center justify-center gap-3 flex-wrap">
                        <a href="{{ route('marketplace.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-2 active:scale-95">
                            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                            <span>Browse Marketplace</span>
                        </a>
                        <a href="{{ route('attendance.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition-colors flex items-center gap-2">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            <span>Claim Daily Rewards</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- In-Game Delivery Explainer Footer -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 shrink-0 mt-0.5">
                <i data-lucide="archive" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Option A: Kafra Storage Delivery</h4>
                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                    Items are transferred immediately into your account's main Kafra Storage. Can be delivered whether your character is online or offline. Talk to any Kafra Employee in-game to access.
                </p>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0 mt-0.5">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Option B: Character Inventory Delivery</h4>
                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                    Items are inserted directly into your chosen character's inventory bag. The character must be logged off during delivery to avoid emulator memory rollbacks.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

