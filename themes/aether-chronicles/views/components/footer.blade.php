<footer class="chronicles-footer">
    <div class="chronicles-container">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 py-2">
            <!-- Left: Server Logo & Copyright -->
            <div class="text-center md:text-left space-y-1">
                <div class="font-cinzel-decorative font-bold text-sm text-[#F3E5AB] tracking-widest uppercase">
                    {{ \App\Models\SiteSetting::get('server_name', 'AETHER CHRONICLES') }}
                </div>
                <div class="text-xs text-[#8C7F99]">
                    &copy; 2026 {{ \App\Models\SiteSetting::get('server_name', 'Aether Chronicles') }}. All rights reserved. Powered by 
                    <a href="https://github.com/markwillowa/aethercp" target="_blank" class="text-[#D4AF37] hover:underline font-bold">AetherCP</a>
                </div>
            </div>

            <!-- Center: Bottom Nav Links -->
            <div class="flex flex-wrap items-center justify-center gap-3 text-xs font-cinzel">
                <a href="{{ route('home') }}">Home</a>
                <span class="text-[#564766]">|</span>
                <a href="{{ route('news.index') }}">News</a>
                <span class="text-[#564766]">|</span>
                <a href="{{ route('rankings.index') }}">Rankings</a>
                <span class="text-[#564766]">|</span>
                <a href="{{ route('guilds.index') }}">Guilds</a>
                <span class="text-[#564766]">|</span>
                <a href="{{ route('itemdb.index') }}">Database</a>
                <span class="text-[#564766]">|</span>
                <a href="{{ route('downloads.index') }}">Downloads</a>
                @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('faq'))
                    <span class="text-[#564766]">|</span>
                    <a href="{{ route('faq.index') }}">FAQ</a>
                @endif
                @if(app(\App\Services\Module\ModuleManager::class)->isEnabled('support'))
                    <span class="text-[#564766]">|</span>
                    <a href="{{ route('support.index') }}">Support</a>
                @endif
            </div>

            <!-- Right: Follow Us -->
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-cinzel text-[#A396AD]">Follow Us:</span>
                <a href="{{ \App\Models\SiteSetting::get('social_discord', '#') }}" target="_blank" rel="noopener" 
                   class="w-7 h-7 rounded bg-[#5865F2] text-white flex items-center justify-center hover:scale-110 transition-transform shadow" title="Discord">
                    <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                </a>
                <a href="{{ \App\Models\SiteSetting::get('social_facebook', '#') }}" target="_blank" rel="noopener" 
                   class="w-7 h-7 rounded bg-[#1877F2] text-white flex items-center justify-center hover:scale-110 transition-transform shadow" title="Facebook">
                    <i data-lucide="facebook" class="w-3.5 h-3.5"></i>
                </a>
                <a href="{{ \App\Models\SiteSetting::get('social_youtube', '#') }}" target="_blank" rel="noopener" 
                   class="w-7 h-7 rounded bg-[#CD201F] text-white flex items-center justify-center hover:scale-110 transition-transform shadow" title="YouTube">
                    <i data-lucide="youtube" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>
    </div>
</footer>
