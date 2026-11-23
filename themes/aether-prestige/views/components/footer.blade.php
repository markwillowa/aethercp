<footer class="prestige-footer">
    <div class="prestige-container">
        <!-- 4-Column Grid -->
        <div class="prestige-footer-grid">
            <!-- Brand Column -->
            <div class="space-y-4">
                <a href="{{ route('home') }}" class="font-cinzel text-xl font-bold tracking-luxury text-white text-decoration-none">
                    {{ config('aether.server_name', 'MYTHERA') }}
                </a>
                <p class="text-xs text-[#C6A76A] tracking-wider font-cinzel">
                    "Elevate Your World. Define Your Legacy."
                </p>
                <p class="text-xs text-[#788394] leading-relaxed max-w-sm">
                    {{ config('aether.description', 'An elite Ragnarok Online private server realm engineered for competitive guilds, balanced PvP, and monumental instances.') }}
                </p>
                
                <!-- Social Media Icons (matching reference image) -->
                <div class="flex items-center gap-3 pt-2">
                    @if(config('aether.community.facebook'))
                        <a href="{{ config('aether.community.facebook') }}" target="_blank" rel="noopener" class="w-9 h-9 rounded bg-[#151B26] border border-white/10 hover:border-[#C6A76A] flex items-center justify-center text-[#AAB3C1] hover:text-[#C6A76A] transition-colors">
                            <i data-lucide="facebook" class="w-4 h-4"></i>
                        </a>
                    @else
                        <span class="w-9 h-9 rounded bg-[#151B26] border border-white/10 flex items-center justify-center text-[#AAB3C1]"><i data-lucide="facebook" class="w-4 h-4"></i></span>
                    @endif

                    @if(config('aether.community.discord'))
                        <a href="{{ config('aether.community.discord') }}" target="_blank" rel="noopener" class="w-9 h-9 rounded bg-[#151B26] border border-white/10 hover:border-[#C6A76A] flex items-center justify-center text-[#AAB3C1] hover:text-[#C6A76A] transition-colors">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                        </a>
                    @endif

                    <span class="w-9 h-9 rounded bg-[#151B26] border border-white/10 flex items-center justify-center text-[#AAB3C1]"><i data-lucide="twitter" class="w-4 h-4"></i></span>

                    @if(config('aether.community.youtube'))
                        <a href="{{ config('aether.community.youtube') }}" target="_blank" rel="noopener" class="w-9 h-9 rounded bg-[#151B26] border border-white/10 hover:border-[#C6A76A] flex items-center justify-center text-[#AAB3C1] hover:text-[#C6A76A] transition-colors">
                            <i data-lucide="youtube" class="w-4 h-4"></i>
                        </a>
                    @else
                        <span class="w-9 h-9 rounded bg-[#151B26] border border-white/10 flex items-center justify-center text-[#AAB3C1]"><i data-lucide="youtube" class="w-4 h-4"></i></span>
                    @endif
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="space-y-3">
                <h4 class="text-xs uppercase tracking-wider font-bold text-white font-cinzel">Navigation</h4>
                <ul class="space-y-2 text-xs text-[#AAB3C1] list-none p-0 m-0">
                    <li><a href="{{ route('home') }}" class="hover:text-[#C6A76A] transition-colors text-decoration-none">Home Realm</a></li>
                    <li><a href="{{ route('news.index') }}" class="hover:text-[#C6A76A] transition-colors text-decoration-none">Announcements</a></li>
                    <li><a href="{{ route('rankings.index') }}" class="hover:text-[#C6A76A] transition-colors text-decoration-none">Hall of Fame</a></li>
                    <li><a href="{{ route('guilds.index') }}" class="hover:text-[#C6A76A] transition-colors text-decoration-none">Guild Directory</a></li>
                    <li><a href="{{ route('woe.index') }}" class="hover:text-[#C6A76A] transition-colors text-decoration-none">War of Emperium</a></li>
                </ul>
            </div>

            <!-- Resources & Database -->
            <div class="space-y-3">
                <h4 class="text-xs uppercase tracking-wider font-bold text-white font-cinzel">Resources</h4>
                <ul class="space-y-2 text-xs text-[#AAB3C1] list-none p-0 m-0">
                    <li><a href="{{ route('itemdb.index') }}" class="hover:text-[#C6A76A] transition-colors text-decoration-none">Item Database</a></li>
                    <li><a href="{{ route('downloads.index') }}" class="hover:text-[#C6A76A] transition-colors text-decoration-none">Client Downloads</a></li>
                    <li><a href="{{ route('donations.index') }}" class="hover:text-[#C6A76A] transition-colors text-decoration-none">Cash Shop Points</a></li>
                    <li><a href="{{ route('characters.online') }}" class="hover:text-[#C6A76A] transition-colors text-decoration-none">Active Adventurers</a></li>
                </ul>
            </div>

            <!-- Server Meta & Specs -->
            <div class="space-y-3">
                <h4 class="text-xs uppercase tracking-wider font-bold text-white font-cinzel">Server Meta</h4>
                <div class="space-y-2 text-xs text-[#AAB3C1]">
                    <div class="flex items-center justify-between border-b border-white/[0.06] pb-1.5">
                        <span>Base / Job:</span>
                        <strong class="text-[#D8BE8A]">{{ config('aether.rates.base') }} / {{ config('aether.rates.job') }}</strong>
                    </div>
                    <div class="flex items-center justify-between border-b border-white/[0.06] pb-1.5">
                        <span>Drop Rates:</span>
                        <strong class="text-[#D8BE8A]">{{ config('aether.rates.drop') }} (Cards: {{ config('aether.rates.card') }})</strong>
                    </div>
                    <div class="flex items-center justify-between border-b border-white/[0.06] pb-1.5">
                        <span>Max Level:</span>
                        <strong class="text-white">{{ config('aether.rates.max_base_level', 260) }} / {{ config('aether.rates.max_job_level', 55) }}</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Emulator:</span>
                        <strong class="text-[#2DD4BF] uppercase">{{ config('emulator.driver', 'rAthena') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright Row -->
        <div class="prestige-footer-bottom">
            <p class="m-0">© {{ date('Y') }} {{ config('aether.server_name', 'MYTHERA') }}. All rights reserved.</p>
            <p class="m-0 flex items-center gap-2">
                <span>Designed for</span>
                <span class="text-[#C6A76A] font-bold font-cinzel">Aether Prestige</span>
                <span class="text-white/20">•</span>
                <span>Flagship Edition</span>
            </p>
        </div>
    </div>
</footer>
