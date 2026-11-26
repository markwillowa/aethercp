-- =====================================================================
-- Migration: 2026_11_27_000002_create_character_management_tables
-- Description: Create Character Management transactions, audit logs, locks, deletion/recovery requests, and admin notes
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `character_service_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `idempotency_key` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `char_id` bigint unsigned NOT NULL,
  `char_name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cash_points_cost` int unsigned NOT NULL DEFAULT '0',
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `refunded_by` bigint unsigned DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `character_service_transactions_idempotency_key_unique` (`idempotency_key`),
  KEY `character_service_transactions_user_id_foreign` (`user_id`),
  KEY `character_service_transactions_account_id_index` (`account_id`),
  KEY `character_service_transactions_char_id_index` (`char_id`),
  KEY `character_service_transactions_service_type_index` (`service_type`),
  KEY `character_service_transactions_status_index` (`status`),
  KEY `char_svc_cooldown_idx` (`char_id`,`service_type`,`created_at`),
  CONSTRAINT `character_service_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `character_service_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `actor_user_id` bigint unsigned DEFAULT NULL,
  `actor_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'player',
  `account_id` bigint unsigned DEFAULT NULL,
  `char_id` bigint unsigned NOT NULL,
  `action_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_values` json DEFAULT NULL,
  `updated_values` json DEFAULT NULL,
  `service_cost` int unsigned NOT NULL DEFAULT '0',
  `transaction_reference` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `result` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_service_logs_actor_user_id_index` (`actor_user_id`),
  KEY `character_service_logs_account_id_index` (`account_id`),
  KEY `character_service_logs_char_id_index` (`char_id`),
  KEY `character_service_logs_action_type_index` (`action_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `character_locks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `char_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT '1',
  `lock_scope` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'services',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locked_by` bigint unsigned DEFAULT NULL,
  `locked_at` timestamp NOT NULL,
  `unlocked_by` bigint unsigned DEFAULT NULL,
  `unlocked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_locks_char_id_index` (`char_id`),
  KEY `character_locks_account_id_index` (`account_id`),
  KEY `character_locks_is_locked_index` (`is_locked`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `character_deletion_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `char_id` bigint unsigned NOT NULL,
  `char_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `requested_at` timestamp NOT NULL,
  `scheduled_delete_at` timestamp NOT NULL,
  `executed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `recovery_status` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_deletion_requests_user_id_foreign` (`user_id`),
  KEY `character_deletion_requests_account_id_index` (`account_id`),
  KEY `character_deletion_requests_char_id_index` (`char_id`),
  KEY `character_deletion_requests_status_index` (`status`),
  KEY `character_deletion_requests_scheduled_delete_at_index` (`scheduled_delete_at`),
  CONSTRAINT `character_deletion_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `character_admin_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `char_id` bigint unsigned NOT NULL,
  `admin_user_id` bigint unsigned NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_admin_notes_char_id_index` (`char_id`),
  KEY `character_admin_notes_admin_user_id_foreign` (`admin_user_id`),
  CONSTRAINT `character_admin_notes_admin_user_id_foreign` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_27_000002_create_character_management_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_27_000002_create_character_management_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
