-- =====================================================================
-- Migration: 2026_11_25_000003_create_reward_system_tables
-- Description: Create reward_definitions, reward_issuances, and reward_delivery_logs tables
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `reward_definitions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trigger_type` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'badge',
  `badge_id` bigint unsigned DEFAULT NULL,
  `badge_tier_id` bigint unsigned DEFAULT NULL,
  `reward_type` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'zeny',
  `zeny_amount` bigint unsigned NOT NULL DEFAULT '0',
  `items_json` json DEFAULT NULL,
  `policy` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'one_time',
  `period_key` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reward_definitions_badge_id_foreign` (`badge_id`),
  KEY `reward_definitions_badge_tier_id_foreign` (`badge_tier_id`),
  KEY `reward_definitions_category_index` (`category`),
  KEY `reward_definitions_is_enabled_index` (`is_enabled`),
  CONSTRAINT `reward_definitions_badge_id_foreign` FOREIGN KEY (`badge_id`) REFERENCES `badge_definitions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reward_definitions_badge_tier_id_foreign` FOREIGN KEY (`badge_tier_id`) REFERENCES `badge_tiers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reward_issuances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `idempotency_key` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_ref` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reward_definition_id` bigint unsigned NOT NULL,
  `target_type` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `char_id` bigint unsigned DEFAULT NULL,
  `account_id` bigint unsigned DEFAULT NULL,
  `guild_id` bigint unsigned DEFAULT NULL,
  `reward_type` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zeny_amount` bigint unsigned NOT NULL DEFAULT '0',
  `items_json` json DEFAULT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `last_error` text COLLATE utf8mb4_unicode_ci,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reward_issuances_idempotency_key_unique` (`idempotency_key`),
  UNIQUE KEY `reward_issuances_transaction_ref_unique` (`transaction_ref`),
  KEY `reward_issuances_reward_definition_id_foreign` (`reward_definition_id`),
  KEY `reward_issuances_char_id_index` (`char_id`),
  KEY `reward_issuances_account_id_index` (`account_id`),
  KEY `reward_issuances_guild_id_index` (`guild_id`),
  KEY `reward_issuances_status_index` (`status`),
  CONSTRAINT `reward_issuances_reward_definition_id_foreign` FOREIGN KEY (`reward_definition_id`) REFERENCES `reward_definitions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reward_delivery_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reward_issuance_id` bigint unsigned NOT NULL,
  `attempt_number` tinyint unsigned NOT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `attempted_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reward_delivery_logs_reward_issuance_id_foreign` (`reward_issuance_id`),
  CONSTRAINT `reward_delivery_logs_reward_issuance_id_foreign` FOREIGN KEY (`reward_issuance_id`) REFERENCES `reward_issuances` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_25_000003_create_reward_system_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_25_000003_create_reward_system_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
