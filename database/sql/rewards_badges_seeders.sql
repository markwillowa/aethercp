-- =====================================================================
-- AetherCP Badges & Milestone Rewards Seeders
-- Compatible with MySQL 5.7+ / MariaDB / phpMyAdmin
-- Non-destructive: Uses ON DUPLICATE KEY UPDATE to preserve existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

-- 1. Badge Definitions
INSERT INTO `badge_definitions` (`id`, `slug`, `name`, `description`, `category`, `icon_type`, `icon`, `color`, `visual_style`, `priority`, `is_enabled`, `is_permanent`, `auto_condition_type`, `auto_condition_value`, `created_at`, `updated_at`) VALUES
(1, 'first-to-max-level', 'First to Max Level', 'First adventurer on the server to reach maximum base level.', 'level', 'lucide', 'crown', '#f59e0b', 'glow', 100, 1, 1, 'first_max_base', NULL, NOW(), NOW()),
(2, 'max-level', 'Max Level', 'Reached the server maximum base level.', 'level', 'lucide', 'star', '#06b6d4', 'solid', 80, 1, 1, 'max_base', NULL, NOW(), NOW()),
(3, 'first-to-max-job', 'First to Max Job', 'First adventurer to reach maximum job level.', 'job', 'lucide', 'award', '#8b5cf6', 'glow', 95, 1, 1, 'first_max_job', NULL, NOW(), NOW()),
(4, 'max-job-level', 'Max Job Level', 'Mastered the maximum job level for their class.', 'job', 'lucide', 'sparkles', '#3b82f6', 'solid', 75, 1, 1, 'max_job', NULL, NOW(), NOW()),
(5, 'top-1-guild', 'Top 1 Guild', 'Currently ranked #1 Guild on the server.', 'guild', 'lucide', 'trophy', '#f59e0b', 'glow', 100, 1, 0, 'guild_rank_1', NULL, NOW(), NOW()),
(6, 'top-2-guild', 'Top 2 Guild', 'Currently ranked #2 Guild on the server.', 'guild', 'lucide', 'shield', '#cbd5e1', 'solid', 90, 1, 0, 'guild_rank_2', NULL, NOW(), NOW()),
(7, 'top-3-guild', 'Top 3 Guild', 'Currently ranked #3 Guild on the server.', 'guild', 'lucide', 'shield', '#d97706', 'solid', 80, 1, 0, 'guild_rank_3', NULL, NOW(), NOW()),
(8, 'top-10-guild', 'Top 4–10 Guild', 'Ranked among the Top 10 Guilds on the server.', 'guild', 'lucide', 'flag', '#06b6d4', 'outline', 60, 1, 0, 'guild_rank_top10', NULL, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `description` = VALUES(`description`),
    `color` = VALUES(`color`),
    `visual_style` = VALUES(`visual_style`),
    `priority` = VALUES(`priority`),
    `is_enabled` = VALUES(`is_enabled`),
    `updated_at` = NOW();

-- 2. Badge Tiers (PvP & MVP Progression)
INSERT INTO `badge_tiers` (`id`, `category`, `tier_name`, `tier_order`, `stars`, `min_kills`, `color`, `is_enabled`, `created_at`, `updated_at`) VALUES
(1, 'pvp', 'Bronze', 1, 1, 10, '#cd7f32', 1, NOW(), NOW()),
(2, 'mvp', 'Bronze', 1, 1, 5, '#cd7f32', 1, NOW(), NOW()),
(3, 'pvp', 'Bronze', 1, 2, 25, '#cd7f32', 1, NOW(), NOW()),
(4, 'mvp', 'Bronze', 1, 2, 15, '#cd7f32', 1, NOW(), NOW()),
(5, 'pvp', 'Bronze', 1, 3, 50, '#cd7f32', 1, NOW(), NOW()),
(6, 'mvp', 'Bronze', 1, 3, 30, '#cd7f32', 1, NOW(), NOW()),
(7, 'pvp', 'Bronze', 1, 4, 75, '#cd7f32', 1, NOW(), NOW()),
(8, 'mvp', 'Bronze', 1, 4, 50, '#cd7f32', 1, NOW(), NOW()),
(9, 'pvp', 'Bronze', 1, 5, 100, '#cd7f32', 1, NOW(), NOW()),
(10, 'mvp', 'Bronze', 1, 5, 75, '#cd7f32', 1, NOW(), NOW()),
(11, 'pvp', 'Silver', 2, 1, 150, '#cbd5e1', 1, NOW(), NOW()),
(12, 'mvp', 'Silver', 2, 1, 120, '#cbd5e1', 1, NOW(), NOW()),
(13, 'pvp', 'Gold', 3, 1, 300, '#fbbf24', 1, NOW(), NOW()),
(14, 'mvp', 'Gold', 3, 1, 250, '#fbbf24', 1, NOW(), NOW()),
(15, 'pvp', 'Platinum', 4, 1, 600, '#22d3ee', 1, NOW(), NOW()),
(16, 'mvp', 'Platinum', 4, 1, 500, '#22d3ee', 1, NOW(), NOW()),
(17, 'pvp', 'Legend', 5, 1, 1200, '#f43f5e', 1, NOW(), NOW()),
(18, 'mvp', 'Legend', 5, 1, 1000, '#f43f5e', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `min_kills` = VALUES(`min_kills`),
    `color` = VALUES(`color`),
    `is_enabled` = VALUES(`is_enabled`),
    `updated_at` = NOW();

-- 3. Milestone Reward Definitions
INSERT INTO `reward_definitions` (`id`, `name`, `category`, `trigger_type`, `badge_id`, `badge_tier_id`, `reward_type`, `item_id`, `item_name`, `item_amount`, `zeny_amount`, `cashpoints_amount`, `policy`, `message`, `is_enabled`, `created_at`, `updated_at`) VALUES
(1, 'First to Max Level Grand Prize', 'level', 'badge', 1, NULL, 'zeny', NULL, NULL, 1, 500000, 0, 'one_time', 'Congratulations on being the first to reach Maximum Base Level!', 1, NOW(), NOW()),
(2, 'Max Base Level Milestone', 'level', 'badge', 2, NULL, 'zeny', NULL, NULL, 1, 100000, 0, 'one_time', 'Congratulations on reaching Maximum Base Level!', 1, NOW(), NOW()),
(3, 'PvP Bronze 5-Star Milestone', 'pvp', 'tier', NULL, 9, 'zeny', NULL, NULL, 1, 25000, 0, 'one_time', 'You reached Bronze 5 Stars in PvP!', 1, NOW(), NOW()),
(4, 'MVP Hunter Bronze 5-Star Milestone', 'mvp', 'tier', NULL, 10, 'zeny', NULL, NULL, 1, 25000, 0, 'one_time', 'You reached Bronze 5 Stars in MVP Hunts!', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `zeny_amount` = VALUES(`zeny_amount`),
    `message` = VALUES(`message`),
    `is_enabled` = VALUES(`is_enabled`),
    `updated_at` = NOW();

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
