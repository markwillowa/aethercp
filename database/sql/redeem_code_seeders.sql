-- =====================================================================
-- AetherCP Control Panel: Redeem Code Campaigns & Rewards Seeder
-- Note: Code hashes depend on APP_KEY HMAC-SHA256; run `php artisan db:seed --class=RedeemCodeSeeder`
-- or generate codes from Admin -> Redeem Codes after importing this campaign/reward template.
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

INSERT INTO `redeem_campaigns` (`id`, `name`, `slug`, `description`, `campaign_type`, `instructions`, `status`, `is_public`, `one_per_account`, `max_per_account`, `created_at`, `updated_at`) VALUES
(1, 'Welcome Reward 2027', 'welcome-reward-2027', 'Starter welcome package for new and returning Rune-Midgard adventurers.', 'welcome_reward', 'Enter your welcome code to claim Cash Points and starter supplies.', 'active', 1, 1, 1, NOW(), NOW()),
(2, 'Aether Launch Event', 'aether-launch-event', 'Grand launch celebration gift with Cash Points and Zeny.', 'launch_event', 'Enter your launch code and select an offline character to receive Zeny and Cash Points.', 'active', 1, 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `status` = VALUES(`status`), `updated_at` = NOW();

INSERT INTO `redeem_code_rewards` (`id`, `campaign_id`, `reward_type`, `amount`, `delivery_scope`, `item_id`, `item_name`, `is_bound`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'cash_points', 500, 'account', NULL, NULL, 0, 1, NOW(), NOW()),
(2, 1, 'item', 10, 'account', 501, 'Red Potion', 1, 2, NOW(), NOW()),
(3, 2, 'cash_points', 1000, 'account', NULL, NULL, 0, 1, NOW(), NOW()),
(4, 2, 'zeny', 50000, 'character', NULL, NULL, 0, 2, NOW(), NOW())
ON DUPLICATE KEY UPDATE `amount` = VALUES(`amount`), `updated_at` = NOW();

COMMIT;
