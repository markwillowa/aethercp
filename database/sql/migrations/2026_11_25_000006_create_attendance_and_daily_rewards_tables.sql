-- =====================================================================
-- Migration: 2026_11_25_000006_create_attendance_and_daily_rewards_tables
-- Description: Create attendance campaigns, days, items, participations, claims, deliveries, and player_inbox_items tables
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `attendance_campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `duration_days` int unsigned NOT NULL DEFAULT '7',
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `timezone` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UTC',
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `progression_mode` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cumulative',
  `reset_rule` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `participation_scope` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'account',
  `delivery_destination` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inbox',
  `allow_character_choice` tinyint(1) NOT NULL DEFAULT '1',
  `require_in_game_login` tinyint(1) NOT NULL DEFAULT '0',
  `repeatable` tinyint(1) NOT NULL DEFAULT '0',
  `display_order` int NOT NULL DEFAULT '0',
  `min_base_level` int unsigned NOT NULL DEFAULT '1',
  `min_account_age_days` int unsigned NOT NULL DEFAULT '0',
  `daily_reset_hour` smallint unsigned NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_campaigns_slug_unique` (`slug`),
  KEY `attendance_campaigns_status_index` (`status`),
  KEY `attendance_campaigns_progression_mode_index` (`progression_mode`),
  KEY `attendance_campaigns_participation_scope_index` (`participation_scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance_campaign_days` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned NOT NULL,
  `day_number` int unsigned NOT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_milestone` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_campaign_days_campaign_id_day_number_unique` (`campaign_id`,`day_number`),
  KEY `attendance_campaign_days_day_number_index` (`day_number`),
  CONSTRAINT `attendance_campaign_days_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `attendance_campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance_campaign_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_day_id` bigint unsigned NOT NULL,
  `reward_type` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'item',
  `item_id` int unsigned DEFAULT NULL,
  `item_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `zeny_amount` bigint unsigned NOT NULL DEFAULT '0',
  `cashpoints_amount` int unsigned NOT NULL DEFAULT '0',
  `refine` tinyint unsigned NOT NULL DEFAULT '0',
  `card0` smallint unsigned NOT NULL DEFAULT '0',
  `card1` smallint unsigned NOT NULL DEFAULT '0',
  `card2` smallint unsigned NOT NULL DEFAULT '0',
  `card3` smallint unsigned NOT NULL DEFAULT '0',
  `identified` tinyint(1) NOT NULL DEFAULT '1',
  `bound` tinyint unsigned NOT NULL DEFAULT '0',
  `delivery_method` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_campaign_items_campaign_day_id_foreign` (`campaign_day_id`),
  CONSTRAINT `attendance_campaign_items_campaign_day_id_foreign` FOREIGN KEY (`campaign_day_id`) REFERENCES `attendance_campaign_days` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance_participations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `character_id` bigint unsigned NOT NULL DEFAULT '0',
  `current_progress` int unsigned NOT NULL DEFAULT '0',
  `current_streak` int unsigned NOT NULL DEFAULT '0',
  `highest_streak` int unsigned NOT NULL DEFAULT '0',
  `total_claimed_days` int unsigned NOT NULL DEFAULT '0',
  `last_attendance_date` date DEFAULT NULL,
  `last_claimed_at` datetime DEFAULT NULL,
  `started_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `att_part_unique` (`campaign_id`,`account_id`,`character_id`),
  KEY `attendance_participations_user_id_index` (`user_id`),
  KEY `attendance_participations_account_id_index` (`account_id`),
  KEY `attendance_participations_character_id_index` (`character_id`),
  KEY `attendance_participations_last_attendance_date_index` (`last_attendance_date`),
  KEY `attendance_participations_status_index` (`status`),
  CONSTRAINT `attendance_participations_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `attendance_campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance_claims` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `participation_id` bigint unsigned NOT NULL,
  `campaign_id` bigint unsigned NOT NULL,
  `campaign_day_id` bigint unsigned NOT NULL,
  `day_number` int unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `character_id` bigint unsigned DEFAULT NULL,
  `claimed_at` datetime NOT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivered',
  `idempotency_key` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `att_claim_part_day_unique` (`participation_id`,`day_number`),
  UNIQUE KEY `attendance_claims_idempotency_key_unique` (`idempotency_key`),
  KEY `attendance_claims_campaign_id_index` (`campaign_id`),
  KEY `attendance_claims_campaign_day_id_index` (`campaign_day_id`),
  KEY `attendance_claims_account_id_index` (`account_id`),
  KEY `attendance_claims_character_id_index` (`character_id`),
  KEY `attendance_claims_claimed_at_index` (`claimed_at`),
  KEY `attendance_claims_status_index` (`status`),
  CONSTRAINT `attendance_claims_participation_id_foreign` FOREIGN KEY (`participation_id`) REFERENCES `attendance_participations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `claim_id` bigint unsigned NOT NULL,
  `campaign_item_id` bigint unsigned DEFAULT NULL,
  `reward_type` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination_target` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivered',
  `attempts` smallint unsigned NOT NULL DEFAULT '1',
  `delivered_at` datetime DEFAULT NULL,
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `external_reference` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_deliveries_claim_id_foreign` (`claim_id`),
  KEY `attendance_deliveries_campaign_item_id_index` (`campaign_item_id`),
  KEY `attendance_deliveries_status_index` (`status`),
  CONSTRAINT `attendance_deliveries_claim_id_foreign` FOREIGN KEY (`claim_id`) REFERENCES `attendance_claims` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `player_inbox_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `character_id` bigint unsigned DEFAULT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `source_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attendance',
  `source_id` bigint unsigned DEFAULT NULL,
  `reward_type` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'item',
  `item_id` int unsigned DEFAULT NULL,
  `item_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `zeny_amount` bigint unsigned NOT NULL DEFAULT '0',
  `cashpoints_amount` int unsigned NOT NULL DEFAULT '0',
  `refine` tinyint unsigned NOT NULL DEFAULT '0',
  `card0` smallint unsigned NOT NULL DEFAULT '0',
  `card1` smallint unsigned NOT NULL DEFAULT '0',
  `card2` smallint unsigned NOT NULL DEFAULT '0',
  `card3` smallint unsigned NOT NULL DEFAULT '0',
  `identified` tinyint(1) NOT NULL DEFAULT '1',
  `bound` tinyint unsigned NOT NULL DEFAULT '0',
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unclaimed',
  `claimed_to` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `claimed_character_id` bigint unsigned DEFAULT NULL,
  `claimed_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `player_inbox_items_user_id_index` (`user_id`),
  KEY `player_inbox_items_account_id_index` (`account_id`),
  KEY `player_inbox_items_character_id_index` (`character_id`),
  KEY `player_inbox_items_source_type_index` (`source_type`),
  KEY `player_inbox_items_source_id_index` (`source_id`),
  KEY `player_inbox_items_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_25_000006_create_attendance_and_daily_rewards_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_25_000006_create_attendance_and_daily_rewards_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
