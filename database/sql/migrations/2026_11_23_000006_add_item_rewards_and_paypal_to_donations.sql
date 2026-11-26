-- =====================================================================
-- Migration: 2026_11_23_000006_add_item_rewards_and_paypal_to_donations
-- Description: Add item reward and PayPal IPN columns to donation_packages and donations
-- Non-destructive: Safe to import without dropping existing data.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

ALTER TABLE `donation_packages`
  ADD COLUMN `item_id` int unsigned DEFAULT NULL AFTER `bonus_points`,
  ADD COLUMN `item_amount` int unsigned NOT NULL DEFAULT '0' AFTER `item_id`,
  ADD COLUMN `item_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `item_amount`;

ALTER TABLE `donations`
  ADD COLUMN `points_delivered` tinyint(1) NOT NULL DEFAULT '0' AFTER `points_awarded`,
  ADD COLUMN `items_delivered` tinyint(1) NOT NULL DEFAULT '0' AFTER `points_delivered`,
  ADD COLUMN `payer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `transaction_reference`,
  ADD COLUMN `payer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `payer_email`,
  ADD COLUMN `ipn_data` text COLLATE utf8mb4_unicode_ci AFTER `notes`;

-- Record migration if not already recorded
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_11_23_000006_add_item_rewards_and_paypal_to_donations', 1 FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = '2026_11_23_000006_add_item_rewards_and_paypal_to_donations'
);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
