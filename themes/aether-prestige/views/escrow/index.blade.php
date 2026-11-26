@extends('layouts.app')

@section('content')
<div class="prestige-container py-10 space-y-8">
    <!-- News & Marketplace Style Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-white/[0.08] pb-6">
        <div>
            <h1 class="font-cinzel text-2xl sm:text-3xl font-extrabold text-[#F4F5F7] tracking-wider uppercase">
                Escrow Inbox
            </h1>
            <p class="text-xs text-[#788394] mt-1">
                Central holding vault for items and rewards from Daily Attendance, Marketplace purchases, Rankings, and Donations.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('marketplace.index') }}" class="h-9 px-4 rounded-xl bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] font-cinzel font-bold text-xs uppercase tracking-wider inline-flex items-center gap-1.5">
                <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                <span>Browse Marketplace</span>
            </a>
            <a href="{{ route('attendance.index') }}" class="h-9 px-3.5 rounded-xl bg-[#151B26] border border-[#C6A76A]/30 text-[#D8BE8A] text-xs font-bold inline-flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                <span>Daily Rewards</span>
            </a>
            <a href="{{ route('rankings.index') }}" class="h-9 px-3.5 rounded-xl bg-[#151B26] border border-white/[0.08] text-[#AAB3C1] hover:text-white text-xs font-bold inline-flex items-center gap-1.5">
                <i data-lucide="trophy" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                <span>Rankings</span>
            </a>
        </div>
    </div>

    <!-- Source Filter Pills & Status Tabs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('escrow.index', ['source' => 'all', 'status' => $status]) }}"
               class="h-9 px-3.5 rounded-xl text-xs font-bold border inline-flex items-center gap-2 transition-colors {{ $source === 'all' ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border-[#C6A76A]/50' : 'bg-[#151B26] text-[#AAB3C1] border-white/[0.08] hover:text-white' }}">
                <span>All Sources</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-[#101725] text-[#D8BE8A]">{{ $counts['total'] }}</span>
            </a>
            <a href="{{ route('escrow.index', ['source' => 'attendance', 'status' => $status]) }}"
               class="h-9 px-3.5 rounded-xl text-xs font-bold border inline-flex items-center gap-2 transition-colors {{ $source === 'attendance' ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border-[#C6A76A]/50' : 'bg-[#151B26] text-[#AAB3C1] border-white/[0.08] hover:text-white' }}">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                <span>Daily Rewards</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-[#101725] text-[#D8BE8A]">{{ $counts['attendance'] }}</span>
            </a>
            <a href="{{ route('escrow.index', ['source' => 'marketplace', 'status' => $status]) }}"
               class="h-9 px-3.5 rounded-xl text-xs font-bold border inline-flex items-center gap-2 transition-colors {{ $source === 'marketplace' ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border-[#C6A76A]/50' : 'bg-[#151B26] text-[#AAB3C1] border-white/[0.08] hover:text-white' }}">
                <i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                <span>Marketplace</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-[#101725] text-[#D8BE8A]">{{ $counts['marketplace'] }}</span>
            </a>
            <a href="{{ route('escrow.index', ['source' => 'cash_shop', 'status' => $status]) }}"
               class="h-9 px-3.5 rounded-xl text-xs font-bold border inline-flex items-center gap-2 transition-colors {{ $source === 'cash_shop' ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border-[#C6A76A]/50' : 'bg-[#151B26] text-[#AAB3C1] border-white/[0.08] hover:text-white' }}">
                <i data-lucide="store" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                <span>Cash Shop</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-[#101725] text-[#D8BE8A]">{{ $counts['cash_shop'] ?? 0 }}</span>
            </a>
            <a href="{{ route('escrow.index', ['source' => 'ranking', 'status' => $status]) }}"
               class="h-9 px-3.5 rounded-xl text-xs font-bold border inline-flex items-center gap-2 transition-colors {{ $source === 'ranking' ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border-[#C6A76A]/50' : 'bg-[#151B26] text-[#AAB3C1] border-white/[0.08] hover:text-white' }}">
                <i data-lucide="trophy" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                <span>Rankings</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-[#101725] text-[#D8BE8A]">{{ $counts['ranking'] }}</span>
            </a>
            <a href="{{ route('escrow.index', ['source' => 'donation', 'status' => $status]) }}"
               class="h-9 px-3.5 rounded-xl text-xs font-bold border inline-flex items-center gap-2 transition-colors {{ $source === 'donation' ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border-[#C6A76A]/50' : 'bg-[#151B26] text-[#AAB3C1] border-white/[0.08] hover:text-white' }}">
                <i data-lucide="heart" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                <span>Donations</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-[#101725] text-[#D8BE8A]">{{ $counts['donation'] }}</span>
            </a>
        </div>

        <div class="h-9 flex items-center gap-1 bg-[#151B26] p-1 rounded-xl border border-white/[0.08] shrink-0">
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'unclaimed']) }}"
               class="h-7 px-3 rounded-lg text-xs font-bold inline-flex items-center transition-all {{ $status === 'unclaimed' ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/40' : 'text-[#AAB3C1] hover:text-white' }}">
                Unclaimed ({{ $counts['unclaimed'] }})
            </a>
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'claimed']) }}"
               class="h-7 px-3 rounded-lg text-xs font-bold inline-flex items-center transition-all {{ $status === 'claimed' ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/40' : 'text-[#AAB3C1] hover:text-white' }}">
                Claimed History ({{ $counts['claimed'] }})
            </a>
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'all']) }}"
               class="h-7 px-2.5 rounded-lg text-xs font-bold inline-flex items-center transition-all {{ $status === 'all' ? 'bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/40' : 'text-[#AAB3C1] hover:text-white' }}">
                All
            </a>
        </div>
    </div>

    <!-- Escrow Items List -->
    <div class="prestige-card overflow-hidden">
        <div class="p-5 border-b border-white/[0.08] flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#C6A76A]/15 border border-[#C6A76A]/30 flex items-center justify-center text-[#C6A76A]">
                    <i data-lucide="inbox" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="font-cinzel text-sm font-bold text-white uppercase tracking-wide">
                        {{ $status === 'claimed' ? 'Claimed In-Game Transfer History' : 'Items In Escrow Ready For In-Game Delivery' }}
                    </h2>
                    <p class="text-[11px] text-[#788394]">
                        {{ $status === 'claimed' ? 'Archived log of items successfully transferred to Storage or Character' : 'Select whether to deposit items to your Kafra Storage or directly to a Character inventory' }}
                    </p>
                </div>
            </div>
            <span class="text-xs font-mono px-3 py-1 rounded-full bg-[#101725] border border-white/10 text-[#D9DEE7]">
                <strong class="text-[#D8BE8A]">{{ $items->total() }}</strong> Item{{ $items->total() === 1 ? '' : 's' }}
            </span>
        </div>

        @if($items->count() > 0)
            <div class="divide-y divide-white/[0.06]">
                @foreach($items as $item)
                    <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-5 hover:bg-white/[0.02] transition-colors">
                        <div class="flex items-start sm:items-center gap-4 min-w-0">
                            <div class="w-14 h-14 rounded-2xl bg-[#101725] border border-white/10 flex items-center justify-center shrink-0 overflow-hidden p-1">
                                @if(!empty($item->icon_url))
                                    <img src="{{ $item->icon_url }}" alt="{{ $item->item_name }}" class="w-9 h-9 object-contain" onerror="this.src='/images/item-placeholder.png'">
                                @elseif($item->reward_type === 'zeny')
                                    <span class="text-[#D8BE8A] font-black text-base font-mono">Z</span>
                                @elseif($item->reward_type === 'cashpoints')
                                    <span class="text-[#D8BE8A] font-black text-base font-mono">CP</span>
                                @else
                                    <i data-lucide="package" class="w-6 h-6 text-[#C6A76A]"></i>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-cinzel text-sm sm:text-base font-bold text-white truncate">
                                        {{ $item->formatted_title }}
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-[#101725] text-[#D8BE8A] border border-[#C6A76A]/30">
                                        {{ $item->source_label }}
                                    </span>
                                    @if($item->refine > 0)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#C6A76A]/20 text-[#D8BE8A] border border-[#C6A76A]/30 font-mono">
                                            +{{ $item->refine }} Refined
                                        </span>
                                    @endif
                                    @if($item->bound > 0)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-white/5 text-[#AAB3C1] border border-white/10">
                                            Account Bound
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-[#D9DEE7] mt-1">
                                    {{ $item->title }}
                                    @if(!empty($item->message))
                                        • <span class="text-[#788394] italic">{{ $item->message }}</span>
                                    @endif
                                </p>
                                <p class="text-[11px] text-[#788394] font-mono mt-1">
                                    Deposited: {{ $item->created_at->format('M d, Y H:i') }}
                                    @if($item->claimed_at)
                                        • <span class="text-emerald-400">Transferred: {{ $item->claimed_at->format('M d, Y H:i') }} to {{ $item->claimed_destination_label }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-2.5 flex-wrap">
                            @if($item->status === 'unclaimed')
                                @if($item->reward_type === 'item')
                                    <form method="POST" action="{{ route('escrow.claim-storage', $item->id) }}" class="inline-flex m-0 p-0">
                                        @csrf
                                        <button type="submit" class="h-9 px-4 rounded-xl text-xs font-cinzel font-bold uppercase tracking-wider bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] inline-flex items-center justify-center gap-1.5 cursor-pointer">
                                            <i data-lucide="archive" class="w-3.5 h-3.5"></i>
                                            <span>Transfer to Storage</span>
                                        </button>
                                    </form>
                                @endif

                                @if(count($characters) > 0)
                                    <div x-data="{ openCharSelect: false }" class="relative inline-flex" :class="{ 'z-50': openCharSelect, 'z-10': !openCharSelect }">
                                        <button type="button" @click="openCharSelect = !openCharSelect"
                                                class="h-9 px-4 rounded-xl text-xs font-bold bg-[#101725] hover:border-[#C6A76A]/50 text-[#D8BE8A] border border-[#C6A76A]/35 inline-flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-[#C6A76A]"></i>
                                            <span>Transfer to Character</span>
                                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': openCharSelect }"></i>
                                        </button>

                                        <template x-teleport="body">
                                            <div x-show="openCharSelect" x-cloak
                                                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
                                                 @keydown.escape.window="openCharSelect = false">
                                                <div @click.away="openCharSelect = false" class="prestige-card w-full max-w-md p-5 space-y-4">
                                                    <div class="flex items-start justify-between gap-3 border-b border-white/[0.08] pb-3">
                                                        <div>
                                                            <p class="font-cinzel text-sm font-bold text-white uppercase tracking-wide flex items-center gap-2">
                                                                <i data-lucide="user-check" class="w-4 h-4 text-[#C6A76A]"></i>
                                                                <span>Select Receiving Character</span>
                                                            </p>
                                                            <p class="text-xs text-[#788394] mt-1">Character must be <strong class="text-[#D8BE8A] font-semibold">Offline</strong> to receive items or Zeny safely.</p>
                                                        </div>
                                                        <button type="button" @click="openCharSelect = false" class="p-1.5 rounded-lg text-[#788394] hover:text-white cursor-pointer">
                                                            <i data-lucide="x" class="w-4 h-4"></i>
                                                        </button>
                                                    </div>
                                                    <div class="max-h-72 overflow-y-auto space-y-2 pr-1">
                                                        @foreach($characters as $c)
                                                            <form method="POST" action="{{ route('escrow.claim-character', $item->id) }}">
                                                                @csrf
                                                                <input type="hidden" name="character_id" value="{{ $c->char_id }}">
                                                                <button type="submit" class="w-full text-left p-3 rounded-xl bg-[#101725] hover:border-[#C6A76A]/50 border border-white/10 flex items-center justify-between gap-3 transition-all group cursor-pointer">
                                                                    <div class="min-w-0">
                                                                        <span class="text-sm font-bold text-white group-hover:text-[#D8BE8A] block truncate">{{ $c->name }}</span>
                                                                        <span class="text-xs text-[#788394] block mt-0.5">{{ \App\Services\Emulator\JobHelper::getName($c->class) }} • Lv. {{ $c->base_level }}</span>
                                                                    </div>
                                                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase shrink-0 {{ (int) $c->online > 0 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' }}">
                                                                        {{ (int) $c->online > 0 ? 'Online' : 'Ready' }}
                                                                    </span>
                                                                </button>
                                                            </form>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                @endif
                            @elseif($item->status === 'claimed')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-500/30">
                                    <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                                    <span>Delivered in-game ({{ $item->claimed_destination_label }})</span>
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
                <div class="p-5 border-t border-white/[0.08]">{{ $items->links() }}</div>
            @endif
        @else
            <div class="py-12 px-6 text-center space-y-6 max-w-xl mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-[#101725] border border-[#C6A76A]/30 flex items-center justify-center mx-auto text-[#C6A76A]">
                    <i data-lucide="inbox" class="w-8 h-8"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="font-cinzel text-xl font-bold text-white uppercase">
                        {{ $status === 'claimed' ? 'No Claimed History Found' : 'Your Escrow Inbox is Empty' }}
                    </h3>
                    <p class="text-xs text-[#788394] leading-relaxed max-w-md mx-auto">
                        When you purchase items in the Cash Shop, trade in the Marketplace, claim daily rewards, or contribute donations, all items arrive here safely until you transfer them to your game.
                    </p>
                </div>
                <div class="pt-2 flex items-center justify-center gap-3 flex-wrap">
                    @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('cash_shop'))
                        <a href="{{ route('cash-shop.index') }}" class="h-10 px-5 rounded-xl text-xs font-cinzel font-bold uppercase tracking-wider bg-gradient-to-r from-[#D8BE8A] to-[#C6A76A] text-[#101725] inline-flex items-center gap-2">Cash Shop</a>
                    @endif
                    <a href="{{ route('marketplace.index') }}" class="h-10 px-5 rounded-xl text-xs font-bold bg-[#101725] border border-white/10 text-[#D8BE8A] hover:text-white inline-flex items-center gap-2">Marketplace</a>
                    <a href="{{ route('attendance.index') }}" class="h-10 px-5 rounded-xl text-xs font-bold bg-[#101725] border border-white/10 text-[#AAB3C1] hover:text-white inline-flex items-center gap-2">Daily Rewards</a>
                </div>
            </div>
        @endif
    </div>

    <!-- In-Game Delivery Explainer Footer -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="prestige-card p-5 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-[#101725] border border-[#C6A76A]/30 flex items-center justify-center text-[#C6A76A] shrink-0 mt-0.5">
                <i data-lucide="archive" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-cinzel text-xs font-bold text-white uppercase tracking-wider">Option A: Kafra Storage Delivery</h4>
                <p class="text-[11px] text-[#788394] mt-1 leading-relaxed">
                    Items are transferred immediately into your account's main Kafra Storage. Can be delivered whether your character is online or offline. Talk to any Kafra Employee in-game to access.
                </p>
            </div>
        </div>

        <div class="prestige-card p-5 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-[#101725] border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0 mt-0.5">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-cinzel text-xs font-bold text-white uppercase tracking-wider">Option B: Character Inventory Delivery</h4>
                <p class="text-[11px] text-[#788394] mt-1 leading-relaxed">
                    Items are inserted directly into your chosen character's inventory bag. The character must be logged off during delivery to avoid emulator memory rollbacks.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
