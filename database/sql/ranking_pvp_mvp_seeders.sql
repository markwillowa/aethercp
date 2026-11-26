-- =====================================================================
-- AetherCP Control Panel: PvP & MVP Ranking Sample Seeder (cPanel Ready)
-- Database: Control Panel DB (contains `ranking_character_stats`)
-- Replace `char_id` / `account_id` values with your actual rAthena characters if needed.
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

INSERT INTO `ranking_character_stats` (`char_id`, `account_id`, `pvp_kills`, `pvp_deaths`, `mvp_kills`, `last_event_at`, `created_at`, `updated_at`) VALUES
(150002, 2000002, 1250, 320, 620, NOW(), NOW(), NOW()),
(150003, 2000003, 860, 240, 410, NOW(), NOW(), NOW()),
(150004, 2000004, 540, 180, 290, NOW(), NOW(), NOW()),
(150005, 2000005, 320, 110, 185, NOW(), NOW(), NOW()),
(150006, 2000006, 210, 75, 130, NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `pvp_kills` = VALUES(`pvp_kills`),
  `pvp_deaths` = VALUES(`pvp_deaths`),
  `mvp_kills` = VALUES(`mvp_kills`),
  `last_event_at` = VALUES(`last_event_at`),
  `updated_at` = NOW();

COMMIT;
