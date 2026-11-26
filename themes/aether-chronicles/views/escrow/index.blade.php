@extends('layouts.app')

@section('content')
<div class="chronicles-container py-10 space-y-6">
    <!-- Wood Board Header -->
    <div class="chronicles-wood-board flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#D4AF37]/20 text-[#FFE4A0] border border-[#D4AF37]/40">Web-to-Game Clearinghouse</span>
            <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase mt-1">Escrow Inbox</h1>
            <p class="text-xs text-[#EAD2B0] mt-1">Central holding vault for items and Zeny from Redeem Codes, Daily Rewards, Marketplace, Cash Shop, and Rankings.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('marketplace.index') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto"><i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-[#FFD700]"></i><span>Marketplace</span></a>
            <a href="{{ route('attendance.index') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto"><i data-lucide="calendar" class="w-3.5 h-3.5 text-[#FFD700]"></i><span>Daily Rewards</span></a>
            <a href="{{ route('account.redeem.index') }}" class="chronicles-btn-wood text-xs py-2 px-4 w-auto"><i data-lucide="ticket" class="w-3.5 h-3.5 text-[#FFD700]"></i><span>Redeem Code</span></a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2 text-xs">
            @foreach([
                'all' => ['label' => 'All Sources', 'count' => $counts['total']],
                'attendance' => ['label' => 'Daily Rewards', 'count' => $counts['attendance']],
                'marketplace' => ['label' => 'Marketplace', 'count' => $counts['marketplace']],
                'cash_shop' => ['label' => 'Cash Shop', 'count' => $counts['cash_shop'] ?? 0],
                'ranking' => ['label' => 'Rankings', 'count' => $counts['ranking']],
                'donation' => ['label' => 'Donations', 'count' => $counts['donation']],
            ] as $sKey => $sMeta)
                <a href="{{ route('escrow.index', ['source' => $sKey, 'status' => $status]) }}"
                   class="px-3 py-1.5 rounded-xl font-bold border transition-colors inline-flex items-center gap-1.5 {{ $source === $sKey ? 'bg-[#4A2200] text-[#FFE4A0] border-[#D4AF37]' : 'bg-[#FAF4EA] text-[#5A3D28] border-[#DECBB1] hover:bg-[#EFE4D0]' }}">
                    <span>{{ $sMeta['label'] }}</span>
                    <span class="font-mono text-[10px] opacity-80">({{ $sMeta['count'] }})</span>
                </a>
            @endforeach
        </div>

        <div class="inline-flex rounded-xl bg-[#FAF4EA] border border-[#DECBB1] p-1 text-xs font-bold self-start">
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'unclaimed']) }}" class="px-3 py-1 rounded-lg {{ $status === 'unclaimed' ? 'bg-[#4A2200] text-[#FFE4A0]' : 'text-[#5A3D28]' }}">Unclaimed ({{ $counts['unclaimed'] }})</a>
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'claimed']) }}" class="px-3 py-1 rounded-lg {{ $status === 'claimed' ? 'bg-[#4A2200] text-[#FFE4A0]' : 'text-[#5A3D28]' }}">Claimed ({{ $counts['claimed'] }})</a>
            <a href="{{ route('escrow.index', ['source' => $source, 'status' => 'all']) }}" class="px-3 py-1 rounded-lg {{ $status === 'all' ? 'bg-[#4A2200] text-[#FFE4A0]' : 'text-[#5A3D28]' }}">All</a>
        </div>
    </div>

    <!-- Escrow Items List -->
    <div class="chronicles-panel-ivy p-0 overflow-hidden">
        <div class="px-5 py-3 bg-[#4A2200] border-b border-[#8B5A2B] flex items-center justify-between">
            <span class="font-cinzel font-bold text-[#FFE4A0] text-sm">Items In Escrow</span>
            <span class="text-xs font-mono text-[#FFE4A0]">{{ $items->total() }} {{ \Illuminate\Support\Str::plural('Item', $items->total()) }}</span>
        </div>

        <div class="divide-y divide-[#DECBB1]">
            @forelse($items as $item)
                <div class="p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 hover:bg-[#FAF4EA]/60 transition-colors">
                    <div class="flex items-start gap-4 min-w-0">
                        <div class="w-12 h-12 rounded-xl bg-white border border-[#DECBB1] flex items-center justify-center shrink-0 shadow-sm">
                            @if(!empty($item->icon_url))
                                <img src="{{ $item->icon_url }}" alt="{{ $item->item_name }}" class="w-8 h-8 object-contain" onerror="this.src='/images/item-placeholder.png'">
                            @elseif($item->reward_type === 'zeny')
                                <span class="font-black text-[#9E5600] text-base">Z</span>
                            @else
                                <i data-lucide="package" class="w-5 h-5 text-[#9E5600]"></i>
                            @endif
                        </div>
                        <div class="min-w-0 space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-cinzel font-bold text-sm text-[#4A2200]">
                                    @if($item->reward_type === 'zeny')
                                        {{ number_format($item->zeny_amount) }} Zeny
                                    @else
                                        @if(($item->refine ?? 0) > 0)<span class="text-[#D4441D]">+{{ $item->refine }}</span> @endif
                                        {{ $item->item_name ?? "Item #{$item->item_id}" }}
                                        @if(($item->quantity ?? 1) > 1)<span class="text-[#9E5600]">x{{ $item->quantity }}</span>@endif
                                    @endif
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#EFE4D0] text-[#5A3D28] border border-[#DECBB1]">{{ $item->source_type }}</span>
                            </div>
                            <p class="text-xs text-[#795E43]">{{ $item->title }} @if($item->message) • {{ $item->message }} @endif</p>
                            <p class="text-[10px] text-[#8C7A68]">Deposited: {{ $item->created_at?->format('M d, Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        @if($item->status === 'unclaimed')
                            @if($item->reward_type !== 'zeny')
                                <form method="POST" action="{{ route('escrow.claim-storage', $item->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-[#FAF4EA] hover:bg-[#EFE4D0] text-[#4A2200] border border-[#DECBB1] cursor-pointer">
                                        Transfer to Storage
                                    </button>
                                </form>
                            @endif

                            @if(count($characters) > 0)
                                <div x-data="{ openCharSelect: false }" class="inline-flex">
                                    <button type="button" @click="openCharSelect = true" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#1E4620] hover:bg-[#275A2A] text-[#D8FFB8] border border-[#4CAF50] cursor-pointer inline-flex items-center gap-1.5">
                                        <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                                        <span>Transfer to Character</span>
                                    </button>
                                    <template x-teleport="body">
                                        <div x-show="openCharSelect" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm" @keydown.escape.window="openCharSelect = false">
                                            <div @click.away="openCharSelect = false" class="w-full max-w-md bg-[#FAF4EA] border-2 border-[#8B5A2B] rounded-2xl shadow-2xl p-5 space-y-4">
                                                <div class="flex items-start justify-between border-b border-[#DECBB1] pb-3">
                                                    <div>
                                                        <p class="font-cinzel text-sm font-bold text-[#4A2200]">Select Receiving Character</p>
                                                        <p class="text-xs text-[#795E43] mt-0.5">Character must be <strong>Offline</strong> to receive items or Zeny safely.</p>
                                                    </div>
                                                    <button type="button" @click="openCharSelect = false" class="text-[#795E43] hover:text-[#4A2200] font-bold cursor-pointer">✕</button>
                                                </div>
                                                <div class="max-h-72 overflow-y-auto space-y-2">
                                                    @foreach($characters as $c)
                                                        <form method="POST" action="{{ route('escrow.claim-character', $item->id) }}">
                                                            @csrf
                                                            <input type="hidden" name="character_id" value="{{ $c->char_id }}">
                                                            <button type="submit" class="w-full text-left p-3 rounded-xl bg-white hover:bg-[#EFE4D0] border border-[#DECBB1] flex items-center justify-between gap-3 cursor-pointer">
                                                                <div>
                                                                    <span class="font-bold text-[#4A2200] block">{{ $c->name }}</span>
                                                                    <span class="text-xs text-[#795E43]">{{ \App\Services\Emulator\JobHelper::getName($c->class) }} • Lv. {{ $c->base_level }}</span>
                                                                </div>
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ (int) $c->online > 0 ? 'bg-[#FFF4E5] text-[#9E5600]' : 'bg-[#E8F5E9] text-[#1E4620]' }}">
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
                        @else
                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-[#E8F5E9] text-[#1E4620] border border-[#4CAF50]/40">
                                Claimed {{ $item->claimed_at?->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-xs text-[#8C7A68]">No escrow items matched your current filter.</div>
            @endforelse
        </div>
    </div>

    @if($items->hasPages())
        <div>{{ $items->links() }}</div>
    @endif
</div>
@endsection
