-- AetherCP Cash Shop / Web Item Mall Seeders
-- Compatible with MySQL 5.7+ / MariaDB / phpMyAdmin

SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE `cash_shop_purchases`;
TRUNCATE TABLE `cash_shop_products`;
TRUNCATE TABLE `cash_shop_categories`;
SET FOREIGN_KEY_CHECKS=1;

-- Categories
INSERT INTO `cash_shop_categories` (`id`, `name`, `slug`, `icon`, `description`, `sort_order`, `is_active`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 'Consumables', 'consumables', 'coffee', 'Buff potions, food boxes, and consumable supply packs', 1, 1, 0, NOW(), NOW()),
(2, 'Special Items', 'special-items', 'package', 'Rare gift boxes, albums, gym passes and utility tokens', 2, 1, 0, NOW(), NOW()),
(3, 'Headgears', 'headgears', 'crown', 'Upper, middle, and lower headgears with powerful stats', 3, 1, 0, NOW(), NOW()),
(4, 'Costumes', 'costumes', 'sparkles', 'Exclusive visual costume garments and vanity garments', 4, 1, 0, NOW(), NOW()),
(5, 'Weapons', 'weapons', 'sword', 'Refined weaponry, slotted blades, bows and staves', 5, 1, 0, NOW(), NOW()),
(6, 'Armor', 'armor', 'shield', 'Defensive armors, garments, shields and footwear', 6, 1, 0, NOW(), NOW()),
(7, 'Cards', 'cards', 'layers', 'Monster cards ready to be socketed into your gear', 7, 1, 0, NOW(), NOW());

-- Products
INSERT INTO `cash_shop_products` (`id`, `category_id`, `name`, `slug`, `description`, `item_id`, `item_quantity`, `cash_points_price`, `promotional_price`, `stock_quantity`, `unlimited_stock`, `purchase_limit_per_account`, `purchase_limit_daily`, `item_metadata`, `image_url`, `status`, `is_featured`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 2, 'Old Card Album', 'old-card-album', 'A mysterious album containing a random monster card. Double-click to open and receive your card.', 616, 1, 100, 80, 0, 1, 0, 0, NULL, NULL, 'published', 1, 1, NOW(), NOW()),
(2, 2, 'Gym Membership Pass', 'gym-membership-pass', 'A membership pass for the Prontera Gymnasium. Bring to Ripped Paul to increase your weight capacity permanently.', 7621, 1, 150, NULL, 0, 1, 0, 0, NULL, NULL, 'published', 1, 2, NOW(), NOW()),
(3, 1, 'Bubble Gum (10x Pack)', 'bubble-gum-10x', 'Increases item drop rate by +100% for 30 minutes. Essential for farming rare equipment and cards.', 12210, 10, 75, NULL, 0, 1, 0, 0, NULL, NULL, 'published', 0, 3, NOW(), NOW()),
(4, 2, 'Bloody Branch', 'bloody-branch', 'A cursed branch that summons a powerful MVP boss monster when snapped in a field or dungeon.', 12103, 1, 200, NULL, 0, 1, 0, 0, NULL, NULL, 'published', 1, 4, NOW(), NOW()),
(5, 3, '+7 Valkyrie Helm', 'plus-7-valkyrie-helm', 'A glorious golden winged helm blessed by the Valkyries. Refined to +7 with high defense and all-stat bonuses.', 5171, 1, 1500, 1200, 0, 1, 0, 0, '{\"refine\": 7, \"slots\": 1}', NULL, 'published', 1, 5, NOW(), NOW()),
(6, 1, 'Enriched Elunium (5x)', 'enriched-elunium-5x', 'A specially refined ore used by master blacksmiths. Significantly increases armor refine success rate up to +10.', 7620, 5, 250, NULL, 0, 1, 0, 0, NULL, NULL, 'published', 0, 6, NOW(), NOW()),
(7, 5, '+7 Tsurugi [2]', 'plus-7-tsurugi-2', 'A mastercrafted one-handed sword honed to +7. Features 2 empty card slots ready for enhancement.', 1123, 1, 450, NULL, 0, 1, 0, 0, '{\"refine\": 7, \"slots\": 2}', NULL, 'published', 0, 7, NOW(), NOW()),
(8, 7, 'Raydric Card', 'raydric-card', 'Increases resistance to Neutral Property attacks by 20%. Must be compounded into a Garment.', 4133, 1, 600, NULL, 0, 1, 0, 0, NULL, NULL, 'published', 0, 8, NOW(), NOW());
