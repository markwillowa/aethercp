<?php

namespace Database\Seeders;

use App\Models\RankingCharacterStat;
use App\Models\RankingEvent;
use App\Services\Ranking\BadgeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RankingPvpMvpSeeder extends Seeder
{
    /**
     * Seed realistic PvP and MVP ranking stats for existing rAthena characters
     * and recalculate PvP/MVP tier badges.
     */
    public function run(): void
    {
        $chars = DB::connection('ragnarok')
            ->table('char as c')
            ->join('login as l', 'c.account_id', '=', 'l.account_id')
            ->where('l.group_id', '<', (int) config('emulator.gm_level_threshold', 1))
            ->select('c.char_id', 'c.account_id', 'c.name')
            ->orderByDesc('c.base_level')
            ->limit(15)
            ->get();

        if ($chars->isEmpty()) {
            $chars = DB::connection('ragnarok')
                ->table('char')
                ->select('char_id', 'account_id', 'name')
                ->orderByDesc('base_level')
                ->limit(15)
                ->get();
        }

        if ($chars->isEmpty()) {
            $this->command?->warn('No characters found in ragnarok.char table. Skipping PvP/MVP ranking seeder.');
            return;
        }

        $pvpPresets = [1250, 860, 540, 320, 210, 155, 110, 80, 55, 30, 18, 12, 6, 3, 1];
        $mvpPresets = [620, 410, 290, 185, 130, 95, 72, 50, 34, 22, 15, 9, 5, 2, 1];

        foreach ($chars as $idx => $char) {
            $pvpKills = $pvpPresets[$idx] ?? max(1, 20 - $idx);
            $pvpDeaths = max(1, (int) floor($pvpKills * 0.35));
            $mvpKills = $mvpPresets[$idx] ?? max(1, 10 - $idx);

            RankingCharacterStat::updateOrCreate(
                ['char_id' => (int) $char->char_id],
                [
                    'account_id' => (int) $char->account_id,
                    'pvp_kills' => $pvpKills,
                    'pvp_deaths' => $pvpDeaths,
                    'mvp_kills' => $mvpKills,
                    'last_event_at' => now()->subMinutes($idx * 15),
                ]
            );

            RankingEvent::firstOrCreate(
                ['event_uuid' => 'seed-pvp-' . $char->char_id],
                [
                    'event_type' => 'pvp_kill',
                    'char_id' => (int) $char->char_id,
                    'account_id' => (int) $char->account_id,
                    'value' => $pvpKills,
                    'status' => 'processed',
                    'occurred_at' => now()->subMinutes($idx * 15),
                    'processed_at' => now(),
                ]
            );

            RankingEvent::firstOrCreate(
                ['event_uuid' => 'seed-mvp-' . $char->char_id],
                [
                    'event_type' => 'mvp_kill',
                    'char_id' => (int) $char->char_id,
                    'account_id' => (int) $char->account_id,
                    'mob_id' => 1039, // Baphomet
                    'value' => $mvpKills,
                    'status' => 'processed',
                    'occurred_at' => now()->subMinutes($idx * 20),
                    'processed_at' => now(),
                ]
            );
        }

        app(BadgeService::class)->recalculateAll();
        $this->command?->info('Seeded PvP and MVP ranking stats for ' . $chars->count() . ' characters and recalculated badges.');
    }
}
