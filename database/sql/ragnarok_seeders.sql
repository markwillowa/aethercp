-- =====================================================================
-- Ragnarok Online Emulator Game Database Starter Seeders
-- Target Database: Ragnarok Game Database (`freynix` / `ragnarok`)
-- Compatible with: rAthena & Hercules (Renewal & Pre-Renewal)
-- Generated for AetherCP Control Panel Integration
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

-- -----------------------------------------------------
-- Dumping starter game accounts for table `login`
-- Default passwords:
--   admin    -> Admin1234!
--   malda009 -> Maki0913$
--   s1       -> athena
--   players  -> Password123!
-- -----------------------------------------------------

INSERT INTO `login` (`account_id`, `userid`, `user_pass`, `sex`, `email`, `group_id`, `state`, `unban_time`, `expiration_time`, `logincount`, `lastlogin`, `last_ip`, `birthdate`, `character_slots`, `pincode`, `pincode_change`, `vip_time`, `old_group`, `web_auth_token`, `web_auth_token_enabled`) VALUES
(1, 's1', 'athena', 'M', 'athena@athena.com', 0, 0, 0, 0, 0, NULL, '127.0.0.1', '2000-01-01', 9, '', 0, 0, 0, NULL, 0),
(2000000, 'malda009', 'Maki0913$', 'M', 'owner@freynix.local', 99, 0, 0, 0, 1, '2026-11-23 10:29:51', '127.0.0.1', '1995-09-13', 9, '', 0, 0, 0, NULL, 0),
(2000001, 'admin', 'Admin1234!', 'M', 'admin@freynix.local', 99, 0, 0, 0, 5, '2026-11-24 22:12:35', '127.0.0.1', '1990-01-01', 9, '', 0, 0, 0, NULL, 0),
(2000002, 'rune_smith', 'Password123!', 'M', 'runesmith@aether.local', 0, 0, 0, 0, 0, NULL, '127.0.0.1', '2000-01-01', 9, '', 0, 0, 0, NULL, 0),
(2000003, 'kafra_trader', 'Password123!', 'F', 'trader@aether.local', 0, 0, 0, 0, 0, NULL, '127.0.0.1', '2000-01-01', 9, '', 0, 0, 0, NULL, 0),
(2000004, 'card_collector', 'Password123!', 'M', 'cards@aether.local', 0, 0, 0, 0, 0, NULL, '127.0.0.1', '2000-01-01', 9, '', 0, 0, 0, NULL, 0),
(2000005, 'alchemist_pro', 'Password123!', 'F', 'alchemist@aether.local', 0, 0, 0, 0, 0, NULL, '127.0.0.1', '2000-01-01', 9, '', 0, 0, 0, NULL, 0)
ON DUPLICATE KEY UPDATE
    `group_id` = VALUES(`group_id`),
    `email` = VALUES(`email`),
    `character_slots` = VALUES(`character_slots`);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
