<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RagnarokGuildSeeder extends Seeder
{
    /**
     * Seed sample Guilds, Guild Members & Castles into the Ragnarok game database (`ragnarok` connection).
     */
    public function run(): void
    {
        if (!config('database.connections.ragnarok.database')) {
            $this->command?->warn('Ragnarok database connection not configured. Skipping RagnarokGuildSeeder.');
            return;
        }

        $ragnarokDb = DB::connection('ragnarok');
        $guildDefs = [
            [
                'guild_id' => 1,
                'name' => 'Valhalla Ascendant',
                'master_id' => 150001,
                'master_name' => 'AetherKnight',
                'guild_lv' => 50,
                'exp' => 95000000,
                'members' => [150001, 150002],
                'castles' => [1, 2],
            ],
            [
                'guild_id' => 2,
                'name' => 'Ironclad Syndicate',
                'master_id' => 150005,
                'master_name' => 'ThorinSmith',
                'guild_lv' => 45,
                'exp' => 62000000,
                'members' => [150005, 150006],
                'castles' => [3],
            ],
            [
                'guild_id' => 3,
                'name' => 'Shadow Stalkers',
                'master_id' => 150007,
                'master_name' => 'ShadowSniper',
                'guild_lv' => 40,
                'exp' => 41000000,
                'members' => [150007, 150008],
                'castles' => [4],
            ],
            [
                'guild_id' => 4,
                'name' => 'Arcane Dominion',
                'master_id' => 150004,
                'master_name' => 'Archmage',
                'guild_lv' => 35,
                'exp' => 28000000,
                'members' => [150004, 150003],
                'castles' => [],
            ],
        ];

        foreach ($guildDefs as $g) {
            $ragnarokDb->table('guild')->updateOrInsert(
                ['guild_id' => $g['guild_id']],
                [
                    'name' => $g['name'],
                    'char_id' => $g['master_id'],
                    'master' => $g['master_name'],
                    'guild_lv' => $g['guild_lv'],
                    'connect_member' => count($g['members']),
                    'max_member' => 36,
                    'average_lv' => 99,
                    'exp' => $g['exp'],
                    'next_exp' => $g['exp'] + 1000000,
                    'skill_point' => 10,
                    'mes1' => 'Glory to the guild!',
                    'mes2' => 'War of Emperium ready.',
                    'emblem_len' => 0,
                    'emblem_id' => 0,
                    'last_master_change' => 0,
                ]
            );

            foreach ($g['members'] as $pos => $memCharId) {
                $ragnarokDb->table('guild_member')->updateOrInsert(
                    ['guild_id' => $g['guild_id'], 'char_id' => $memCharId],
                    [
                        'exp' => 100000,
                        'position' => $pos,
                    ]
                );

                $ragnarokDb->table('char')->where('char_id', $memCharId)->update(['guild_id' => $g['guild_id']]);
            }

            foreach ($g['castles'] as $cId) {
                $ragnarokDb->table('guild_castle')->updateOrInsert(
                    ['castle_id' => $cId],
                    [
                        'guild_id' => $g['guild_id'],
                        'economy' => 50,
                        'defense' => 50,
                        'triggerE' => 0,
                        'triggerD' => 0,
                        'nextTime' => 0,
                        'payTime' => 0,
                        'createTime' => 0,
                        'visibleC' => 1,
                    ]
                );
            }
        }
    }
}
