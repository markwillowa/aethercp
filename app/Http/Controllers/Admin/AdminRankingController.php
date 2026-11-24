<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\EmulatorSyncLog;
use App\Models\RankingEvent;
use App\Models\SiteSetting;
use App\Services\Ranking\RankingEngineService;
use App\Services\Ranking\RankingSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminRankingController extends Controller
{
    public function __construct(
        protected RankingEngineService $rankingEngine,
        protected RankingSyncService $syncService
    ) {}

    public function index()
    {
        $config = $this->rankingEngine->getConfig();
        $syncHealth = $this->syncService->getSyncHealth();
        $syncLogs = EmulatorSyncLog::latest('synced_at')->limit(15)->get();
        $recentEvents = RankingEvent::latest('occurred_at')->limit(20)->get();

        $pvpCooldown = (int) SiteSetting::get('ranking_pvp_cooldown_seconds', 300);
        $syncToken = (string) SiteSetting::get('ranking_sync_token', '');

        if ($syncToken === '') {
            $syncToken = Str::random(40);
            SiteSetting::set('ranking_sync_token', $syncToken);
        }

        return view('admin.rankings.index', compact(
            'config',
            'syncHealth',
            'syncLogs',
            'recentEvents',
            'pvpCooldown',
            'syncToken'
        ));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'max_base_level' => 'required|integer|min:1|max:9999',
            'max_job_level' => 'required|integer|min:1|max:999',
            'homepage_limit' => 'required|integer|min:1|max:25',
            'leaderboard_limit' => 'required|integer|min:5|max:100',
            'cache_ttl' => 'required|integer|min:0|max:3600',
            'guild_method' => 'required|in:castles,level,members,combined',
            'weight_castles' => 'required|integer|min:0|max:10000',
            'weight_level' => 'required|integer|min:0|max:10000',
            'weight_members' => 'required|integer|min:0|max:10000',
            'pvp_cooldown_seconds' => 'required|integer|min:0|max:86400',
            'enabled_categories' => 'nullable|array',
            'excluded_chars' => 'nullable|string|max:1000',
            'excluded_accounts' => 'nullable|string|max:1000',
            'excluded_guilds' => 'nullable|string|max:1000',
        ]);

        SiteSetting::set('ranking_max_base_level', (string) $validated['max_base_level']);
        SiteSetting::set('ranking_max_job_level', (string) $validated['max_job_level']);
        SiteSetting::set('ranking_homepage_limit', (string) $validated['homepage_limit']);
        SiteSetting::set('ranking_leaderboard_limit', (string) $validated['leaderboard_limit']);
        SiteSetting::set('ranking_cache_ttl', (string) $validated['cache_ttl']);
        SiteSetting::set('ranking_guild_method', $validated['guild_method']);
        SiteSetting::set('ranking_guild_weights', json_encode([
            'castles' => (int) $validated['weight_castles'],
            'level' => (int) $validated['weight_level'],
            'members' => (int) $validated['weight_members'],
        ]));
        SiteSetting::set('ranking_pvp_cooldown_seconds', (string) $validated['pvp_cooldown_seconds']);
        SiteSetting::set('ranking_public_visibility', $request->has('public_visibility') ? '1' : '0');
        SiteSetting::set('ranking_enabled_categories', json_encode($validated['enabled_categories'] ?? ['lvl', 'job', 'pvp', 'mvp', 'guild']));
        SiteSetting::set('ranking_excluded_chars', trim((string) ($validated['excluded_chars'] ?? '')));
        SiteSetting::set('ranking_excluded_accounts', trim((string) ($validated['excluded_accounts'] ?? '')));
        SiteSetting::set('ranking_excluded_guilds', trim((string) ($validated['excluded_guilds'] ?? '')));

        $this->rankingEngine->invalidateCache();
        ActivityLog::log('admin.rankings_update', 'Updated Ranking Engine configuration & anti-abuse rules.');

        return back()->with('success', 'Ranking Engine configuration saved and leaderboard cache refreshed.');
    }

    public function syncNow()
    {
        $res = $this->syncService->runFullSync();
        ActivityLog::log('admin.rankings_sync', "Triggered manual ranking sync ({$res['status']}, processed: {$res['processed']}, rejected: {$res['rejected']}).");

        if ($res['status'] === 'success') {
            return back()->with('success', "Synchronization complete! Processed {$res['processed']} updates in {$res['duration_ms']}ms.");
        }

        return back()->with('error', 'Synchronization encountered an issue: ' . ($res['error'] ?? 'Unknown error'));
    }

    public function regenerateToken()
    {
        $token = Str::random(40);
        SiteSetting::set('ranking_sync_token', $token);
        ActivityLog::log('admin.rankings_token', 'Regenerated rAthena emulator sync token.');

        return back()->with('success', 'New emulator event sync token generated.');
    }
}
