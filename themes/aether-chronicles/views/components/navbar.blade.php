<nav class="chronicles-nav">
    <div class="flex items-center overflow-x-auto scrollbar-none py-1">
        <!-- Home -->
        <a href="{{ route('home') }}" class="chronicles-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>Home</span>
        </a>

        <!-- Story -->
        <a href="{{ route('news.index') }}" class="chronicles-nav-item {{ request()->routeIs('news.*') ? 'active' : '' }}">
            <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
            <span>Story</span>
        </a>

        <!-- Information -->
        <div class="relative group">
            <button type="button" class="chronicles-nav-item flex items-center gap-1 cursor-pointer">
                <i data-lucide="info" class="w-3.5 h-3.5"></i>
                <span>Information</span>
                <i data-lucide="chevron-down" class="w-3 h-3 text-[#A88B57]"></i>
            </button>
            <div class="hidden group-hover:block absolute top-full left-0 mt-0.5 w-48 bg-[#1F1926] border-2 border-[#5A4A68] rounded-md shadow-2xl p-1.5 z-50">
                <a href="{{ route('downloads.index') }}" class="block px-3 py-1.5 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352B42] hover:text-[#FFD700] rounded">Client Downloads</a>
                <a href="{{ route('characters.online') }}" class="block px-3 py-1.5 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352B42] hover:text-[#FFD700] rounded">Active Adventurers</a>
            </div>
        </div>

        <!-- Jobs -->
        <a href="{{ route('rankings.index') }}" class="chronicles-nav-item">
            <i data-lucide="swords" class="w-3.5 h-3.5"></i>
            <span>Jobs</span>
        </a>

        <!-- Maps & Monsters -->
        <a href="{{ route('itemdb.index') }}" class="chronicles-nav-item {{ request()->routeIs('itemdb.*') ? 'active' : '' }}">
            <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
            <span>Maps & Monsters</span>
        </a>

        <!-- Quests -->
        <a href="{{ route('news.index') }}" class="chronicles-nav-item">
            <i data-lucide="scroll" class="w-3.5 h-3.5"></i>
            <span>Quests</span>
        </a>

        <!-- Rankings -->
        <a href="{{ route('rankings.index') }}" class="chronicles-nav-item {{ request()->routeIs('rankings.*') ? 'active' : '' }}">
            <i data-lucide="trophy" class="w-3.5 h-3.5"></i>
            <span>Rankings</span>
        </a>

        <!-- Community -->
        <div class="relative group">
            <button type="button" class="chronicles-nav-item flex items-center gap-1 cursor-pointer">
                <i data-lucide="users" class="w-3.5 h-3.5"></i>
                <span>Community</span>
                <i data-lucide="chevron-down" class="w-3 h-3 text-[#A88B57]"></i>
            </button>
            <div class="hidden group-hover:block absolute top-full left-0 mt-0.5 w-48 bg-[#1F1926] border-2 border-[#5A4A68] rounded-md shadow-2xl p-1.5 z-50">
                <a href="{{ route('guilds.index') }}" class="block px-3 py-1.5 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352B42] hover:text-[#FFD700] rounded">Guild Directory</a>
                <a href="{{ route('woe.index') }}" class="block px-3 py-1.5 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352B42] hover:text-[#FFD700] rounded">War of Emperium</a>
                <a href="{{ route('donations.index') }}" class="block px-3 py-1.5 text-xs text-[#E2D9EB] font-bold font-cinzel hover:bg-[#352B42] hover:text-[#FFD700] rounded">Donations Portal</a>
            </div>
        </div>
    </div>

    <!-- Far Right: Prominent Ornate Gold "Play Now" CTA -->
    <div class="py-1 pl-2">
        <a href="{{ route('downloads.index') }}" class="chronicles-btn-playnow">
            <span>Play Now</span>
            <span class="text-sm">✦</span>
        </a>
    </div>
</nav>
