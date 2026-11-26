@php
    $modMgr = app(\App\Services\Module\ModuleManager::class);
    $worldActive = request()->routeIs('news.*') || request()->routeIs('rankings.*') || request()->routeIs('guilds.*') || request()->routeIs('characters.online') || request()->routeIs('woe.*') || request()->routeIs('itemdb.*') || request()->routeIs('database.*') || request()->routeIs('downloads.*');
    $treasuryActive = request()->routeIs('cash-shop.*') || request()->routeIs('marketplace.*') || request()->routeIs('donations.*') || request()->routeIs('attendance.*');
    $communityActive = request()->routeIs('forum.*') || request()->routeIs('gallery.*') || request()->routeIs('faq.*') || request()->routeIs('support.*');
@endphp
<nav x-data="{ mobileOpen: false, userDropdown: false }" class="payon-nav-signboard sticky top-0 z-50">
    <div class="payon-container">
        <div class="flex items-center justify-between h-16 gap-4">
            <!-- Payon Kingdom Crest & Brand -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 group">
                <div class="w-10 h-10 rounded-md bg-[#493322] border border-[#A88950] flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6 text-[#FFF8E8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M3 9l9-5 9 5" stroke="#A88950" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5 9v10h14V9" stroke="#F2E8CF"/>
                        <path d="M9 19v-5h6v5" stroke="#A88950"/>
                        <path d="M12 4v2" stroke="#A88950"/>
                    </svg>
                </div>
                <div>
                    <span class="font-cinzel text-base sm:text-lg font-extrabold tracking-wider text-[#FFF8E8] group-hover:text-[#E7D8B5] transition-colors block leading-tight">
                        {{ config('aether.name', 'AetherCP') }}
                    </span>
                    <span class="text-[10px] uppercase tracking-widest text-[#A8B08B] font-semibold block">
                        {{ config('aether.server_name', 'Payon') }} &bull; Eastern Kingdom
                    </span>
                </div>
            </a>

            <!-- Desktop Signboard Links -->
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}" class="payon-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i data-lucide="home" class="w-3.5 h-3.5 text-[#A88950]"></i>
                    <span>Sanctuary</span>
                </a>

                <!-- Realm Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" @click="open = !open" class="payon-nav-link cursor-pointer {{ $worldActive ? 'active' : '' }}">
                        <i data-lucide="compass" class="w-3.5 h-3.5 text-[#A88950]"></i>
                        <span>Kingdom</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-80"></i>
                    </button>
                    <div x-show="open" x-transition.opacity style="display: none;" class="absolute left-0 mt-1 w-52 payon-wood-frame p-1.5 z-50">
                        @if($modMgr->isEnabled('news'))
                            <a href="{{ route('news.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="scroll-text" class="w-3.5 h-3.5 text-[#526D3A]"></i><span>Kingdom Notices</span></a>
                        @endif
                        @if($modMgr->isEnabled('rankings'))
                            <a href="{{ route('rankings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="trophy" class="w-3.5 h-3.5 text-[#A88950]"></i><span>Ranking Hall</span></a>
                        @endif
                        @if($modMgr->isEnabled('guild'))
                            <a href="{{ route('guilds.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="shield" class="w-3.5 h-3.5 text-[#263D2C]"></i><span>Clans &amp; Guilds</span></a>
                        @endif
                        @if($modMgr->isEnabled('character'))
                            <a href="{{ route('characters.online') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="users" class="w-3.5 h-3.5 text-[#526D3A]"></i><span>Online Warriors</span></a>
                        @endif
                        <a href="{{ route('woe.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="swords" class="w-3.5 h-3.5 text-[#80633B]"></i><span>War of Emperium</span></a>
                        @if($modMgr->isEnabled('database'))
                            <a href="{{ route('itemdb.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="book-open" class="w-3.5 h-3.5 text-[#526D3A]"></i><span>Compendium</span></a>
                        @endif
                        @if($modMgr->isEnabled('downloads'))
                            <a href="{{ route('downloads.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="download" class="w-3.5 h-3.5 text-[#263D2C]"></i><span>Client Scrolls</span></a>
                        @endif
                    </div>
                </div>

                <!-- Treasury Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" @click="open = !open" class="payon-nav-link cursor-pointer {{ $treasuryActive ? 'active' : '' }}">
                        <i data-lucide="coins" class="w-3.5 h-3.5 text-[#A88950]"></i>
                        <span>Treasury</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-80"></i>
                    </button>
                    <div x-show="open" x-transition.opacity style="display: none;" class="absolute left-0 mt-1 w-52 payon-wood-frame p-1.5 z-50">
                        @if($modMgr->isEnabled('cash_shop'))
                            <a href="{{ route('cash-shop.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="store" class="w-3.5 h-3.5 text-[#526D3A]"></i><span>Cash Shop Mall</span></a>
                        @endif
                        @if($modMgr->isEnabled('marketplace'))
                            <a href="{{ route('marketplace.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-[#80633B]"></i><span>Bazaar Marketplace</span></a>
                        @endif
                        @if($modMgr->isEnabled('donations'))
                            <a href="{{ route('donations.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="gem" class="w-3.5 h-3.5 text-[#A88950]"></i><span>Donations &amp; Tribute</span></a>
                        @endif
                        @if($modMgr->isEnabled('attendance'))
                            <a href="{{ route('attendance.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="calendar" class="w-3.5 h-3.5 text-[#526D3A]"></i><span>Daily Rewards</span></a>
                        @endif
                    </div>
                </div>

                <!-- Community Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" @click="open = !open" class="payon-nav-link cursor-pointer {{ $communityActive ? 'active' : '' }}">
                        <i data-lucide="messages-square" class="w-3.5 h-3.5 text-[#A88950]"></i>
                        <span>Pavilion</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-80"></i>
                    </button>
                    <div x-show="open" x-transition.opacity style="display: none;" class="absolute left-0 mt-1 w-52 payon-wood-frame p-1.5 z-50">
                        @if($modMgr->isEnabled('forum') && Route::has('forum.index'))
                            <a href="{{ route('forum.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="message-square" class="w-3.5 h-3.5 text-[#526D3A]"></i><span>Village Forum</span></a>
                        @endif
                        @if($modMgr->isEnabled('gallery') && Route::has('gallery.index'))
                            <a href="{{ route('gallery.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="image" class="w-3.5 h-3.5 text-[#80633B]"></i><span>Art &amp; Gallery</span></a>
                        @endif
                        @if($modMgr->isEnabled('faq') && Route::has('faq.index'))
                            <a href="{{ route('faq.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="help-circle" class="w-3.5 h-3.5 text-[#526D3A]"></i><span>FAQ Scrolls</span></a>
                        @endif
                        @if($modMgr->isEnabled('support') && Route::has('support.index'))
                            <a href="{{ route('support.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="life-buoy" class="w-3.5 h-3.5 text-[#263D2C]"></i><span>Support Hall</span></a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Auth / Account Controls -->
            <div class="hidden md:flex items-center gap-2.5 shrink-0">
                @auth
                    <div class="relative" @click.away="userDropdown = false">
                        <button type="button" @click="userDropdown = !userDropdown" class="flex items-center gap-2 px-3 py-1.5 rounded-md bg-[#493322] border border-[#A88950] text-[#FFF8E8] text-xs font-bold cursor-pointer hover:bg-[#5D422D] transition-colors">
                            <span class="w-6 h-6 rounded bg-[#A88950] text-[#35291F] flex items-center justify-center font-cinzel font-black text-xs">
                                {{ strtoupper(substr(auth()->user()->username ?? auth()->user()->name, 0, 1)) }}
                            </span>
                            <span>{{ auth()->user()->username ?? auth()->user()->name }}</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#E7D8B5]"></i>
                        </button>

                        <div x-show="userDropdown" x-transition.opacity style="display: none;" class="absolute right-0 mt-1.5 w-56 payon-wood-frame p-1.5 z-50 divide-y divide-[#E7D8B5]">
                            <div class="px-3 py-2 bg-[#F2E8CF] rounded-t">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-[#80633B]">Account ID</p>
                                <p class="text-xs font-mono font-bold text-[#35291F]">#{{ auth()->user()->account_id }}</p>
                            </div>
                            <div class="py-1 space-y-0.5">
                                <a href="{{ route('account.profile') }}" class="flex items-center gap-2 px-3 py-1.5 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="user" class="w-3.5 h-3.5 text-[#526D3A]"></i><span>Account Profile</span></a>
                                @if($modMgr->isEnabled('character'))
                                    <a href="{{ route('account.characters') }}" class="flex items-center gap-2 px-3 py-1.5 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="users" class="w-3.5 h-3.5 text-[#263D2C]"></i><span>My Characters</span></a>
                                @endif
                                @if($modMgr->isEnabled('character_creation') && Route::has('account.characters.create'))
                                    <a href="{{ route('account.characters.create') }}" class="flex items-center gap-2 px-3 py-1.5 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="user-plus" class="w-3.5 h-3.5 text-[#526D3A]"></i><span>Create Character</span></a>
                                @endif
                                @if($modMgr->isEnabled('redeem_codes') && Route::has('account.redeem'))
                                    <a href="{{ route('account.redeem') }}" class="flex items-center gap-2 px-3 py-1.5 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="ticket" class="w-3.5 h-3.5 text-[#A88950]"></i><span>Redeem Code</span></a>
                                @endif
                                @if(Route::has('escrow.index'))
                                    <a href="{{ route('escrow.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded text-xs font-semibold text-[#35291F] hover:bg-[#E7D8B5]"><i data-lucide="inbox" class="w-3.5 h-3.5 text-[#526D3A]"></i><span>Escrow Inbox</span></a>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-1.5 rounded text-xs font-bold text-[#493322] bg-[#E7D8B5] hover:bg-[#DEC99A]"><i data-lucide="shield-alert" class="w-3.5 h-3.5 text-[#80633B]"></i><span>Admin Control Panel</span></a>
                                @endif
                            </div>
                            <div class="pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 rounded text-xs font-bold text-[#8C2D2D] hover:bg-[#FDE8E8] cursor-pointer">
                                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                        <span>Depart (Sign Out)</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="payon-nav-link text-xs">Sign In</a>
                    <a href="{{ route('register') }}" class="payon-btn-gold text-xs py-1.5 px-3.5">Create Account</a>
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <button type="button" @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded bg-[#493322] border border-[#A88950] text-[#FFF8E8] cursor-pointer" aria-label="Toggle Menu">
                <i data-lucide="menu" class="w-5 h-5" x-show="!mobileOpen"></i>
                <i data-lucide="x" class="w-5 h-5" x-show="mobileOpen" style="display: none;"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="mobileOpen" x-transition style="display: none;" class="md:hidden bg-[#1E3123] border-t border-[#493322] px-4 py-4 space-y-1.5 text-xs">
        <a href="{{ route('home') }}" class="block px-3 py-2 rounded font-bold text-[#FFF8E8] bg-[#493322]">Sanctuary (Home)</a>
        @if($modMgr->isEnabled('news'))<a href="{{ route('news.index') }}" class="block px-3 py-2 rounded text-[#F2E8CF] hover:bg-[#263D2C]">Kingdom Notices</a>@endif
        @if($modMgr->isEnabled('rankings'))<a href="{{ route('rankings.index') }}" class="block px-3 py-2 rounded text-[#F2E8CF] hover:bg-[#263D2C]">Ranking Hall</a>@endif
        @if($modMgr->isEnabled('guild'))<a href="{{ route('guilds.index') }}" class="block px-3 py-2 rounded text-[#F2E8CF] hover:bg-[#263D2C]">Clans &amp; Guilds</a>@endif
        @if($modMgr->isEnabled('downloads'))<a href="{{ route('downloads.index') }}" class="block px-3 py-2 rounded text-[#F2E8CF] hover:bg-[#263D2C]">Client Downloads</a>@endif
        @if($modMgr->isEnabled('cash_shop'))<a href="{{ route('cash-shop.index') }}" class="block px-3 py-2 rounded text-[#FFF8E8] hover:bg-[#263D2C]">Cash Shop Mall</a>@endif
        @if($modMgr->isEnabled('marketplace'))<a href="{{ route('marketplace.index') }}" class="block px-3 py-2 rounded text-[#F2E8CF] hover:bg-[#263D2C]">Marketplace</a>@endif
        @if($modMgr->isEnabled('donations'))<a href="{{ route('donations.index') }}" class="block px-3 py-2 rounded text-[#F2E8CF] hover:bg-[#263D2C]">Donations</a>@endif
        @if($modMgr->isEnabled('forum') && Route::has('forum.index'))<a href="{{ route('forum.index') }}" class="block px-3 py-2 rounded text-[#F2E8CF] hover:bg-[#263D2C]">Village Forum</a>@endif
        @if($modMgr->isEnabled('faq') && Route::has('faq.index'))<a href="{{ route('faq.index') }}" class="block px-3 py-2 rounded text-[#F2E8CF] hover:bg-[#263D2C]">FAQ Scrolls</a>@endif
        @guest
            <div class="pt-2 flex gap-2">
                <a href="{{ route('login') }}" class="payon-btn-wood flex-1 text-center">Sign In</a>
                <a href="{{ route('register') }}" class="payon-btn-gold flex-1 text-center">Register</a>
            </div>
        @endguest
    </div>
</nav>
