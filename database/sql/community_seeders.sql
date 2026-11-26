-- =====================================================================
-- AetherCP Community Forum & Reputation Seeders
-- Compatible with MySQL 5.7+ / MariaDB / phpMyAdmin
-- Non-destructive: Uses ON DUPLICATE KEY UPDATE to preserve existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

-- 1. Reputation Levels
INSERT INTO `reputation_levels` (`id`, `name`, `slug`, `min_points`, `badge_color`, `badge_icon`, `badge_style`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Newcomer', 'newcomer', 0, '#64748b', 'sprout', 'subtle', 1, 1, NOW(), NOW()),
(2, 'Contributor', 'contributor', 10, '#10b981', 'sparkles', 'solid', 2, 1, NOW(), NOW()),
(3, 'Active Member', 'active-member', 50, '#06b6d4', 'shield', 'solid', 3, 1, NOW(), NOW()),
(4, 'Veteran', 'veteran', 150, '#3b82f6', 'swords', 'solid', 4, 1, NOW(), NOW()),
(5, 'Elite Member', 'elite-member', 400, '#8b5cf6', 'gem', 'glow', 5, 1, NOW(), NOW()),
(6, 'Community Legend', 'community-legend', 1000, '#f59e0b', 'crown', 'glow', 6, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `min_points` = VALUES(`min_points`),
    `badge_color` = VALUES(`badge_color`),
    `badge_icon` = VALUES(`badge_icon`),
    `badge_style` = VALUES(`badge_style`),
    `sort_order` = VALUES(`sort_order`),
    `is_active` = VALUES(`is_active`),
    `updated_at` = NOW();

-- 2. Forum Categories
INSERT INTO `forum_categories` (`id`, `name`, `slug`, `description`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Realm & Official', 'realm-official', 'Official announcements, patch notes, and server maintenance updates.', 'megaphone', 1, 1, NOW(), NOW()),
(2, 'Adventurer Tavern', 'adventurer-tavern', 'General Ragnarok Online discussions, class guides, and party recruitment.', 'message-square', 2, 1, NOW(), NOW()),
(3, 'Trade & Support', 'trade-support', 'Marketplace price checks, technical help, and player Q&A.', 'life-buoy', 3, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `description` = VALUES(`description`),
    `icon` = VALUES(`icon`),
    `sort_order` = VALUES(`sort_order`),
    `is_active` = VALUES(`is_active`),
    `updated_at` = NOW();

-- 3. Forum Boards
INSERT INTO `forum_boards` (`id`, `category_id`, `name`, `slug`, `description`, `icon`, `sort_order`, `is_active`, `is_locked`, `allow_topics_roles`, `topics_count`, `posts_count`, `created_at`, `updated_at`) VALUES
(1, 1, 'Announcements & Patch Notes', 'announcements', 'Official server news, events, and changelog discussions.', 'bell', 1, 1, 0, '[\"admin\",\"staff\"]', 0, 0, NOW(), NOW()),
(2, 1, 'Server Events & Contests', 'server-events', 'Community tournaments, WoE schedules, and seasonal contests.', 'trophy', 2, 1, 0, NULL, 0, 0, NOW(), NOW()),
(3, 2, 'General Discussion', 'general-discussion', 'Chat about anything related to Rune-Midgard and our community.', 'messages-square', 1, 1, 0, NULL, 0, 0, NOW(), NOW()),
(4, 2, 'Class Builds & Guides', 'class-guides', 'Share equipment builds, MVP strategies, and leveling guides.', 'book-open', 2, 1, 0, NULL, 0, 0, NOW(), NOW()),
(5, 2, 'Guild Recruitment & Diplomacy', 'guild-recruitment', 'Recruit adventurers for WoE, Endless Tower, and instance runs.', 'flag', 3, 1, 0, NULL, 0, 0, NOW(), NOW()),
(6, 3, 'Questions & Help', 'questions-help', 'Ask the community for help with quests, client setup, or mechanics.', 'help-circle', 1, 1, 0, NULL, 0, 0, NOW(), NOW()),
(7, 3, 'Market & Price Checks', 'market-trade', 'Buy, sell, trade, and request item price checks from fellow players.', 'coins', 2, 1, 0, NULL, 0, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `category_id` = VALUES(`category_id`),
    `name` = VALUES(`name`),
    `description` = VALUES(`description`),
    `icon` = VALUES(`icon`),
    `sort_order` = VALUES(`sort_order`),
    `is_active` = VALUES(`is_active`),
    `allow_topics_roles` = VALUES(`allow_topics_roles`),
    `updated_at` = NOW();

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
