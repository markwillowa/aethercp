-- =====================================================================
-- Ragnarok Online Emulator Game Database — Guilds & Castles Seeder
-- Target Database: Ragnarok Game Database (`freynix` / `ragnarok`)
-- Compatible with: rAthena & Hercules
-- Separated from AetherCP Control Panel rewards/badges seeders.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

INSERT INTO `guild` (`guild_id`, `name`, `char_id`, `master`, `guild_lv`, `connect_member`, `max_member`, `average_lv`, `exp`, `next_exp`, `skill_point`, `mes1`, `mes2`, `emblem_len`, `emblem_id`, `last_master_change`) VALUES
(1, 'Valhalla Ascendant', 150001, 'AetherKnight', 50, 2, 36, 99, 95000000, 96000000, 10, 'Glory to the guild!', 'War of Emperium ready.', 0, 0, 0),
(2, 'Ironclad Syndicate', 150005, 'ThorinSmith', 45, 2, 36, 99, 62000000, 63000000, 10, 'Glory to the guild!', 'War of Emperium ready.', 0, 0, 0),
(3, 'Shadow Stalkers', 150007, 'ShadowSniper', 40, 2, 36, 99, 41000000, 42000000, 10, 'Glory to the guild!', 'War of Emperium ready.', 0, 0, 0),
(4, 'Arcane Dominion', 150004, 'Archmage', 35, 2, 36, 99, 28000000, 29000000, 10, 'Glory to the guild!', 'War of Emperium ready.', 0, 0, 0)
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `guild_lv` = VALUES(`guild_lv`),
    `exp` = VALUES(`exp`),
    `next_exp` = VALUES(`next_exp`);

INSERT INTO `guild_castle` (`castle_id`, `guild_id`, `economy`, `defense`, `triggerE`, `triggerD`, `nextTime`, `payTime`, `createTime`, `visibleC`) VALUES
(1, 1, 50, 50, 0, 0, 0, 0, 0, 1),
(2, 1, 50, 50, 0, 0, 0, 0, 0, 1),
(3, 2, 50, 50, 0, 0, 0, 0, 0, 1),
(4, 3, 50, 50, 0, 0, 0, 0, 0, 1)
ON DUPLICATE KEY UPDATE
    `guild_id` = VALUES(`guild_id`),
    `economy` = VALUES(`economy`),
    `defense` = VALUES(`defense`);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
