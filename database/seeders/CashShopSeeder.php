<?php

namespace Database\Seeders;

use App\Models\CashShopCategory;
use App\Models\CashShopProduct;
use Illuminate\Database\Seeder;

class CashShopSeeder extends Seeder
{
    public function run(): void
    {
        $categoriesData = [
            ['name' => 'Consumables', 'slug' => 'consumables', 'icon' => 'coffee', 'sort_order' => 1, 'description' => 'Buff potions, food boxes, and consumable supply packs'],
            ['name' => 'Special Items', 'slug' => 'special-items', 'icon' => 'package', 'sort_order' => 2, 'description' => 'Rare gift boxes, albums, gym passes and utility tokens'],
            ['name' => 'Headgears', 'slug' => 'headgears', 'icon' => 'crown', 'sort_order' => 3, 'description' => 'Upper, middle, and lower headgears with powerful stats'],
            ['name' => 'Costumes', 'slug' => 'costumes', 'icon' => 'sparkles', 'sort_order' => 4, 'description' => 'Exclusive visual costume garments and vanity garments'],
            ['name' => 'Weapons', 'slug' => 'weapons', 'icon' => 'sword', 'sort_order' => 5, 'description' => 'Refined weaponry, slotted blades, bows and staves'],
            ['name' => 'Armor', 'slug' => 'armor', 'icon' => 'shield', 'sort_order' => 6, 'description' => 'Defensive armors, garments, shields and footwear'],
            ['name' => 'Cards', 'slug' => 'cards', 'icon' => 'layers', 'sort_order' => 7, 'description' => 'Monster cards ready to be socketed into your gear'],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $categories[$data['slug']] = CashShopCategory::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_active' => true])
            );
        }

        $productsData = [
            [
                'name' => 'Old Card Album',
                'slug' => 'old-card-album',
                'item_id' => 616,
                'category_id' => $categories['special-items']->id,
                'item_quantity' => 1,
                'cash_points_price' => 100,
                'promotional_price' => 80,
                'unlimited_stock' => true,
                'description' => 'A mysterious album containing a random monster card. Double-click to open and receive your card.',
                'is_featured' => true,
                'status' => 'published',
                'sort_order' => 1,
            ],
            [
                'name' => 'Gym Membership Pass',
                'slug' => 'gym-membership-pass',
                'item_id' => 7621,
                'category_id' => $categories['special-items']->id,
                'item_quantity' => 1,
                'cash_points_price' => 150,
                'unlimited_stock' => true,
                'description' => 'A membership pass for the Prontera Gymnasium. Bring to Ripped Paul to increase your weight capacity permanently.',
                'is_featured' => true,
                'status' => 'published',
                'sort_order' => 2,
            ],
            [
                'name' => 'Bubble Gum (10x Pack)',
                'slug' => 'bubble-gum-10x',
                'item_id' => 12210,
                'category_id' => $categories['consumables']->id,
                'item_quantity' => 10,
                'cash_points_price' => 75,
                'unlimited_stock' => true,
                'description' => 'Increases item drop rate by +100% for 30 minutes. Essential for farming rare equipment and cards.',
                'is_featured' => false,
                'status' => 'published',
                'sort_order' => 3,
            ],
            [
                'name' => 'Bloody Branch',
                'slug' => 'bloody-branch',
                'item_id' => 12103,
                'category_id' => $categories['special-items']->id,
                'item_quantity' => 1,
                'cash_points_price' => 200,
                'unlimited_stock' => true,
                'description' => 'A cursed branch that summons a powerful MVP boss monster when snapped in a field or dungeon.',
                'is_featured' => true,
                'status' => 'published',
                'sort_order' => 4,
            ],
            [
                'name' => '+7 Valkyrie Helm',
                'slug' => 'plus-7-valkyrie-helm',
                'item_id' => 5171,
                'category_id' => $categories['headgears']->id,
                'item_quantity' => 1,
                'item_metadata' => ['refine' => 7, 'slots' => 1],
                'cash_points_price' => 1500,
                'promotional_price' => 1200,
                'unlimited_stock' => true,
                'description' => 'A glorious golden winged helm blessed by the Valkyries. Refined to +7 with high defense and all-stat bonuses.',
                'is_featured' => true,
                'status' => 'published',
                'sort_order' => 5,
            ],
            [
                'name' => 'Enriched Elunium (5x)',
                'slug' => 'enriched-elunium-5x',
                'item_id' => 7620,
                'category_id' => $categories['consumables']->id,
                'item_quantity' => 5,
                'cash_points_price' => 250,
                'unlimited_stock' => true,
                'description' => 'A specially refined ore used by master blacksmiths. Significantly increases armor refine success rate up to +10.',
                'is_featured' => false,
                'status' => 'published',
                'sort_order' => 6,
            ],
            [
                'name' => '+7 Tsurugi [2]',
                'slug' => 'plus-7-tsurugi-2',
                'item_id' => 1123,
                'category_id' => $categories['weapons']->id,
                'item_quantity' => 1,
                'item_metadata' => ['refine' => 7, 'slots' => 2],
                'cash_points_price' => 450,
                'unlimited_stock' => true,
                'description' => 'A mastercrafted one-handed sword honed to +7. Features 2 empty card slots ready for enhancement.',
                'is_featured' => false,
                'status' => 'published',
                'sort_order' => 7,
            ],
            [
                'name' => 'Raydric Card',
                'slug' => 'raydric-card',
                'item_id' => 4133,
                'category_id' => $categories['cards']->id,
                'item_quantity' => 1,
                'cash_points_price' => 600,
                'unlimited_stock' => true,
                'description' => 'Increases resistance to Neutral Property attacks by 20%. Must be compounded into a Garment.',
                'is_featured' => false,
                'status' => 'published',
                'sort_order' => 8,
            ],
        ];

        foreach ($productsData as $product) {
            CashShopProduct::updateOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }
    }
}
