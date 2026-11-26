-- =====================================================================
-- Migration: 2026_11_23_000003_create_aether_downloads_table
-- Description: Create downloads table
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `downloads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('Full Client','Lite Client','Manual Patch','Tools & Addons') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Full Client',
  `version` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_mirror1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_mirror2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_mirror3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `downloads_category_is_active_index` (`category`,`is_active`),
  KEY `downloads_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_23_000003_create_aether_downloads_table', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_23_000003_create_aether_downloads_table'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
