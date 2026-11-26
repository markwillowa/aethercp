-- =====================================================================
-- Migration: 2026_11_25_000002_create_badge_system_tables
-- Description: Create badge_definitions, badge_tiers, and badge_assignments tables
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `badge_definitions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_type` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lucide',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'award',
  `color` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#06b6d4',
  `visual_style` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'solid',
  `priority` int NOT NULL DEFAULT '10',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `is_permanent` tinyint(1) NOT NULL DEFAULT '1',
  `auto_condition_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_condition_value` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_definitions_slug_unique` (`slug`),
  KEY `badge_definitions_category_index` (`category`),
  KEY `badge_definitions_priority_index` (`priority`),
  KEY `badge_definitions_is_enabled_index` (`is_enabled`),
  KEY `badge_definitions_auto_condition_type_index` (`auto_condition_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `badge_tiers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tier_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tier_order` tinyint unsigned NOT NULL,
  `stars` tinyint unsigned NOT NULL,
  `min_kills` int unsigned NOT NULL,
  `color` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#cd7f32',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_tiers_cat_name_stars_unique` (`category`,`tier_name`,`stars`),
  KEY `badge_tiers_category_index` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `badge_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` bigint unsigned DEFAULT NULL,
  `badge_tier_id` bigint unsigned DEFAULT NULL,
  `target_type` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` bigint unsigned NOT NULL,
  `category` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stars` tinyint unsigned DEFAULT NULL,
  `color` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#06b6d4',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_permanent` tinyint(1) NOT NULL DEFAULT '1',
  `assigned_by` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `assigned_by_user_id` bigint unsigned DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `awarded_at` timestamp NOT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `badge_assignments_badge_id_foreign` (`badge_id`),
  KEY `badge_assignments_badge_tier_id_foreign` (`badge_tier_id`),
  KEY `badge_assignments_target_active_idx` (`target_type`,`target_id`,`is_active`),
  KEY `badge_assignments_category_index` (`category`),
  KEY `badge_assignments_is_active_index` (`is_active`),
  KEY `badge_assignments_awarded_at_index` (`awarded_at`),
  CONSTRAINT `badge_assignments_badge_id_foreign` FOREIGN KEY (`badge_id`) REFERENCES `badge_definitions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `badge_assignments_badge_tier_id_foreign` FOREIGN KEY (`badge_tier_id`) REFERENCES `badge_tiers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_25_000002_create_badge_system_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_25_000002_create_badge_system_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
