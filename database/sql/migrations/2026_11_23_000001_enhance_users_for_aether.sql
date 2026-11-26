-- =====================================================================
-- Migration: 2026_11_23_000001_enhance_users_for_aether
-- Description: Add AetherCP account fields to users table
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

ALTER TABLE `users`
  ADD COLUMN `account_id` int unsigned DEFAULT NULL AFTER `id`,
  ADD COLUMN `username` varchar(23) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `account_id`,
  ADD COLUMN `role` enum('admin','staff','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user' AFTER `password`,
  ADD COLUMN `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `role`,
  ADD COLUMN `last_login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `avatar`,
  ADD COLUMN `last_login_at` timestamp NULL DEFAULT NULL AFTER `last_login_ip`,
  ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT '1' AFTER `last_login_at`,
  ADD UNIQUE KEY `users_account_id_unique` (`account_id`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_23_000001_enhance_users_for_aether', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_23_000001_enhance_users_for_aether'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
