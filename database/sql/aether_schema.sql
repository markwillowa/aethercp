-- =====================================================================
-- AetherCP Complete Database Schema
-- Designed for phpMyAdmin & Direct MySQL/MariaDB Import (cPanel Ready)
-- Generated: 2026-11-24 23:06:57
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- -----------------------------------------------------
-- Table structure for `activity_logs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `account_id` int unsigned DEFAULT NULL,
  `action` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_action_index` (`action`),
  KEY `activity_logs_created_at_index` (`created_at`),
  KEY `activity_logs_user_id_index` (`user_id`),
  KEY `activity_logs_account_id_index` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `attendance_campaign_days`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `attendance_campaign_days`;
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

-- -----------------------------------------------------
-- Table structure for `attendance_campaign_items`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `attendance_campaign_items`;
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

-- -----------------------------------------------------
-- Table structure for `attendance_campaigns`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `attendance_campaigns`;
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

-- -----------------------------------------------------
-- Table structure for `attendance_claims`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `attendance_claims`;
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

-- -----------------------------------------------------
-- Table structure for `attendance_deliveries`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `attendance_deliveries`;
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

-- -----------------------------------------------------
-- Table structure for `attendance_participations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `attendance_participations`;
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

-- -----------------------------------------------------
-- Table structure for `badge_assignments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `badge_assignments`;
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

-- -----------------------------------------------------
-- Table structure for `badge_definitions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `badge_definitions`;
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

-- -----------------------------------------------------
-- Table structure for `badge_tiers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `badge_tiers`;
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

-- -----------------------------------------------------
-- Table structure for `cache`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `cache_locks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `donation_packages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `donation_packages`;
CREATE TABLE IF NOT EXISTS `donation_packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `points` int NOT NULL,
  `bonus_points` int NOT NULL DEFAULT '0',
  `item_id` int unsigned DEFAULT NULL,
  `item_amount` int unsigned NOT NULL DEFAULT '0',
  `item_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_popular` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `donations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `donations`;
CREATE TABLE IF NOT EXISTS `donations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int unsigned NOT NULL,
  `package_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `points_awarded` int NOT NULL DEFAULT '0',
  `points_delivered` tinyint(1) NOT NULL DEFAULT '0',
  `items_delivered` tinyint(1) NOT NULL DEFAULT '0',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `transaction_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `ipn_data` text COLLATE utf8mb4_unicode_ci,
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `donations_transaction_reference_unique` (`transaction_reference`),
  KEY `donations_package_id_foreign` (`package_id`),
  KEY `donations_account_id_status_index` (`account_id`,`status`),
  KEY `donations_account_id_index` (`account_id`),
  CONSTRAINT `donations_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `donation_packages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `downloads`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `downloads`;
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

-- -----------------------------------------------------
-- Table structure for `emulator_sync_logs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `emulator_sync_logs`;
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

-- -----------------------------------------------------
-- Table structure for `failed_jobs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `faq_categories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `faq_categories`;
CREATE TABLE IF NOT EXISTS `faq_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'help-circle',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faq_categories_slug_unique` (`slug`),
  KEY `faq_categories_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `faq_feedback`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `faq_feedback`;
CREATE TABLE IF NOT EXISTS `faq_feedback` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `faq_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned DEFAULT NULL,
  `feedback_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `faq_feedback_faq_id_account_id_index` (`faq_id`,`account_id`),
  KEY `faq_feedback_faq_id_ip_address_index` (`faq_id`,`ip_address`),
  CONSTRAINT `faq_feedback_faq_id_foreign` FOREIGN KEY (`faq_id`) REFERENCES `faqs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `faq_related_questions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `faq_related_questions`;
CREATE TABLE IF NOT EXISTS `faq_related_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `faq_id` bigint unsigned NOT NULL,
  `related_faq_id` bigint unsigned NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `faq_related_questions_faq_id_related_faq_id_unique` (`faq_id`,`related_faq_id`),
  KEY `faq_related_questions_related_faq_id_foreign` (`related_faq_id`),
  CONSTRAINT `faq_related_questions_faq_id_foreign` FOREIGN KEY (`faq_id`) REFERENCES `faqs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `faq_related_questions_related_faq_id_foreign` FOREIGN KEY (`related_faq_id`) REFERENCES `faqs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `faq_search_logs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `faq_search_logs`;
CREATE TABLE IF NOT EXISTS `faq_search_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `search_term` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `results_count` int NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `faq_search_logs_search_term_index` (`search_term`),
  KEY `faq_search_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `faqs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned DEFAULT NULL,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `answer` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `views_count` bigint unsigned NOT NULL DEFAULT '0',
  `seo_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faqs_slug_unique` (`slug`),
  KEY `faqs_created_by_foreign` (`created_by`),
  KEY `faqs_updated_by_foreign` (`updated_by`),
  KEY `faqs_status_is_featured_index` (`status`,`is_featured`),
  KEY `faqs_category_id_status_index` (`category_id`,`status`),
  KEY `faqs_views_count_index` (`views_count`),
  CONSTRAINT `faqs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `faq_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `faqs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `faqs_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `job_batches`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `jobs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `marketplace_items`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `marketplace_items`;
CREATE TABLE IF NOT EXISTS `marketplace_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `account_id` int unsigned NOT NULL,
  `char_id` int unsigned NOT NULL,
  `nameid` int unsigned NOT NULL,
  `item_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `amount` int unsigned NOT NULL DEFAULT '1',
  `equip` int unsigned NOT NULL DEFAULT '0',
  `identify` tinyint unsigned NOT NULL DEFAULT '1',
  `refine` tinyint unsigned NOT NULL DEFAULT '0',
  `attribute` tinyint unsigned NOT NULL DEFAULT '0',
  `card0` int unsigned NOT NULL DEFAULT '0',
  `card1` int unsigned NOT NULL DEFAULT '0',
  `card2` int unsigned NOT NULL DEFAULT '0',
  `card3` int unsigned NOT NULL DEFAULT '0',
  `expire_time` bigint unsigned NOT NULL DEFAULT '0',
  `bound` tinyint unsigned NOT NULL DEFAULT '0',
  `unique_id` bigint unsigned NOT NULL DEFAULT '0',
  `item_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Etc',
  `status` enum('in_custody','listed','sold','withdrawn','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_custody',
  `deposited_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `withdrawn_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `marketplace_items_user_id_foreign` (`user_id`),
  KEY `marketplace_items_account_id_index` (`account_id`),
  KEY `marketplace_items_char_id_index` (`char_id`),
  KEY `marketplace_items_nameid_index` (`nameid`),
  KEY `marketplace_items_status_index` (`status`),
  CONSTRAINT `marketplace_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `marketplace_listings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `marketplace_listings`;
CREATE TABLE IF NOT EXISTS `marketplace_listings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `listing_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `seller_user_id` bigint unsigned NOT NULL,
  `seller_account_id` int unsigned NOT NULL,
  `seller_char_id` int unsigned NOT NULL,
  `seller_char_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_type` enum('zeny','cashpoints') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'zeny',
  `unit_price` bigint unsigned NOT NULL DEFAULT '0',
  `total_price` bigint unsigned NOT NULL DEFAULT '0',
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `remaining_quantity` int unsigned NOT NULL DEFAULT '1',
  `duration_hours` int unsigned NOT NULL DEFAULT '72',
  `status` enum('active','sold','cancelled','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `expires_at` timestamp NOT NULL,
  `sold_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketplace_listings_listing_code_unique` (`listing_code`),
  KEY `marketplace_listings_item_id_foreign` (`item_id`),
  KEY `marketplace_listings_seller_user_id_foreign` (`seller_user_id`),
  KEY `marketplace_listings_seller_account_id_index` (`seller_account_id`),
  KEY `marketplace_listings_seller_char_id_index` (`seller_char_id`),
  KEY `marketplace_listings_currency_type_index` (`currency_type`),
  KEY `marketplace_listings_status_index` (`status`),
  KEY `marketplace_listings_expires_at_index` (`expires_at`),
  CONSTRAINT `marketplace_listings_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `marketplace_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_listings_seller_user_id_foreign` FOREIGN KEY (`seller_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `marketplace_transactions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `marketplace_transactions`;
CREATE TABLE IF NOT EXISTS `marketplace_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `listing_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `seller_user_id` bigint unsigned NOT NULL,
  `seller_account_id` int unsigned NOT NULL,
  `seller_char_id` int unsigned NOT NULL,
  `seller_char_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_user_id` bigint unsigned NOT NULL,
  `buyer_account_id` int unsigned NOT NULL,
  `buyer_char_id` int unsigned NOT NULL,
  `buyer_char_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_type` enum('zeny','cashpoints') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'zeny',
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `unit_price` bigint unsigned NOT NULL DEFAULT '0',
  `gross_amount` bigint unsigned NOT NULL DEFAULT '0',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` bigint unsigned NOT NULL DEFAULT '0',
  `net_seller_proceeds` bigint unsigned NOT NULL DEFAULT '0',
  `status` enum('completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `delivery_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivered_to_storage',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketplace_transactions_transaction_code_unique` (`transaction_code`),
  KEY `marketplace_transactions_listing_id_foreign` (`listing_id`),
  KEY `marketplace_transactions_item_id_foreign` (`item_id`),
  KEY `marketplace_transactions_seller_user_id_foreign` (`seller_user_id`),
  KEY `marketplace_transactions_buyer_user_id_foreign` (`buyer_user_id`),
  KEY `marketplace_transactions_seller_account_id_index` (`seller_account_id`),
  KEY `marketplace_transactions_seller_char_id_index` (`seller_char_id`),
  KEY `marketplace_transactions_buyer_account_id_index` (`buyer_account_id`),
  KEY `marketplace_transactions_buyer_char_id_index` (`buyer_char_id`),
  CONSTRAINT `marketplace_transactions_buyer_user_id_foreign` FOREIGN KEY (`buyer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_transactions_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `marketplace_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_transactions_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES `marketplace_listings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_transactions_seller_user_id_foreign` FOREIGN KEY (`seller_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `migrations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `news`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('news','announcement','event','update','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'news',
  `summary` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` bigint unsigned DEFAULT NULL,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Admin',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_slug_unique` (`slug`),
  KEY `news_category_is_published_index` (`category`,`is_published`),
  KEY `news_is_pinned_index` (`is_pinned`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `password_reset_tokens`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `player_achievements`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `player_achievements`;
CREATE TABLE IF NOT EXISTS `player_achievements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `char_id` bigint unsigned NOT NULL,
  `achievement_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'milestone',
  `icon` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trophy',
  `color` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#f59e0b',
  `earned_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_achievements_char_key_unique` (`char_id`,`achievement_key`),
  KEY `player_achievements_char_id_index` (`char_id`),
  KEY `player_achievements_achievement_key_index` (`achievement_key`),
  KEY `player_achievements_earned_at_index` (`earned_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `player_inbox_items`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `player_inbox_items`;
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

-- -----------------------------------------------------
-- Table structure for `player_profile_settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `player_profile_settings`;
CREATE TABLE IF NOT EXISTS `player_profile_settings` (
  `char_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `show_char_info` tinyint(1) NOT NULL DEFAULT '1',
  `show_equipments` tinyint(1) NOT NULL DEFAULT '1',
  `show_zeny` tinyint(1) NOT NULL DEFAULT '1',
  `show_rankings` tinyint(1) NOT NULL DEFAULT '1',
  `show_badges` tinyint(1) NOT NULL DEFAULT '1',
  `show_achievements` tinyint(1) NOT NULL DEFAULT '1',
  `show_stats` tinyint(1) NOT NULL DEFAULT '1',
  `show_guild` tinyint(1) NOT NULL DEFAULT '1',
  `show_last_login` tinyint(1) NOT NULL DEFAULT '0',
  `bio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`char_id`),
  KEY `player_profile_settings_account_id_index` (`account_id`),
  KEY `player_profile_settings_is_public_index` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `ranking_character_stats`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ranking_character_stats`;
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

-- -----------------------------------------------------
-- Table structure for `ranking_events`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ranking_events`;
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

-- -----------------------------------------------------
-- Table structure for `reward_definitions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `reward_definitions`;
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

-- -----------------------------------------------------
-- Table structure for `reward_delivery_logs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `reward_delivery_logs`;
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

-- -----------------------------------------------------
-- Table structure for `reward_issuances`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `reward_issuances`;
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

-- -----------------------------------------------------
-- Table structure for `sessions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `site_settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table structure for `support_ticket_activities`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `support_ticket_activities`;
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

-- -----------------------------------------------------
-- Table structure for `support_ticket_attachments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `support_ticket_attachments`;
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

-- -----------------------------------------------------
-- Table structure for `support_ticket_messages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `support_ticket_messages`;
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

-- -----------------------------------------------------
-- Table structure for `support_tickets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `support_tickets`;
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

-- -----------------------------------------------------
-- Table structure for `users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int unsigned DEFAULT NULL,
  `username` varchar(23) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','staff','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_account_id_unique` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Migration History (Marks all migrations as completed)
-- -----------------------------------------------------
TRUNCATE TABLE `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4, '2026_11_23_000001_enhance_users_for_aether', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5, '2026_11_23_000002_create_aether_news_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6, '2026_11_23_000003_create_aether_downloads_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7, '2026_11_23_000004_create_aether_donations_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8, '2026_11_23_000005_create_aether_settings_and_logs_tables', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9, '2026_11_23_000006_add_item_rewards_and_paypal_to_donations', 2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10, '2026_11_24_000001_create_aether_marketplace_tables', 3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11, '2026_11_24_000002_create_aether_support_ticket_tables', 4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12, '2026_11_24_000003_create_aether_faq_tables', 5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13, '2026_11_25_000001_create_ranking_tracking_tables', 6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14, '2026_11_25_000002_create_badge_system_tables', 7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15, '2026_11_25_000003_create_reward_system_tables', 8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16, '2026_11_25_000004_create_player_profiles_tables', 9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17, '2026_11_25_000005_add_profile_customization_fields', 10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18, '2026_11_25_000006_create_attendance_and_daily_rewards_tables', 11);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;


-- --------------------------------------------------------
-- Table structure for table `cash_shop_categories`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cash_shop_categories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'tag',
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_shop_categories_slug_unique` (`slug`),
  KEY `cash_shop_categories_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `cash_shop_products`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cash_shop_products` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `item_quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `cash_points_price` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `promotional_price` int(10) UNSIGNED DEFAULT NULL,
  `promotion_start_at` timestamp NULL DEFAULT NULL,
  `promotion_end_at` timestamp NULL DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `unlimited_stock` tinyint(1) NOT NULL DEFAULT 1,
  `purchase_limit_per_account` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `purchase_limit_daily` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `item_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`item_metadata`)),
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_shop_products_slug_unique` (`slug`),
  KEY `cash_shop_products_item_id_index` (`item_id`),
  KEY `cash_shop_products_status_index` (`status`),
  KEY `cash_shop_products_is_featured_index` (`is_featured`),
  KEY `cash_shop_products_category_id_foreign` (`category_id`),
  CONSTRAINT `cash_shop_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `cash_shop_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `cash_shop_purchases`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cash_shop_purchases` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_code` varchar(32) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name_snapshot` varchar(150) NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `item_quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `cash_points_spent` int(10) UNSIGNED NOT NULL,
  `escrow_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('completed','refunded','cancelled') NOT NULL DEFAULT 'completed',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_shop_purchases_purchase_code_unique` (`purchase_code`),
  KEY `cash_shop_purchases_account_id_index` (`account_id`),
  KEY `cash_shop_purchases_escrow_item_id_index` (`escrow_item_id`),
  KEY `cash_shop_purchases_user_id_foreign` (`user_id`),
  KEY `cash_shop_purchases_product_id_foreign` (`product_id`),
  CONSTRAINT `cash_shop_purchases_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_shop_purchases_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `cash_shop_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
