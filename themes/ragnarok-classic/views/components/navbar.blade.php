@php
    $modMgr = app(\App\Services\Module\ModuleManager::class);
@endphp
<nav x-data="{ mobileOpen: false, userDropdown: false }" class="relative z-30 shadow-md">
    <!-- Middle Brand Banner (Classic Ragnarok Medieval Crest) -->
    <div class="ro-brand-banner px-4 sm:px-6">
        <div class="ro-container flex flex-wrap items-center justify-between gap-4">
            <!-- Brand Crest & Server Titles -->
            <a href="{{ route('home') }}" class="ro-brand flex items-center no-underline group">
                <div class="ro-brand-crest shrink-0 mr-3">
                    <div class="ro-brand-crest-inner">
                        <i data-lucide="shield" class="w-6 h-6 text-[#FFE082] stroke-[#FFE082] fill-[#FFDA89]/40 drop-shadow"></i>
                    </div>
                </div>
                <div class="ro-brand-text">
                    <span class="ro-brand-title block">
                        {{ config('aether.name', 'AetherCP') }}
                    </span>
                    <span class="ro-brand-subtitle block">
                        {{ config('aether.server_name', 'Ragnarok Online') }} &bull; Rune-Midgard Saga
                    </span>
                </div>
            </a>

            <!-- Right: Quick Auth Action Buttons or Player Card -->
            <div class="flex items-center gap-2">
                @auth
                    <div class="relative" @click.away="userDropdown = false">
                        <button @click="userDropdown = !userDropdown" class="ro-btn-wood py-1.5 px-3">
                            <i data-lucide="user" class="w-4 h-4 text-[#FFDA89]"></i>
                            <span>{{ auth()->user()->username ?? auth()->user()->name }}</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#E6C280]"></i>
                        </button>

                        <div x-show="userDropdown" x-transition.opacity.duration.100ms style="display: none;" class="absolute right-0 mt-1 w-48 ro-panel z-50 divide-y divide-[#DECBB1] p-1 shadow-xl">
                            <div class="px-3 py-2 bg-[#FCF7ED]">
                                <p class="text-[10px] text-[#77644E] font-bold uppercase tracking-wider">Account ID</p>
                                <p class="text-xs font-mono font-bold text-[#3A2A1C]">#{{ auth()->user()->account_id }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('account.profile') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded transition-colors">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-[#315B88]"></i>
                                    <span>My Account</span>
                                </a>
                                @if($modMgr->isEnabled('character'))
                                <a href="{{ route('account.characters') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded transition-colors">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-[#315B88]"></i>
                                    <span>My Characters</span>
                                </a>
                                @endif
                                @if($modMgr->isEnabled('character_creation') && Route::has('account.characters.create'))
                                <a href="{{ route('account.characters.create') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded transition-colors">
                                    <i data-lucide="user-plus" class="w-3.5 h-3.5 text-[#315B88]"></i>
                                    <span>Create Character</span>
                                </a>
                                @endif
                                @if($modMgr->isEnabled('redeem_codes') && Route::has('account.redeem'))
                                <a href="{{ route('account.redeem') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded transition-colors">
                                    <i data-lucide="ticket" class="w-3.5 h-3.5 text-[#8C5E1E]"></i>
                                    <span>Redeem Code</span>
                                </a>
                                @endif
                                @if(Route::has('escrow.index'))
                                <a href="{{ route('escrow.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded transition-colors">
                                    <i data-lucide="inbox" class="w-3.5 h-3.5 text-[#315B88]"></i>
                                    <span>Escrow Inbox</span>
                                </a>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#8C5E1E] font-bold hover:bg-[#EBD6B3] rounded transition-colors">
                                        <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 text-[#8C5E1E]"></i>
                                        <span>Admin Control</span>
                                    </a>
                                @endif
                            </div>
                            <div class="pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 text-xs text-[#C62828] hover:bg-[#FDEEED] rounded font-semibold transition-colors">
                                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                        <span>Log Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="ro-btn-primary text-xs">
                        <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                        <span>Account Login</span>
                    </a>
                    <a href="{{ route('register') }}" class="ro-btn-gold text-xs">
                        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                        <span>Register Now</span>
                    </a>
                @endauth

                <!-- Mobile Hamburger Button (Only on Mobile) -->
                <button @click="mobileOpen = !mobileOpen" class="ro-mobile-only ro-btn-wood py-1.5 px-2.5">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Classic Wooden Menu Bar -->
    @php
        $rcWorldActive = request()->routeIs('rankings.*') || request()->routeIs('guilds.*') || request()->routeIs('characters.online') || request()->routeIs('woe.*') || request()->routeIs('itemdb.*') || request()->routeIs('database.*') || request()->routeIs('downloads.*');
        $rcTreasuryActive = request()->routeIs('cash-shop.*') || request()->routeIs('marketplace.*') || request()->routeIs('donations.*') || request()->routeIs('attendance.*');
        $rcCommunityActive = request()->routeIs('forum.*') || request()->routeIs('gallery.*') || request()->routeIs('faq.*') || request()->routeIs('support.*');
    @endphp
    <div class="ro-navbar">
        <div class="ro-container flex items-center justify-between">
            <!-- Desktop Links (Single-Row Grouped Dropdowns) -->
            <div class="ro-desktop-only items-center">
                <a href="{{ route('home') }}" class="ro-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                    <span>Home</span>
                </a>
                @if($modMgr->isEnabled('news'))
                <a href="{{ route('news.index') }}" class="ro-nav-item {{ request()->routeIs('news.*') ? 'active' : '' }}">
                    <i data-lucide="newspaper" class="w-3.5 h-3.5"></i>
                    <span>News</span>
                </a>
                @endif

                <!-- World Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" @click="open = !open" class="ro-nav-item cursor-pointer {{ $rcWorldActive ? 'active' : '' }}">
                        <i data-lucide="compass" class="w-3.5 h-3.5"></i>
                        <span>World</span>
                        <i data-lucide="chevron-down" class="w-3 h-3 opacity-75"></i>
                    </button>
                    <div x-show="open" x-transition.opacity style="display: none;" class="absolute left-0 top-full w-48 ro-panel z-50 p-1 shadow-xl">
                        @if($modMgr->isEnabled('rankings'))
                            <a href="{{ route('rankings.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="award" class="w-3.5 h-3.5 text-[#8C5E1E]"></i><span>Rankings</span></a>
                        @endif
                        @if($modMgr->isEnabled('guild'))
                            <a href="{{ route('guilds.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="shield" class="w-3.5 h-3.5 text-[#315B88]"></i><span>Guilds</span></a>
                        @endif
                        @if($modMgr->isEnabled('character'))
                            <a href="{{ route('characters.online') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="users" class="w-3.5 h-3.5 text-[#315B88]"></i><span>Online Players</span></a>
                        @endif
                        <a href="{{ route('woe.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="swords" class="w-3.5 h-3.5 text-[#8C5E1E]"></i><span>War of Emperium</span></a>
                        @if($modMgr->isEnabled('database'))
                            <a href="{{ route('itemdb.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="database" class="w-3.5 h-3.5 text-[#315B88]"></i><span>Database</span></a>
                        @endif
                        @if($modMgr->isEnabled('downloads'))
                            <a href="{{ route('downloads.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="download" class="w-3.5 h-3.5 text-[#8C5E1E]"></i><span>Downloads</span></a>
                        @endif
                    </div>
                </div>

                <!-- Treasury Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" @click="open = !open" class="ro-nav-item cursor-pointer {{ $rcTreasuryActive ? 'active' : '' }}">
                        <i data-lucide="coins" class="w-3.5 h-3.5"></i>
                        <span>Treasury</span>
                        <i data-lucide="chevron-down" class="w-3 h-3 opacity-75"></i>
                    </button>
                    <div x-show="open" x-transition.opacity style="display: none;" class="absolute left-0 top-full w-48 ro-panel z-50 p-1 shadow-xl">
                        @if($modMgr->isEnabled('cash_shop'))
                            <a href="{{ route('cash-shop.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="store" class="w-3.5 h-3.5 text-[#8C5E1E]"></i><span>Cash Shop</span></a>
                        @endif
                        @if($modMgr->isEnabled('marketplace'))
                            <a href="{{ route('marketplace.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-[#315B88]"></i><span>Marketplace</span></a>
                        @endif
                        @if($modMgr->isEnabled('donations'))
                            <a href="{{ route('donations.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="heart-handshake" class="w-3.5 h-3.5 text-[#8C5E1E]"></i><span>Donations</span></a>
                        @endif
                        @if($modMgr->isEnabled('attendance'))
                            <a href="{{ route('attendance.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="calendar" class="w-3.5 h-3.5 text-[#315B88]"></i><span>Daily Rewards</span></a>
                        @endif
                    </div>
                </div>

                <!-- Community Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" @click="open = !open" class="ro-nav-item cursor-pointer {{ $rcCommunityActive ? 'active' : '' }}">
                        <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                        <span>Community</span>
                        <i data-lucide="chevron-down" class="w-3 h-3 opacity-75"></i>
                    </button>
                    <div x-show="open" x-transition.opacity style="display: none;" class="absolute left-0 top-full w-48 ro-panel z-50 p-1 shadow-xl">
                        @if($modMgr->isEnabled('forum') && Route::has('forum.index'))
                            <a href="{{ route('forum.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="message-square" class="w-3.5 h-3.5 text-[#8C5E1E]"></i><span>Forum</span></a>
                        @endif
                        @if($modMgr->isEnabled('gallery') && Route::has('gallery.index'))
                            <a href="{{ route('gallery.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="image" class="w-3.5 h-3.5 text-[#315B88]"></i><span>Media Gallery</span></a>
                        @endif
                        @if($modMgr->isEnabled('faq') && Route::has('faq.index'))
                            <a href="{{ route('faq.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="help-circle" class="w-3.5 h-3.5 text-[#8C5E1E]"></i><span>FAQ &amp; Guides</span></a>
                        @endif
                        @if($modMgr->isEnabled('support') && Route::has('support.index'))
                            <a href="{{ route('support.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded"><i data-lucide="life-buoy" class="w-3.5 h-3.5 text-[#315B88]"></i><span>Support Center</span></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Navigation (Only on Mobile) -->
        <div x-show="mobileOpen" x-transition style="display: none;" class="ro-mobile-only flex-col bg-[#2D1D13] border-t border-[#65482D] py-2 px-4 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Home</a>
            @if($modMgr->isEnabled('news'))
            <a href="{{ route('news.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">News & Updates</a>
            @endif
            @if($modMgr->isEnabled('downloads'))
            <a href="{{ route('downloads.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Game Client Downloads</a>
            @endif
            @if($modMgr->isEnabled('rankings'))
            <a href="{{ route('rankings.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Player Rankings</a>
            @endif
            @if($modMgr->isEnabled('character'))
            <a href="{{ route('characters.online') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Online Adventurers</a>
            @endif
            @if($modMgr->isEnabled('guild'))
            <a href="{{ route('guilds.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Guild Roster</a>
            @endif
            <a href="{{ route('woe.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">War of Emperium</a>
            @if($modMgr->isEnabled('database'))
            <a href="{{ route('itemdb.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Item Compendium</a>
            @endif
            @if($modMgr->isEnabled('cash_shop'))
            <a href="{{ route('cash-shop.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#FFDA89] hover:bg-[#3D281A] rounded">Cash Shop Mall</a>
            @endif
            @if($modMgr->isEnabled('donations'))
            <a href="{{ route('donations.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Support & Donations</a>
            @endif
        </div>
    </div>
</nav>

