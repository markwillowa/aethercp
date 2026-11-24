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
                <a href="{{ route('server.status') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="activity" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>Server Status &amp; Info</span>
                </a>
                <a href="{{ route('downloads.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="download" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>Client Downloads</span>
                </a>
                @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('faq'))
                    <a href="{{ route('faq.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="help-circle" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Knowledge Base &amp; FAQ</span>
                    </a>
                @endif
                @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('support'))
                    <a href="{{ route('support.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="life-buoy" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Support Center</span>
                    </a>
                @endif
                @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('gallery'))
                    <a href="{{ route('gallery.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                        <i data-lucide="image" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                        <span>Media &amp; Screenshots</span>
                    </a>
                @endif
                <a href="{{ route('characters.online') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="users" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>Active Adventurers</span>
                </a>
                <a href="{{ route('news.index') }}" class="block px-3 py-2 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352643] hover:text-[#FFD700] rounded transition-colors flex items-center gap-2">
                    <i data-lucide="scroll" class="w-3.5 h-3.5 text-[#D4AF37]"></i>
                    <span>Server Chronicles</span>
                </a>
            </div>
        </div>

        <!-- DATABASE (with Item, Monster, Map, Card Dropdown) -->
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

        <!-- GUILDS -->
        <a href="{{ route('guilds.index') }}" class="chronicles-nav-item {{ request()->routeIs('guilds.*') ? 'active' : '' }}">
            <i data-lucide="shield" class="w-3.5 h-3.5"></i>
            <span>GUILDS</span>
        </a>

        <!-- MARKETPLACE -->
        @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('marketplace'))
            <a href="{{ route('marketplace.index') }}" class="chronicles-nav-item {{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
                <i data-lucide="scale" class="w-3.5 h-3.5"></i>
                <span>MARKETPLACE</span>
            </a>
        @endif

        <!-- DONATE -->
        <a href="{{ route('donations.index') }}" class="chronicles-nav-item {{ request()->routeIs('donations.*') ? 'active' : '' }}">
            <i data-lucide="gem" class="w-3.5 h-3.5"></i>
            <span>DONATE</span>
        </a>
    </div>

    <!-- Far Right: Prominent Ornate Gold "Play Now" CTA Button -->
    <div class="py-1 pl-2 shrink-0">
        <a href="{{ route('downloads.index') }}" class="chronicles-btn-playnow">
            <span>PLAY NOW</span>
            <span class="text-sm">✦</span>
        </a>
    </div>
</nav>
