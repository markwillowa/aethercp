-- =====================================================================
-- Migration: 2026_11_26_000001_create_cash_shop_tables
-- Description: Create cash_shop_categories, cash_shop_products, and cash_shop_purchases tables
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

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

CREATE TABLE IF NOT EXISTS `cash_shop_purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `account_id` int unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_name_snapshot` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` int unsigned NOT NULL,
  `item_quantity` int unsigned NOT NULL DEFAULT '1',
  `cash_points_spent` int unsigned NOT NULL,
  `escrow_item_id` bigint unsigned DEFAULT NULL,
  `status` enum('completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_shop_purchases_purchase_code_unique` (`purchase_code`),
  KEY `cash_shop_purchases_account_id_index` (`account_id`),
  KEY `cash_shop_purchases_escrow_item_id_index` (`escrow_item_id`),
  KEY `cash_shop_purchases_status_index` (`status`),
  KEY `cash_shop_purchases_user_id_foreign` (`user_id`),
  KEY `cash_shop_purchases_product_id_foreign` (`product_id`),
  CONSTRAINT `cash_shop_purchases_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_shop_purchases_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `cash_shop_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_26_000001_create_cash_shop_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_26_000001_create_cash_shop_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
