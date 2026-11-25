<footer class="relative z-10 border-t border-amber-500/25 bg-[#060913] mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
            <!-- Col 1: Brand & Mythic Realm Bio -->
            <div class="md:col-span-2 space-y-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 flex items-center justify-center text-slate-950 font-bold shadow-md shadow-amber-500/20">
                        <i data-lucide="crown" class="w-4 h-4 text-slate-950"></i>
                    </div>
                    <span class="text-lg font-bold elysian-gold-text">{{ config('aether.name', 'AetherCP') }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold elysian-gold-badge uppercase tracking-wider">Elysian Realm</span>
                </div>
                <p class="text-sm text-slate-300 leading-relaxed max-w-md">
                    {{ config('aether.description') }} Experience an elevated Valkyrie realm with high-rate combat, guild rivalries, and community-driven events.
                </p>
                <div class="pt-1 flex items-center space-x-3 text-xs text-amber-300/80">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="cpu" class="w-3.5 h-3.5 text-amber-400"></i>
                        Emulator: <strong class="text-slate-200 uppercase font-semibold">{{ config('emulator.driver') }}</strong>
                    </span>
                    <span class="text-slate-700">&bull;</span>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-400"></i>
                        Mode: <strong class="text-slate-200 font-semibold">{{ config('emulator.renewal') ? 'Renewal' : 'Pre-Renewal' }}</strong>
                    </span>
                </div>
            </div>

            <!-- Col 2: Navigation Links with Amber Hovers -->
            @php $elysianModMgr = app(\App\Services\Module\ModuleManager::class); @endphp
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-amber-400 mb-3 flex items-center gap-1.5">
                    <i data-lucide="compass" class="w-3.5 h-3.5"></i>
                    <span>Realm Navigation</span>
                </h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li><a href="{{ route('home') }}" class="hover:text-amber-300 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-amber-500/60"></i> Server Home</a></li>
                    <li><a href="{{ route('server.status') }}" class="hover:text-amber-300 transition-colors flex items-center gap-1.5"><i data-lucide="activity" class="w-3 h-3 text-amber-500/60"></i> Server Status</a></li>
                    @if($elysianModMgr->isEnabled('news'))
                        <li><a href="{{ route('news.index') }}" class="hover:text-amber-300 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-amber-500/60"></i> Chronicle Dispatches</a></li>
                    @endif
                    @if($elysianModMgr->isEnabled('rankings'))
                        <li><a href="{{ route('rankings.index') }}" class="hover:text-amber-300 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-amber-500/60"></i> Hall of Fame</a></li>
                    @endif
                    @if($elysianModMgr->isEnabled('database'))
                        <li><a href="{{ route('itemdb.index') }}" class="hover:text-amber-300 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-amber-500/60"></i> Compendium Database</a></li>
                    @endif
                    @if($elysianModMgr->isEnabled('downloads'))
                        <li><a href="{{ route('downloads.index') }}" class="hover:text-amber-300 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3 h-3 text-amber-500/60"></i> Game Downloads</a></li>
                    @endif
                </ul>
            </div>

            <!-- Col 3: Mythic Server Parameters -->
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-amber-400 mb-3 flex items-center gap-1.5">
                    <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                    <span>Realm Parameters</span>
                </h4>
                <ul class="space-y-1.5 text-xs text-slate-300">
                    <li class="flex justify-between py-1 border-b border-slate-800/80">
                        <span class="text-slate-400">Max Base Lv:</span>
                        <strong class="text-amber-200 font-semibold">{{ config('aether.rates.max_base_level') }}</strong>
                    </li>
                    <li class="flex justify-between py-1 border-b border-slate-800/80">
                        <span class="text-slate-400">Max Job Lv:</span>
                        <strong class="text-amber-200 font-semibold">{{ config('aether.rates.max_job_level') }}</strong>
                    </li>
                    <li class="flex justify-between py-1 border-b border-slate-800/80">
                        <span class="text-slate-400">Max ASPD:</span>
                        <strong class="text-amber-200 font-semibold">{{ config('aether.rates.max_aspd') }}</strong>
                    </li>
                    <li class="flex justify-between py-1 border-b border-slate-800/80">
                        <span class="text-slate-400">Rates (B/J/D):</span>
                        <strong class="text-amber-400 font-bold">{{ config('aether.rates.base') }} / {{ config('aether.rates.job') }} / {{ config('aether.rates.drop') }}</strong>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-amber-500/20 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-3">
            <p>&copy; {{ date('Y') }} <strong class="text-amber-300/90 font-medium">{{ config('aether.server_name') }}</strong>. Elysian Mythic Realm.</p>
            <p class="flex items-center gap-1.5 text-slate-400">
                <span>Sanctuary by</span>
                <span class="text-amber-300 font-semibold">AetherCP</span>
                <span>&bull; Elysian Mythic Theme</span>
            </p>
        </div>
    </div>
</footer>

