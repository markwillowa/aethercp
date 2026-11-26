<?php

namespace Database\Seeders;

use App\Models\BadgeDefinition;
use App\Models\BadgeTier;
use App\Models\EmulatorSyncLog;
use App\Models\PlayerAchievement;
use App\Models\PlayerProfileSetting;
use App\Models\RankingCharacterStat;
use App\Models\RankingEvent;
use App\Models\RewardDefinition;
use App\Services\Ranking\BadgeService;
use App\Services\Ranking\RankingEngineService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RankingSystemSeeder extends Seeder
{
    /**
     * Populate realistic development/demo ranking stats, events, badges,
     * rewards, and achievements against existing characters in ragnarok.char
     * WITHOUT inserting fabricated characters into the live rAthena database.
     */
    public function run(): void
    {
        $badgeService = app(BadgeService::class);
        $rankingEngine = app(RankingEngineService::class);
        $badgeService->ensureDefaultDefinitionsExist();

        $chars = DB::connection('ragnarok')
            ->table('char')
            ->orderByDesc('base_level')
            ->limit(15)
            ->get();

        if ($chars->isEmpty()) {
            $this->command?->warn('No characters found in ragnarok.char to populate ranking stats.');
            return;
        }

        $cfg = $rankingEngine->getConfig();
        $pvpSamples = [1280, 720, 410, 185, 92, 48, 22, 11];
        $mvpSamples = [1150, 620, 340, 160, 75, 35, 16, 6];
        $baseTimes = [
            Carbon::now()->subDays(14),
            Carbon::now()->subDays(12),
            Carbon::now()->subDays(9),
            Carbon::now()->subDays(6),
            Carbon::now()->subDays(3),
        ];

        foreach ($chars as $idx => $char) {
            $cid = (int) $char->char_id;
            $aid = (int) $char->account_id;
            $pvpKills = $pvpSamples[$idx] ?? max(5, 50 - ($idx * 4));
            $pvpDeaths = max(1, (int) round($pvpKills * 0.28));
            $mvpKills = $mvpSamples[$idx] ?? max(2, 30 - ($idx * 3));

            $maxBaseAt = ((int) $char->base_level >= $cfg['max_base_level'])
                ? ($baseTimes[$idx] ?? Carbon::now()->subDays(1))
                : null;

            $maxJobAt = ((int) $char->job_level >= $cfg['max_job_level'])
                ? ($baseTimes[$idx] ?? Carbon::now()->subDays(1))->copy()->addHours(2)
                : null;

            RankingCharacterStat::updateOrCreate(
                ['char_id' => $cid],
                [
                    'account_id' => $aid,
                    'pvp_kills' => $pvpKills,
                    'pvp_deaths' => $pvpDeaths,
                    'mvp_kills' => $mvpKills,
                    'max_base_reached_at' => $maxBaseAt,
                    'max_base_level_achieved' => $maxBaseAt ? (int) $char->base_level : null,
                    'max_job_reached_at' => $maxJobAt,
                    'max_job_level_achieved' => $maxJobAt ? (int) $char->job_level : null,
                    'last_event_at' => now(),
                ]
            );

            // Seed deduplicated sample events
            if ($maxBaseAt) {
                RankingEvent::firstOrCreate(
                    ['event_uuid' => "seed_max_base_{$cid}"],
                    [
                        'event_type' => 'max_base_level',
                        'char_id' => $cid,
                        'account_id' => $aid,
                        'value' => (int) $char->base_level,
                        'status' => 'processed',
                        'occurred_at' => $maxBaseAt,
                        'processed_at' => $maxBaseAt,
                    ]
                );

                PlayerAchievement::firstOrCreate(
                    ['char_id' => $cid, 'achievement_key' => $idx === 0 ? 'first_max_base' : 'max_base'],
                    [
                        'title' => $idx === 0 ? 'First to Max Level' : 'Reached Max Base Level',
                        'description' => "Achieved Base Level {$char->base_level} on the server.",
                        'category' => 'level',
                        'icon' => $idx === 0 ? 'crown' : 'star',
                        'color' => $idx === 0 ? '#f59e0b' : '#06b6d4',
                        'earned_at' => $maxBaseAt,
                    ]
                );
            }

            if ($pvpKills >= 10) {
                PlayerAchievement::firstOrCreate(
                    ['char_id' => $cid, 'achievement_key' => 'pvp_veteran'],
                    [
                        'title' => 'Arena Challenger',
                        'description' => "Recorded {$pvpKills} verified PvP kills.",
                        'category' => 'pvp',
                        'icon' => 'swords',
                        'color' => '#f43f5e',
                        'earned_at' => now()->subDays(1),
                    ]
                );
            }

            if ($mvpKills >= 5) {
                PlayerAchievement::firstOrCreate(
                    ['char_id' => $cid, 'achievement_key' => 'mvp_hunter'],
                    [
                        'title' => 'Boss Slayer',
                        'description' => "Defeated {$mvpKills} verified MVP bosses.",
                        'category' => 'mvp',
                        'icon' => 'skull',
                        'color' => '#a855f7',
                        'earned_at' => now()->subHours(6),
                    ]
                );
            }

            PlayerProfileSetting::forCharacter($cid, $aid);
        }

        // Seed sample Guilds, Guild Members & Castles in the separate Ragnarok game database
        $this->call(RagnarokGuildSeeder::class);

        // Seed sample configurable milestone rewards (idempotent)
        $firstMaxBadge = BadgeDefinition::where('auto_condition_type', 'first_max_base')->first();
        if ($firstMaxBadge) {
            RewardDefinition::firstOrCreate(
                ['name' => 'First to Max Level Grand Prize'],
                [
                    'category' => 'level',
                    'trigger_type' => 'badge',
                    'badge_id' => $firstMaxBadge->id,
                    'reward_type' => 'zeny',
                    'zeny_amount' => 500000,
                    'policy' => 'one_time',
                    'message' => 'Congratulations on being the first to reach Maximum Base Level!',
                    'is_enabled' => true,
                ]
            );
        }

        $bronze5Pvp = BadgeTier::where('category', 'pvp')->where('tier_name', 'Bronze')->where('stars', 5)->first();
        if ($bronze5Pvp) {
            RewardDefinition::firstOrCreate(
                ['name' => 'PvP Bronze 5-Star Milestone'],
                [
                    'category' => 'pvp',
                    'trigger_type' => 'tier',
                    'badge_tier_id' => $bronze5Pvp->id,
                    'reward_type' => 'zeny',
                    'zeny_amount' => 10000,
                    'policy' => 'one_time',
                    'message' => 'You reached Bronze 5 Stars in PvP!',
                    'is_enabled' => true,
                ]
            );
        }

        // Evaluate and assign all badges + trigger eligible rewards
        $badgeService->recalculateAll();
        $rankingEngine->invalidateCache();

        EmulatorSyncLog::create([
            'sync_type' => 'reconciliation',
            'status' => 'success',
            'events_processed' => $chars->count(),
            'events_rejected' => 0,
            'duration_ms' => 45,
            'synced_at' => now(),
        ]);
    }
}
