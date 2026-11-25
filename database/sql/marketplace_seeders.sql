-- =====================================================================
-- AetherCP Player Marketplace Sample Live Seeders
-- Target Database: AetherCP Web Application Database (`aether_cp`)
-- Purpose: Provides active consignment listings across Weapons, Armor,
--          Cards, and Consumables for live testing of purchases and Escrow.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

-- -----------------------------------------------------
-- 1. Ensure Seller Web Users exist in `users`
-- Default password for seller accounts: Password123!
-- -----------------------------------------------------

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `account_id`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'Rune Smith', 'rune_smith', 'runesmith@aether.local', '$2y$12$N2.YgqFom8F6h6Jg16k7l.W2Y43/T9x4o6J4j7y0a9V8u7p1X0Z.2', 2000002, 'user', 1, NOW(), NOW()),
(3, 'Kafra Trader', 'kafra_trader', 'trader@aether.local', '$2y$12$N2.YgqFom8F6h6Jg16k7l.W2Y43/T9x4o6J4j7y0a9V8u7p1X0Z.2', 2000003, 'user', 1, NOW(), NOW()),
(4, 'Card Collector', 'card_collector', 'cards@aether.local', '$2y$12$N2.YgqFom8F6h6Jg16k7l.W2Y43/T9x4o6J4j7y0a9V8u7p1X0Z.2', 2000004, 'user', 1, NOW(), NOW()),
(5, 'Alchemist Pro', 'alchemist_pro', 'alchemist@aether.local', '$2y$12$N2.YgqFom8F6h6Jg16k7l.W2Y43/T9x4o6J4j7y0a9V8u7p1X0Z.2', 2000005, 'user', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `account_id` = VALUES(`account_id`),
    `is_active` = VALUES(`is_active`);

-- -----------------------------------------------------
-- 2. Seed `marketplace_items` (Trade House Custody)
-- -----------------------------------------------------

INSERT INTO `marketplace_items` (`id`, `user_id`, `account_id`, `char_id`, `nameid`, `item_name`, `amount`, `equip`, `identify`, `refine`, `attribute`, `card0`, `card1`, `card2`, `card3`, `expire_time`, `bound`, `unique_id`, `item_type`, `status`, `deposited_at`, `created_at`, `updated_at`) VALUES
(101, 2, 2000002, 150002, 1119, 'Tsurugi', 1, 0, 1, 8, 0, 4105, 4105, 0, 0, 0, 0, 0, 'Weapon', 'listed', NOW(), NOW(), NOW()),
(102, 2, 2000002, 150002, 1123, 'Haedonggum', 1, 0, 1, 9, 0, 0, 0, 0, 0, 0, 0, 0, 'Weapon', 'listed', NOW(), NOW(), NOW()),
(103, 3, 2000003, 150003, 1704, 'Composite Bow', 1, 0, 1, 10, 0, 4003, 4003, 4003, 4003, 0, 0, 0, 'Weapon', 'listed', NOW(), NOW(), NOW()),
(104, 3, 2000003, 150003, 2357, 'Valkyrian Armor', 1, 0, 1, 7, 0, 0, 0, 0, 0, 0, 0, 0, 'Armor', 'listed', NOW(), NOW(), NOW()),
(105, 2, 2000002, 150002, 2101, 'Guard', 1, 0, 1, 6, 0, 4054, 0, 0, 0, 0, 0, 0, 'Armor', 'listed', NOW(), NOW(), NOW()),
(106, 3, 2000003, 150003, 2256, 'Elven Ears', 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'Armor', 'listed', NOW(), NOW(), NOW()),
(107, 4, 2000004, 150004, 4142, 'Ghostring Card', 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'Card', 'listed', NOW(), NOW(), NOW()),
(108, 4, 2000004, 150004, 4001, 'Poring Card', 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'Card', 'listed', NOW(), NOW(), NOW()),
(109, 4, 2000004, 150004, 4122, 'Raydric Card', 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'Card', 'listed', NOW(), NOW(), NOW()),
(110, 5, 2000005, 150005, 607, 'Yggdrasil Berry', 50, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'Consumable', 'listed', NOW(), NOW(), NOW()),
(111, 5, 2000005, 150005, 504, 'White Potion', 200, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'Consumable', 'listed', NOW(), NOW(), NOW()),
(112, 5, 2000005, 150005, 984, 'Oridecon', 50, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'Etc', 'listed', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `item_name` = VALUES(`item_name`),
    `status` = VALUES(`status`),
    `refine` = VALUES(`refine`);

-- -----------------------------------------------------
-- 3. Seed `marketplace_listings` (Active Consignments)
-- -----------------------------------------------------

INSERT INTO `marketplace_listings` (`id`, `listing_code`, `item_id`, `seller_user_id`, `seller_account_id`, `seller_char_id`, `seller_char_name`, `currency_type`, `unit_price`, `total_price`, `quantity`, `remaining_quantity`, `duration_hours`, `status`, `expires_at`, `created_at`, `updated_at`) VALUES
(101, 'MKT-TSURUGI8Z', 101, 2, 2000002, 150002, 'RuneSmith', 'zeny', 2500000, 2500000, 1, 1, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(102, 'MKT-HAEDONG9Z', 102, 2, 2000002, 150002, 'RuneSmith', 'zeny', 4800000, 4800000, 1, 1, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(103, 'MKT-QUADHYDRA', 103, 3, 2000003, 150003, 'KafraTrader', 'zeny', 7500000, 7500000, 1, 1, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(104, 'MKT-VALKARM7Z', 104, 3, 2000003, 150003, 'KafraTrader', 'zeny', 12000000, 12000000, 1, 1, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(105, 'MKT-CRANGUAR6', 105, 2, 2000002, 150002, 'RuneSmith', 'zeny', 1800000, 1800000, 1, 1, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(106, 'MKT-ELVEARSCP', 106, 3, 2000003, 150003, 'KafraTrader', 'cashpoints', 150, 150, 1, 1, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(107, 'MKT-GHOSTRING', 107, 4, 2000004, 150004, 'CardCollector', 'zeny', 25000000, 25000000, 1, 1, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(108, 'MKT-PORINGCRD', 108, 4, 2000004, 150004, 'CardCollector', 'zeny', 50000, 50000, 1, 1, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(109, 'MKT-RAYDRICRD', 109, 4, 2000004, 150004, 'CardCollector', 'zeny', 3200000, 3200000, 1, 1, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(110, 'MKT-YGGDBERRY', 110, 5, 2000005, 150005, 'AlchemistPro', 'cashpoints', 25, 1250, 50, 50, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(111, 'MKT-WHITEPOTN', 111, 5, 2000005, 150005, 'AlchemistPro', 'zeny', 1200, 240000, 200, 200, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(112, 'MKT-ORIDECONX', 112, 5, 2000005, 150005, 'AlchemistPro', 'zeny', 45000, 2250000, 50, 50, 168, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `unit_price` = VALUES(`unit_price`),
    `remaining_quantity` = VALUES(`remaining_quantity`),
    `status` = VALUES(`status`),
    `expires_at` = VALUES(`expires_at`);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
