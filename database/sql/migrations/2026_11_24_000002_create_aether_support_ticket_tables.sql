-- =====================================================================
-- Migration: 2026_11_24_000002_create_aether_support_ticket_tables
-- Description: Create support_tickets, support_ticket_messages, support_ticket_attachments, and support_ticket_activities tables
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_reference` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `account_id` int unsigned NOT NULL,
  `char_id` int unsigned DEFAULT NULL,
  `char_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `priority` enum('low','normal','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `status` enum('open','in_progress','awaiting_player','awaiting_staff','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `assigned_staff_id` bigint unsigned DEFAULT NULL,
  `related_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `map_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incident_time` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_message_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_reference_unique` (`ticket_reference`),
  KEY `support_tickets_user_id_foreign` (`user_id`),
  KEY `support_tickets_assigned_staff_id_foreign` (`assigned_staff_id`),
  KEY `support_tickets_account_id_index` (`account_id`),
  KEY `support_tickets_char_id_index` (`char_id`),
  KEY `support_tickets_category_index` (`category`),
  KEY `support_tickets_priority_index` (`priority`),
  KEY `support_tickets_status_index` (`status`),
  KEY `support_tickets_last_message_at_index` (`last_message_at`),
  CONSTRAINT `support_tickets_assigned_staff_id_foreign` FOREIGN KEY (`assigned_staff_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `support_ticket_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `sender_type` enum('player','staff','system') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'player',
  `sender_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_messages_ticket_id_foreign` (`ticket_id`),
  KEY `support_ticket_messages_user_id_foreign` (`user_id`),
  KEY `support_ticket_messages_is_internal_index` (`is_internal`),
  CONSTRAINT `support_ticket_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `support_ticket_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `message_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stored_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_attachments_ticket_id_foreign` (`ticket_id`),
  KEY `support_ticket_attachments_message_id_foreign` (`message_id`),
  KEY `support_ticket_attachments_user_id_foreign` (`user_id`),
  CONSTRAINT `support_ticket_attachments_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `support_ticket_messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_attachments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `support_ticket_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `activity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_activities_ticket_id_foreign` (`ticket_id`),
  KEY `support_ticket_activities_user_id_foreign` (`user_id`),
  CONSTRAINT `support_ticket_activities_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_24_000002_create_aether_support_ticket_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_24_000002_create_aether_support_ticket_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
