@php
    $modMgr = app(\App\Services\Module\ModuleManager::class);
    $worldActive = request()->routeIs('news.*') || request()->routeIs('rankings.*') || request()->routeIs('guilds.*') || request()->routeIs('characters.online') || request()->routeIs('woe.*') || request()->routeIs('itemdb.*') || request()->routeIs('database.*') || request()->routeIs('downloads.*');
    $treasuryActive = request()->routeIs('cash-shop.*') || request()->routeIs('marketplace.*') || request()->routeIs('donations.*') || request()->routeIs('attendance.*');
    $communityActive = request()->routeIs('forum.*') || request()->routeIs('gallery.*') || request()->routeIs('faq.*') || request()->routeIs('support.*');
@endphp
<nav x-data="{ mobileOpen: false, userDropdown: false }" class="relative z-40 border-b border-amber-500/20 bg-[#080d1a]/95 backdrop-blur-md sticky top-0 shadow-lg shadow-black/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Brand Logo with Elysian Gold Emblem -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 via-amber-500 to-sky-500 p-0.5 shadow-lg shadow-amber-500/20 group-hover:shadow-amber-500/40 transition-all">
                        <div class="w-full h-full bg-[#070b16] rounded-[10px] flex items-center justify-center">
                            <i data-lucide="crown" class="w-5 h-5 text-amber-400 group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-lg font-bold tracking-tight elysian-gold-text whitespace-nowrap">{{ config('aether.name', 'AetherCP') }}</span>
                        <span class="block text-[10px] text-amber-400/80 font-medium tracking-widest uppercase whitespace-nowrap">Elysian Realm &bull; {{ config('aether.server_name', 'Ragnarok') }}</span>
                    </div>
                </a>
            </div>

            <!-- Desktop Menu Links (Grouped Dropdowns) -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap {{ request()->routeIs('home') ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    Home
                </a>

                <!-- World Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" @click="open = !open" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1 cursor-pointer whitespace-nowrap {{ $worldActive ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <span>World</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-75"></i>
                    </button>
                    <div x-show="open" x-transition.opacity style="display: none;" class="absolute left-0 mt-1 w-52 rounded-2xl bg-[#0c1322] border border-amber-500/30 shadow-2xl py-2 z-50">
                        @if($modMgr->isEnabled('news'))
                            <a href="{{ route('news.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="newspaper" class="w-3.5 h-3.5 text-amber-400"></i><span>News &amp; Dispatches</span></a>
                        @endif
                        @if($modMgr->isEnabled('rankings'))
                            <a href="{{ route('rankings.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="trophy" class="w-3.5 h-3.5 text-amber-400"></i><span>Hall of Fame</span></a>
                        @endif
                        @if($modMgr->isEnabled('guild'))
                            <a href="{{ route('guilds.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="shield" class="w-3.5 h-3.5 text-amber-400"></i><span>Guilds</span></a>
                        @endif
                        @if($modMgr->isEnabled('character'))
                            <a href="{{ route('characters.online') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="users" class="w-3.5 h-3.5 text-sky-400"></i><span>Online Players</span></a>
                        @endif
                        <a href="{{ route('woe.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="swords" class="w-3.5 h-3.5 text-amber-400"></i><span>War of Emperium</span></a>
                        @if($modMgr->isEnabled('database'))
                            <a href="{{ route('itemdb.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="database" class="w-3.5 h-3.5 text-sky-400"></i><span>Item &amp; Mob Database</span></a>
                        @endif
                        @if($modMgr->isEnabled('downloads'))
                            <a href="{{ route('downloads.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="download" class="w-3.5 h-3.5 text-amber-400"></i><span>Downloads</span></a>
                        @endif
                    </div>
                </div>

                <!-- Treasury Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" @click="open = !open" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1 cursor-pointer whitespace-nowrap {{ $treasuryActive ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <span>Treasury</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-75"></i>
                    </button>
                    <div x-show="open" x-transition.opacity style="display: none;" class="absolute left-0 mt-1 w-52 rounded-2xl bg-[#0c1322] border border-amber-500/30 shadow-2xl py-2 z-50">
                        @if($modMgr->isEnabled('cash_shop'))
                            <a href="{{ route('cash-shop.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="store" class="w-3.5 h-3.5 text-amber-400"></i><span>Cash Shop</span></a>
                        @endif
                        @if($modMgr->isEnabled('marketplace'))
                            <a href="{{ route('marketplace.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-sky-400"></i><span>Marketplace</span></a>
                        @endif
                        @if($modMgr->isEnabled('donations'))
                            <a href="{{ route('donations.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="gem" class="w-3.5 h-3.5 text-amber-400"></i><span>Donations &amp; Top-up</span></a>
                        @endif
                        @if($modMgr->isEnabled('attendance'))
                            <a href="{{ route('attendance.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="calendar" class="w-3.5 h-3.5 text-amber-400"></i><span>Daily Rewards</span></a>
                        @endif
                    </div>
                </div>

                <!-- Community Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" @click="open = !open" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1 cursor-pointer whitespace-nowrap {{ $communityActive ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <span>Community</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-75"></i>
                    </button>
                    <div x-show="open" x-transition.opacity style="display: none;" class="absolute left-0 mt-1 w-52 rounded-2xl bg-[#0c1322] border border-amber-500/30 shadow-2xl py-2 z-50">
                        @if($modMgr->isEnabled('forum') && Route::has('forum.index'))
                            <a href="{{ route('forum.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="message-square" class="w-3.5 h-3.5 text-amber-400"></i><span>Community Forum</span></a>
                        @endif
                        @if($modMgr->isEnabled('gallery') && Route::has('gallery.index'))
                            <a href="{{ route('gallery.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="image" class="w-3.5 h-3.5 text-sky-400"></i><span>Media Gallery</span></a>
                        @endif
                        @if($modMgr->isEnabled('faq') && Route::has('faq.index'))
                            <a href="{{ route('faq.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="help-circle" class="w-3.5 h-3.5 text-amber-400"></i><span>FAQ &amp; Guides</span></a>
                        @endif
                        @if($modMgr->isEnabled('support') && Route::has('support.index'))
                            <a href="{{ route('support.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60"><i data-lucide="life-buoy" class="w-3.5 h-3.5 text-sky-400"></i><span>Support Center</span></a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side Auth / User Controls -->
            <div class="hidden md:flex items-center space-x-3 shrink-0">
                @auth
                    <div class="relative" @click.away="userDropdown = false">
                        <button @click="userDropdown = !userDropdown" class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-900 border border-amber-500/30 hover:border-amber-400 transition-colors shadow-sm cursor-pointer">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-amber-500 to-amber-300 flex items-center justify-center text-xs font-black text-slate-950 shadow-sm">
                                {{ strtoupper(substr(auth()->user()->username ?? auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-amber-100">{{ auth()->user()->username ?? auth()->user()->name }}</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-amber-400/80"></i>
                        </button>

                        <div x-show="userDropdown"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             style="display: none;"
                             class="absolute right-0 mt-2 w-56 rounded-2xl bg-[#0c1322] border border-amber-500/30 shadow-2xl py-2 z-50 divide-y divide-slate-800">

                            <div class="px-4 py-2">
                                <p class="text-[10px] text-amber-400 font-bold uppercase tracking-wider">Game Account</p>
                                <p class="text-xs font-mono text-slate-200 mt-0.5">#{{ auth()->user()->account_id }}</p>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('account.profile') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60 transition-colors">
                                    <i data-lucide="user" class="w-3.5 h-3.5 text-amber-400"></i>
                                    <span>Account Profile</span>
                                </a>
                                @if($modMgr->isEnabled('character'))
                                    <a href="{{ route('account.characters') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60 transition-colors">
                                        <i data-lucide="shield" class="w-3.5 h-3.5 text-amber-400"></i>
                                        <span>My Characters</span>
                                    </a>
                                @endif
                                @if($modMgr->isEnabled('character_creation') && Route::has('account.characters.create'))
                                    <a href="{{ route('account.characters.create') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60 transition-colors">
                                        <i data-lucide="user-plus" class="w-3.5 h-3.5 text-sky-400"></i>
                                        <span>Create Character</span>
                                    </a>
                                @endif
                                @if($modMgr->isEnabled('redeem_codes') && Route::has('account.redeem'))
                                    <a href="{{ route('account.redeem') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60 transition-colors">
                                        <i data-lucide="ticket" class="w-3.5 h-3.5 text-amber-400"></i>
                                        <span>Redeem Code</span>
                                    </a>
                                @endif
                                @if(Route::has('escrow.index'))
                                    <a href="{{ route('escrow.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-300 hover:text-amber-300 hover:bg-slate-800/60 transition-colors">
                                        <i data-lucide="inbox" class="w-3.5 h-3.5 text-sky-400"></i>
                                        <span>Escrow Inbox</span>
                                    </a>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-amber-400 font-semibold hover:bg-amber-500/10 transition-colors">
                                        <i data-lucide="lock" class="w-3.5 h-3.5 text-amber-400"></i>
                                        <span>Admin Control Panel</span>
                                    </a>
                                @endif
                            </div>

                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs text-red-400 hover:bg-red-500/10 transition-colors cursor-pointer">
                                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-amber-300 transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 shadow-md shadow-amber-500/20 transition-all flex items-center gap-1.5">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                        <span>Register</span>
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="flex md:hidden items-center">
                <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-xl bg-slate-900 border border-amber-500/30 text-amber-400 cursor-pointer">
                    <i data-lucide="menu" class="w-5 h-5" x-show="!mobileOpen"></i>
                    <i data-lucide="x" class="w-5 h-5" x-show="mobileOpen" style="display: none;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer -->
    <div x-show="mobileOpen" style="display: none;" class="md:hidden border-t border-amber-500/20 bg-[#080d1a] px-4 pt-3 pb-6 space-y-1.5">
        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-amber-300 bg-amber-500/10">Home</a>
        @if($modMgr->isEnabled('news'))
            <a href="{{ route('news.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">News</a>
        @endif
        @if($modMgr->isEnabled('rankings'))
            <a href="{{ route('rankings.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">Rankings</a>
        @endif
        @if($modMgr->isEnabled('guild'))
            <a href="{{ route('guilds.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">Guilds</a>
        @endif
        @if($modMgr->isEnabled('character'))
            <a href="{{ route('characters.online') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">Online</a>
        @endif
        <a href="{{ route('woe.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">WoE</a>
        @if($modMgr->isEnabled('database'))
            <a href="{{ route('itemdb.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">Database</a>
        @endif
        @if($modMgr->isEnabled('downloads'))
            <a href="{{ route('downloads.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">Downloads</a>
        @endif
        @if($modMgr->isEnabled('cash_shop'))
            <a href="{{ route('cash-shop.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-amber-300 hover:bg-slate-800">Cash Shop</a>
        @endif
        @if($modMgr->isEnabled('marketplace'))
            <a href="{{ route('marketplace.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">Marketplace</a>
        @endif
        @if($modMgr->isEnabled('donations'))
            <a href="{{ route('donations.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">Donations</a>
        @endif
        @if($modMgr->isEnabled('forum') && Route::has('forum.index'))
            <a href="{{ route('forum.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">Community Forum</a>
        @endif
        @if($modMgr->isEnabled('faq') && Route::has('faq.index'))
            <a href="{{ route('faq.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">FAQ &amp; Guides</a>
        @endif
        @if($modMgr->isEnabled('support') && Route::has('support.index'))
            <a href="{{ route('support.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800">Support Center</a>
        @endif
    </div>
</nav>
