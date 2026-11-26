@php
    $cModMgr = app(\App\Services\Module\ModuleManager::class);
@endphp
<nav class="chronicles-nav" style="overflow: visible !important; z-index: 100;">
    <div class="chronicles-nav-links" style="overflow: visible !important;">
        <!-- HOME -->
        <a href="{{ route('home') }}" class="chronicles-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>HOME</span>
        </a>

        <!-- INFORMATION (with Click & Hover Dropdown) -->
        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.away="open = false" style="position: relative; z-index: 150;">
            <button type="button" @click="open = !open" class="chronicles-nav-item flex items-center gap-1 cursor-pointer" :class="open ? 'active' : ''">
                <i data-lucide="info" class="w-3.5 h-3.5"></i>
                <span>INFORMATION</span>
                <i data-lucide="chevron-down" class="w-3 h-3 text-[#A88B57] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 transform -translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 transform -translate-y-2 scale-95"
                 class="absolute top-full left-0 mt-1.5 w-52 bg-[#1B1322] border-2 border-[#D4AF37]/60 rounded-lg shadow-2xl p-1.5"
                 style="z-index: 99999; box-shadow: 0 10px 25px rgba(0,0,0,0.9); display: none;">
                @if($cModMgr->isEnabled('downloads'))
                    <a href="{{ route('downloads.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="download" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Client Downloads</span>
                    </a>
                @endif
                @if($cModMgr->isEnabled('forum'))
                    <a href="{{ route('forum.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="messages-square" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Community</span>
                    </a>
                @endif
                @if($cModMgr->isEnabled('support'))
                    <a href="{{ route('support.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="life-buoy" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Support Center</span>
                    </a>
                @endif
                @if($cModMgr->isEnabled('gallery'))
                    <a href="{{ route('gallery.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="image" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Media &amp; Screenshots</span>
                    </a>
                @endif
                @if($cModMgr->isEnabled('character'))
                    <a href="{{ route('characters.online') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="users" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Active Adventurers</span>
                    </a>
                @endif
                @if($cModMgr->isEnabled('news'))
                    <a href="{{ route('news.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="scroll" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Server Chronicles</span>
                    </a>
                @endif
                @if($cModMgr->isEnabled('rankings'))
                    <a href="{{ route('rankings.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="trophy" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Hall of Fame</span>
                    </a>
                @endif
                <a href="{{ route('woe.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="swords" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>War of Emperium</span>
                </a>
                @if($cModMgr->isEnabled('faq'))
                    <a href="{{ route('faq.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="help-circle" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>FAQ &amp; Guides</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- DATABASE (with Item, Monster, Map, Card Dropdown) -->
        @if($cModMgr->isEnabled('database'))
        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.away="open = false" style="position: relative; z-index: 150;">
            <button type="button" @click="open = !open" class="chronicles-nav-item flex items-center gap-1 cursor-pointer {{ request()->routeIs('itemdb.*') || request()->routeIs('database.*') ? 'active' : '' }}" :class="open ? 'active' : ''">
                <i data-lucide="database" class="w-3.5 h-3.5"></i>
                <span>DATABASE</span>
                <i data-lucide="chevron-down" class="w-3 h-3 text-[#A88B57] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 transform -translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 transform -translate-y-2 scale-95"
                 class="absolute top-full left-0 mt-1.5 w-56 bg-[#1B1322] border-2 border-[#D4AF37]/60 rounded-lg shadow-2xl p-1.5"
                 style="z-index: 99999; box-shadow: 0 10px 25px rgba(0,0,0,0.9); display: none;">
                <a href="{{ route('itemdb.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="sword" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>Item Database</span>
                </a>
                <a href="{{ route('database.monsters') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="skull" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>Monster Bestiary</span>
                </a>
                <a href="{{ route('database.maps') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="map" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>Maps &amp; Dungeons</span>
                </a>
                <a href="{{ route('itemdb.index', ['type' => 'Card']) }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="layers" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>Monster Cards</span>
                </a>
            </div>
        </div>
        @endif

        <!-- GUILDS -->
        @if($cModMgr->isEnabled('guild'))
            <a href="{{ route('guilds.index') }}" class="chronicles-nav-item {{ request()->routeIs('guilds.*') ? 'active' : '' }}">
                <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                <span>GUILDS</span>
            </a>
        @endif

        <!-- MARKETPLACE -->
        @if($cModMgr->isEnabled('marketplace'))
            <a href="{{ route('marketplace.index') }}" class="chronicles-nav-item {{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
                <i data-lucide="scale" class="w-3.5 h-3.5"></i>
                <span>MARKETPLACE</span>
            </a>
        @endif

        <!-- CASH SHOP -->
        @if($cModMgr->isEnabled('cash_shop'))
            <a href="{{ route('cash-shop.index') }}" class="chronicles-nav-item {{ request()->routeIs('cash-shop.*') ? 'active' : '' }}">
                <i data-lucide="store" class="w-3.5 h-3.5"></i>
                <span>CASH SHOP</span>
            </a>
        @endif

        <!-- DONATE -->
        @if($cModMgr->isEnabled('donations'))
            <a href="{{ route('donations.index') }}" class="chronicles-nav-item {{ request()->routeIs('donations.*') ? 'active' : '' }}">
                <i data-lucide="gem" class="w-3.5 h-3.5"></i>
                <span>DONATE</span>
            </a>
        @endif

        <!-- ACCOUNT & SERVICES DROPDOWN -->
        @auth
        <div class="relative" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false" style="position: relative; z-index: 150;">
            <button type="button" @click="open = !open" class="chronicles-nav-item flex items-center gap-1 cursor-pointer {{ request()->routeIs('account.*') || request()->routeIs('escrow.*') || request()->routeIs('attendance.*') ? 'active' : '' }}" :class="open ? 'active' : ''">
                <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                <span>ACCOUNT</span>
                <i data-lucide="chevron-down" class="w-3 h-3 text-[#A88B57] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open"
                 class="absolute top-full right-0 pt-1 w-56"
                 style="z-index: 99999; display: none;">
                <div class="bg-[#1B1322] border-2 border-[#D4AF37]/60 rounded-lg shadow-2xl p-1.5" style="box-shadow: 0 10px 25px rgba(0,0,0,0.9);">
                <a href="{{ route('account.profile') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="user" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>My Account</span>
                </a>
                <a href="{{ route('account.characters') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="sword" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>My Characters</span>
                </a>
                @if($cModMgr->isEnabled('character_creation'))
                    <a href="{{ route('account.characters.create') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="user-plus" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Create Character</span>
                    </a>
                @endif
                @if($cModMgr->isEnabled('attendance'))
                    <a href="{{ route('attendance.index') }}" class="block px-3 py-2 text-xs text-[#FFD700] font-bold font-cinzel hover:bg-[#352643] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                        <span>Daily Rewards</span>
                    </a>
                @endif
                <a href="{{ route('escrow.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="inbox" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>Escrow Inbox</span>
                </a>
                @if($cModMgr->isEnabled('redeem_codes'))
                    <a href="{{ route('account.redeem.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="ticket" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Redeem Code</span>
                    </a>
                @endif
                @if($cModMgr->isEnabled('cash_shop'))
                    <a href="{{ route('cash-shop.history') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="receipt" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Cash Shop History</span>
                    </a>
                @endif
                @if(auth()->user()->isStaff())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-xs text-[#FF8A80] font-bold font-cinzel hover:bg-[#352643] rounded transition-colors flex items-center gap-2 border-t border-[#3E324A] mt-1 pt-2">
                        <i data-lucide="shield-alert" class="w-3.5 h-3.5"></i>
                        <span>Admin Panel</span>
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="border-t border-[#3E324A] mt-1 pt-1">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-xs text-[#C8BDAA] hover:text-white font-bold font-cinzel hover:bg-[#352643] rounded transition-colors flex items-center gap-2 cursor-pointer">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span>Logout</span>
                    </button>
                </form>
                </div>
            </div>
        </div>
        @endauth
    </div>

    <!-- Far Right: Prominent Ornate Gold "Play Now" CTA Button -->
    <div class="py-1 pl-2 shrink-0">
        @if($cModMgr->isEnabled('downloads'))
            <a href="{{ route('downloads.index') }}" class="chronicles-btn-playnow">
                <span>PLAY NOW</span>
                <span class="text-sm">✦</span>
            </a>
        @else
            @guest
                <a href="{{ route('register') }}" class="chronicles-btn-playnow">
                    <span>PLAY NOW</span>
                    <span class="text-sm">✦</span>
                </a>
            @endguest
        @endif
    </div>
</nav>
