-- ============================================================================
-- AetherCP — Character Equipment, Inventory & Kafra Storage SQL Seeder
-- Target Database: Ragnarok Game Database (e.g. `ragnarok` / `freynix`)
-- Non-destructive & idempotent: seeds sample equipped gear, inventory items,
-- and Kafra storage items for existing characters (up to first 6 characters).
-- ============================================================================

-- 1. Equipped Gear (Upper Headgear, Armor, Weapon, Shield, Garment, Shoes, Accessories)
INSERT INTO `inventory` (`char_id`, `nameid`, `amount`, `equip`, `identify`, `refine`, `attribute`, `card0`, `card1`, `card2`, `card3`, `bound`)
SELECT c.`char_id`, v.`nameid`, v.`amount`, v.`equip`, 1, v.`refine`, 0, v.`card0`, 0, 0, 0, 0
FROM (
    SELECT `char_id` FROM `char` ORDER BY `char_id` ASC LIMIT 6
) c
CROSS JOIN (
    SELECT 5013 AS `nameid`, 1 AS `amount`, 256 AS `equip`, 7 AS `refine`, 4121 AS `card0`
    UNION ALL SELECT 2357, 1, 16,  7, 4128
    UNION ALL SELECT 1220, 1, 2,   9, 4140
    UNION ALL SELECT 2115, 1, 32,  7, 4058
    UNION ALL SELECT 2524, 1, 4,   7, 4133
    UNION ALL SELECT 2421, 1, 64,  7, 4131
    UNION ALL SELECT 2620, 1, 8,   0, 4064
    UNION ALL SELECT 2620, 1, 128, 0, 4064
) v
WHERE NOT EXISTS (
    SELECT 1 FROM `inventory` i
    WHERE i.`char_id` = c.`char_id` AND i.`nameid` = v.`nameid` AND i.`equip` = v.`equip`
);

-- 2. Character Inventory Bag Items
INSERT INTO `inventory` (`char_id`, `nameid`, `amount`, `equip`, `identify`, `refine`, `attribute`, `card0`, `card1`, `card2`, `card3`, `bound`)
SELECT c.`char_id`, v.`nameid`, v.`amount`, 0, 1, 0, 0, 0, 0, 0, 0, 0
FROM (
    SELECT `char_id` FROM `char` ORDER BY `char_id` ASC LIMIT 6
) c
CROSS JOIN (
    SELECT 504 AS `nameid`, 150 AS `amount`
    UNION ALL SELECT 505, 80
    UNION ALL SELECT 522, 50
    UNION ALL SELECT 601, 100
    UNION ALL SELECT 602, 25
    UNION ALL SELECT 607, 15
) v
WHERE NOT EXISTS (
    SELECT 1 FROM `inventory` i
    WHERE i.`char_id` = c.`char_id` AND i.`nameid` = v.`nameid` AND i.`equip` = 0
);

-- 3. Account Kafra Storage Items
INSERT INTO `storage` (`account_id`, `nameid`, `amount`, `equip`, `identify`, `refine`, `attribute`, `card0`, `card1`, `card2`, `card3`, `bound`)
SELECT a.`account_id`, v.`nameid`, v.`amount`, 0, 1, v.`refine`, 0, v.`card0`, 0, 0, 0, 0
FROM (
    SELECT DISTINCT `account_id` FROM `char` ORDER BY `account_id` ASC LIMIT 6
) a
CROSS JOIN (
    SELECT 501  AS `nameid`, 200 AS `amount`, 0 AS `refine`, 0 AS `card0`
    UNION ALL SELECT 607,  30, 0, 0
    UNION ALL SELECT 5123, 1,  7, 4121
    UNION ALL SELECT 1107, 1,  7, 4001
    UNION ALL SELECT 2101, 1,  6, 4058
    UNION ALL SELECT 4001, 2,  0, 0
) v
WHERE NOT EXISTS (
    SELECT 1 FROM `storage` s
    WHERE s.`account_id` = a.`account_id` AND s.`nameid` = v.`nameid`
);
