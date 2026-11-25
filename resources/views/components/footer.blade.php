<footer class="relative z-10 border-t border-slate-800/80 bg-[#070a13] mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
            <!-- Col 1: Brand & Bio -->
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-slate-950 font-bold">
                        <i data-lucide="shield" class="w-4 h-4 text-white"></i>
                    </div>
                    <span class="text-lg font-bold aether-gradient-text">{{ config('aether.name', 'AetherCP') }}</span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed max-w-md">
                    {{ config('aether.description') }}
                </p>
                <div class="mt-4 flex items-center space-x-3 text-xs text-slate-500">
                    <span class="flex items-center gap-1">
                        <i data-lucide="cpu" class="w-3.5 h-3.5 text-cyan-400"></i>
                        Emulator: <strong class="text-slate-300 uppercase">{{ config('emulator.driver') }}</strong>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-blue-400"></i>
                        Mode: <strong class="text-slate-300">{{ config('emulator.renewal') ? 'Renewal' : 'Pre-Renewal' }}</strong>
                    </span>
                </div>
            </div>

            <!-- Col 2: Navigation Links -->
            @php $ftModMgr = app(\App\Services\Module\ModuleManager::class); @endphp
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Quick Navigation</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li><a href="{{ route('home') }}" class="hover:text-cyan-400 transition-colors">Server Home</a></li>
                    @if($ftModMgr->isEnabled('news'))
                        <li><a href="{{ route('news.index') }}" class="hover:text-cyan-400 transition-colors">Latest News</a></li>
                    @endif
                    @if($ftModMgr->isEnabled('rankings'))
                        <li><a href="{{ route('rankings.index') }}" class="hover:text-cyan-400 transition-colors">Player Rankings</a></li>
                    @endif
                    @if($ftModMgr->isEnabled('database'))
                        <li><a href="{{ route('itemdb.index') }}" class="hover:text-cyan-400 transition-colors">Database</a></li>
                    @endif
                    @if($ftModMgr->isEnabled('downloads'))
                        <li><a href="{{ route('downloads.index') }}" class="hover:text-cyan-400 transition-colors">Game Downloads</a></li>
                    @endif
                    @if($ftModMgr->isEnabled('faq'))
                        <li><a href="{{ route('faq.index') }}" class="hover:text-cyan-400 transition-colors">Knowledge Base &amp; FAQ</a></li>
                    @endif
                    @if($ftModMgr->isEnabled('support'))
                        <li><a href="{{ route('support.index') }}" class="hover:text-cyan-400 transition-colors">Support Center</a></li>
                    @endif
                </ul>
            </div>

            <!-- Col 3: Community & Rates -->
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Server Info</h4>
                <ul class="space-y-1.5 text-xs text-slate-400">
                    <li class="flex justify-between py-1 border-b border-slate-800/60">
                        <span>Max Base Lv:</span>
                        <strong class="text-slate-200">{{ config('aether.rates.max_base_level') }}</strong>
                    </li>
                    <li class="flex justify-between py-1 border-b border-slate-800/60">
                        <span>Max Job Lv:</span>
                        <strong class="text-slate-200">{{ config('aether.rates.max_job_level') }}</strong>
                    </li>
                    <li class="flex justify-between py-1 border-b border-slate-800/60">
                        <span>Max ASPD:</span>
                        <strong class="text-slate-200">{{ config('aether.rates.max_aspd') }}</strong>
                    </li>
                    <li class="flex justify-between py-1 border-b border-slate-800/60">
                        <span>Base / Job:</span>
                        <strong class="text-cyan-400">{{ config('aether.rates.base') }} / {{ config('aether.rates.job') }}</strong>
                    </li>
                    @if($ftModMgr->isEnabled('server_info'))
                        <li class="pt-2.5">
                            <a href="{{ route('server.status') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-400 text-xs font-semibold hover:bg-emerald-900/50 hover:border-emerald-400 transition-all group">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Live Server Status</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform"></i>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
            <p>© {{ date('Y') }} {{ config('aether.server_name') }}. All rights reserved.</p>
            <p class="flex items-center gap-1.5">
                <span>Powered by</span>
                <span class="text-slate-300 font-semibold">AetherCP</span>
                <span>• Independent RO Control Panel</span>
            </p>
        </div>
    </div>
</footer>

