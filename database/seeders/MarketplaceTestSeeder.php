<?php

namespace Database\Seeders;

use App\Models\MarketplaceItem;
use App\Models\MarketplaceListing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketplaceTestSeeder extends Seeder
{
    public function run(): void
    {
        $conn = DB::connection('ragnarok');

        echo "=== 1. Seeding Ragnarok Emulator Accounts, Characters & Currency ===\n";

        $accounts = [
            [
                'account_id' => 2000000,
                'userid' => 'admin',
                'name' => 'Freynix',
                'char_id' => 150001,
                'class' => 4013, // Assassin Cross
                'sex' => 'M',
                'zeny' => 50000000,
                'cashpoints' => 50000,
            ],
            [
                'account_id' => 2000002,
                'userid' => 'rune_smith',
                'name' => 'RuneSmith',
                'char_id' => 150002,
                'class' => 4012, // Whitesmith
                'sex' => 'M',
                'zeny' => 50000000,
                'cashpoints' => 50000,
            ],
            [
                'account_id' => 2000003,
                'userid' => 'kafra_trader',
                'name' => 'KafraTrader',
                'char_id' => 150003,
                'class' => 4015, // Paladin
                'sex' => 'F',
                'zeny' => 50000000,
                'cashpoints' => 50000,
            ],
            [
                'account_id' => 2000004,
                'userid' => 'card_collector',
                'name' => 'CardCollector',
                'char_id' => 150004,
                'class' => 4016, // Champion
                'sex' => 'M',
                'zeny' => 50000000,
                'cashpoints' => 50000,
            ],
            [
                'account_id' => 2000005,
                'userid' => 'alchemist_pro',
                'name' => 'AlchemistPro',
                'char_id' => 150005,
                'class' => 4018, // Creator
                'sex' => 'F',
                'zeny' => 50000000,
                'cashpoints' => 50000,
            ],
        ];

        foreach ($accounts as $acc) {
            $accId = $acc['account_id'];

            // 1a. Ensure login record exists
            $loginExists = $conn->table('login')->where('account_id', $accId)->first();
            if (!$loginExists) {
                $conn->table('login')->insert([
                    'account_id' => $accId,
                    'userid' => $acc['userid'],
                    'user_pass' => Hash::make('Password123!'),
                    'sex' => $acc['sex'],
                    'email' => "{$acc['userid']}@aether.local",
                    'group_id' => $acc['userid'] === 'admin' ? 99 : 0,
                    'character_slots' => 9,
                ]);
            }

            // 1b. Cash points
            try {
                $conn->table('acc_reg_num')->updateOrInsert(
                    ['account_id' => $accId, 'key' => '#CASHPOINTS', 'index' => 0],
                    ['value' => $acc['cashpoints']]
                );
            } catch (\Throwable $e) {}

            // 1c. Seed Character
            $charExists = $conn->table('char')->where('char_id', $acc['char_id'])->first();
            if (!$charExists) {
                $conn->table('char')->insert([
                    'char_id' => $acc['char_id'],
                    'account_id' => $accId,
                    'char_num' => 0,
                    'name' => $acc['name'],
                    'class' => $acc['class'],
                    'base_level' => 99,
                    'job_level' => 70,
                    'base_exp' => 0,
                    'job_exp' => 0,
                    'zeny' => $acc['zeny'],
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
                    'sex' => $acc['sex'],
                ]);
                echo "Inserted character: {$acc['name']} (ID: {$acc['char_id']}, Account: {$accId})\n";
            } else {
                $conn->table('char')->where('char_id', $acc['char_id'])->update([
                    'zeny' => $acc['zeny'],
                    'base_level' => 99,
                    'job_level' => 70,
                    'online' => 0,
                ]);
                echo "Updated character: {$acc['name']} (ID: {$acc['char_id']})\n";
            }

            // 1d. Seed Storage items
            try {
                $conn->table('storage')->where('account_id', $accId)->delete();
                $storageItems = [
                    ['nameid' => 1107, 'amount' => 1, 'refine' => 7],
                    ['nameid' => 2101, 'amount' => 1, 'refine' => 6],
                    ['nameid' => 501,  'amount' => 500, 'refine' => 0],
                    ['nameid' => 504,  'amount' => 250, 'refine' => 0],
                    ['nameid' => 607,  'amount' => 50,  'refine' => 0],
                    ['nameid' => 984,  'amount' => 100, 'refine' => 0],
                    ['nameid' => 985,  'amount' => 100, 'refine' => 0],
                    ['nameid' => 4001, 'amount' => 2,   'refine' => 0],
                ];
                foreach ($storageItems as $sItem) {
                    $conn->table('storage')->insert([
                        'account_id' => $accId,
                        'nameid' => $sItem['nameid'],
                        'amount' => $sItem['amount'],
                        'equip' => 0,
                        'identify' => 1,
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
            } catch (\Throwable $e) {}
        }

        echo "\n=== 2. Seeding Web Users & Marketplace Consignments ===\n";

        // Seed Seller Web Users
        $sellerUsers = [
            ['id' => 2, 'name' => 'Rune Smith', 'username' => 'rune_smith', 'account_id' => 2000002, 'char_id' => 150002, 'char_name' => 'RuneSmith'],
            ['id' => 3, 'name' => 'Kafra Trader', 'username' => 'kafra_trader', 'account_id' => 2000003, 'char_id' => 150003, 'char_name' => 'KafraTrader'],
            ['id' => 4, 'name' => 'Card Collector', 'username' => 'card_collector', 'account_id' => 2000004, 'char_id' => 150004, 'char_name' => 'CardCollector'],
            ['id' => 5, 'name' => 'Alchemist Pro', 'username' => 'alchemist_pro', 'account_id' => 2000005, 'char_id' => 150005, 'char_name' => 'AlchemistPro'],
        ];

        foreach ($sellerUsers as $su) {
            User::updateOrCreate(
                ['username' => $su['username']],
                [
                    'name' => $su['name'],
                    'email' => "{$su['username']}@aether.local",
                    'password' => Hash::make('Password123!'),
                    'account_id' => $su['account_id'],
                    'role' => 'user',
                    'is_active' => true,
                ]
            );
        }

        // Clean previous listings & items
        MarketplaceListing::query()->delete();
        MarketplaceItem::query()->delete();

        $listingsData = [
            // 1. +8 Tsurugi (2x Skeleton Worker)
            [
                'seller_username' => 'rune_smith',
                'nameid' => 1119,
                'item_name' => 'Tsurugi',
                'item_type' => 'Weapon',
                'refine' => 8,
                'card0' => 4105,
                'card1' => 4105,
                'quantity' => 1,
                'currency' => 'zeny',
                'unit_price' => 2500000,
                'code' => 'MKT-TSURUGI8Z',
            ],
            // 2. +9 Haedonggum
            [
                'seller_username' => 'rune_smith',
                'nameid' => 1123,
                'item_name' => 'Haedonggum',
                'item_type' => 'Weapon',
                'refine' => 9,
                'quantity' => 1,
                'currency' => 'zeny',
                'unit_price' => 4800000,
                'code' => 'MKT-HAEDONG9Z',
            ],
            // 3. +10 Composite Bow (4x Hydra)
            [
                'seller_username' => 'kafra_trader',
                'nameid' => 1704,
                'item_name' => 'Composite Bow',
                'item_type' => 'Weapon',
                'refine' => 10,
                'card0' => 4003,
                'card1' => 4003,
                'card2' => 4003,
                'card3' => 4003,
                'quantity' => 1,
                'currency' => 'zeny',
                'unit_price' => 7500000,
                'code' => 'MKT-QUADHYDRA',
            ],
            // 4. +7 Valkyrian Armor
            [
                'seller_username' => 'kafra_trader',
                'nameid' => 2357,
                'item_name' => 'Valkyrian Armor',
                'item_type' => 'Armor',
                'refine' => 7,
                'quantity' => 1,
                'currency' => 'zeny',
                'unit_price' => 12000000,
                'code' => 'MKT-VALKARM7Z',
            ],
            // 5. +6 Guard (Thara Frog Card)
            [
                'seller_username' => 'rune_smith',
                'nameid' => 2101,
                'item_name' => 'Guard',
                'item_type' => 'Armor',
                'refine' => 6,
                'card0' => 4054,
                'quantity' => 1,
                'currency' => 'zeny',
                'unit_price' => 1800000,
                'code' => 'MKT-CRANGUAR6',
            ],
            // 6. Elven Ears (Cash Points)
            [
                'seller_username' => 'kafra_trader',
                'nameid' => 2256,
                'item_name' => 'Elven Ears',
                'item_type' => 'Armor',
                'refine' => 0,
                'quantity' => 1,
                'currency' => 'cashpoints',
                'unit_price' => 150,
                'code' => 'MKT-ELVEARSCP',
            ],
            // 7. Ghostring Card
            [
                'seller_username' => 'card_collector',
                'nameid' => 4142,
                'item_name' => 'Ghostring Card',
                'item_type' => 'Card',
                'refine' => 0,
                'quantity' => 1,
                'currency' => 'zeny',
                'unit_price' => 25000000,
                'code' => 'MKT-GHOSTRING',
            ],
            // 8. Poring Card (Affordable test card for 50,000 Zeny)
            [
                'seller_username' => 'card_collector',
                'nameid' => 4001,
                'item_name' => 'Poring Card',
                'item_type' => 'Card',
                'refine' => 0,
                'quantity' => 1,
                'currency' => 'zeny',
                'unit_price' => 50000,
                'code' => 'MKT-PORINGCRD',
            ],
            // 9. Raydric Card
            [
                'seller_username' => 'card_collector',
                'nameid' => 4122,
                'item_name' => 'Raydric Card',
                'item_type' => 'Card',
                'refine' => 0,
                'quantity' => 1,
                'currency' => 'zeny',
                'unit_price' => 3200000,
                'code' => 'MKT-RAYDRICRD',
            ],
            // 10. Yggdrasil Berry (25 CP each)
            [
                'seller_username' => 'alchemist_pro',
                'nameid' => 607,
                'item_name' => 'Yggdrasil Berry',
                'item_type' => 'Consumable',
                'refine' => 0,
                'quantity' => 50,
                'currency' => 'cashpoints',
                'unit_price' => 25,
                'code' => 'MKT-YGGDBERRY',
            ],
            // 11. White Potion (1,200 Zeny each)
            [
                'seller_username' => 'alchemist_pro',
                'nameid' => 504,
                'item_name' => 'White Potion',
                'item_type' => 'Consumable',
                'refine' => 0,
                'quantity' => 200,
                'currency' => 'zeny',
                'unit_price' => 1200,
                'code' => 'MKT-WHITEPOTN',
            ],
            // 12. Oridecon (45,000 Zeny each)
            [
                'seller_username' => 'alchemist_pro',
                'nameid' => 984,
                'item_name' => 'Oridecon',
                'item_type' => 'Etc',
                'refine' => 0,
                'quantity' => 50,
                'currency' => 'zeny',
                'unit_price' => 45000,
                'code' => 'MKT-ORIDECONX',
            ],
        ];

        foreach ($listingsData as $ld) {
            $seller = User::where('username', $ld['seller_username'])->first();
            if (!$seller) continue;

            $sellerConf = collect($sellerUsers)->firstWhere('username', $ld['seller_username']);

            $item = MarketplaceItem::create([
                'user_id' => $seller->id,
                'account_id' => $seller->account_id,
                'char_id' => $sellerConf['char_id'] ?? 0,
                'nameid' => $ld['nameid'],
                'item_name' => $ld['item_name'],
                'amount' => $ld['quantity'],
                'equip' => 0,
                'identify' => 1,
                'refine' => $ld['refine'] ?? 0,
                'attribute' => 0,
                'card0' => $ld['card0'] ?? 0,
                'card1' => $ld['card1'] ?? 0,
                'card2' => $ld['card2'] ?? 0,
                'card3' => $ld['card3'] ?? 0,
                'item_type' => $ld['item_type'],
                'status' => 'listed',
                'deposited_at' => now(),
            ]);

            MarketplaceListing::create([
                'listing_code' => $ld['code'],
                'item_id' => $item->id,
                'seller_user_id' => $seller->id,
                'seller_account_id' => $seller->account_id,
                'seller_char_id' => $sellerConf['char_id'] ?? 0,
                'seller_char_name' => $sellerConf['char_name'] ?? $seller->username,
                'currency_type' => $ld['currency'],
                'unit_price' => $ld['unit_price'],
                'total_price' => $ld['unit_price'] * $ld['quantity'],
                'quantity' => $ld['quantity'],
                'remaining_quantity' => $ld['quantity'],
                'duration_hours' => 168,
                'status' => 'active',
                'expires_at' => now()->addDays(7),
            ]);

            echo "Created listing [{$ld['code']}]: {$ld['item_name']} ({$ld['currency']} {$ld['unit_price']})\n";
        }

        echo "\n=== ALL MARKETPLACE SEEDERS CREATED SUCCESSFULLY ===\n";
    }
}
