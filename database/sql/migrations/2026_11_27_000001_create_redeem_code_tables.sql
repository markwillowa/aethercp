-- =====================================================================
-- Migration: 2026_11_27_000001_create_redeem_code_tables
-- Description: Create Redeem Code Management tables (campaigns, codes, rewards, redemptions, deliveries, logs)
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `redeem_campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `banner_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general_promotion',
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `max_total_redemptions` int unsigned DEFAULT NULL,
  `max_per_account` int unsigned NOT NULL DEFAULT '1',
  `one_per_account` tinyint(1) NOT NULL DEFAULT '1',
  `one_per_character` tinyint(1) NOT NULL DEFAULT '0',
  `allowed_account_ids` json DEFAULT NULL,
  `allowed_character_ids` json DEFAULT NULL,
  `account_age_rule` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'any',
  `account_created_after` timestamp NULL DEFAULT NULL,
  `account_created_before` timestamp NULL DEFAULT NULL,
  `min_account_age_days` int unsigned DEFAULT NULL,
  `min_base_level` int unsigned DEFAULT NULL,
  `max_base_level` int unsigned DEFAULT NULL,
  `allowed_classes` json DEFAULT NULL,
  `excluded_classes` json DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `redeem_campaigns_slug_unique` (`slug`),
  KEY `redeem_campaigns_campaign_type_index` (`campaign_type`),
  KEY `redeem_campaigns_starts_at_index` (`starts_at`),
  KEY `redeem_campaigns_ends_at_index` (`ends_at`),
  KEY `redeem_campaigns_status_index` (`status`),
  KEY `redeem_campaigns_is_public_index` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `redeem_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned NOT NULL,
  `code_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_prefix` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_hint` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single',
  `max_redemptions` int unsigned NOT NULL DEFAULT '1',
  `redemptions_count` int unsigned NOT NULL DEFAULT '0',
  `bound_account_id` bigint unsigned DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `redeem_codes_code_hash_unique` (`code_hash`),
  KEY `redeem_codes_code_prefix_index` (`code_prefix`),
  KEY `redeem_codes_code_type_index` (`code_type`),
  KEY `redeem_codes_bound_account_id_index` (`bound_account_id`),
  KEY `redeem_codes_expires_at_index` (`expires_at`),
  KEY `redeem_codes_status_index` (`status`),
  KEY `redeem_codes_campaign_id_status_index` (`campaign_id`,`status`),
  CONSTRAINT `redeem_codes_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `redeem_campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `redeem_code_rewards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned NOT NULL,
  `redeem_code_id` bigint unsigned DEFAULT NULL,
  `reward_type` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` bigint unsigned NOT NULL DEFAULT '0',
  `delivery_scope` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'account',
  `item_id` int unsigned DEFAULT NULL,
  `item_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refine` tinyint unsigned NOT NULL DEFAULT '0',
  `card0` int unsigned NOT NULL DEFAULT '0',
  `card1` int unsigned NOT NULL DEFAULT '0',
  `card2` int unsigned NOT NULL DEFAULT '0',
  `card3` int unsigned NOT NULL DEFAULT '0',
  `is_bound` tinyint(1) NOT NULL DEFAULT '0',
  `custom_payload` json DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `redeem_code_rewards_campaign_id_foreign` (`campaign_id`),
  KEY `redeem_code_rewards_redeem_code_id_foreign` (`redeem_code_id`),
  KEY `redeem_code_rewards_reward_type_index` (`reward_type`),
  KEY `redeem_code_rewards_item_id_index` (`item_id`),
  CONSTRAINT `redeem_code_rewards_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `redeem_campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `redeem_code_rewards_redeem_code_id_foreign` FOREIGN KEY (`redeem_code_id`) REFERENCES `redeem_codes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `redeem_redemptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned NOT NULL,
  `redeem_code_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `username_snapshot` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `character_id` bigint unsigned DEFAULT NULL,
  `character_name_snapshot` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_hint` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `delivery_status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `idempotency_key` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reward_summary` json DEFAULT NULL,
  `redeemed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `redeem_redemptions_idempotency_key_unique` (`idempotency_key`),
  KEY `redeem_redemptions_user_id_foreign` (`user_id`),
  KEY `redeem_redemptions_account_id_index` (`account_id`),
  KEY `redeem_redemptions_character_id_index` (`character_id`),
  KEY `redeem_redemptions_status_index` (`status`),
  KEY `redeem_redemptions_delivery_status_index` (`delivery_status`),
  KEY `redeem_redemptions_redeemed_at_index` (`redeemed_at`),
  KEY `redeem_redemptions_redeem_code_id_account_id_index` (`redeem_code_id`,`account_id`),
  KEY `redeem_redemptions_campaign_id_account_id_index` (`campaign_id`,`account_id`),
  CONSTRAINT `redeem_redemptions_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `redeem_campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `redeem_redemptions_redeem_code_id_foreign` FOREIGN KEY (`redeem_code_id`) REFERENCES `redeem_codes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `redeem_redemptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `redeem_reward_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `redemption_id` bigint unsigned NOT NULL,
  `reward_id` bigint unsigned DEFAULT NULL,
  `reward_type` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idempotency_key` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `account_id` bigint unsigned NOT NULL,
  `character_id` bigint unsigned DEFAULT NULL,
  `amount` bigint unsigned NOT NULL DEFAULT '0',
  `item_id` int unsigned DEFAULT NULL,
  `item_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `destination_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `redeem_reward_deliveries_idempotency_key_unique` (`idempotency_key`),
  KEY `redeem_reward_deliveries_redemption_id_foreign` (`redemption_id`),
  KEY `redeem_reward_deliveries_reward_id_foreign` (`reward_id`),
  KEY `redeem_reward_deliveries_reward_type_index` (`reward_type`),
  KEY `redeem_reward_deliveries_status_index` (`status`),
  KEY `redeem_reward_deliveries_account_id_index` (`account_id`),
  KEY `redeem_reward_deliveries_character_id_index` (`character_id`),
  CONSTRAINT `redeem_reward_deliveries_redemption_id_foreign` FOREIGN KEY (`redemption_id`) REFERENCES `redeem_redemptions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `redeem_reward_deliveries_reward_id_foreign` FOREIGN KEY (`reward_id`) REFERENCES `redeem_code_rewards` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `redeem_campaign_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned DEFAULT NULL,
  `redeem_code_id` bigint unsigned DEFAULT NULL,
  `redemption_id` bigint unsigned DEFAULT NULL,
  `actor_user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `redeem_campaign_logs_campaign_id_foreign` (`campaign_id`),
  KEY `redeem_campaign_logs_redeem_code_id_foreign` (`redeem_code_id`),
  KEY `redeem_campaign_logs_redemption_id_foreign` (`redemption_id`),
  KEY `redeem_campaign_logs_actor_user_id_index` (`actor_user_id`),
  KEY `redeem_campaign_logs_action_index` (`action`),
  CONSTRAINT `redeem_campaign_logs_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `redeem_campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `redeem_campaign_logs_redeem_code_id_foreign` FOREIGN KEY (`redeem_code_id`) REFERENCES `redeem_codes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `redeem_campaign_logs_redemption_id_foreign` FOREIGN KEY (`redemption_id`) REFERENCES `redeem_redemptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_27_000001_create_redeem_code_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_27_000001_create_redeem_code_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
