<?php

namespace Database\Seeders;

use App\Models\MarketplaceItem;
use App\Models\MarketplaceListing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketplaceTestSeeder extends Seeder
{
    public function run(): void
    {
        $conn = DB::connection('ragnarok');

        echo "Seeding Characters, Zeny, Cash Points, and Kafra Storage...\n";

        $accounts = [
            [
                'account_id' => 2000000,
                'chars' => [
                    [
                        'char_id' => 150001,
                        'name' => 'AetherKnight',
                        'class' => 4008, // Lord Knight
                        'base_level' => 99,
                        'job_level' => 70,
                        'zeny' => 75000000,
                    ],
                    [
                        'char_id' => 150002,
                        'name' => 'Valkyrie',
                        'class' => 4010, // High Priest
                        'base_level' => 98,
                        'job_level' => 70,
                        'zeny' => 30000000,
                    ]
                ],
                'cashpoints' => 50000,
            ],
            [
                'account_id' => 2000001,
                'chars' => [
                    [
                        'char_id' => 150003,
                        'name' => 'Freynix',
                        'class' => 4013, // Assassin Cross
                        'base_level' => 99,
                        'job_level' => 70,
                        'zeny' => 80000000,
                    ],
                    [
                        'char_id' => 150004,
                        'name' => 'Archmage',
                        'class' => 4009, // High Wizard
                        'base_level' => 97,
                        'job_level' => 70,
                        'zeny' => 25000000,
                    ]
                ],
                'cashpoints' => 50000,
            ],
        ];

        // 1. Seed Characters & Cash Points
        foreach ($accounts as $acc) {
            $accId = $acc['account_id'];

            // Cash points
            $conn->table('acc_reg_num')->updateOrInsert(
                ['account_id' => $accId, 'key' => '#CASHPOINTS', 'index' => 0],
                ['value' => $acc['cashpoints']]
            );

            // Also check global_acc_reg_num
            try {
                if ($conn->getSchemaBuilder()->hasTable('global_acc_reg_num')) {
                    $conn->table('global_acc_reg_num')->updateOrInsert(
                        ['account_id' => $accId, 'key' => '#CASHPOINTS', 'index' => 0],
                        ['value' => $acc['cashpoints']]
                    );
                }
            } catch (\Throwable $e) {}

            $charNum = 0;
            foreach ($acc['chars'] as $c) {
                $exists = $conn->table('char')->where('char_id', $c['char_id'])->exists();
                if (!$exists) {
                    $conn->table('char')->insert([
                        'char_id' => $c['char_id'],
                        'account_id' => $accId,
                        'char_num' => $charNum++,
                        'name' => $c['name'],
                        'class' => $c['class'],
                        'base_level' => $c['base_level'],
                        'job_level' => $c['job_level'],
                        'base_exp' => 0,
                        'job_exp' => 0,
                        'zeny' => $c['zeny'],
                        'str' => 99,
                        'agi' => 90,
                        'vit' => 80,
                        'int' => 40,
                        'dex' => 80,
                        'luk' => 30,
                        'max_hp' => 15000,
                        'hp' => 15000,
                        'max_sp' => 1200,
                        'sp' => 1200,
                        'status_point' => 100,
                        'skill_point' => 50,
                        'hair' => 1,
                        'hair_color' => 2,
                        'clothes_color' => 0,
                        'last_map' => 'prontera',
                        'last_x' => 156,
                        'last_y' => 191,
                        'save_map' => 'prontera',
                        'save_x' => 156,
                        'save_y' => 191,
                        'online' => 0,
                    ]);
                    echo "Inserted character: {$c['name']} (ID: {$c['char_id']}) with " . number_format($c['zeny']) . " Zeny.\n";
                } else {
                    $conn->table('char')->where('char_id', $c['char_id'])->update([
                        'zeny' => $c['zeny'],
                        'base_level' => $c['base_level'],
                        'job_level' => $c['job_level'],
                        'online' => 0,
                    ]);
                    echo "Updated character: {$c['name']} (ID: {$c['char_id']}) with " . number_format($c['zeny']) . " Zeny.\n";
                }
            }

            // 2. Seed Kafra Storage Items
            // Remove previous test storage items for this account to avoid duplicate clutter
            $conn->table('storage')->where('account_id', $accId)->delete();

            $storageItems = [
                ['nameid' => 1107, 'amount' => 1, 'refine' => 7, 'identify' => 1], // +7 Blade
                ['nameid' => 1119, 'amount' => 1, 'refine' => 8, 'identify' => 1], // +8 Tsurugi
                ['nameid' => 1123, 'amount' => 1, 'refine' => 9, 'identify' => 1], // +9 Haedonggum
                ['nameid' => 2101, 'amount' => 1, 'refine' => 6, 'identify' => 1], // +6 Guard
                ['nameid' => 2226, 'amount' => 1, 'refine' => 4, 'identify' => 1], // +4 Cap
                ['nameid' => 501,  'amount' => 500, 'refine' => 0, 'identify' => 1], // 500x Red Potion
                ['nameid' => 504,  'amount' => 250, 'refine' => 0, 'identify' => 1], // 250x White Potion
                ['nameid' => 607,  'amount' => 50,  'refine' => 0, 'identify' => 1], // 50x Yggdrasil Berry
                ['nameid' => 714,  'amount' => 5,   'refine' => 0, 'identify' => 1], // 5x Emperium
                ['nameid' => 984,  'amount' => 100, 'refine' => 0, 'identify' => 1], // 100x Oridecon
                ['nameid' => 985,  'amount' => 100, 'refine' => 0, 'identify' => 1], // 100x Elunium
                ['nameid' => 4001, 'amount' => 2,   'refine' => 0, 'identify' => 1], // 2x Poring Card
                ['nameid' => 4142, 'amount' => 1,   'refine' => 0, 'identify' => 1], // 1x Ghostring Card
            ];

            foreach ($storageItems as $sItem) {
                $conn->table('storage')->insert([
                    'account_id' => $accId,
                    'nameid' => $sItem['nameid'],
                    'amount' => $sItem['amount'],
                    'equip' => 0,
                    'identify' => $sItem['identify'],
                    'refine' => $sItem['refine'],
                    'attribute' => 0,
                    'card0' => 0,
                    'card1' => 0,
                    'card2' => 0,
                    'card3' => 0,
                    'expire_time' => 0,
                    'bound' => 0,
                    'unique_id' => 0,
                ]);
            }
            echo "Seeded " . count($storageItems) . " diverse item stacks into Kafra storage for account {$accId}.\n";
        }

        // 3. Seed 2 Active Market Listings (from account 2000001 so 2000000 can buy them, and vice versa)
        $sellerUser = User::where('account_id', 2000001)->first() ?? User::first();
        if ($sellerUser) {
            // Remove existing listings to re-seed cleanly
            MarketplaceListing::query()->delete();
            MarketplaceItem::query()->delete();

            // Item 1: +8 Tsurugi listed for 2,500,000 Zeny
            $item1 = MarketplaceItem::create([
                'user_id' => $sellerUser->id,
                'account_id' => $sellerUser->account_id,
                'char_id' => 150003,
                'nameid' => 1119,
                'item_name' => 'Tsurugi',
                'amount' => 1,
                'refine' => 8,
                'identify' => 1,
                'item_type' => 'Weapon',
                'status' => 'listed',
                'deposited_at' => now(),
            ]);

            MarketplaceListing::create([
                'listing_code' => 'MKT-' . strtoupper(Str::random(10)),
                'item_id' => $item1->id,
                'seller_user_id' => $sellerUser->id,
                'seller_account_id' => $sellerUser->account_id,
                'seller_char_id' => 150003,
                'seller_char_name' => 'Freynix',
                'currency_type' => 'zeny',
                'unit_price' => 2500000,
                'total_price' => 2500000,
                'quantity' => 1,
                'remaining_quantity' => 1,
                'duration_hours' => 72,
                'status' => 'active',
                'expires_at' => now()->addDays(3),
            ]);

            // Item 2: 20x Yggdrasil Berry listed for 25 Cash Points each
            $item2 = MarketplaceItem::create([
                'user_id' => $sellerUser->id,
                'account_id' => $sellerUser->account_id,
                'char_id' => 150003,
                'nameid' => 607,
                'item_name' => 'Yggdrasil Berry',
                'amount' => 20,
                'refine' => 0,
                'identify' => 1,
                'item_type' => 'Consumable',
                'status' => 'listed',
                'deposited_at' => now(),
            ]);

            MarketplaceListing::create([
                'listing_code' => 'MKT-' . strtoupper(Str::random(10)),
                'item_id' => $item2->id,
                'seller_user_id' => $sellerUser->id,
                'seller_account_id' => $sellerUser->account_id,
                'seller_char_id' => 150003,
                'seller_char_name' => 'Freynix',
                'currency_type' => 'cashpoints',
                'unit_price' => 25,
                'total_price' => 500,
                'quantity' => 20,
                'remaining_quantity' => 20,
                'duration_hours' => 72,
                'status' => 'active',
                'expires_at' => now()->addDays(3),
            ]);

            echo "Created sample market listings (+8 Tsurugi for Zeny, Yggdrasil Berries for CP) for live market testing.\n";
        }

        echo "\n=== SEEDING COMPLETED SUCCESSFULLY ===\n";
    }
}
