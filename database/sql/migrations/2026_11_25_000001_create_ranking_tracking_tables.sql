-- =====================================================================
-- Migration: 2026_11_25_000001_create_ranking_tracking_tables
-- Description: Create ranking_events, ranking_character_stats, and emulator_sync_logs tables
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `ranking_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_uuid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `char_id` bigint unsigned DEFAULT NULL,
  `account_id` bigint unsigned DEFAULT NULL,
  `target_char_id` bigint unsigned DEFAULT NULL,
  `guild_id` bigint unsigned DEFAULT NULL,
  `mob_id` bigint unsigned DEFAULT NULL,
  `value` bigint NOT NULL DEFAULT '1',
  `payload` json DEFAULT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `rejection_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occurred_at` timestamp NOT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ranking_events_event_uuid_unique` (`event_uuid`),
  KEY `ranking_events_event_type_index` (`event_type`),
  KEY `ranking_events_char_id_index` (`char_id`),
  KEY `ranking_events_account_id_index` (`account_id`),
  KEY `ranking_events_target_char_id_index` (`target_char_id`),
  KEY `ranking_events_guild_id_index` (`guild_id`),
  KEY `ranking_events_status_index` (`status`),
  KEY `ranking_events_occurred_at_index` (`occurred_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ranking_character_stats` (
  `char_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `pvp_kills` int unsigned NOT NULL DEFAULT '0',
  `pvp_deaths` int unsigned NOT NULL DEFAULT '0',
  `mvp_kills` int unsigned NOT NULL DEFAULT '0',
  `max_base_reached_at` timestamp NULL DEFAULT NULL,
  `max_base_level_achieved` int unsigned DEFAULT NULL,
  `max_job_reached_at` timestamp NULL DEFAULT NULL,
  `max_job_level_achieved` int unsigned DEFAULT NULL,
  `last_event_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`char_id`),
  KEY `ranking_character_stats_account_id_index` (`account_id`),
  KEY `ranking_character_stats_pvp_kills_index` (`pvp_kills`),
  KEY `ranking_character_stats_mvp_kills_index` (`mvp_kills`),
  KEY `ranking_character_stats_max_base_reached_at_index` (`max_base_reached_at`),
  KEY `ranking_character_stats_max_job_reached_at_index` (`max_job_reached_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `emulator_sync_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sync_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `events_processed` int unsigned NOT NULL DEFAULT '0',
  `events_rejected` int unsigned NOT NULL DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `duration_ms` int unsigned NOT NULL DEFAULT '0',
  `synced_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emulator_sync_logs_sync_type_index` (`sync_type`),
  KEY `emulator_sync_logs_status_index` (`status`),
  KEY `emulator_sync_logs_synced_at_index` (`synced_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_25_000001_create_ranking_tracking_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_25_000001_create_ranking_tracking_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
