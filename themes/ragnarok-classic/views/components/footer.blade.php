<footer class="mt-auto border-t-4 border-[#8C6A2D] bg-[#291A10] text-[#E7D5BE] shadow-inner">
    <div class="ro-container py-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Col 1: Server Lore & Branding -->
            <div class="md:col-span-2 space-y-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded bg-[#8C6A2D] p-0.5 flex items-center justify-center shadow-md">
                        <div class="w-full h-full bg-[#3B2618] rounded flex items-center justify-center">
                            <i data-lucide="shield" class="w-4 h-4 text-[#FFDA89]"></i>
                        </div>
                    </div>
                    <span class="text-lg font-bold font-serif text-[#FFF0D4]">{{ config('aether.name', 'AetherCP') }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#4A3220] border border-[#8C6A2D] text-[#FFDA89] uppercase tracking-wider">
                        Classic Ragnarok
                    </span>
                </div>
                <p class="text-xs text-[#C5B090] leading-relaxed max-w-md">
                    {{ config('aether.description') }} Embark on a nostalgic journey through Rune-Midgard. Re-experience the golden age of Ragnarok Online featuring authentic mechanics, balanced guild warfare, and an active player community.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-4 text-xs text-[#E6C280]">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="cpu" class="w-3.5 h-3.5 text-[#FFDA89]"></i>
                        <span>Emulator:</span>
                        <strong class="uppercase text-[#FFF0D4]">{{ config('emulator.driver') }}</strong>
                    </span>
                    <span class="text-[#5A3F2A]">&bull;</span>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-[#FFDA89]"></i>
                        <span>System:</span>
                        <strong class="text-[#FFF0D4]">{{ config('emulator.renewal') ? 'Renewal' : 'Pre-Renewal' }}</strong>
                    </span>
                </div>
            </div>

            <!-- Col 2: Navigation Links -->
            <div>
                <h4 class="text-xs font-serif font-bold uppercase tracking-wider text-[#FFDA89] mb-3 border-b border-[#4A3220] pb-1.5 flex items-center gap-1.5">
                    <i data-lucide="compass" class="w-3.5 h-3.5 text-[#E6C280]"></i>
                    <span>Midgard Portals</span>
                </h4>
                <ul class="space-y-1.5 text-xs text-[#C5B090]">
                    <li><a href="{{ route('home') }}" class="hover:text-[#FFDA89] transition-colors flex items-center gap-1"><i data-lucide="chevron-right" class="w-3 h-3 text-[#7D5D39]"></i> Server Home</a></li>
                    <li><a href="{{ route('news.index') }}" class="hover:text-[#FFDA89] transition-colors flex items-center gap-1"><i data-lucide="chevron-right" class="w-3 h-3 text-[#7D5D39]"></i> Prontera News</a></li>
                    <li><a href="{{ route('downloads.index') }}" class="hover:text-[#FFDA89] transition-colors flex items-center gap-1"><i data-lucide="chevron-right" class="w-3 h-3 text-[#7D5D39]"></i> Game Downloads</a></li>
                    <li><a href="{{ route('rankings.index') }}" class="hover:text-[#FFDA89] transition-colors flex items-center gap-1"><i data-lucide="chevron-right" class="w-3 h-3 text-[#7D5D39]"></i> Hall of Fame</a></li>
                    <li><a href="{{ route('itemdb.index') }}" class="hover:text-[#FFDA89] transition-colors flex items-center gap-1"><i data-lucide="chevron-right" class="w-3 h-3 text-[#7D5D39]"></i> Item Compendium</a></li>
                </ul>
            </div>

            <!-- Col 3: Server Specifications -->
            <div>
                <h4 class="text-xs font-serif font-bold uppercase tracking-wider text-[#FFDA89] mb-3 border-b border-[#4A3220] pb-1.5 flex items-center gap-1.5">
                    <i data-lucide="feather" class="w-3.5 h-3.5 text-[#E6C280]"></i>
                    <span>Realm Rates</span>
                </h4>
                <ul class="space-y-1.5 text-xs text-[#C5B090]">
                    <li class="flex justify-between py-0.5 border-b border-[#3B2516]">
                        <span>Base EXP:</span>
                        <strong class="text-[#FFDA89]">{{ config('aether.rates.base') }}</strong>
                    </li>
                    <li class="flex justify-between py-0.5 border-b border-[#3B2516]">
                        <span>Job EXP:</span>
                        <strong class="text-[#FFDA89]">{{ config('aether.rates.job') }}</strong>
                    </li>
                    <li class="flex justify-between py-0.5 border-b border-[#3B2516]">
                        <span>Drop Multiplier:</span>
                        <strong class="text-[#FFDA89]">{{ config('aether.rates.drop') }}</strong>
                    </li>
                    <li class="flex justify-between py-0.5 border-b border-[#3B2516]">
                        <span>Max Levels:</span>
                        <strong class="text-[#E7D5BE]">{{ config('aether.rates.max_base_level') }} / {{ config('aether.rates.max_job_level') }}</strong>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="border-t border-[#4A3220] pt-4 flex flex-col sm:flex-row items-center justify-between text-[11px] text-[#A68F73] gap-2">
            <p>&copy; {{ date('Y') }} <strong class="text-[#E7D5BE]">{{ config('aether.server_name') }}</strong>. All rights reserved.</p>
            <p class="flex items-center gap-1.5">
                <span>Powered by</span>
                <strong class="text-[#FFDA89]">AetherCP</strong>
                <span>&bull; Ragnarok Classic Theme</span>
            </p>
        </div>
    </div>
</footer>

