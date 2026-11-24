@extends('layouts.admin')

@section('admin_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Ranking Engine &amp; rAthena Sync</h1>
            <p class="text-xs text-slate-400 mt-1">Configure leaderboard thresholds, guild scoring weights, anti-abuse rules, and monitor emulator synchronization.</p>
        </div>
        <form method="POST" action="{{ route('admin.rankings.sync') }}">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg flex items-center gap-2 transition-colors">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                <span>Sync &amp; Reconcile Now</span>
            </button>
        </form>
    </div>

    <!-- Sync Health Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold block">rAthena Connection</span>
            <div class="mt-2 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full {{ $syncHealth['ragnarok_connected'] ? 'bg-emerald-400 shadow-[0_0_8px_#34d399]' : 'bg-red-500' }}"></span>
                <span class="text-sm font-bold {{ $syncHealth['ragnarok_connected'] ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ $syncHealth['ragnarok_connected'] ? 'Connected' : 'Disconnected' }}
                </span>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold block">Last Successful Sync</span>
            <p class="mt-2 text-sm font-bold text-white font-mono">
                {{ $syncHealth['last_success_at'] ? $syncHealth['last_success_at']->diffForHumans() : 'Never' }}
            </p>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold block">Verified Events Processed</span>
            <p class="mt-2 text-xl font-black text-cyan-400 font-mono">{{ number_format($syncHealth['total_events_processed']) }}</p>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold block">Rejected / Failed Events</span>
            <p class="mt-2 text-xl font-black text-amber-400 font-mono">
                {{ number_format($syncHealth['total_events_rejected']) }}
                <span class="text-xs text-red-400 font-normal">/ {{ number_format($syncHealth['total_events_failed']) }} failed</span>
            </p>
        </div>
    </div>

    <!-- Configuration Form -->
    <form method="POST" action="{{ route('admin.rankings.settings') }}" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-6">
        @csrf
        <h2 class="text-sm font-bold uppercase tracking-wider text-cyan-400 border-b border-slate-800 pb-3">Leaderboard Rules &amp; Scoring</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Max Base Level</label>
                <input type="number" name="max_base_level" value="{{ $config['max_base_level'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Max Job Level</label>
                <input type="number" name="max_job_level" value="{{ $config['max_job_level'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Homepage Top Limit</label>
                <input type="number" name="homepage_limit" value="{{ $config['homepage_limit'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Leaderboard Page Size</label>
                <input type="number" name="leaderboard_limit" value="{{ $config['leaderboard_limit'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>

            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">PvP Anti-Abuse Cooldown (sec)</label>
                <input type="number" name="pvp_cooldown_seconds" value="{{ $pvpCooldown }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Cache TTL (seconds)</label>
                <input type="number" name="cache_ttl" value="{{ $config['cache_ttl'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Guild Ranking Method</label>
                <select name="guild_method" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                    <option value="combined" {{ $config['guild_method'] === 'combined' ? 'selected' : '' }}>Combined Weighted Score</option>
                    <option value="castles" {{ $config['guild_method'] === 'castles' ? 'selected' : '' }}>Occupied Castles</option>
                    <option value="level" {{ $config['guild_method'] === 'level' ? 'selected' : '' }}>Guild Level</option>
                    <option value="members" {{ $config['guild_method'] === 'members' ? 'selected' : '' }}>Member Count</option>
                </select>
            </div>
            <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
                    <input type="checkbox" name="public_visibility" value="1" {{ $config['public_visibility'] ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                    <span class="font-semibold">Public Leaderboards Enabled</span>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs pt-2 border-t border-slate-800/60">
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Guild Weight: Castles</label>
                <input type="number" name="weight_castles" value="{{ $config['guild_weights']['castles'] ?? 100 }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Guild Weight: Level</label>
                <input type="number" name="weight_level" value="{{ $config['guild_weights']['level'] ?? 10 }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Guild Weight: Members</label>
                <input type="number" name="weight_members" value="{{ $config['guild_weights']['members'] ?? 5 }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs pt-2 border-t border-slate-800/60">
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Excluded Char IDs (comma-separated)</label>
                <input type="text" name="excluded_chars" value="{{ implode(',', $config['excluded_chars']) }}" placeholder="e.g. 150001,150002" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Excluded Account IDs</label>
                <input type="text" name="excluded_accounts" value="{{ implode(',', $config['excluded_accounts']) }}" placeholder="e.g. 2000000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1.5">Excluded Guild IDs</label>
                <input type="text" name="excluded_guilds" value="{{ implode(',', $config['excluded_guilds']) }}" placeholder="e.g. 10001" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
            </div>
        </div>

        <div class="pt-2 border-t border-slate-800/60 text-xs">
            <span class="block text-slate-400 font-semibold mb-2">Enabled Ranking Categories</span>
            <div class="flex flex-wrap gap-4">
                @foreach(['lvl' => 'Base', 'job' => 'Job', 'pvp' => 'PvP', 'mvp' => 'MVP', 'guild' => 'Guild'] as $catKey => $catLabel)
                    <label class="flex items-center gap-2 text-slate-200 cursor-pointer">
                        <input type="checkbox" name="enabled_categories[]" value="{{ $catKey }}" {{ in_array($catKey, $config['enabled_categories'], true) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-cyan-500">
                        <span>{{ $catLabel }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold transition-colors">
                Save Ranking Settings
            </button>
        </div>
    </form>

    <!-- Emulator Bridge Credentials -->
    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 space-y-3 text-xs">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-white">rAthena Event Bridge Endpoint</h2>
            <form method="POST" action="{{ route('admin.rankings.token') }}">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-300 font-semibold border border-slate-700">Regenerate Token</button>
            </form>
        </div>
        <p class="text-slate-400">
            Endpoint: <code class="text-cyan-400 font-mono">POST {{ url('/api/emulator/events') }}</code> • Header: <code class="text-cyan-400 font-mono">X-Aether-Sync-Token: {{ $syncToken }}</code>
        </p>
        <p class="text-slate-500">
            SQL Bridge NPC Script available at: <code class="text-slate-300 font-mono">resources/rathena/aether_ranking_bridge.txt</code>
        </p>
    </div>

    <!-- Recent Sync Logs & Events -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-800 bg-slate-950/60">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">Recent Synchronization Logs</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/50 text-slate-400 text-[10px] uppercase">
                        <tr>
                            <th class="py-2.5 px-4">Type</th>
                            <th class="py-2.5 px-4">Status</th>
                            <th class="py-2.5 px-4">Processed</th>
                            <th class="py-2.5 px-4">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($syncLogs as $log)
                            <tr>
                                <td class="py-2.5 px-4 font-mono text-slate-300">{{ $log->sync_type }}</td>
                                <td class="py-2.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $log->status === 'success' ? 'bg-emerald-950 text-emerald-400' : 'bg-red-950 text-red-400' }}">{{ $log->status }}</span>
                                </td>
                                <td class="py-2.5 px-4 text-slate-300">{{ $log->events_processed }} / {{ $log->events_rejected }} rej</td>
                                <td class="py-2.5 px-4 text-slate-500">{{ $log->synced_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-slate-500">No sync logs yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-800 bg-slate-950/60">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">Recent Game Events</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/50 text-slate-400 text-[10px] uppercase">
                        <tr>
                            <th class="py-2.5 px-4">Event</th>
                            <th class="py-2.5 px-4">Char/Guild</th>
                            <th class="py-2.5 px-4">Status</th>
                            <th class="py-2.5 px-4">Occurred</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($recentEvents as $evt)
                            <tr>
                                <td class="py-2.5 px-4 font-mono text-cyan-400">{{ $evt->event_type }}</td>
                                <td class="py-2.5 px-4 font-mono text-slate-300">#{{ $evt->char_id ?? $evt->guild_id ?? '-' }}</td>
                                <td class="py-2.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $evt->status === 'processed' ? 'bg-emerald-950 text-emerald-400' : ($evt->status === 'rejected' ? 'bg-amber-950 text-amber-400' : 'bg-red-950 text-red-400') }}">{{ $evt->status }}</span>
                                </td>
                                <td class="py-2.5 px-4 text-slate-500">{{ $evt->occurred_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-slate-500">No events recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
