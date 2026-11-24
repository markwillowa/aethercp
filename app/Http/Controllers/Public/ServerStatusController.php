<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Contracts\EmulatorInterface;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServerStatusController extends Controller
{
    public function __construct(
        protected EmulatorInterface $emulator
    ) {}

    public function index(Request $request)
    {
        // 1. Live Server Cluster Metrics
        $servers = [
            'login' => [
                'name' => 'Login Gateway',
                'port' => config('aether.login_server.port', 6900),
                'desc' => 'Player Authentication & Session Management',
            ],
            'char' => [
                'name' => 'Character Realm',
                'port' => config('aether.char_server.port', 6121),
                'desc' => 'Character Persistence & Storage Sync',
            ],
            'map' => [
                'name' => 'Midgard Map Server',
                'port' => config('aether.map_server.port', 5121),
                'desc' => 'World Simulation & Real-time Combat',
            ],
        ];

        $overallOnline = true;
        foreach ($servers as $key => &$srv) {
            try {
                $status = $this->emulator->checkServerStatus($key);
                $srv['online'] = !empty($status['online']);
                $srv['latency'] = $status['latency'] ?? 15;
            } catch (\Throwable $e) {
                $srv['online'] = false;
                $srv['latency'] = 0;
            }
            if (!$srv['online']) {
                $overallOnline = false;
            }
        }

        // 2. Population Metrics
        $onlinePlayers = 0;
        try {
            $onlinePlayers = $this->emulator->getOnlinePlayerCount();
        } catch (\Throwable $e) {
            $onlinePlayers = 0;
        }

        $totalAccounts = 0;
        $totalCharacters = 0;
        $totalGuilds = 0;
        try {
            $conn = DB::connection('ragnarok');
            $totalAccounts = $conn->table('login')->count();
            $totalCharacters = $conn->table('char')->count();
            $totalGuilds = $conn->table('guild')->count();
        } catch (\Throwable $e) {
            // fallback
        }

        // 3. Server Specifications & Rates
        $serverInfo = [
            'name' => SiteSetting::get('server_name', config('aether.server_name', 'FreynixRO')),
            'episode' => SiteSetting::get('server_episode', 'Episode XIII: Noghaltz'),
            'type' => SiteSetting::get('server_type', 'Pre-Renewal'),
            'emulator' => SiteSetting::get('server_emulator', 'rAthena'),
            'base_rate' => SiteSetting::get('rate_base', '100x'),
            'job_rate' => SiteSetting::get('rate_job', '100x'),
            'drop_rate' => SiteSetting::get('rate_drop', '100x'),
            'card_rate' => SiteSetting::get('rate_card', '50x'),
            'mvp_card_rate' => SiteSetting::get('rate_mvp_card', '1x'),
            'quest_rate' => SiteSetting::get('rate_quest', '100x'),
            'max_base_level' => SiteSetting::get('max_base_level', '99'),
            'max_job_level' => SiteSetting::get('max_job_level', '70'),
            'max_stats' => SiteSetting::get('max_stats', '99'),
            'max_aspd' => SiteSetting::get('max_aspd', '190'),
            'party_share' => SiteSetting::get('party_share_range', '15 Levels'),
            'woe_schedule' => SiteSetting::get('woe_schedule', 'Wednesday & Saturday 20:00 - 22:00 (GMT+8)'),
            'anti_cheat' => SiteSetting::get('anti_cheat', 'Gepard Shield 3.0 Active'),
            'maintenance_window' => SiteSetting::get('maintenance_window', 'Daily 04:00 - 04:15 GMT+8'),
        ];

        return view('server-status', compact('servers', 'overallOnline', 'onlinePlayers', 'totalAccounts', 'totalCharacters', 'totalGuilds', 'serverInfo'));
    }
}
