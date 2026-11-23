<nav class="chronicles-nav">
    <div class="chronicles-nav-links">
        <!-- HOME -->
        <a href="{{ route('home') }}" class="chronicles-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>HOME</span>
        </a>

        <!-- INFORMATION (with Dropdown) -->
        <div class="relative group">
            <button type="button" class="chronicles-nav-item flex items-center gap-1 cursor-pointer">
                <i data-lucide="info" class="w-3.5 h-3.5"></i>
                <span>INFORMATION</span>
                <i data-lucide="chevron-down" class="w-3 h-3 text-[#A88B57]"></i>
            </button>
            <div class="hidden group-hover:block absolute top-full left-0 mt-0.5 w-48 bg-[#1F1926] border-2 border-[#5A4A68] rounded shadow-2xl p-1.5 z-50">
                <a href="{{ route('downloads.index') }}" class="block px-3 py-1.5 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352B42] hover:text-[#FFD700] rounded">Client Downloads</a>
                <a href="{{ route('characters.online') }}" class="block px-3 py-1.5 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352B42] hover:text-[#FFD700] rounded">Active Adventurers</a>
                <a href="{{ route('news.index') }}" class="block px-3 py-1.5 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352B42] hover:text-[#FFD700] rounded">Server Chronicles</a>
            </div>
        </div>

        <!-- JOBS -->
        <a href="{{ route('rankings.index') }}" class="chronicles-nav-item">
            <i data-lucide="swords" class="w-3.5 h-3.5"></i>
            <span>JOBS</span>
        </a>

        <!-- MAPS & MONSTERS -->
        <a href="{{ route('itemdb.index') }}" class="chronicles-nav-item {{ request()->routeIs('itemdb.*') ? 'active' : '' }}">
            <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
            <span>MAPS &amp; MONSTERS</span>
        </a>

        <!-- RANKINGS -->
        <a href="{{ route('rankings.index') }}" class="chronicles-nav-item {{ request()->routeIs('rankings.*') ? 'active' : '' }}">
            <i data-lucide="trophy" class="w-3.5 h-3.5"></i>
            <span>RANKINGS</span>
        </a>

        <!-- GUILDS -->
        <a href="{{ route('guilds.index') }}" class="chronicles-nav-item {{ request()->routeIs('guilds.*') ? 'active' : '' }}">
            <i data-lucide="shield" class="w-3.5 h-3.5"></i>
            <span>GUILDS</span>
        </a>

        <!-- DOWNLOADS -->
        <a href="{{ route('downloads.index') }}" class="chronicles-nav-item {{ request()->routeIs('downloads.*') ? 'active' : '' }}">
            <i data-lucide="download" class="w-3.5 h-3.5"></i>
            <span>DOWNLOADS</span>
        </a>

        <!-- DONATE / COMMUNITY -->
        <a href="{{ route('donations.index') }}" class="chronicles-nav-item {{ request()->routeIs('donations.*') ? 'active' : '' }}">
            <i data-lucide="gem" class="w-3.5 h-3.5"></i>
            <span>DONATE</span>
        </a>
    </div>

    <!-- Far Right: Prominent Ornate Gold "Play Now" CTA Button -->
    <div class="py-1 pl-2">
        <a href="{{ route('downloads.index') }}" class="chronicles-btn-playnow">
            <span>PLAY NOW</span>
            <span class="text-sm">✦</span>
        </a>
    </div>
</nav>
