<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CharacterEquipmentInventorySeeder extends Seeder
{
    public function run(): void
    {
        $ro = DB::connection('ragnarok');
        $chars = $ro->table('char')->select('char_id', 'account_id', 'name')->limit(6)->get();

        if ($chars->isEmpty()) {
            $this->command?->warn('No characters found in ragnarok.char; skipping equipment & inventory seeding.');
            return;
        }

        // Standard rAthena equip bitmasks:
        // 256=Top Headgear, 16=Armor, 2=Weapon, 32=Shield, 4=Garment, 64=Shoes, 8=Acc R, 128=Acc L
        $equippedSet = [
            ['nameid' => 5013, 'amount' => 1, 'equip' => 256, 'refine' => 7, 'card0' => 4121], // Lord Kaho's Horn +7
            ['nameid' => 2357, 'amount' => 1, 'equip' => 16,  'refine' => 7, 'card0' => 4128], // Valkyrian Armor +7
            ['nameid' => 1220, 'amount' => 1, 'equip' => 2,   'refine' => 9, 'card0' => 4140], // Ice Pick +9
            ['nameid' => 2115, 'amount' => 1, 'equip' => 32,  'refine' => 7, 'card0' => 4058], // Valkyrja's Shield +7
            ['nameid' => 2524, 'amount' => 1, 'equip' => 4,   'refine' => 7, 'card0' => 4133], // Valkyrian Manteau +7
            ['nameid' => 2421, 'amount' => 1, 'equip' => 64,  'refine' => 7, 'card0' => 4131], // Valkyrian Shoes +7
            ['nameid' => 2620, 'amount' => 1, 'equip' => 8,   'refine' => 0, 'card0' => 4064], // Ring of Flame Lord
            ['nameid' => 2620, 'amount' => 1, 'equip' => 128, 'refine' => 0, 'card0' => 4064], // Ring of Flame Lord
        ];

        $inventoryBag = [
            ['nameid' => 504, 'amount' => 150, 'equip' => 0, 'refine' => 0, 'card0' => 0], // White Potion
            ['nameid' => 505, 'amount' => 80,  'equip' => 0, 'refine' => 0, 'card0' => 0], // Blue Potion
            ['nameid' => 522, 'amount' => 50,  'equip' => 0, 'refine' => 0, 'card0' => 0], // Mastela Fruit
            ['nameid' => 601, 'amount' => 100, 'equip' => 0, 'refine' => 0, 'card0' => 0], // Fly Wing
            ['nameid' => 602, 'amount' => 25,  'equip' => 0, 'refine' => 0, 'card0' => 0], // Butterfly Wing
            ['nameid' => 607, 'amount' => 15,  'equip' => 0, 'refine' => 0, 'card0' => 0], // Yggdrasil Berry
        ];

        $seededAccounts = [];

        foreach ($chars as $char) {
            foreach (array_merge($equippedSet, $inventoryBag) as $item) {
                $exists = $ro->table('inventory')
                    ->where('char_id', $char->char_id)
                    ->where('nameid', $item['nameid'])
                    ->where('equip', $item['equip'])
                    ->exists();

                if (!$exists) {
                    $ro->table('inventory')->insert([
                        'char_id' => $char->char_id,
                        'nameid' => $item['nameid'],
                        'amount' => $item['amount'],
                        'equip' => $item['equip'],
                        'identify' => 1,
                        'refine' => $item['refine'],
                        'attribute' => 0,
                        'card0' => $item['card0'],
                        'card1' => 0,
                        'card2' => 0,
                        'card3' => 0,
                        'bound' => 0,
                    ]);
                }
            }

            if (!in_array($char->account_id, $seededAccounts, true)) {
                $seededAccounts[] = $char->account_id;
                foreach ([
                    ['nameid' => 501, 'amount' => 200, 'refine' => 0],
                    ['nameid' => 607, 'amount' => 30,  'refine' => 0],
                    ['nameid' => 5123, 'amount' => 1,  'refine' => 7],
                ] as $stItem) {
                    $exists = $ro->table('storage')
                        ->where('account_id', $char->account_id)
                        ->where('nameid', $stItem['nameid'])
                        ->exists();
                    if (!$exists) {
                        $ro->table('storage')->insert([
                            'account_id' => $char->account_id,
                            'nameid' => $stItem['nameid'],
                            'amount' => $stItem['amount'],
                            'equip' => 0,
                            'identify' => 1,
                            'refine' => $stItem['refine'],
                            'attribute' => 0,
                            'bound' => 0,
                        ]);
                    }
                }
            }
        }

        $this->command?->info('Seeded equipment, inventory, and storage items for ' . $chars->count() . ' character(s).');
    }
}
