-- =====================================================================
-- Migration: 2026_11_24_000001_create_aether_marketplace_tables
-- Description: Create marketplace_items, marketplace_listings, and marketplace_transactions tables
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

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

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_24_000001_create_aether_marketplace_tables', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_24_000001_create_aether_marketplace_tables'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
