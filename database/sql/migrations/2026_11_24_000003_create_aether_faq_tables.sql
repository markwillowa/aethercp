-- =====================================================================
-- Migration: 2026_11_24_000003_create_aether_faq_tables
-- Description: Create faq_categories, faqs, faq_feedback, faq_related_questions, and faq_search_logs tables
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

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

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_24_000003_create_aether_faq_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_24_000003_create_aether_faq_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
