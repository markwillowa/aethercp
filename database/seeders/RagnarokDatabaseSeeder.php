<?php

namespace Database\Seeders;

use App\Services\Emulator\EmulatorPasswordHasher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RagnarokDatabaseSeeder extends Seeder
{
    /**
     * Seed starter accounts into the Ragnarok game server `login` table.
     */
    public function run(): void
    {
        try {
            if (!config('database.connections.ragnarok.database')) {
                $this->command?->warn('Ragnarok database connection not configured. Skipping RagnarokDatabaseSeeder.');
                return;
            }

            $roDb = DB::connection('ragnarok');
            $hasher = app(EmulatorPasswordHasher::class);

            $accounts = [
                [
                    'account_id' => 1,
                    'userid' => 's1',
                    'plain_pass' => 'athena',
                    'sex' => 'M',
                    'email' => 'athena@athena.com',
                    'group_id' => 0,
                    'birthdate' => '2000-01-01',
                ],
                [
                    'account_id' => 2000000,
                    'userid' => 'malda009',
                    'plain_pass' => 'Maki0913$',
                    'sex' => 'M',
                    'email' => 'owner@freynix.local',
                    'group_id' => 99,
                    'birthdate' => '1995-09-13',
                ],
                [
                    'account_id' => 2000001,
                    'userid' => 'admin',
                    'plain_pass' => 'Admin1234!',
                    'sex' => 'M',
                    'email' => 'admin@freynix.local',
                    'group_id' => 99,
                    'birthdate' => '1990-01-01',
                ],
                [
                    'account_id' => 2000002,
                    'userid' => 'rune_smith',
                    'plain_pass' => 'Password123!',
                    'sex' => 'M',
                    'email' => 'runesmith@aether.local',
                    'group_id' => 0,
                    'birthdate' => '2000-01-01',
                ],
                [
                    'account_id' => 2000003,
                    'userid' => 'kafra_trader',
                    'plain_pass' => 'Password123!',
                    'sex' => 'F',
                    'email' => 'trader@aether.local',
                    'group_id' => 0,
                    'birthdate' => '2000-01-01',
                ],
                [
                    'account_id' => 2000004,
                    'userid' => 'card_collector',
                    'plain_pass' => 'Password123!',
                    'sex' => 'M',
                    'email' => 'cards@aether.local',
                    'group_id' => 0,
                    'birthdate' => '2000-01-01',
                ],
                [
                    'account_id' => 2000005,
                    'userid' => 'alchemist_pro',
                    'plain_pass' => 'Password123!',
                    'sex' => 'F',
                    'email' => 'alchemist@aether.local',
                    'group_id' => 0,
                    'birthdate' => '2000-01-01',
                ],
            ];

            foreach ($accounts as $acc) {
                $hashedPass = $hasher->hash($acc['plain_pass']);

                $exists = $roDb->table('login')
                    ->where('account_id', $acc['account_id'])
                    ->orWhereRaw('LOWER(userid) = LOWER(?)', [$acc['userid']])
                    ->first();

                if ($exists) {
                    $roDb->table('login')
                        ->where('account_id', $exists->account_id)
                        ->update([
                            'userid' => $acc['userid'],
                            'user_pass' => $hashedPass,
                            'group_id' => $acc['group_id'],
                            'email' => $acc['email'],
                        ]);
                } else {
                    $roDb->table('login')->insert([
                        'account_id' => $acc['account_id'],
                        'userid' => $acc['userid'],
                        'user_pass' => $hashedPass,
                        'sex' => $acc['sex'],
                        'email' => $acc['email'],
                        'group_id' => $acc['group_id'],
                        'state' => 0,
                        'character_slots' => 9,
                        'birthdate' => $acc['birthdate'],
                        'pincode' => '',
                        'last_ip' => '127.0.0.1',
                    ]);
                }
            }

            $this->command?->info('Ragnarok database login accounts seeded successfully.');
        } catch (\Throwable $e) {
            $this->command?->warn('Could not seed Ragnarok login table: ' . $e->getMessage());
        }
    }
}
