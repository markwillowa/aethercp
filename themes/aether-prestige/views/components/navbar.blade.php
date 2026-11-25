@php
    $isHome = request()->routeIs('home');
    $modMgr = app(\App\Services\Module\ModuleManager::class);
@endphp
<header class="{{ $isHome ? 'prestige-header-transparent' : 'prestige-header-solid' }}">
    <div class="prestige-container">
        <div class="prestige-nav-wrapper">
            <!-- Left: Brand Logo & Title (Matching reference image: 'M Y T H E R A') -->
            <a href="{{ route('home') }}" class="prestige-nav-brand">
                {{ config('aether.server_name', 'MYTHERA') }}
            </a>

            <!-- Center: Navigation Links (Matching reference: HOME, WORLD, CHARACTERS, RANKINGS, DATABASE, MEDIA, DOWNLOADS) -->
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="prestige-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    Home
                </a>
                <a href="{{ route('woe.index') }}" class="prestige-nav-link {{ request()->routeIs('woe.*') ? 'active' : '' }}">
                    World
                </a>
                @if($modMgr->isEnabled('character'))
                    <a href="{{ route('characters.online') }}" class="prestige-nav-link {{ request()->routeIs('characters.*') ? 'active' : '' }}">
                        Characters
                    </a>
                @endif
                @if($modMgr->isEnabled('rankings'))
                    <a href="{{ route('rankings.index') }}" class="prestige-nav-link {{ request()->routeIs('rankings.*') ? 'active' : '' }}">
                        Rankings
                    </a>
                @endif
                @if($modMgr->isEnabled('database'))
                    <a href="{{ route('itemdb.index') }}" class="prestige-nav-link {{ request()->routeIs('itemdb.*') || request()->routeIs('database.*') ? 'active' : '' }}">
                        Database
                    </a>
                @endif
                @if($modMgr->isEnabled('news'))
                    <a href="{{ route('news.index') }}" class="prestige-nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}">
                        Media
                    </a>
                @endif
                @if($modMgr->isEnabled('downloads'))
                    <a href="{{ route('downloads.index') }}" class="prestige-nav-link {{ request()->routeIs('downloads.*') ? 'active' : '' }}">
                        Downloads
                    </a>
                @endif
                @if($modMgr->isEnabled('marketplace'))
                    <a href="{{ route('marketplace.index') }}" class="prestige-nav-link {{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
                        Marketplace
                    </a>
                @endif
                @if($modMgr->isEnabled('cash_shop'))
                    <a href="{{ route('cash-shop.index') }}" class="prestige-nav-link {{ request()->routeIs('cash-shop.*') ? 'active' : '' }}">
                        Cash Shop
                    </a>
                @endif
            </nav>

            <!-- Right: User Account Profile (Matching reference: 'FERDI' with square avatar box) -->
            <div class="hidden sm:flex items-center space-x-3">
                @auth
                    <div class="relative" id="prestigeUserDropdown">
                        <button type="button" onclick="document.getElementById('prestigeUserMenu').classList.toggle('hidden')" class="prestige-user-box">
                            <span class="font-cinzel text-xs font-bold uppercase tracking-wider">
                                {{ strtoupper(auth()->user()->username ?? auth()->user()->name) }}
                            </span>
                            <div class="prestige-user-avatar">
                                {{ strtoupper(substr(auth()->user()->username ?? auth()->user()->name, 0, 1)) }}
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="prestigeUserMenu" class="hidden absolute right-0 mt-2 w-56 bg-[#151B26] border border-white/[0.12] rounded-xl shadow-2xl py-2 z-50 backdrop-blur-xl">
                            <div class="px-4 py-2 border-b border-white/[0.06]">
                                <p class="text-[10px] uppercase tracking-wider text-[#788394] font-semibold">Signed in as</p>
                                <p class="text-xs font-bold text-white truncate">{{ auth()->user()->email ?? auth()->user()->username }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('account.profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-[#AAB3C1] hover:text-white hover:bg-[#1B2432] transition-colors">
                                    <i data-lucide="user" class="w-4 h-4 text-[#C6A76A]"></i>
                                    <span>Account Profile</span>
                                </a>
                                <a href="{{ route('account.characters') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-[#AAB3C1] hover:text-white hover:bg-[#1B2432] transition-colors">
                                    <i data-lucide="users" class="w-4 h-4 text-[#D8BE8A]"></i>
                                    <span>My Characters</span>
                                </a>
                                @if($modMgr->isEnabled('cash_shop'))
                                    <a href="{{ route('cash-shop.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-[#AAB3C1] hover:text-white hover:bg-[#1B2432] transition-colors">
                                        <i data-lucide="store" class="w-4 h-4 text-[#C6A76A]"></i>
                                        <span>Cash Shop</span>
                                    </a>
                                @endif
                                <a href="{{ route('escrow.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-[#AAB3C1] hover:text-white hover:bg-[#1B2432] transition-colors">
                                    <i data-lucide="inbox" class="w-4 h-4 text-[#84E1BC]"></i>
                                    <span>Escrow Inbox</span>
                                </a>
                                <a href="{{ route('donations.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-[#AAB3C1] hover:text-white hover:bg-[#1B2432] transition-colors">
                                    <i data-lucide="coins" class="w-4 h-4 text-[#C6A76A]"></i>
                                    <span>Cash Points / Donate</span>
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-[#F59E0B] hover:text-white hover:bg-[#1B2432] transition-colors">
                                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                                        <span>Admin Control Panel</span>
                                    </a>
                                @endif
                            </div>
                            <div class="pt-1 border-t border-white/[0.06]">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-red-400 hover:text-red-300 hover:bg-[#1B2432] transition-colors text-left">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-bold uppercase tracking-wider text-[#AAB3C1] hover:text-white px-3 py-2 transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="prestige-btn-cyan text-xs py-2 px-4">
                        <span>Register</span>
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="lg:hidden flex items-center">
                <button type="button" onclick="document.getElementById('prestigeMobileMenu').classList.toggle('hidden')" class="p-2 rounded-lg bg-[#151B26]/80 border border-white/10 text-white hover:text-[#C6A76A] transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div id="prestigeMobileMenu" class="hidden lg:hidden bg-[#101725] border-b border-white/10 px-4 pt-3 pb-6 space-y-3">
        <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
            <a href="{{ route('home') }}" class="p-2.5 rounded-lg bg-[#151B26] text-white hover:text-[#C6A76A]">Home</a>
            <a href="{{ route('woe.index') }}" class="p-2.5 rounded-lg bg-[#151B26] text-white hover:text-[#C6A76A]">World (WoE)</a>
            @if($modMgr->isEnabled('character'))
                <a href="{{ route('characters.online') }}" class="p-2.5 rounded-lg bg-[#151B26] text-white hover:text-[#C6A76A]">Characters</a>
            @endif
            @if($modMgr->isEnabled('rankings'))
                <a href="{{ route('rankings.index') }}" class="p-2.5 rounded-lg bg-[#151B26] text-white hover:text-[#C6A76A]">Rankings</a>
            @endif
            @if($modMgr->isEnabled('guild'))
                <a href="{{ route('guilds.index') }}" class="p-2.5 rounded-lg bg-[#151B26] text-white hover:text-[#C6A76A]">Guilds</a>
            @endif
            @if($modMgr->isEnabled('database'))
                <a href="{{ route('itemdb.index') }}" class="p-2.5 rounded-lg bg-[#151B26] text-white hover:text-[#C6A76A]">Item Codex</a>
            @endif
            @if($modMgr->isEnabled('news'))
                <a href="{{ route('news.index') }}" class="p-2.5 rounded-lg bg-[#151B26] text-white hover:text-[#C6A76A]">Media &amp; News</a>
            @endif
            @if($modMgr->isEnabled('downloads'))
                <a href="{{ route('downloads.index') }}" class="p-2.5 rounded-lg bg-[#151B26] text-white hover:text-[#C6A76A]">Downloads</a>
            @endif
            @if($modMgr->isEnabled('donations'))
                <a href="{{ route('donations.index') }}" class="p-2.5 rounded-lg bg-[#151B26] text-white hover:text-[#C6A76A]">Donations</a>
            @endif
        </div>

        <div class="pt-3 border-t border-white/10 flex items-center justify-between">
            @auth
                <div class="flex items-center gap-2">
                    <a href="{{ route('account.profile') }}" class="prestige-btn-outline text-xs py-2 px-3">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs text-red-400 font-bold px-3 py-2">Logout</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-xs font-bold text-white px-3 py-2">Login</a>
                <a href="{{ route('register') }}" class="prestige-btn-cyan text-xs py-2 px-4">Register</a>
            @endauth
        </div>
    </div>
</header>
