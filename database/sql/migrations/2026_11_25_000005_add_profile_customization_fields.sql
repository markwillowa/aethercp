-- =====================================================================
-- Migration: 2026_11_25_000005_add_profile_customization_fields
-- Description: Add show_equipments, show_zeny, and avatar_path to player_profile_settings
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

ALTER TABLE `player_profile_settings`
  ADD COLUMN `show_equipments` tinyint(1) NOT NULL DEFAULT '1' AFTER `show_char_info`,
  ADD COLUMN `show_zeny` tinyint(1) NOT NULL DEFAULT '1' AFTER `show_equipments`,
  ADD COLUMN `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `bio`;

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_25_000005_add_profile_customization_fields', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_25_000005_add_profile_customization_fields'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
