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

                        <div x-show="userDropdown" x-transition.opacity.duration.100ms class="absolute right-0 mt-1 w-48 ro-panel z-50 divide-y divide-[#DECBB1] p-1 shadow-xl">
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
    <div class="ro-navbar">
        <div class="ro-container flex items-center justify-between">
            <!-- Desktop Links -->
            <div class="ro-desktop-only items-center flex-wrap">
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
                @if($modMgr->isEnabled('downloads'))
                <a href="{{ route('downloads.index') }}" class="ro-nav-item {{ request()->routeIs('downloads.*') ? 'active' : '' }}">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span>Downloads</span>
                </a>
                @endif
                @if($modMgr->isEnabled('rankings'))
                <a href="{{ route('rankings.index') }}" class="ro-nav-item {{ request()->routeIs('rankings.*') ? 'active' : '' }}">
                    <i data-lucide="award" class="w-3.5 h-3.5"></i>
                    <span>Rankings</span>
                </a>
                @endif
                @if($modMgr->isEnabled('character'))
                <a href="{{ route('characters.online') }}" class="ro-nav-item {{ request()->routeIs('characters.online') ? 'active' : '' }}">
                    <i data-lucide="users" class="w-3.5 h-3.5"></i>
                    <span>Online</span>
                </a>
                @endif
                @if($modMgr->isEnabled('guild'))
                <a href="{{ route('guilds.index') }}" class="ro-nav-item {{ request()->routeIs('guilds.*') ? 'active' : '' }}">
                    <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                    <span>Guilds</span>
                </a>
                @endif
                <a href="{{ route('woe.index') }}" class="ro-nav-item {{ request()->routeIs('woe.*') ? 'active' : '' }}">
                    <i data-lucide="swords" class="w-3.5 h-3.5"></i>
                    <span>WoE</span>
                </a>
                @if($modMgr->isEnabled('database'))
                <a href="{{ route('itemdb.index') }}" class="ro-nav-item {{ request()->routeIs('itemdb.*') || request()->routeIs('database.*') ? 'active' : '' }}">
                    <i data-lucide="database" class="w-3.5 h-3.5"></i>
                    <span>Database</span>
                </a>
                @endif
                @if($modMgr->isEnabled('cash_shop'))
                <a href="{{ route('cash-shop.index') }}" class="ro-nav-item {{ request()->routeIs('cash-shop.*') ? 'active' : '' }}">
                    <i data-lucide="store" class="w-3.5 h-3.5"></i>
                    <span>Cash Shop</span>
                </a>
                @endif
                @if($modMgr->isEnabled('donations'))
                <a href="{{ route('donations.index') }}" class="ro-nav-item {{ request()->routeIs('donations.*') ? 'active' : '' }}">
                    <i data-lucide="heart-handshake" class="w-3.5 h-3.5"></i>
                    <span>Donations</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Mobile Drawer Navigation (Only on Mobile) -->
        <div x-show="mobileOpen" x-transition class="ro-mobile-only flex-col bg-[#2D1D13] border-t border-[#65482D] py-2 px-4 space-y-1">
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

