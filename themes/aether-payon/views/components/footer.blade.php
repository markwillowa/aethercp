@php $payonModMgr = app(\App\Services\Module\ModuleManager::class); @endphp
<footer class="bg-[#263D2C] border-t-4 border-[#493322] text-[#F2E8CF] mt-16">
    <div class="payon-container py-10 space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2 space-y-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded bg-[#493322] border border-[#A88950] flex items-center justify-center text-[#FFF8E8]">
                        <i data-lucide="trees" class="w-4 h-4 text-[#A88950]"></i>
                    </span>
                    <span class="font-cinzel text-lg font-bold text-[#FFF8E8]">{{ config('aether.name', 'AetherCP') }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#493322] border border-[#A88950] text-[#E7D8B5]">Payon Kingdom</span>
                </div>
                <p class="text-xs text-[#E7D8B5] leading-relaxed max-w-md">
                    {{ config('aether.description', 'An enchanting eastern fantasy Ragnarok Online realm nestled among ancient bamboo forests.') }}
                </p>
                <div class="text-xs text-[#A8B08B] flex items-center gap-3">
                    <span>Emulator: <strong class="text-[#FFF8E8] uppercase">{{ config('emulator.driver') }}</strong></span>
                    <span>&bull;</span>
                    <span>Mode: <strong class="text-[#FFF8E8]">{{ config('emulator.renewal') ? 'Renewal' : 'Pre-Renewal' }}</strong></span>
                </div>
            </div>

            <div>
                <h4 class="font-cinzel text-xs font-bold uppercase tracking-wider text-[#A88950] mb-3">Kingdom Links</h4>
                <ul class="space-y-1.5 text-xs text-[#E7D8B5]">
                    <li><a href="{{ route('home') }}" class="hover:text-[#FFF8E8] transition-colors">Sanctuary Home</a></li>
                    <li><a href="{{ route('server.status') }}" class="hover:text-[#FFF8E8] transition-colors">Server Status</a></li>
                    @if($payonModMgr->isEnabled('news'))<li><a href="{{ route('news.index') }}" class="hover:text-[#FFF8E8] transition-colors">Kingdom Notices</a></li>@endif
                    @if($payonModMgr->isEnabled('rankings'))<li><a href="{{ route('rankings.index') }}" class="hover:text-[#FFF8E8] transition-colors">Ranking Hall</a></li>@endif
                    @if($payonModMgr->isEnabled('downloads'))<li><a href="{{ route('downloads.index') }}" class="hover:text-[#FFF8E8] transition-colors">Client Downloads</a></li>@endif
                </ul>
            </div>

            <div>
                <h4 class="font-cinzel text-xs font-bold uppercase tracking-wider text-[#A88950] mb-3">Realm Rates</h4>
                <ul class="space-y-1.5 text-xs text-[#E7D8B5] font-mono">
                    <li class="flex justify-between"><span>Base / Job:</span> <strong class="text-[#FFF8E8]">{{ config('aether.rates.base') }} / {{ config('aether.rates.job') }}</strong></li>
                    <li class="flex justify-between"><span>Drop Rate:</span> <strong class="text-[#FFF8E8]">{{ config('aether.rates.drop') }}</strong></li>
                    <li class="flex justify-between"><span>Max Level:</span> <strong class="text-[#FFF8E8]">{{ config('aether.rates.max_base_level') }} / {{ config('aether.rates.max_job_level') }}</strong></li>
                    <li class="flex justify-between"><span>Max ASPD:</span> <strong class="text-[#FFF8E8]">{{ config('aether.rates.max_aspd') }}</strong></li>
                </ul>
            </div>
        </div>

        <div class="pt-5 border-t border-[#526D3A]/50 flex flex-col sm:flex-row items-center justify-between text-xs text-[#A8B08B] gap-2">
            <p>&copy; {{ date('Y') }} <strong class="text-[#FFF8E8]">{{ config('aether.server_name', 'AetherCP') }}</strong>. All rights reserved.</p>
            <p>Aether Payon Theme &bull; AetherCP Studios</p>
        </div>
    </div>
</footer>
