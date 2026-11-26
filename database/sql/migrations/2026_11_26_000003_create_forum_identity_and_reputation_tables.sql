-- =====================================================================
-- Migration: 2026_11_26_000003_create_forum_identity_and_reputation_tables
-- Description: Create forum user profiles, reputation levels, transactions ledger, and audit logs tables
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `forum_user_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `identity_mode` enum('username','character') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'username',
  `selected_char_id` int unsigned DEFAULT NULL,
  `reputation_points` int NOT NULL DEFAULT '0',
  `forum_title` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forum_user_profiles_user_id_unique` (`user_id`),
  KEY `forum_user_profiles_selected_char_id_index` (`selected_char_id`),
  KEY `forum_user_profiles_reputation_points_index` (`reputation_points`),
  CONSTRAINT `forum_user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reputation_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_points` int NOT NULL DEFAULT '0',
  `badge_color` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#64748b',
  `badge_icon` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'award',
  `badge_style` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'solid',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reputation_levels_slug_unique` (`slug`),
  KEY `reputation_levels_min_points_index` (`min_points`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reputation_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `recipient_user_id` bigint unsigned NOT NULL,
  `source_user_id` bigint unsigned DEFAULT NULL,
  `event_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int NOT NULL,
  `topic_id` bigint unsigned DEFAULT NULL,
  `post_id` bigint unsigned DEFAULT NULL,
  `event_reference` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_reversal` tinyint(1) NOT NULL DEFAULT '0',
  `reversed_transaction_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reputation_transactions_event_reference_unique` (`event_reference`),
  KEY `reputation_transactions_source_user_id_index` (`source_user_id`),
  KEY `reputation_transactions_event_type_index` (`event_type`),
  KEY `reputation_transactions_topic_id_index` (`topic_id`),
  KEY `reputation_transactions_post_id_index` (`post_id`),
  KEY `rep_tx_recipient_created_idx` (`recipient_user_id`,`created_at`),
  KEY `rep_tx_event_created_idx` (`event_type`,`created_at`),
  CONSTRAINT `reputation_transactions_recipient_user_id_foreign` FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reputation_audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_user_id` bigint unsigned NOT NULL,
  `recipient_user_id` bigint unsigned NOT NULL,
  `points_delta` int NOT NULL,
  `previous_total` int NOT NULL,
  `new_total` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reputation_audit_logs_admin_user_id_foreign` (`admin_user_id`),
  KEY `reputation_audit_logs_recipient_user_id_foreign` (`recipient_user_id`),
  CONSTRAINT `reputation_audit_logs_admin_user_id_foreign` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reputation_audit_logs_recipient_user_id_foreign` FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_26_000003_create_forum_identity_and_reputation_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_26_000003_create_forum_identity_and_reputation_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
