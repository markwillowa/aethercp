-- =====================================================================
-- Migration: 2026_11_25_000004_create_player_profiles_tables
-- Description: Create player_profile_settings and player_achievements tables
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `player_profile_settings` (
  `char_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `show_char_info` tinyint(1) NOT NULL DEFAULT '1',
  `show_equipments` tinyint(1) NOT NULL DEFAULT '1',
  `show_zeny` tinyint(1) NOT NULL DEFAULT '1',
  `show_rankings` tinyint(1) NOT NULL DEFAULT '1',
  `show_badges` tinyint(1) NOT NULL DEFAULT '1',
  `show_achievements` tinyint(1) NOT NULL DEFAULT '1',
  `show_stats` tinyint(1) NOT NULL DEFAULT '1',
  `show_guild` tinyint(1) NOT NULL DEFAULT '1',
  `show_last_login` tinyint(1) NOT NULL DEFAULT '0',
  `bio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`char_id`),
  KEY `player_profile_settings_account_id_index` (`account_id`),
  KEY `player_profile_settings_is_public_index` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `player_achievements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `char_id` bigint unsigned NOT NULL,
  `achievement_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'milestone',
  `icon` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trophy',
  `color` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#f59e0b',
  `earned_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_achievements_char_key_unique` (`char_id`,`achievement_key`),
  KEY `player_achievements_char_id_index` (`char_id`),
  KEY `player_achievements_achievement_key_index` (`achievement_key`),
  KEY `player_achievements_earned_at_index` (`earned_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_25_000004_create_player_profiles_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_25_000004_create_player_profiles_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
