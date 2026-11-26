@extends('layouts.app')

@section('content')
@php
    $payonMod = app(\App\Services\Module\ModuleManager::class);
    $heroAsset = route('theme.asset', ['theme' => 'aether-payon', 'path' => 'images/hero-payon.jpg']);
    $mapOnline = (bool) data_get($serverStatus ?? [], 'map.online', false);
    $onlineCount = (int) data_get($serverStatus ?? [], 'online_count', 0);
@endphp

<!-- ======================================================================
     A. ATMOSPHERIC PAYON HERO SECTION
     ====================================================================== -->
<section class="relative overflow-hidden border-b-4 border-[#493322]">
    <img src="{{ $heroAsset }}" alt="Payon Mountain Village" class="absolute inset-0 w-full h-full object-cover object-center">
    <div class="absolute inset-0 bg-gradient-to-r from-[#263D2C]/90 via-[#263D2C]/65 to-[#263D2C]/25"></div>

    <div class="payon-container relative z-10 py-14 sm:py-20">
        <div class="max-w-xl payon-wood-frame p-6 sm:p-8 space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-[#263D2C] text-[#FFF8E8] text-[10px] font-bold uppercase tracking-widest border border-[#A88950]">
                    <i data-lucide="trees" class="w-3.5 h-3.5 text-[#A88950]"></i>
                    <span>Payon Mountain Kingdom</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-[11px] font-mono font-bold {{ $mapOnline ? 'bg-[#526D3A]/20 text-[#263D2C] border border-[#526D3A]' : 'bg-[#493322]/15 text-[#493322] border border-[#80633B]' }}">
                    <span class="w-2 h-2 rounded-full {{ $mapOnline ? 'bg-[#526D3A]' : 'bg-[#8C2D2D]' }}"></span>
                    <span>{{ $mapOnline ? 'REALM ONLINE' : 'REALM OFFLINE' }} &bull; {{ number_format($onlineCount) }} Online</span>
                </span>
            </div>

            <h1 class="font-cinzel text-3xl sm:text-4xl font-extrabold text-[#35291F] leading-tight">
                {{ config('aether.server_name', 'AetherCP') }}
            </h1>

            <p class="text-sm text-[#493322] leading-relaxed">
                Enter an ancient eastern realm of misty bamboo groves, handcrafted wooden sanctuaries, and timeless Ragnarok Online adventure.
            </p>

            <div class="flex flex-wrap items-center gap-3 pt-1">
                @if($payonMod->isEnabled('downloads'))
                    <a href="{{ route('downloads.index') }}" class="payon-btn-forest py-2.5 px-5">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Download Client</span>
                    </a>
                @endif
                @guest
                    <a href="{{ route('register') }}" class="payon-btn-gold py-2.5 px-5">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Create Account</span>
                    </a>
                @else
                    <a href="{{ route('account.characters') }}" class="payon-btn-gold py-2.5 px-5">
                        <i data-lucide="swords" class="w-4 h-4"></i>
                        <span>My Characters</span>
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>

<!-- ======================================================================
     B. WOODEN PLAQUE SERVER INFORMATION STRIP
     ====================================================================== -->
<section class="bg-[#E7D8B5] border-b border-[#A88950]">
    <div class="payon-container py-5">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach(['login' => 'Login Gate', 'char' => 'Char Gate', 'map' => 'Map Realm'] as $sKey => $sLabel)
                @php $st = data_get($serverStatus ?? [], $sKey, ['online' => false, 'latency_ms' => 0]); @endphp
                <div class="payon-parchment-panel px-3.5 py-2.5 flex items-center justify-between gap-2">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#80633B] block">{{ $sLabel }}</span>
                        <span class="text-xs font-mono font-bold {{ !empty($st['online']) ? 'text-[#263D2C]' : 'text-[#8C2D2D]' }}">
                            {{ !empty($st['online']) ? 'ONLINE' : 'OFFLINE' }}
                        </span>
                    </div>
                    <span class="text-[10px] font-mono text-[#80633B]">{{ $st['latency_ms'] ?? 0 }}ms</span>
                </div>
            @endforeach

            <div class="payon-parchment-panel px-3.5 py-2.5">
                <span class="text-[10px] uppercase font-bold text-[#80633B] block">Online Players</span>
                <span class="text-sm font-mono font-extrabold text-[#263D2C]">{{ number_format($onlineCount) }}</span>
            </div>

            <div class="payon-parchment-panel px-3.5 py-2.5">
                <span class="text-[10px] uppercase font-bold text-[#80633B] block">EXP Rates (B / J)</span>
                <span class="text-sm font-mono font-extrabold text-[#35291F]">{{ config('aether.rates.base') }} / {{ config('aether.rates.job') }}</span>
            </div>

            <div class="payon-parchment-panel px-3.5 py-2.5">
                <span class="text-[10px] uppercase font-bold text-[#80633B] block">Drop / Mode</span>
                <span class="text-sm font-mono font-extrabold text-[#35291F]">{{ config('aether.rates.drop') }} &bull; {{ config('emulator.renewal') ? 'RE' : 'Pre-RE' }}</span>
            </div>
        </div>
    </div>
</section>

<div class="payon-container py-10 space-y-12">

    <!-- ======================================================================
         C. ASYMMETRIC EDITORIAL SECTION: KINGDOM NOTICE BOARD + SCROLL PORTAL
         ====================================================================== -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left 7 Cols: Kingdom Notice Board -->
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-[#526D3A]">Official Village Dispatches</span>
                    <h2 class="font-cinzel text-2xl font-bold text-[#35291F]">Kingdom Notice Board</h2>
                </div>
                @if($payonMod->isEnabled('news'))
                    <a href="{{ route('news.index') }}" class="payon-btn-wood text-[11px] py-1.5 px-3">All Notices</a>
                @endif
            </div>

            <div class="payon-bamboo-divider"><span class="payon-bamboo-knot"></span></div>

            @if($payonMod->isEnabled('news'))
                <div class="space-y-3">
                    @forelse($news as $article)
                        <article class="payon-notice-card">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <div class="flex items-center gap-2">
                                    @if($article->is_pinned)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#493322] text-[#FFF8E8]">Pinned</span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#E7D8B5] text-[#263D2C] border border-[#A88950]">
                                        {{ ucfirst($article->category) }}
                                    </span>
                                </div>
                                <span class="text-[11px] font-mono text-[#80633B]">{{ $article->published_at?->format('M d, Y') ?? 'Recent' }}</span>
                            </div>
                            <h3 class="font-cinzel text-base font-bold text-[#35291F]">
                                <a href="{{ route('news.show', $article->slug) }}" class="hover:text-[#526D3A] transition-colors">{{ $article->title }}</a>
                            </h3>
                            <p class="text-xs text-[#493322] mt-1 line-clamp-2 leading-relaxed">
                                {{ $article->summary ?: strip_tags($article->content) }}
                            </p>
                        </article>
                    @empty
                        <div class="payon-parchment-panel p-8 text-center text-xs text-[#80633B]">
                            No notices have been posted on the village board yet.
                        </div>
                    @endforelse
                </div>
            @endif
        </div>

        <!-- Right 5 Cols: Ancient Scroll Adventurer Portal -->
        <div class="lg:col-span-5 space-y-5">
            <div class="payon-scroll-panel p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-[#A88950]/40 pb-3">
                    <h3 class="font-cinzel text-base font-bold text-[#263D2C] uppercase tracking-wider">Adventurer Scroll</h3>
                    <i data-lucide="scroll-text" class="w-5 h-5 text-[#A88950]"></i>
                </div>

                @auth
                    <div class="p-3.5 rounded bg-[#E7D8B5] border border-[#A88950] flex items-center justify-between">
                        <div>
                            <p class="font-cinzel font-bold text-sm text-[#35291F]">{{ auth()->user()->username ?? auth()->user()->name }}</p>
                            <p class="text-[11px] font-mono text-[#80633B]">Account #{{ auth()->user()->account_id }}</p>
                        </div>
                        <a href="{{ route('account.profile') }}" class="payon-btn-forest text-[10px] py-1 px-2.5">Profile</a>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        @if($payonMod->isEnabled('character'))
                            <a href="{{ route('account.characters') }}" class="payon-parchment-panel p-2.5 font-semibold hover:bg-[#E7D8B5] flex items-center gap-2"><i data-lucide="users" class="w-3.5 h-3.5 text-[#526D3A]"></i><span>Characters</span></a>
                        @endif
                        @if($payonMod->isEnabled('attendance'))
                            <a href="{{ route('attendance.index') }}" class="payon-parchment-panel p-2.5 font-semibold hover:bg-[#E7D8B5] flex items-center gap-2"><i data-lucide="calendar" class="w-3.5 h-3.5 text-[#A88950]"></i><span>Daily Rewards</span></a>
                        @endif
                        @if($payonMod->isEnabled('redeem_codes') && Route::has('account.redeem'))
                            <a href="{{ route('account.redeem') }}" class="payon-parchment-panel p-2.5 font-semibold hover:bg-[#E7D8B5] flex items-center gap-2"><i data-lucide="ticket" class="w-3.5 h-3.5 text-[#80633B]"></i><span>Redeem Code</span></a>
                        @endif
                        <a href="{{ route('escrow.index') }}" class="payon-parchment-panel p-2.5 font-semibold hover:bg-[#E7D8B5] flex items-center gap-2"><i data-lucide="inbox" class="w-3.5 h-3.5 text-[#263D2C]"></i><span>Escrow Inbox</span></a>
                    </div>
                @else
                    <p class="text-xs text-[#493322] leading-relaxed">
                        Inscribe your name in the Payon ledger to forge characters, claim daily attendance treasures, and trade in the bazaar.
                    </p>
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ route('login') }}" class="payon-btn-forest w-full">Sign In</a>
                        <a href="{{ route('register') }}" class="payon-btn-gold w-full">Register</a>
                    </div>
                @endauth
            </div>

            @if(config('aether.community.discord'))
                <div class="payon-wood-frame p-5 bg-[#263D2C] text-[#F2E8CF] space-y-2.5" style="background-color: #263D2C; color: #F2E8CF;">
                    <h3 class="font-cinzel text-sm font-bold text-[#FFF8E8] flex items-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4 text-[#A88950]"></i>
                        <span>Payon Tavern Discord</span>
                    </h3>
                    <p class="text-xs text-[#E7D8B5]">Join fellow archers, monks, and guild leaders in our village tavern.</p>
                    <a href="{{ config('aether.community.discord') }}" target="_blank" rel="noopener" class="payon-btn-gold w-full">Join Tavern Discord</a>
                </div>
            @endif
        </div>
    </section>

    <!-- ======================================================================
         D. PAYON RANKING HALL (WOODEN LEADERBOARD SHOWCASE)
         ====================================================================== -->
    @if($payonMod->isEnabled('rankings'))
    <section class="payon-ranking-board">
        <div class="payon-wood-header">
            <div class="flex items-center gap-2">
                <i data-lucide="trophy" class="w-4 h-4 text-[#A88950]"></i>
                <span class="font-cinzel uppercase tracking-wider text-sm">Payon Ranking Hall &mdash; Top Adventurers</span>
            </div>
            <a href="{{ route('rankings.index') }}" class="text-xs text-[#E7D8B5] hover:text-[#FFF8E8] font-semibold">View Full Hall &rarr;</a>
        </div>

        <div class="p-5 space-y-4">
            @php $topList = collect($topPlayers ?? []); @endphp
            @if($topList->count() > 0)
                <!-- Top 3 Podium Plaques -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($topList->take(3) as $idx => $player)
                        @php
                            $pCharId = data_get($player, 'char_id');
                            $pName = data_get($player, 'name', 'Adventurer');
                            $pBase = data_get($player, 'base_level', 1);
                            $pJob = data_get($player, 'job_level', 1);
                            $pClass = data_get($player, 'class_name') ?? (\App\Services\Emulator\JobHelper::getName(data_get($player, 'class', 0)) ?: 'Novice');
                            $badgeClass = $idx === 0 ? 'payon-rank-badge-1' : ($idx === 1 ? 'payon-rank-badge-2' : 'payon-rank-badge-3');
                        @endphp
                        <div class="payon-reward-card p-4 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-9 h-9 rounded-full {{ $badgeClass }} flex items-center justify-center font-cinzel font-black text-sm shrink-0">
                                    #{{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    @if($pCharId)
                                        <a href="{{ route('characters.show', $pCharId) }}" class="font-cinzel font-bold text-sm text-[#35291F] hover:text-[#526D3A] truncate block">{{ $pName }}</a>
                                    @else
                                        <span class="font-cinzel font-bold text-sm text-[#35291F] truncate block">{{ $pName }}</span>
                                    @endif
                                    <span class="text-[11px] text-[#80633B] block">{{ $pClass }}</span>
                                </div>
                            </div>
                            <span class="font-mono text-xs font-bold text-[#263D2C] shrink-0">Lv. {{ $pBase }}/{{ $pJob }}</span>
                        </div>
                    @endforeach
                </div>

                @if($topList->count() > 3)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                        @foreach($topList->slice(3) as $idx => $player)
                            <div class="payon-parchment-panel px-4 py-2.5 flex items-center justify-between text-xs">
                                <span class="font-bold text-[#35291F]">#{{ $idx + 1 }} &bull; {{ data_get($player, 'name', 'Adventurer') }}</span>
                                <span class="font-mono text-[#526D3A] font-bold">Lv. {{ data_get($player, 'base_level', 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <p class="text-xs text-center text-[#80633B] py-4">No adventurers have been inscribed in the Ranking Hall yet.</p>
            @endif
        </div>
    </section>
    @endif

    <!-- ======================================================================
         E. REALM SCROLLS & TREASURES (HORIZONTAL FEATURE STRIP)
         ====================================================================== -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @if($payonMod->isEnabled('downloads'))
            <a href="{{ route('downloads.index') }}" class="payon-parchment-panel p-5 flex items-start gap-4 group">
                <div class="w-11 h-11 rounded bg-[#263D2C] text-[#FFF8E8] flex items-center justify-center shrink-0">
                    <i data-lucide="download" class="w-5 h-5 text-[#A88950]"></i>
                </div>
                <div>
                    <h3 class="font-cinzel text-sm font-bold text-[#35291F] group-hover:text-[#526D3A]">Client &amp; Patch Scrolls</h3>
                    <p class="text-xs text-[#80633B] mt-1">Download the full game client, patches, and setup guides.</p>
                </div>
            </a>
        @endif

        @if($payonMod->isEnabled('database'))
            <a href="{{ route('itemdb.index') }}" class="payon-parchment-panel p-5 flex items-start gap-4 group">
                <div class="w-11 h-11 rounded bg-[#493322] text-[#FFF8E8] flex items-center justify-center shrink-0">
                    <i data-lucide="book-open" class="w-5 h-5 text-[#A88950]"></i>
                </div>
                <div>
                    <h3 class="font-cinzel text-sm font-bold text-[#35291F] group-hover:text-[#526D3A]">Ancient Compendium</h3>
                    <p class="text-xs text-[#80633B] mt-1">Browse items, monster drops, cards, and Payon hunting grounds.</p>
                </div>
            </a>
        @endif

        @if($payonMod->isEnabled('cash_shop'))
            <a href="{{ route('cash-shop.index') }}" class="payon-parchment-panel p-5 flex items-start gap-4 group">
                <div class="w-11 h-11 rounded bg-[#526D3A] text-[#FFF8E8] flex items-center justify-center shrink-0">
                    <i data-lucide="gem" class="w-5 h-5 text-[#FFF8E8]"></i>
                </div>
                <div>
                    <h3 class="font-cinzel text-sm font-bold text-[#35291F] group-hover:text-[#526D3A]">Royal Cash Shop</h3>
                    <p class="text-xs text-[#80633B] mt-1">Acquire supplies, refinement chests, and rare cosmetics.</p>
                </div>
            </a>
        @endif
    </section>

</div>
@endsection
