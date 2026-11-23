<nav x-data="{ mobileOpen: false, userDropdown: false }" class="relative z-30 shadow-md">
    <!-- Middle Brand Banner (Classic Ragnarok Medieval Crest) -->
    <div class="bg-gradient-to-r from-[#2F1D13] via-[#483120] to-[#2F1D13] border-b border-[#7D5D39] py-4 px-4 sm:px-6">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4">
            <!-- Brand Crest & Server Titles -->
            <a href="{{ route('home') }}" class="flex items-center gap-3.5 group text-decoration-none">
                <div class="w-12 h-12 rounded bg-gradient-to-b from-[#D4AF67] to-[#7D5A1B] p-0.5 shadow-md flex items-center justify-center">
                    <div class="w-full h-full bg-[#3B2618] rounded flex items-center justify-center border border-[#E9C788]">
                        <i data-lucide="shield" class="w-6 h-6 text-[#FFDA89]"></i>
                    </div>
                </div>
                <div>
                    <span class="text-2xl font-black font-serif tracking-tight text-[#FFF0D4] drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)] block">
                        {{ config('aether.name', 'AetherCP') }}
                    </span>
                    <span class="text-xs uppercase tracking-widest text-[#E6C280] font-bold block">
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
                                <a href="{{ route('account.characters') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#3A2A1C] hover:bg-[#EBD6B3] rounded transition-colors">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-[#315B88]"></i>
                                    <span>My Characters</span>
                                </a>
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
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <!-- Desktop Links -->
            <div class="ro-desktop-only items-center flex-wrap">
                <a href="{{ route('home') }}" class="ro-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('news.index') }}" class="ro-nav-item {{ request()->routeIs('news.*') ? 'active' : '' }}">
                    <i data-lucide="newspaper" class="w-3.5 h-3.5"></i>
                    <span>News</span>
                </a>
                <a href="{{ route('downloads.index') }}" class="ro-nav-item {{ request()->routeIs('downloads.*') ? 'active' : '' }}">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span>Downloads</span>
                </a>
                <a href="{{ route('rankings.index') }}" class="ro-nav-item {{ request()->routeIs('rankings.*') ? 'active' : '' }}">
                    <i data-lucide="award" class="w-3.5 h-3.5"></i>
                    <span>Rankings</span>
                </a>
                <a href="{{ route('characters.online') }}" class="ro-nav-item {{ request()->routeIs('characters.online') ? 'active' : '' }}">
                    <i data-lucide="users" class="w-3.5 h-3.5"></i>
                    <span>Online</span>
                </a>
                <a href="{{ route('guilds.index') }}" class="ro-nav-item {{ request()->routeIs('guilds.*') ? 'active' : '' }}">
                    <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                    <span>Guilds</span>
                </a>
                <a href="{{ route('woe.index') }}" class="ro-nav-item {{ request()->routeIs('woe.*') ? 'active' : '' }}">
                    <i data-lucide="swords" class="w-3.5 h-3.5"></i>
                    <span>WoE</span>
                </a>
                <a href="{{ route('itemdb.index') }}" class="ro-nav-item {{ request()->routeIs('itemdb.*') ? 'active' : '' }}">
                    <i data-lucide="database" class="w-3.5 h-3.5"></i>
                    <span>Database</span>
                </a>
                <a href="{{ route('donations.index') }}" class="ro-nav-item {{ request()->routeIs('donations.*') ? 'active' : '' }}">
                    <i data-lucide="heart-handshake" class="w-3.5 h-3.5"></i>
                    <span>Donations</span>
                </a>
            </div>
        </div>

        <!-- Mobile Drawer Navigation (Only on Mobile) -->
        <div x-show="mobileOpen" x-transition class="ro-mobile-only flex-col bg-[#2D1D13] border-t border-[#65482D] py-2 px-4 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Home</a>
            <a href="{{ route('news.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">News & Updates</a>
            <a href="{{ route('downloads.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Game Client Downloads</a>
            <a href="{{ route('rankings.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Player Rankings</a>
            <a href="{{ route('characters.online') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Online Adventurers</a>
            <a href="{{ route('guilds.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Guild Roster</a>
            <a href="{{ route('woe.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">War of Emperium</a>
            <a href="{{ route('itemdb.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Item Compendium</a>
            <a href="{{ route('donations.index') }}" class="block px-3 py-2 text-xs font-serif font-bold text-[#E7D5BE] hover:bg-[#3D281A] rounded">Support & Donations</a>
        </div>
    </div>
</nav>

